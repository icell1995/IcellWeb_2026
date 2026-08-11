<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Polda;
use App\Models\RequestData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;

class RequestDataController extends Controller
{
    /* ─────────────────────────────────────────────────
     | INDEX – halaman daftar dengan DataTable
     ───────────────────────────────────────────────── */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RequestData::with(['polda', 'polres', 'penyediaData'])
                ->where('status', true); // hanya tampilkan yang belum soft-deleted

            // Filter jenis institusi
            if ($request->filled('jenis_institusi')) {
                $query->where('jenis_institusi', $request->jenis_institusi);
            }

            // Filter polda
            if ($request->filled('polda_id')) {
                $query->where('polda_id', $request->polda_id);
            }

            // Filter polres
            if ($request->filled('polres_id')) {
                $query->where('polres_id', $request->polres_id);
            }

            // Filter instansi lain (freetext search)
            if ($request->filled('instansi_lain')) {
                $query->where('instansi_lain', 'ilike', '%' . $request->instansi_lain . '%');
            }

            // Filter tanggal (format dd-mm-yyyy dari datepicker)
            if ($request->filled('dari_tanggal')) {
                try {
                    $dari = Carbon::createFromFormat('d-m-Y', $request->dari_tanggal)->startOfDay();
                    $query->where('tanggal_permintaan', '>=', $dari);
                } catch (\Exception $e) {}
            }
            if ($request->filled('hingga_tanggal')) {
                try {
                    $hingga = Carbon::createFromFormat('d-m-Y', $request->hingga_tanggal)->endOfDay();
                    $query->where('tanggal_permintaan', '<=', $hingga);
                } catch (\Exception $e) {}
            }

            // Filter search nama
            if ($request->filled('search_nama')) {
                $q = $request->search_nama;
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_depan_pemohon', 'ilike', "%$q%")
                        ->orWhere('nama_belakang_pemohon', 'ilike', "%$q%");
                });
            }

            $query->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_pemohon', fn($row) =>
                    $row->nama_lengkap_pemohon
                )
                ->addColumn('instansi_label', fn($row) => $row->instansi_label)
                ->editColumn('tanggal_permintaan', fn($row) =>
                    $row->tanggal_permintaan ? $row->tanggal_permintaan->format('Y-m-d') : '-'
                )
                ->editColumn('tanggal_penyajian', fn($row) =>
                    $row->tanggal_penyajian ? $row->tanggal_penyajian->format('Y-m-d') : '-'
                )
                ->addColumn('penyedia_label', function ($row) {
                    $user = $row->penyediaData;
                    if (!$user) return '-';
                    $name = $user->full_name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    $username = $user->username ?? '';
                    return $name . ($username ? " ({$username})" : '');
                })
                ->addColumn('file_link', function ($row) {
                    if (!$row->file_data_path) return '-';
                    $url   = route('request-data.download', $row->id);
                    $label = $row->file_data_name ?? basename($row->file_data_path);
                    return '<a href="' . $url . '" class="text-primary" title="Download">
                                <i class="bi bi-download me-1"></i>' . e($label) . '
                            </a>';
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <div class="d-flex gap-1 flex-wrap">
                            <button class="btn btn-sm btn-warning btn-edit-request"
                                data-id="' . $row->id . '">
                                <i class="bi bi-pencil-square"></i> Ubah
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete-request"
                                data-id="' . $row->id . '">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </div>';
                })
                ->rawColumns(['file_link', 'aksi'])
                ->make(true);
        }

        $poldas = $this->getPoldaList();

        return view('cms.request-data.index', compact('poldas'))
            ->with('_title', 'Request Data');
    }

    /* ─────────────────────────────────────────────────
     | STORE – simpan data baru
     ───────────────────────────────────────────────── */
    public function store(Request $request)
    {
        // Konversi tanggal dd-mm-yyyy → Y-m-d sebelum validasi
        foreach (['tanggal_permintaan', 'tanggal_penyajian'] as $f) {
            if ($request->filled($f)) {
                try { $request->merge([$f => Carbon::createFromFormat('d-m-Y', $request->input($f))->format('Y-m-d')]); } catch (\Exception $e) {}
            }
        }

        $validated = $request->validate([
            'catatan_permintaan'    => 'nullable|string|max:2000',
            'nama_lengkap_pemohon'  => 'required|string|max:255',
            'no_telp_pemohon'       => 'nullable|string|max:30',
            'jenis_institusi'       => 'required|in:korlantas,polda,polres,lainnya',
            'polda_id'              => 'nullable|string',
            'polres_id'             => 'nullable|string',
            'instansi_lain'         => 'nullable|string|max:255',
            'evidence'              => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'tanggal_permintaan'    => 'required|date',
            'tanggal_penyajian'     => 'nullable|date',
            'file_data'             => 'nullable|file|mimes:xlsx,xls,ppt,pptx,doc,docx,pdf|max:20480',
        ]);

        $data = $validated;
        $data['penyedia_data_id'] = Auth::id();
        $data['created_by']       = Auth::id();

        // Upload evidence
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $path = $file->store('request-data/evidence', 'public');
            $data['evidence_path'] = $path;
            $data['evidence_name'] = $file->getClientOriginalName();
        }

        // Upload file data
        if ($request->hasFile('file_data')) {
            $file = $request->file('file_data');
            $path = $file->store('request-data/data', 'public');
            $data['file_data_path'] = $path;
            $data['file_data_name'] = $file->getClientOriginalName();
        }

        RequestData::create($data);

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
    }

    /* ─────────────────────────────────────────────────
     | SHOW – ambil satu record untuk modal edit
     ───────────────────────────────────────────────── */
    public function show($id)
    {
        $record = RequestData::with(['polda', 'polres', 'penyediaData'])->findOrFail($id);

        // Polres list untuk polda yang sudah dipilih
        $polresList = collect();
        if ($record->polda_id) {
            $polresList = DB::table('polres')
                ->select('id', 'name')
                ->where('state', '<>', 0)
                ->where('polda_id', $record->polda_id)
                ->orderBy('name')
                ->get();
        }

        return response()->json([
            'success'     => true,
            'data'        => array_merge($record->toArray(), [
                'tanggal_permintaan' => $record->tanggal_permintaan?->format('d-m-Y'),
                'tanggal_penyajian'  => $record->tanggal_penyajian?->format('d-m-Y'),
            ]),
            'polres_list' => $polresList,
        ]);
    }

    /* ─────────────────────────────────────────────────
     | UPDATE – simpan perubahan
     ───────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $record = RequestData::findOrFail($id);

        // Konversi tanggal dd-mm-yyyy → Y-m-d sebelum validasi
        foreach (['tanggal_permintaan', 'tanggal_penyajian'] as $f) {
            if ($request->filled($f)) {
                try { $request->merge([$f => Carbon::createFromFormat('d-m-Y', $request->input($f))->format('Y-m-d')]); } catch (\Exception $e) {}
            }
        }

        $validated = $request->validate([
            'catatan_permintaan'    => 'nullable|string|max:2000',
            'nama_lengkap_pemohon'  => 'required|string|max:255',
            'no_telp_pemohon'       => 'nullable|string|max:30',
            'jenis_institusi'       => 'required|in:korlantas,polda,polres,lainnya',
            'polda_id'              => 'nullable|string',
            'polres_id'             => 'nullable|string',
            'instansi_lain'         => 'nullable|string|max:255',
            'evidence'              => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'tanggal_permintaan'    => 'required|date',
            'tanggal_penyajian'     => 'nullable|date',
            'file_data'             => 'nullable|file|mimes:xlsx,xls,ppt,pptx,doc,docx,pdf|max:20480',
        ]);

        $data = $validated;

        // Upload evidence (jika diupload ulang)
        if ($request->hasFile('evidence')) {
            // Hapus file lama
            if ($record->evidence_path) {
                Storage::disk('public')->delete($record->evidence_path);
            }
            $file = $request->file('evidence');
            $data['evidence_path'] = $file->store('request-data/evidence', 'public');
            $data['evidence_name'] = $file->getClientOriginalName();
        }

        // Upload file data (jika diupload ulang)
        if ($request->hasFile('file_data')) {
            if ($record->file_data_path) {
                Storage::disk('public')->delete($record->file_data_path);
            }
            $file = $request->file('file_data');
            $data['file_data_path'] = $file->store('request-data/data', 'public');
            $data['file_data_name'] = $file->getClientOriginalName();
        }

        $record->update($data);

        return response()->json(['success' => true, 'message' => 'Data berhasil diubah.']);
    }

    /* ─────────────────────────────────────────────────
     | DESTROY – hapus record
     ───────────────────────────────────────────────── */
    public function destroy($id)
    {
        $record = RequestData::findOrFail($id);

        // Soft delete: tandai dengan status = false
        $record->status = false;
        $record->save();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    /* ─────────────────────────────────────────────────
     | DOWNLOAD – paksa download file data
     ───────────────────────────────────────────────── */
    public function download($id)
    {
        $record = RequestData::findOrFail($id);

        if (!$record->file_data_path || !Storage::disk('public')->exists($record->file_data_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fileName = $record->file_data_name ?? basename($record->file_data_path);

        return Storage::disk('public')->download($record->file_data_path, $fileName);
    }

    /* ─────────────────────────────────────────────────
     | POLRES LIST – AJAX: polres per polda
     ───────────────────────────────────────────────── */
    public function polresList($poldaId)
    {
        $polres = DB::table('polres')
            ->select('id', 'name')
            ->where('state', '<>', 0)
            ->where('polda_id', $poldaId)
            ->orderBy('name')
            ->get();

        return response()->json($polres);
    }

    /* ─────────────────────────────────────────────────
     | EXPORT EXCEL
     ───────────────────────────────────────────────── */
    public function exportExcel(Request $request)
    {
        $query = RequestData::with(['polda', 'polres', 'penyediaData']);

        if ($request->filled('jenis_institusi')) {
            $query->where('jenis_institusi', $request->jenis_institusi);
        }
        if ($request->filled('polda_id')) {
            $query->where('polda_id', $request->polda_id);
        }
        if ($request->filled('dari_tanggal')) {
            try {
                $query->where('tanggal_permintaan', '>=', Carbon::parse($request->dari_tanggal)->startOfDay());
            } catch (\Exception $e) {}
        }
        if ($request->filled('hingga_tanggal')) {
            try {
                $query->where('tanggal_permintaan', '<=', Carbon::parse($request->hingga_tanggal)->endOfDay());
            } catch (\Exception $e) {}
        }

        $records = $query->orderBy('tanggal_permintaan', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Request Data');

        // Header
        $sheet->fromArray([[
            'No', 'Nama Pemohon', 'No. Telp',
            'Institusi', 'Tgl Permintaan', 'Tgl Penyajian',
            'Penyedia Data', 'File Data', 'Status',
        ]], null, 'A1');

        $rowNum = 2;
        foreach ($records as $idx => $r) {
            $user = $r->penyediaData;
            $penyediaName = $user
                ? ($user->full_name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')))
                : '-';

            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $r->nama_lengkap_pemohon);
            $sheet->setCellValue("C{$rowNum}", $r->no_telp_pemohon ?? '-');
            $sheet->setCellValue("D{$rowNum}", $r->instansi_label);
            $sheet->setCellValue("E{$rowNum}", $r->tanggal_permintaan?->format('d-m-Y') ?? '-');
            $sheet->setCellValue("F{$rowNum}", $r->tanggal_penyajian?->format('d-m-Y') ?? '-');
            $sheet->setCellValue("G{$rowNum}", $penyediaName);
            $sheet->setCellValue("H{$rowNum}", $r->file_data_name ?? '-');
            $sheet->setCellValue("I{$rowNum}", $r->status);
            $rowNum++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'request_data_' . now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /* ─────────────────────────────────────────────────
     | HELPER
     ───────────────────────────────────────────────── */
    private function getPoldaList()
    {
        $excludePolda = ['90', '99', '80'];
        return Polda::select('id', 'name')
            ->whereNotIn('id', $excludePolda)
            ->orderBy('name')
            ->get();
    }
}
