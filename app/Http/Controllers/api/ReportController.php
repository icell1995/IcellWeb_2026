<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ReportController extends BaseController
{
    //

   public function report_indinvidu(Request $request){
        $polda = $request->polda_id;
        $polres= $request->polres_id;
        $null='-';
        $data = DB::select('select officer.id,officer.first_name,officer.last_name,polres.name as polres_name,coalesce(sidik.jumlah_sidik,0) as total_laka_ditangani,coalesce(sidik_proses.jumlah_sidik,0) as total_laka_dalam_proses,coalesce(sidik_selesai.jumlah_sidik,0) as total_laka_selesai, foto.avatars from officer
        left join 
        (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan group by officer_id) as sidik on officer.id = sidik.sidik_id
        left join
        (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = \'S0106\' group by surat_penyidikan.officer_id) as sidik_proses on officer.id = sidik_proses.sidik_id
        left join
        (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag <> \'S0106\' group by surat_penyidikan.officer_id) as sidik_selesai on officer.id = sidik_selesai.sidik_id
        left join
        (select users.avatar as avatars, users.officer_id as users_id from users) as foto on officer.id = foto.users_id
        left join polda on polda.id = officer.polda_id
        
        left join polres on polres.id = officer.polres_id
        where
        CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN officer.polda_id = \''.$polda.'\' ELSE TRUE END
        AND
        CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN officer.polres_id = \''.$polres.'\' ELSE TRUE END  
        ');

        return $this->sendResponse($data,'Data Successfully');
    }

    public function report_maps(Request $request){
        $polda = $request->polda_id;
        $polres= $request->polres_id;
	$dateFrom =Carbon::parse($request->dateFrom)->format('Y-m-d');
        $null='-';
        $data = DB::select('select accidents.id,no_lp,latitude,longtitude,road_name,md,lb,lr from accidents  left join polres on polres.id = accidents.polres_id
        left join polda on polda.id = polres.polda_id
        where
        CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
        AND
        CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
        AND
        CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date = \''.$dateFrom.'\' ELSE TRUE END  ');
        return $this->sendResponse($data,'Data Successfully');
    }

    public function report_polres(Request $request){
        $polda = $request->polda_id;
        $P21 = 'S0101';
        $SP3 = 'S0102';
        $diversi = 'S0103';
        $PomTni = 'S0104';
        $rj = 'S0105';
        $dalamProses = 'S0106';
        $polres = $request->polres_id;
        $null = '-';
	$dateFrom=Carbon::parse($request->dateFrom)->format('Y-m-d');
	$dateTo=Carbon::parse($request->dateTo)->format('Y-m-d');
        $data = DB::select('select  
        CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.name ELSE polres.name END AS polres_names,
        total_p21.total AS p21,
        total_SP3.total AS SP3,
        total_diversi.total AS diversi,
        total_PomTni.total AS pomtni,
        total_rj.total AS rj,
        total_dalamProses.total AS dalamproses

        from polda
        left join polres on polda.id = polres.polda_id

        LEFT JOIN(
            SELECT CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.id ELSE polres.id END AS id,
            count(accidents.id) AS total
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE
            CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END 
     	    AND
            CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
            AND
            CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date between \''.$dateFrom.'\' and \''.$dateTo.'\' ELSE TRUE END    
	    AND accidents.selra_flag=\''.$P21.'\'
            AND polda.state <> 0
            AND polres.state <> 0
            GROUP BY 1
        )total_p21 on polda.id = total_p21.id or polres.id = total_p21.id

        LEFT JOIN(
            SELECT CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.id ELSE polres.id END AS id,
            count(accidents.id) AS total
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE
            CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END AND
            CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
            AND
            CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date between \''.$dateFrom.'\' and \''.$dateTo.'\' ELSE TRUE END    
	    AND accidents.selra_flag=\''.$SP3.'\'
            AND polda.state <> 0
            AND polres.state <> 0
            GROUP BY 1
        )total_SP3 on polda.id = total_SP3.id or polres.id = total_SP3.id

        LEFT JOIN(
            SELECT CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.id ELSE polres.id END AS id,
            count(accidents.id) AS total
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE
            CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END AND
            CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
	    AND
            CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date between \''.$dateFrom.'\' and \''.$dateTo.'\' ELSE TRUE END               
 	    AND accidents.selra_flag=\''.$diversi.'\'
            AND polda.state <> 0
            AND polres.state <> 0
            GROUP BY 1
        )total_diversi on polda.id = total_diversi.id or polres.id = total_diversi.id
        
        LEFT JOIN(
            SELECT CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.id ELSE polres.id END AS id,
            count(accidents.id) AS total
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE
            CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END AND
            CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
            AND
            CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date between \''.$dateFrom.'\' and \''.$dateTo.'\' ELSE TRUE END    
	    AND accidents.selra_flag=\''.$PomTni.'\'
            AND polda.state <> 0
            AND polres.state <> 0
            GROUP BY 1
        )total_PomTni on polda.id = total_PomTni.id or polres.id = total_PomTni.id
        
        LEFT JOIN(
            SELECT CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.id ELSE polres.id END AS id,
            count(accidents.id) AS total
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE
            CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END AND
            CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
            AND
            CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date between \''.$dateFrom.'\' and \''.$dateTo.'\' ELSE TRUE END       
       	    AND accidents.selra_flag=\''.$rj.'\'
            AND polda.state <> 0
            AND polres.state <> 0
            GROUP BY 1
        )total_rj on polda.id = total_rj.id or polres.id = total_rj.id

        LEFT JOIN(
            SELECT CASE WHEN \''.$polda.'\' = \''.$null.'\' THEN polda.id ELSE polres.id END AS id,
            count(accidents.id) AS total
            FROM polda
            JOIN polres ON polda.id = polres.polda_id
            JOIN accidents ON polres.id = accidents.polres_id
            WHERE
            CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END AND
            CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
 	    AND
            CASE WHEN \''.$dateFrom.'\' <> \''.$null.'\' THEN accident_date between \''.$dateFrom.'\' and \''.$dateTo.'\' ELSE TRUE END            
	    AND accidents.selra_flag=\''.$dalamProses.'\'
            AND polda.state <> 0
            AND polres.state <> 0
            GROUP BY 1
        )total_dalamProses on polda.id = total_dalamProses.id or polres.id = total_dalamProses.id

        where 
        CASE WHEN \''.$polda.'\' <> \''.$null.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
        AND
        CASE WHEN \''.$polres.'\' <> \''.$null.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
        GROUP BY polres_names, p21,sp3,diversi,pomtni, rj, dalamproses
        ');

        return $this->sendResponse($data,'Data Successfully');
    } 
}
