<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\CaseClassification;

class CaseClassificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $caseClassifications = [
            [
                'id' => '2',
                'emp_id' => '2',
                'code' => 'CC-SANGAT-SULIT',
                'name' => 'SANGAT SULIT',
                'description' => 'SANGAT SULIT',
                'duration' => 120,
            ],
            [
                'id' => '3',
                'emp_id' => '3',
                'code' => 'CC-SULIT',
                'name' => 'SULIT',
                'description' => 'SULIT',
                'duration' => 90,
            ],
            [
                'id' => '4',
                'emp_id' => '4',
                'code' => 'CC-SEDANG',
                'name' => 'SEDANG',
                'description' => 'SEDANG',
                'duration' => 60,
            ],
            [
                'id' => '7',
                'emp_id' => '7',
                'code' => 'CC-MUDAH',
                'name' => 'MUDAH',
                'description' => 'MUDAH',
                'duration' => 30,
            ],
        ];

        
        DB::beginTransaction();
        try{
            foreach ($caseClassifications as $caseClassification) {
                CaseClassification::updateOrcreate(
                    [
                        'id' => $caseClassification['id']
                    ],
                    $caseClassification
                );
            }

            DB::commit();

            $this->command->info('Case Classifications table seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
