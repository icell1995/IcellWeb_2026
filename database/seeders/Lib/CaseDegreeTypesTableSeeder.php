<?php

namespace Database\Seeders\Lib;

use App\Models\Lib\CaseDegreeType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CaseDegreeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $caseDegreeTypes = json_decode($this->getCaseDegreeTypes(), true);

        DB::beginTransaction();
        try{
            foreach($caseDegreeTypes as $caseDegreeType){
                CaseDegreeType::updateOrCreate(
                    [
                        'id' => $caseDegreeType['Id']
                    ],
                    [
                        'id' => $caseDegreeType['Id'],
                        'emp_id' => $caseDegreeType['Id'],

                        'name' => $caseDegreeType['Nama'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Case Degree Types table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }                                   
    }

    private function getCaseDegreeTypes(){
        $caseDegreeTypes = File::get(base_path('master_seeder/case_degree_types.json'));

        return $caseDegreeTypes;
    }
}
