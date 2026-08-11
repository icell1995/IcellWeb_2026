<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\PoliceDivision;

class PoliceDivisionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try{
            $this->getPoliceDivisions()->each(function ($policeDivision) {
                PoliceDivision::updateOrCreate([
                    'id' => $policeDivision['Id'],
                ],
                [
                    'id' => $policeDivision['Id'],
                    'emp_id' => $policeDivision['Id'],
                    'name' => $policeDivision['Nama'],
                ]);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function getPoliceDivisions()
    {
        $json = File::get(base_path('master_seeder/police_divisions.json'));
        $data = json_decode($json, true);

        return collect($data);
    }
}
