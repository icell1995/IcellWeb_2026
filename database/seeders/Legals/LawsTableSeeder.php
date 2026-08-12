<?php

namespace Database\Seeders\Legals;

use Illuminate\Database\Seeder;

use App\Models\Meta\Legals\Law;

class LawsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $laws = collect([
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 273',
                'verse' => '',
                'description' => 'PENYELENGGARA JALAN, JALAN YANG RUSAK',
                'sort' => 1
            ],
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 275',
                'verse' => 'Ayat 2',
                'description' => 'PERUSAKAN RAMBU',
                'sort' => 2
            ],
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 277',
                'verse' => '',
                'description' => 'OVERDIMENSI',
                'sort' => 3
            ],
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 310',
                'verse' => '',
                'description' => 'LAKA LANTAS, LALAI',
                'sort' => 4
            ],
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 311',
                'verse' => '',
                'description' => 'LAKA LANTAS, SENGAJA',
                'sort' => 5
            ],
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 312',
                'verse' => '',
                'description' => 'TABRAK LARI',
                'sort' => 6
            ],
            [
                'law' => 'UU no.22 tahun 2009 UULAJ',
                'chapter' => 'Pasal 316',
                'verse' => '',
                'description' => 'TENTANG TINDAK PIDANA KEJAHATAN LALU LINTAS',
                'sort' => 7
            ]
        ]);

        $laws->each(function ($law) {
            Law::create([
                'law' => $law['law'],
                'chapter' => $law['chapter'],
                'verse' => $law['verse'],
                'description' => $law['description'],
            ]);
        });
    }
}
