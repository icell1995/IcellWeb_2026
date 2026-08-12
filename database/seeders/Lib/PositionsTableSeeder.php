<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\Position;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert to table positions
        $positions = $this->getPositions();
        dump($positions);

        DB::beginTransaction();
        try{
            foreach($positions as $position){
                Position::updateOrCreate(
                    [
                        'id' => $position['id']
                    ],
                    [
                        'id' => $position['id'],
                        // 'code' => $position['code'],
                        'name' => $position['name'],
                        'police_id' => $position['police_id'],
                        'employment_type_id' => $position['employment_type_id'],
                        'position_cluster_id' => $position['position_cluster_id'],
                    ]
                );
            }
            
            DB::commit();

            $this->command->info('Table positions seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getPositions(){
        $positions = File::get(base_path('master_seeder/positions.json'));
        $positions = json_decode($positions, true);
        $positions = collect($positions);
        
        return $positions;
    }
}
