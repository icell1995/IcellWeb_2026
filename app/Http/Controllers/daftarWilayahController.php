<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\Polda;
use App\Models\Polres;

class daftarWilayahController extends Controller
{
    public function index(){
        $user=Auth::getUser();
        // // $check = $user->role_id;
        // $officer = DB::table('polda')
        //               ->select('polda.name as polda_name', 'polres.name as polres_name')
        //               ->join('polres', 'polda.id', '=', 'polres.polda_id')
        //               ->paginate(10);

        
         // $rekap = DB::table('Accident')->get();

         $user = Auth::getUser();
         $allpolres = DB::table('polres')->get();
         $no_lp = '';
         $polres = '';
         $polda = '';
         $checkTanggal  = '';
         $checkstatus   = '';
         $checkuser=$user->role_id;
         switch ($user->role_id) {
            case 2:
                $officer = DB::table('polda')
                ->select('polda.name as polda_name', 'polres.name as polres_name', 'polres.address as polres_alamat','polres.state as state')
                ->join('polres', 'polda.id', '=', 'polres.polda_id')
                ->where('polda.id','=',$user->polda_id)
                ->paginate(10);
            break;
            case 3:
                $officer = DB::table('polda')
                ->select('polda.name as polda_name', 'polres.name as polres_name','polres.address as polres_alamat','polres.state as state')
                ->join('polres', 'polda.id', '=', 'polres.polda_id')
                ->where('polda.id','=',$user->polda_id)
                ->paginate(10);
            break;
            case 4:
                $officer = DB::table('polda')
                ->select('polda.name as polda_name', 'polres.name as polres_name','polres.address as polres_alamat','polres.state as state')
                ->join('polres', 'polda.id', '=', 'polres.polda_id')
                ->where('polda.id','=',$user->polda_id)
                ->paginate(10);
            break;
            case 5:
                $officer = DB::table('polda')
                ->select('polda.name as polda_name', 'polres.name as polres_name','polres.address as polres_alamat','polres.state as state')
                ->join('polres', 'polda.id', '=', 'polres.polda_id')
                ->where('polda.id','=',$user->polda_id)
                ->paginate(10);
            break;
            default:
                $officer = DB::table('polda')
                ->select('polda.name as polda_name', 'polres.name as polres_name','polres.address as polres_alamat','polres.state as state')
                ->join('polres', 'polda.id', '=', 'polres.polda_id')
                ->paginate(10);
            break;
         }
        return view('daftar_wilayah.daftar_wilayah_index',compact('officer'));
    }
}
