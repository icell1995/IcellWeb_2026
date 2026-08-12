<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
// use Illuminate\Pagination\Paginator;
// use Illuminate\Support\Collection;
// use Illuminate\Pagination\LengthAwarePaginator;
// use App\Models\User;
// use App\Models\Polres;

// use App\Models\SuratTugas;
// use App\Models\SuratTugasDetail;
// use App\Models\SuratTugasOfficerDetail;
// use Auth;
// use Http;
// use DB;
// use Validator;
use App\Models\Accident;
use App\Http\Resources\Accident as AccidentResource;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class AccidentController extends BaseController
{
    // public function index()

    // {
    //     $user = Auth::user();
    //     $a = ' ';
    //     switch($user->role_id){
    //         case 2:
    //             $polda = $user->polda_id;
    //             $polres = '-';
    //             $accident = DB::select('select 
    //                                         accidents.id as id
    //                                         , no_lp,concat_ws(\''.$a.'\'
    //                                         , officer_first_name,officer_last_name) as name
    //                                         , polres.name as polres_name
    //                                         , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
    //                                         , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
    //                                         , to_char(report_date, \'DD-MM-YYYY\') as report_date
    //                                         , road_name
    //                                         , damage_lose_desc as description
    //                                         , damage_lose_desc as tindak_lanjut
    //                                         , (select ref.name from ref where ref.id = selra_flag) as status
    //                                     from accidents
    //                                         left join polres on accidents.polres_id = polres.id
    //                                         left join polda on polres.polda_id = polda.id
    //                                     where polda.id = \''.$polda.'\' ');
    //         break;
        
    //         case 3:
    //             $polda = $user->polda_id;
    //             $polres = $user->polres_id;
    //             $accident = DB::select('select 
    //                                         accidents.id as id
    //                                         , no_lp,concat_ws(\''.$a.'\'
    //                                         , officer_first_name,officer_last_name) as name
    //                                         , polres.name as polres_name
    //                                         , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
    //                                         , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
    //                                         , to_char(report_date, \'DD-MM-YYYY\') as report_date
    //                                         , road_name
    //                                         , damage_lose_desc as description
    //                                         , damage_lose_desc as tindak_lanjut
    //                                         , (select ref.name from ref where ref.id = selra_flag) as status
    //                                     from accidents
    //                                         left join polres on accidents.polres_id = polres.id
    //                                     where polres.id = \''.$polres.'\' ');
    //         break;
    //         default:
    //             $accident = DB::select('select 
    //                                         accidents.id as id
    //                                         , no_lp,concat_ws(\''.$a.'\'
    //                                         , officer_first_name,officer_last_name) as name
    //                                         , polres.name as polres_name
    //                                         , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
    //                                         , to_char(accident_date, \'DD-Mon-YYYY\') as accident_date
    //                                         , to_char(report_date, \'DD-MM-YYYY\') as report_date
    //                                         , road_name
    //                                         , damage_lose_desc as description
    //                                         , damage_lose_desc as tindak_lanjut
    //                                         , (select ref.name from ref where ref.id = selra_flag) as status
    //                                     from accidents 
    //                                         left join polres on accidents.polres_id = polres.id');
    //     }
       
    //     // $accident = DB::select('select * from accidents');
    //     // dd($accident);
    //     // return $this->sendResponse(AccidentResource::collection($accident), 'Products retrieved successfully.');
    //     return $this->sendResponse($accident, 'Products retrieved successfully.');

    // }

    public function index(Request $request)

    {
        $user = Auth::user();
        $a = ' ';
        $b = '-';
        switch($user->role_id){
            case 2:
                $polda = $request->polda_id;
                $polres = $request->polres_id;

                // $accident = DB::select( DB::raw("select(SELECT row_to_json(t)
                // FROM(
                //     SELECT
                //         (
                //             SELECT array_to_json(array_agg(row_to_json(w)))
                //                 FROM(
                //                     SELECT
                //                     (
                //                         SELECT count(accidents.id)
                //                         FROM
                //                         polda
                //                         JOIN polres ON polda.id = polres.polda_id
                //                         JOIN accidents ON polres.id = accidents.polres_id
                //                         WHERE
                //                         polda.state <> 0
                //                         AND polres.state <> 0
                //                     )AS count_data
                //                 )w
                //             )AS summary
                //         )t) as data"));


                        
                // $accident = DB::select( DB::raw(
                // "SELECT *
                // FROM (
                //     SELECT 
                // 	accidents.id as id
                // 					, created_at
                // 					,   accident_date
                // 					,  report_date
                // 					, road_name
                // 					, damage_lose_desc as description
                // 					, damage_lose_desc as tindak_lanjut,  
                //         (              
                //             SELECT array_to_json(array_agg(row_to_json(nested_question)))
                // 		     			FROM (
                // 		     				SELECT
                // 		     				upload_image.id,
                // 		     				upload_image.name
                // 			     			FROM upload_image
                // 			     			where upload_image.accident_id = accidents.id
                // 		     			) AS nested_question
                //         ) AS photo
                //     FROM accidents
                // ) AS forms"
                // ));

                // $accident = DB::select( DB::raw("(SELECT row_to_json(t)
                // FROM( 
                //  SELECT
                //  (
                //   SELECT array_to_json(array_agg(row_to_json(w)))
                //   FROM(
                //       SELECT 
                //       accidents.id,no_lp ,
                //       (SELECT array_to_json(array_agg(row_to_json(s)))
                //           FROM(
                //               SELECT 
                //               accident_id,
                //               name
                //               FROM upload_image       
                //                 where upload_image.accident_id = accidents.id
                //       )s)as photo
                //       FROM accidents       
                //     WHERE accidents.state <> 0 
                //   )w
                //  )AS data
                //  )t)"));

                
                $accident = DB::select( DB::raw(" select(SELECT
                (
                 SELECT array_to_json(array_agg(row_to_json(w)))
                 FROM(
                     SELECT accidents.id
                     ,no_lp 
                     ,polres.name as polres_name
                     ,concat_ws(' ', officer_first_name,officer_last_name) as name
                     ,to_char(accidents.created_at, 'DD-MM-YYYY') as created_at 
                     ,to_char(accident_date, 'DD-MM-YYYY') as accident_date
                     ,to_char(report_date, 'DD-MM-YYYY') as report_date
                     , road_name
                     , damage_lose_desc as description
                     , damage_lose_desc as tindak_lanjut
                     , (select ref.name from ref where ref.id = selra_flag) as status
                     ,
                     (SELECT array_to_json(array_agg(row_to_json(s)))
                         FROM(
                             SELECT 
                             upload_image.id,
                             name
                             FROM upload_image       
                               where upload_image.accident_id = accidents.id
                               limit 1
                     )s)as photo
                     FROM 
                     accidents  
                     left join polres on accidents.polres_id = polres.id  
                     left join polda on polres.polda_id = polda.id   
                     where
                     CASE WHEN '$polda' <> '$b' THEN polda.id = '$polda' ELSE TRUE END
                      AND
                      CASE WHEN '$polres' <> '$b' THEN polres.id = '$polres' ELSE TRUE END
                 )w
                )AS data)"));

                

             $accident = json_decode($accident[0]->data);

            // $accident = DB::select('select 
            // accidents.id as id
            // , no_lp,concat_ws(\''.$a.'\'
            // , officer_first_name,officer_last_name) as name
            // , polres.name as polres_name
            // , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
            // , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
            // , to_char(report_date, \'DD-MM-YYYY\') as report_date
            // , road_name
            // , damage_lose_desc as description
            // , damage_lose_desc as tindak_lanjut
            // , (select ref.name from ref where ref.id = selra_flag) as status
            // ,          (SELECT string_to_array(
            //                   upload_image.id,
            //                   name
                              
            //           ) as a FROM upload_image       
            //           where upload_image.accident_id = accidents.id
            //            limit 1)as photo
            // from accidents
            // left join polres on accidents.polres_id = polres.id
            // left join polda on polres.polda_id = polda.id
            // where
            // CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
            // AND
            // CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END ');

                // $accident = DB::select('select 
                //                             accidents.id as id
                //                             , no_lp,concat_ws(\''.$a.'\'
                //                             , officer_first_name,officer_last_name) as name
                //                             , polres.name as polres_name
                //                             , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                //                             , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                //                             , to_char(report_date, \'DD-MM-YYYY\') as report_date
                //                             , road_name
                //                             , damage_lose_desc as description
                //                             , damage_lose_desc as tindak_lanjut
                //                             , (select ref.name from ref where ref.id = selra_flag) as status
                //                             , photo.name as photo
                //                             from accidents
                //                             left join polres on accidents.polres_id = polres.id
                //                             left join polda on polres.polda_id = polda.id
                //                             left join (select accidents.id as id, upload_image.name as name from upload_image left join accidents on accidents.id = upload_image.accident_id) as photo on accidents.id = photo.id
                //                             where
                //                             CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                //                             AND
                //                             CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END ');
            break;
        
            case 3:
                $polda = $request->polda_id;
                $polres = $request->polres_id;
                // $accident = DB::select('select 
                //                             accidents.id as id
                //                             , no_lp,concat_ws(\''.$a.'\'
                //                             , officer_first_name,officer_last_name) as name
                //                             , polres.name as polres_name
                //                             , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                //                             , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                //                             , to_char(report_date, \'DD-MM-YYYY\') as report_date
                //                             , road_name
                //                             , damage_lose_desc as description
                //                             , damage_lose_desc as tindak_lanjut
                //                             , (select ref.name from ref where ref.id = selra_flag) as status
                //                             from accidents
                //                             left join polres on accidents.polres_id = polres.id
                //                             left join polda on polres.polda_id = polda.id
                //                             where
                //                             CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                //                             AND
                //                             CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END ');
                $accident = DB::select( DB::raw(" select(SELECT
                (
                 SELECT array_to_json(array_agg(row_to_json(w)))
                 FROM(
                     SELECT 
                     accidents.id
                     ,no_lp 
                     ,polres.name as polres_name
                     ,concat_ws(' ', officer_first_name,officer_last_name) as name
                     ,to_char(accidents.created_at, 'DD-MM-YYYY') as created_at 
                     ,to_char(accident_date, 'DD-MM-YYYY') as accident_date
                     ,to_char(report_date, 'DD-MM-YYYY') as report_date
                     , road_name
                     , damage_lose_desc as description
                     , damage_lose_desc as tindak_lanjut
                     , (select ref.name from ref where ref.id = selra_flag) as status
                     ,
                     (SELECT array_to_json(array_agg(row_to_json(s)))
                         FROM(
                             SELECT 
                             upload_image.id,
                             name
                             FROM upload_image       
                               where upload_image.accident_id = accidents.id
                               limit 1
                     )s)as photo
                     FROM 
                     accidents  
                     left join polres on accidents.polres_id = polres.id  
                     left join polda on polres.polda_id = polda.id   
                     where
                     CASE WHEN '$polda' <> '$b' THEN polda.id = '$polda' ELSE TRUE END
                      AND
                      CASE WHEN '$polres' <> '$b' THEN polres.id = '$polres' ELSE TRUE END
                 )w
                )AS data)"));

                

             $accident = json_decode($accident[0]->data);
            break;
            default:
                $polda = $request->polda_id;
                $polres = $request->polres_id;
                $accident = DB::select( DB::raw(" select(SELECT
                (
                 SELECT array_to_json(array_agg(row_to_json(w)))
                 FROM(
                     SELECT 
                     accidents.id
                     ,no_lp 
                     ,polres.name as polres_name
                     ,concat_ws(' ', officer_first_name,officer_last_name) as name
                     ,to_char(accidents.created_at, 'DD-MM-YYYY') as created_at 
                     ,to_char(accident_date, 'DD-MM-YYYY') as accident_date
                     ,to_char(report_date, 'DD-MM-YYYY') as report_date
                     , road_name
                     , damage_lose_desc as description
                     , damage_lose_desc as tindak_lanjut
                     , (select ref.name from ref where ref.id = selra_flag) as status
                     ,
                     (SELECT array_to_json(array_agg(row_to_json(s)))
                         FROM(
                             SELECT 
                             upload_image.id,
                             name
                             FROM upload_image       
                               where upload_image.accident_id = accidents.id
                     )s)as photo
                     FROM 
                     accidents  
                     left join polres on accidents.polres_id = polres.id  
                     left join polda on polres.polda_id = polda.id   
                     where
                     CASE WHEN '$polda' <> '$b' THEN polda.id = '$polda' ELSE TRUE END
                      AND
                      CASE WHEN '$polres' <> '$b' THEN polres.id = '$polres' ELSE TRUE END
                 )w
                )AS data)"));

                

             $accident = json_decode($accident[0]->data);
            break;
                // $accident = DB::select('select 
                // accidents.id as id
                // , no_lp,concat_ws(\''.$a.'\'
                // , officer_first_name,officer_last_name) as name
                // , polres.name as polres_name
                // , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                // , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                // , to_char(report_date, \'DD-MM-YYYY\') as report_date
                // , road_name
                // , damage_lose_desc as description
                // , damage_lose_desc as tindak_lanjut
                // , (select ref.name from ref where ref.id = selra_flag) as status
                // from accidents
                // left join polres on accidents.polres_id = polres.id
                // left join polda on polres.polda_id = polda.id
                // where
                // CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                // AND
                // CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END ');
        }
       
        // $accident = DB::select('select * from accidents');
        // dd($accident);
        // return $this->sendResponse(AccidentResource::collection($accident), 'Products retrieved successfully.');
        return $this->sendResponse($accident, 'Products retrieved successfully.');

    }

    
    public function search(Request $request)
    {
        $no_lp = $request->input('no_lp');
        $user = Auth::user();
        $a = ' ';
        switch($user->role_id){
            case 2:
                $polda = $user->polda_id;
                $polres = '-';
                $accident = DB::select('select 
                                            accidents.id as id
                                            , no_lp,concat_ws(\''.$a.'\'
                                            , officer_first_name,officer_last_name) as name
                                            , polres.name as polres_name
                                            , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                                            , road_name
                                            , damage_lose_desc as description
                                            , damage_lose_desc as tindak_lanjut
                                            , (select ref.name from ref where ref.id = selra_flag) as status
                                        from accidents
                                            left join polres on accidents.polres_id = polres.id
                                            left join polda on polres.polda_id = polda.id
                                        where polda.id = \''.$polda.'\' 
                                        and accidents.no_lp ilike\'%'.$no_lp.'%\' ');
            break;
        
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;
                $accident = DB::select('select 
                                            accidents.id as id
                                            , no_lp,concat_ws(\''.$a.'\'
                                            , officer_first_name,officer_last_name) as name
                                            , polres.name as polres_name
                                            , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                                            , road_name
                                            , damage_lose_desc as description
                                            , damage_lose_desc as tindak_lanjut
                                            , (select ref.name from ref where ref.id = selra_flag) as status
                                        from accidents
                                            left join polres on accidents.polres_id = polres.id
                                        where polres.id = \''.$polres.'\' and accidents.no_lp ilike\'%'.$no_lp.'%\' ');
            break;
            default:
                $accident = DB::select('select 
                                            accidents.id as id
                                            , no_lp,concat_ws(\''.$a.'\'
                                            , officer_first_name,officer_last_name) as name
                                            , polres.name as polres_name
                                            , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-Mon-YYYY\') as accident_date
                                            , road_name
                                            , damage_lose_desc as description
                                            , damage_lose_desc as tindak_lanjut
                                            , (select ref.name from ref where ref.id = selra_flag) as status
                                        from accidents 
                                            left join polres on accidents.polres_id = polres.id where  accidents.no_lp ilike\'%'.$no_lp.'%\' ');
        }
        // $a= ' ';
        // $accident = DB::select('select 
        //                             no_lp
        //                             , concat_ws(\''.$a.'\',officer_first_name,officer_last_name) as name
        //                             , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
        //                             , road_name
        //                             , damage_lose_desc as description
        //                             , damage_lose_desc as tindak_lanjut
        //                         from accidents where id = \''.$id.'\' '); 
        return $this->sendResponse($accident, 'Products retrieved successfully.');
    }

    public function search_id(Request $request)
    {
        $id = $request->input('id');
        $user = Auth::user();
        $a = ' ';
        switch($user->role_id){
            case 2:
                $polda = $user->polda_id;
                $polres = '-';
                $accident = DB::select('select 
                                            accidents.id as id
                                            , no_lp,concat_ws(\''.$a.'\'
                                            , officer_first_name,officer_last_name) as name
                                            , polres.name as polres_name
                                            , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                                            , road_name
                                            , damage_lose_desc as description
                                            , damage_lose_desc as tindak_lanjut
                                            , (select ref.name from ref where ref.id = selra_flag) as status
                                        from accidents
                                            left join polres on accidents.polres_id = polres.id
                                            left join polda on polres.polda_id = polda.id
                                        where polda.id = \''.$polda.'\' 
                                        and accidents.id = \''.$id.'\' ' );
            break;
        
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;
                $accident = DB::select('select 
                                            accidents.id as id
                                            , no_lp,concat_ws(\''.$a.'\'
                                            , officer_first_name,officer_last_name) as name
                                            , polres.name as polres_name
                                            , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
                                            , road_name
                                            , damage_lose_desc as description
                                            , damage_lose_desc as tindak_lanjut
                                            , (select ref.name from ref where ref.id = selra_flag) as status
                                        from accidents
                                            left join polres on accidents.polres_id = polres.id
                                        where polres.id = \''.$polres.'\' and accidents.id = \''.$id.'\' ' );
            break;
            default:
                $accident = DB::select('select 
                                            accidents.id as id
                                            , no_lp,concat_ws(\''.$a.'\'
                                            , officer_first_name,officer_last_name) as name
                                            , polres.name as polres_name
                                            , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
                                            , to_char(accident_date, \'DD-Mon-YYYY\') as accident_date
                                            , road_name
                                            , damage_lose_desc as description
                                            , damage_lose_desc as tindak_lanjut
                                            , (select ref.name from ref where ref.id = selra_flag) as status
                                        from accidents 
                                            left join polres on accidents.polres_id = polres.id where  and accidents.id = \''.$id.'\' ' );
        }
        // $a= ' ';
        // $accident = DB::select('select 
        //                             no_lp
        //                             , concat_ws(\''.$a.'\',officer_first_name,officer_last_name) as name
        //                             , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
        //                             , road_name
        //                             , damage_lose_desc as description
        //                             , damage_lose_desc as tindak_lanjut
        //                         from accidents where id = \''.$id.'\' '); 
        return $this->sendResponse($accident, 'Products retrieved successfully.');
    }

    public function search_dokumen(Request $request)
    {
        $id = $request->input('id');
        $user = Auth::user();
         switch($user->role_id){
            case 2:
                $polda = $user->polda_id;
                $polres = '-';
            break;
            case 3:
                $polda = $user->polda_id;
                $polres = $user->polres_id;
            break;
            default:
                $polda = '-';
                $polres = '-';
        }
        // $a= ' ';
        // $accident = DB::select('select
        //                             no_lp
        //                             , concat_ws(\''.$a.'\',officer_first_name,officer_last_name) as name
        //                             , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
        //                             , road_name
        //                             , damage_lose_desc as description
        //                             , damage_lose_desc as tindak_lanjut
        //                         from accidents where id = \''.$id.'\' ');
        $dokumen = DB::select('
        select  * from (

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, users.first_name ||\' \' || users.last_name  as officer_name,category,ref_grp.name as path from surat_perintah_membawa_saksi a left join ref on a.category = ref.id left join users on a.created_by = users.username left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_membawa_saksi a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_penyumpahan_saksi a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_panggilan_tersangka a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_perintah_penangkapan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_pemeriksaan_tersangka a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_konfrontasi a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_rekonstruksi a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from sket_tkp a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_bantuan_penangkapan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_penyerahan_tersangka a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_pelepasan_tersangka a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_perintah_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from permintaan_perpanjangan_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_perpanjangan_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_pengeluaran_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_pembatalan_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_pencabutan_pembatalan_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_pencabutan_pembatalan_penahanan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_penahanan_lanjutan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_penahanan_lanjutan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_izin_penggeledahan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_perintah_penggeledahan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_persetujuan_penggeledahan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_penggeledahan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_izin_penyitaan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_persetujuan_penyitaan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_pengiriman_berkas_perkara a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from tanda_terima_berkas_perkara a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_pengiriman_tersangka_barang_bukti a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_serah_terima_tersangka a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_bantuan_penyelidikan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_persetujuan_penyegelan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_penyegelan a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_permintaan_bantuan_labfor a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_hasil_pemeriksaan_labfor a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_permintaan_bantuan_identifikasi a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_hasil_pemeriksaan_identifikasi a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all

        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_blokir_rekening_bank a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_blokir_rekening_bank a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from surat_pembukaan_blokir_rekening_bank a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'
        union all
        select a.id as id,accident_id,a.created_at as created_at,a.name as dok_name ,initial, ref.name ref_name, officer.first_name ||\' \' || officer.last_name  as officer_name,category,ref_grp.name as path from berita_acara_pembukaan_blokir_rekening_bank a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = \''.$id.'\'

        ) dokumen
        order by dokumen.created_at desc
        ');       
        return $this->sendResponse($dokumen, 'Products retrieved successfully.');
    }

    public function getPenyidik(Request $request){
        $id = $request->input('id');
        $getdata = DB::select('select a.accident_id, a.officer_id,a.created_at,a.updated_at, concat_ws(\''.' '.'\', officer.first_name,officer.last_name) as name from surat_penyidikan a left join officer on a.officer_id = officer.id  where accident_id = \''.$id.'\'');
        return $this->sendResponse($getdata, 'Products retrieved successfully.');
    }

    public function getCountAccident(Request $request){
        $user = Auth::user();
        $b = '-';
        switch($user->role_id){
            case 2:
                $polda = $request->polda_id;
                $polres = $request->polres_id;
                $jumlahTotalLaka = '0';
                $jumlahLakaInput='0';
                $jumlahLakaSidik = DB::select('select coalesce(count(*),0) as jumlah_sidik from accidents
                                    left join polres on accidents.polres_id = polres.id
                                    left join polda on polres.polda_id = polda.id
                                    where CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                    AND
                                    CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END  
                                    and accidents.selra_flag = \'S0106\' ');
                $jumlahLakaSelra = DB::select('select coalesce(count(*),0) as jumlah_selra from accidents
                                    left join polres on accidents.polres_id = polres.id
                                    left join polda on polres.polda_id = polda.id
                                    where CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                    AND
                                    CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END 
                                    and accidents.selra_flag <> \'S0106\' ');
            break;
        
            case 3:
                $polda = $request->polda_id;
                $polres = $request->polres_id;
                $jumlahTotalLaka = '0';
                $jumlahLakaInput='0';
                $jumlahLakaSidik = DB::select('select coalesce(count(*),0) as jumlah_sidik from accidents
                                                left join polres on accidents.polres_id = polres.id
                                                left join polda on polres.polda_id = polda.id
                                                where CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                                AND
                                                CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END 
                                                and accidents.selra_flag = \'S0106\' ');
                $jumlahLakaSelra = DB::select('select coalesce(count(*),0) as jumlah_selra from accidents
                                                left join polres on accidents.polres_id = polres.id
                                                left join polda on polres.polda_id = polda.id
                                                where CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                                AND
                                                CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END 
                                                and accidents.selra_flag <> \'S0106\' ');
                
            break;
            default:
            $polda = $request->polda_id;
            $polres = $request->polres_id;
            $jumlahTotalLaka = '0';
            $jumlahLakaInput='0';
            $jumlahLakaSidik = DB::select('select coalesce(count(*),0) as jumlah_sidik 
                                            from accidents  
                                            left join polres on accidents.polres_id = polres.id
                                            left join polda on polres.polda_id = polda.id
                                            where CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                            AND
                                            CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END and 
                                            accidents.selra_flag = \'S0106\' ');
            $jumlahLakaSelra = DB::select('select coalesce(count(*),0) as jumlah_selra 
                                            from accidents  
                                            left join polres on accidents.polres_id = polres.id
                                            left join polda on polres.polda_id = polda.id
                                            where CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                            AND
                                            CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END and  accidents.selra_flag <> \'S0106\' ');
              
        }
        $data['jumlahTotalLaka'] = $jumlahTotalLaka;
        $data['jumlahLakaInput'] = $jumlahLakaInput;
        $data['jumlahLakaSidik'] = $jumlahLakaSidik[0]->jumlah_sidik;
        $data['jumlahLakaSelra'] = $jumlahLakaSelra[0]->jumlah_selra;
        return $this->sendResponse($data, 'Products retrieved successfully.');
    }

    public function getCountPersonal(){
        $user=Auth::user();
        $laporan_terkirim = DB::select('select coalesce(count(*),0) as laka_terkirim from surat_penyidikan as sidik 
                                        left join accidents as a on sidik.accident_id = a.id 
                                        where sidik.officer_id = \''.$user->officer_id.'\' ');
        $laporan_proses = DB::select('select coalesce(count(*),0) as laka_proses from surat_penyidikan as sidik 
                                              left join accidents as a on sidik.accident_id = a.id 
                                              where sidik.officer_id = \''.$user->officer_id.'\' 
                                                and a.selra_flag = \'S0107\' ');
        $laporan_selesai = DB::select('select coalesce(count(*),0) as laka_selesai from surat_penyidikan as sidik 
                                                left join accidents as a on sidik.accident_id = a.id 
                                                where sidik.officer_id = \''.$user->officer_id.'\' 
                                                and a.selra_flag <> \'S0107\' ');
        $data['laka_terkirim'] = $laporan_terkirim[0]->laka_terkirim;
        $data['laka_proses'] =  $laporan_proses[0]->laka_proses;
        $data['laka_selesai'] =  $laporan_selesai[0]->laka_selesai;
        return $this->sendResponse($data, 'Products retrieved successfully.');
            
    }

    public function getStatistik(Request $request){
        $polda = $request->polda_id;
        $polres=$request->polres_id;
	$dateFrom =Carbon::parse($request->dateFrom)->format('Y-m-d');
        $dateTo =Carbon::parse($request->dateTo)->format('Y-m-d');
        $kode= 'S01';
        $b='-';
        $data = DB::select('select ref.name,coalesce(total.count,0) as count from ref
        left join 
        (
            select ref.id ,ref.name
                ,coalesce(count(accidents.id),0) as count from accidents 
                 left join ref on accidents.selra_flag = ref.id 
                left join polres on polres.id = accidents.polres_id
                left join polda on polda.id = polres.polda_id
                where
                CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                AND
                CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END   
                AND
                CASE WHEN \''.$dateFrom.'\' <> \''.$b.'\' THEN accident_date BETWEEN \''.$dateFrom.'\' AND  \''.$dateTo.'\' ELSE TRUE END
		AND
		polres.state = 1                 	  
		group by ref.id, ref.name order by ref.id
        )as total on total.id = ref.id
        where grp_id = \''.$kode.'\'
        
        '); 
        // $data = DB::select('select \'P21\' as name, coalesce(count(*),0) as count from accidents ');
        return $this->sendResponse($data, 'Products retrieved successfully.');
            
    }


    public function getPhotoAccident(Request $request){
        $id=$request->id;
        $photo = DB::select('select name from upload_image
        left join accidents on accidents.id = upload_image.accident_id where accidents.id = \''.$id.'\' ');
        return $this->sendResponse($photo, 'Products retrieved successfully.');
    }
}
