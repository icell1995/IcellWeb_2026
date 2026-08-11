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



    public function chartcalculationMonth(Request $request){
        $bulan=Carbon::parse($request->input('bulan'))->format('Y-m-d');
        $textMonth=Carbon::parse($request->input('bulan'))->formatLocalized('%B');
        $textMonthPast=Carbon::parse($request->input('bulan'))->addMonths(-1)->formatLocalized('%B');
        $checkBulanPast = Carbon::parse($request->input('bulan'))->addMonths(-1)->format('Y-m-d');
        $checkBulanPast30 = Carbon::parse($request->input('bulan'))->addDays(-1)->format('Y-m-d');
        $checkBulan = Carbon::parse($request->input('bulan'))->addMonths()->addDays(-1)->format('Y-m-d');//it's for check between date what you input or check 30 days from feature in date in form
        $polres = $request->input('polres_id');
        $polda  = $request->input('polda_id');
        $status = $request->input('selra_id');
        if($polres == null){
            $polres = '-';
        }else{
            $polres = $polres;
        }
        if($polda == null){
            $polda = '-';
        }else{
            $polda = $polda;
        }
        if($polres!="-" && $polda!="-"){

        $countdata = DB::table('accidents')
                        ->where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();
        $md = Accident::where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('md');

        $lb = Accident::where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('lb');

        $lr = Accident::where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('lr');

        $p21 = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0101')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $sp3 = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0102')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $diversi = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0103')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $tni = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0104')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $adat_rj = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0106')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $sp2led = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0108')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $dalamProses = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0107')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $mdPast = Accident::where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('md');

        $lbPast = Accident::where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('lb');

        $lrPast = Accident::where('polres_id','=', $polres)
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('lr');

        $p21Past = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0101')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $sp3Past = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0102')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $diversiPast = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0103')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $tniPast = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0104')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $adat_rjPast = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0106')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $sp2ledPast = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0108')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $dalamProsesPast = Accident::where('polres_id','=', $polres)
                        ->where('selra_flag', '=', 'S0107')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $polresName1 = Polres::where('id', '=', $polres)
                        ->get('name');

        $poldaName1 = Polda::where('id', '=', $polda)
                        ->get('name');

        $polresName = json_encode($polresName1[0]->name);
        $poldaName = json_encode($poldaName1[0]->name);
        return response()->json([
            'countdata' => $countdata,
            'md' => $md,
            'lb'=> $lb,
            'lr'=> $lr,
            'mdPast' => $mdPast,
            'lbPast'=> $lbPast,
            'lrPast' => $lrPast,
            'textMonth' => $textMonth,
            'textMonthPast' => $textMonthPast,
            'checkBulanPast' => $checkBulanPast,
            'checkBulanPast30' => $checkBulanPast30,
            'checkBulan'=> $checkBulan,
            'p21' => $p21,
            'sp3' => $sp3,
            'diversi' => $diversi,
            'tni' => $tni,
            'adat_rj' => $adat_rj,
            'sp2led' => $sp2led,
            'dalamProses' => $dalamProses,
            'p21Past' => $p21Past,
            'sp3Past' => $sp3Past,
            'disversiPast' => $diversiPast,
            'tniPast' => $tniPast,
            'adat_rjPast' => $adat_rjPast,
            'sp2ledPast' => $sp2ledPast,
            'dalamProsesPast' => $dalamProsesPast,
            'polresName' => $polresName,
            'poldaName' => $poldaName
        ]);
    }
    else if($polres=="-" && $polda!="-"){

        $countdata = DB::table('accidents')
                        ->join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $md = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('md');

        $lb = Accident::join('polres','polres.id','=','accidents.polres_id')
                    ->join('polda','polda.id','=','polres.polda_id')
                    ->where('polda.id','=', $polda)
                    ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                    ->sum('md');

        $lr = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('md');

        $p21 = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0101')
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $sp3 = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $diversi = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0103')
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $tni = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0104')
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $adat_rj = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0106')
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $sp2led = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0108')
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $dalamProses = Accident::  join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0107')
                        ->whereraw("date(accidents.accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $mdPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('md');

        $lbPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date)between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('lb');

        $lrPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('lr');

        $p21Past = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0101')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $sp3Past = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0102')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $diversiPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0103')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $tniPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0104')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $adat_rjPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0106')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $sp2ledPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0108')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $dalamProsesPast = Accident::join('polres','polres.id','=','accidents.polres_id')
                        ->join('polda','polda.id','=','polres.polda_id')
                        ->where('polda.id','=', $polda)
                        ->where('selra_flag', '=', 'S0107')
                        ->whereraw("date(accidents.accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $polresName1 = Polres::where('id', '=', $polres)
                        ->get('name');

        $poldaName1 = Polda::where('id', '=', $polda)
                        ->get('name');

        $polresName = ' ';
        $poldaName = json_encode($poldaName1[0]->name);

        // dd($polresName1);
        return response()->json([
            'countdata' => $countdata,
            'md' => $md,
            'lb'=> $lb,
            'lr'=> $lr,
            'mdPast' => $mdPast,
            'lbPast'=> $lbPast,
            'lrPast' => $lrPast,
            'textMonth' => $textMonth,
            'textMonthPast' => $textMonthPast,
            'checkBulanPast' => $checkBulanPast,
            'checkBulanPast30' => $checkBulanPast30,
            'checkBulan'=> $checkBulan,
            'p21' => $p21,
            'sp3' => $sp3,
            'diversi' => $diversi,
            'tni' => $tni,
            'adat_rj' => $adat_rj,
            'sp2led' => $sp2led,
            'dalamProses' => $dalamProses,
            'p21Past' => $p21Past,
            'sp3Past' => $sp3Past,
            'disversiPast' => $diversiPast,
            'tniPast' => $tniPast,
            'adat_rjPast' => $adat_rjPast,
            'sp2ledPast' => $sp2ledPast,
            'dalamProsesPast' => $dalamProsesPast,
            'polresName' => $polresName1,
            'poldaName' => $poldaName
        ]);
    }
    else{
        $countdata = DB::table('accidents')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $md = Accident::whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('md');

        $lb = Accident::whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('lb');

        $lr = Accident::whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->sum('lr');

        $p21 = Accident::where('selra_flag', '=', 'S0101')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $sp3 = Accident::where('selra_flag', '=', 'S0102')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $diversi = Accident::where('selra_flag', '=', 'S0103')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $tni = Accident::where('selra_flag', '=', 'S0104')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $adat_rj = Accident::where('selra_flag', '=', 'S0106')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $sp2led = Accident::where('selra_flag', '=', 'S0108')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $dalamProses = Accident::where('selra_flag', '=', 'S0107')
                        ->whereraw("date(accident_date) between '$bulan' and '$checkBulan'")
                        ->count();

        $mdPast = Accident::whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('md');

        $lbPast = Accident::whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('lb');

        $lrPast = Accident::whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->sum('lr');

        $p21Past = Accident::where('selra_flag', '=', 'S0101')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $sp3Past = Accident::where('selra_flag', '=', 'S0102')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $diversiPast = Accident::where('selra_flag', '=', 'S0103')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $tniPast = Accident::where('selra_flag', '=', 'S0104')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $adat_rjPast = Accident::where('selra_flag', '=', 'S0106')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $sp2ledPast = Accident::where('selra_flag', '=', 'S0108')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();

        $dalamProsesPast = Accident::where('selra_flag', '=', 'S0107')
                        ->whereraw("date(accident_date) between '$checkBulanPast' and '$checkBulanPast30'")
                        ->count();


        $polresName = ' ';
        $poldaName = ' ';


        return response()->json([
            'countdata' => $countdata,
            'md' => $md,
            'lb'=> $lb,
            'lr'=> $lr,
            'mdPast' => $mdPast,
            'lbPast'=> $lbPast,
            'lrPast' => $lrPast,
            'textMonth' => $textMonth,
            'textMonthPast' => $textMonthPast,
            'checkBulanPast' => $checkBulanPast,
            'checkBulanPast30' => $checkBulanPast30,
            'checkBulan'=> $checkBulan,
            'p21' => $p21,
            'sp3' => $sp3,
            'diversi' => $diversi,
            'tni' => $tni,
            'adat_rj' => $adat_rj,
            'sp2led' => $sp2led,
            'dalamProses' => $dalamProses,
            'p21Past' => $p21Past,
            'sp3Past' => $sp3Past,
            'diversiPast' => $diversiPast,
            'tniPast' => $tniPast,
            'adat_rjPast' => $adat_rjPast,
            'sp2ledPast' => $sp2ledPast,
            'dalamProsesPast' => $dalamProsesPast,
            'polresName' => $polresName ,
            'poldaName' => 'Semua Polda'
        ]);
    }
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
        $date=Carbon::parse($request->input('week'))->format('Y-m-d');
        $polda  = $request->input('polda_id');
        $polres = $request->input('polres_id');
        if($polres == null){
            $polres = '-';
        }else{
            $polres = $polres;
        }
        // $status = $request->input('selra_id');


        $expression = DB::raw("select(SELECT row_to_json(t)
        	FROM(
        	SELECT
        	'$date'||' - '||date'$date' + INTERVAL '6 days' AS range_date,
        	(SELECT CASE WHEN '$polda' <> '-' THEN name ELSE 'All Polda' END FROM polda WHERE id = '$polda') AS polda,
        	(SELECT CASE WHEN '$polres' <> '-' THEN name ELSE 'All Polres' END FROM polres WHERE id = '$polres') AS polres,
        	now() AS tanggal_dan_jam_pencarian,
			(
				SELECT array_to_json(array_agg(row_to_json(w)))
				FROM(
					SELECT
					(
						SELECT count(accidents.id)
						FROM polda
						JOIN polres ON polda.id = polres.polda_id
						JOIN accidents ON accidents.polres_id = polres.id
						WHERE
						CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
						AND
						CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
						AND
						accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
						AND polda.state <> 0
						AND polres.state <> 0
					)AS total_laka,
                    (
                        SELECT sum(md) AS total
                        FROM
                        polda
                        JOIN polres ON polda.id = polres.polda_id
                        JOIN accidents ON accidents.polres_id = polres.id
                        WHERE
                        CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                        AND
                        CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                        AND
                        accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                        AND polda.state <> 0
                        AND polres.state <> 0
                    )AS total_md,
                    (
                        SELECT sum(lb) AS total
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON accidents.polres_id = polres.id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                    )AS total_lb,
                    (
                        SELECT sum(lr) AS total
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON accidents.polres_id = polres.id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                    )AS total_lr

				    )w
			    )AS summary,
                (
                    SELECT array_to_json(array_agg(row_to_json(w)))
                    FROM(
                        SELECT
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0101'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS p21,
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0102'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS sp3,
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0103'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS diversi,
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0104'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS pom_tni,
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0106'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS adat_rj,
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0108'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS sp2lid,
                        (
                            SELECT count(accidents.id)
                            FROM
                            polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND accidents.selra_flag = 'S0107'
                            AND accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        )AS dalam_proses
                    )w
                )AS summary_selra,
                (
                    SELECT array_to_json(array_agg(row_to_json(w)))
                    FROM(
                        SELECT
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0101'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_p21_week_1,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0102'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_sp3_week_1,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0103'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_diversi_week_1,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0104'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_pom_tni_week_1,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0106'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_adat_rj_week_1,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0108'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_sp2lid_week_1,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN '$date' AND date '$date' + INTERVAL '6 days'
                            AND accidents.selra_flag = 'S0107'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_dalam_proses_week_1,
                        -------------------------------
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0101'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_p21_week_2,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0102'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_sp3_week_2,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0103'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_diversi_week_2,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0104'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_pom_tni_week_2,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0106'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_adat_rj_week_2,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0108'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_sp2lid_week_2,
                        (
                            SELECT coalesce(count(accidents.id),0) AS total
                            FROM polda
                            JOIN polres ON polda.id = polres.polda_id
                            JOIN accidents ON polres.id = accidents.polres_id
                            WHERE
                            CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                            AND
                            CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                            AND
                            accidents.accident_date BETWEEN date '$date' - INTERVAL '1 weeks' AND date '$date' - INTERVAL '1 days'
                            AND accidents.selra_flag = 'S0107'
                            AND polda.state <> 0
                            AND polres.state <> 0
                        ) AS total_dalam_proses_week_2
                    )w
                )AS fatalitas_selra
            )t
        )as data");
        $query = DB::select($expression->getValue(DB::connection()->getQueryGrammar()));
        $data = json_decode($query[0]->data);

        $summary = [
            'total_laka'      => 0,
            'md'              => 0,
            'lb'              => 0,
            'lr'              => 0,
        ];
        if(!empty($data->summary)) {
            foreach ($data->summary as $dt) {

                $summary['total_laka']   = $summary['total_laka'] + $dt->total_laka;
                $summary['md']   = $summary['md'] + $dt->total_md;
                $summary['lb']   = $summary['lb'] + $dt->total_lb;
                $summary['lr']   = $summary['lr'] + $dt->total_lr;
                // $md = $dt->total_md;
                // $lb = $dt->total_lb;
                // $lr = $dt->total_lr;
            }
        }

        $summary_selra = [
            'p21'               => 0,
            'sp3'               => 0,
            'diversi'           => 0,
            'pom_tni'           => 0,
            'adat_rj'           => 0,
            'sp2lid'            => 0,
            'dalam_proses'      => 0,
        ];
        if(!empty($data->summary_selra)) {
            foreach ($data->summary_selra as $dt) {

                $summary_selra['p21']   = $summary_selra['p21'] + $dt->p21;
                $summary_selra['sp3']   = $summary_selra['sp3'] + $dt->sp3;
                $summary_selra['diversi']   = $summary_selra['diversi'] + $dt->diversi;
                $summary_selra['pom_tni']   = $summary_selra['pom_tni'] + $dt->pom_tni;
                $summary_selra['adat_rj']   = $summary_selra['adat_rj'] + $dt->adat_rj;
                $summary_selra['sp2lid']   = $summary_selra['sp2lid'] + $dt->sp2lid;
                $summary_selra['dalam_proses']   = $summary_selra['dalam_proses'] + $dt->dalam_proses;
            }
        }

        $facilityselra = [];
        $facilityselra['labels']=[];
        $facilityselra['value_p21']    = [];
        $facilityselra['value_sp3'] =  [];
        $facilityselra['value_diversi'] =  [];
        $facilityselra['value_pom_tni'] =  [];
        $facilityselra['value_adat_rj'] =  [];
        $facilityselra['value_sp2lid'] =  [];
        $facilityselra['value_dalam_proses'] =  [];
        $dateFrom = Carbon::parse($date)->subDays(7);

        for ($i=0; $i < 2; $i++) {
            $dates = $dateFrom->format('d/m');
            $newDate = $dateFrom->addDays(6);
            $newDates = $newDate->format('d/m');
            $facilityselra['labels'][] = $dates.'-'.$newDates;
            $dateFrom = $newDate->addDays(1);
        }


        if(!empty($data->fatalitas_selra)) {
            $facility = $data->fatalitas_selra[0];

            $facilityselra['value_p21'] = [
                $facility->total_p21_week_2,
                $facility->total_p21_week_1,
            ];

            $facilityselra['value_sp3'] = [
                $facility->total_sp3_week_2,
                $facility->total_sp3_week_1,
            ];

            $facilityselra['value_diversi'] = [
                $facility->total_diversi_week_2,
                $facility->total_diversi_week_1,
            ];

            $facilityselra['value_pom_tni'] = [
                $facility->total_pom_tni_week_2,
                $facility->total_pom_tni_week_1,
            ];

            $facilityselra['value_adat_rj'] = [
                $facility->total_adat_rj_week_2,
                $facility->total_adat_rj_week_1,
            ];

            $facilityselra['value_sp2lid'] = [
                $facility->total_sp2lid_week_2,
                $facility->total_sp2lid_week_1,
            ];

            $facilityselra['value_dalam_proses'] = [
                $facility->total_dalam_proses_week_2,
                $facility->total_dalam_proses_week_1,
            ];
        }

        $chartSelra = [
            'labels' => $facilityselra['labels'],
            'datasets' => [
                [
                    'label' => 'P21',
                    'data'  => $facilityselra['value_p21']
                ],
                [
                    'label' => 'SP3',
                    'data'  => $facilityselra['value_sp3']
                ],
                [
                    'label' => 'DIVERSI',
                    'data'  => $facilityselra['value_diversi']
                ],
                [
                    'label' => 'POM/TNI',
                    'data'  => $facilityselra['value_pom_tni']
                ],
                [
                    'label' => 'ADAT/RJ',
                    'data'  => $facilityselra['value_adat_rj']
                ],
                [
                    'label' => 'SP2LID',
                    'data'  => $facilityselra['value_sp2lid']
                ],
                [
                    'label' => 'DALAM PROSES',
                    'data'  => $facilityselra['value_dalam_proses']
                ],
            ]
        ];

        $data = [
            'poldaName'  => $data->polda?: 'Semua Polda',
            'polresName' => $data->polres,
            'datetitle'   => date('d F Y', strtotime(substr($data->range_date, 0, 10))).' - '.date('d F Y', strtotime(substr($data->range_date, 13, 23))),
            // 'subtitle'   => date('d F Y', strtotime(substr($data->range_date, 0, 10))),
            // 'subtitle2'   => $data->range_date,
            'summary'         => $summary,
            'summary_selra'   => $summary_selra,
            'date'            => Carbon::parse($request->input('hari'))->format('d-m-Y'),
            'cart' => [
                'chartSelra' => $chartSelra,
            ]
        ];
        // dd($data);
        return response()->json(['data'=> $data]);


        // $week=Carbon::parse($request->input('week'))->format('Y-m-d');
        // $weekPast=Carbon::parse($request->input('week'))->addDays(-7)->format('Y-m-d');
        // $checkWeek = Carbon::parse($request->input('week'))->addDays(7)->format('Y-m-d');//it's for check between date what you input or check 7 days from feature in date in form
        // $polres = $request->input('polres_id');
        // $status = $request->input('selra_id');
        // $countdata = DB::table('accidents')
        //                 ->where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$week, $checkWeek])
        //                 ->count();
        // $md = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$week, $checkWeek])
        //                 ->sum('md');

        // $lb = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$week, $checkWeek])
        //                 ->sum('lb');

        // $lr = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$week, $checkWeek])
        //                 ->sum('lr');

        // $mdPast = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$weekPast, $week])
        //                 ->sum('md');

        // $lbPast = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$weekPast, $week])
        //                 ->sum('lb');

        // $lrPast = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->whereBetween('created_at', [$weekPast, $week])
        //                 ->sum('lr');


        // return response()->json([$countdata, $md, $lb, $lr, $mdPast, $lbPast, $lrPast, $week, $checkWeek, $weekPast]);
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
        $hari=Carbon::parse($request->input('hari'))->format('Y-m-d');
        $polda  = $request->input('polda_id');
        $polres = $request->input('polres_id');
        if($polres == null){
            $polres = '-';
        }else{
            $polres = $polres;
        }
        // $status = $request->input('selra_id');

        $expression = DB::raw("select(SELECT row_to_json(t)
        FROM(
            SELECT
                (SELECT CASE WHEN '$polda'<> '-' THEN name ELSE 'All Polda' END FROM polda WHERE id = '$polda') AS polda,
                (SELECT CASE WHEN '$polres' <> '-' THEN name ELSE 'All Polres' END FROM polres WHERE id = '$polres') AS polres,
                now() AS tanggal_dan_jam_pencarian,
                (
                    SELECT array_to_json(array_agg(row_to_json(w)))
                        FROM(
                            SELECT
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END

                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS count_data,
                            (
                                SELECT sum(accidents.md)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END

                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS md,
                            (
                                SELECT sum(accidents.lb)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END

                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS lb,
                            (
                                SELECT sum(accidents.lr)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END

                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS lr
                        )w
                    )AS summary,
                    (
                        SELECT array_to_json(array_agg(row_to_json(w)))
                        FROM(
                            SELECT
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0101'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS p21,
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0102'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS sp3,
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0103'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS diversi,
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0104'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS pom_tni,
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0106'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS adat_rj,
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0108'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS sp2lid,
                            (
                                SELECT count(accidents.id)
                                FROM
                                polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda'<> '-' THEN polda.id = '$polda'ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND accidents.selra_flag = 'S0107'
                                AND date(accidents.accident_date) = '$hari'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            )AS dalam_proses
                        )w
                    )AS summary_selra,
                    (
                        SELECT array_to_json(array_agg(row_to_json(w)))
                        FROM(
                            SELECT
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) = '$hari'
                                AND accidents.selra_flag = 'S0101'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_p21_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'
                                AND accidents.selra_flag = 'S0102'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_sp3_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'
                                AND accidents.selra_flag = 'S0103'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_diversi_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'
                                AND accidents.selra_flag = 'S0104'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_pom_tni_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'
                                AND accidents.selra_flag = 'S0106'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_adat_rj_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'
                                AND accidents.selra_flag = 'S0108'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_sp2lid_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'
                                AND accidents.selra_flag = 'S0107'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_dalam_proses_hari_1,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0101'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_p21_hari_2,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0102'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_sp3_hari_2,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0103'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_diversi_hari_2,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0104'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_pom_tni_hari_2,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0106'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_adat_rj_hari_2,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0108'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_sp2lid_hari_2,
                            (
                                SELECT coalesce(count(accidents.id),0) AS total
                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                JOIN accidents ON polres.id = accidents.polres_id
                                WHERE
                                CASE WHEN '$polda' <> '-' THEN polda.id = '$polda' ELSE TRUE END
                                AND
                                CASE WHEN '$polres' <> '-' THEN polres.id = '$polres' ELSE TRUE END
                                AND
                                date(accidents.accident_date) =  '$hari'::timestamp - INTERVAL '1 day'
                                AND accidents.selra_flag = 'S0107'
                                AND polda.state <> 0
                                AND polres.state <> 0
                            ) AS total_dalam_proses_hari_2
                        )w
                    )AS fatalitas_selra
                )t) as data");

        $query = DB::select($expression->getValue(DB::connection()->getQueryGrammar()));

        $data = json_decode($query[0]->data);
        $summary = [
            'count_data'      => 0,
            'md'              => 0,
            'lb'              => 0,
            'lr'              => 0,
        ];
        if(!empty($data->summary)) {
            foreach ($data->summary as $dt) {

                $summary['count_data']   = $summary['count_data'] + $dt->count_data;
                $summary['md']   = $summary['md'] + $dt->md;
                $summary['lb']   = $summary['lb'] + $dt->lb;
                $summary['lr']   = $summary['lr'] + $dt->lr;
                // $md = $dt->total_md;
                // $lb = $dt->total_lb;
                // $lr = $dt->total_lr;
            }
        }

        $summary_selra = [
            'p21'               => 0,
            'sp3'               => 0,
            'diversi'           => 0,
            'pom_tni'           => 0,
            'adat_rj'           => 0,
            'sp2lid'            => 0,
            'dalam_proses'      => 0,
        ];
        if(!empty($data->summary_selra)) {
            foreach ($data->summary_selra as $dt) {

                $summary_selra['p21']   = $summary_selra['p21'] + $dt->p21;
                $summary_selra['sp3']   = $summary_selra['sp3'] + $dt->sp3;
                $summary_selra['diversi']   = $summary_selra['diversi'] + $dt->diversi;
                $summary_selra['pom_tni']   = $summary_selra['pom_tni'] + $dt->pom_tni;
                $summary_selra['adat_rj']   = $summary_selra['adat_rj'] + $dt->adat_rj;
                $summary_selra['sp2lid']   = $summary_selra['sp2lid'] + $dt->sp2lid;
                $summary_selra['dalam_proses']   = $summary_selra['dalam_proses'] + $dt->dalam_proses;
            }
        }


        $facilityselra = [];
        $facilityselra['labels']      = [];
        $facilityselra['value_p21']    = [];
        $facilityselra['value_sp3'] =  [];
        $facilityselra['value_diversi'] =  [];
        $facilityselra['value_pom_tni'] =  [];
        $facilityselra['value_adat_rj'] =  [];
        $facilityselra['value_sp2lid'] =  [];
        $facilityselra['value_dalam_proses'] =  [];
        $dateFrom = Carbon::parse($hari)->subDays(1);

        for ($i=0; $i < 2; $i++) {
            $dates = $dateFrom->format('d/m/y');
            $newDate = $dateFrom->addDays(1);
            $facilityselra['labels'][] = $dates;
            $dateFrom = $newDate;
        }


        if(!empty($data->fatalitas_selra)) {
            $facility = $data->fatalitas_selra[0];

            $facilityselra['value_p21'] = [
                $facility->total_p21_hari_2,
                $facility->total_p21_hari_1,
            ];

            $facilityselra['value_sp3'] = [
                $facility->total_sp3_hari_2,
                $facility->total_sp3_hari_1,
            ];

            $facilityselra['value_diversi'] = [
                $facility->total_diversi_hari_2,
                $facility->total_diversi_hari_1,
            ];

            $facilityselra['value_pom_tni'] = [
                $facility->total_pom_tni_hari_2,
                $facility->total_pom_tni_hari_1,
            ];

            $facilityselra['value_adat_rj'] = [
                $facility->total_adat_rj_hari_2,
                $facility->total_adat_rj_hari_1,
            ];

            $facilityselra['value_sp2lid'] = [
                $facility->total_sp2lid_hari_2,
                $facility->total_sp2lid_hari_1,
            ];

            $facilityselra['value_dalam_proses'] = [
                $facility->total_dalam_proses_hari_2,
                $facility->total_dalam_proses_hari_1,
            ];
        }

        $chartSelra = [
            'labels' => $facilityselra['labels'],
            'datasets' => [
                [
                    'label' => 'P21',
                    'data'  => $facilityselra['value_p21']
                ],
                [
                    'label' => 'SP3',
                    'data'  => $facilityselra['value_sp3']
                ],
                [
                    'label' => 'DIVERSI',
                    'data'  => $facilityselra['value_diversi']
                ],
                [
                    'label' => 'POM/TNI',
                    'data'  => $facilityselra['value_pom_tni']
                ],
                [
                    'label' => 'ADAT/RJ',
                    'data'  => $facilityselra['value_adat_rj']
                ],
                [
                    'label' => 'SP2LID',
                    'data'  => $facilityselra['value_sp2lid']
                ],
                [
                    'label' => 'DALAM PROSES',
                    'data'  => $facilityselra['value_dalam_proses']
                ],
            ]
        ];



        $data = [
            'poldaName'  => $data->polda?: 'Semua Polda',
            'polresName' => $data->polres,
            'datetitle'  => Carbon::parse($request->input('hari'))->format('d-m-Y'),
            'summary'         => $summary,
            'summary_selra'   => $summary_selra,
            'cart' => [
                'chartSelra' => $chartSelra,
            ]
        ];
        // $data['md']=$md;
        // $data['lb']=$lb;
        // $data['lr']=$lr;
        // $countdata = DB::table('accidents')
        //                 ->where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->where('created_at', '=', $hari)
        //                 ->count();
        // $md = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->where('created_at', '=', $hari)
        //                 ->sum('md');

        // $lb = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->where('created_at', '=', $hari)
        //                 ->sum('lb');

        // $lr = Accident::where('selra_flag', '=', $status)
        //                 ->where('polres_id','=', $polres)
        //                 ->where('created_at', '=', $hari)
        //                 ->sum('lr');



        // return response()->json([$countdata, $md, $lb, $lr, $hari]);
        return response()->json(['data'=>$data]);
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
