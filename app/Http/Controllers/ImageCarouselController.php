<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageCarousel;;
use Illuminate\Support\Facades\Storage;
use DB;


class ImageCarouselController extends Controller
{
    public function index(){
        $date= ImageCarousel::paginate(6);
        return view('carousel.carousel-index')->with('date',$date);
    }

    public function add_image(){
        return view('carousel.carousel-add');
    }

    public function save_image(Request $request){
       
        $title = $request->title;
        $description = $request->description;
        $url = $request->url;
        $check = $request->validate([
            'name_image' => 'required',
            'name_image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $fileName = time(). '.' .$request->file('name_image')->getClientOriginalName();
        $path = $request->file('name_image')->move(public_path('caraousel'), $fileName);
        $check = ImageCarousel::create(['title'=>$title, 'name_image'=>$fileName, 'description'=>$description, 'url'=>$url]);
        // dd($check);
        return redirect('caraousel/caraousel')->with('success', 'data sudah masuk dalam database');
    }

    public function deleteImage(Request $request){
        $name = $request->name;
        // $name = ImageCarousel::find($id);
        $path = public_path(). '/caraousel/'. $name;
        unlink($path);
        ImageCarousel::where('name_image', '=', $name)->delete();
        
        return back();
    }
}
