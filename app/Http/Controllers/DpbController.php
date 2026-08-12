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
            case 3 :
                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
                            ->leftjoin('polres', 'polres.id', '=', 'accidents.polres_id')
                            ->leftjoin('ref', 'ref.id', '=', 'accidents.selra_flag')
                            ->where('polres.id','=',$user->polres_id)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
                $dpb = DB::select('select * from dpb left join accidents on accidents.id = dpb.accident_id left join polres on accidents.polres_id = polres.id
                where polres.id = \''.$user->polres_id.'\'
                ');
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('id','=',$user->polres_id)->get();
            break;
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
                $dpb = DB::select('select * from dpb left join accidents on accidents.id = dpb.accident_id left join polres on accidents.polres_id = polres.id
                where polres.id = \''.$user->polres_id.'\'
                ');
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
            $dpb = DB::select('select * from dpb 
            left join accidents on accidents.id = dpb.accident_id 
            ');
            $polda=Polda::all();
            $polres= Polres::all();
            break;
        }
        $data['accident']=$accident;
        $data['dpb']=$dpb;
        $data['polda']=$polda;
        $data['polres']=$polres;
        // dd($data);  

        return view('dpb.index-dpb', $data);
    }

    public function list_dpb(Request $request)
    {
        $id = $request->id;
        $dpb= DB::select('select * from dpb where accident_id = \''.$id.'\' ');
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

                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
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
                            ->where('dpb.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
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
                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
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
                            ->where('dpb.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
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
                $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
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
                            ->where('dpb.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
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
        
            $accident = DB::table('dpb')
                            ->select('accidents.id as accident_id', 'accidents.no_lp' , 'accident_date', 'polres.name as polres_name', 
                            'ref.name as selra')
                            ->leftjoin('accidents', 'accidents.id', '=', 'dpb.accident_id')
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
                            ->where('dpb.state','=',0)
                            ->groupBy('accidents.id', 'polres.id','ref.id')
                            ->paginate(10);
            $polda=Polda::all();
            $polres= Polres::all();
            break;
        }
        $data['accident']=$accident;
        $data['polda']=$polda;
        $data['polres']=$polres;
        return view('dpb.index-dpb', $data);
    }
}
