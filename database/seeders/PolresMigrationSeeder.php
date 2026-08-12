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
                    "old_police_id" => "3108",
                    "new_police_id" => "3608",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
                ],
                [
                    "old_police_id" => "3110",
                    "new_police_id" => "3603",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
                ],
                [
                    "old_police_id" => "3125",
                    "new_police_id" => "3601",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
                ],
                [
                    "old_police_id" => "3129",
                    "new_police_id" => "3607",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
                ],
                [
                    "old_police_id" => "3135",
                    "new_police_id" => "3602",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
                ],
                [
                    "old_police_id" => "3136",
                    "new_police_id" => "3605",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
                ],
                [
                    "old_police_id" => "3137",
                    "new_police_id" => "3606",
                    "old_polda_id" => "31",
                    "new_polda_id" => "36",
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
