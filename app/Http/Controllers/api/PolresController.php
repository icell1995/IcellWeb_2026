<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accident;
use App\Http\Resources\Accident as AccidentResource;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PolresController extends BaseController
{
    public function getPolres(Request $requset){
        $polda=$requset->polda_id;
        $user = Auth::user();
        switch($user->role_id){
            case 2:
                $getPolres = DB::select('select id
                                        ,name
                                        from polres
                                        where polda_id = \''.$polda.'\' 
					and polres.state = 1 ');
            break;
        
            case 3:
                $polres=$user->polres_id;
                $getPolres = DB::select('select polres.id as id
                                        ,polres.name as name
                                        from polda
                                        left join polres on polda.id = polres.polda_id
                                        where polres.id = \''.$polres.'\' ');
            break;
            default:
                $getPolres = DB::select('select id
                                        ,name
                                        from polres
                                        where polda_id = \''.$polda.'\'
					and polres.state = 1
                                           ');
        }
       
        // $accident = DB::select('select * from accidents');
        // dd($accident);
        // return $this->sendResponse(AccidentResource::collection($accident), 'Products retrieved successfully.');
        return $this->sendResponse($getPolres, 'Products retrieved successfully.');

    }
}
