<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportStatistika;
use App\Models\Accident;
use App\Models\Polres;
use Illuminate\Support\Facades\DB;
// use DB;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function ExportMonth(Request $request){
        $checkBulan= Carbon::parse($request->input('bulan'))->addMonths()->addDays(-1)->format('Y-m-d');
        $checkBulanPast = Carbon::parse($request->input('bulan'))->format('Y-m-d');
        $polda = $request->input('polda_id');
        $today = Carbon::parse($request->input('hari'))->format('Y-m-d');
        $polres = $request->input('polres_id');
        $status = $request->input('selra_id');
        $grp_1 = 'A072';
		$grp_2 = 'A073';
        if($polres=="" || $polres==null){
            $polres = "-";
        }if($polda=="" || $polda==null){
            $polda = "-";
        }
        $data = DB::select(
            "select
            no_lp,
            polres.name as polres_name,
            polda.name as polda_name,
            md,
            lb,
            lr,
            date(accidents.created_at),
            (
                CASE
                WHEN LEFT(accidents.accident_type_id,4) IN ('$grp_1' , '$grp_2') THEN 'TUNGGAL'
                  WHEN LEFT(ACCIDENT_TYPE_ID,4) NOT IN ('$grp_1' , '$grp_2') THEN 'KONTRA'
                END
            ) as jenislaka,
            (
                case
                    when md>=1 and lb<=md and lr<=md then 'Berat'
                    when lb>=1 and lb<md and lr<=lb then 'Sedang'
                    else 'Ringan'

                end
            )as tingkatlaka,
            accidents.total_ranmor as ranmor,
            ref.name as ref_name
            from accidents
            left join polres on polres.id = accidents.polres_id
            left join polda on polda.id = polres.polda_id
            left join ref on ref.id = accidents.selra_flag
            where
            case when '$polda' <> '-' then polda.id = '$polda' else true end
            and
            case when '$polres' <> '-' then polres.id = '$polres' else true end
            and
            date(accidents.created_at) between '$checkBulanPast' and '$checkBulan'
            "
        );
        $check = Excel::download(new ExportStatistika($data),"Statistika Month".date("Y-m-d H-i-s")." .xlsx");
        return $check;
    }
    public function ExportDays(Request $request){
        $polda = $request->input('polda_id');
        $checkWeek = '';
        $today = Carbon::parse($request->input('hari'))->format('Y-m-d');
        $polres = $request->input('polres_id');
        $status = $request->input('selra_id');
        $grp_1 = 'A072';
		$grp_2 = 'A073';
        if($polres=="" || $polres==null){
            $polres = "-";
        }if($polda=="" || $polda==null){
            $polda = "-";
        }
        // $data = DB::select(
        //     "select
        //     no_lp,
        //     polres.name as polres_name,
        //     polda.name as polda_name,
        //     md,
        //     lb,
        //     lr,
        //     date(accidents.created_at),
        //     (
        //         CASE
        //         WHEN LEFT(accidents.accident_type_id,4) IN ('$grp_1' , '$grp_2') THEN 'TUNGGAL'
        //           WHEN LEFT(ACCIDENT_TYPE_ID,4) NOT IN ('$grp_1' , '$grp_2') THEN 'KONTRA'
        //         END
        //     ) as jenislaka,
        //     (
        //         case
        //             when md>=1 and lb<=md and lr<=md then 'Berat'
        //             when lb>=1 and lb<md and lr<=lb then 'Sedang'
        //             else 'Ringan'

        //         end
        //     )as tingkatlaka,
        //     accidents.total_ranmor as ranmor,
        //     ref.name as ref_name
        //     from accidents
        //     left join polres on polres.id = accidents.polres_id
        //     left join polda on polda.id = polres.polda_id
        //     left join ref on ref.id = accidents.selra_flag
        //     where
        //     case when '$polda' <> '-' then polda.id = '$polda' else true end
        //     and
        //     case when '$polres' <> '-' then polres.id = '$polres' else true end
        //     and
        //     date(accidents.created_at) '$today'
        //     "
        // );

        $data = DB::table('accidents')
            ->select(
                'no_lp',
                'polres.name as polres_name',
                'polda.name as polda_name',
                'md',
                'lb',
                'lr',
                DB::raw('DATE(accidents.created_at) as created_date'),
                DB::raw("CASE WHEN LEFT(accidents.accident_type_id, 4) IN ('$grp_1', '$grp_2') THEN 'TUNGGAL' WHEN LEFT(ACCIDENT_TYPE_ID, 4) NOT IN ('$grp_1', '$grp_2') THEN 'KONTRA' END as jenislaka"),
                DB::raw("CASE WHEN md >= 1 AND lb <= md AND lr <= md THEN 'Berat' WHEN lb >= 1 AND lb < md AND lr <= lb THEN 'Sedang' ELSE 'Ringan' END as tingkatlaka"),
                'accidents.total_ranmor as ranmor',
                'ref.name as ref_name'
            )
            ->leftJoin('polres', 'polres.id', '=', 'accidents.polres_id')
            ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
            ->leftJoin('ref', 'ref.id', '=', 'accidents.selra_flag')
            ->where(function ($query) use ($polda) {
                if ($polda !== '-') {
                    $query->where('polda.id', '=', $polda);
                }
            })
            ->where(function ($query) use ($polres) {
                if ($polres !== '-') {
                    $query->where('polres.id', '=', $polres);
                }
            })
            ->whereDate('accidents.created_at', $today)
            ->get();

        $check = Excel::download(new ExportStatistika($data),"Statistika Days ".date("Y-m-d H-i-s")." .xlsx");
        return $check;
    }
    public function ExportWeeks(Request $request){
        $checkBulan='';
        $checkBulanPast = '';
        $weekPast=Carbon::parse($request->input('week'))->format('Y-m-d');
        $checkWeek = Carbon::parse($request->input('week'))->addDays(7)->format('Y-m-d');
        $today = '';
        $polres = $request->input('polres_id');
        $status = $request->input('selra_id');
        $polda = $request->input('polda_id');
        $grp_1 = 'A072';
		$grp_2 = 'A073';
        if($polres=="" || $polres==null){
            $polres = "-";
        }if($polda=="" || $polda==null){
            $polda = "-";
        }
        // $data = DB::select(
        //     "select
        //     no_lp,
        //     polres.name as polres_name,
        //     polda.name as polda_name,
        //     md,
        //     lb,
        //     lr,
        //     date(accidents.created_at),
        //     (
        //         CASE
        //         WHEN LEFT(accidents.accident_type_id,4) IN ('$grp_1' , '$grp_2') THEN 'TUNGGAL'
        //           WHEN LEFT(ACCIDENT_TYPE_ID,4) NOT IN ('$grp_1' , '$grp_2') THEN 'KONTRA'
        //         END
        //     ) as jenislaka,
        //     (
        //         case
        //             when md>=1 and lb<=md and lr<=md then 'Berat'
        //             when lb>=1 and lb<md and lr<=lb then 'Sedang'
        //             else 'Ringan'

        //         end
        //     )as tingkatlaka,
        //     accidents.total_ranmor as ranmor,
        //     ref.name as ref_name
        //     from accidents
        //     left join polres on polres.id = accidents.polres_id
        //     left join polda on polda.id = polres.polda_id
        //     left join ref on ref.id = accidents.selra_flag
        //     where
        //     case when '$polda' <> '-' then polda.id = '$polda' else true end
        //     and
        //     case when '$polres' <> '-' then polres.id = '$polres' else true end
        //     and
        //     date(accidents.created_at) between '$weekPast' and '$checkWeek'
        //     "
        // );


        $data = DB::table('accidents')
        ->select(
            'no_lp',
            'polres.name as polres_name',
            'polda.name as polda_name',
            'md',
            'lb',
            'lr',
            DB::raw('DATE(accidents.created_at) as created_date'),
            DB::raw("CASE WHEN LEFT(accidents.accident_type_id, 4) IN ('$grp_1', '$grp_2') THEN 'TUNGGAL' WHEN LEFT(ACCIDENT_TYPE_ID, 4) NOT IN ('$grp_1', '$grp_2') THEN 'KONTRA' END as jenislaka"),
            DB::raw("CASE WHEN md >= 1 AND lb <= md AND lr <= md THEN 'Berat' WHEN lb >= 1 AND lb < md AND lr <= lb THEN 'Sedang' ELSE 'Ringan' END as tingkatlaka"),
            'accidents.total_ranmor as ranmor',
            'ref.name as ref_name'
        )
        ->leftJoin('polres', 'polres.id', '=', 'accidents.polres_id')
        ->leftJoin('polda', 'polda.id', '=', 'polres.polda_id')
        ->leftJoin('ref', 'ref.id', '=', 'accidents.selra_flag')
        ->when($polda !== '-', function ($query) use ($polda) {
            return $query->where('polda.id', $polda);
        })
        ->when($polres !== '-', function ($query) use ($polres) {
            return $query->where('polres.id', $polres);
        })
        ->whereBetween(DB::raw('DATE(accidents.created_at)'), [$weekPast, $checkWeek])
        ->get();

        $check = Excel::download(new ExportStatistika($data),"Statistika Days ".date("Y-m-d H-i-s")." .xlsx");
        return $check;
    }
}
