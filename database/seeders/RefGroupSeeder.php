<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RefGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\RefGroup::create([
            'id' => 'A05A',
            'name' => 'Titik Acuan / Referensi',
            'state' => '1',
        ]);

         \App\Models\RefGroup::create([
            'id' => 'A07',
            'name' => 'Tipe Kecelakaan',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'PRP1',
            'name' => 'Kerusakan Material/Infrastruktur',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'R09',
            'name' => 'Pengaturan Simpang',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'A08',
            'name' => 'Kondisi Cahaya',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'S01',
            'name' => 'Penyelesaian Perkara',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'G01',
            'name' => 'Jenis Kelamin',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'G02',
            'name' => 'Jenis Identitas',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'E01',
            'name' => 'Pendidikan',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'R01',
            'name' => 'Agama',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D01',
            'name' => 'tugas',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D02',
            'name' => 'saksi',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D03',
            'name' => 'tersangka',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D04',
            'name' => 'penahanan',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D05',
            'name' => 'penggeledahan',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D06',
            'name' => 'penyitaan',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D07',
            'name' => 'penyegelan',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D08',
            'name' => 'labfor',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D09',
            'name' => 'rekening-bank',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'D10',
            'name' => 'dpo-dpb',
            'state' => '1',
        ]);

        \App\Models\RefGroup::create([
            'id' => 'RANK',
            'name' => 'Pangkat Petugas Kepolisian',
            'state' => '1',
        ]);

    }
}
