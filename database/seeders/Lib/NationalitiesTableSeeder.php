<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\Nationality;

class NationalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = json_decode($this->getNationalities(), true);

        DB::beginTransaction();
        try{
            foreach($nationalities as $nationality){
                Nationality::updateOrCreate(
                    [
                        'id' => $nationality['Id']
                    ],
                    [ 
                        'id' => $nationality['Id'],
                        'emp_id' => $nationality['Emp_Id'],
                        'name' => $nationality['Nama'],
                        'code' => $nationality['code'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Nationalities Seeded');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }   
    }

    private function getNationalities(){
        $nationalities = File::get(base_path('master_seeder/nationalities.json'));

        return $nationalities;
    }
}
