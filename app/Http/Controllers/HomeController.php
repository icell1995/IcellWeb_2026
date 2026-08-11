<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Image;
use File;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Officer;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Traits\HomeQueryTraits;

use App\Services\IRSMSService;
use App\Services\IRSMSService\IrsmsServices;
use HomeQueryTarits;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected $_title  = 'Beranda';

    function __construct()
    {
        view()->share('_title', $this->_title);
    }

    use HomeQueryTraits;

    public function index()
    {
        $user = Auth::user();

        switch ($user->role_id) {
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;

                if ($user->officer && $user->officer->polres_id === 0) {
                    $get_accident = $this->getAccidents()
                        ->where('polda.id', $polda)
                        ->get();
                    $get_dpo = $this->getDPO()
                        ->where('polda.id', $polda)
                        ->get();
                    $get_dpb = $this->getDPB()
                        ->where('polda.id', $polda)
                        ->get();
                } else {
                    $get_accident = $this->getAccidents()
                        ->where('polres.id', $polres)
                        ->get();
                    $get_dpo = $this->getDPO()
                        ->where('polres.id', $polres)
                        ->get();
                    $get_dpb = $this->getDPB()
                        ->where('polres.id', $polres)
                        ->get();
                }
                break;
            case 4:
                $polda = $user->polda_id;
                $polres = $user->polres_id;

                $get_accident = $this->getAccidents()
                    ->where('polres.id', $polres)
                    ->get();
                $get_dpo = $this->getDPO()
                    ->where('polres.id', $polres)
                    ->get();
                $get_dpb = $this->getDPB()
                    ->where('polres.id', $polres)
                    ->get();
                break;
            default:
                $polda = '-';
                $polres = '-';

                $get_accident = $this->getAccidents()
                    ->get();
                $get_dpo = $this->getDPO()
                    ->where('dpo.state', '0')
                    ->where('polres.state', '1')
                    ->get();
                $get_dpb = $this->getDPB()
                    ->where('polres.state', '1')
                    ->get();
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/get_total_accident?user=" . $user->role_id . "&polda=" . $polda . "&polres=" . $polres,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        //  dd($response);
        curl_close($curl);

        if ($err) {
            //  echo "cURL Error #:" . $err;
            $total_laka = 'Coba Refresh Kembali';
        } else {
            //  print_r(json_decode($response));
            //  dd(json_decode($response));
            $get_data = json_decode($response);
            $total_laka = ($get_data) ? $get_data->result[0]->total : 'Null';
            //  $data = $response->json();
        }

        $accident = $get_accident[0]->total_laka;
        $dpo = $get_dpo[0]->total_dpo;
        $dpb = $get_dpb[0]->total_dpb;

	// dd($dpo);

        $totalService = new IrsmsServices();

        $beginDate = "2024-01-01";
        $limitDate = "2024-09-30";

        $caseResolutions = $this->caseResolutions($beginDate, $limitDate)
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        $caseCollections = $totalService->getDataWithDateRange($beginDate, $limitDate);
        $caseResolutionCollections = collect($caseResolutions);

        // Rekapitulasi Selra 2024 ( Lomba )

        $recapLombaBeginDate = "2024-01-01";
        $recapLombaLimitDate = "2024-12-31";
        $recapLombaNewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
        $recapLombaNewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';

        $recapLombaCaseResolutions = $this->recapLombaCaseResolutions(
            $recapLombaBeginDate,
            $recapLombaLimitDate,
            $recapLombaNewCrimeClearanceStartTime,
            $recapLombaNewCrimeClearanceEndTime
        )
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        $recapLombaCaseCollections = $totalService->getDataWithDateRange($recapLombaBeginDate, $recapLombaLimitDate);
        $recapLombaCaseResolutionCollections = collect($recapLombaCaseResolutions);

        $recapLombaLeaderboardItems = $recapLombaCaseCollections->map(function ($item) use ($recapLombaCaseResolutionCollections) {
            $match = $recapLombaCaseResolutionCollections->firstWhere('polres_id', $item['polres']);

            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;
                $pomtni = $match->pomtni ?? 0;
                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $total = $p21 + $sp3 + $sp2lid + $diversi;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'pomtni' => $pomtni ?? 0,
                    'total' => $total,
                ]);
            }

            return $item;
        });

        $recapLombaLeaderboardItems = collect($recapLombaLeaderboardItems)
            ->groupBy('polda')
            ->map(function ($items, $polda) {
                if (isset($items->first()['polda_name'])) {
                    $accidentTotal = $items->sum('jumlah_laka');
                    $p21Total = $items->sum('p21');
                    $p21TotalWeight = $items->sum('p21') * 6;
                    $sp3Total = $items->sum('sp3');
                    $sp3TotalWeight = $items->sum('sp3') * 2;
                    $diversiTotal = $items->sum('diversi');
                    $diversiTotalWeight = $items->sum('diversi') * 2;
                    $sp2lidTotal = $items->sum('sp2lid');
                    $sp2lidTotalWeight = $items->sum('sp2lid') * 1;
                    $totalTotal = $items->sum('total');
                    $pomtniTotal = $items->sum('pomtni');

                    $accidentNewTotal = $accidentTotal - $pomtniTotal;

                    $selraTotalPercentage = ($accidentTotal != 0) ? ($totalTotal / $accidentNewTotal) * 100 : 0;

                    $maxWeight = $accidentNewTotal * 6;
                    $totalWeight = $p21TotalWeight + $sp3TotalWeight + $diversiTotalWeight + $sp2lidTotalWeight;
                    $weightPercentage = ($maxWeight != 0) ? ($totalWeight / $maxWeight) * 100 : 0;

                    $p21TotalWeightPercentage = ($maxWeight != 0) ? ($p21TotalWeight / $maxWeight) * 100 : 0;
                    $sp3TotalWeightPercentage = ($maxWeight != 0) ? ($sp3TotalWeight / $maxWeight) * 100 : 0;
                    $diversiTotalWeightPercentage = ($maxWeight != 0) ? ($diversiTotalWeight / $maxWeight) * 100 : 0;
                    $sp2lidTotalWeightPercentage = ($maxWeight != 0) ? ($sp2lidTotalWeight / $maxWeight) * 100 : 0;
                    $selraTotalWeightPercentage = $p21TotalWeightPercentage + $sp3TotalWeightPercentage + $diversiTotalWeightPercentage + $sp2lidTotalWeightPercentage;

                    return [
                        'polda' => $polda,
                        'polda_name' => $items->first()['polda_name'],
                        'accident_total' => $accidentTotal,
                        'accident_new_total' => $accidentNewTotal,
                        'p21_total' => $p21Total,
                        'p21_total_weight' => $p21TotalWeight,
                        'p21_total_weight_percentage' => $p21TotalWeightPercentage,
                        'sp3_total' => $sp3Total,
                        'sp3_total_weight' => $sp3TotalWeight,
                        'sp3_total_weight_percentage' => $sp3TotalWeightPercentage,
                        'diversi_total' => $diversiTotal,
                        'diversi_total_weight' => $diversiTotalWeight,
                        'diversi_total_weight_percentage' => $diversiTotalWeightPercentage,
                        'sp2lid_total' => $sp2lidTotal,
                        'sp2lid_total_weight' => $sp2lidTotalWeight,
                        'sp2lid_total_weight_percentage' => $sp2lidTotalWeightPercentage,
                        'pom_tni_total' => $pomtniTotal,
                        'selra_total' => $totalTotal,
                        'selra_total_percentage' => $selraTotalPercentage,
                        'max_weight' => $maxWeight,
                        'total_weight' => $totalWeight,
                        'weight_percentage' => $weightPercentage,
                        'selra_total_weight_percentage' => $selraTotalWeightPercentage,
                    ];
                }
            });

        $recapBeginDate = "2024-01-01";
        $recapLimitDate = "2024-12-31";
        $recapNewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
        $recapNewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';
        $recapExceptCrimeClearanceStartTime = '2025-01-02 00:00:00';
        $recapExceptCrimeClearanceEndTime = date('Y-m-d') . ' 00:00:00';

        $recapCaseResolutions = $this->recapCaseResolutions(
            $recapBeginDate,
            $recapLimitDate,
            $recapNewCrimeClearanceStartTime,
            $recapNewCrimeClearanceEndTime,
            $recapExceptCrimeClearanceStartTime,
            $recapExceptCrimeClearanceEndTime
        )
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->orderBy('xpolices.id')
            ->get();

        $recapCaseCollections = $totalService->getDataWithDateRange($recapBeginDate, $recapLimitDate);
        $recapCaseResolutionCollections = collect($recapCaseResolutions);

        $recapLeaderboardItems = $recapCaseCollections->map(function ($item) use ($recapCaseResolutionCollections) {
            $match = $recapCaseResolutionCollections->firstWhere('polres_id', $item['polres']);

            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;
                $pomtni = $match->pomtni ?? 0;
                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;
                $crime_cleareance_tabraklari = $match->crime_clearance_tabraklari ?? 0;
                $new_entry_crime_clearance = $match->new_entry_crime_clearance ?? 0;
                $p21_except_entry = $match->p21_except_entry ?? 0;
                $sp3_except_entry = $match->sp3_except_entry ?? 0;
                $diversi_except_entry = $match->diversi_except_entry ?? 0;
                $sp2lid_except_entry = $match->sp2lid_except_entry ?? 0;
                $except_entry_crime_clearance = $match->except_entry_crime_clearance ?? 0;

                $jumlah_laka = $item['jumlah_laka'] - $pomtni;
                $hit_and_run = $item['tabrak_lari'] - $crime_cleareance_tabraklari;
                $total = $p21 + $sp3 + $sp2lid + $diversi;
                $in_the_process = $jumlah_laka - $total - $hit_and_run;
                $on_progress = $in_the_process + $hit_and_run;

                $before_eval_crime_clearance = $total - $except_entry_crime_clearance;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total' => $total,

                    'pom_tni' => $pomtni ?? 0,
                    'in_the_process' => $in_the_process,
                    'hit_and_run' => $hit_and_run,
                    'on_progress' => $on_progress,
                    'new_entry_crime_clearance' => $new_entry_crime_clearance,

                    'except_entry_crime_clearance' => $except_entry_crime_clearance,
                    'except_entry_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($except_entry_crime_clearance / $jumlah_laka) * 100) : 0,
                    'p21_except_entry' => $p21_except_entry,
                    'sp3_except_entry' => $sp3_except_entry,
                    'diversi_except_entry' => $diversi_except_entry,
                    'sp2lid_except_entry' => $sp2lid_except_entry,

                    'before_eval_crime_clearance' => $before_eval_crime_clearance,
                    'before_eval_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($before_eval_crime_clearance / $jumlah_laka) * 100) : 0,
                    'before_eval_p21' => $p21 - $p21_except_entry,
                    'before_eval_sp3' => $sp3 - $sp3_except_entry,
                    'before_eval_diversi' => $diversi - $diversi_except_entry,
                    'before_eval_sp2lid' => $sp2lid - $sp2lid_except_entry,

                    'percentage_p21' => ($jumlah_laka != 0) ? (($p21 / $jumlah_laka) * 100) : 0,
                    'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3 / $jumlah_laka) * 100) : 0,
                    'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid / $jumlah_laka) * 100) : 0,
                    'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi / $jumlah_laka) * 100) : 0,
                    'percentage_total' => ($jumlah_laka != 0) ? (($total / $jumlah_laka) * 100) : 0,
                    'percentage_on_progress' => ($jumlah_laka != 0) ? (($on_progress / $jumlah_laka) * 100) : 0,
                ]);
            }

            return $item;
        });
        $recapLeaderboardItems = collect($recapLeaderboardItems)
            ->groupBy('polda')
            ->map(function ($items, $polda) {

                $accidentTotal = $items->sum('jumlah_laka');
                $p21Total = $items->sum('p21');
                $sp3Total = $items->sum('sp3');
                $sp2lidTotal = $items->sum('sp2lid');
                $diversiTotal = $items->sum('diversi');
                $totalTotal = $items->sum('total');

                $inTheProcessTotal = $items->sum('in_the_process');
                $hitAndRunTotal = $items->sum('hit_and_run');
                $onProgressTotal = $items->sum('on_progress');

                $newEntryCrimeClearanceTotal = $items->sum('new_entry_crime_clearance');
                $pomTniTotal = $items->sum('pom_tni');

                $accidentTotalPercentage = 0;
                $p21TotalPercentage = ($accidentTotal != 0) ? (($p21Total / $accidentTotal) * 100) : 0;
                $sp3TotalPercentage = ($accidentTotal != 0) ? (($sp3Total / $accidentTotal) * 100) : 0;
                $sp2lidTotalPercentage = ($accidentTotal != 0) ? (($sp2lidTotal / $accidentTotal) * 100) : 0;
                $diversiTotalPercentage = ($accidentTotal != 0) ? (($diversiTotal / $accidentTotal) * 100) : 0;
                $totalTotalPercentage = ($accidentTotal != 0) ? (($totalTotal / $accidentTotal) * 100) : 0;
                $onProgressTotalPercentage = ($accidentTotal != 0) ? (($onProgressTotal / $accidentTotal) * 100) : 0;

                $exceptEntryCrimeClearanceTotal = $items->sum('except_entry_crime_clearance');
                $exceptEntryCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($exceptEntryCrimeClearanceTotal / $accidentTotal) * 100) : 0;
                $p21ExceptEntryTotal = $items->sum('p21_except_entry');
                $sp3ExceptEntryTotal = $items->sum('sp3_except_entry');
                $diversiExceptEntryTotal = $items->sum('diversi_except_entry');
                $sp2lidExceptEntryTotal = $items->sum('sp2lid_except_entry');

                $beforeEvalCrimeClearanceTotal = $items->sum('before_eval_crime_clearance');
                $beforeEvalCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($beforeEvalCrimeClearanceTotal / $accidentTotal) * 100) : 0;
                $beforeEvalP21Total = $items->sum('before_eval_p21');
                $beforeEvalSp3Total = $items->sum('before_eval_sp3');
                $beforeEvalDiversiTotal = $items->sum('before_eval_diversi');
                $beforeEvalSp2lidTotal = $items->sum('before_eval_sp2lid');

                return [
                    'polda' => $polda,
                    'polda_name' => $items->first()['polda_name'],
                    'accident_total' => $accidentTotal - $pomTniTotal,

                    'p21_total' => $p21Total,
                    'sp3_total' => $sp3Total,
                    'sp2lid_total' => $sp2lidTotal,
                    'diversi_total' => $diversiTotal,
                    'total_total' => $totalTotal,

                    'in_the_process_total' => $inTheProcessTotal,
                    'hit_and_run_total' => $hitAndRunTotal,
                    'on_progress_total' => $onProgressTotal,

                    'accident_total_percentage' => $accidentTotalPercentage,
                    'p21_total_percentage' => $p21TotalPercentage,
                    'sp3_total_percentage' => $sp3TotalPercentage,
                    'sp2lid_total_percentage' => $sp2lidTotalPercentage,
                    'diversi_total_percentage' => $diversiTotalPercentage,
                    'total_total_percentage' => $totalTotalPercentage,

                    'on_progress_total_percentage' => $onProgressTotalPercentage,
                    'new_entry_crime_clearance_total' => $newEntryCrimeClearanceTotal,
                    'pom_tni_total' => $pomTniTotal,

                    'except_entry_crime_clearance_total' => $exceptEntryCrimeClearanceTotal,
                    'except_entry_crime_clearance_total_percentage' => $exceptEntryCrimeClearanceTotalPercentage,
                    'p21_except_entry_total' => $p21ExceptEntryTotal,
                    'sp3_except_entry_total' => $sp3ExceptEntryTotal,
                    'diversi_except_entry_total' => $diversiExceptEntryTotal,
                    'sp2lid_except_entry_total' => $sp2lidExceptEntryTotal,

                    'before_eval_crime_clearance_total' => $beforeEvalCrimeClearanceTotal,
                    'before_eval_crime_clearance_total_percentage' => $beforeEvalCrimeClearanceTotalPercentage,
                    'before_eval_p21_total' => $beforeEvalP21Total,
                    'before_eval_sp3_total' => $beforeEvalSp3Total,
                    'before_eval_diversi_total' => $beforeEvalDiversiTotal,
                    'before_eval_sp2lid_total' => $beforeEvalSp2lidTotal,

                    'polres' => $items->map(function ($item) {
                        if (isset($item['polda_name'])) {
                            return [
                                'polda' => $item['polda'],
                                'polres' => $item['polres'],
                                'name' => $item['name'],
                                'jumlah_laka' => $item['jumlah_laka'] - $item['pom_tni'],
                                'tabrak_lari' => $item['tabrak_lari'],
                                'polda_name' => $item['polda_name'],
                                'polres_name' => $item['polres_name'],

                                'p21' => $item['p21'],
                                'sp3' => $item['sp3'],
                                'sp2lid' => $item['sp2lid'],
                                'diversi' => $item['diversi'],
                                'total' => $item['total'],

                                'in_the_process' => $item['in_the_process'],
                                'hit_and_run' => $item['hit_and_run'],
                                'on_progress' => $item['on_progress'],

                                'percentage_p21' => $item['percentage_p21'],
                                'percentage_sp3' => $item['percentage_sp3'],
                                'percentage_sp2lid' => $item['percentage_sp2lid'],
                                'percentage_diversi' => $item['percentage_diversi'],
                                'percentage_total' => $item['percentage_total'],
                                'percentage_on_progress' => $item['percentage_on_progress'],

                                'new_entry_crime_clearance' => $item['new_entry_crime_clearance'],
                                'pom_tni' => $item['pom_tni'],

                                'except_entry_crime_clearance' => $item['except_entry_crime_clearance'],
                                'except_entry_crime_clearance_percentage' => $item["except_entry_crime_clearance_percentage"],
                                'p21_except_entry' => $item['p21_except_entry'],
                                'sp3_except_entry' => $item['sp3_except_entry'],
                                'diversi_except_entry' => $item['diversi_except_entry'],
                                'sp2lid_except_entry' => $item['sp2lid_except_entry'],

                                'before_eval_crime_clearance' => $item['before_eval_crime_clearance'],
                                'before_eval_crime_clearance_percentage' => $item['before_eval_crime_clearance_percentage'],
                                'before_eval_p21' => $item['before_eval_p21'],
                                'before_eval_sp3' => $item['before_eval_sp3'],
                                'before_eval_diversi' => $item['before_eval_diversi'],
                                'before_eval_sp2lid' => $item['before_eval_sp2lid'],
                            ];
                        }
                    })
                ];
            });

        // Rekap Selra 2025 ( Bukan Lomba )
        $recap2025BeginDate = "2025-01-01";
        $recap2025LimitDateToday = date('Y-m-d');
        $recap2025LimitDate = ($recap2025LimitDateToday > '2025-12-31') ? '2025-12-31' : $recap2025LimitDateToday;
        $recap2025NewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
        $recap2025NewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';
        $recap2025ExceptCrimeClearanceStartTime = '2025-01-02 00:00:00';
        $recap2025ExceptCrimeClearanceEndTime = date('Y-m-d') . ' 00:00:00';

        $recap2025CaseResolutions = $this->recap2025CaseResolutions(
            $recap2025BeginDate,
            $recap2025LimitDate,
            $recap2025NewCrimeClearanceStartTime,
            $recap2025NewCrimeClearanceEndTime,
            $recap2025ExceptCrimeClearanceStartTime,
            $recap2025ExceptCrimeClearanceEndTime
        )
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->orderBy('xpolices.id')
            ->get();

        $recap2025CaseCollections = $totalService->getDataWithDateRange($recap2025BeginDate, $recap2025LimitDate);
        $recap2025CaseResolutionCollections = collect($recap2025CaseResolutions);

        $recap2025LeaderboardItems = $recap2025CaseCollections->map(function ($item) use ($recap2025CaseResolutionCollections) {
            $match = $recap2025CaseResolutionCollections->firstWhere('polres_id', $item['polres']);

            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;
                $pomtni = $match->pomtni ?? 0;
                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;
                $crime_cleareance_tabraklari = $match->crime_clearance_tabraklari ?? 0;
		$tabrak_lari = $match->tabrak_lari ?? 0;
                $new_entry_crime_clearance = $match->new_entry_crime_clearance ?? 0;
                $p21_except_entry = $match->p21_except_entry ?? 0;
                $sp3_except_entry = $match->sp3_except_entry ?? 0;
                $diversi_except_entry = $match->diversi_except_entry ?? 0;
                $sp2lid_except_entry = $match->sp2lid_except_entry ?? 0;
                $except_entry_crime_clearance = $match->except_entry_crime_clearance ?? 0;

                $jumlah_laka = $item['jumlah_laka'] - $pomtni;
                //$hit_and_run = $item['tabrak_lari'] - $crime_cleareance_tabraklari;
		$hit_and_run = $tabrak_lari - $crime_cleareance_tabraklari;
                $total = $p21 + $sp3 + $sp2lid + $diversi;
                $in_the_process = $jumlah_laka - $total - $hit_and_run;
                $on_progress = $in_the_process + $hit_and_run;

                $before_eval_crime_clearance = $total - $except_entry_crime_clearance;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total' => $total,

                    'pom_tni' => $pomtni ?? 0,
                    'in_the_process' => $in_the_process,
                    'hit_and_run' => $hit_and_run,
                    'on_progress' => $on_progress,
                    'new_entry_crime_clearance' => $new_entry_crime_clearance,

                    'except_entry_crime_clearance' => $except_entry_crime_clearance,
                    'except_entry_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($except_entry_crime_clearance / $jumlah_laka) * 100) : 0,
                    'p21_except_entry' => $p21_except_entry,
                    'sp3_except_entry' => $sp3_except_entry,
                    'diversi_except_entry' => $diversi_except_entry,
                    'sp2lid_except_entry' => $sp2lid_except_entry,

                    'before_eval_crime_clearance' => $before_eval_crime_clearance,
                    'before_eval_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($before_eval_crime_clearance / $jumlah_laka) * 100) : 0,
                    'before_eval_p21' => $p21 - $p21_except_entry,
                    'before_eval_sp3' => $sp3 - $sp3_except_entry,
                    'before_eval_diversi' => $diversi - $diversi_except_entry,
                    'before_eval_sp2lid' => $sp2lid - $sp2lid_except_entry,

                    'percentage_p21' => ($jumlah_laka != 0) ? (($p21 / $jumlah_laka) * 100) : 0,
                    'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3 / $jumlah_laka) * 100) : 0,
                    'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid / $jumlah_laka) * 100) : 0,
                    'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi / $jumlah_laka) * 100) : 0,
                    'percentage_total' => ($jumlah_laka != 0) ? (($total / $jumlah_laka) * 100) : 0,
                    'percentage_on_progress' => ($jumlah_laka != 0) ? (($on_progress / $jumlah_laka) * 100) : 0,
                ]);
            }

            return $item;
        });
        $recap2025LeaderboardItems = collect($recap2025LeaderboardItems)
            ->groupBy('polda')
            ->map(function ($items, $polda) {

                $accidentTotal = $items->sum('jumlah_laka');
                $p21Total = $items->sum('p21');
                $sp3Total = $items->sum('sp3');
                $sp2lidTotal = $items->sum('sp2lid');
                $diversiTotal = $items->sum('diversi');
                $totalTotal = $items->sum('total');

                $inTheProcessTotal = $items->sum('in_the_process');
                $hitAndRunTotal = $items->sum('hit_and_run');
                $onProgressTotal = $items->sum('on_progress');

                $newEntryCrimeClearanceTotal = $items->sum('new_entry_crime_clearance');
                $pomTniTotal = $items->sum('pom_tni');

                $accidentTotalPercentage = 0;
                $p21TotalPercentage = ($accidentTotal != 0) ? (($p21Total / $accidentTotal) * 100) : 0;
                $sp3TotalPercentage = ($accidentTotal != 0) ? (($sp3Total / $accidentTotal) * 100) : 0;
                $sp2lidTotalPercentage = ($accidentTotal != 0) ? (($sp2lidTotal / $accidentTotal) * 100) : 0;
                $diversiTotalPercentage = ($accidentTotal != 0) ? (($diversiTotal / $accidentTotal) * 100) : 0;
                $totalTotalPercentage = ($accidentTotal != 0) ? (($totalTotal / $accidentTotal) * 100) : 0;
                $onProgressTotalPercentage = ($accidentTotal != 0) ? (($onProgressTotal / $accidentTotal) * 100) : 0;

                $exceptEntryCrimeClearanceTotal = $items->sum('except_entry_crime_clearance');
                $exceptEntryCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($exceptEntryCrimeClearanceTotal / $accidentTotal) * 100) : 0;
                $p21ExceptEntryTotal = $items->sum('p21_except_entry');
                $sp3ExceptEntryTotal = $items->sum('sp3_except_entry');
                $diversiExceptEntryTotal = $items->sum('diversi_except_entry');
                $sp2lidExceptEntryTotal = $items->sum('sp2lid_except_entry');

                $beforeEvalCrimeClearanceTotal = $items->sum('before_eval_crime_clearance');
                $beforeEvalCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($beforeEvalCrimeClearanceTotal / $accidentTotal) * 100) : 0;
                $beforeEvalP21Total = $items->sum('before_eval_p21');
                $beforeEvalSp3Total = $items->sum('before_eval_sp3');
                $beforeEvalDiversiTotal = $items->sum('before_eval_diversi');
                $beforeEvalSp2lidTotal = $items->sum('before_eval_sp2lid');

                return [
                    'polda' => $polda,
                    'polda_name' => $items->first()['polda_name'],
                    'accident_total' => $accidentTotal - $pomTniTotal,

                    'p21_total' => $p21Total,
                    'sp3_total' => $sp3Total,
                    'sp2lid_total' => $sp2lidTotal,
                    'diversi_total' => $diversiTotal,
                    'total_total' => $totalTotal,

                    'in_the_process_total' => $inTheProcessTotal,
                    'hit_and_run_total' => $hitAndRunTotal,
                    'on_progress_total' => $onProgressTotal,

                    'accident_total_percentage' => $accidentTotalPercentage,
                    'p21_total_percentage' => $p21TotalPercentage,
                    'sp3_total_percentage' => $sp3TotalPercentage,
                    'sp2lid_total_percentage' => $sp2lidTotalPercentage,
                    'diversi_total_percentage' => $diversiTotalPercentage,
                    'total_total_percentage' => $totalTotalPercentage,

                    'on_progress_total_percentage' => $onProgressTotalPercentage,
                    'new_entry_crime_clearance_total' => $newEntryCrimeClearanceTotal,
                    'pom_tni_total' => $pomTniTotal,

                    'except_entry_crime_clearance_total' => $exceptEntryCrimeClearanceTotal,
                    'except_entry_crime_clearance_total_percentage' => $exceptEntryCrimeClearanceTotalPercentage,
                    'p21_except_entry_total' => $p21ExceptEntryTotal,
                    'sp3_except_entry_total' => $sp3ExceptEntryTotal,
                    'diversi_except_entry_total' => $diversiExceptEntryTotal,
                    'sp2lid_except_entry_total' => $sp2lidExceptEntryTotal,

                    'before_eval_crime_clearance_total' => $beforeEvalCrimeClearanceTotal,
                    'before_eval_crime_clearance_total_percentage' => $beforeEvalCrimeClearanceTotalPercentage,
                    'before_eval_p21_total' => $beforeEvalP21Total,
                    'before_eval_sp3_total' => $beforeEvalSp3Total,
                    'before_eval_diversi_total' => $beforeEvalDiversiTotal,
                    'before_eval_sp2lid_total' => $beforeEvalSp2lidTotal,

                    'polres' => $items->map(function ($item) {
                        if (isset($item['polda_name'])) {
                            return [
                                'polda' => $item['polda'],
                                'polres' => $item['polres'],
                                'name' => $item['name'],
                                'jumlah_laka' => $item['jumlah_laka'] - $item['pom_tni'],
                                'tabrak_lari' => $item['tabrak_lari'],
                                'polda_name' => $item['polda_name'],
                                'polres_name' => $item['polres_name'],

                                'p21' => $item['p21'],
                                'sp3' => $item['sp3'],
                                'sp2lid' => $item['sp2lid'],
                                'diversi' => $item['diversi'],
                                'total' => $item['total'],

                                'in_the_process' => $item['in_the_process'],
                                'hit_and_run' => $item['hit_and_run'],
                                'on_progress' => $item['on_progress'],

                                'percentage_p21' => $item['percentage_p21'],
                                'percentage_sp3' => $item['percentage_sp3'],
                                'percentage_sp2lid' => $item['percentage_sp2lid'],
                                'percentage_diversi' => $item['percentage_diversi'],
                                'percentage_total' => $item['percentage_total'],
                                'percentage_on_progress' => $item['percentage_on_progress'],

                                'new_entry_crime_clearance' => $item['new_entry_crime_clearance'],
                                'pom_tni' => $item['pom_tni'],

                                'except_entry_crime_clearance' => $item['except_entry_crime_clearance'],
                                'except_entry_crime_clearance_percentage' => $item["except_entry_crime_clearance_percentage"],
                                'p21_except_entry' => $item['p21_except_entry'],
                                'sp3_except_entry' => $item['sp3_except_entry'],
                                'diversi_except_entry' => $item['diversi_except_entry'],
                                'sp2lid_except_entry' => $item['sp2lid_except_entry'],

                                'before_eval_crime_clearance' => $item['before_eval_crime_clearance'],
                                'before_eval_crime_clearance_percentage' => $item['before_eval_crime_clearance_percentage'],
                                'before_eval_p21' => $item['before_eval_p21'],
                                'before_eval_sp3' => $item['before_eval_sp3'],
                                'before_eval_diversi' => $item['before_eval_diversi'],
                                'before_eval_sp2lid' => $item['before_eval_sp2lid'],
                            ];
                        }
                    })
                ];
            });

            // Rekap Selra 2026 ( Bukan Lomba )
            $recap2026BeginDate = "2026-01-01";
            $recap2026LimitDateToday = date('Y-m-d');
            $recap2026LimitDate = ($recap2026LimitDateToday > "2026-12-31") ? "2026-12-31" : $recap2026LimitDateToday;
            $recap2026NewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
            $recap2026NewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';
            $recap2026ExceptCrimeClearanceStartTime = '2026-01-02 00:00:00';
            $recap2026ExceptCrimeClearanceEndTime = date('Y-m-d') . ' 00:00:00';

            $recap2026CaseResolutions = $this->recap2026CaseResolutions(
                $recap2026BeginDate,
                $recap2026LimitDate,
                $recap2026NewCrimeClearanceStartTime,
                $recap2026NewCrimeClearanceEndTime,
                $recap2026ExceptCrimeClearanceStartTime,
                $recap2026ExceptCrimeClearanceEndTime
            )
                ->where('xpolices.is_active', true)
                ->where('xpolices.class', 'RESOR')
                ->orderBy('xpolices.id')
                ->get();

            $recap2026CaseCollections = $totalService->getDataWithDateRange($recap2026BeginDate, $recap2026LimitDate);
            $recap2026CaseResolutionCollections = collect($recap2026CaseResolutions);

            $recap2026LeaderboardItems = $recap2026CaseCollections->map(function ($item) use ($recap2026CaseResolutionCollections) {
                $match = $recap2026CaseResolutionCollections->firstWhere('polres_id', $item['polres']);

                if ($match) {
                    $p21 = $match->p21 ?? 0;
                    $sp3 = $match->sp3 ?? 0;
                    $sp2lid = $match->sp2lid ?? 0;
                    $diversi = $match->diversi ?? 0;
                    $pomtni = $match->pomtni ?? 0;
                    $polda_name = $match->polda_name;
                    $polres_name = $match->polres_name;
                    $crime_cleareance_tabraklari = $match->crime_clearance_tabraklari ?? 0;
                    $new_entry_crime_clearance = $match->new_entry_crime_clearance ?? 0;
                    $p21_except_entry = $match->p21_except_entry ?? 0;
                    $sp3_except_entry = $match->sp3_except_entry ?? 0;
                    $diversi_except_entry = $match->diversi_except_entry ?? 0;
                    $sp2lid_except_entry = $match->sp2lid_except_entry ?? 0;
                    $except_entry_crime_clearance = $match->except_entry_crime_clearance ?? 0;

                    $jumlah_laka = $item['jumlah_laka'] - $pomtni;
                    $hit_and_run = $item['tabrak_lari'] - $crime_cleareance_tabraklari;
                    $total = $p21 + $sp3 + $sp2lid + $diversi;
                    $in_the_process = $jumlah_laka - $total - $hit_and_run;
                    $on_progress = $in_the_process + $hit_and_run;

                    $before_eval_crime_clearance = $total - $except_entry_crime_clearance;

                    return array_merge($item, [
                        'polda_name' => $polda_name,
                        'polres_name' => $polres_name,
                        'p21' => $p21 ?? 0,
                        'sp3' => $sp3 ?? 0,
                        'sp2lid' => $sp2lid ?? 0,
                        'diversi' => $diversi ?? 0,
                        'total' => $total,

                        'pom_tni' => $pomtni ?? 0,
                        'in_the_process' => $in_the_process,
                        'hit_and_run' => $hit_and_run,
                        'on_progress' => $on_progress,
                        'new_entry_crime_clearance' => $new_entry_crime_clearance,

                        'except_entry_crime_clearance' => $except_entry_crime_clearance,
                        'except_entry_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($except_entry_crime_clearance / $jumlah_laka) * 100) : 0,
                        'p21_except_entry' => $p21_except_entry,
                        'sp3_except_entry' => $sp3_except_entry,
                        'diversi_except_entry' => $diversi_except_entry,
                        'sp2lid_except_entry' => $sp2lid_except_entry,

                        'before_eval_crime_clearance' => $before_eval_crime_clearance,
                        'before_eval_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($before_eval_crime_clearance / $jumlah_laka) * 100) : 0,
                        'before_eval_p21' => $p21 - $p21_except_entry,
                        'before_eval_sp3' => $sp3 - $sp3_except_entry,
                        'before_eval_diversi' => $diversi - $diversi_except_entry,
                        'before_eval_sp2lid' => $sp2lid - $sp2lid_except_entry,

                        'percentage_p21' => ($jumlah_laka != 0) ? (($p21 / $jumlah_laka) * 100) : 0,
                        'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3 / $jumlah_laka) * 100) : 0,
                        'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid / $jumlah_laka) * 100) : 0,
                        'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi / $jumlah_laka) * 100) : 0,
                        'percentage_total' => ($jumlah_laka != 0) ? (($total / $jumlah_laka) * 100) : 0,
                        'percentage_on_progress' => ($jumlah_laka != 0) ? (($on_progress / $jumlah_laka) * 100) : 0,
                    ]);
                }

                return $item;
            });
            $recap2026LeaderboardItems = collect($recap2026LeaderboardItems)
                ->groupBy('polda')
                ->map(function ($items, $polda) {

                    $accidentTotal = $items->sum('jumlah_laka');
                    $p21Total = $items->sum('p21');
                    $sp3Total = $items->sum('sp3');
                    $sp2lidTotal = $items->sum('sp2lid');
                    $diversiTotal = $items->sum('diversi');
                    $totalTotal = $items->sum('total');

                    $inTheProcessTotal = $items->sum('in_the_process');
                    $hitAndRunTotal = $items->sum('hit_and_run');
                    $onProgressTotal = $items->sum('on_progress');

                    $newEntryCrimeClearanceTotal = $items->sum('new_entry_crime_clearance');
                    $pomTniTotal = $items->sum('pom_tni');

                    $accidentTotalPercentage = 0;
                    $p21TotalPercentage = ($accidentTotal != 0) ? (($p21Total / $accidentTotal) * 100) : 0;
                    $sp3TotalPercentage = ($accidentTotal != 0) ? (($sp3Total / $accidentTotal) * 100) : 0;
                    $sp2lidTotalPercentage = ($accidentTotal != 0) ? (($sp2lidTotal / $accidentTotal) * 100) : 0;
                    $diversiTotalPercentage = ($accidentTotal != 0) ? (($diversiTotal / $accidentTotal) * 100) : 0;
                    $totalTotalPercentage = ($accidentTotal != 0) ? (($totalTotal / $accidentTotal) * 100) : 0;
                    $onProgressTotalPercentage = ($accidentTotal != 0) ? (($onProgressTotal / $accidentTotal) * 100) : 0;

                    $exceptEntryCrimeClearanceTotal = $items->sum('except_entry_crime_clearance');
                    $exceptEntryCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($exceptEntryCrimeClearanceTotal / $accidentTotal) * 100) : 0;
                    $p21ExceptEntryTotal = $items->sum('p21_except_entry');
                    $sp3ExceptEntryTotal = $items->sum('sp3_except_entry');
                    $diversiExceptEntryTotal = $items->sum('diversi_except_entry');
                    $sp2lidExceptEntryTotal = $items->sum('sp2lid_except_entry');

                    $beforeEvalCrimeClearanceTotal = $items->sum('before_eval_crime_clearance');
                    $beforeEvalCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($beforeEvalCrimeClearanceTotal / $accidentTotal) * 100) : 0;
                    $beforeEvalP21Total = $items->sum('before_eval_p21');
                    $beforeEvalSp3Total = $items->sum('before_eval_sp3');
                    $beforeEvalDiversiTotal = $items->sum('before_eval_diversi');
                    $beforeEvalSp2lidTotal = $items->sum('before_eval_sp2lid');

                    return [
                        'polda' => $polda,
                        'polda_name' => $items->first()['polda_name'],
                        'accident_total' => $accidentTotal - $pomTniTotal,

                        'p21_total' => $p21Total,
                        'sp3_total' => $sp3Total,
                        'sp2lid_total' => $sp2lidTotal,
                        'diversi_total' => $diversiTotal,
                        'total_total' => $totalTotal,

                        'in_the_process_total' => $inTheProcessTotal,
                        'hit_and_run_total' => $hitAndRunTotal,
                        'on_progress_total' => $onProgressTotal,

                        'accident_total_percentage' => $accidentTotalPercentage,
                        'p21_total_percentage' => $p21TotalPercentage,
                        'sp3_total_percentage' => $sp3TotalPercentage,
                        'sp2lid_total_percentage' => $sp2lidTotalPercentage,
                        'diversi_total_percentage' => $diversiTotalPercentage,
                        'total_total_percentage' => $totalTotalPercentage,

                        'on_progress_total_percentage' => $onProgressTotalPercentage,
                        'new_entry_crime_clearance_total' => $newEntryCrimeClearanceTotal,
                        'pom_tni_total' => $pomTniTotal,

                        'except_entry_crime_clearance_total' => $exceptEntryCrimeClearanceTotal,
                        'except_entry_crime_clearance_total_percentage' => $exceptEntryCrimeClearanceTotalPercentage,
                        'p21_except_entry_total' => $p21ExceptEntryTotal,
                        'sp3_except_entry_total' => $sp3ExceptEntryTotal,
                        'diversi_except_entry_total' => $diversiExceptEntryTotal,
                        'sp2lid_except_entry_total' => $sp2lidExceptEntryTotal,

                        'before_eval_crime_clearance_total' => $beforeEvalCrimeClearanceTotal,
                        'before_eval_crime_clearance_total_percentage' => $beforeEvalCrimeClearanceTotalPercentage,
                        'before_eval_p21_total' => $beforeEvalP21Total,
                        'before_eval_sp3_total' => $beforeEvalSp3Total,
                        'before_eval_diversi_total' => $beforeEvalDiversiTotal,
                        'before_eval_sp2lid_total' => $beforeEvalSp2lidTotal,

                        'polres' => $items->map(function ($item) {
                            if (isset($item['polda_name'])) {
                                return [
                                    'polda' => $item['polda'],
                                    'polres' => $item['polres'],
                                    'name' => $item['name'],
                                    'jumlah_laka' => $item['jumlah_laka'] - $item['pom_tni'],
                                    'tabrak_lari' => $item['tabrak_lari'],
                                    'polda_name' => $item['polda_name'],
                                    'polres_name' => $item['polres_name'],

                                    'p21' => $item['p21'],
                                    'sp3' => $item['sp3'],
                                    'sp2lid' => $item['sp2lid'],
                                    'diversi' => $item['diversi'],
                                    'total' => $item['total'],

                                    'in_the_process' => $item['in_the_process'],
                                    'hit_and_run' => $item['hit_and_run'],
                                    'on_progress' => $item['on_progress'],

                                    'percentage_p21' => $item['percentage_p21'],
                                    'percentage_sp3' => $item['percentage_sp3'],
                                    'percentage_sp2lid' => $item['percentage_sp2lid'],
                                    'percentage_diversi' => $item['percentage_diversi'],
                                    'percentage_total' => $item['percentage_total'],
                                    'percentage_on_progress' => $item['percentage_on_progress'],

                                    'new_entry_crime_clearance' => $item['new_entry_crime_clearance'],
                                    'pom_tni' => $item['pom_tni'],

                                    'except_entry_crime_clearance' => $item['except_entry_crime_clearance'],
                                    'except_entry_crime_clearance_percentage' => $item["except_entry_crime_clearance_percentage"],
                                    'p21_except_entry' => $item['p21_except_entry'],
                                    'sp3_except_entry' => $item['sp3_except_entry'],
                                    'diversi_except_entry' => $item['diversi_except_entry'],
                                    'sp2lid_except_entry' => $item['sp2lid_except_entry'],

                                    'before_eval_crime_clearance' => $item['before_eval_crime_clearance'],
                                    'before_eval_crime_clearance_percentage' => $item['before_eval_crime_clearance_percentage'],
                                    'before_eval_p21' => $item['before_eval_p21'],
                                    'before_eval_sp3' => $item['before_eval_sp3'],
                                    'before_eval_diversi' => $item['before_eval_diversi'],
                                    'before_eval_sp2lid' => $item['before_eval_sp2lid'],
                                ];
                            }
                        })
                    ];
                });

        return view('home', compact(
            'user',
            'total_laka',
            'accident',
            'dpo',
            'dpb',
            'recapBeginDate',
            'recapLimitDate',
            'recapLeaderboardItems',
            'recap2025BeginDate',
            'recap2025LimitDate',
            'recap2025LeaderboardItems',
            'recap2026BeginDate',
            'recap2026LimitDate',
            'recap2026LeaderboardItems',
            'recapLombaBeginDate',
            'recapLombaLimitDate',
            'recapLombaLeaderboardItems',
        ));
    }

    public function profile()
    {
        // $user = Auth::getUser();
        $id = Auth::id();
        $year = Carbon::now()->year;
        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('id', $id)
            ->first();
        // dd($user->toArray());

        $userData = Officer::select(
            'officers.id',
            'officers.rank_short_name',
            'polres.name as polres_name',
            DB::raw('COALESCE(p21.jumlah_lidik, 0) AS total_p21'),
            DB::raw('COALESCE(sp3.jumlah_lidik, 0) AS total_sp3'),
            DB::raw('COALESCE(diversi.jumlah_lidik, 0) AS total_diversi'),
            DB::raw('COALESCE(pom_tni.jumlah_lidik, 0) AS total_pom_tni'),
            DB::raw('COALESCE(sp2lid.jumlah_lidik, 0) AS total_sp2lid')
        )
            ->leftJoin('polda', 'polda.id', '=', 'officers.polda_id')
            ->leftJoin('polres', 'polres.id', '=', 'officers.polres_id')
            ->leftJoin(
                DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0101'
                      AND date_part('year',accident_date) = $year
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS p21"),
                'officers.register_number',
                '=',
                'p21.lidik_id'
            )->leftJoin(
                DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0102'
                      AND date_part('year',accident_date) = $year
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS sp3"),
                'officers.register_number',
                '=',
                'sp3.lidik_id'
            )
            ->leftJoin(
                DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0103'
                      AND date_part('year',accident_date) = $year
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS diversi"),
                'officers.register_number',
                '=',
                'diversi.lidik_id'
            )
            ->leftJoin(
                DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0104'
                      AND date_part('year',accident_date) = $year
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS pom_tni"),
                'officers.register_number',
                '=',
                'pom_tni.lidik_id'
            )
            ->leftJoin(
                DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0108'
                      AND date_part('year',accident_date) = $year
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS sp2lid"),
                'officers.register_number',
                '=',
                'sp2lid.lidik_id'
            )
            ->where('officers.user_id', $id)
            ->first();

        // dd($userData);

        return view('profile', compact('user', 'userData'));
    }

    public function reset_password()
    {
        $user = Auth::getUser();

        $username = $user->username;

        return view('reset_password', compact('username'));
    }

    public function post_reset_password(Request $request)
    {
        $request->validate([
            'newPassword' => 'required|confirmed',
        ]);

        User::where('username', $request->username)->update(['password' => Hash::make($request->newPassword)]);
        return redirect('/login');
    }

    public function update_profile(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300, 300)->save(public_path('/image-profile/profile640/' . $filename));

            $user = Auth::user();
            $user->avatar = $filename;
            $user->save();
        }

        // return view('profile', array('user' => Auth::user()) );

        return redirect('profile')->with(array('user' => Auth::user()));
    }

    // $this->validate($request , [
    //     'avatar' =>  'required|image|mimes:jpg,jpeg,png'
    //     ]);dd($name);


    public function getChartBulan()
    {
        $range = 11;
        $outputs = collect();
        $polresId = Auth::user()->polres_id;
        $poldaId = Auth::user()->polda_id;
        $role = Auth::user()->role_id;

        // $laporan_belum_selesai = DB::table('accident')
        // ->join('polres', 'accident.polres_id', 'polres.id')
        // ->select('accident.report_id as report_id', 'accident.report_date as report_date', 'polres.name as polres_name')
        // ->where('accident.state', '0')
        // ->paginate(5)

        switch ($role) {
            case 2:
                for ($x = 0; $x <= $range; $x++) {
                    $current_month = Carbon::now()->subMonth($range - $x);
                    $date = $current_month->month;
                    $date_year = $current_month->year;

                    // $counts = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
                    $counts = DB::table('accidents')
                        ->join('polres', 'accidents.polres_id', 'polres.id')
                        ->join('polda', 'polres.polda_id', 'polda.id')
                        ->where('polda.id', '=', $poldaId)
                        ->where('polres.state', '=', 1)
                        ->where('polda.state', '=', 1)
                        ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                    $count = intval($counts);
                    // setlocale(LC_TIME, 'id');
                    $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
                    $output = collect(['date' => $date, 'count' => $count]);
                    $outputs->push($output);
                }
                break;
            case 3:
                for ($x = 0; $x <= $range; $x++) {
                    $current_month = Carbon::now()->subMonth($range - $x);
                    $date = $current_month->month;

                    // return($date);
                    $date_year = $current_month->year;

                    // $counts = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
                    $counts = DB::table('accidents')
                        ->join('polres', 'accidents.polres_id', 'polres.id')
                        ->where('polres.id', '=', $polresId)
                        ->where('polres.state', '=', 1)
                        ->whereMonth('accidents.created_at', '=', $date)
                        ->whereYear('accidents.created_at', '=', $date_year)->get(['accidents.id'])->count();
                    $count = intval($counts);
                    // setlocale(LC_TIME, 'id');
                    $date = $current_month->translatedFormat('F') . " " . $current_month->year;
                    $output = collect(['date' => $date, 'count' => $count]);
                    $outputs->push($output);
                    // return($outputs);
                }
                break;
            default:
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
                    $current_month = Carbon::now()->subMonth($range - $x);
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
        }
        return response()->json($outputs);
    }

    public function getPieBulan()
    {
        $range = 11;
        $outputs = collect();
        $polresId = Auth::user()->polres_id;
        $poldaId = Auth::user()->polda_id;
        $role = Auth::user()->role_id;
        $jumlah_laka = 0;


        // for($x=0;$x<=$range;$x++){
        //     $current_month = Carbon::now()->subMonth($range - $x);
        //     $date = $current_month->month;
        //     $date_year = $current_month->year;
        //     $count = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
        //     $jumlah_laka+=$count;

        // }

        // for($x=0;$x<=$range;$x++){
        //     $current_month = Carbon::now()->subMonth($range - $x);
        //     $date = $current_month->month;
        //     $date_year = $current_month->year;
        //     $count = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
        //     $percentage = ($count / $jumlah_laka) * 100 ;
        //     // setlocale(LC_TIME, 'id');
        //     $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
        //     $output = collect(['date' => $date, 'percentage' => $percentage, 'jumlah_laka'=>$jumlah_laka]);
        //     $outputs->push($output);
        // }


        switch ($role) {
            case 2:
                $current_month = Carbon::now()->subMonth(0);
                $current_year = Carbon::now()->subYear();
                $month = $current_month->formatLocalized('%B');
                $date_month = $month;
                $date_year = $current_month->year;
                $jumlah_laka = DB::table('accidents')
                    ->join('polres', 'accidents.polres_id', 'polres.id')
                    ->join('polda', 'polres.polda_id', 'polda.id')
                    ->where('polda.id', '=', $poldaId)
                    ->where('polres.state', '=', 1)
                    ->where('polda.state', '=', 1)
                    ->whereNotIn('accidents.selra_flag', ['S0107'])
                    ->whereYear('accidents.created_at', '=', $date_year)
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
                                polda.id = \'' . $poldaId . '\'
                                and polres.state = 1
                                and polda.state = 1
                                and date_part(\'year\',  accidents.created_at) = \'' . $date_year . '\'
                                and ref.grp_id = \'S01\'
                                group by REF.ID,ref.name,ref.sort
                                order by ref.sort
                            ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                            where
                            ref.grp_id = \'S01\'
                            and ref.id NOT IN (\'S0107\', \'S0106\')
                        '
                );
                $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'jumlah_laka' => $jumlah_laka, 'jumlah_selra' => $jumlah_selra]);
                $outputs->push($output);
                //  for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')
                //             ->join('polres', 'accidents.polres_id', 'polres.id')
                //             ->join('polda', 'polres.polda_id', 'polda.id')
                //             ->where('polda.id','=',$poldaId)
                //             ->where('polres.state','=',1)
                //             ->where('polda.state','=',1)
                //             ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                //     $percentage = ($count / $jumlah_laka) * 100 ;
                //     // setlocale(LC_TIME, 'id');
                //     $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
                //     $output = collect(['date' => $date, 'percentage' => $percentage, 'jumlah_laka'=>$jumlah_laka]);
                //     $outputs->push($output);
                //  }
                // for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')
                //             ->join('polres', 'accidents.polres_id', 'polres.id')
                //             ->join('polda', 'polres.polda_id', 'polda.id')
                //             ->where('polda.id','=',$poldaId)
                //             ->where('polres.state','=',1)
                //             ->where('polda.state','=',1)
                //             ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                //     $jumlah_laka+=$count;

                // }

                // for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')
                //             ->join('polres', 'accidents.polres_id', 'polres.id')
                //             ->join('polda', 'polres.polda_id', 'polda.id')
                //             ->where('polda.id','=',$poldaId)
                //             ->where('polres.state','=',1)
                //             ->where('polda.state','=',1)
                //             ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                //     $percentage = ($count / $jumlah_laka) * 100 ;
                //     // setlocale(LC_TIME, 'id');
                //     $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
                //     $output = collect(['date' => $date, 'percentage' => $percentage, 'jumlah_laka'=>$jumlah_laka]);
                //     $outputs->push($output);
                // }
                break;
            case 3:
                $current_month = Carbon::now()->subMonth(0);
                $current_year = Carbon::now()->subYear();
                $month = $current_month->formatLocalized('%B');
                $date_month = $month;
                $date_year = $current_month->year;
                $jumlah_laka = DB::table('accidents')
                    ->join('polres', 'accidents.polres_id', 'polres.id')
                    ->join('polda', 'polres.polda_id', 'polda.id')
                    ->where('polres.id', '=', $polresId)
                    ->where('polres.state', '=', 1)
                    ->where('polda.state', '=', 1)
                    ->whereNotIn('accidents.selra_flag', ['S0107'])
                    ->whereYear('accidents.created_at', '=', $date_year)
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
                            polres.id = \'' . $polresId . '\'
                            and polres.state = 1
                            and polda.state = 1
                            and date_part(\'year\',  accidents.created_at) = \'' . $date_year . '\'
                            and ref.grp_id = \'S01\'
                            group by REF.ID,ref.name,ref.sort
                            order by ref.sort
                        ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                        where
                        ref.grp_id = \'S01\'
                        and ref.id NOT IN (\'S0107\', \'S0106\')
                    '
                );
                $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'jumlah_laka' => $jumlah_laka, 'jumlah_selra' => $jumlah_selra]);
                $outputs->push($output);
                // for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')
                //             ->join('polres', 'accidents.polres_id', 'polres.id')
                //             ->where('polres.id','=',$polresId)
                //             ->where('polres.state','=',1)
                //             ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                //     $jumlah_laka+=$count;

                // }

                // for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')
                //             ->join('polres', 'accidents.polres_id', 'polres.id')
                //             ->where('polres.id','=',$polresId)
                //             ->where('polres.state','=',1)
                //             ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                //     $percentage = ($count / $jumlah_laka) * 100 ;
                //     // setlocale(LC_TIME, 'id');
                //     $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
                //     $output = collect(['date' => $date, 'percentage' => $percentage, 'jumlah_laka'=>$jumlah_laka]);
                //     $outputs->push($output);
                // }
                break;
            default:
                $current_month = Carbon::now()->subMonth(0);
                $current_year = Carbon::now()->subYear();
                $month = $current_month->formatLocalized('%B');
                $date_month = $month;
                $date_year = $current_month->year;
                $jumlah_laka = DB::table('accidents')
                    ->join('polres', 'accidents.polres_id', 'polres.id')
                    ->join('polda', 'polres.polda_id', 'polda.id')
                    ->where('polres.state', '=', 1)
                    ->where('polda.state', '=', 1)
                    ->whereNotIn('accidents.selra_flag', ['S0107'])
                    ->whereYear('accidents.created_at', '=', $date_year)
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
                        and ref.id NOT IN (\'S0107\', \'S0106\')
                    '
                );
                $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'jumlah_laka' => $jumlah_laka, 'jumlah_selra' => $jumlah_selra]);
                $outputs->push($output);
                // for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
                //     $jumlah_laka+=$count;

                // }

                // for($x=0;$x<=$range;$x++){
                //     $current_month = Carbon::now()->subMonth($range - $x);
                //     $date = $current_month->month;
                //     $date_year = $current_month->year;
                //     $count = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
                //     $percentage = ($count / $jumlah_laka) * 100 ;
                //     // setlocale(LC_TIME, 'id');
                //     $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
                //     $output = collect(['date' => $date, 'percentage' => $percentage, 'jumlah_laka'=>$jumlah_laka]);
                //     $outputs->push($output);
                // }
                break;
        }
        return response()->json($outputs);
    }
}
