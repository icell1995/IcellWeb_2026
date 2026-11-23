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
        $user = Auth::getUser();

        $query = DB::table('polda')
            ->select(
                'polda.name as polda_name',
                'polres.name as polres_name',
                'polres.address as polres_alamat',
                'polres.state as state'
            )
            ->join('polres', 'polda.id', '=', 'polres.polda_id');

        if (in_array((int) $user->role_id, [2, 3, 4, 5], true)) {
            $query->where('polda.id', '=', $user->polda_id);
        }

        $officer = $query->paginate(10);

        return view('daftar_wilayah.daftar_wilayah_index', compact('officer'));
    }
}
