<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportRekap;
use Carbon\Carbon;
use Auth;
use DB;

class ExportRekapController extends Controller
{
    public function ExportRekap(Request $request){
        $user=Auth::getUser();
        $checkuser=$user->role_id;
        $no_lp = $request->input('no_lp');
        $polres = $request->input('polres');
        $polda = $request->input('polda');
        $checkTanggal = $request->input('date_from');//for check date if user click the form
        $date_from=Carbon::parse($request->date_from)->format('Y-m-d');
        $date_to=Carbon::parse($request->date_to)->format('Y-m-d');
        $checkstatus = $request->input('status');

        if($checkTanggal == null){
            $checkTanggal = '-';
        }

        if($no_lp == null){
            $no_lp = '-';
        }

        if($checkstatus == null){
            $checkstatus = '-';
        }
        if($polres == null){
            $polres = '-';
        }

        $date_now = Carbon::now();

        switch ($checkuser) {
            case 2:
                $rekap = DB::select('
                    select
                    accidents.no_lp as no_lp,
                    to_char(accidents.accident_date, \'DD-MM-YYYY\') as accident_date,
                    to_char(accidents.created_at, \'DD-MM-YYYY\') as accident_tindak_lanjut,
                    CASE
                        WHEN selra_flag <> \'S0107\' THEN AGE(accidents.updated_at,accidents.created_at) ELSE AGE(\''.$date_now.'\',accidents.created_at)
                    END AS accident_proses,
                    to_char(accidents.last_update, \'DD-MM-YYYY HH24:ii:ss\') as accident_last_update,
                    concat(tipe_update, \' \',(select ref.name from ref where id = accidents.category) )AS tipe_berkas,
                    ref.name as selra_flag
                    from accidents
                    left join ref on ref.id=accidents.selra_flag
                    left join polres on polres.id = accidents.polres_id
                    left join polda on polda.id = polres.polda_id
                    where ref.grp_id = \'S01\'
                    AND
                    CASE
                        WHEN \''.$no_lp.'\' <> \'-\' THEN no_lp ilIke \'%'.$no_lp.'%\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$checkTanggal.'\' <> \'-\' THEN accidents.accident_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$checkstatus.'\' <> \'-\' THEN accidents.selra_flag = \''.$checkstatus.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$polda.'\' <> \'-\' THEN polda.id = \''.$polda.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$polres.'\' <> \'-\' THEN polres.id = \''.$polres.'\' ELSE true
                    END
                        order by accident_tindak_lanjut desc
                ');
                break;
            case 3:
                $rekap = DB::select('
                    select
                    accidents.no_lp as no_lp,
                    to_char(accidents.accident_date, \'DD-MM-YYYY\') as accident_date,
                    to_char(accidents.created_at, \'DD-MM-YYYY\') as accident_tindak_lanjut,
                    CASE
                        WHEN selra_flag <> \'S0107\' THEN AGE(accidents.updated_at,accidents.created_at) ELSE AGE(\''.$date_now.'\',accidents.created_at)
                    END AS accident_proses,
                    to_char(accidents.last_update, \'DD-MM-YYYY HH24:ii:ss\') as accident_last_update,
                    concat(tipe_update, \' \',(select ref.name from ref where id = accidents.category)) AS tipe_berkas,
                    ref.name as selra_flag
                    from accidents
                    left join ref on ref.id=accidents.selra_flag
                    left join polres on polres.id = accidents.polres_id
                    left join polda on polda.id = polres.polda_id
                    where ref.grp_id = \'S01\'
                    AND
                    CASE
                        WHEN \''.$no_lp.'\' <> \'-\' THEN no_lp ilIke \'%'.$no_lp.'%\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$checkTanggal.'\' <> \'-\' THEN accidents.accident_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$checkstatus.'\' <> \'-\' THEN accidents.selra_flag = \''.$checkstatus.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$polda.'\' <> \'-\' THEN polda.id = \''.$polda.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$polres.'\' <> \'-\' THEN polres.id = \''.$polres.'\' ELSE true
                    END
                        order by accident_tindak_lanjut desc
                    ');
                break;
                case 4:
                    $rekap = DB::select('
                        select
                        accidents.no_lp as no_lp,
                        to_char(accidents.accident_date, \'DD-MM-YYYY\') as accident_date,
                        to_char(accidents.created_at, \'DD-MM-YYYY\') as accident_tindak_lanjut,
                        CASE
                            WHEN selra_flag <> \'S0107\' THEN AGE(accidents.updated_at,accidents.created_at) ELSE AGE(\''.$date_now.'\',accidents.created_at)
                        END AS accident_proses,
                        to_char(accidents.last_update, \'DD-MM-YYYY HH24:ii:ss\') as accident_last_update,
                        tipe_update,
                        (select ref.name from ref where id = accidents.category) AS tipe_berkas,
                        ref.name as selra_flag,
                        accidents.selra_flag as selra
                        from accidents
                        left join ref on ref.id=accidents.selra_flag
                        left join polres on polres.id = accidents.polres_id
                        where ref.grp_id = \'S01\'
                        AND
                        CASE
                            WHEN \''.$no_lp.'\' <> \'-\' THEN no_lp ilIke \'%'.$no_lp.'%\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$checkTanggal.'\' <> \'-\' THEN accidents.accident_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$checkstatus.'\' <> \'-\' THEN accidents.selra_flag = \''.$checkstatus.'\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$polda.'\' <> \'-\' THEN polda.id = \''.$polda.'\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$polres.'\' <> \'-\' THEN polres.id = \''.$polres.'\' ELSE true
                        END
                            order by accident_tindak_lanjut desc
                        ');
                    $poldas=Polda::where('id','=',$user->polda_id)->get();
                    $polress=Polres::where('id','=',$user->polres_id)->get();

                    break;
                case 5:
                    $rekap = DB::select('
                        select
                        accidents.no_lp as no_lp,
                        to_char(accidents.accident_date, \'DD-MM-YYYY\') as accident_date,
                        to_char(accidents.created_at, \'DD-MM-YYYY\') as accident_tindak_lanjut,
                        CASE
                            WHEN selra_flag <> \'S0107\' THEN AGE(accidents.updated_at,accidents.created_at) ELSE AGE(\''.$date_now.'\',accidents.created_at)
                        END AS accident_proses,
                        to_char(accidents.last_update, \'DD-MM-YYYY HH24:ii:ss\') as accident_last_update,
                        tipe_update,
                        (select ref.name from ref where id = accidents.category) AS tipe_berkas,
                        ref.name as selra_flag,
                        accidents.selra_flag as selra
                        from accidents
                        left join ref on ref.id=accidents.selra_flag
                        left join polres on polres.id = accidents.polres_id
                        where ref.grp_id = \'S01\'
                        AND
                        CASE
                            WHEN \''.$no_lp.'\' <> \'-\' THEN no_lp ilIke \'%'.$no_lp.'%\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$checkTanggal.'\' <> \'-\' THEN accidents.accident_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$checkstatus.'\' <> \'-\' THEN accidents.selra_flag = \''.$checkstatus.'\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$polda.'\' <> \'-\' THEN polda.id = \''.$polda.'\' ELSE true
                        END
                        AND
                        CASE
                            WHEN \''.$polres.'\' <> \'-\' THEN polres.id = \''.$polres.'\' ELSE true
                        END
                            order by accident_tindak_lanjut desc
                        ');
                    $poldas=Polda::where('id','=',$user->polda_id)->get();
                    $polress=Polres::where('id','=',$user->polres_id)->get();

            default:
                $rekap = DB::select('
                    select
                    accidents.no_lp as no_lp,
                    to_char(accidents.accident_date, \'DD-MM-YYYY\') as accident_date,
                    to_char(accidents.created_at, \'DD-MM-YYYY\') as accident_tindak_lanjut,
                    CASE
                        WHEN selra_flag <> \'S0107\' THEN AGE(accidents.updated_at,accidents.created_at) ELSE AGE(\''.$date_now.'\',accidents.created_at)
                    END AS accident_proses,
                    to_char(accidents.last_update, \'DD-MM-YYYY HH24:ii:ss\') as accident_last_update,
                    concat(tipe_update, \' \',(select ref.name from ref where id = accidents.category) )AS tipe_berkas,
                    ref.name as selra_flag
                    from accidents
                    left join ref on ref.id=accidents.selra_flag
                    left join polres on polres.id = accidents.polres_id
                    left join polda on polda.id = polres.polda_id
                    where ref.grp_id = \'S01\'
                    AND
                    CASE
                        WHEN \''.$no_lp.'\' <> \'-\' THEN no_lp ilIke \'%'.$no_lp.'%\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$checkstatus.'\' <> \'-\' THEN accidents.selra_flag = \''.$checkstatus.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$polda.'\' <> \'-\' THEN polda.id = \''.$polda.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$polres.'\' <> \'-\' THEN polres.id = \''.$polres.'\' ELSE true
                    END
                    AND
                    CASE
                        WHEN \''.$checkTanggal.'\' <> \'-\' THEN accidents.accident_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\' ELSE true
                    END

                        order by accident_tindak_lanjut desc
                    ');
                break;
        }
        return Excel::download(new ExportRekap($rekap),"Rekap".date("Y-m-d H-i-s")." .xlsx");
    }
}
