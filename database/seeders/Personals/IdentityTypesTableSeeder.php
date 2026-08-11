<?php

namespace Database\Seeders\Personals;

use Illuminate\Database\Seeder;

use App\Models\Meta\Personals\IdentityType;

class IdentityTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $identityTypes = [
            [
                "id" => "3",
                "description" => "BADAN PENYELENGGARA JAMINAN SOSIAL",
                "name" => "BPJS",
            ],
            ["id" => "4", "description" => "KARTU", "name" => "KARTU"],
            [
                "id" => "5",
                "description" => "KARTU IZIN TINGGAL TERBATAS",
                "name" => "KITAS",
            ],
            [
                "id" => "6",
                "description" => "KARTU MAHASISWA",
                "name" => "KARTU MAHASISWA",
            ],
            [
                "id" => "7",
                "description" => "KARTU PELAJAR",
                "name" => "KARTU PELAJAR",
            ],
            [
                "id" => "8",
                "description" => "KARTU KELUARGA",
                "name" => "KK",
            ],
            [
                "id" => "9",
                "description" => "KARTU TANDA ANGGOTA",
                "name" => "KTA",
            ],
            [
                "id" => "10",
                "description" => "KARTU TANDA PENDUDUK",
                "name" => "KTP",
            ],
            [
                "id" => "11",
                "description" => "NOMOR REGISTER POKOK",
                "name" => "NRP",
            ],
            [
                "id" => "12",
                "description" => "PASPOR",
                "name" => "PASSPORT",
            ],
            [
                "id" => "13",
                "description" => "SURAT IZIN MENGEMUDI",
                "name" => "SIM",
            ],
            [
                "id" => "14",
                "description" => "SURAT KETERANGAN LAIN",
                "name" => "SURAT KETERANGAN LAIN",
            ],
            [
                "id" => "15",
                "description" => "TIDAK DIKETAHUI",
                "name" => "TIDAK DIKETAHUI",
            ],
            [
                "id" => "16",
                "description" => "TIDAK MEMILIKI",
                "name" => "TIDAK MEMILIKI",
            ],
        ];

        foreach ($identityTypes as $identityType) {
            IdentityType::create([
                "id" => $identityType["id"],
                "description" => $identityType["description"],
                "name" => $identityType["name"],
            ]);
        }      
    }
}
