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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use DB;

class ExportReportIndividu implements WithEvents,WithHeadings, ShouldAutoSize, WithStrictNullComparison, FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($poldaId, $polresId, $start_date_then, $end_date_then, $start_date_now, $end_date_now)
    {
        $this->poldaId = $poldaId;
        if($polresId==''){
            $this->polresId = '-';
        }else{
            $this->polresId = $polresId;
        }
        $this->start_date_then = $start_date_then;
        $this->end_date_then = $end_date_then;
        $this->start_date_now = $start_date_now;
        $this->end_date_now = $end_date_now;
    }

    public function collection()
    {
        if($this->start_date_then=='-' && $this->start_date_then==null)
        {
            $data = DB::select("select
            officers.id,
            officers.first_name,
            officers.last_name,
            officers.rank_short_name,
            polres.name as polres_name,
            coalesce(p21_lalu.jumlah_sidik,0) as total_p21_lalu,
            coalesce(sp3_lalu.jumlah_sidik,0) as total_sp3_lalu,
            coalesce(diversi_lalu.jumlah_sidik,0) as total_diversi_lalu,
            coalesce(pom_tni_lalu.jumlah_sidik,0) as total_pom_tni_lalu,
            coalesce(rj_lalu.jumlah_sidik,0) as total_rj_lalu,
            coalesce(dalam_proses_lalu.jumlah_sidik,0) as total_dalam_proses_lalu,
            coalesce(sp2lid_lalu.jumlah_sidik,0) as total_sp2lid_lalu,

            coalesce(p21_kini.jumlah_sidik,0) as total_p21_kini,
            coalesce(sp3_kini.jumlah_sidik,0) as total_sp3_kini,
            coalesce(diversi_kini.jumlah_sidik,0) as total_diversi_kini,
            coalesce(pom_tni_kini.jumlah_sidik,0) as total_pom_tni_kini,
            coalesce(rj_kini.jumlah_sidik,0) as total_rj_kini,
            coalesce(dalam_proses_kini.jumlah_sidik,0) as total_dalam_proses_kini,
            coalesce(sp2lid_kini.jumlah_sidik,0) as total_sp2lid_kini 
            from officers

            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0101' group by surat_penyidikan.officer_id) as p21_lalu on officers.id = p21_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0102' group by surat_penyidikan.officer_id) as sp3_lalu on officers.id = sp3_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0103' group by surat_penyidikan.officer_id) as diversi_lalu on officers.id = diversi_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0104' group by surat_penyidikan.officer_id) as pom_tni_lalu on officers.id = pom_tni_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0106' group by surat_penyidikan.officer_id) as rj_lalu on officers.id = rj_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0107' group by surat_penyidikan.officer_id) as dalam_proses_lalu on officers.id = dalam_proses_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0108' group by surat_penyidikan.officer_id) as sp2lid_lalu on officers.id = sp2lid_lalu.sidik_id


            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0101' group by surat_penyidikan.officer_id) as p21_kini on officers.id = p21_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0102' group by surat_penyidikan.officer_id) as sp3_kini on officers.id = sp3_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0103' group by surat_penyidikan.officer_id) as diversi_kini on officers.id = diversi_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0104' group by surat_penyidikan.officer_id) as pom_tni_kini on officers.id = pom_tni_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0106' group by surat_penyidikan.officer_id) as rj_kini on officers.id = rj_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0107' group by surat_penyidikan.officer_id) as dalam_proses_kini on officers.id = dalam_proses_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0108' group by surat_penyidikan.officer_id) as sp2lid_kini on officers.id = sp2lid_kini.sidik_id

            LEFT JOIN polda on polda.id = officers.polda_id
            LEFT JOIN polres on polres.id = officers.polres_id
            where
            CASE WHEN '$this->poldaId' <> '-' THEN officers.polda_id = '$this->poldaId' ELSE TRUE END
            AND
            CASE WHEN '$this->polresId' <> '-' THEN officers.polres_id = '$this->polresId' ELSE TRUE END
            ");
        }else
        {
            $data = DB::select("select
            officers.id,
            officers.first_name,
            officers.last_name,
            officers.rank_short_name,
            polres.name as polres_name,
            coalesce(p21_lalu.jumlah_sidik,0) as total_p21_lalu,
            coalesce(sp3_lalu.jumlah_sidik,0) as total_sp3_lalu,
            coalesce(diversi_lalu.jumlah_sidik,0) as total_diversi_lalu,
            coalesce(pom_tni_lalu.jumlah_sidik,0) as total_pom_tni_lalu,
            coalesce(rj_lalu.jumlah_sidik,0) as total_rj_lalu,
            coalesce(dalam_proses_lalu.jumlah_sidik,0) as total_dalam_proses_lalu,
            coalesce(sp2lid_lalu.jumlah_sidik,0) as total_sp2lid_lalu,

            coalesce(p21_kini.jumlah_sidik,0) as total_p21_kini,
            coalesce(sp3_kini.jumlah_sidik,0) as total_sp3_kini,
            coalesce(diversi_kini.jumlah_sidik,0) as total_diversi_kini,
            coalesce(pom_tni_kini.jumlah_sidik,0) as total_pom_tni_kini,
            coalesce(rj_kini.jumlah_sidik,0) as total_rj_kini,
            coalesce(dalam_proses_kini.jumlah_sidik,0) as total_dalam_proses_kini,
            coalesce(sp2lid_kini.jumlah_sidik,0) as total_sp2lid_kini
            from officers
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0101' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as p21_lalu on officers.id = p21_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0102' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as sp3_lalu on officers.id = sp3_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0103' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as diversi_lalu on officers.id = diversi_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0104' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as pom_tni_lalu on officers.id = pom_tni_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0106' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as rj_lalu on officers.id = rj_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0107' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as dalam_proses_lalu on officers.id = dalam_proses_lalu.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0108' and accidents.accident_date between '$this->start_date_then' and '$this->end_date_then' group by surat_penyidikan.officer_id) as sp2lid_lalu on officers.id = sp2lid_lalu.sidik_id


            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0101' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as p21_kini on officers.id = p21_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0102' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as sp3_kini on officers.id = sp3_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0103' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as diversi_kini on officers.id = diversi_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0104' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as pom_tni_kini on officers.id = pom_tni_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0106' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as rj_kini on officers.id = rj_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0107' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as dalam_proses_kini on officers.id = dalam_proses_kini.sidik_id
            LEFT JOIN
            (select surat_penyidikan.officer_id as sidik_id,count(accident_id) as jumlah_sidik from surat_penyidikan join accidents on accidents.id = surat_penyidikan.accident_id where accidents.selra_flag = 'S0108' and accidents.accident_date between '$this->start_date_now' and '$this->end_date_now' group by surat_penyidikan.officer_id) as sp2lid_kini on officers.id = sp2lid_kini.sidik_id

            LEFT JOIN polres on polres.id = officers.polres_id
            where
            CASE WHEN '$this->poldaId' <> '-' THEN officers.polda_id = '$this->poldaId' ELSE TRUE END
            AND
            CASE WHEN '$this->polresId' <> '-' THEN officers.polres_id = '$this->polresId' ELSE TRUE END
            ");
        }
        // dd(collect($data));
        return collect($data);
        
    }

    public function headings():array
    {
        return [
        [
            'Laporan Individu'
        ],
        [   
            'NRP',
            'Nama',
            'Nama Belakang',
            'Pangkat',
            'Polres',
            'Periode Tahun Lalu',
            '',
            '',
            '',
            '',
            '',
            '',
            'Periode Tahun Lalu',

        ],
        [
            '',
            '',
            '',
            '',
            '',
            'P21',
            'SP3',
            'DIVERSI',
            'POM / TNI',
            'RJ',
            'DALAM PROSES',
            'SP2LID',
            'P21',
            'SP3',
            'DIVERSI',
            'POM / TNI',
            'RJ',
            'DALAM PROSES',
            'SP2LID'
        ]
        ];
    }

    public function registerEvents():array{
        
        return[
            AfterSheet::class=>function(AfterSheet $event){
                $event->sheet->getStyle('A1:S3')->applyFromArray([
                    'font' =>[
                        'bold'=> true,
                        'center'=> true,
                        'size' => 16,
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
                $event->sheet->mergeCells('A1:S1');
                $event->sheet->mergeCells('A2:A3');
                $event->sheet->mergeCells('B2:B3');
                $event->sheet->mergeCells('C2:C3');
                $event->sheet->mergeCells('D2:D3');
                $event->sheet->mergeCells('E2:E3');
                $event->sheet->mergeCells('F2:L2');
                $event->sheet->mergeCells('M2:S2');
                
            }
        ];
    }
}
