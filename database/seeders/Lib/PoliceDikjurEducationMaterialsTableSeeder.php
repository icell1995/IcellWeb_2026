<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\PoliceDikjurEducationMaterial;

class PoliceDikjurEducationMaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try{
            $this->getPoliceDikjurEducationMaterials()->each(function ($policeDikjurEducationMaterial) {
                PoliceDikjurEducationMaterial::updateOrCreate([
                    'id' => $policeDikjurEducationMaterial['id'],
                ],[
                    'id' => $policeDikjurEducationMaterial['id'],
                    'name' => $policeDikjurEducationMaterial['name'],
                ]);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function getPoliceDikjurEducationMaterials()
    {
        $json = File::get(base_path('master_seeder/police_dikjur_education_materials.json'));
        $data = json_decode($json, true);

        return collect($data);
    }
}
