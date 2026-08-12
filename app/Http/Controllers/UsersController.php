<?php

namespace App\Http\Controllers;

use App\Models\Polda;
use App\Models\Polres;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //
    public function index()
    {
        // Total keseluruhan anggota penyidik
        $totalAnggota = User::active()
            ->whereHasOfficerActive()
            ->count();

        // Data Polda + jumlah anggota (Polda + semua Polres di bawahnya)
        $poldas = Polda::whereNotIn('id', ['77', '90', '99', '80'])
            ->withCount(['users' => function ($query) {
                $query->active()->whereHasOfficerActive();
            }])
            ->orderBy('name')
            ->get();

        return view('anggota.index', compact('totalAnggota', 'poldas'));
    }

    public function getPolresByPolda(Request $request)
    {
        $request->validate([
            'polda_id' => 'required|exists:polda,id'
        ]);

        $polresList = Polres::where('polda_id', $request->polda_id)
            ->whereNotIn('state', ['0'])
            ->withCount(['users' => function ($query) {
                $query->active()->whereHasOfficerActive();
            }])
            ->orderBy('name')
            ->get();

        return response()->json($polresList);
    }
}
