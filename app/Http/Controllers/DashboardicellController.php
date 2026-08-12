<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Lib\Police;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardicellController extends Controller
{
    public function index()
    {
        $day = Carbon::now()->day;
        $now = Carbon::now()->month;
        $year = Carbon::now()->year;
        $dateNow = Carbon::now()->toDateString();
        // dd($dateNow);
        $data = DB::select("
            select
            polda.name,
            count(case when selra_flag='S0101' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as p21,
            count(case when selra_flag='S0102' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as sp3,
            count(case when selra_flag='S0103' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as diversi,
            count(case when selra_flag='S0104' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as pomtni,
            count(case when selra_flag='S0106' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as adrrj,
            count(case when selra_flag='S0107' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as dalamproses,
            count(case when selra_flag='S0108' and date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as sp2lid,
            count(case when date_part('month',accidents.accident_date)=$now and date_part('year',accidents.accident_date)=$year then 1 else null end) as total
            from accidents
            right join polres on polres.id = accidents.polres_id
            right join polda on polda.id = polres.polda_id
            where polda.id not in ('90','99')
            group by polda.name order by polda.name asc
        ");
        $total = DB::select("
            select
            count(case when selra_flag='S0101' and DATE(created_at)='$dateNow'  then 1 else null end) as p21,
            count(case when selra_flag='S0102' and DATE(created_at)='$dateNow'  then 1 else null end) as sp3,
            count(case when selra_flag='S0103' and DATE(created_at)='$dateNow'  then 1 else null end) as diversi,
            count(case when selra_flag='S0104' and DATE(created_at)='$dateNow'  then 1 else null end) as pomtni,
            count(case when selra_flag='S0106' and DATE(created_at)='$dateNow'  then 1 else null end) as adrrj,
            count(case when selra_flag='S0107' and DATE(created_at)='$dateNow'  then 1 else null end) as dalamproses,
            count(case when selra_flag='S0108' and DATE(created_at)='$dateNow'  then 1 else null end) as sp2lid,
            count(case when DATE(created_at)='$dateNow'  then 1 else null end) as totalall
            from accidents
        ");

        $get_dpo = DB::select("select coalesce(count(*),0) as total_dpo from dpo join accidents ON accidents.id = dpo.accident_id where dpo.state = '0' and date_part('month',dpo.created_at)=$now");
        $get_dpb = DB::select("select coalesce(count(*),0) as total_dpb from dpb join accidents ON accidents.id = dpb.accident_id where dpb.state = '0' and date_part('month',dpb.created_at)=$now");

        $get_selra = DB::select(
            '
            SELECT
            SUM(total_selra) AS overall_total_selra
            FROM (
                SELECT
                    ref.name,
                    COALESCE(SUM(ACCIDENT.jumlah_selra), 0) AS total_selra
                FROM
                    ref
                LEFT JOIN (
                    SELECT
                    REF.ID AS ID,
                    ref.name,
                    COALESCE(COUNT(accidents.id), 0) AS jumlah_selra
                    FROM
                    accidents
                    LEFT JOIN polres ON accidents.polres_id = polres.id
                    LEFT JOIN polda ON polres.polda_id = polda.id
                    LEFT JOIN ref ON accidents.selra_flag = ref.id
                    WHERE
                    polres.state = 1
                    AND polda.state = 1
                    and date_part(\'year\',  accidents.created_at) = \'' . $year . '\'
                    and ref.grp_id = \'S01\'
                    GROUP BY REF.ID, ref.name, ref.sort
                    ORDER BY ref.sort
                ) AS ACCIDENT ON REF.ID = ACCIDENT.ID
                WHERE
                    ref.grp_id = \'S01\'
                    AND ref.id NOT IN (\'S0107\', \'S0106\')
                GROUP BY ref.name, ref.sort
            ) AS overall_totals;
            '
        );

        $selra = DB::select(
            "
            SELECT
                polda.name,
                total_p21.total AS p21,
                total_sp3.total AS sp3,
                total_diversi.total AS diversi,
                total_pom_tni.total AS pom_tni,
                total_sp2lid.total AS sp2lid
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            LEFT JOIN(
                SELECT polda.id as id,
                count(accidents.id) AS total
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                JOIN accidents ON polres.id = accidents.polres_id
                JOIN accident_resolutions ON accidents.id = accident_resolutions.accident_id
                WHERE EXTRACT(YEAR FROM accident_resolutions.created_at) = $year
                AND selra_flag = 'S0101'
                AND polda.state <> 0
                AND polres.state <> 0
                AND accidents.state <> 0
                GROUP BY 1
            )total_p21 on polda.id = total_p21.id or polres.id = total_p21.id
            LEFT JOIN(
                SELECT polda.id as id,
                count(accidents.id) AS total
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                JOIN accidents ON polres.id = accidents.polres_id
                JOIN accident_resolutions ON accidents.id = accident_resolutions.accident_id
                WHERE EXTRACT(YEAR FROM accident_resolutions.created_at) = $year
                AND selra_flag = 'S0102'
                AND polda.state <> 0
                AND polres.state <> 0
                AND accidents.state <> 0
                GROUP BY 1
            )total_sp3 on polda.id = total_sp3.id or polres.id = total_sp3.id
            LEFT JOIN(
                SELECT polda.id as id,
                count(accidents.id) AS total
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                JOIN accidents ON polres.id = accidents.polres_id
                JOIN accident_resolutions ON accidents.id = accident_resolutions.accident_id
                WHERE EXTRACT(YEAR FROM accident_resolutions.created_at) = $year
                AND selra_flag = 'S0103'
                AND polda.state <> 0
                AND polres.state <> 0
                AND accidents.state <> 0
                GROUP BY 1
            )total_diversi on polda.id = total_diversi.id or polres.id = total_diversi.id
            LEFT JOIN(
                SELECT polda.id as id,
                count(accidents.id) AS total
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                JOIN accidents ON polres.id = accidents.polres_id
                JOIN accident_resolutions ON accidents.id = accident_resolutions.accident_id
                WHERE EXTRACT(YEAR FROM accident_resolutions.created_at) = $year
                AND selra_flag = 'S0104'
                AND polda.state <> 0
                AND polres.state <> 0
                AND accidents.state <> 0
                GROUP BY 1
            )total_pom_tni on polda.id = total_pom_tni.id or polres.id = total_pom_tni.id
            LEFT JOIN(
                SELECT polda.id as id,
                count(accidents.id) AS total
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                JOIN accidents ON polres.id = accidents.polres_id
                JOIN accident_resolutions ON accidents.id = accident_resolutions.accident_id
                WHERE EXTRACT(YEAR FROM accident_resolutions.created_at) = $year
                AND selra_flag = 'S0108'
                AND polda.state <> 0
                AND polres.state <> 0
                AND accidents.state <> 0
                GROUP BY 1
            )total_sp2lid on polda.id = total_sp2lid.id or polres.id = total_sp2lid.id
            WHERE
            polda.state <> 0
            AND polres.state <> 0
            AND polda.id NOT IN ('90','99')
            GROUP BY 1,2,3,4,5,6
            "
        );

        $countDORS = DB::table('stg_dors_accidents')->whereDate('waktu_kejadian', $dateNow)->count();

        $total_p21 = 0;
        $total_sp3 = 0;
        $total_diversi = 0;
        $total_pom_tni = 0;
        $total_sp2lid = 0;

        // Menghitung total untuk setiap selra
        foreach ($selra as $row) {
            $total_p21 += $row->p21 ?? 0;
            $total_sp3 += $row->sp3 ?? 0;
            $total_diversi += $row->diversi ?? 0;
            $total_pom_tni += $row->pom_tni ?? 0;
            $total_sp2lid += $row->sp2lid ?? 0;
        }

        // $response = Http::withHeaders([
        //     'Key' => '09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL',
        //     'Content-Type' => 'application/json',
        // ])->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/get_total_dors')->json();

        $dpo = $get_dpo[0]->total_dpo;
        $dpb = $get_dpb[0]->total_dpb;
        $total_selra = $get_selra[0]->overall_total_selra;
        // $DorsCount = $response['data'][0]['jumlah'] ?? 0;

        // Get Pejabat TTE data
        $pejabatTTE = $this->getPejabatTTE();
        $total_pejabat_tte = $pejabatTTE->total_pejabat_tte;
        $total_pejabat = $pejabatTTE->total_pejabat;
        $total_polres = $pejabatTTE->total_polres;
        $active_tte_units = $pejabatTTE->active_tte_units;
        $persentase_pejabat_tte = $total_pejabat > 0 ? round(($total_pejabat_tte / $total_pejabat) * 100) : 0;
        $persentase_polres_tte = $total_polres > 0 ? round(($active_tte_units / $total_polres) * 100) : 0;

        return view('dashboard-icell', compact(
            'data',
            'total',
            'now',
            'dpo',
            'dpb',
            'total_selra',
            'selra',
            'total_p21',
            'total_sp3',
            'total_diversi',
            'total_pom_tni',
            'total_sp2lid',
            'countDORS',
            'total_pejabat_tte',
            'total_pejabat',
            'total_polres',
            'active_tte_units',
            'persentase_pejabat_tte',
            'persentase_polres_tte'
        ));
    }

    /**
     * Get statistics for TTE officials
     *
     * @return object
     */
    public function getPejabatTTE()
    {
        // Get year for filtering
        $year = Carbon::now()->year;

        // Get total polres count
        $totalPolres = $this->getActivePolres()->count();

        // Get unique police_id with active TTE
        $activeTTEUnits = $this->getActiveTTEOfficials()->total_unique_police;

        // Get detailed TTE data for current year
        $result = DB::select("
            SELECT 
                COUNT(CASE WHEN officers.passphrase IS NOT NULL THEN 1 ELSE NULL END) AS total_pejabat_tte,
                COUNT(CASE WHEN officers.status = 'PRESENT' THEN 1 ELSE NULL END) AS total_pejabat
            FROM users
            JOIN officers ON officers.user_id = users.id
            JOIN lib.polices ON users.police_id = lib.polices.id
            WHERE lib.polices.is_active = true
            AND EXTRACT(YEAR FROM users.created_at) = $year
        ");

        // Add additional information to result
        $data = $result[0];
        $data->total_polres = $totalPolres;
        $data->active_tte_units = $activeTTEUnits;

        return $data;
    }
    public function updateContent()
    {
        $dashBar = $this->getDashBar();
        $dashPie = $this->getDashPie();
        $day = Carbon::now()->day;
        $now = Carbon::now()->month;
        $year = Carbon::now()->year;
        $dateNow = Carbon::now()->toDateString();

        $countDORS = DB::table('stg_dors_accidents')->whereDate('waktu_kejadian', $dateNow)->count();

        $total = DB::select("
            select
            count(case when selra_flag='S0101' and DATE(created_at)='$dateNow' then 1 else null end) as p21,
            count(case when selra_flag='S0102' and DATE(created_at)='$dateNow' then 1 else null end) as sp3,
            count(case when selra_flag='S0103' and DATE(created_at)='$dateNow' then 1 else null end) as diversi,
            count(case when selra_flag='S0104' and DATE(created_at)='$dateNow' then 1 else null end) as pomtni,
            count(case when selra_flag='S0106' and DATE(created_at)='$dateNow' then 1 else null end) as adrrj,
            count(case when selra_flag='S0107' and DATE(created_at)='$dateNow' then 1 else null end) as dalamproses,
            count(case when selra_flag='S0108' and DATE(created_at)='$dateNow' then 1 else null end) as sp2lid,
            (select count(selra_flag) from accidents where DATE(created_at)='$dateNow') as totalall
            from accidents
        ");

        $get_dpo = DB::select("select coalesce(count(*),0) as total_dpo from dpo join accidents ON accidents.id = dpo.accident_id where dpo.state = '0' and date_part('month',dpo.created_at)=$now");
        $get_dpb = DB::select("select coalesce(count(*),0) as total_dpb from dpb join accidents ON accidents.id = dpb.accident_id where dpb.state = '0' and date_part('month',dpb.created_at)=$now");

        $get_selra = DB::select(
            '
            SELECT
            SUM(total_selra) AS overall_total_selra
            FROM (
                SELECT
                ref.name,
                COALESCE(SUM(ACCIDENT.jumlah_selra), 0) AS total_selra
                FROM ref
                LEFT JOIN (
                    SELECT
                        REF.ID AS ID,
                        ref.name,
                        COALESCE(COUNT(accidents.id), 0) AS jumlah_selra
                    FROM
                        accidents
                    LEFT JOIN polres ON accidents.polres_id = polres.id
                    LEFT JOIN polda ON polres.polda_id = polda.id
                    LEFT JOIN ref ON accidents.selra_flag = ref.id
                    WHERE
                        polres.state = 1
                        AND polda.state = 1
                        and date_part(\'year\',  accidents.created_at) = \'' . $year . '\'
                        and ref.grp_id = \'S01\'
                        GROUP BY REF.ID, ref.name, ref.sort
                    ORDER BY ref.sort
                ) AS ACCIDENT ON REF.ID = ACCIDENT.ID
                WHERE ref.grp_id = \'S01\'
                AND ref.id NOT IN (\'S0107\', \'S0106\')
                GROUP BY ref.name, ref.sort
            ) AS overall_totals;
            '
        );

        // $response = Http::withHeaders([
        //     'Key' => '09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL',
        //     'Content-Type' => 'application/json',
        // ])->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/get_total_dors')->json();

        $dpo = $get_dpo[0]->total_dpo;
        $dpb = $get_dpb[0]->total_dpb;
        $total_selra = $get_selra[0]->overall_total_selra;
        $pejabatTTE = $this->getPejabatTTE();
        // $DorsCount = $response['data'][0]['jumlah'] ?? 0;

        return response()->json([
            'total' => $total,
            'dpo' => $dpo,
            'dpb' => $dpb,
            'total_selra' => $total_selra,
            'countDORS' => $countDORS,
            'pejabatTTE' => [
                'total_pejabat_tte' => $pejabatTTE->total_pejabat_tte,
                'total_pejabat' => $pejabatTTE->total_pejabat,
                'total_polres' => $pejabatTTE->total_polres,
                'active_tte_units' => $pejabatTTE->active_tte_units,
                'persentase_pejabat_tte' => $pejabatTTE->total_pejabat > 0 ?
                    round(($pejabatTTE->total_pejabat_tte / $pejabatTTE->total_pejabat) * 100) : 0,
                'persentase_polres_tte' => $pejabatTTE->total_polres > 0 ?
                    round(($pejabatTTE->active_tte_units / $pejabatTTE->total_polres) * 100) : 0
            ],
            // 'dashBar' => $dashBar,
            // 'dashPie' => $dashPie,
        ]);
    }

    public function getDashBar()
    {
        $range = 11;
        $outputs = collect();

        // for ($x = 0; $x <= $range; $x++) {
        //     $current_month = Carbon::now()->subMonth($range - $x);
        //     $date = $current_month->month;
        //     $date_year = $current_month->year;

        //     // $counts = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
        //     $counts = DB::table('accidents')
        //         ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
        //     // dd($counts);
        //     $count = intval($counts);
        //     // setlocale(LC_TIME, 'id');
        //     // $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
        //     $date = $current_month->translatedFormat('F') . " " . $current_month->year;
        //     $output = collect(['date' => $date, 'count' => $count]);
        //     $outputs->push($output);
        // }

        for ($x = 0; $x <= $range; $x++) {
            $current_month = Carbon::now()->startOfMonth()->subMonth($range - $x);
            $start_date = $current_month->startOfMonth()->toDateString();
            $end_date = $current_month->endOfMonth()->toDateString();

            $counts = DB::table('accidents')
                ->whereBetween('accidents.created_at', [$start_date, $end_date])->get(['accidents.id'])->count();
            // dd($counts);
            $count = intval($counts);
            $date = $current_month->translatedFormat('F Y');
            $output = collect(['date' => $date, 'count' => $count]);
            $outputs->push($output);
        }
        return response()->json($outputs);
    }

    public function getDashPie()
    {
        $range = 11;
        $outputs = collect();
        $jumlah_laka = 0;

        $current_month = Carbon::now()->subMonth(0);
        // $current_year = Carbon::now()->subYear();
        $lastYear = Carbon::now()->subYear()->year;
        // dd($lastYear);
        $month = $current_month->formatLocalized('%B');
        $date_month = $month;
        $date_year = $current_month->year;
        // dd($date_year);

        $jumlah_laka = DB::table('accidents')
            ->join('polres', 'accidents.polres_id', 'polres.id')
            ->join('polda', 'polres.polda_id', 'polda.id')
            ->where('polres.state', '=', 1)
            ->where('polda.state', '=', 1)
            ->whereNotIn('accidents.selra_flag', ['S0107'])
            ->whereYear('accidents.created_at', '=', $date_year)
            ->get(['accidents.id'])
            ->count();

        $jumlah_laka_lastYear = DB::table('accidents')
            ->join('polres', 'accidents.polres_id', 'polres.id')
            ->join('polda', 'polres.polda_id', 'polda.id')
            ->where('polres.state', '=', 1)
            ->where('polda.state', '=', 1)
            ->whereNotIn('accidents.selra_flag', ['S0107'])
            ->whereYear('accidents.created_at', '=', $lastYear)
            ->get(['accidents.id'])
            ->count();

        $selra_id = DB::table('ref')
            ->where('ref.grp_id', '=', 'S01')->orderBy('sort')->get();

        $jumlah_selra = DB::select(
            '
            select
                ref.name,
                coalesce(accident.jumlah_selra,0) as percentage
                from ref
                LEFT JOIN
                (
                    select
                    REF.ID AS ID,
                    ref.name,
                    coalesce(count(accidents.id),0) as jumlah_selra
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    left join polda on polres.polda_id = polda.id
                    left join ref on accidents.selra_flag = ref.id
                    where
                    polres.state = 1
                    and polda.state = 1
                    and date_part(\'year\',  accidents.created_at) = \'' . $date_year . '\'
                    and ref.grp_id = \'S01\'
                    group by REF.ID,ref.name,ref.sort
                    order by ref.sort
                ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                where
                    ref.grp_id = \'S01\'
                AND
                    ref.id NOT IN (\'S0107\', \'S0106\')
            '
        );

        $jumlah_selra_lastYear = DB::select(
            '
            select
                ref.name,
                coalesce(accident.jumlah_selra_lastYear,0) as percentage_lastYear
                from ref
                LEFT JOIN
                (
                    select
                    REF.ID AS ID,
                    ref.name,
                    coalesce(count(accidents.id),0) as jumlah_selra_lastYear
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    left join polda on polres.polda_id = polda.id
                    left join ref on accidents.selra_flag = ref.id
                    where
                    polres.state = 1
                    and polda.state = 1
                    and date_part(\'year\',  accidents.created_at) = \'' . $lastYear . '\'
                    and ref.grp_id = \'S01\'
                    group by REF.ID,ref.name,ref.sort
                    order by ref.sort
                ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                where
                    ref.grp_id = \'S01\'
                AND
                    ref.id NOT IN (\'S0107\', \'S0106\')
            '
        );

        // dd($jumlah_selra_lastYear);
        $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'lastYear' => $lastYear, 'jumlah_laka' => $jumlah_laka, 'jumlah_laka_lastYear' => $jumlah_laka_lastYear, 'jumlah_selra' => $jumlah_selra, 'jumlah_selra_lastYear' => $jumlah_selra_lastYear]);
        $outputs->push($output);

        return response()->json($outputs);
    }

    /**
     * Get total active Polres (Resort Police) based on specific criteria
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getActivePolres()
    {
        $polres = DB::select("
            SELECT id, parent_id, class, name, full_name 
            FROM lib.polices 
            WHERE class = 'RESOR'
            AND is_active = true
            AND parent_id NOT IN ('77', '80', '99')
            ORDER BY parent_id ASC
        ");

        return collect($polres);
    }

    /**
     * Get total active TTE officials with unique police_id count
     * 
     * @return object
     */
    public function getActiveTTEOfficials()
    {
        $result = DB::select("
            SELECT COUNT(DISTINCT users.police_id) AS total_unique_police
            FROM users
            JOIN officers ON officers.user_id = users.id
            JOIN lib.polices ON officers.police_id = lib.polices.id
            WHERE officers.passphrase IS NOT NULL
                AND officers.status = 'PRESENT'
                AND officers.state = '1'
                AND officers.is_active = true
                AND lib.polices.class = 'RESOR'
                AND lib.polices.is_active = true
        ");

        return $result[0];
    }
}