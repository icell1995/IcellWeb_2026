<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\PoliceDiktukEducation;

class PoliceDiktukEducationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $policeDiktukEducations = $this->getPoliceDiktukEducations();

        DB::beginTransaction();
        try{
            foreach($policeDiktukEducations as $policeDiktukEducation){
                PoliceDiktukEducation::updateOrCreate(
                    [
                        'id' => $policeDiktukEducation['Id']
                    ],
                    [
                        'id' => $policeDiktukEducation['Id'],
                        'emp_id' => $policeDiktukEducation['Id'],
                        'name' => $policeDiktukEducation['Nama'],
                    ]
                );
            }
            DB::commit();

            $this->command->info('Table police_diktuk_educations seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getPoliceDiktukEducations()
    {
        $path = base_path('master_seeder/police_diktuk_educations.json');

        $file = File::get($path);

        $data = json_decode($file, true);

        return $data;
    }
}
