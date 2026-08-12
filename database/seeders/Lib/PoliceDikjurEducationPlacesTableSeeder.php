<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\PoliceDikjurEducationPlace;

class PoliceDikjurEducationPlacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try{
            $this->getPoliceDikjurEducationPlaces()->each(function ($policeDikjurEducationPlace) {
                PoliceDikjurEducationPlace::updateOrCreate([
                    'id' => $policeDikjurEducationPlace['id'],
                ],[
                    'id' => $policeDikjurEducationPlace['id'],
                    'name' => $policeDikjurEducationPlace['name'],
                ]);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function getPoliceDikjurEducationPlaces()
    {
        $json = File::get(base_path('master_seeder/police_dikjur_education_places.json'));
        $data = json_decode($json, true);

        return collect($data);
    }
}
