<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportRekap;

class ExportRekapController extends Controller
{
    public function ExportRekap(Request $request)
    {
        $user = Auth::user();
        $checkuser = $user->role_id ?? 0;
        $no_lp = $request->input('no_lp');
        $polres = $request->input('polres');
        $polda = $request->input('polda');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $checkstatus = $request->input('status');

        // Batasi scope berdasarkan role pengguna
        if ($checkuser == 2) {
            $polda = $user->polda_id;
        } elseif (in_array($checkuser, [3, 4])) {
            $polda = $user->polda_id;
            $polres = $user->polres_id;
        }

        $query = DB::table('accidents')
            ->leftJoin('ref', 'ref.id', '=', 'accidents.selra_flag')
            ->leftJoin('polres', 'polres.id', '=', 'accidents.polres_id')
            ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
            ->where('ref.grp_id', '=', 'S01')
            ->select([
                'accidents.no_lp as no_lp',
                DB::raw("to_char(accidents.accident_date, 'DD-MM-YYYY') as accident_date"),
                DB::raw("to_char(accidents.created_at, 'DD-MM-YYYY') as accident_tindak_lanjut"),
                DB::raw("CASE WHEN accidents.selra_flag <> 'S0107' THEN AGE(accidents.updated_at, accidents.created_at) ELSE AGE(NOW(), accidents.created_at) END AS accident_proses"),
                DB::raw("to_char(accidents.last_update, 'DD-MM-YYYY HH24:MI:SS') as accident_last_update"),
                DB::raw("concat(accidents.tipe_update, ' ', (select r2.name from ref r2 where r2.id = accidents.category)) AS tipe_berkas"),
                'ref.name as selra_flag'
            ]);

        if (!empty($no_lp) && $no_lp !== '-') {
            $query->where('accidents.no_lp', 'ILIKE', '%' . $no_lp . '%');
        }

        if (!empty($date_from) && !empty($date_to) && $date_from !== '-' && $date_to !== '-') {
            $query->whereBetween('accidents.accident_date', [
                Carbon::parse($date_from)->format('Y-m-d'),
                Carbon::parse($date_to)->format('Y-m-d')
            ]);
        }

        if (!empty($checkstatus) && $checkstatus !== '-') {
            $query->where('accidents.selra_flag', '=', $checkstatus);
        }

        if (!empty($polda) && $polda !== '-') {
            $query->where('polda.id', '=', $polda);
        }

        if (!empty($polres) && $polres !== '-') {
            $query->where('polres.id', '=', $polres);
        }

        $rekap = $query->orderBy('accidents.created_at', 'desc')->get();

        return Excel::download(new ExportRekap($rekap), "Rekap_" . date("Y-m-d_H-i-s") . ".xlsx");
    }
}
