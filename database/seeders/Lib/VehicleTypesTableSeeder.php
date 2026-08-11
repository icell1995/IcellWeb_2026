<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\VehicleType;

class VehicleTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = [
            [
                "id"=> "V02A00",
                "name"=> "Tidak Diketahui"
            ],
            [
                "id"=> "V02A14",
                "name"=> "Sepeda Angin"
            ],
            [
                "id"=> "V02A01",
                "name"=> "Becak Angin"
            ],
            [
                "id"=> "V02A03",
                "name"=> "Cikar/ Dokar/ Delman"
            ],
            [
                "id"=> "V02B15",
                "name"=> "R2 (Sepeda Motor)"
            ],
            [
                "id"=> "V02A05",
                "name"=> "R3 (Bentor / Bajaj / Bemo)"
            ],
            [
                "id"=> "V02A13",
                "name"=> "Sedan Penumpang"
            ],
            [
                "id"=> "V02A04",
                "name"=> "Jeep ( SUV )"
            ],
            [
                "id"=> "V02A27",
                "name"=> "Van Penumpang"
            ],
            [
                "id"=> "V02A09",
                "name"=> "Mini Bus"
            ],
            [
                "id"=> "V02A07",
                "name"=> "Medium Bus"
            ],
            [
                "id"=> "V02A16",
                "name"=> "Standar Bus"
            ],
            [
                "id"=> "V02A02",
                "name"=> "Bus Gandeng"
            ],
            [
                "id"=> "V02A26",
                "name"=> "Van / Box Hantaran"
            ],
            [
                "id"=> "V02A11",
                "name"=> "Mobil Tangki"
            ],
            [
                "id"=> "V02A17",
                "name"=> "Tangki Gandeng"
            ],
            [
                "id"=> "V02A12",
                "name"=> "Pick up"
            ],
            [
                "id"=> "V02A10",
                "name"=> "Mini Truk"
            ],
            [
                "id"=> "V02A08",
                "name"=> "Medium Truk"
            ],
            [
                "id"=> "V02A24",
                "name"=> "Truk Berat / Tronton"
            ],
            [
                "id"=> "V02A25",
                "name"=> "Truk Gandeng"
            ],
            [
                "id"=> "V02A29",
                "name"=> "Trailer 20 Feet"
            ],
            [
                "id"=> "V02A28",
                "name"=> "Trailer 40 Feet"
            ],
            [
                "id"=> "V02A06",
                "name"=> "Kereta Api"
            ],
            [
                "id"=> "V02A30",
                "name"=> "Kendaraan Alat Berat"
            ],
            [
                "id"=> "V02A23",
                "name"=> "Traktor"
            ],
            [
                "id"=> "V02A19",
                "name"=> "Tidak Diketahui (Modifikasi)"
            ],
            [
                "id"=> "V02A20",
                "name"=> "Tidak Diketahui (Tabrak Lari)"
            ],
            [
                "id"=> "V02A22",
                "name"=> "Trailer"
            ],
            [
                "id"=> "V02B16",
                "name"=> "TESTING KATALOG MOTOR/RD2"
            ],
            [
                "id"=> "V02A31",
                "name"=> "TESTING KATALOG MOBIL"
            ],
            [
                "id"=> "V02B17",
                "name"=> "dev test"
            ],
            [
                "id"=> "V02A32",
                "name"=> "Sepeda Listrik"
            ],
            [
                "id"=> "V02A34",
                "name"=> "Mobil Penumpang Listrik"
            ],
            [
                "id"=> "V02A35",
                "name"=> "Bus Listrik"
            ],
            [
                "id"=> "V02A33",
                "name"=> "R2 Listrik (Sepeda Motor Listrik)"
            ],
            [
                "id"=> "V02A36",
                "name"=> "Kendaraan Rubah Bentuk"
            ]
        ];

        
        DB::beginTransaction();
        try{
            foreach ($vehicleTypes as $vehicleType) {
                VehicleType::updateOrcreate(
                    [
                        'irsms_id' => $vehicleType['id'],
                    ],
                    [
                        'irsms_id' => $vehicleType['id'],
                        'name' => $vehicleType['name'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Vehicle Types table seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
