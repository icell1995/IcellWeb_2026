<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Education;

class EducationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $educations = File::get(base_path('master_seeder/educations.json'));
        $educations = json_decode($educations, true);

        DB::beginTransaction();
        try{
            foreach($educations as $education){
                Education::updateOrCreate(
                    [
                        'id' => $education['IdEMP']
                    ],
                    [ 
                        'id' => $education['IdEMP'],
                        'emp_id' => $education['IdEMP'],
                        'name' => $education['Nama'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Success inserting data to educations table');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }   
    }
}
