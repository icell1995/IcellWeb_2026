<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ExportStatistika;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
// use DB;
use App\Models\Polda;
use App\Models\Polres;
use App\Models\Ref;
use App\Models\Accident;
use Auth;
use Carbon\Carbon;


class statistikaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('statistika.statistika');
    }

    //controller untuk bulanan
    public function index_month(Request $request)
    {
        $bulan=Carbon::parse($request->input('bulan'))->format('Y-m-d');
        $checkyear=Carbon::now()->year;
        $checkBulan = Carbon::parse($request->input('bulan'))->addMonths()->format('Y-m-d');//it's for check between date what you input or check 30 days from feature in date in form
        $polres = $request->input('polres_id');
        $status = $request->input('selra_id');
        $user = Auth::user();
        $md = Accident::select('md')->count();
        $roleData=Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                    $polda=Polda::where('id','=',$user->polda_id)->get();
                    $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 4:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            case 5:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            default:
                $polda=Polda::all();
                $polres=Polres::all();
                break;
        }
        $selra = Ref::where('grp_id','=','S01')->get();
        $md = Accident::select(DB::raw("SUM(md) as count"))
                       ->get()->toArray();

        return view('statistika/statistika_month',compact('polda','polres','selra', 'status'));
    }



    private function queryPeriodStats(string $startDate, string $endDate, string $polda = '-', string $polres = '-', bool $useDateCast = true): object
    {
        $query = DB::table('accidents')
            ->join('polres', 'polres.id', '=', 'accidents.polres_id')
            ->join('polda', 'polda.id', '=', 'polres.polda_id')
            ->selectRaw("
                COUNT(accidents.id) AS countdata,
                COALESCE(SUM(accidents.md), 0) AS md,
                COALESCE(SUM(accidents.lb), 0) AS lb,
                COALESCE(SUM(accidents.lr), 0) AS lr,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS p21,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS sp3,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS diversi,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0104') AS tni,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0106') AS adat_rj,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS sp2led,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0107') AS dalam_proses
            ")
            ->when($polda !== '-', fn ($q) => $q->where('polda.id', $polda))
            ->when($polres !== '-', fn ($q) => $q->where('polres.id', $polres));

        if ($useDateCast) {
            $query->whereBetween(DB::raw('DATE(accidents.accident_date)'), [$startDate, $endDate]);
        } else {
            $query->whereBetween('accidents.accident_date', [$startDate, $endDate])
                ->where('polda.state', '<>', 0)
                ->where('polres.state', '<>', 0);
        }

        return $query->first();
    }

    public function chartcalculationMonth(Request $request){
        $bulan=Carbon::parse($request->input('bulan'))->format('Y-m-d');
        $textMonth=Carbon::parse($request->input('bulan'))->translatedFormat('F');
        $textMonthPast=Carbon::parse($request->input('bulan'))->addMonths(-1)->translatedFormat('F');
        $checkBulanPast = Carbon::parse($request->input('bulan'))->addMonths(-1)->format('Y-m-d');
        $checkBulanPast30 = Carbon::parse($request->input('bulan'))->addDays(-1)->format('Y-m-d');
        $checkBulan = Carbon::parse($request->input('bulan'))->addMonths()->addDays(-1)->format('Y-m-d');
        $polres = $request->input('polres_id') ?: '-';
        $polda  = $request->input('polda_id') ?: '-';
        $status = $request->input('selra_id');

        $curr = $this->queryPeriodStats($bulan, $checkBulan, (string) $polda, (string) $polres, true);
        $past = $this->queryPeriodStats($checkBulanPast, $checkBulanPast30, (string) $polda, (string) $polres, true);

        if ($polres !== "-" && $polda !== "-") {
            $polresName1 = Polres::where('id', '=', $polres)->get('name');
            $poldaName1 = Polda::where('id', '=', $polda)->get('name');
            $polresName = isset($polresName1[0]) ? json_encode($polresName1[0]->name) : ' ';
            $poldaName = isset($poldaName1[0]) ? json_encode($poldaName1[0]->name) : ' ';
            $diversiPastKey = 'disversiPast';
        } elseif ($polres === "-" && $polda !== "-") {
            $polresName1 = Polres::where('id', '=', $polres)->get('name');
            $poldaName1 = Polda::where('id', '=', $polda)->get('name');
            $polresName = $polresName1;
            $poldaName = isset($poldaName1[0]) ? json_encode($poldaName1[0]->name) : ' ';
            $diversiPastKey = 'disversiPast';
        } else {
            $polresName = ' ';
            $poldaName = 'Semua Polda';
            $diversiPastKey = 'diversiPast';
        }

        return response()->json([
            'countdata' => (int) $curr->countdata,
            'md' => (int) $curr->md,
            'lb'=> (int) $curr->lb,
            'lr'=> (int) $curr->lr,
            'mdPast' => (int) $past->md,
            'lbPast'=> (int) $past->lb,
            'lrPast' => (int) $past->lr,
            'textMonth' => $textMonth,
            'textMonthPast' => $textMonthPast,
            'checkBulanPast' => $checkBulanPast,
            'checkBulanPast30' => $checkBulanPast30,
            'checkBulan'=> $checkBulan,
            'p21' => (int) $curr->p21,
            'sp3' => (int) $curr->sp3,
            'diversi' => (int) $curr->diversi,
            'tni' => (int) $curr->tni,
            'adat_rj' => (int) $curr->adat_rj,
            'sp2led' => (int) $curr->sp2led,
            'dalamProses' => (int) $curr->dalam_proses,
            'p21Past' => (int) $past->p21,
            'sp3Past' => (int) $past->sp3,
            $diversiPastKey => (int) $past->diversi,
            'tniPast' => (int) $past->tni,
            'adat_rjPast' => (int) $past->adat_rj,
            'sp2ledPast' => (int) $past->sp2led,
            'dalamProsesPast' => (int) $past->dalam_proses,
            'polresName' => $polresName,
            'poldaName' => $poldaName
        ]);
    }




    public function get_months()
    {
        // $id='b34b7c31-d1b9-4240-a30c-91a51de5bc35';
        // $test=DB::select('select id,no_lp,road_name from accidents where id = \''.$id.'\'');
        // $a=$test[0];
        return response()->json('sukses');
    }

    //controller untuk mingguan
    public function index_week()
    {
        $user = Auth::user();
        $roleData=Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                    $polda=Polda::where('id','=',$user->polda_id)->get();
                    $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 4:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            case 5:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            default:
                $polda=Polda::all();
                $polres=Polres::all();
                break;
        }

        $selra = Ref::where('grp_id','=','S01')->get();
        return view('statistika/statistika_week',compact('polda','polres','selra'));
    }

    public function chartcalculationWeek(Request $request){
        $date = Carbon::parse($request->input('week'))->format('Y-m-d');
        $dateEnd = Carbon::parse($date)->addDays(6)->format('Y-m-d');
        $prevStart = Carbon::parse($date)->subDays(7)->format('Y-m-d');
        $prevEnd = Carbon::parse($date)->subDays(1)->format('Y-m-d');

        $polda  = $request->input('polda_id') ?: '-';
        $polres = $request->input('polres_id') ?: '-';

        $week1 = $this->queryPeriodStats($date, $dateEnd, (string) $polda, (string) $polres, false);
        $week2 = $this->queryPeriodStats($prevStart, $prevEnd, (string) $polda, (string) $polres, false);

        $poldaName = $polda !== '-' ? Polda::where('id', $polda)->value('name') : null;
        $polresName = $polres !== '-' ? Polres::where('id', $polres)->value('name') : null;

        $summary = [
            'total_laka' => (int) $week1->countdata,
            'md'         => (int) $week1->md,
            'lb'         => (int) $week1->lb,
            'lr'         => (int) $week1->lr,
        ];

        $summary_selra = [
            'p21'          => (int) $week1->p21,
            'sp3'          => (int) $week1->sp3,
            'diversi'      => (int) $week1->diversi,
            'pom_tni'      => (int) $week1->tni,
            'adat_rj'      => (int) $week1->adat_rj,
            'sp2lid'       => (int) $week1->sp2led,
            'dalam_proses' => (int) $week1->dalam_proses,
        ];

        $facilityselra = [
            'labels'             => [],
            'value_p21'          => [(int) $week2->p21, (int) $week1->p21],
            'value_sp3'          => [(int) $week2->sp3, (int) $week1->sp3],
            'value_diversi'      => [(int) $week2->diversi, (int) $week1->diversi],
            'value_pom_tni'      => [(int) $week2->tni, (int) $week1->tni],
            'value_adat_rj'      => [(int) $week2->adat_rj, (int) $week1->adat_rj],
            'value_sp2lid'       => [(int) $week2->sp2led, (int) $week1->sp2led],
            'value_dalam_proses' => [(int) $week2->dalam_proses, (int) $week1->dalam_proses],
        ];

        $dateFrom = Carbon::parse($date)->subDays(7);
        for ($i = 0; $i < 2; $i++) {
            $dates = $dateFrom->format('d/m');
            $newDate = $dateFrom->addDays(6);
            $newDates = $newDate->format('d/m');
            $facilityselra['labels'][] = $dates . '-' . $newDates;
            $dateFrom = $newDate->addDays(1);
        }

        $chartSelra = [
            'labels' => $facilityselra['labels'],
            'datasets' => [
                ['label' => 'P21', 'data' => $facilityselra['value_p21']],
                ['label' => 'SP3', 'data' => $facilityselra['value_sp3']],
                ['label' => 'DIVERSI', 'data' => $facilityselra['value_diversi']],
                ['label' => 'POM/TNI', 'data' => $facilityselra['value_pom_tni']],
                ['label' => 'ADAT/RJ', 'data' => $facilityselra['value_adat_rj']],
                ['label' => 'SP2LID', 'data' => $facilityselra['value_sp2lid']],
                ['label' => 'DALAM PROSES', 'data' => $facilityselra['value_dalam_proses']],
            ]
        ];

        $data = [
            'poldaName'     => $poldaName ?: 'Semua Polda',
            'polresName'    => $polresName,
            'datetitle'     => date('d F Y', strtotime($date)) . ' - ' . date('d F Y', strtotime($dateEnd)),
            'summary'       => $summary,
            'summary_selra' => $summary_selra,
            'date'          => Carbon::parse($request->input('hari'))->format('d-m-Y'),
            'cart'          => [
                'chartSelra' => $chartSelra,
            ]
        ];
        return response()->json(['data' => $data]);
    }


    public function get_weeks()
    {
        // $id='b34b7c31-d1b9-4240-a30c-91a51de5bc35';
        // $test=DB::select('select id,no_lp,road_name from accidents where id = \''.$id.'\'');
        // $a=$test[0];
        return response()->json('sukses');
    }

    //controller untuk harian
    public function index_day()
    {
        $user = Auth::user();
        $roleData=Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                    $polda=Polda::where('id','=',$user->polda_id)->get();
                    $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 4:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            case 5:
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            default:
                $polda=Polda::all();
                $polres=Polres::all();
                break;
        }

        $selra = Ref::where('grp_id','=','S01')->get();
        return view('statistika/statistika_day',compact('polda','polres','selra'));
    }

    public function chartcalculationDays(Request $request){
        $hari = Carbon::parse($request->input('hari'))->format('Y-m-d');
        $hariPrev = Carbon::parse($hari)->subDays(1)->format('Y-m-d');
        $polda  = $request->input('polda_id') ?: '-';
        $polres = $request->input('polres_id') ?: '-';

        $day1 = DB::table('accidents')
            ->join('polres', 'polres.id', '=', 'accidents.polres_id')
            ->join('polda', 'polda.id', '=', 'polres.polda_id')
            ->selectRaw("
                COUNT(accidents.id) AS countdata,
                COALESCE(SUM(accidents.md), 0) AS md,
                COALESCE(SUM(accidents.lb), 0) AS lb,
                COALESCE(SUM(accidents.lr), 0) AS lr,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS p21,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS sp3,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS diversi,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0104') AS tni,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0106') AS adat_rj,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS sp2led,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0107') AS dalam_proses
            ")
            ->when($polda !== '-', fn ($q) => $q->where('polda.id', $polda))
            ->when($polres !== '-', fn ($q) => $q->where('polres.id', $polres))
            ->where(DB::raw('DATE(accidents.accident_date)'), $hari)
            ->where('polda.state', '<>', 0)
            ->where('polres.state', '<>', 0)
            ->first();

        $day2 = DB::table('accidents')
            ->join('polres', 'polres.id', '=', 'accidents.polres_id')
            ->join('polda', 'polda.id', '=', 'polres.polda_id')
            ->selectRaw("
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS p21,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS sp3,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS diversi,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0104') AS tni,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0106') AS adat_rj,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS sp2led,
                COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0107') AS dalam_proses
            ")
            ->when($polda !== '-', fn ($q) => $q->where('polda.id', $polda))
            ->when($polres !== '-', fn ($q) => $q->where('polres.id', $polres))
            ->where(DB::raw('DATE(accidents.accident_date)'), $hariPrev)
            ->where('polda.state', '<>', 0)
            ->where('polres.state', '<>', 0)
            ->first();

        $poldaName = $polda !== '-' ? Polda::where('id', $polda)->value('name') : null;
        $polresName = $polres !== '-' ? Polres::where('id', $polres)->value('name') : null;

        $summary = [
            'count_data' => (int) $day1->countdata,
            'md'         => (int) $day1->md,
            'lb'         => (int) $day1->lb,
            'lr'         => (int) $day1->lr,
        ];

        $summary_selra = [
            'p21'          => (int) $day1->p21,
            'sp3'          => (int) $day1->sp3,
            'diversi'      => (int) $day1->diversi,
            'pom_tni'      => (int) $day1->tni,
            'adat_rj'      => (int) $day1->adat_rj,
            'sp2lid'       => (int) $day1->sp2led,
            'dalam_proses' => (int) $day1->dalam_proses,
        ];

        $facilityselra = [
            'labels'             => [],
            'value_p21'          => [(int) $day2->p21, (int) $day1->p21],
            'value_sp3'          => [(int) $day2->sp3, (int) $day1->sp3],
            'value_diversi'      => [(int) $day2->diversi, (int) $day1->diversi],
            'value_pom_tni'      => [(int) $day2->tni, (int) $day1->tni],
            'value_adat_rj'      => [(int) $day2->adat_rj, (int) $day1->adat_rj],
            'value_sp2lid'       => [(int) $day2->sp2led, (int) $day1->sp2led],
            'value_dalam_proses' => [(int) $day2->dalam_proses, (int) $day1->dalam_proses],
        ];

        $dateFrom = Carbon::parse($hari)->subDays(1);
        for ($i = 0; $i < 2; $i++) {
            $dates = $dateFrom->format('d/m/y');
            $newDate = $dateFrom->addDays(1);
            $facilityselra['labels'][] = $dates;
            $dateFrom = $newDate;
        }

        $chartSelra = [
            'labels' => $facilityselra['labels'],
            'datasets' => [
                ['label' => 'P21', 'data' => $facilityselra['value_p21']],
                ['label' => 'SP3', 'data' => $facilityselra['value_sp3']],
                ['label' => 'DIVERSI', 'data' => $facilityselra['value_diversi']],
                ['label' => 'POM/TNI', 'data' => $facilityselra['value_pom_tni']],
                ['label' => 'ADAT/RJ', 'data' => $facilityselra['value_adat_rj']],
                ['label' => 'SP2LID', 'data' => $facilityselra['value_sp2lid']],
                ['label' => 'DALAM PROSES', 'data' => $facilityselra['value_dalam_proses']],
            ]
        ];

        $data = [
            'poldaName'     => $poldaName ?: 'Semua Polda',
            'polresName'    => $polresName,
            'datetitle'     => Carbon::parse($request->input('hari'))->format('d-m-Y'),
            'summary'       => $summary,
            'summary_selra' => $summary_selra,
            'cart'          => [
                'chartSelra' => $chartSelra,
            ]
        ];
        return response()->json(['data' => $data]);
    }

    public function get_days()
    {
        // $id='b34b7c31-d1b9-4240-a30c-91a51de5bc35';
        // $test=DB::select('select id,no_lp,road_name from accidents where id = \''.$id.'\'');
        // $a=$test[0];
        return response()->json('sukses');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
