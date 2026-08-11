<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\CrimeType;

class CrimeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $crimeTypes = [
            [
                'id' => '1',
                'crime_class_id' => '1',
                'emp_id' => NULL,
                'name' => 'Penyelenggara Jalan',
                'code' => 'CT-0001',
            ],
            [
                'id' => '2',
                'crime_class_id' => '1',
                'emp_id' => NULL,
                'name' => 'Perusakan Perambu-rambuan',
                'code' => 'CT-0002',
            ],
            [
                'id' => '3',
                'crime_class_id' => '1',
                'emp_id' => NULL,
                'name' => 'Kecelakaan karena Over Dimensi',
                'code' => 'CT-0003',
            ],
            [
                'id' => '4',
                'crime_class_id' => '1',
                'emp_id' => NULL,
                'name' => 'Kecelakaan karena kelalaian',
                'code' => 'CT-0004',
            ],
            [
                'id' => '5',
                'crime_class_id' => '1',
                'emp_id' => NULL,
                'name' => 'Kecelakaan karena disengaja',
                'code' => 'CT-0005',
            ],
            [
                'id' => '6',
                'crime_class_id' => '1',
                'emp_id' => NULL,
                'name' => 'Kecelakaan karena tidak melakukan pertolongan',
                'code' => 'CT-0006',
            ],
        ];

        DB::beginTransaction();
        try{
            foreach ($crimeTypes as $crimeType) {
                CrimeType::updateOrCreate(
                    [
                        'id' => $crimeType['id']
                    ],
                    $crimeType
                );
            }

            DB::commit();

            $this->command->info('Success');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
