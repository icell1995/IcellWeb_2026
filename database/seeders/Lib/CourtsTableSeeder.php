<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\Court;

class CourtsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courts = json_decode($this->getCourts(), true);

        DB::beginTransaction();
        try{
            foreach($courts as $court){
                /*Court::updateOrCreate(
                    [
                        'id' => $court['Id'],
                    ],
                    [
                        'id' => $court['Id'],
                        'emp_id' => $court['Id'],

                        'parent_id' => null,

                        'class' => null,

                        'name' => $court['Nama'],

                        'address' => $court['Alamat'],
                        'country_id' => 'C101',
                        'province_id' => ($court['PropinsiId']) ? 'P' . $court['PropinsiId'] : null,
                        'regency_id' => ($court['KotaId']) ? 'R' . $court['KotaId'] : null,
                        'district_id' => ($court['KecamatanId']) ? 'D' . $court['KecamatanId'] : null,
                        'village_id' => ($court['KelurahanId']) ? 'V' . $court['KelurahanId'] : null,
                        'postal_code' => $court['KodePos'],
                    ]
                );*/

                Court::updateOrCreate(
                    [
                        'id' => $court['Id_Pengadilan'],
                    ],
                    [
                        'id' => $court['Id_Pengadilan'],
                        'emp_id' => $court['Id_Pengadilan'],

                        'parent_id' => null,

                        'class' => $court['class'],

                        'name' => strtoupper($court['EMP_Name']),
                        'full_name' => strtoupper($court['EMP_Name']),

                        // 'address' => $court['Alamat'],
                        // 'country_id' => 'C101',
                        // 'province_id' => ($court['PropinsiId']) ? 'P' . $court['PropinsiId'] : null,
                        // 'regency_id' => ($court['KotaId']) ? 'R' . $court['KotaId'] : null,
                        // 'district_id' => ($court['KecamatanId']) ? 'D' . $court['KecamatanId'] : null,
                        // 'village_id' => ($court['KelurahanId']) ? 'V' . $court['KelurahanId'] : null,
                        // 'postal_code' => $court['KodePos'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Courts table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }                                   
        
    }

    private function getCourts(){
        $courts = File::get(base_path('master_seeder/courts.json'));

        return $courts;
    }
}
