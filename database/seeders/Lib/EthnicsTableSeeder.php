<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Ethnic;

class EthnicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ethnics = json_decode($this->getEthnics(), true);

        DB::beginTransaction();
        try {
            foreach($ethnics as $ethnic){
                Ethnic::updateOrCreate(
                    [
                        'id' => $ethnic['Id']
                    ],
                    [ 
                        'id' => $ethnic['Id'],
                        'emp_id' => $ethnic['Id'],
                        
                        'name' => $ethnic['Nama'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Ethnics table seeded!');
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function getEthnics(){
        $ethnics = File::get(base_path('master_seeder/ethnics.json'));

        return $ethnics;
    }

}
