<?php

namespace App\Http\Controllers;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\api\BaseController as BaseController;

use Illuminate\Http\Request;

class DashboardApiController extends BaseController
{
    public function dashboard(Request $req)
    {  
        $token = $req->header('token');
        //if($token == 'Wzvp+eEwdQLnSjfviqeC+mWwdfE0a2H2q9fGyw7y2tU='){
            $now = Carbon::now()->month;
            //$past = Carbon::now()->addMonths(-1)->month;
		$past = Carbon::now()->addDays(-30)->month;
        $data = DB::select(
            "select 
            count(case when selra_flag='S0101' and date_part('month',accidents.created_at)=$now then 1 else null end) as P21,
            count(case when selra_flag='S0102' and date_part('month',accidents.created_at)=$now then 1 else null end) as SP3,
            count(case when selra_flag='S0103' and date_part('month',accidents.created_at)=$now then 1 else null end) as Diversi,
            count(case when selra_flag='S0104' and date_part('month',accidents.created_at)=$now then 1 else null end) as POM_TNI,
            count(case when selra_flag='S0105' and date_part('month',accidents.created_at)=$now then 1 else null end) as ADR_RJ,
            count(case when selra_flag='S0106' and date_part('month',accidents.created_at)=$now then 1 else null end) as DALAM_PROSES,
            count(case when selra_flag='S0107' and date_part('month',accidents.created_at)=$now then 1 else null end) as SP2LID
            from accidents
            left join polres on polres.id = accidents.polres_id
            left join polda on polda.id = polres.polda_id
            left join ref on ref.id = accidents.selra_flag
            ");
            
        $poldaNow = DB::select(
            "select
            polda.name,
            count(case when date_part('month',accidents.created_at)=$now then 1 else null end) as total
            from accidents
            right join polres on polres.id = accidents.polres_id
            right join polda on polda.id = polres.polda_id
            where polda.id not in('90','99')
            group by polda.name"
        );
        $poldaPast = DB::select(
            "select
            polda.name,
            count(case when date_part('month',accidents.created_at)=$past then 1 else null end) as total
            from accidents
            right join polres on polres.id = accidents.polres_id
            right join polda on polda.id = polres.polda_id
            where polda.id not in('90','99')
            group by polda.name"
        );
        $Total = DB::select(
        "select 
        count(case when date_part('month',accidents.created_at)=$now then 1 else null end) as totalNow,
        count(case when date_part('month',accidents.created_at)=$past then 1 else null end) as totalPast
        from accidents
        ");

        $JumlahSelra = DB::select("
            select 
            count(case when date_part('month',accidents.created_at)=$now  then 1 else null end) as totalNow,
            count(case when date_part('month',accidents.created_at)=$past  then 1 else null end) as totalOld,
            ref.name
            from accidents
            right join ref on ref.id = accidents.selra_flag
            where grp_id = 'S01'
            group by ref.name 
        ");

        $send['data']=$data;
        $send['poldaNow']=$poldaNow;
        $send['poldaPast']=$poldaPast;
        $send['Total']=$Total;
        $send['JumlahSelra']=$JumlahSelra;
        return $this->sendResponse($send, 'successfully.');
        //}else{
            
        //    return $this->sendResponse('Invalid API', 'Failed'); 
        //}
        
    }
}
