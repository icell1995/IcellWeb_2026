<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\Polda;
use App\Models\Polres;
use Carbon\Carbon;

class DpoController extends Controller
{
    //

    public function index_dpo(Request $request)
    {    
        $user = Auth::getUser();
        $checkuser=$user->role_id;

        switch($checkuser){
            case 2 :
                $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'to_char(accident_date,yyyy-mm-dd)', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('polda','polda_id','=','polres.polda_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('polda.id','=',$user->polda_id)
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpo = DB::select('select * from dpo left join accidents on accidents.id = dpo.accident_id left join polres on accidents.polres_id = polres.id left join polda on polda.id = polres.polda_id
                where polda.id = \''.$user->polda_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3 :
                $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('polres.id','=',$user->polres_id)
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpo = DB::select('select * from dpo left join accidents on accidents.id = dpo.accident_id left join polres on accidents.polres_id = polres.id
                where polres.id = \''.$user->polres_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            case 4 :
                $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('polres.id','=',$user->polres_id)
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpo = DB::select('select * from dpo left join accidents on accidents.id = dpo.accident_id left join polres on accidents.polres_id = polres.id
                where polres.id = \''.$user->polres_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;

            default:
            $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
            $dpo = DB::select('select * from dpo 
            left join accidents on accidents.id = dpo.accident_id 
            ');
            $polda=Polda::all();
            $polres= Polres::all();
            break;
        }
        $data['accident']=$accident;
        $data['dpo']=$dpo;
        $data['polda']=$polda;
        $data['polres']=$polres;
        // dd($data);  

        return view('dpo.index-dpo', $data);
    }

    public function list_dpo(Request $request)
    {
        $id = $request->id;
        $dpo= DB::select('select dpo.name as name, ref.name as gender , dpo.state as state from dpo left join ref on ref.id = dpo.gender where accident_id = \''.$id.'\' and dpo.state = \'0\' ');
        $data=[
            'dpo'=>$dpo,
        ];
        return response()->json($data);
    }

    public function search_dpo(Request $request)
    {    
        $user = Auth::getUser();
        $checkuser=$user->role_id;
        $search = $request->input('search');
        $polres_input = $request->input('polres');
        $polda_input = $request->input('polda');
        $status = $request->status;
        if($status == null){
            $status = 99;
        }

        $check_accident_date=$request->input('accident_date');
        $accident_date=Carbon::parse($request->accident_date)->format('Y-m-d');
        if($check_accident_date == null){
            $check_accident_date = '-';
        }
        switch($checkuser){
            case 2 :
                if($polda_input==null){
                    $polda_input= $user->polda_id;
                }else if($polda_input != $user->polda_id){
                    $polda_input = $user->polda_id;
                }
                
                if($polres_input==null){
                    $polres_input='-';
                }
                $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('polda','polda_id','=','polres.polda_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->whereRaw("case
                                    when '$polda_input' <> '-' then polda_id = '$polda_input' else true 
                                    end
                                    and
                                    case 
                                        when '$polres_input' <> '-' then polres_id = '$polres_input' else true 
                                    end
                                    and
                                    case 
                                        when '$search' <> '-' then accidents.no_lp ilike '%$search%' else true
                                    end
                                    and
                                    case 
                                        when '$check_accident_date' <> '-' then accidents.accident_date =  \''.$accident_date.'\' else true
                                    end")
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpo = DB::select('select * from dpo left join accidents on accidents.id = dpo.accident_id left join polres on accidents.polres_id = polres.id left join polda on polda.id = polres.polda_id
                where polda.id = \''.$user->polda_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
            break;
            case 3 :
                if($polda_input==null){
                    $polda_input= $user->polda_id;
                }else if($polda_input != $user->polda_id){
                    $polda_input = $user->polda_id;
                }
                
                if($polres_input==null){
                    $polres_input= $user->polres_id;
                }else if($polres_input != $user->polres_id){
                    $polres_input = $user->polres_id;
                }
                $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->whereRaw("case
                                            when '$polda_input' <> '-' then polda_id = '$polda_input' else true 
                                        end
                                        and
                                        case 
                                            when '$polres_input' <> '-' then polres_id = '$polres_input' else true 
                                        end
                                        and
                                        case 
                                            when '$search' <> '-' then accidents.no_lp ilike '%$search%' else true
                                        end
                                        and
                                        case 
                                            when '$check_accident_date' <> '-' then accidents.accident_date =  \''.$accident_date.'\' else true
                                        end")
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpo = DB::select('select * from dpo left join accidents on accidents.id = dpo.accident_id left join polres on accidents.polres_id = polres.id
                where polres.id = \''.$user->polres_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
            case 4 :
                if($polda_input==null){
                    $polda_input= $user->polda_id;
                }else if($polda_input != $user->polda_id){
                    $polda_input = $user->polda_id;
                }
                
                if($polres_input==null){
                    $polres_input= $user->polres_id;
                }else if($polres_input != $user->polres_id){
                    $polres_input = $user->polres_id;
                }
                $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->whereRaw("case
                                            when '$polda_input' <> '-' then polda_id = '$polda_input' else true 
                                        end
                                        and
                                        case 
                                            when '$polres_input' <> '-' then polres_id = '$polres_input' else true 
                                        end
                                        and
                                        case 
                                            when '$search' <> '-' then accidents.no_lp ilike '%$search%' else true
                                        end
                                        and
                                        case 
                                            when '$check_accident_date' <> '-' then accidents.accident_date =  \''.$accident_date.'\' else true
                                        end")
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpo = DB::select('select * from dpo left join accidents on accidents.id = dpo.accident_id left join polres on accidents.polres_id = polres.id
                where polres.id = \''.$user->polres_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;

            default:
            if($polda_input==null){
                $polda_input='-';
            }
            
            if($polres_input==null){
                $polres_input='-';
            }
            $accident = DB::table('dpo')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpo.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->whereRaw("case
                                            when '$polda_input' <> '-' then polda_id = '$polda_input' else true 
                                        end
                                        and
                                        case 
                                            when '$polres_input' <> '-' then polres_id = '$polres_input' else true 
                                        end
                                        and
                                        case 
                                            when '$search' <> '-' then accidents.no_lp ilike '%$search%' else true
                                        end
                                        and
                                        case 
                                            when '$check_accident_date' <> '-' then accidents.accident_date = '$accident_date' else true 
                                        end")
                            ->where('dpo.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
            $dpo = DB::select('select * from dpo 
            left join accidents on accidents.id = dpo.accident_id 
            ');
            $polda=Polda::all();
            $polres= Polres::all();
            break;
        }
        $data['accident']=$accident;
        $data['dpo']=$dpo;
        $data['polda']=$polda;
        $data['polres']=$polres;
        return view('dpo.index-dpo', $data);
    }
}
