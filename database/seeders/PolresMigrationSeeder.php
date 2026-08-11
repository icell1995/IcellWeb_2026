<?php

namespace Database\Seeders;

use App\Models\PolresMigrationHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PolresMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $polresMigration =
            [
                [
                    "old_police_id" => "3310",
                    "new_police_id" => "3502",
                    "old_polda_id" => "33",
                    "new_polda_id" => "35",
                ],
		[
                    "old_police_id" => "3311",
                    "new_police_id" => "3504",
                    "old_polda_id" => "33",
                    "new_polda_id" => "35",
                ],
		[
                    "old_police_id" => "3318",
                    "new_police_id" => "3501",
                    "old_polda_id" => "33",
                    "new_polda_id" => "35",
                ],
                
            ];

        DB::beginTransaction();
        foreach ($polresMigration as $data) {
            PolresMigrationHistory::updateOrcreate(
                [
                    'old_police_id' => $data['old_police_id'],
                    'new_police_id' => $data['new_police_id'],                    
                    'old_polda_id' => $data['old_polda_id'],
                    'new_polda_id' => $data['new_polda_id'],
                ]
            );
        }

        DB::commit();
    }
}
