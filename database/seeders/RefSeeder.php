<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //titik acuan
        \App\Models\Ref::create([
            'id' => 'A05A00',
            'name' => 'Tempat Ibadah',
            'grp_id' => 'A05A',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A01',
            'name' => 'Gedung Bisnis/ Hotel/ Apartment',
            'grp_id' => 'A05A',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A02',
            'name' => 'Gereja',
            'grp_id' => 'A05A',
            'sort' => '3',
            'state' => '0',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A03',
            'name' => 'Jembatan',
            'grp_id' => 'A05A',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A04',
            'name' => 'Kantor/ Perkantoran',
            'grp_id' => 'A05A',
            'sort' => '5',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A05',
            'name' => 'Masjid',
            'grp_id' => 'A05A',
            'sort' => '6',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A06',
            'name' => 'Toko/ Pertokoan / Pasar',
            'grp_id' => 'A05A',
            'sort' => '7',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A07',
            'name' => 'Pohon',
            'grp_id' => 'A05A',
            'sort' => '8',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A08',
            'name' => 'Pura',
            'grp_id' => 'A05A',
            'sort' => '9',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A09',
            'name' => 'Rumah / Perumahan / Pemukiman',
            'grp_id' => 'A05A',
            'sort' => '10',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'A05A10',
            'name' => 'Rumah Sakit',
            'grp_id' => 'A05A',
            'sort' => '11',
            'state' => '1',
        ]);

        //gender
        \App\Models\Ref::create([
            'id' => 'G0102',
            'name' => 'PRIA',
            'grp_id' => 'G01',
            'sort' => '2',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'G0103',
            'name' => 'WANITA',
            'grp_id' => 'G01',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'G0101',
            'name' => 'TIDAK DIKETAHUI',
            'grp_id' => 'G01',
            'sort' => '1',
            'state' => '1',
        ]);


        //jenis_identitas
        \App\Models\Ref::create([
            'id' => 'G0202',
            'name' => 'KTP',
            'grp_id' => 'G02',
            'sort' => '2',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'G0203',
            'name' => 'SIM',
            'grp_id' => 'G02',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'G0201',
            'name' => 'TIDAK DIKETAHUI',
            'grp_id' => 'G02',
            'sort' => '1',
            'state' => '1',
        ]);


        //pendidikan
        \App\Models\Ref::create([
            'id' => 'E0101',
            'name' => 'TIDAK DIKETAHUI',
            'grp_id' => 'E01',
            'sort' => '1',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'E0102',
            'name' => 'SD',
            'grp_id' => 'E01',
            'sort' => '2',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'E0103',
            'name' => 'SMP',
            'grp_id' => 'E01',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'E0104',
            'name' => 'SMA / SMK',
            'grp_id' => 'E01',
            'sort' => '4',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'E0105',
            'name' => 'S1',
            'grp_id' => 'E01',
            'sort' => '5',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'E0106',
            'name' => 'S2',
            'grp_id' => 'E01',
            'sort' => '6',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'E0107',
            'name' => 'S3',
            'grp_id' => 'E01',
            'sort' => '7',
            'state' => '1',
        ]);

        //religi
        \App\Models\Ref::create([
            'id' => 'R0101',
            'name' => 'TIDAK DIKETAHUI',
            'grp_id' => 'R01',
            'sort' => '1',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'R0102',
            'name' => 'ISLAM',
            'grp_id' => 'R01',
            'sort' => '2',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'R0103',
            'name' => 'KRISTEN',
            'grp_id' => 'R01',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'R0104',
            'name' => 'KATOLIK',
            'grp_id' => 'R01',
            'sort' => '4',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'R0105',
            'name' => 'HINDU',
            'grp_id' => 'R01',
            'sort' => '5',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'R0106',
            'name' => 'BUDHA',
            'grp_id' => 'R01',
            'sort' => '6',
            'state' => '1',
        ]);


        //tipe kecelakaan
          \App\Models\Ref::create([
            'id' => 'A0701',
            'name' => 'Di simpang, pejalan kaki menyeberang dari kiri ke kanan',
            'grp_id' => 'A07',
            'sort' => '1',
            'state' => '1',
        ]);

        //cuaca
           \App\Models\Ref::create([
            'id' => 'A0801',
            'name' => 'Gelap / Sulit Terlihat',
            'grp_id' => 'A08',
            'sort' => '1',
            'state' => '1',
        ]);

        //Kerusakan
        \App\Models\Ref::create([
            'id' => 'PRP101',
            'name' => 'Alat Pekerjaan Jalan',
            'grp_id' => 'PRP1',
            'sort' => '1',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'R0901',
            'name' => 'APILL (Berfungsi)',
            'grp_id' => 'R09',
            'sort' => '1',
            'state' => '1',
        ]);

        //SELRA
        \App\Models\Ref::create([
            'id' => 'S0101',
            'name' => 'P21',
            'grp_id' => 'S01',
            'sort' => '1',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'S0102',
            'name' => 'SP3',
            'grp_id' => 'S01',
            'sort' => '2',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'S0103',
            'name' => 'Diversi',
            'grp_id' => 'S01',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'S0104',
            'name' => 'POM/TNI',
            'grp_id' => 'S01',
            'sort' => '4',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'S0106',
            'name' => 'RJ',
            'grp_id' => 'S01',
            'sort' => '5',
            'state' => '1',
        ]);

        // \App\Models\Ref::create([
        //     'id' => 'S0106',
        //     'name' => 'RJ',
        //     'grp_id' => 'S01',
        //     'sort' => '6',
        //     'state' => '1',
        // ]);

        \App\Models\Ref::create([
            'id' => 'S0107',
            'name' => 'DALAM PROSES',
            'grp_id' => 'S01',
            'sort' => '6',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'S0108',
            'name' => 'SP2LID',
            'grp_id' => 'S01',
            'sort' => '7',
            'state' => '1',
        ]);

        //KATEGORI 1
        \App\Models\Ref::create([
            'id' => 'D010101',
            'name' => 'SURAT PERINTAH TUGAS',
            'grp_id' => 'D01',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D010102',
            'name' => 'SURAT PERINTAH PENYELIDIKAN',
            'grp_id' => 'D01',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D010103',
            'name' => 'SURAT PERINTAH PENYIDIKAN',
            'grp_id' => 'D01',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010104',
            'name' => 'SURAT PEMBERITAHUAN DIMULAINYA PENYIDIKAN',
            'grp_id' => 'D01',
            'sort' => '4',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010105',
            'name' => 'LP',
            'grp_id' => 'D01',
            'sort' => '5',
            'state' => '1',
        ]);
     
        \App\Models\Ref::create([
            'id' => 'D010106',
            'name' => 'BERITA ACARA PENANGKAPAN',
            'grp_id' => 'D01',
            'sort' => '6',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010114',
            'name' => 'SP2HP',
            'grp_id' => 'D01',
            'sort' => '14',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010108',
            'name' => 'BERITA ACARA PENGAMBILAN DARAH',
            'grp_id' => 'D01',
            'sort' => '8',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010109',
            'name' => 'LAPORAN HASIL PENYELIDIKAN',
            'grp_id' => 'D01',
            'sort' => '9',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010110',
            'name' => 'BERITA ACARA INTROGASI',
            'grp_id' => 'D01',
            'sort' => '10',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010111',
            'name' => 'LHGP (PENETAPAN TERSANGKA)',
            'grp_id' => 'D01',
            'sort' => '11',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D010112',
            'name' => 'SPDP UPLOAD',
            'grp_id' => 'D01',
            'sort' => '12',
            'state' => '1',
        ]);

        //KATEGORI 2
        \App\Models\Ref::create([
            'id' => 'D020101',
            'name' => 'SURAT PERINTAH MEMBAWA SAKSI',
            'grp_id' => 'D02',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D020102',
            'name' => 'BERITA ACARA MEMBAWA DAN MENGHADAPKAN SAKSI',
            'grp_id' => 'D02',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D020103',
            'name' => 'BERITA ACARA PENYUMPAHAN SAKSI / AHLI',
            'grp_id' => 'D02',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D020104',
            'name' => 'BERITA ACARA PEMERIKSAAN SAKSI',
            'grp_id' => 'D02',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D020105',
            'name' => 'BERITA ACARA PEMERIKSAAN AHLI',
            'grp_id' => 'D02',
            'sort' => '5',
            'state' => '1',
        ]);

        //KATEGORI 3
        \App\Models\Ref::create([
            'id' => 'D030101',
            'name' => 'SURAT PANGGILAN TERSANGKA',
            'grp_id' => 'D03',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030102',
            'name' => 'SURAT PERINTAH PENANGKAPAN',
            'grp_id' => 'D03',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030103',
            'name' => 'BERITA ACARA PEMERIKASAAN TERSANGKA',
            'grp_id' => 'D03',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030104',
            'name' => 'BERITA ACARA KONFRONTASI',
            'grp_id' => 'D03',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030105',
            'name' => 'BERITA ACARA REKONTRUKSI',
            'grp_id' => 'D03',
            'sort' => '5',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030106',
            'name' => 'SKET TKP LAKA LANTAS',
            'grp_id' => 'D03',
            'sort' => '6',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030107',
            'name' => 'SURAT BANTUAN PENANGKAPAN',
            'grp_id' => 'D03',
            'sort' => '7',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030108',
            'name' => 'BERITA ACARA PENYERAHAN TERSANGKA',
            'grp_id' => 'D03',
            'sort' => '8',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D030109',
            'name' => 'BERITA ACARA PELEPASAN TERSANGKA',
            'grp_id' => 'D03',
            'sort' => '9',
            'state' => '1',
        ]);

        //KATEGORI 4
        \App\Models\Ref::create([
            'id' => 'D040101',
            'name' => 'SURAT PERINTAH PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040102',
            'name' => 'BERITA ACARA PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040103',
            'name' => 'PERMINTAAN PERPANJANGAN PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040104',
            'name' => 'SURAT PERINTAH PERPANJANGAN PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040105',
            'name' => 'BERITA ACARA PENGELUARAN PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '5',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040106',
            'name' => 'SURAT PEMBATALAN PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '6',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040107',
            'name' => 'SURAT PENCABUTAN PEMBATALAN PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '7',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040108',
            'name' => 'BERITA ACARA PENCABUTAN PEMBATALAN PENAHANAN',
            'grp_id' => 'D04',
            'sort' => '8',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040109',
            'name' => 'SURAT PERINTAH PENAHANAN LANJUTAN',
            'grp_id' => 'D04',
            'sort' => '9',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D040110',
            'name' => 'BERITA ACARA PERINTAH PENAHANAN LANJUTAN',
            'grp_id' => 'D04',
            'sort' => '10',
            'state' => '1',
        ]);

        //KATEGORI 5
        \App\Models\Ref::create([
            'id' => 'D050101',
            'name' => 'SURAT PERMINTAAN IZIN PENGGELEDAHAN',
            'grp_id' => 'D05',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D050102',
            'name' => 'SURAT PERINTAH PENGGELEDAHAN',
            'grp_id' => 'D05',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D050103',
            'name' => 'SURAT PERSETUJUAN PENGGELEDAHAN',
            'grp_id' => 'D05',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D050104',
            'name' => 'BERITA ACARA PENGGELEDAHAN',
            'grp_id' => 'D05',
            'sort' => '4',
            'state' => '1',
        ]);

        //KATEGORI 6
        \App\Models\Ref::create([
            'id' => 'D060101',
            'name' => 'SURAT IZIN PENYITAAN',
            'grp_id' => 'D06',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060102',
            'name' => 'SURAT PERSETUJUAN PENYITAAN',
            'grp_id' => 'D06',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060103',
            'name' => 'DAFTAR BARANG BUKTI',
            'grp_id' => 'D06',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060104',
            'name' => 'SURAT PERINTAH PENYITAAN',
            'grp_id' => 'D06',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060105',
            'name' => 'BERITA ACARA PENYITAAN',
            'grp_id' => 'D06',
            'sort' => '5',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060106',
            'name' => 'SURAT PENGIRIMAN BERKAS PERKARA',
            'grp_id' => 'D06',
            'sort' => '6',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060107',
            'name' => 'TANDA TERIMA BERKAS PERKARA',
            'grp_id' => 'D06',
            'sort' => '7',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060108',
            'name' => 'SURAT PENGIRIMAN TERASANGKA DAN BARANG BUKTI',
            'grp_id' => 'D06',
            'sort' => '8',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060109',
            'name' => 'BERITA ACARA SERAH TERIMA TERSANGKA DAN BARANG BUKTI',
            'grp_id' => 'D06',
            'sort' => '9',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060110',
            'name' => 'SURAT BANTUAN PENYELIDIKAN',
            'grp_id' => 'D06',
            'sort' => '10',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060111',
            'name' => 'SURAT PERINTAH PENITIPAN RAWAT BARANG BUKTI',
            'grp_id' => 'D06',
            'sort' => '11',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060112',
            'name' => 'SURAT PERINTAH PENGEMBALIAN BENDA SITAAN',
            'grp_id' => 'D06',
            'sort' => '12',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060113',
            'name' => 'BERITA ACARA PENITIPAN RAWAT BARANG BUKTI',
            'grp_id' => 'D06',
            'sort' => '13',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060114',
            'name' => 'BERITA ACARA PENGEMBALIAN BENDA SITAAN',
            'grp_id' => 'D06',
            'sort' => '14',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060115',
            'name' => 'KETETAPAN IJIN PENYITAAN',
            'grp_id' => 'D06',
            'sort' => '15',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060116',
            'name' => 'KETETAPAN PERSETUJUAN PENYITAAN',
            'grp_id' => 'D06',
            'sort' => '16',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060117',
            'name' => 'SURAT TANDA PENERIMAAN',
            'grp_id' => 'D06',
            'sort' => '17',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060118',
            'name' => 'SURAT PENGANTAR',
            'grp_id' => 'D06',
            'sort' => '18',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060119',
            'name' => 'BERITA ACARA PENYERAHAN BERKAS PERKARA',
            'grp_id' => 'D06',
            'sort' => '19',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060120',
            'name' => 'LAPORAN HASIL GELAR PERKARA',
            'grp_id' => 'D06',
            'sort' => '20',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D060121',
            'name' => 'LAPORAN HASIL GELAR PERKARA KHUSUS',
            'grp_id' => 'D06',
            'sort' => '21',
            'state' => '1',
        ]);

        //KATEGORI 7
        \App\Models\Ref::create([
            'id' => 'D070101',
            'name' => 'SURAT PERSETUJUAN PENYEGELAN',
            'grp_id' => 'D07',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D070102',
            'name' => 'BERITA ACARA PENYEGELAN',
            'grp_id' => 'D07',
            'sort' => '2',
            'state' => '1',
        ]);

        //KATEGORI 8
        \App\Models\Ref::create([
            'id' => 'D080101',
            'name' => 'SURAT PERMINTAAN BANTUAN LABFOR',
            'grp_id' => 'D08',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D080102',
            'name' => 'SURAT HASIL PEMERIKSAAN LABFOR',
            'grp_id' => 'D08',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D080103',
            'name' => 'SURAT PERMINTAAN BANTUAN LABFOR IDENTIFIKASI',
            'grp_id' => 'D08',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D080104',
            'name' => 'SURAT HASIL PEMERIKSAAN IDENTIFIKASI',
            'grp_id' => 'D08',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D080105',
            'name' => 'KETETAPAN IJIN KHUSUS PEMERIKSAAN SURAT',
            'grp_id' => 'D08',
            'sort' => '5',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D080106',
            'name' => 'SURAT PERINTAH PEMERIKSAAN SURAT',
            'grp_id' => 'D08',
            'sort' => '6',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D080107',
            'name' => 'BERITA ACARA PEMERIKSAAN SURAT',
            'grp_id' => 'D08',
            'sort' => '7',
            'state' => '1',
        ]);

        //KATEGORI 9
        \App\Models\Ref::create([
            'id' => 'D090101',
            'name' => 'SURAT PEMBLOKIRAN REKENING BANK',
            'grp_id' => 'D09',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D090102',
            'name' => 'BERITA ACARA PEMBLOKIRAN REKENING BANK',
            'grp_id' => 'D09',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D090103',
            'name' => 'SURAT PEMBUKAAN BLOKIR REKENING BANK',
            'grp_id' => 'D09',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D090104',
            'name' => 'BERITA ACARA PEMBUKAAN BLOKIR REKENING BANK',
            'grp_id' => 'D09',
            'sort' => '4',
            'state' => '1',
        ]);

        //KATEGORI 10
        \App\Models\Ref::create([
            'id' => 'D100101',
            'name' => 'SURAT PENCABUTAN PERMINTAAN PENANGKAPAN TERSANGKA',
            'grp_id' => 'D10',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D100102',
            'name' => 'SURAT PENCABUTAN PERMINTAAN PENCARIAN BARANG',
            'grp_id' => 'D10',
            'sort' => '2',
            'state' => '1',
        ]);

        //KATEGORI 11
        \App\Models\Ref::create([
            'id' => 'D110101',
            'name' => 'SURAT PERINTAH PENGHENTIAN PENYELIDIKAN',
            'grp_id' => 'D11',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D110102',
            'name' => 'SURAT KETETAPAN PENGHENTIAN PENYELIDIKAN',
            'grp_id' => 'D11',
            'sort' => '2',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110103',
            'name' => 'SURAT PENCABUTAN PENGHENTIAN PENYELIDIKAN',
            'grp_id' => 'D11',
            'sort' => '3',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110104',
            'name' => 'SURAT PERINTAH PENYELIDIKAN LANJUTAN',
            'grp_id' => 'D11',
            'sort' => '4',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110105',
            'name' => 'BERITA ACARA PENGHENTIAN PENYELIDIKAN',
            'grp_id' => 'D11',
            'sort' => '5',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110106',
            'name' => 'PERSETUJUAN / DISPOSISI / ARAHAN PEJABAT YANG BERWENANG',
            'grp_id' => 'D11',
            'sort' => '6',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110107',
            'name' => 'SURAT PERINTAH PENGHENTIAN PENYIDIKAN',
            'grp_id' => 'D11',
            'sort' => '7',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110108',
            'name' => 'SURAT KETETAPAN PENGHENTIAN PENYIDIKAN',
            'grp_id' => 'D11',
            'sort' => '8',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110109',
            'name' => 'PUTUSAN PRA PERADILAN',
            'grp_id' => 'D11',
            'sort' => '9',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110110',
            'name' => 'SURAT PENCABUTAN PENGHENTIAN PENYIDIKAN',
            'grp_id' => 'D11',
            'sort' => '10',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110111',
            'name' => 'SURAT PERINTAH PENYIDIKAN LANJUTAN',
            'grp_id' => 'D11',
            'sort' => '11',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110112',
            'name' => 'BERITA ACARA PENGHENTIAN PENYIDIKAN',
            'grp_id' => 'D11',
            'sort' => '12',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110113',
            'name' => 'SURAT PERNYATAAN',
            'grp_id' => 'D11',
            'sort' => '13',
            'state' => '1',
        ]);

        \App\Models\Ref::create([
            'id' => 'D110114',
            'name' => 'SURAT KESEPAKATAN PERDAMAIAN',
            'grp_id' => 'D11',
            'sort' => '14',
            'state' => '1',
        ]);

        //KATEGORI 12
        \App\Models\Ref::create([
            'id' => 'D120101',
            'name' => 'SURAT KETETAPAN TENTANG PENETAPAN TERSANGKA',
            'grp_id' => 'D12',
            'sort' => '1',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D120102',
            'name' => 'SURAT PERINTAH PENANGKAPAN',
            'grp_id' => 'D12',
            'sort' => '2',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D120103',
            'name' => 'SURAT PERINTAH MEMBAWA DAN MENGHADAPKAN',
            'grp_id' => 'D12',
            'sort' => '3',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D120104',
            'name' => 'SURAT PERINTAH PELEPASAN TERSANGKA',
            'grp_id' => 'D12',
            'sort' => '4',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D120105',
            'name' => 'BERITA ACARA PENANGKAPAN',
            'grp_id' => 'D12',
            'sort' => '5',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'D120106',
            'name' => 'BERITA ACARA PELEPASAN TERSANGKA',
            'grp_id' => 'D12',
            'sort' => '6',
            'state' => '1',
        ]);
        \App\Models\Ref::create([
            'id' => 'LG0101',
            'name' => 'PENETAPAN TERSANGKA',
            'grp_id' => 'LG01',
            'sort' =>  '1',
            'state' => '1'
        ]);

    }
}
