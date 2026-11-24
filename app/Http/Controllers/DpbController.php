<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\Polda;
use App\Models\Polres;
use Carbon\Carbon;

class DpbController extends Controller
{
    public function index_dpb(Request $request)
    {    
        $user = Auth::getUser();
        $checkuser=$user->role_id;

        switch($checkuser){
            case 2 :
                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('polda','polda_id','=','polres.polda_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('polda.id','=',$user->polda_id)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3 :
            case 4 :
                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('polres.id','=',$user->polres_id)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;

            default:
                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $polda=Polda::all();
                $polres= Polres::all();
            break;
        }
        $data['accident']=$accident;
        $data['dpb']=[];
        $data['polda']=$polda;
        $data['polres']=$polres;

        return view('dpb.index-dpb', $data);
    }

    public function list_dpb(Request $request)
    {
        $id = $request->id;
        $dpb = DB::table('dpb')->where('accident_id', (string) $id)->get();
        $data=[
            'dpb'=>$dpb,
        ];
        return response()->json($data);
    }

    public function search_dpb(Request $request)
    {    
        $user = Auth::getUser();
        $checkuser=$user->role_id;
        $search = $request->input('search');
        $polres_input = $request->input('polres');
        $polda_input = $request->input('polda');
        $status = $request->status ?? 99;
        $check_accident_date = $request->input('accident_date');
        $accident_date = !empty($check_accident_date) && $check_accident_date !== '-'
            ? Carbon::parse($check_accident_date)->format('Y-m-d')
            : null;

        switch($checkuser){
            case 2 :
                $polda_input = $user->polda_id;
                $polres_input = $polres_input ?: '-';
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3 :
            case 4 :
                $polda_input = $user->polda_id;
                $polres_input = $user->polres_id;
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            default:
                $polda_input = $polda_input ?: '-';
                $polres_input = $polres_input ?: '-';
                $polda=Polda::all();
                $polres= Polres::all();
            break;
        }

        $accident = DB::table('dpb')
            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 'ref.name as selra')
            ->leftJoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
            ->leftJoin('polres', 'polres.id', '=', 'accidents.polres_id')
            ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
            ->leftJoin('ref', 'ref.id', '=', 'accidents.selra_flag')
            ->when($polda_input !== '-', fn ($q) => $q->where('polres.polda_id', $polda_input))
            ->when($polres_input !== '-', fn ($q) => $q->where('accidents.polres_id', $polres_input))
            ->when(!empty($search) && $search !== '-', fn ($q) => $q->where('accidents.no_lp', 'ILIKE', '%' . $search . '%'))
            ->when($accident_date !== null, fn ($q) => $q->where('accidents.accident_date', $accident_date))
            ->where('dpb.state', '=', 0)
            ->groupBy('accidents.id', 'polres.id', 'ref.id')
            ->paginate(10);

        $data['accident']=$accident;
        $data['polda']=$polda;
        $data['polres']=$polres;
        return view('dpb.index-dpb', $data);
    }
}
