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


    public function index()
    {
        $user = Auth::user();
        switch ($user->role_id) {
            case 2:
                $polda = $user->polda_id;
                $polres = '-';
                $get_accident = DB::select('select coalesce(count(*),0) as total_laka
                from accidents left join polres on polres.id = accidents.polres_id
                left join polda on polda.id = polres.polda_id
                where polda.id =\''.$polda.'\'
                ');
                $get_dpo = DB::select('select coalesce(count(*),0) as total_dpo
                from dpo left join accidents on accidents.id = dpo.accident_id
                left join polres on polres.id = accidents.polres_id
                left join polda on polda.id = polres.polda_id where polda.id =\''.$polda.'\' and dpo.state = \'0\' ');
                $get_dpb = DB::select('select coalesce(count(*),0) as total_dpb
                from dpb left join accidents on accidents.id = dpb.accident_id
                left join polres on polres.id = accidents.polres_id
                left join polda on polda.id = polres.polda_id where polda.id =\''.$polda.'\' ');
                break;
            case 3:
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;
                    $get_accident = DB::select('select coalesce(count(*),0) as total_laka
                    from accidents left join polres on polres.id = accidents.polres_id
                    where polres.id =\''.$polres.'\'
                    ');
                    $get_dpo = DB::select('select coalesce(count(*),0) as total_dpo
                    from dpo left join accidents on accidents.id = dpo.accident_id
                    left join polres on polres.id = accidents.polres_id
                    where polres.id =\''.$polres.'\'  ');
                    $get_dpb = DB::select('select coalesce(count(*),0) as total_dpb
                    from dpb left join accidents on accidents.id = dpb.accident_id
                    left join polres on polres.id = accidents.polres_id
                    where polres.id =\''.$polres.'\'  ');
                break;
                case 4:
                    $officer = $user->officer_id;
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;
                    // $get_accident = DB::select('select coalesce(count(*),0) as total_laka
                    // from accidents
                    // left join polres on polres.id = accidents.polres_id
                    // left join surat_penyidikan on surat_penyidikan.accident_id = accidents.id
                    // left join officers on officers.id = surat_penyidikan.officer_id
                    // where polres.id =\''.$polres.'\' and surat_penyidikan.officer_id = \''.$officer.'\'
                    // ');
                    $get_accident = DB::select('select coalesce(count(*),0) as total_laka
                    from accidents
                    left join polres on polres.id = accidents.polres_id
                    where polres.id =\''.$polres.'\'
                    ');
                    $get_dpo = DB::select('select coalesce(count(*),0) as total_dpo
                    from dpo left join accidents on accidents.id = dpo.accident_id
                    left join polres on polres.id = accidents.polres_id
                    where polres.id =\''.$polres.'\'  ');
                    $get_dpb = DB::select('select coalesce(count(*),0) as total_dpb
                    from dpb left join accidents on accidents.id = dpb.accident_id
                    left join polres on polres.id = accidents.polres_id
                    where polres.id =\''.$polres.'\'  ');
                break;
            default:
            $polda = '-';
            $polres = '-';
            $get_accident = DB::select('select coalesce(count(*),0) as total_laka from accidents');
            $get_dpo = DB::select('select coalesce(count(*),0) as total_dpo from dpo left join accidents on accidents.id = dpo.accident_id left join polres on polres.id = accidents.polres_id where dpo.state = \'0\' and polres.state = \'1\' ');
            $get_dpb = DB::select('select coalesce(count(*),0) as total_dpb from dpb left join accidents on accidents.id = dpb.accident_id left join polres on polres.id = accidents.polres_id where polres.state = \'1\' ');
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://irsms.korlantas.polri.go.id/irsmsapi/api/get_total_accident?user=".$user->role_id."&polda=".$polda."&polres=".$polres,
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
           $get_data=json_decode($response);
           $total_laka = ($get_data) ? $get_data->result[0]->total : 'Null';
           //  $data = $response->json();
        }


      $accident = $get_accident[0]->total_laka;
      $dpo = $get_dpo[0]->total_dpo;
      $dpb = $get_dpb[0]->total_dpb;

      $user=Auth::getUser();

        $beginDate = "2024-01-01";
        $limitDate = '2024-09-30';
       
        $caseResolutions = DB::table('lib.polices as xpolices')->select('xpolices.id as polres_id', 'xpolices.name as polres_name', 'xpolices.category as polres_category', 'ypolices.id as polda_id', 'ypolices.name as polda_name')
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0101' AND accidents.accident_date BETWEEN '$beginDate' AND '$limitDate' THEN 1 ELSE 0 END) as p21")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0102' AND accidents.accident_date BETWEEN '$beginDate' AND '$limitDate' THEN 1 ELSE 0 END) as sp3")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0103' AND accidents.accident_date BETWEEN '$beginDate' AND '$limitDate' THEN 1 ELSE 0 END) as diversi")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0108' AND accidents.accident_date BETWEEN '$beginDate' AND '$limitDate' THEN 1 ELSE 0 END) as sp2lid")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0104' AND accidents.accident_date BETWEEN '$beginDate' AND '$limitDate' THEN 1 ELSE 0 END) as pomtni")
            ->leftJoin('accidents', 'xpolices.id', '=', 'accidents.police_id')
            ->join('lib.polices as ypolices', 'xpolices.parent_id', '=', 'ypolices.id')
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->groupBy('xpolices.id', 'xpolices.name', 'ypolices.id', 'ypolices.name')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        try {
            $responseCases = Http::withHeaders([
                'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
                'Content-Type' => 'application/json'
            ])
            ->withQueryParameters([
                'start_date' => $beginDate,
                'end_date' => $limitDate
            ])
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka')
                ->json();
            $cases = $responseCases['result'];
        } catch (\Throwable $th) {
            $cases = [];
        }
        
        $caseCollections = collect($cases);
        $caseResolutionCollections = collect($caseResolutions);

        /* // Rekapitulasi Data Selra Lomba
        $leaderboardItems = $caseCollections->map(function ($item) use ($caseResolutionCollections) {
            $match = $caseResolutionCollections->firstWhere('polres_id', $item['polres']);
            
            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;
                $pomtni = $match->pomtni ?? 0;
                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $jumlah_laka = $item['jumlah_laka'];
                $hit_and_run = $item['tabrak_lari'];
                $total = $p21 + $sp3 + $sp2lid + $diversi;
                $in_the_process = $jumlah_laka - $total - $pomtni - $hit_and_run;
                $on_progress = $in_the_process + $hit_and_run;
                
                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total' => $total,
                    'in_the_process' => $in_the_process,
                    'hit_and_run' => $hit_and_run,
                    'on_progress' => $on_progress,
                    'percentage_p21' => ($jumlah_laka != 0) ? (($p21/$jumlah_laka) * 100) : 0,
                    'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3/$jumlah_laka) * 100) : 0,
                    'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid/$jumlah_laka) * 100) : 0,
                    'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi/$jumlah_laka) * 100) : 0,
                    'percentage_total' => ($jumlah_laka != 0) ? (($total/$jumlah_laka) * 100) : 0,
                    'percentage_on_progress' => ($jumlah_laka != 0) ? (($on_progress/$jumlah_laka) * 100) : 0,
                ]);
            }
            
            return $item;
        });
        $leaderboardItems = collect($leaderboardItems)
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

                $accidentTotalPercentage = 0;
                $p21TotalPercentage = ($accidentTotal != 0) ? (($p21Total/$accidentTotal) * 100) : 0;
                $sp3TotalPercentage = ($accidentTotal != 0) ? (($sp3Total/$accidentTotal) * 100) : 0;
                $sp2lidTotalPercentage = ($accidentTotal != 0) ? (($sp2lidTotal/$accidentTotal) * 100) : 0;
                $diversiTotalPercentage = ($accidentTotal != 0) ? (($diversiTotal/$accidentTotal) * 100) : 0;
                $totalTotalPercentage = ($accidentTotal != 0) ? (($totalTotal/$accidentTotal) * 100) : 0;
                $onProgressTotalPercentage = ($accidentTotal != 0) ? (($onProgressTotal/$accidentTotal) * 100) : 0;

                return [
                    'polda' => $polda,
                    'polda_name' => $items->first()['polda_name'],
                    'accident_total' => $accidentTotal,
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
                    'polres' => $items->map(function ($item) {
                        if(isset($item['polda_name'])){
                            return [
                                'polda' => $item['polda'],
                                'polres' => $item['polres'],
                                'name' => $item['name'],
                                'jumlah_laka' => $item['jumlah_laka'],
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
                            ];
                        }
                    })
                ];
            });

        // Mengelompokkan dan mengakumulasi jumlah kasus dari caseCollections berdasarkan polda_id
        $groupedCaseCollections = $caseCollections->groupBy('polda')->map(function ($cases) {
            return [
                'polda' => $cases->first()['polda'],
                'jumlah_laka' => $cases->sum('jumlah_laka'),
                'tabrak_lari' => $cases->sum('tabrak_lari')
            ];
        });

        // Mengelompokkan dan mengakumulasi jumlah resolusi kasus dari caseResolutionCollections berdasarkan polda_id
        $groupedCaseResolutionCollections = $caseResolutionCollections->groupBy('polda_id')->map(function ($resolutions) {
            return [
                'polda_id' => $resolutions->first()->polda_id,
                'polda_name' => $resolutions->first()->polda_name,
                'p21' => $resolutions->sum('p21'),
                'sp3' => $resolutions->sum('sp3'),
                'diversi' => $resolutions->sum('diversi'),
                'sp2lid' => $resolutions->sum('sp2lid'),
                'pomtni' => $resolutions->sum('pomtni')
            ];
        });

        // Menggabungkan kedua koleksi yang telah digroup berdasarkan polda_id
        $groupedCaseFinalResult = $groupedCaseCollections->map(function ($cases) use ($groupedCaseResolutionCollections) {
            $poldaId = $cases['polda'];
            $resolutions = $groupedCaseResolutionCollections->firstWhere('polda_id', $poldaId) ?? [];

            return array_merge($cases, $resolutions);
        });*/

        // Output $groupedCaseFinalResult sebagai hasil akhir
        // dd($groupedCaseFinalResult->values()->toArray());

        // $cat1 = 1000;
        // $cat2 = [1000, 5000];
        // $cat3 = 5000;

        // Dibawah 500 LP
        /*$leaderboardCategoryUnderFiveHundred = $caseCollections->where('jumlah_laka', '<=', 500);
        $underFiveHundredCaseResolutionCollections = $caseResolutionCollections;
        $leaderboardCategoryUnderFiveHundredItems = $leaderboardCategoryUnderFiveHundred->map(function ($item) use ($underFiveHundredCaseResolutionCollections) {
            $match = $underFiveHundredCaseResolutionCollections->firstWhere('polres_id', $item['polres']);
            
            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;

                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $totalValue = ($p21*6) + ($sp3*2) + ($diversi*2) + ($sp2lid*1);
                $maxWeight = $item['jumlah_laka'] * 5;
                $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total_value' => $totalValue,
                    'max_weight' => $maxWeight,
                    'weight_percentage' => $weightPercentage,
                ]);
            }

            // return $item;
        });*/
        /*$leaderboardCategoryUnderFiveHundred = $groupedCaseFinalResult->where('jumlah_laka', '<=', $cat1);
        $leaderboardCategoryUnderFiveHundredItems = $leaderboardCategoryUnderFiveHundred->map(function ($item) {
            $p21 = $item['p21'] ?? 0;
            $sp3 = $item['sp3'] ?? 0;
            $sp2lid = $item['sp2lid'] ?? 0;
            $diversi = $item['diversi'] ?? 0;

            $polda_name = $item['polda_name'];
            $polda = $item['polda'];

            $totalValue = ($p21*6) + ($sp3*2) + ($diversi*2) + ($sp2lid*1);
            $maxWeight = $item['jumlah_laka'] * 6;
            $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

            return array_merge($item, [
                'polda' => $polda,
                'polda_name' => $polda_name,
                'p21' => $p21 ?? 0,
                'sp3' => $sp3 ?? 0,
                'sp2lid' => $sp2lid ?? 0,
                'diversi' => $diversi ?? 0,
                'total_value' => $totalValue,
                'max_weight' => $maxWeight,
                'weight_percentage' => $weightPercentage,
            ]);
        });*/
        
        // 500+ LP
        /*$leaderboardCategoryUpperFiveHundred = $caseCollections->whereBetween('jumlah_laka', [501, 1000]);
        $upperFiveHundredCaseResolutionCollections = $caseResolutionCollections;
        $leaderboardCategoryUpperFiveHundredItems = $leaderboardCategoryUpperFiveHundred->map(function ($item) use ($upperFiveHundredCaseResolutionCollections) {
            $match = $upperFiveHundredCaseResolutionCollections->firstWhere('polres_id', $item['polres']);
            
            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;

                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $totalValue = ($p21*6) + ($sp3*2) + ($diversi*2) + ($sp2lid*1);
                $maxWeight = $item['jumlah_laka'] * 6;
                $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total_value' => $totalValue,
                    'max_weight' => $maxWeight,
                    'weight_percentage' => $weightPercentage,
                ]);
            }

            // return $item;
        });*/
        /*$leaderboardCategoryUpperFiveHundred = $groupedCaseFinalResult->whereBetween('jumlah_laka', [$cat2[0], $cat2[1]]);
        $leaderboardCategoryUpperFiveHundredItems = $leaderboardCategoryUpperFiveHundred->map(function ($item) {
            $p21 = $item['p21'] ?? 0;
            $sp3 = $item['sp3'] ?? 0;
            $sp2lid = $item['sp2lid'] ?? 0;
            $diversi = $item['diversi'] ?? 0;

            $polda_name = $item['polda_name'];
            $polda = $item['polda'];

            $totalValue = ($p21*6) + ($sp3*2) + ($diversi*2) + ($sp2lid*1);
            $maxWeight = $item['jumlah_laka'] * 6;
            $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

            return array_merge($item, [
                'polda' => $polda,
                'polda_name' => $polda_name,
                'p21' => $p21 ?? 0,
                'sp3' => $sp3 ?? 0,
                'sp2lid' => $sp2lid ?? 0,
                'diversi' => $diversi ?? 0,
                'total_value' => $totalValue,
                'max_weight' => $maxWeight,
                'weight_percentage' => $weightPercentage,
            ]);
        });*/
       
        // 1000+ LP
        /*$leaderboardCategoryUpperOneThousand = $caseCollections->where('jumlah_laka', '>', 1000);
        $upperOneThousandCaseResolutionCollections = $caseResolutionCollections;
        $leaderboardCategoryUpperOneThousandItems = $leaderboardCategoryUpperOneThousand->map(function ($item) use ($upperOneThousandCaseResolutionCollections) {
            $match = $upperOneThousandCaseResolutionCollections->firstWhere('polres_id', $item['polres']);
            
            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;

                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $totalValue = ($p21*6) + ($sp3*2) + ($diversi*2) + ($sp2lid*1);
                $maxWeight = $item['jumlah_laka'] * 6;
                $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total_value' => $totalValue,
                    'max_weight' => $maxWeight,
                    'weight_percentage' => $weightPercentage,
                ]);
            }

            // return $item;
        });*/
        /*$leaderboardCategoryUpperOneThousand = $groupedCaseFinalResult->where('jumlah_laka', '>', $cat3);
        $leaderboardCategoryUpperOneThousandItems = $leaderboardCategoryUpperOneThousand->map(function ($item) {
  
            $p21 = $item['p21'] ?? 0;
            $sp3 = $item['sp3'] ?? 0;
            $sp2lid = $item['sp2lid'] ?? 0;
            $diversi = $item['diversi'] ?? 0;

            $polda_name = $item['polda_name'];
            $polda = $item['polda'];

            $totalValue = ($p21*6) + ($sp3*2) + ($diversi*2) + ($sp2lid*1);
            $maxWeight = $item['jumlah_laka'] * 6;
            $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

            return array_merge($item, [
                'polda' => $polda,
                'polda_name' => $polda_name,
                'p21' => $p21 ?? 0,
                'sp3' => $sp3 ?? 0,
                'sp2lid' => $sp2lid ?? 0,
                'diversi' => $diversi ?? 0,
                'total_value' => $totalValue,
                'max_weight' => $maxWeight,
                'weight_percentage' => $weightPercentage,
            ]);
        });*/

        // TA, TABES, TRO
        // $leaderboardCategoryBigCity = $caseCollections;
        // $bigCityCaseResolutionCollections = $caseResolutionCollections->whereIn('polres_category', ['POLRESTA', 'POLRESTABES', 'POLRESTRO']);
        // $leaderboardCategoryBigCityItems = $leaderboardCategoryBigCity->map(function ($item) use ($bigCityCaseResolutionCollections) {
        //     $match = $bigCityCaseResolutionCollections->firstWhere('polres_id', $item['polres']);
            
        //     if ($match) {
        //         $p21 = $match->p21 ?? 0;
        //         $sp3 = $match->sp3 ?? 0;
        //         $sp2lid = $match->sp2lid ?? 0;
        //         $diversi = $match->diversi ?? 0;

        //         $polda_name = $match->polda_name;
        //         $polres_name = $match->polres_name;

        //         $totalValue = ($p21*5) + ($sp3*3) + ($diversi*2) + ($sp2lid*1);
        //         $maxWeight = $item['jumlah_laka'] * 5;
        //         $weightPercentage = ($maxWeight != 0) ? ($totalValue / $maxWeight) * 100 : 0;

        //         return array_merge($item, [
        //             'polda_name' => $polda_name,
        //             'polres_name' => $polres_name,
        //             'p21' => $p21 ?? 0,
        //             'sp3' => $sp3 ?? 0,
        //             'sp2lid' => $sp2lid ?? 0,
        //             'diversi' => $diversi ?? 0,
        //             'total_value' => $totalValue,
        //             'max_weight' => $maxWeight,
        //             'weight_percentage' => $weightPercentage,
        //         ]);
        //     }

        //     // return $item;
        // });

         // Selra Rekap 2024 (Lomba)
         $recapLombaBeginDate = "2024-01-01";
         $recapLombaLimitDate = "2024-12-31";
         $recapLombaNewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
         $recapLombaNewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';
 
         $recapLombaCaseResolutions = DB::table('lib.polices as xpolices')->select('xpolices.id as polres_id', 'xpolices.name as polres_name', 'xpolices.category as polres_category', 'ypolices.id as polda_id', 'ypolices.name as polda_name')
             ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0101' AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' THEN 1 ELSE 0 END) as p21")
             ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0102' AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' THEN 1 ELSE 0 END) as sp3")
             ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0103' AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' THEN 1 ELSE 0 END) as diversi")
             ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0108' AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' THEN 1 ELSE 0 END) as sp2lid")
             ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0104' AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' THEN 1 ELSE 0 END) as pomtni")
             ->selectRaw("SUM(CASE WHEN accidents.selra_flag IN ('S0101', 'S0102', 'S0103', 'S0104', 'S0108') AND accidents.special_info = 'TABRAK_LARI' AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' THEN 1 ELSE 0 END) as crime_clearance_tabraklari")
             ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accidents.accident_date BETWEEN '$recapLombaBeginDate' AND '$recapLombaLimitDate' AND accident_resolutions.created_at BETWEEN '$recapLombaNewCrimeClearanceStartTime' AND '$recapLombaNewCrimeClearanceEndTime' THEN 1 ELSE 0 END) as new_entry_crime_clearance")
             ->leftJoin('accidents', 'xpolices.id', '=', 'accidents.police_id')
             ->leftJoin('accident_resolutions', 'accidents.id', '=', 'accident_resolutions.accident_id')
             ->join('lib.polices as ypolices', 'xpolices.parent_id', '=', 'ypolices.id')
             ->where('xpolices.is_active', true)
             ->where('xpolices.class', 'RESOR')
             ->groupBy('xpolices.id', 'xpolices.name', 'ypolices.id', 'ypolices.name')
             ->orderBy('xpolices.id', 'asc')
             ->get();
 
         try {
             $recapLombaResponseCases = Http::withHeaders([
                 'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
                 'Content-Type' => 'application/json'
             ])
             ->withQueryParameters([
                 'start_date' => $recapLombaBeginDate,
                 'end_date' => $recapLombaLimitDate
             ])
                 ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka')
                 ->json();
             $recapLombaCases = $recapLombaResponseCases['result'];
         } catch (\Throwable $th) {
             $recapLombaCases = [];
         }
         
         $recapLombaCaseCollections = collect($recapLombaCases);
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
                if(isset($items->first()['polda_name'])){
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
                        // 'polres' => $items->map(function ($item) {
                            
                        //     if(isset($item['polda_name'])){
                        //         $p21TotalWeight = $item['p21'] * 6;
                        //         $sp3TotalWeight = $item['sp3'] * 2;
                        //         $diversiTotalWeight = $item['diversi'] * 2;
                        //         $sp2lidTotalWeight = $item['sp2lid'] * 1;
                        //         $totalWeight = $p21TotalWeight + $sp3TotalWeight + $diversiTotalWeight + $sp2lidTotalWeight;
                        //         $maxWeight = $item['jumlah_laka'] * 6;
                        //         $weightPercentage = ($maxWeight != 0) ? ($totalWeight / $maxWeight) * 100 : 0;
                        //         return [
                        //             'polda' => $item['polda'],
                        //             'polres' => $item['polres'],
                        //             'name' => $item['name'],
                        //             'jumlah_laka' => $item['jumlah_laka'],
                        //             'polda_name' => $item['polda_name'],
                        //             'polres_name' => $item['polres_name'],
                        //             'p21' => $item['p21'],
                        //             'p21_weight' => $p21TotalWeight,
                        //             'sp3' => $item['sp3'],
                        //             'sp3_weight' => $sp3TotalWeight,
                        //             'diversi' => $item['diversi'],
                        //             'diversi_weight' => $diversiTotalWeight,
                        //             'sp2lid' => $item['sp2lid'],
                        //             'sp2lid_weight' => $sp2lidTotalWeight,
                        //             'total' => $item['total'],
                        //             'total_weight' => $totalWeight,
                        //             'max_weight' => $maxWeight,
                        //             'weight_percentage' => $weightPercentage
                        //         ];
                        //     }
                        // })
                    ];
                }
             });
        // dd($recapLombaLeaderboardItems);

        // Selra Rekap 2024 (Bukan Lomba)
        $recapBeginDate = "2024-01-01";
        $recapLimitDate = "2024-12-31";
        $recapNewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
        $recapNewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';
        $recapExceptCrimeClearanceStartTime = '2025-01-02 00:00:00';
        $recapExceptCrimeClearanceEndTime = date('Y-m-d') . ' 00:00:00';

        $recapCaseResolutions = DB::table('lib.polices as xpolices')->select('xpolices.id as polres_id', 'xpolices.name as polres_name', 'xpolices.category as polres_category', 'ypolices.id as polda_id', 'ypolices.name as polda_name')
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0101' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' THEN 1 ELSE 0 END) as p21")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0102' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' THEN 1 ELSE 0 END) as sp3")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0103' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' THEN 1 ELSE 0 END) as diversi")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0108' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' THEN 1 ELSE 0 END) as sp2lid")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0104' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' THEN 1 ELSE 0 END) as pomtni")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag IN ('S0101', 'S0102', 'S0103', 'S0104', 'S0108') AND accidents.special_info = 'TABRAK_LARI' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' THEN 1 ELSE 0 END) as crime_clearance_tabraklari")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' AND accident_resolutions.created_at BETWEEN '$recapNewCrimeClearanceStartTime' AND '$recapNewCrimeClearanceEndTime' THEN 1 ELSE 0 END) as new_entry_crime_clearance")
            
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0101' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' AND accident_resolutions.created_at BETWEEN '$recapExceptCrimeClearanceStartTime' AND '$recapExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as p21_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0102' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' AND accident_resolutions.created_at BETWEEN '$recapExceptCrimeClearanceStartTime' AND '$recapExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as sp3_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0103' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' AND accident_resolutions.created_at BETWEEN '$recapExceptCrimeClearanceStartTime' AND '$recapExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as diversi_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0108' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' AND accident_resolutions.created_at BETWEEN '$recapExceptCrimeClearanceStartTime' AND '$recapExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as sp2lid_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accidents.selra_flag <> 'S0104' AND accidents.accident_date BETWEEN '$recapBeginDate' AND '$recapLimitDate' AND accident_resolutions.created_at BETWEEN '$recapExceptCrimeClearanceStartTime' AND '$recapExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as except_entry_crime_clearance")
            ->leftJoin('accidents', 'xpolices.id', '=', 'accidents.police_id')
            ->leftJoin('accident_resolutions', 'accidents.id', '=', 'accident_resolutions.accident_id')
            ->join('lib.polices as ypolices', 'xpolices.parent_id', '=', 'ypolices.id')
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->groupBy('xpolices.id', 'xpolices.name', 'ypolices.id', 'ypolices.name')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        try {
            $recapResponseCases = Http::withHeaders([
                'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
                'Content-Type' => 'application/json'
            ])
            ->withQueryParameters([
                'start_date' => $recapBeginDate,
                'end_date' => $recapLimitDate
            ])
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka')
                ->json();
            $recapCases = $recapResponseCases['result'];
        } catch (\Throwable $th) {
            $recapCases = [];
        }
        
        $recapCaseCollections = collect($recapCases);
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
                    'except_entry_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($except_entry_crime_clearance/$jumlah_laka) * 100) : 0,
                    'p21_except_entry' => $p21_except_entry,
                    'sp3_except_entry' => $sp3_except_entry,
                    'diversi_except_entry' => $diversi_except_entry,
                    'sp2lid_except_entry' => $sp2lid_except_entry,

                    'before_eval_crime_clearance' => $before_eval_crime_clearance,
                    'before_eval_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($before_eval_crime_clearance/$jumlah_laka) * 100) : 0,
                    'before_eval_p21' => $p21 - $p21_except_entry,
                    'before_eval_sp3' => $sp3 - $sp3_except_entry,
                    'before_eval_diversi' => $diversi - $diversi_except_entry,
                    'before_eval_sp2lid' => $sp2lid - $sp2lid_except_entry,

                    'percentage_p21' => ($jumlah_laka != 0) ? (($p21/$jumlah_laka) * 100) : 0,
                    'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3/$jumlah_laka) * 100) : 0,
                    'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid/$jumlah_laka) * 100) : 0,
                    'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi/$jumlah_laka) * 100) : 0,
                    'percentage_total' => ($jumlah_laka != 0) ? (($total/$jumlah_laka) * 100) : 0,
                    'percentage_on_progress' => ($jumlah_laka != 0) ? (($on_progress/$jumlah_laka) * 100) : 0,
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
                $p21TotalPercentage = ($accidentTotal != 0) ? (($p21Total/$accidentTotal) * 100) : 0;
                $sp3TotalPercentage = ($accidentTotal != 0) ? (($sp3Total/$accidentTotal) * 100) : 0;
                $sp2lidTotalPercentage = ($accidentTotal != 0) ? (($sp2lidTotal/$accidentTotal) * 100) : 0;
                $diversiTotalPercentage = ($accidentTotal != 0) ? (($diversiTotal/$accidentTotal) * 100) : 0;
                $totalTotalPercentage = ($accidentTotal != 0) ? (($totalTotal/$accidentTotal) * 100) : 0;
                $onProgressTotalPercentage = ($accidentTotal != 0) ? (($onProgressTotal/$accidentTotal) * 100) : 0;
                
                $exceptEntryCrimeClearanceTotal = $items->sum('except_entry_crime_clearance');
                $exceptEntryCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($exceptEntryCrimeClearanceTotal/$accidentTotal) * 100) : 0;
                $p21ExceptEntryTotal = $items->sum('p21_except_entry');
                $sp3ExceptEntryTotal = $items->sum('sp3_except_entry');
                $diversiExceptEntryTotal = $items->sum('diversi_except_entry');
                $sp2lidExceptEntryTotal = $items->sum('sp2lid_except_entry');

                $beforeEvalCrimeClearanceTotal = $items->sum('before_eval_crime_clearance');
                $beforeEvalCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($beforeEvalCrimeClearanceTotal/$accidentTotal) * 100) : 0;
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
                        if(isset($item['polda_name'])){
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
        // dd($recapLeaderboardItems);

        // Selra Rekap 2025 (Bukan Lomba)
        $recap2025BeginDate = "2025-01-01";
        $recap2025LimitDate = date('Y-m-d');
        $recap2025NewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
        $recap2025NewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';
        $recap2025ExceptCrimeClearanceStartTime = '2025-01-02 00:00:00';
        $recap2025ExceptCrimeClearanceEndTime = date('Y-m-d') . ' 00:00:00';

        $recap2025CaseResolutions = DB::table('lib.polices as xpolices')->select('xpolices.id as polres_id', 'xpolices.name as polres_name', 'xpolices.category as polres_category', 'ypolices.id as polda_id', 'ypolices.name as polda_name')
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0101' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' THEN 1 ELSE 0 END) as p21")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0102' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' THEN 1 ELSE 0 END) as sp3")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0103' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' THEN 1 ELSE 0 END) as diversi")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0108' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' THEN 1 ELSE 0 END) as sp2lid")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0104' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' THEN 1 ELSE 0 END) as pomtni")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag IN ('S0101', 'S0102', 'S0103', 'S0104', 'S0108') AND accidents.special_info = 'TABRAK_LARI' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' THEN 1 ELSE 0 END) as crime_clearance_tabraklari")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' AND accident_resolutions.created_at BETWEEN '$recap2025NewCrimeClearanceStartTime' AND '$recap2025NewCrimeClearanceEndTime' THEN 1 ELSE 0 END) as new_entry_crime_clearance")
            
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0101' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' AND accident_resolutions.created_at BETWEEN '$recap2025ExceptCrimeClearanceStartTime' AND '$recap2025ExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as p21_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0102' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' AND accident_resolutions.created_at BETWEEN '$recap2025ExceptCrimeClearanceStartTime' AND '$recap2025ExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as sp3_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0103' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' AND accident_resolutions.created_at BETWEEN '$recap2025ExceptCrimeClearanceStartTime' AND '$recap2025ExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as diversi_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accident_resolutions.type_id = 'S0108' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' AND accident_resolutions.created_at BETWEEN '$recap2025ExceptCrimeClearanceStartTime' AND '$recap2025ExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as sp2lid_except_entry")
            ->selectRaw("SUM(CASE WHEN accident_resolutions.accident_id IS NOT NULL AND accidents.selra_flag <> 'S0104' AND accidents.accident_date BETWEEN '$recap2025BeginDate' AND '$recap2025LimitDate' AND accident_resolutions.created_at BETWEEN '$recap2025ExceptCrimeClearanceStartTime' AND '$recap2025ExceptCrimeClearanceEndTime' THEN 1 ELSE 0 END) as except_entry_crime_clearance")
            ->leftJoin('accidents', 'xpolices.id', '=', 'accidents.police_id')
            ->leftJoin('accident_resolutions', 'accidents.id', '=', 'accident_resolutions.accident_id')
            ->join('lib.polices as ypolices', 'xpolices.parent_id', '=', 'ypolices.id')
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->groupBy('xpolices.id', 'xpolices.name', 'ypolices.id', 'ypolices.name')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        try {
            $recap2025ResponseCases = Http::withHeaders([
                'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
                'Content-Type' => 'application/json'
            ])
            ->withQueryParameters([
                'start_date' => $recap2025BeginDate,
                'end_date' => $recap2025LimitDate
            ])
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka')
                ->json();
            $recap2025Cases = $recap2025ResponseCases['result'];
        } catch (\Throwable $th) {
            $recap2025Cases = [];
        }
        
        $recap2025CaseCollections = collect($recap2025Cases);
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
                    'except_entry_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($except_entry_crime_clearance/$jumlah_laka) * 100) : 0,
                    'p21_except_entry' => $p21_except_entry,
                    'sp3_except_entry' => $sp3_except_entry,
                    'diversi_except_entry' => $diversi_except_entry,
                    'sp2lid_except_entry' => $sp2lid_except_entry,

                    'before_eval_crime_clearance' => $before_eval_crime_clearance,
                    'before_eval_crime_clearance_percentage' => ($jumlah_laka != 0) ? (($before_eval_crime_clearance/$jumlah_laka) * 100) : 0,
                    'before_eval_p21' => $p21 - $p21_except_entry,
                    'before_eval_sp3' => $sp3 - $sp3_except_entry,
                    'before_eval_diversi' => $diversi - $diversi_except_entry,
                    'before_eval_sp2lid' => $sp2lid - $sp2lid_except_entry,

                    'percentage_p21' => ($jumlah_laka != 0) ? (($p21/$jumlah_laka) * 100) : 0,
                    'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3/$jumlah_laka) * 100) : 0,
                    'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid/$jumlah_laka) * 100) : 0,
                    'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi/$jumlah_laka) * 100) : 0,
                    'percentage_total' => ($jumlah_laka != 0) ? (($total/$jumlah_laka) * 100) : 0,
                    'percentage_on_progress' => ($jumlah_laka != 0) ? (($on_progress/$jumlah_laka) * 100) : 0,
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
                $p21TotalPercentage = ($accidentTotal != 0) ? (($p21Total/$accidentTotal) * 100) : 0;
                $sp3TotalPercentage = ($accidentTotal != 0) ? (($sp3Total/$accidentTotal) * 100) : 0;
                $sp2lidTotalPercentage = ($accidentTotal != 0) ? (($sp2lidTotal/$accidentTotal) * 100) : 0;
                $diversiTotalPercentage = ($accidentTotal != 0) ? (($diversiTotal/$accidentTotal) * 100) : 0;
                $totalTotalPercentage = ($accidentTotal != 0) ? (($totalTotal/$accidentTotal) * 100) : 0;
                $onProgressTotalPercentage = ($accidentTotal != 0) ? (($onProgressTotal/$accidentTotal) * 100) : 0;
                
                $exceptEntryCrimeClearanceTotal = $items->sum('except_entry_crime_clearance');
                $exceptEntryCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($exceptEntryCrimeClearanceTotal/$accidentTotal) * 100) : 0;
                $p21ExceptEntryTotal = $items->sum('p21_except_entry');
                $sp3ExceptEntryTotal = $items->sum('sp3_except_entry');
                $diversiExceptEntryTotal = $items->sum('diversi_except_entry');
                $sp2lidExceptEntryTotal = $items->sum('sp2lid_except_entry');

                $beforeEvalCrimeClearanceTotal = $items->sum('before_eval_crime_clearance');
                $beforeEvalCrimeClearanceTotalPercentage = ($accidentTotal != 0) ? (($beforeEvalCrimeClearanceTotal/$accidentTotal) * 100) : 0;
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
                        if(isset($item['polda_name'])){
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
            // 'beginDate',
            // 'limitDate',
            // 'cat1',
            // 'cat2',
            // 'cat3',
            // 'leaderboardItems', 
            // 'leaderboardCategoryUnderFiveHundredItems',
            // 'leaderboardCategoryUpperFiveHundredItems',
            // 'leaderboardCategoryUpperOneThousandItems',

            //'leaderboardCategoryBigCityItems',
            'recapBeginDate',
            'recapLimitDate',
            'recapLeaderboardItems', 
            'recap2025BeginDate',
            'recap2025LimitDate',
            'recap2025LeaderboardItems',
            'recapLombaBeginDate',
            'recapLombaLimitDate', 
            'recapLombaLeaderboardItems',
        ) );

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
        ->where('officers.user_id',$id)
        ->first();

        // dd($userData);

        return view('profile',compact('user','userData'));
    }

    public function reset_password(){
        $user = Auth::getUser();

        $username = $user->username;

        return view('reset_password', compact('username'));
    }

    public function post_reset_password(Request $request){
        $request->validate([
            'newPassword' => 'required|confirmed',
        ]);

        User::where('username',$request->username)->update(['password' => Hash::make($request->newPassword)]);
        return redirect('/login');
    }

    public function update_profile(Request $request)
    {
        if($request->hasFile('avatar')){
    		$avatar = $request->file('avatar');
    		$filename = time() . '.' . $avatar->getClientOriginalExtension();
    		Image::make($avatar)->resize(300, 300)->save( public_path('/image-profile/profile640/' . $filename ) );

    		$user = Auth::user();
    		$user->avatar = $filename;
    		$user->save();
    	}

    	// return view('profile', array('user' => Auth::user()) );

    	return redirect('profile')->with(array('user' => Auth::user()) );

    }

    // $this->validate($request , [
    //     'avatar' =>  'required|image|mimes:jpg,jpeg,png'
    //     ]);dd($name);


    public function getChartBulan(){
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
                for($x=0;$x<=$range;$x++){
                    $current_month = Carbon::now()->subMonth($range - $x);
                    $date = $current_month->month;
                    $date_year = $current_month->year;

                    // $counts = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
                    $counts = DB::table('accidents')
                            ->join('polres', 'accidents.polres_id', 'polres.id')
                            ->join('polda', 'polres.polda_id', 'polda.id')
                            ->where('polda.id','=',$poldaId)
                            ->where('polres.state','=',1)
                            ->where('polda.state','=',1)
                            ->whereMonth('accidents.created_at', '=', $date)->get(['accidents.id'])->count();
                    $count = intval($counts);
                    // setlocale(LC_TIME, 'id');
                    $date = $current_month->formatLocalized('%B') . " " . $current_month->year;
                    $output = collect(['date' => $date, 'count' => $count]);
                    $outputs->push($output);
                }
                break;
            case 3:
                for($x=0;$x<=$range;$x++){
                    $current_month = Carbon::now()->subMonth($range - $x);
                    $date = $current_month->month;

                    // return($date);
                    $date_year = $current_month->year;

                    // $counts = DB::table('accidents')->whereMonth('created_at', '=', $date)->get(['accidents.id'])->count();
                    $counts = DB::table('accidents')
                            ->join('polres', 'accidents.polres_id', 'polres.id')
                            ->where('polres.id','=',$polresId)
                            ->where('polres.state','=',1)
                            ->whereMonth('accidents.created_at', '=', $date)
                            ->whereYear('accidents.created_at','=',$date_year)->get(['accidents.id'])->count();
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

    public function getPieBulan(){
        $range = 11;
        $outputs = collect();
        $polresId = Auth::user()->polres_id;
        $poldaId = Auth::user()->polda_id;
        $role = Auth::user()->role_id;
        $jumlah_laka=0;


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


        switch($role){
            case 2:
                    $current_month = Carbon::now()->subMonth(0);
                    $current_year = Carbon::now()->subYear();
                    $month = $current_month->formatLocalized('%B');
                    $date_month = $month;
                    $date_year = $current_month->year;
                    $jumlah_laka = DB::table('accidents')
                            ->join('polres', 'accidents.polres_id', 'polres.id')
                            ->join('polda', 'polres.polda_id', 'polda.id')
                            ->where('polda.id','=',$poldaId)
                            ->where('polres.state','=',1)
                            ->where('polda.state','=',1)
                            ->whereNotIn('accidents.selra_flag', ['S0107'])
                            ->whereYear('accidents.created_at', '=', $date_year)
                            ->get(['accidents.id'])
                            ->count();

                    $selra_id = DB::table('ref')
                                ->where('ref.grp_id','=','S01')->orderBy('sort')->get();

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
                                polda.id = \''.$poldaId.'\'
                                and polres.state = 1
                                and polda.state = 1
                                and date_part(\'year\',  accidents.created_at) = \''.$date_year.'\'
                                and ref.grp_id = \'S01\'
                                group by REF.ID,ref.name,ref.sort
                                order by ref.sort
                            ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                            where
                            ref.grp_id = \'S01\'
                            and ref.id NOT IN (\'S0107\', \'S0106\')
                        '
                    );
                    $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'jumlah_laka'=>$jumlah_laka,'jumlah_selra'=>$jumlah_selra]);
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
                        ->where('polres.id','=',$polresId)
                        ->where('polres.state','=',1)
                        ->where('polda.state','=',1)
                        ->whereNotIn('accidents.selra_flag', ['S0107'])
                        ->whereYear('accidents.created_at', '=', $date_year)
                        ->get(['accidents.id'])
                        ->count();

                $selra_id = DB::table('ref')
                            ->where('ref.grp_id','=','S01')->orderBy('sort')->get();

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
                            polres.id = \''.$polresId.'\'
                            and polres.state = 1
                            and polda.state = 1
                            and date_part(\'year\',  accidents.created_at) = \''.$date_year.'\'
                            and ref.grp_id = \'S01\'
                            group by REF.ID,ref.name,ref.sort
                            order by ref.sort
                        ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                        where
                        ref.grp_id = \'S01\'
                        and ref.id NOT IN (\'S0107\', \'S0106\')
                    '
                );
                $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'jumlah_laka'=>$jumlah_laka,'jumlah_selra'=>$jumlah_selra]);
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
                        ->where('polres.state','=',1)
                        ->where('polda.state','=',1)
                        ->whereNotIn('accidents.selra_flag', ['S0107'])
                        ->whereYear('accidents.created_at', '=', $date_year)
                        ->get(['accidents.id'])
                        ->count();

                $selra_id = DB::table('ref')
                            ->where('ref.grp_id','=','S01')->orderBy('sort')->get();

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
                            and date_part(\'year\',  accidents.created_at) = \''.$date_year.'\'
                            and ref.grp_id = \'S01\'
                            group by REF.ID,ref.name,ref.sort
                            order by ref.sort
                        ) AS ACCIDENT ON  REF.ID = ACCIDENT.ID
                        where
                        ref.grp_id = \'S01\'
                        and ref.id NOT IN (\'S0107\', \'S0106\')
                    '
                );
                $output = collect(['date_month' => $date_month, 'date_year' => $date_year, 'jumlah_laka'=>$jumlah_laka,'jumlah_selra'=>$jumlah_selra]);
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
