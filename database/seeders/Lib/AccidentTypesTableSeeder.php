<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\AccidentType;

class AccidentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accidentTypes = [
            [
                "id"=> "A0701",
                "name"=> "Di simpang, pejalan kaki menyeberang dari kiri ke kanan"
            ],
            [
                "id"=> "A0702",
                "name"=> "Di simpang, pejalan kaki menyeberang dari kanan ke kiri"
            ],
            [
                "id"=> "A0703",
                "name"=> "Di ruas jalan, pejalan kaki menyeberang dari kiri ke kanan"
            ],
            [
                "id"=> "A0704",
                "name"=> "Di ruas jalan, pejalan kaki menyeberang dari kanan ke kiri"
            ],
            [
                "id"=> "A0705",
                "name"=> "Di ruas jalan, pejalan kaki berdiri ragu-ragu di tengah jalan"
            ],
            [
                "id"=> "A0706",
                "name"=> "Di ruas jalan, pejalan kaki sejajar jalan di kiri atau di kanan"
            ],
            [
                "id"=> "A0707",
                "name"=> "Di ruas jalan, pejalan kaki di bahu jalan atau di trotoar"
            ],
            [
                "id"=> "A0711",
                "name"=> "Di simpang, kendaraan lurus dengan pejalan kaki dari kiri di Zebra Cross"
            ],
            [
                "id"=> "A0712",
                "name"=> "Di simpang, kendaraan lurus dengan pejalan kaki dari kanan di Zebra Cross"
            ],
            [
                "id"=> "A0713",
                "name"=> "Di ruas, kendaraan lurus dengan pejalan kaki dari kiri di Zebra Cross"
            ],
            [
                "id"=> "A0714",
                "name"=> "Di ruas, kendaraan lurus dengan pejalan kaki dari kanan di Zebra Cross"
            ],
            [
                "id"=> "A0715",
                "name"=> "Di ruas, kendaraan lurus dengan pejalan kaki berdiri ragu-ragu di ZebraCross"
            ],
            [
                "id"=> "A0716",
                "name"=> "Kendaraan belok kiri dengan pejalan kaki menyebrang di Zebra Cross"
            ],
            [
                "id"=> "A0717",
                "name"=> "Kendaraan belok kanan dengan pejalan kaki menyeberang di Zebra Cross"
            ],
            [
                "id"=> "A0723",
                "name"=> "Kendaraan tidak terkendali di simpang"
            ],
            [
                "id"=> "A0726",
                "name"=> "Kendaraan sedang melintas tertimpa Tiang atau Pohon"
            ],
            [
                "id"=> "A0731",
                "name"=> "Tabrakan dengan kendaraan parkir di kiri"
            ],
            [
                "id"=> "A0732",
                "name"=> "Tabrakan dengan kendaraan parkir di kanan"
            ],
            [
                "id"=> "A0733",
                "name"=> "Tabrakan dengan kendaraan parkir saat manuver parkir"
            ],
            [
                "id"=> "A0725",
                "name"=> "Sepeda angin/ Sepeda motor jatuh"
            ],
            [
                "id"=> "A0734",
                "name"=> "Tabrakan dengan atau kecelakaan akibat binatang melintas jalan"
            ],
            [
                "id"=> "A0735",
                "name"=> "Tabrakan dengan benda di jalan atau di atas jalan"
            ],
            [
                "id"=> "A0736",
                "name"=> "Tabrakan dengan material / rambu pekerjaan jalan"
            ],
            [
                "id"=> "A0737",
                "name"=> "Tabrakan dengan kereta api"
            ],
            [
                "id"=> "A0738",
                "name"=> "Tabrakan dengan Binatang yang sedang berjalan di sisi jalan"
            ],
            [
                "id"=> "A0741",
                "name"=> "Di simpang, tabrakan dengan Kendaraan B yang datang dari arah kiri"
            ],
            [
                "id"=> "A0742",
                "name"=> "Di simpang, tabrakan dengan Kendaraan B yang datang dari arah kanan"
            ],
            [
                "id"=> "A0743",
                "name"=> "Tabrakan dengan Kendaraan menyebrang dari sisi kiri jalan"
            ],
            [
                "id"=> "A0700",
                "name"=> "Tidak Diketahui (Tabrak lari dan tidak ada saksi)"
            ],
            [
                "id"=> "A0744",
                "name"=> "Tabrakan dengan kendaraan menyebrang dari sisi kanan jalan"
            ],
            [
                "id"=> "A0751",
                "name"=> "Tabrakan saat menyalip"
            ],
            [
                "id"=> "A0752",
                "name"=> "Tabrakan depan - depan"
            ],
            [
                "id"=> "A0753",
                "name"=> "Tabrakan saat gerakan putar balik"
            ],
            [
                "id"=> "A0755",
                "name"=> "Tabrakan saat belok ke sisi kanan jalan"
            ],
            [
                "id"=> "A0761",
                "name"=> "Tabrakan saat menyalip dari kanan"
            ],
            [
                "id"=> "A0762",
                "name"=> "Tabrakan saat menyalip dari kiri"
            ],
            [
                "id"=> "A0763",
                "name"=> "Tabrakan depan - belakang"
            ],
            [
                "id"=> "A0764",
                "name"=> "Tabrakan saat pindah lajur ke kanan"
            ],
            [
                "id"=> "A0765",
                "name"=> "Tabrakan saat pindah lajur ke kiri"
            ],
            [
                "id"=> "A0767",
                "name"=> "Tabrakan samping"
            ],
            [
                "id"=> "A0771",
                "name"=> "Tabrak belakang kendaraan depan yang belok kiri"
            ],
            [
                "id"=> "A0772",
                "name"=> "Tabrakan sesama kendaraan saat belok kiri"
            ],
            [
                "id"=> "A0773",
                "name"=> "Tabrakan kendaraan belok kiri dengan kendaraan yang jalan lurus"
            ],
            [
                "id"=> "A0774",
                "name"=> "Tabrak belakang kendaraan depan yang belok kanan"
            ],
            [
                "id"=> "A0775",
                "name"=> "Tabrak sesama kendaraan belok kanan"
            ],
            [
                "id"=> "A0776",
                "name"=> "Tabrak kendaraan belok kanan dengan kendaraan yang jalan lurus"
            ],
            [
                "id"=> "A0781",
                "name"=> "Tabrakan kendaraan belok kanan dengan kendaraan yang datang dari arah berlawanan"
            ],
            [
                "id"=> "A0782",
                "name"=> "Tabrakan kendaraan belok kanan dengan kendaraan belok kiri"
            ],
            [
                "id"=> "A0783",
                "name"=> "Tabrak antara dua kendaraan belok kanan"
            ],
            [
                "id"=> "A0784",
                "name"=> "Tabrakan kendaraan belok kiri dengan kendaraan yang datang dari arah yang berlawanan"
            ],
            [
                "id"=> "A0791",
                "name"=> "Tabrakan kendaraan belok kiri dengan kendaraan dari arah kanan"
            ],
            [
                "id"=> "A0792",
                "name"=> "Tabrakan kendaraan belok kiri dengan kendaraan dari arah kiri"
            ],
            [
                "id"=> "A0793",
                "name"=> "Tabrakan kendaraan belok kanan dengan kendaraan dari arah kanan"
            ],
            [
                "id"=> "A0794",
                "name"=> "Tabrakan kendaraan belok kanan dengan kendaraan dari arah kiri"
            ],
            [
                "id"=> "A0795",
                "name"=> "Tabrakan antara dua kendaraan belok"
            ],
            [
                "id"=> "A0766",
                "name"=> "Tabrakan saat gerakan putar balik"
            ],
            [
                "id"=> "A0722",
                "name"=> "Kendaraan Out of Control keluar ke kanan jalan"
            ],
            [
                "id"=> "A0721",
                "name"=> "Kendaraan Out of Control keluar ke kiri jalan"
            ],
            [
                "id"=> "A0754",
                "name"=> "Tabrakan saat kendaraan A melakukan gerakan mundur / lawan arah"
            ],
            [
                "id"=> "A0709",
                "name"=> "dev test"
            ],
            [
                "id"=> "A0797",
                "name"=> "dev testq"
            ],
            [
                "id"=> "A0796",
                "name"=> "Tabrakan Menyerempet Kendaraan Dari samping, setelah memotong lajur"
            ],
            [
                "id"=> "A0724",
                "name"=> "Penumpang jatuh dari kendaraan (input 1 penumpang)"
            ]
        ];

        
        DB::beginTransaction();
        try{
            foreach ($accidentTypes as $accidentType) {
                AccidentType::updateOrcreate(
                    [
                        'irsms_id' => $accidentType['id'],
                    ],
                    [
                        'irsms_id' => $accidentType['id'],
                        'name' => $accidentType['name'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Accident Types table seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
