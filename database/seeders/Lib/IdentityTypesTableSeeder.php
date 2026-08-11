<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\IdentityType;

class IdentityTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $identityTypes = json_decode($this->getIdentityTypes(), true);

        DB::beginTransaction();
        try{
            foreach($identityTypes as $identityType){
                IdentityType::updateOrCreate(
                    [
                        'id' => $identityType['Id']
                    ],
                    [ 
                        'id' => $identityType['Id'],
                        'emp_id' => $identityType['Id'],
                        'name' => $identityType['Nama'],
                        'full_name' => $identityType['Deskripsi'],
                        'irsms_id' => $identityType['IrsmsId'] ?? null,
                    ]
                );
            }

            DB::commit();

            $this->command->info('Identity Types table seeded!');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }   
    }

    private function getIdentityTypes(){
        $prosecutors = File::get(base_path('master_seeder/identity_types.json'));

        return $prosecutors;
    }
}
