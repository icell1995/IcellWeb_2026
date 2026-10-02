<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Job;

class JobsTableSeeder extends Seeder
{
    public function run()
    {
        $jobs = File::get(base_path('master_seeder/jobs.json'));
        $jobs = json_decode($jobs, true);

        DB::beginTransaction();
        try {
            foreach ($jobs as $job) {
                $pusiknasId = $job['IdPuskarda'] ?? null;
                // Jobs.json kadang punya nilai "None" (string) — konversi ke null
                if ($pusiknasId === 'None' || $pusiknasId === '') {
                    $pusiknasId = null;
                }

                Job::updateOrCreate(
                    ['id' => $job['IdEMP']],
                    [
                        'id'          => $job['IdEMP'],
                        'emp_id'      => $job['IdEMP'],
                        'name'        => $job['Nama'],
                        'pusiknas_id' => $pusiknasId,
                    ]
                );
            }

            DB::commit();
            $this->command->info('Job table seeded!');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
