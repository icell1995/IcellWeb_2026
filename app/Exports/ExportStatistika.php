<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use DB;
use App\Models\Accident;
use App\Models\Polres;
use App\Models\Polda;
use App\Models\Ref;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStatistika implements FromCollection,WithHeadings, ShouldAutoSize, WithEvents, WithStrictNullComparison
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    protected $polres;
    protected $status;
    protected $bulan;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function collection()
    {
        return collect($this->data) ;
    }

    public function headings():array
    {
        return [
        [
            'Laporan Data'
        ],
        [
            'Nomor LP',
            'Polres',
            'Polda',
            'Korban',
            '',
            '',
            'Tanggal tindak lanjut',
            'Jenis Laka',
            'Golongan Laka',
            'Jumlah Ranmor Terlibat',
            'Status',

        ],
        [
            '',
            '',
            '',
            'Meninggal Dunia',
            'Luka Berat',
            'Luka Ringan',
        ]
        ];
    }


    public function registerEvents():array{

        return[
            AfterSheet::class=>function(AfterSheet $event){
                $event->sheet->getStyle('A1:K3')->applyFromArray([
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
                $event->sheet->mergeCells('D2:F2');
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->mergeCells('A2:A3');
                $event->sheet->mergeCells('B2:B3');
                $event->sheet->mergeCells('C2:C3');
                $event->sheet->mergeCells('G2:G3');
                $event->sheet->mergeCells('H2:H3');
                $event->sheet->mergeCells('I2:I3');
                $event->sheet->mergeCells('J2:J3');
                $event->sheet->mergeCells('K2:K3');

            }
        ];
    }
}
