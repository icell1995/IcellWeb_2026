<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\AccidentDetail;

class AccidentDetailController extends Controller
{
    //
    public function fileUploadPost(Request $request)
    {
        
        // dd($request->all);
        $request->validate([
            'file' => 'required|file|mimes:pdf',
        ]);

        $fileName = time().'.'.$request->file->extension();  
        // dd($fileName);
     
        $request->file->move(public_path('file'), $fileName);
  
        // /* Store $fileName name in DATABASE from HERE */
        AccidentDetail::create([
        'accident_id'=>$request->accident_id,
        'name' => $fileName,
        'category_id'=>'SP0101',
        'state'=>'1']);   
        return back();
    }
}
