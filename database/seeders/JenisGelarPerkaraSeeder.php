<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisGelarPerkara;

class JenisGelarPerkaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        JenisGelarPerkara::create([
            'id' => '0',
            'nama_permasalahan' => 'TIDAK DIKETAHUI',
        ]);
        JenisGelarPerkara::create([
            'id' => '1',
            'nama_permasalahan' => 'PENETAPAN TERSANGKA',
        ]);
        JenisGelarPerkara::create([
            'id' => '2',
            'nama_permasalahan' => 'PENINGKATAN STATUS MENJADI PENYIDIKAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '3',
            'nama_permasalahan' => 'PENYITAAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '4',
            'nama_permasalahan' => 'PENAHANAN TERSANGKA',
        ]);
        JenisGelarPerkara::create([
            'id' => '6',
            'nama_permasalahan' => 'PENGGELEDAHAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '7',
            'nama_permasalahan' => 'PENGHENTIAN PENYIDIKAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '8',
            'nama_permasalahan' => 'PENANGKAPAN TERSANGKA',
        ]);
        JenisGelarPerkara::create([
            'id' => '9',
            'nama_permasalahan' => 'BIASA',
        ]);
        JenisGelarPerkara::create([
            'id' => '10',
            'nama_permasalahan' => 'PENGHENTIAN PENYELIDIKAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '11',
            'nama_permasalahan' => 'PENCABUTAN KETETAPAN PENGHENTIAN PENYELIDIKAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '12',
            'nama_permasalahan' => 'PENCABUTAN KETETAPAN PENGHENTIAN PENYIDIKAN',
        ]);
        JenisGelarPerkara::create([
            'id' => '13',
            'nama_permasalahan' => 'PENGHENTIAN PENYELIDIKAN KARENA KEADILAN RESTORATIF',
        ]);
        JenisGelarPerkara::create([
            'id' => '14',
            'nama_permasalahan' => 'PENGHENTIAN PENYIDIKAN KARENA KEADILAN RESTORATIF',
        ]);
        JenisGelarPerkara::create([
            'id' => '15',
            'nama_permasalahan' => 'PERINTAH ATASAN PENYIDIK',
        ]);
        JenisGelarPerkara::create([
            'id' => '16',
            'nama_permasalahan' => 'PENCABUTAN STATUS TERSANGKA',
        ]);
    }
}
