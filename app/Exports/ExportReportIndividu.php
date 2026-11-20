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
use Illuminate\Support\Facades\DB;

class ExportReportIndividu implements WithEvents,WithHeadings, ShouldAutoSize, WithStrictNullComparison, FromCollection
{
    protected string $poldaId;
    protected string $polresId;
    protected ?string $start_date_then;
    protected ?string $end_date_then;
    protected ?string $start_date_now;
    protected ?string $end_date_now;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($poldaId, $polresId, $start_date_then, $end_date_then, $start_date_now, $end_date_now)
    {
        $this->poldaId = $poldaId !== null ? preg_replace('/[^A-Za-z0-9_-]/', '', (string)$poldaId) : '-';
        if ($this->poldaId === '') {
            $this->poldaId = '-';
        }

        $this->polresId = $polresId !== null ? preg_replace('/[^A-Za-z0-9_-]/', '', (string)$polresId) : '-';
        if ($this->polresId === '') {
            $this->polresId = '-';
        }

        $this->start_date_then = !empty($start_date_then) && $start_date_then !== '-' ? date('Y-m-d', strtotime($start_date_then)) : null;
        $this->end_date_then   = !empty($end_date_then) && $end_date_then !== '-' ? date('Y-m-d', strtotime($end_date_then)) : null;
        $this->start_date_now  = !empty($start_date_now) && $start_date_now !== '-' ? date('Y-m-d', strtotime($start_date_now)) : null;
        $this->end_date_now    = !empty($end_date_now) && $end_date_now !== '-' ? date('Y-m-d', strtotime($end_date_now)) : null;
    }

    public function collection()
    {
        if ($this->start_date_then == '-' || $this->start_date_then === null) {
            $data = DB::select("select
            officers.id,
            officers.first_name,
            officers.last_name,
            officers.rank_short_name,
            polres.name as polres_name,
            coalesce(sidik_agg.total_p21, 0) as total_p21_lalu,
            coalesce(sidik_agg.total_sp3, 0) as total_sp3_lalu,
            coalesce(sidik_agg.total_diversi, 0) as total_diversi_lalu,
            coalesce(sidik_agg.total_pom_tni, 0) as total_pom_tni_lalu,
            coalesce(sidik_agg.total_rj, 0) as total_rj_lalu,
            coalesce(sidik_agg.total_dalam_proses, 0) as total_dalam_proses_lalu,
            coalesce(sidik_agg.total_sp2lid, 0) as total_sp2lid_lalu,

            coalesce(sidik_agg.total_p21, 0) as total_p21_kini,
            coalesce(sidik_agg.total_sp3, 0) as total_sp3_kini,
            coalesce(sidik_agg.total_diversi, 0) as total_diversi_kini,
            coalesce(sidik_agg.total_pom_tni, 0) as total_pom_tni_kini,
            coalesce(sidik_agg.total_rj, 0) as total_rj_kini,
            coalesce(sidik_agg.total_dalam_proses, 0) as total_dalam_proses_kini,
            coalesce(sidik_agg.total_sp2lid, 0) as total_sp2lid_kini
            from officers
            LEFT JOIN (
                SELECT
                    surat_penyidikan.officer_id as sidik_id,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0101') as total_p21,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0102') as total_sp3,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0103') as total_diversi,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0104') as total_pom_tni,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0106') as total_rj,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0107') as total_dalam_proses,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0108') as total_sp2lid
                FROM surat_penyidikan
                JOIN accidents ON accidents.id = surat_penyidikan.accident_id
                GROUP BY surat_penyidikan.officer_id
            ) as sidik_agg on officers.id = sidik_agg.sidik_id
            LEFT JOIN polda on polda.id = officers.polda_id
            LEFT JOIN polres on polres.id = officers.polres_id
            where
            CASE WHEN ? <> '-' THEN officers.polda_id = ? ELSE TRUE END
            AND
            CASE WHEN ? <> '-' THEN officers.polres_id = ? ELSE TRUE END
            ", [
                $this->poldaId, $this->poldaId,
                $this->polresId, $this->polresId,
            ]);
        } else {
            $periodSubSql = "
                SELECT
                    surat_penyidikan.officer_id as sidik_id,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0101') as total_p21,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0102') as total_sp3,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0103') as total_diversi,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0104') as total_pom_tni,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0106') as total_rj,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0107') as total_dalam_proses,
                    COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0108') as total_sp2lid
                FROM surat_penyidikan
                JOIN accidents ON accidents.id = surat_penyidikan.accident_id
                WHERE accidents.accident_date BETWEEN ? AND ?
                GROUP BY surat_penyidikan.officer_id
            ";
            $data = DB::select("select
            officers.id,
            officers.first_name,
            officers.last_name,
            officers.rank_short_name,
            polres.name as polres_name,
            coalesce(agg_lalu.total_p21, 0) as total_p21_lalu,
            coalesce(agg_lalu.total_sp3, 0) as total_sp3_lalu,
            coalesce(agg_lalu.total_diversi, 0) as total_diversi_lalu,
            coalesce(agg_lalu.total_pom_tni, 0) as total_pom_tni_lalu,
            coalesce(agg_lalu.total_rj, 0) as total_rj_lalu,
            coalesce(agg_lalu.total_dalam_proses, 0) as total_dalam_proses_lalu,
            coalesce(agg_lalu.total_sp2lid, 0) as total_sp2lid_lalu,

            coalesce(agg_kini.total_p21, 0) as total_p21_kini,
            coalesce(agg_kini.total_sp3, 0) as total_sp3_kini,
            coalesce(agg_kini.total_diversi, 0) as total_diversi_kini,
            coalesce(agg_kini.total_pom_tni, 0) as total_pom_tni_kini,
            coalesce(agg_kini.total_rj, 0) as total_rj_kini,
            coalesce(agg_kini.total_dalam_proses, 0) as total_dalam_proses_kini,
            coalesce(agg_kini.total_sp2lid, 0) as total_sp2lid_kini
            from officers
            LEFT JOIN ({$periodSubSql}) as agg_lalu on officers.id = agg_lalu.sidik_id
            LEFT JOIN ({$periodSubSql}) as agg_kini on officers.id = agg_kini.sidik_id
            LEFT JOIN polres on polres.id = officers.polres_id
            where
            CASE WHEN ? <> '-' THEN officers.polda_id = ? ELSE TRUE END
            AND
            CASE WHEN ? <> '-' THEN officers.polres_id = ? ELSE TRUE END
            ", [
                $this->start_date_then, $this->end_date_then,
                $this->start_date_now, $this->end_date_now,
                $this->poldaId, $this->poldaId,
                $this->polresId, $this->polresId,
            ]);
        }
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
