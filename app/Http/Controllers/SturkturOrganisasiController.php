<?php

namespace App\Http\Controllers;

use Doctrine\DBAL\Schema\Index;
use Illuminate\Http\Request;
use App\Models\StrukturOrganisasi;
use Illuminate\Database\Console\DumpCommand;

class SturkturOrganisasiController extends Controller
{
    public function index()
    {
        $image = StrukturOrganisasi::all();
        return view('struktur_organisasi.struktur-organisasi')->with('image',$image);
    }

    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required',
            'filename.*'=> 'image|mimes:png,jpg,jpeg,svg'
        ]);

        $file = $request->file('filename')->getClientOriginalName();
        $request->file('filename')->move(public_path('struktur-organisasi'), $file);
        $check = StrukturOrganisasi::create(['filename'=>$file]);

        return redirect('organisasi/struktur-organisasi')->with('success', 'data sudah masuk dalam database');
    }

    public function delete_img(Request $request){
        $name = $request->name;
        // $name = ImageCarousel::find($id);
        $path = public_path(). '/struktur-organisasi/'. $name;
        unlink($path);
        StrukturOrganisasi::where('filename', '=', $name)->delete();

        return back();
    }
}
