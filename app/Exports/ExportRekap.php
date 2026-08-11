<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Accident;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportRekap implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct($rekap){
        $this->rekap = $rekap;
    }
    public function collection()
    {
        // dd($this->rekap);
        return collect($this->rekap) ;
    }
    public function headings():array
    {
        return[
            'No LP',
            'Tanggal Kejadian',
            'Tanggal Tindak Lanjut',
            'Proses Selama',
            'Tanggal Aktivitas Terakhir	',
            'Aktivitas',
            'Status'
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
