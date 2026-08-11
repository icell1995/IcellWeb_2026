<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccidentRecent;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Image;
use Illuminate\Support\Facades\Storage;
use File;
use App\Models\Accident;
use App\Http\Resources\Accident as AccidentResource;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccidentRecentController extends BaseController
{
    public function postAccident(Request $request){
        $acc = new AccidentRecent;
        $acc->title = $request->title;
        $acc->description = $request->description;
        $acc->user_id = $request->user_id;
        $acc->polres_id = $request->polres_id;
        $acc->road_name = $request->road_name;
        $acc->accident_date = Carbon::parse($request->accident_date)->format('Y-m-d');
        $acc->latitude = $request->latitude;
        $acc->longtitude = $request->longtitude;
        
        $acc->state = '1';

        $image = $request->avatar;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::random(10).'.'.'png';
        File::put(public_path(). '/image-accident-recent/image/' . $imageName, base64_decode($image));
        $acc->photo = $imageName;
    
        //  if($request->hasFile('avatar')){
            
    	// 	$avatar = $request->file('avatar');
    	// 	$filename = time() . '.' . $avatar->getClientOriginalExtension();
    	// 	Image::make($avatar)->resize(300, 300)->save( public_path('/image-accident-recent/image/' . $filename ) );
    	// 	$acc->photo = $filename;
    	// 	$acc->save();
    	// }
        // $file = base64_decode($request->avatar);
        //     $folderName = '/image-accident-recent/image/';
        //     $safeName = Str::random(10).'.'.'jpg';
        //     $destinationPath = public_path() . $folderName;
        //     // Image::make($file)->resize(300, 300)->save( public_path($destinationPath.$safeName) );
        //     // public_path().$destinationPath.$safeName, $file);
        //     Storage::disk('public')->put($folderName.$safeName, $file);
        //     dd( Storage::disk('public')->put($folderName.$safeName, $file));
            $acc->save();
        return response()->json([
            'success' => true,
            'Tipe' => 'postData',
            "message" => "post data successfully"
            ], 200);

    }

    public function index_recent()

    {
        $user = Auth::user();
        $a = ' ';
        switch($user->role_id){
            case 2:
                $polda = $user->polda_id;
                $polres = '-';
                $accident = DB::select('select 
                                        accident_recent.id as id
                                        , title
                                        , concat_ws(\''.$a.'\', officer.first_name,officer.last_name) as name 
                                        , to_char(accident_recent.created_at, \'DD-MM-YYYY\') as created_at
                                        , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                                        , description
                                        , road_name
					, latitude
					, longtitude
                                        , polres.name as polres
                                        , photo
                                        , avatar
                                        from accident_recent
                                        left join polres on accident_recent.polres_id = polres.id
                                        left join polda on polres.polda_id = polda.id
                                        left join officer on accident_recent.user_id = officer.id
                                        left join users on accident_recent.user_id =  users.officer_id
                                        where polda.id = \''.$polda.'\' ');
            break;
        
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;
                $accident = DB::select('select 
                                            accident_recent.id as id
                                            , title
                                            , concat_ws(\''.$a.'\', officer.first_name,officer.last_name) as name 
                                            , to_char(accident_recent.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                                            , description
                                            , road_name
					    , latitude
					    , longtitude
                                            , polres.name as polres
                                            , photo
                                            , avatar
                                            from accident_recent
                                            left join polres on accident_recent.polres_id = polres.id
                                            left join officer on accident_recent.user_id = officer.id
                                            left join users on accident_recent.user_id = users.officer_id
                                            where polres.id = \''.$polres.'\' ');
            break;
            default:
                $accident = DB::select('select 
                accident_recent.id as id
                , title
                , concat_ws(\''.$a.'\', officer.first_name,officer.last_name) as name 
                , to_char(accident_recent.created_at, \'DD-MM-YYYY\') as created_at
                , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                , description
                , road_name
		, latitude
		, longtitude
                , polres.name as polres
                , photo
                , avatar
                from accident_recent
                left join polres on accident_recent.polres_id = polres.id
                left join officer on accident_recent.user_id = officer.id
                left join users on accident_recent.user_id = users.officer_id');
        }
       
        // $accident = DB::select('select * from accidents');
        // dd($accident);
        // return $this->sendResponse(AccidentResource::collection($accident), 'Products retrieved successfully.');
        return $this->sendResponse($accident, 'Products retrieved successfully.');

    }

}
