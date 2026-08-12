<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Lib\Location;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = json_decode($this->getCountries(), true);

        DB::beginTransaction();
        try{
            $iteration = 1;
            foreach($countries as $country){
                Location::updateOrCreate(
                    [
                        'id' => 'C' . $country['Id'],
                    ],
                    [
                        'id' => 'C' . $country['Id'],
                        'parent_id' => NULL,

                        'emp_id' => $country['Id'],
        
                        'class' => 'COUNTRY',
        
                        'iso_code' => $country['KodeNegara'],
                        'alpha_code' => $country['KodeNegara'],
        
                        'name' => strtoupper($country['Nama']),

                        'sort' => $iteration
                    ]
                );

                $iteration++;
            }
            DB::commit();

            $this->command->info('Countries table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

        $provinces = json_decode($this->getProvinces(), true);

        DB::beginTransaction();
        try{
            foreach($provinces as $province){
                Location::updateOrCreate(
                    [
                        'id' => 'P' . $province['PropinsiId'],
                    ],
                    [
                        'id' => 'P' . $province['PropinsiId'],
                        'parent_id' => 'C' . $province['CountryId'],

                        'emp_id' => $province['PropinsiId'],
        
                        'class' => 'PROVINCE',
        
                        'name' => strtoupper($province['Nama']),
                    ]
                );
            }
            DB::commit();

            $this->command->info('Provinces table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

        $regencies = json_decode($this->getRegencies(), true);

        DB::beginTransaction();
        try{
            foreach($regencies as $regency){
                Location::updateOrCreate(
                    [
                        'id' => 'R' . $regency['KotaId'],
                    ],
                    [
                        'id' => 'R' . $regency['KotaId'],
                        'parent_id' => 'P' . $regency['PropinsiId'],

                        'emp_id' => $regency['KotaId'],
        
                        'class' => 'REGENCY',
        
                        'name' => strtoupper($regency['Nama']),
                    ]
                );
            }
            DB::commit();

            $this->command->info('Regencies table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

        $districts = json_decode($this->getDistricts(), true);

        DB::beginTransaction();
        try{
            foreach($districts as $district){
                Location::updateOrCreate(
                    [
                        'id' => 'D' . $district['KecamatanId'],
                    ],
                    [
                        'id' => 'D' . $district['KecamatanId'],
                        'parent_id' => 'R' . $district['KotaId'],

                        'emp_id' => $district['KecamatanId'],
        
                        'class' => 'DISTRICT',
        
                        'name' => strtoupper($district['Nama']),
                    ]
                );
            }
            DB::commit();

            $this->command->info('Districts table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

        $villages = json_decode($this->getVillages(), true);

        DB::beginTransaction();
        try{
            foreach($villages as $village){
                Location::updateOrCreate(
                    [
                        'id' => 'V' . $village['KelurahanId'],
                    ],
                    [
                        'id' => 'V' . $village['KelurahanId'],
                        'parent_id' => 'D' . $village['KecamatanId'],

                        'emp_id' => $village['KelurahanId'],
        
                        'class' => 'VILLAGE',
        
                        'name' => strtoupper($village['Nama']),
                    ]
                );
            }
            DB::commit();

            $this->command->info('Villages table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getCountries()
    {
        $countries = File::get(base_path('master_seeder/countries.json'));

        return $countries;
    }
   
    private function getProvinces()
    {
        $provinces = File::get(base_path('master_seeder/provinces.json'));

        return $provinces;
    }

    private function getRegencies()
    {
        $regencies = File::get(base_path('master_seeder/regencies.json'));

        return $regencies;
    }

    private function getDistricts()
    {
        $districts = File::get(base_path('master_seeder/districts.json'));

        return $districts;
    }

    private function getVillages()
    {
        $villages = File::get(base_path('master_seeder/villages.json'));

        return $villages;
    }
}
