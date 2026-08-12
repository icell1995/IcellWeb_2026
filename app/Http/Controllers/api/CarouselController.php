<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;

class CarouselController extends BaseController
{
    public function get_image_carousel(){
        $image = DB::select('
        select  id,name_image as image from image_carousel order by created_at desc
        ');

        return $this->sendResponse($image, 'Products retrieved successfully.');
    }
    //
}
