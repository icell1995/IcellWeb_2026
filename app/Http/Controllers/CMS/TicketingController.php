<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Lib\Rank;
use App\Models\Polda;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class TicketingController extends Controller
{
    /**
     * List ticket + filter Polda/Polres (mirip pola rekap, tapi tanpa locking per akun)
     */
    public function index(Request $request)
    {
        // Ambil filter dari request (support polda_id/polda dan polres_id/polres)
        $poldaId  = trim((string) $request->input('polda_id', $request->input('polda', '')));
        $polresId = trim((string) $request->input('polres_id', $request->input('polres', '')));

        // Perlakukan '-' sebagai "tidak pilih"
        if ($poldaId === '-') {
            $poldaId = '';
        }
        if ($polresId === '-') {
            $polresId = '';
        }

        // daftar polda yang harus di-exclude (persis seperti rekap)
        $excludePolda = ['90', '99', '80'];

        // Validasi kombinasi Polda & Polres (Polres harus milik Polda)
        if ($poldaId !== '' && $polresId !== '') {
            $validCombo = DB::table('polres')
                ->where('id', $polresId)
                ->where('polda_id', $poldaId)
                ->exists();

            if (! $validCombo) {
                return back()
                    ->withErrors(['polres_id' => 'Polres tidak sesuai dengan Polda yang dipilih.'])
                    ->withInput();
            }
        }

        // ===== Query Ticket dasar + eager load Polda/Polres/Assigned =====
        $query = Ticket::with(['polda', 'polres', 'assigned']);

        // Filter Polda (jika ada)
        if ($poldaId !== '') {
            $query->where('polda_id', $poldaId);
        }

        // Filter Polres (jika ada)
        if ($polresId !== '') {
            $query->where('polres_id', $polresId);
        }

        $tickets = $query
            ->orderBy('created_at', 'asc')
            ->paginate(15)
            ->appends($request->all());

        $assignedUsers = $this->getHelpdeskUsers();

        // === Dropdown Polda/Polres ===

        // Pusat: semua polda (kecuali yang di-exclude), polres di-load via AJAX
        $poldas = DB::table('polda')
            ->select('id', 'name')
            ->whereNotIn('id', $excludePolda)
            ->orderBy('name')
            ->get();

        // Polres: kalau ada filter polda, preload daftar polres polda tsb (kalau tidak, kosong)
        $polress = collect();
        if ($poldaId !== '') {
            $polress = DB::table('polres')
                ->select('id', 'name')
                ->where('state', '<>', 0)
                ->where('polda_id', $poldaId)
                ->orderBy('name')
                ->get();
        }

        return view('cms.tickets.index', compact(
            'tickets',
            'poldas',
            'polress',
            'poldaId',
            'polresId',
            'assignedUsers'
        ))->with('_title', 'Ticketing');
    }

    /**
     * Tab "Open" – sementara simple: semua ticket terbaru (status bisa di-filter nanti kalau mau).
     */
    public function open(Request $request)
    {
        $tickets = Ticket::with(['polda', 'polres', 'assigned'])
            ->where('status', '<>', 'solved')   // <- tiket yang SUDAH solved tidak ikut tampil di tab Open
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        $assignedUsers = $this->getHelpdeskUsers();

        return view('cms.tickets.index', compact('tickets', 'assignedUsers'))
            ->with('_title', 'Ticketing - Open');
    }


    /**
     * Tickets tidak solved > 3 hari (Pending).
     */
    public function pending(Request $request)
    {
        $threshold = now()->subDays(3);

        $tickets = Ticket::with(['polda', 'polres', 'assigned'])
            ->where(function ($q) use ($threshold) {
                $q->where('status', '<>', 'solved')
                    ->where('created_at', '<=', $threshold);
            })
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        $assignedUsers = $this->getHelpdeskUsers();

        return view('cms.tickets.index', compact('tickets', 'assignedUsers'))
            ->with('_title', 'Ticketing - Pending');
    }

    /**
     * Tickets yang sudah solved.
     */
    // public function solved(Request $request)
    // {
    //     $tickets = Ticket::with(['polda', 'polres', 'assigned'])
    //         ->where('status', 'solved')
    //         ->orderBy('updated_at', 'asc')
    //         ->paginate(15);

    //     $assignedUsers = $this->getHelpdeskUsers();

    //     return view('cms.tickets.index', compact('tickets', 'assignedUsers'))
    //         ->with('_title', 'Ticketing - Solved');
    // }

    /**
     * Tickets yang sudah solved.
     */
    public function solved(Request $request)
    {
        // Jika request AJAX => balikan JSON untuk DataTables
        if ($request->ajax()) {
            $query = Ticket::with(['polda', 'polres', 'assigned'])
                ->where('status', 'solved');

            // Filter periode tanggal (pakai updated_at atau created_at, pilih salah satu)
            if ($request->filled('from') && $request->filled('to')) {
                try {
                    $from = Carbon::createFromFormat('d-m-Y', $request->input('from'))->startOfDay();
                    $to   = Carbon::createFromFormat('d-m-Y', $request->input('to'))->endOfDay();

                    $query->whereBetween('updated_at', [$from, $to]); // atau created_at
                } catch (\Exception $e) {
                    // kalau format salah, biarkan tanpa filter
                }
            }

            return DataTables::of($query)
                ->addIndexColumn() // otomatis kolom NO buat DataTable (DT_RowIndex)
                ->editColumn('ticket_number', fn ($row) => $row->ticket_number ?? '-')
                ->addColumn('kategori_label', function ($row) {
                    $kategori = $row->kategori ?? '-';
                    return $kategori;
                })
                ->addColumn('polda_name', function ($row) {
                    return optional($row->polda)->name ?? optional($row->polda)->nama ?? '-';
                })
                ->addColumn('polres_name', function ($row) {
                    return optional($row->polres)->name ?? optional($row->polres)->nama ?? '-';
                })
                ->addColumn('assigned_name', function ($row) {
                    $assigned = $row->assigned;
                    if ($assigned) {
                        $name = $assigned->full_name
                            ?? trim(($assigned->first_name ?? '') . ' ' . ($assigned->last_name ?? ''));
                        return $name ?: ('User ID ' . $assigned->id);
                    }
                    return $row->assigned_to ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d-m-Y H:i') : '-';
                })
                ->make(true);
        }

        // Kalau bukan AJAX => render view Blade
        $assignedUsers = $this->getHelpdeskUsers();

        return view('cms.tickets.solved_index', compact('assignedUsers'))
            ->with('_title', 'Ticketing - Solved');
    }
    

    /**
     * Show create ticket form
     * (tidak ada locking: semua Polda bisa dipilih)
     */
    public function create()
    {
        // Semua polda aktif, kecuali yang di-exclude
        $excludePolda = ['90', '99', '80'];

        $poldas = Polda::where('state', '<>', 0)
            ->whereNotIn('id', $excludePolda)
            ->orderBy('sort')
            ->get();

        // Users untuk assigned_to: role_id = 1 (Admin), dengan rank "Consultant" (default)
        $rankId = Rank::where('name', 'Consultant')->value('id');

        $assignedUsersQuery = User::selectFullNameExpression()
            ->whereIn('role_id', [1, 2])
            ->where('state', 1);

        if ($rankId) {
            $assignedUsersQuery->where('rank_id', $rankId);
        } else {
            $assignedUsersQuery->whereHas('rank', function ($q) {
                $q->where('name', 'Consultant');
            });
        }

        $assignedUsers = $assignedUsersQuery->get();

        return view('cms.tickets.create', compact('poldas', 'assignedUsers'))
            ->with('_title', 'Create Ticket');
    }

    /**
     * Return Polres list untuk Polda tertentu (AJAX).
     */
    public function polresList($polda)
    {
        $polres = DB::table('polres')
            ->select('id', 'name')
            ->where('state', '<>', 0)
            ->where('polda_id', $polda)
            ->orderBy('name')
            ->get();

        return response()->json($polres);
    }

    /**
     * Store new ticket.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'polda_id'               => 'nullable|string',
            'polres_id'              => 'nullable|string',
            'assigned_to'            => 'nullable|integer',
            'deskripsi_permasalahan' => 'nullable|string',
            'deskripsi_solusi'       => 'nullable|string',
            'kategori'               => 'nullable|string',
        ]);

        // Normalisasi polda/polres
        $data['polda_id'] = $data['polda_id'] ?: null;

        if (isset($data['polres_id']) && ($data['polres_id'] === '-' || $data['polres_id'] === '')) {
            $data['polres_id'] = null;
        }

        $data['status']     = 'open';
        $data['created_by'] = Auth::id();
        $data['assigned_to'] = empty($data['assigned_to'])
            ? Auth::id()
            : $data['assigned_to'];

        // ===== Generate ticket_number: TIC-YYMMDD-XXX (per hari) =====
        $today      = now()->toDateString();
        $datePart   = now()->format('ymd');
        $prefixLike = "TIC-{$datePart}-%";

        $ticket = null;

        DB::transaction(function () use (&$ticket, &$data, $today, $datePart, $prefixLike) {
            // Ambil ticket terakhir hari ini, lock supaya aman secara concurrency
            $lastTicket = Ticket::whereDate('created_at', $today)
                ->where('ticket_number', 'like', $prefixLike)
                ->lockForUpdate()
                ->orderBy('ticket_number', 'desc')
                ->first();

            $lastSeq = 0;
            if ($lastTicket) {
                // ticket_number = TIC-YYMMDD-XXX
                $parts = explode('-', $lastTicket->ticket_number);
                // index 0 = TIC, 1 = YYMMDD, 2 = XXX
                if (isset($parts[2])) {
                    $lastSeq = (int) $parts[2];
                }
            }

            $nextSeq = $lastSeq + 1; // nomor urut berikutnya

            $data['ticket_number'] = 'TIC-' . $datePart . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

            // simpan ticket
            $ticket = Ticket::create($data);
        });

        return redirect()
            ->route('ticketing.index')
            ->with('success', 'Ticket created: ' . $ticket->ticket_number);
    }

    /**
     * Update ticket status.
     * Accepts 'status' field (open|pending|solved) and optional assigned_to.
     * Returns JSON untuk flow modal.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $payload = $request->validate([
            'status'           => 'required|in:open,pending,solved',
            'assigned_to'      => 'nullable|integer',
            'deskripsi_solusi' => 'required_if:status,solved|nullable|string',
        ]);

        if (array_key_exists('assigned_to', $payload)) {
            $ticket->assigned_to = $payload['assigned_to'];
        }

        $ticket->status = $payload['status'];

        // hanya set solution ketika ada (dan required jika status = solved)
        if (
            array_key_exists('deskripsi_solusi', $payload)
            && $payload['deskripsi_solusi'] !== null
        ) {
            $ticket->deskripsi_solusi = $payload['deskripsi_solusi'];
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'ticket'  => $ticket,
        ]);
    }

    /**
     * Delete a ticket.
     * Returns JSON response for fetch().
     */
    public function destroy(Ticket $ticket)
    {
        // Kalau mau batasi (misal hanya admin), bisa cek di sini:
        // if (Auth::user()->role_id !== 1) { abort(403); }

        $ticket->delete();

        // Response JSON buat dipakai di fetch()
        return response()->json([
            'success' => true,
            'message' => 'Ticket berhasil dihapus.',
        ]);
    }

    /**
     * Get list of helpdesk users (role_id = 1, username ILIKE 'Helpdesk-%').
     */
    private function getHelpdeskUsers()
    {
        return User::selectFullNameExpression()
            ->where('state', 1)
            ->where('username', 'ilike', 'Helpdesk-%')
            ->orderBy('username')
            ->get();
    }

    /**
     * Export tiket solved ke Excel (mengikuti filter tanggal yang sama).
    */
    public function exportSolved(Request $request)
    {
        $query = Ticket::with(['polda', 'polres', 'assigned'])
            ->where('status', 'solved');

        if ($request->filled('from') && $request->filled('to')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->input('from'))->startOfDay();
                $to   = Carbon::createFromFormat('d-m-Y', $request->input('to'))->endOfDay();
                $query->whereBetween('updated_at', [$from, $to]); // sama dengan di solved()
            } catch (\Exception $e) {
                // abaikan jika format salah
            }
        }

        $tickets = $query->orderBy('updated_at', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ticket Solved');

        // Header
        $sheet->fromArray(
            [[
                'No',
                'Ticket Number',
                'Kategori',
                'Status',
                'Polda',
                'Polres',
                'Deskripsi Permasalahan',
                'Deskripsi Solusi',
                'Dibuat',
                'Diupdate',
                'Dikerjakan Oleh',
            ]],
            null,
            'A1'
        );

        // Data
        $rowNum = 2;
        foreach ($tickets as $idx => $t) {
            $assigned = $t->assigned;
            $assignedName = $assigned
                ? ($assigned->full_name
                    ?? trim(($assigned->first_name ?? '') . ' ' . ($assigned->last_name ?? ''))
                    ?: ('User ID ' . $assigned->id))
                : ($t->assigned_to ?? '-');

            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $t->ticket_number ?? '-');
            $sheet->setCellValue("C{$rowNum}", $t->kategori ?? '-');
            $sheet->setCellValue("D{$rowNum}", $t->status ?? '-');
            $sheet->setCellValue("E{$rowNum}", optional($t->polda)->name ?? optional($t->polda)->nama ?? '-');
            $sheet->setCellValue("F{$rowNum}", optional($t->polres)->name ?? optional($t->polres)->nama ?? '-');
            $sheet->setCellValue("G{$rowNum}", $t->deskripsi_permasalahan ?? '-');
            $sheet->setCellValue("H{$rowNum}", $t->deskripsi_solusi ?? '-');
            $sheet->setCellValue("I{$rowNum}", $t->created_at ? $t->created_at->format('d-m-Y H:i') : '-');
            $sheet->setCellValue("J{$rowNum}", $t->updated_at ? $t->updated_at->format('d-m-Y H:i') : '-');
            $sheet->setCellValue("K{$rowNum}", $assignedName);

            $rowNum++;
        }

        // Autosize kolom
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'ticket_solved_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
