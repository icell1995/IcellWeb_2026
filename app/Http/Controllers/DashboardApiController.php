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
        $token = (string) $req->header('token', '');
        $expectedToken = (string) config('services.icell_api_tokens.dashboard');
        if ($expectedToken === '' || !hash_equals($expectedToken, $token)) {
            return $this->sendError('Invalid API', ['error' => 'Unauthorized'], 401);
        }

        $now = (int) Carbon::now()->month;
        $past = (int) Carbon::now()->addDays(-30)->month;
        $data = DB::select(
            "select 
            count(case when selra_flag='S0101' and date_part('month',accidents.created_at)=? then 1 else null end) as P21,
            count(case when selra_flag='S0102' and date_part('month',accidents.created_at)=? then 1 else null end) as SP3,
            count(case when selra_flag='S0103' and date_part('month',accidents.created_at)=? then 1 else null end) as Diversi,
            count(case when selra_flag='S0104' and date_part('month',accidents.created_at)=? then 1 else null end) as POM_TNI,
            count(case when selra_flag='S0105' and date_part('month',accidents.created_at)=? then 1 else null end) as ADR_RJ,
            count(case when selra_flag='S0106' and date_part('month',accidents.created_at)=? then 1 else null end) as DALAM_PROSES,
            count(case when selra_flag='S0107' and date_part('month',accidents.created_at)=? then 1 else null end) as SP2LID
            from accidents
            left join polres on polres.id = accidents.polres_id
            left join polda on polda.id = polres.polda_id
            left join ref on ref.id = accidents.selra_flag
            ", [$now, $now, $now, $now, $now, $now, $now]);
            
        $poldaNow = DB::select(
            "select
            polda.name,
            count(case when date_part('month',accidents.created_at)=? then 1 else null end) as total
            from accidents
            right join polres on polres.id = accidents.polres_id
            right join polda on polda.id = polres.polda_id
            where polda.id not in('90','99')
            group by polda.name",
            [$now]
        );
        $poldaPast = DB::select(
            "select
            polda.name,
            count(case when date_part('month',accidents.created_at)=? then 1 else null end) as total
            from accidents
            right join polres on polres.id = accidents.polres_id
            right join polda on polda.id = polres.polda_id
            where polda.id not in('90','99')
            group by polda.name",
            [$past]
        );
        $Total = DB::select(
            "select 
            count(case when date_part('month',accidents.created_at)=? then 1 else null end) as totalNow,
            count(case when date_part('month',accidents.created_at)=? then 1 else null end) as totalPast
            from accidents",
            [$now, $past]
        );

        $JumlahSelra = DB::select("
            select 
            count(case when date_part('month',accidents.created_at)=? then 1 else null end) as totalNow,
            count(case when date_part('month',accidents.created_at)=? then 1 else null end) as totalOld,
            ref.name
            from accidents
            right join ref on ref.id = accidents.selra_flag
            where grp_id = 'S01'
            group by ref.name 
        ", [$now, $past]);

        $send['data']=$data;
        $send['poldaNow']=$poldaNow;
        $send['poldaPast']=$poldaPast;
        $send['Total']=$Total;
        $send['JumlahSelra']=$JumlahSelra;
        return $this->sendResponse($send, 'successfully.');
    }
}
