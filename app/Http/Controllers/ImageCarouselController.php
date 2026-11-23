<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageCarousel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $request->validate([
            'name_image' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $title = $request->title;
        $description = $request->description;
        $url = $request->url;
        $file = $request->file('name_image');
        $fileName = time() . '_' . Str::uuid()->toString() . '.' . $file->extension();
        $file->move(public_path('caraousel'), $fileName);
        ImageCarousel::create(['title'=>$title, 'name_image'=>$fileName, 'description'=>$description, 'url'=>$url]);
        return redirect('caraousel/caraousel')->with('success', 'data sudah masuk dalam database');
    }

    public function deleteImage(Request $request){
        $name = basename((string) $request->name);
        $path = public_path('caraousel/' . $name);
        if (is_file($path)) {
            @unlink($path);
        }
        ImageCarousel::where('name_image', '=', $name)->delete();
        
        return back();
    }
}
