<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ReportController extends BaseController
{
    //

   public function report_indinvidu(Request $request){
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $null = '-';
        $data = DB::select("
            select
                officer.id,
                officer.first_name,
                officer.last_name,
                polres.name as polres_name,
                coalesce(sidik.jumlah_sidik, 0) as total_laka_ditangani,
                coalesce(sidik.jumlah_proses, 0) as total_laka_dalam_proses,
                coalesce(sidik.jumlah_selesai, 0) as total_laka_selesai,
                foto.avatars
            from officer
            left join (
                select
                    surat_penyidikan.officer_id as sidik_id,
                    count(accident_id) as jumlah_sidik,
                    count(accident_id) filter (where accidents.selra_flag = 'S0106') as jumlah_proses,
                    count(accident_id) filter (where accidents.selra_flag <> 'S0106') as jumlah_selesai
                from surat_penyidikan
                left join accidents on accidents.id = surat_penyidikan.accident_id
                group by surat_penyidikan.officer_id
            ) as sidik on officer.id = sidik.sidik_id
            left join (
                select users.avatar as avatars, users.officer_id as users_id from users
            ) as foto on officer.id = foto.users_id
            left join polda on polda.id = officer.polda_id
            left join polres on polres.id = officer.polres_id
            where
                CASE WHEN ? <> ? THEN officer.polda_id = ? ELSE TRUE END
                AND
                CASE WHEN ? <> ? THEN officer.polres_id = ? ELSE TRUE END
        ", [$polda, $null, $polda, $polres, $null, $polres]);

        return $this->sendResponse($data, 'Data Successfully');
    }

    public function report_maps(Request $request){
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $dateFrom = !empty($request->dateFrom) && $request->dateFrom !== '-' ? Carbon::parse($request->dateFrom)->format('Y-m-d') : '-';
        $null = '-';
        $data = DB::select("
            select accidents.id, no_lp, latitude, longtitude, road_name, md, lb, lr
            from accidents
            left join polres on polres.id = accidents.polres_id
            left join polda on polda.id = polres.polda_id
            where
                CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                AND
                CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
                AND
                CASE WHEN ? <> ? THEN accident_date = ? ELSE TRUE END
        ", [$polda, $null, $polda, $polres, $null, $polres, $dateFrom, $null, $dateFrom]);
        return $this->sendResponse($data, 'Data Successfully');
    }

    public function report_polres(Request $request){
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $null = '-';
        $dateFrom = !empty($request->dateFrom) && $request->dateFrom !== '-' ? Carbon::parse($request->dateFrom)->format('Y-m-d') : '-';
        $dateTo = !empty($request->dateTo) && $request->dateTo !== '-' ? Carbon::parse($request->dateTo)->format('Y-m-d') : '-';

        $data = DB::select("
            select
                CASE WHEN ? = ? THEN polda.name ELSE polres.name END AS polres_names,
                agg.p21 AS p21,
                agg.sp3 AS sp3,
                agg.diversi AS diversi,
                agg.pomtni AS pomtni,
                agg.rj AS rj,
                agg.dalamproses AS dalamproses
            from polda
            left join polres on polda.id = polres.polda_id
            left join (
                SELECT
                    CASE WHEN ? = ? THEN polda.id ELSE polres.id END AS id,
                    count(accidents.id) filter (where accidents.selra_flag = 'S0101') AS p21,
                    count(accidents.id) filter (where accidents.selra_flag = 'S0102') AS sp3,
                    count(accidents.id) filter (where accidents.selra_flag = 'S0103') AS diversi,
                    count(accidents.id) filter (where accidents.selra_flag = 'S0104') AS pomtni,
                    count(accidents.id) filter (where accidents.selra_flag = 'S0105') AS rj,
                    count(accidents.id) filter (where accidents.selra_flag = 'S0106') AS dalamproses
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                JOIN accidents ON polres.id = accidents.polres_id
                WHERE
                    CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                    AND
                    CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
                    AND
                    CASE WHEN ? <> ? THEN accident_date between ? and ? ELSE TRUE END
                    AND polda.state <> 0
                    AND polres.state <> 0
                GROUP BY 1
            ) agg on polda.id = agg.id or polres.id = agg.id
            where
                CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                AND
                CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
            GROUP BY polres_names, p21, sp3, diversi, pomtni, rj, dalamproses
        ", [
            $polda, $null,
            $polda, $null,
            $polda, $null, $polda,
            $polres, $null, $polres,
            $dateFrom, $null, $dateFrom, $dateTo,
            $polda, $null, $polda,
            $polres, $null, $polres,
        ]);

        return $this->sendResponse($data, 'Data Successfully');
    } 
}
