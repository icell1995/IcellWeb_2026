<?php

namespace App\Http\Controllers;

use Doctrine\DBAL\Schema\Index;
use Illuminate\Http\Request;
use App\Models\StrukturOrganisasi;
use Illuminate\Database\Console\DumpCommand;
use Illuminate\Support\Str;

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
            'filename' => 'required|file|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $uploadedFile = $request->file('filename');
        $file = time() . '_' . Str::uuid()->toString() . '.' . $uploadedFile->extension();
        $uploadedFile->move(public_path('struktur-organisasi'), $file);
        StrukturOrganisasi::create(['filename'=>$file]);

        return redirect('organisasi/struktur-organisasi')->with('success', 'data sudah masuk dalam database');
    }

    public function delete_img(Request $request){
        $name = basename((string) $request->name);
        $path = public_path('struktur-organisasi/' . $name);
        if (is_file($path)) {
            @unlink($path);
        }
        StrukturOrganisasi::where('filename', '=', $name)->delete();

        return back();
    }
}
