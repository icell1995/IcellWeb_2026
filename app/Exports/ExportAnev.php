<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
// use DB;
use App\Models\Polda;
use App\Models\Accident;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ExportAnev implements  ShouldAutoSize, WithHeadings , WithEvents, WithStrictNullComparison, FromArray

{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($poldaId, $polresId, $start_date_then, $end_date_then, $start_date_now, $end_date_now)
    {
        if ($poldaId !== null) {
            $this->poldaId = $poldaId;
        } else {
            $this->poldaId = '-';
        }
        
        if ($polresId !== null) {
            $this->polresId = $polresId;
        } else {
            $this->polresId = '-';
        }
                

        $this->start_date_then = $start_date_then;
        $this->end_date_then = $end_date_then;
        $this->start_date_now = $start_date_now;
        $this->end_date_now = $end_date_now;

    }

    public function array(): array
    {
        //

        $expression = DB::raw("select(SELECT row_to_json(t)
        FROM(
            SELECT
                (
                    SELECT array_to_json(array_agg(row_to_json(w)))
                        FROM(
                            SELECT
                            CASE WHEN '$this->poldaId' = '-' THEN polda.name ELSE polres.name END AS polda_polres,
                                total_sidik_laka.total_sidik_laka,
                                total_p21.total AS p21,
                                total_sp3.total AS sp3,
                                total_diversi.total AS diversi,
                                total_pom_tni.total AS pom_tni,
                                total_rj.total AS rj,
                                total_dalam_proses.total AS dalam_proses,
                                total_sp2lid.total AS sp2lid

                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total_sidik_laka
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_sidik_laka on polda.id = total_sidik_laka.id or polres.id = total_sidik_laka.id
                                LEFT JOIN(
                                 SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                AND selra_flag = 'S0101'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_p21 on polda.id = total_p21.id or polres.id = total_p21.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                  AND selra_flag = 'S0102'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_sp3 on polda.id = total_sp3.id or polres.id = total_sp3.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                  AND selra_flag = 'S0103'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_diversi on polda.id = total_diversi.id or polres.id = total_diversi.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                 AND selra_flag = 'S0104'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_pom_tni on polda.id = total_pom_tni.id or polres.id = total_pom_tni.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                  AND selra_flag = 'S0106'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_rj on polda.id = total_rj.id or polres.id = total_rj.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                AND selra_flag = 'S0107'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_dalam_proses on polda.id = total_dalam_proses.id or polres.id = total_dalam_proses.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_then' AND '$this->end_date_then'
                                    AND selra_flag = 'S0108'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_sp2lid on polda.id = total_sp2lid .id or polres.id = total_sp2lid .id

                                WHERE
                                CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                AND polda.state <> 0
                                AND polres.state <> 0
                                GROUP BY 1,2,3,4,5,6,7,8,9,polda.name
                                ORDER BY polda.name
                        )w
                    )AS tahun_lalu,
                    (
                    SELECT array_to_json(array_agg(row_to_json(w)))
                        FROM(
                            SELECT
                            CASE WHEN '$this->poldaId' = '-' THEN polda.name ELSE polres.name END AS polda_polres,
                                total_sidik_laka.total_sidik_laka,
                                total_p21.total AS p21,
                                total_sp3.total AS sp3,
                                total_diversi.total AS diversi,
                                total_pom_tni.total AS pom_tni,
                                total_rj.total AS rj,
                                total_dalam_proses.total AS dalam_proses,
                                total_sp2lid.total AS sp2lid

                                FROM polda
                                JOIN polres ON polda.id = polres.polda_id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total_sidik_laka
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_sidik_laka on polda.id = total_sidik_laka.id or polres.id = total_sidik_laka.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                    AND selra_flag = 'S0101'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_p21 on polda.id = total_p21.id or polres.id = total_p21.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                   AND selra_flag = 'S0102'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_sp3 on polda.id = total_sp3.id or polres.id = total_sp3.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                   AND selra_flag = 'S0103'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_diversi on polda.id = total_diversi.id or polres.id = total_diversi.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                   AND selra_flag = 'S0104'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_pom_tni on polda.id = total_pom_tni.id or polres.id = total_pom_tni.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                   AND selra_flag = 'S0106'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_rj on polda.id = total_rj.id or polres.id = total_rj.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                  AND selra_flag = 'S0107'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_dalam_proses on polda.id = total_dalam_proses.id or polres.id = total_dalam_proses.id
                                LEFT JOIN(
                                    SELECT CASE WHEN '$this->poldaId' = '-' THEN polda.id ELSE polres.id END AS id,
                                    count(accidents.id) AS total
                                    FROM polda
                                    JOIN polres ON polda.id = polres.polda_id
                                    JOIN accidents ON polres.id = accidents.polres_id
                                    WHERE
                                    CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                    CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                    AND accident_date BETWEEN '$this->start_date_now' AND '$this->end_date_now'
                                 AND selra_flag = 'S0108'
                                    AND polda.state <> 0
                                    AND polres.state <> 0
                                    AND accidents.state <> 0
                                    GROUP BY 1
                                )total_sp2lid on polda.id = total_sp2lid .id or polres.id = total_sp2lid .id
                                WHERE
                                CASE WHEN '$this->poldaId' <> '-' THEN polda.id = '$this->poldaId' ELSE TRUE END AND
                                CASE WHEN '$this->polresId' <> '-' THEN polres.id = '$this->polresId' ELSE TRUE END
                                AND polda.state <> 0
                                AND polres.state <> 0
                                GROUP BY 1,2,3,4,5,6,7,8,9,polda.name
                                ORDER BY polda.name
                        )w
                    )AS tahun_ini
                )t) as data");

                $query = DB::select($expression->getValue(DB::connection()->getQueryGrammar()));
                // dd($query);


                $data = json_decode($query[0]->data);
                // return response()->json(['data' => $data]);
                if(empty($data->tahun_lalu) && empty($data->tahun_ini)){
                    return response()->json(['message' => 'Data tidak ditemukan'], 412);
                }

                $tahun_ini = [];
                $tahun_lalu = [];
                $total_sidik = 0;
                $p21 = 0;
                $sp3 = 0;
                $diversi = 0;
                $pom_tni = 0;
                // $rj = 0;
                $sp2lid = 0;
                $dalam_proses = 0;
                $total_sidik_lalu = 0;
                $total_dalam_proses_lalu =0;
                $total_p21_lalu =0;
                $total_sp3_lalu =0;
                $total_diversi_lalu =0;
                // $total_rj_lalu =0;
                $total_sp2lid_lalu =0;
                $total_pom_tni_lalu =0;
                $total_sidik_ini = 0;
                $total_dalam_proses_ini =0;
                $total_p21_ini =0;
                $total_sp3_ini =0;
                $total_diversi_ini =0;
                // $total_rj_ini =0;
                $total_sp2lid_ini =0;
                $total_pom_tni_ini =0;
                
                        for($z=0;$z<count($data->tahun_lalu);$z++){
                            for($x=0;$x<count($data->tahun_ini);$x++){
                                if($data->tahun_lalu[$z]->polda_polres == $data->tahun_ini[$x]->polda_polres){
                                    $data->tahun_lalu[$z]->polda_polres = $data->tahun_lalu[$z]->polda_polres;
                                $data->tahun_lalu[$z]->total_sidik_laka = $data->tahun_lalu[$z]->total_sidik_laka?:0;
                                $data->tahun_lalu[$z]->dalam_proses = $data->tahun_lalu[$z]->dalam_proses?:0;
                                $data->tahun_lalu[$z]->p21 = $data->tahun_lalu[$z]->p21?:0;
                                $data->tahun_lalu[$z]->sp3 = $data->tahun_lalu[$z]->sp3?:0;
                                $data->tahun_lalu[$z]->diversi = $data->tahun_lalu[$z]->diversi?:0;
                                // $data->tahun_lalu[$z]->rj = $data->tahun_lalu[$z]->rj?:0;
                                $data->tahun_lalu[$z]->sp2lid = $data->tahun_lalu[$z]->sp2lid?:0;
                                $data->tahun_lalu[$z]->pom_tni = $data->tahun_lalu[$z]->pom_tni?:0;
                                $data->tahun_lalu[$z]->total_sidik_laka_ini = $data->tahun_ini[$x]->total_sidik_laka?:0;
                                $data->tahun_lalu[$z]->dalam_proses_tahun_ini = $data->tahun_ini[$x]->dalam_proses?:0;
                                $data->tahun_lalu[$z]->p21_tahun_ini = $data->tahun_ini[$x]->p21?:0;
                                $data->tahun_lalu[$z]->sp3_tahun_ini = $data->tahun_ini[$x]->sp3?:0;
                                $data->tahun_lalu[$z]->diversi_tahun_ini = $data->tahun_ini[$x]->diversi?:0;
                                $data->tahun_lalu[$z]->rj_tahun_ini = $data->tahun_ini[$x]->rj?:0;
                                $data->tahun_lalu[$z]->sp2lid_tahun_ini = $data->tahun_ini[$x]->sp2lid?:0;
                                $data->tahun_lalu[$z]->pom_tni_tahun_ini = $data->tahun_ini[$x]->pom_tni?:0;
                                $total_sidik_lalu +=  $data->tahun_lalu[$z]->total_sidik_laka;
                                $total_dalam_proses_lalu += $data->tahun_lalu[$z]->dalam_proses;
                                $total_p21_lalu += $data->tahun_lalu[$z]->p21;
                                $total_sp3_lalu += $data->tahun_lalu[$z]->sp3;
                                $total_diversi_lalu += $data->tahun_lalu[$z]->diversi;
                                // $total_rj_lalu += $data->tahun_lalu[$z]->rj;
                                $total_sp2lid_lalu += $data->tahun_lalu[$z]->sp2lid;
                                $total_pom_tni_lalu += $data->tahun_lalu[$z]->pom_tni;
                                $total_sidik_ini += $data->tahun_lalu[$z]->total_sidik_laka_ini;
                                $total_dalam_proses_ini  += $data->tahun_lalu[$z]->dalam_proses_tahun_ini;
                                $total_p21_ini += $data->tahun_lalu[$z]->p21_tahun_ini;
                                $total_sp3_ini  += $data->tahun_lalu[$z]->sp3_tahun_ini;
                                $total_diversi_ini  += $data->tahun_lalu[$z]->diversi_tahun_ini;
                                // $total_rj_ini  += $data->tahun_lalu[$z]->rj_tahun_ini;
                                $total_sp2lid_ini  += $data->tahun_lalu[$z]->sp2lid_tahun_ini;
                                $total_pom_tni_ini  += $data->tahun_lalu[$z]->pom_tni_tahun_ini;
                                }
                            }
                        }
                        // foreach ($data->tahun_lalu as $dt) {
                        //     $tahun_lalu[] = [
                        //         'polda'        => $dt->polda_polres,
                        //         'total_laka_lalu'   => $dt->total_sidik_laka ?:0,
                        //         'p21_lalu'           => $dt->p21 ?:0,
                        //         'sp3_lalu'           => $dt->sp3 ?:0,
                        //         'diversi_lalu'           => $dt->diversi ?:0,
                        //         'pom_tni_lalu'        => $dt->pom_tni ?:0,
                        //         'rj_lalu'        => $dt->rj ?:0,
                        //         'dalam_proses_lalu'        => $dt->dalam_proses ?:0,
                        //         'sp2lid_lalu'        => $dt->sp2lid ?:0,


                        //         'total_laka_ini'=> $dt->total_sidik_laka_ini,
                        //         'p21_tahun_ini'           => $dt->p21_tahun_ini ?:0,
                        //         'sp3_tahun_ini'           => $dt->sp3_tahun_ini ?:0,
                        //         'diversi_tahun_ini'           => $dt->diversi_tahun_ini ?:0,
                        //         'pom_tni_tahun_ini'        => $dt->pom_tni_tahun_ini ?:0,
                        //         'rj_tahun_ini'        => $dt->rj_tahun_ini ?:0,
                        //         'dalam_proses_tahun_ini'        => $dt->dalam_proses_tahun_ini ?:0,
                        //         'sp2lid_tahun_ini'        => $dt->sp2lid_tahun_ini ?:0,


                        //         ];
                        //     }

                        foreach ($data->tahun_lalu as $dt) {
                            $tahun_lalu[] = [
                                'polda'             => $dt->polda_polres,
                                'total_laka_lalu'   => $dt->total_sidik_laka ?:0,
                                'dalam_proses_lalu' => $dt->dalam_proses ?:0,
                                'p21_lalu'          => $dt->p21 ?:0,
                                'sp3_lalu'          => $dt->sp3 ?:0,
                                'diversi_lalu'      => $dt->diversi ?:0,
                                // 'rj_lalu'           => $dt->rj ?:0,
                                'sp2lid_lalu'       => $dt->sp2lid ?:0,
                                'pom_tni_lalu'      => $dt->pom_tni ?:0,


                                'total_laka_ini'           => $dt->total_sidik_laka_ini,
                                'dalam_proses_tahun_ini'   => $dt->dalam_proses_tahun_ini ?:0,
                                'p21_tahun_ini'            => $dt->p21_tahun_ini ?:0,
                                'sp3_tahun_ini'            => $dt->sp3_tahun_ini ?:0,
                                'diversi_tahun_ini'        => $dt->diversi_tahun_ini ?:0,
                                // 'rj_tahun_ini'             => $dt->rj_tahun_ini ?:0,
                                'sp2lid_tahun_ini'         => $dt->sp2lid_tahun_ini ?:0,
                                'pom_tni_tahun_ini'        => $dt->pom_tni_tahun_ini ?:0,

                                ];
                            }

                            $total[]=[
                            'Total',
                            'total_sidik_lalu'         => $total_sidik_lalu,
                            'total_dalam_proses_lalu'  => $total_dalam_proses_lalu,
                            'total_p21_lalu'           => $total_p21_lalu,
                            'total_sp3_lalu'           => $total_sp3_lalu,
                            'total_diversi_lalu'       => $total_diversi_lalu,
                            // 'total_rj_lalu'            => $total_rj_lalu,
                            'total_sp2lid_lalu'        => $total_sp2lid_lalu,
                            'total_pom_tni_lalu'       => $total_pom_tni_lalu,


                            'total_sidik_ini'          => $total_sidik_ini,
                            'total_dalam_proses_ini'   => $total_dalam_proses_ini,
                            'total_p21_ini'            => $total_p21_ini,
                            'total_sp3_ini'            => $total_sp3_ini,
                            'total_diversi_ini'        => $total_diversi_ini,
                            // 'total_rj_ini'             => $total_rj_ini,
                            'total_sp2lid_ini'         => $total_sp2lid_ini,
                            'total_pom_tni_ini'        => $total_pom_tni_ini,
                            ];

                        return [$tahun_lalu, $total];

    }
     public function headings():array
    {
        return [
        [
            'Laporan Anev ICELL'
        ],
        [
            'POLDA/POLRES',
            'TINDAK LANJUT',
            'DALAM PROSES',
            'SELRA',
            '',
            '',
            '',
            'POM/TNI',
            'TINDAK LANJUT',
            'DALAM PROSES',
            'SELRA',
            '',
            '',
            '',
            'POM/TNI',

        ],
        [
            '',
            '',
            '',
            'P21',
            'SP3',
            'DIVERSI',
            'SP2LID',
            '',
            '',
            '',
            'P21',
            'SP3',
            'DIVERSI',
            'SP2LID',
            ''
        ]
        ];
    }


    public function registerEvents():array{

        return[
            AfterSheet::class=>function(AfterSheet $event){
                $event->sheet->getStyle('A1:O3')->applyFromArray([
                    'font' =>[
                        'bold'=> true,
                        'center'=> true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                       'allBorders' =>[
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                     ],
                ]);
                $event->sheet->mergeCells('O2:O3');// POM/TNI tahun ini
                $event->sheet->mergeCells('K2:N2');// selra tahun ini
                $event->sheet->mergeCells('J2:J3');// dalam proses tahun ini
                $event->sheet->mergeCells('I2:I3');// total tahun ini
                $event->sheet->mergeCells('H2:H3');// POM/TNI tahun ini
                $event->sheet->mergeCells('D2:G2');// selra tahun lalu
                $event->sheet->mergeCells('C2:C3');// dalam proses tahun lalu
                $event->sheet->mergeCells('B2:B3');// total tahun lalu
                $event->sheet->mergeCells('A2:A3');// polda polres
                $event->sheet->mergeCells('A1:O1');// laporan anev

                // // TAHUN LALU
                // $event->sheet->mergeCells('A1:U1');// lapor anev
                // $event->sheet->mergeCells('A2:A3');// polda polres
                // $event->sheet->mergeCells('B2:B3');// total tahun lalu
                // $event->sheet->mergeCells('C2:C3');// dalam proses lalu
                // $event->sheet->mergeCells('D2:I2');// selra tahun lalu
                // $event->sheet->mergeCells('J2:K3');// total selra lalu

                // // TAHUN INI
                // $event->sheet->mergeCells('L2:L3');// total tahun lalu
                // $event->sheet->mergeCells('M2:M3');// dalam proses lalu
                // $event->sheet->mergeCells('N2:S2');// selra tahun lalu
                // $event->sheet->mergeCells('T2:U3');// dalam proses lalu


            }
        ];
    }




}
