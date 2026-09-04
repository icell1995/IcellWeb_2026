<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class rekapController extends Controller
{
    /** Halaman utama: render view + dropdown filter (terkunci per akun) */
    public function index(Request $request)
    {
        $user         = Auth::user();
        $roleId       = (int) ($user->role_id ?? 0);
        $userPoldaId  = $user->polda_id ?? null;
        $userPolresId = $user->polres_id ?? null;

        // daftar polda yang harus di-exclude
        $excludePolda = ['90','99','80'];

        // Lock UI
        $lockPolres = !empty($userPolresId);
        $lockPolda  = !$lockPolres && !empty($userPoldaId);

        if ($lockPolres) {
            $poldas  = DB::table('polda')->select('id','name')
                        ->whereNotIn('id', $excludePolda)
                        ->where('id', $userPoldaId)->get();

            $polress = DB::table('polres')->select('id','name')
                        ->where('state','<>',0)
                        ->where('id', $userPolresId)->get();
        } elseif ($lockPolda) {
            $poldas  = DB::table('polda')->select('id','name')
                        ->whereNotIn('id', $excludePolda)
                        ->where('id', $userPoldaId)->get();

            $polress = DB::table('polres')->select('id','name')
                        ->where('state','<>',0)
                        ->where('polda_id', $userPoldaId)
                        ->orderBy('name')->get();
        } else {
            // pusat: semua polda (kecuali yang di-exclude), polres di-load via ajax/ganti polda
            $poldas  = DB::table('polda')->select('id','name')
                        ->whereNotIn('id', $excludePolda)
                        ->orderBy('name')->get();

            $polress = collect([]);
        }

        $locked = [
            'is_lock_polres' => $lockPolres,
            'is_lock_polda'  => $lockPolda,
            'polda_id'       => $userPoldaId,
            'polres_id'      => $userPolresId,
        ];

        return view('rekap.rekap-index', compact('poldas','polress','locked','roleId'));
    }

    /** JSON: data untuk DataTables (client-side) */
    public function listAll(Request $request)
    {
        $user         = Auth::user();
        $roleId       = (int) ($user->role_id ?? 0);
        $userPoldaId  = $user->polda_id ?? null;
        $userPolresId = $user->polres_id ?? null;

        $noLp     = trim((string) $request->input('no_lp',''));
        $status   = trim((string) $request->input('status',''));
        $poldaId  = trim((string) $request->input('polda',''));
        $polresId = trim((string) $request->input('polres',''));
        $dateType = trim((string) $request->input('date_type', 'accident_date'));
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $df = $this->normalizeYmd($dateFrom);
        $dt = $this->normalizeYmd($dateTo);

        // pusat harus pakai minimal salah satu filter
        if ($roleId === 1) {
            $hasFilter =
                ($noLp !== '' && mb_strlen($noLp) >= 3) ||
                ($status !== '') || ($poldaId !== '') || ($polresId !== '') || ($df && $dt);
            if (!$hasFilter) return response()->json([]);
        }

        $excludePolda = ['90','99','80'];

        $q = DB::table('accidents as a')
            ->leftJoin('ref as r',  'r.id',  '=', 'a.selra_flag')
            ->leftJoin('polres as pr','pr.id','=', 'a.polres_id')
            ->leftJoin('polda as pd', 'pd.id','=', 'pr.polda_id')
            ->where('r.grp_id', 'S01')
            // >>> kondisi global yang diminta <<<
            ->whereNotIn('pd.id', $excludePolda)
            ->where('pr.state','<>', 0)
            ->selectRaw("
                a.id,
                a.no_lp,
                to_char(a.accident_date, 'DD-MM-YYYY') as accident_date,
                to_char(a.report_date, 'DD-MM-YYYY') as report_date,
                to_char(a.created_at,    'DD-MM-YYYY') as accident_tindak_lanjut,
                (
                    CASE
                        WHEN a.selra_flag <> 'S0107'
                            THEN age(a.updated_at, a.created_at)
                        ELSE age(now(), a.created_at)
                    END
                )::text as accident_proses,
                r.name as selra_flag,
                a.selra_flag as selra
            ");

        // hard lock server-side berdasar akun
        if (!empty($userPolresId)) {
            $q->where('pr.id', $userPolresId);
        } elseif (!empty($userPoldaId)) {
            $q->where('pd.id', $userPoldaId);
        } else {
            if ($polresId !== '')      $q->where('pr.id', $polresId);
            elseif ($poldaId !== '')   $q->where('pd.id', $poldaId);
        }

        // filters tambahan
        if ($noLp !== '')   $q->where('a.no_lp', 'ilike', "%{$noLp}%");
        if ($status !== '') $q->where('a.selra_flag', $status);
        if ($df && $dt) {
            if ($dateType === 'report_date') {
                $q->whereBetween('a.report_date', [$df, $dt]);
            } else {
                $q->whereBetween('a.accident_date', [$df, $dt]);
            }
        }

        $q->orderByDesc('a.accident_date')->orderByDesc('a.id');

        return response()->json($q->get());
    }

    public function show(string $id)
    {
        $data = DB::table('accidents as a')
            ->leftJoin('ref as r',  'r.id',  '=', 'a.selra_flag')
            ->leftJoin('polres as pr','pr.id','=', 'a.polres_id')
            ->leftJoin('polda as pd', 'pd.id','=', 'pr.polda_id')
            ->where('a.id', $id)
            ->selectRaw("
                a.id, a.no_lp, a.accident_date, a.created_at, a.updated_at,
                r.name as selra_flag, a.selra_flag as selra,
                pr.name as polres, pd.name as polda
            ")
            ->first();

        return view('rekap.rekap-show', compact('data'));
    }

    private function normalizeYmd(?string $val): ?string
    {
        if (!$val) return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $val)) {
            try { return Carbon::createFromFormat('d-m-Y', $val)->format('Y-m-d'); }
            catch (\Throwable $e) { return null; }
        }
        return null;
    }
}
