<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Gender;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = json_decode($this->getGenders(), true);

        DB::beginTransaction();
        try{
            foreach($genders as $gender){
                Gender::updateOrCreate(
                    [
                        'id' => $gender['Id']
                    ],
                    [ 
                        'id' => $gender['Id'],
                        'emp_id' => $gender['Emp_Id'],
                        'name' => $gender['Nama'],
                        'code' => $gender['code'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Genders Seeded');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }   
    }

    private function getGenders(){
        $genders = File::get(base_path('master_seeder/genders.json'));

        return $genders;
    }
}
