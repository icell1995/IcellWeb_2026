<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Accident;

class AccidentsPoliceIdColumnMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accidents = Accident::all();

        DB::beginTransaction();
        try{
            foreach ($accidents as $accident) {
                $accident->police_id = $accident->polres_id;
                $accident->save();
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
