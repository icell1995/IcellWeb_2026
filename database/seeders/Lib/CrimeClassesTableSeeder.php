<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\CrimeClass;

class CrimeClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $crimeClasses = [
            [
                'id' => '1',
                'emp_id' => NULL,
                'name' => 'Kejahatan Lalu Lintas',
                'code' => 'CS-0001',
            ],
        ];

        DB::beginTransaction();
        try{
            foreach ($crimeClasses as $crimeClass) {
                CrimeClass::updateOrCreate(
                    [
                        'id' => $crimeClass['id'],
                    ],
                    $crimeClass
                );
            }

            DB::commit();

            $this->command->info('Berhasil menambahkan data kejahatan kelas');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
