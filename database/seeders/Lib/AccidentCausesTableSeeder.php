<?php

namespace Database\Seeders\Lib;

use App\Models\Lib\AccidentCause;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccidentCausesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accidentCauses = [
            [
                "id"=> "N0101",
                "name"=> "Manusia"
            ],
            [
                "id"=> "N0102",
                "name"=> "Jalan"
            ],
            [
                "id"=> "N0103",
                "name"=> "Kendaraan"
            ],
            [
                "id"=> "N0105",
                "name"=> "dev testq"
            ],
            [
                "id"=> "N0104",
                "name"=> "Alam (kabut,banjir,dll)"
            ]
        ];

        
        DB::beginTransaction();
        try{
            foreach ($accidentCauses as $accidentCause) {
                AccidentCause::updateOrcreate(
                    [
                        'irsms_id' => $accidentCause['id'],
                    ],
                    [
                        'irsms_id' => $accidentCause['id'],
                        'name' => $accidentCause['name'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Accident Causes table seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
