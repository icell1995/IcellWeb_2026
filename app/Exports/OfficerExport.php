<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OfficerExport implements FromCollection, WithHeadings, ShouldAutoSize
{

    public function __construct($petugas){
        $this->petugas = $petugas;
    }


    public function collection()
    {
        return collect($this->petugas);
    }

    public function headings():array
    {
        return [
            "NRP",
            "Nama Depan",
            "Nama Belakang",
            "Pangkat",
            "Polres",
            "Posisi",
            "Polda",
            
        ];
    }

    public function registerEvents():array{
        return[
            AfterSheet::class=>function(AfterSheet $event){
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' =>[
                        'bold'=> true,
                        'center'=> true,
                        'size' => 20,
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
            }
        ];
    }
}
