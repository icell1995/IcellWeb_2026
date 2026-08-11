<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\MaritalStatus;

class MaritalStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maritalStatuses = json_decode(File::get(base_path('master_seeder/marital_statuses.json')), true);

        DB::beginTransaction();
        try{
            foreach($maritalStatuses as $maritalStatus){
                MaritalStatus::updateOrCreate(
                    [
                        'id' => $maritalStatus['Id']
                    ],
                    [ 
                        'id' => $maritalStatus['Id'],
                        'emp_id' => $maritalStatus['Id'],
                        'name' => $maritalStatus['Nama'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Success inserting data to MaritalStatuses table');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }   
    }
}
