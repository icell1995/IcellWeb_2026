<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\CrimeConstitution;

class CrimeConstitutionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $crimeConstitutions = [
            [
                'id' => '1',
                'crime_type_id' => '1',
                'emp_id' => NULL,
                'name' => 'UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
                'chapter' => 'Pasal 273',
                'description' => '(1) Setiap penyelenggara Jalan yang tidak dengan segera dan patut memperbaiki Jalan yang rusak yang
                                        mengakibatkan Kecelakaan Lalu Lintas sebagaimana dimaksud dalam Pasal 24 ayat (1) sehingga menimbulkan
                                        korban luka ringan dan/atau kerusakan Kendaraan dan/atau barang dipidana dengan penjara paling lama 6
                                        (enam) bulan atau denda paling banyak Rp12.000.000,00 (dua belas juta rupiah). <br> 
                                  (2) Dalam hal perbuatan sebagaimana dimaksud pada ayat (1) mengakibatkan luka berat, pelaku dipidana dengan
                                        pidana penjara paling lama 1 (satu) tahun atau denda paling banyak Rp24.000.000,00 (dua puluh empat juta rupiah). <br>
                                  (3) Dalam hal perbuatan sebagaimana dimaksud pada ayat (1) mengakibatkan orang lain meninggal dunia, pelaku
                                        dipidana dengan pidana penjara paling lama 5 (lima) tahun atau denda paling banyak Rp120.000.000,00 (seratus dua puluh juta rupiah). <br>
                                  (4) Penyelenggara Jalan yang tidak memberi tanda atau rambu pada Jalan yang rusak dan belum diperbaiki sebagaimana dimaksud dalam Pasal 24 ayat (2) dipidana
                                  dengan pidana penjara paling lama 6 (enam) bulan atau denda paling banyak Rp1.500.000,00 (satu juta lima ratus ribu rupiah). <br>',
                'code' => 'CC-0001',
            ],
            [
                'id' => '2',
                'emp_id' => NULL,
                'crime_type_id' => '2',
                'name' => 'UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
                'chapter' => 'Pasal 275 Ayat (2)',
                'description' => '(2) Setiap orang yang merusak Rambu Lalu Lintas, Marka Jalan, Alat Pemberi Isyarat Lalu Lintas, fasilitas Pejalan
                                        Kaki, dan alat pengaman Pengguna Jalan sehingga tidak berfungsi sebagaimana dimaksud dalam Pasal 28 ayat (2) 
                                        dipidana dengan pidana penjara paling lama 2 (dua) tahun atau denda paling banyak Rp50.000.000,00 (lima puluh juta rupiah). ',
                'code' => 'CC-0002',
            ],
            [
                'id' => '3',
                'emp_id' => NULL,
                'crime_type_id' => '3',
                'name' => 'UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
                'chapter' => 'Pasal 277',
                'description' => 'Setiap orang yang memasukkan Kendaraan Bermotor, kereta gandengan, dan kereta tempelan ke dalam wilayah Republik Indonesia, membuat, merakit, atau memodifikasi Kendaraan
                                    Bermotor yang menyebabkan perubahan tipe, kereta gandengan, kereta tempelan, dan kendaraan khusus yang dioperasikan di dalam negeri yang tidak memenuhi kewajiban
                                    uji tipe sebagaimana dimaksud dalam Pasal 50 ayat (1) dipidana dengan pidana penjara paling lama 1 (satu) tahun atau denda paling banyak Rp24.000.000,00 (dua puluh empat juta rupiah).',
                'code' => 'CC-0003',
            ],
            [
                'id' => '4',
                'emp_id' => NULL,
                'crime_type_id' => '4',
                'name' => 'UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
                'chapter' => 'Pasal 310',
                'description' => '(1) Setiap orang yang mengemudikan Kendaraan Bermotor yang karena kelalaiannya mengakibatkan Kecelakaan Lalu Lintas dengan kerusakan Kendaraan dan/atau
                                        barang sebagaimana dimaksud dalam Pasal 229 ayat (2), dipidana dengan pidana penjara paling lama 6 (enam) bulan dan/atau denda paling banyak Rp1.000.000,00
                                        (satu juta rupiah). <br> 
                                  (2) Setiap orang yang mengemudikan Kendaraan Bermotor yang karena kelalaiannya mengakibatkan Kecelakaan Lalu Lintas dengan korban luka ringan dan kerusakan
                                        Kendaraan dan/atau barang sebagaimana dimaksud dalam Pasal 229 ayat (3), dipidana dengan pidana penjara paling lama 1 (satu) tahun dan/atau denda
                                        paling banyak Rp2.000.000,00 (dua juta rupiah). <br>
                                  (3) Setiap orang yang mengemudikan Kendaraan Bermotor yang karena kelalaiannya mengakibatkan Kecelakaan Lalu Lintas dengan korban luka berat sebagaimana
                                        dimaksud dalam Pasal 229 ayat (4), dipidana dengan pidana penjara paling lama 5 (lima) tahun dan/atau denda paling banyak Rp10.000.000,00 (sepuluh juta
                                        rupiah). <br>
                                  (4) Dalam hal kecelakaan sebagaimana dimaksud pada ayat (3) yang mengakibatkan orang lain meninggal dunia, dipidana dengan pidana penjara paling lama 6 (enam)
                                        tahun dan/atau denda paling banyak Rp12.000.000,00 (dua belas juta rupiah). <br>',
                'code' => 'CC-0004',
            ],
            [
                'id' => '5',
                'emp_id' => NULL,
                'crime_type_id' => '5',
                'name' => 'UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
                'chapter' => 'Pasal 311',
                'description' => '(1) Setiap orang yang dengan sengaja mengemudikan Kendaraan Bermotor dengan cara atau keadaan yang membahayakan bagi nyawa atau barang dipidana dengan
                                        pidana penjara paling lama 1 (satu) tahun atau denda paling banyak Rp3.000.000,00 (tiga juta rupiah). <br> 
                                  (2) Dalam hal perbuatan sebagaimana dimaksud pada ayat (1) mengakibatkan Kecelakaan Lalu Lintas dengan kerusakan Kendaraan dan/atau barang sebagaimana
                                        dimaksud dalam Pasal 229 ayat (2), pelaku dipidana dengan pidana penjara paling lama 2 (dua) tahun atau denda paling banyak Rp4.000.000,00 (empat juta
                                        rupiah). <br>
                                  (3) Dalam hal perbuatan sebagaimana dimaksud pada ayat (1) mengakibatkan Kecelakaan Lalu Lintas dengan korban luka ringan dan kerusakan Kendaraan dan/atau
                                        barang sebagaimana dimaksud dalam Pasal 229 ayat (3), pelaku dipidana dengan pidana penjara paling lama 4 (empat) tahun atau denda paling banyak Rp8.000.000,00
                                        (delapan juta rupiah). <br>
                                  (4) Dalam hal perbuatan sebagaimana dimaksud pada ayat (1) mengakibatkan Kecelakaan Lalu Lintas dengan korban luka berat sebagaimana dimaksud dalam Pasal
                                        229 ayat (4), pelaku dipidana dengan pidana penjara paling lama 10 (sepuluh) tahun atau denda paling banyak Rp20.000.000,00 (dua puluh juta rupiah). <br>
                                  (5) Dalam hal perbuatan sebagaimana dimaksud pada ayat (4) mengakibatkan orang lain meninggal dunia, pelaku dipidana dengan pidana penjara paling lama 12 (dua
                                  belas) tahun atau denda paling banyak Rp24.000.000,00 (dua puluh empat juta rupiah). <br>',
                'code' => 'CC-0005',
            ],
            [
                'id' => '6',
                'emp_id' => NULL,
                'crime_type_id' => '6',
                'name' => 'UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
                'chapter' => 'Pasal 312',
                'description' => 'Setiap orang yang mengemudikan Kendaraan Bermotor yang terlibat Kecelakaan Lalu Lintas dan dengan sengaja tidak menghentikan kendaraannya, tidak memberikan pertolongan,
                                    atau tidak melaporkan Kecelakaan Lalu Lintas kepada Kepolisian Negara Republik Indonesia terdekat sebagaimana dimaksud dalam Pasal 231 ayat (1) huruf a, huruf b, dan 
                                    huruf c tanpa alasan yang patut dipidana dengan pidana penjara paling lama 3 (tiga) tahun atau denda paling banyak Rp75.000.000,00 (tujuh puluh lima juta rupiah).',
                'code' => 'CC-0006',
            ],
            
        ];

        DB::beginTransaction();
        try{
            foreach ($crimeConstitutions as $crimeConstitution) {
                CrimeConstitution::updateOrCreate(
                    [
                        'id' => $crimeConstitution['id']
                    ],
                    $crimeConstitution
                );
            }

            DB::commit();

            $this->command->info('Berhasil menambahkan data ke tabel lib.crime_constitutions');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
