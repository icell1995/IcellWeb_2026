<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\DrivingLicenseType;

class DrivingLicenseTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drivingLicenseTypes = [
            [
                "id"=> "D0802",
                "name"=> "A"
            ],
            [
                "id"=> "D0804",
                "name"=> "B I"
            ],
            [
                "id"=> "D0806",
                "name"=> "B II"
            ],
            [
                "id"=> "D0808",
                "name"=> "C"
            ],
            [
                "id"=> "D0809",
                "name"=> "D"
            ],
            [
                "id"=> "D0810",
                "name"=> "Internasional"
            ],
            [
                "id"=> "D0803",
                "name"=> "A Umum"
            ],
            [
                "id"=> "D0805",
                "name"=> "B I Umum"
            ],
            [
                "id"=> "D0807",
                "name"=> "B II Umum"
            ],
            [
                "id"=> "D0811",
                "name"=> "VIP/Diplomat"
            ],
            [
                "id"=> "D0800",
                "name"=> "Tidak Diketahui"
            ],
            [
                "id"=> "D0801",
                "name"=> "Tidak Memiliki SIM"
            ],
            [
                "id"=> "D0812",
                "name"=> "dev test"
            ]
        ];

        
        DB::beginTransaction();
        try{
            foreach ($drivingLicenseTypes as $drivingLicenseType) {
                DrivingLicenseType::updateOrcreate(
                    [
                        'irsms_id' => $drivingLicenseType['id'],
                    ],
                    [
                        'irsms_id' => $drivingLicenseType['id'],
                        'name' => $drivingLicenseType['name'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Driving License Types table seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
