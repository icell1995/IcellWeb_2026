<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accident;
use App\Http\Resources\Accident as AccidentResource;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PoldaController extends BaseController
{
    //
    public function getPolda(){
        $user = Auth::user();
        $a = ' ';
        switch($user->role_id){
            case 2:
                $polda = $user->polda_id;
                $polres = '-';
                $getPolda = DB::select('select id
                                        ,name
                                        from polda
                                        where id = \''.$polda.'\' ');
            break;
        
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;
                $getPolda = DB::select('select polda.id as id
                                        ,polda.name as name
                                        from polda
                                        left join polres on polda.id = polres.polda_id
                                        where polres.id = \''.$polres.'\' ');
            break;
            default:
                $getPolda = DB::select('select id
                                        ,name
                                        from polda 
                                           ');
        }
       
        // $accident = DB::select('select * from accidents');
        // dd($accident);
        // return $this->sendResponse(AccidentResource::collection($accident), 'Products retrieved successfully.');
        return $this->sendResponse($getPolda, 'Products retrieved successfully.');

    }
}
