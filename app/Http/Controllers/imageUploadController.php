<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\uploadImage;
use Illuminate\Support\Facades\Storage;

class imageUploadController extends Controller
{
    public function imageUpload(Request $request)
    {
        $accident=$request->accident_id;
        // dd($request->all());
        $check = $request->validate([
            'files' => 'required',
            'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        if($request->hasfile('files')){
            foreach($request->file('files') as $key => $file){
                $fileName = time(). '.' .$file->getClientOriginalName();
                $path = $file->move(public_path('imageUpload'), $fileName);
                // $path= Storage::disk('public')->url($fileName, 'imageUpload');
                // dd($path);
                uploadImage::create(['accident_id'=>$accident,'name'=>$fileName, 'image'=>$path, 'category' => 'D010905']);
            }
        }

        return back()->with('success','You have successfully upload image.');
    }
}
