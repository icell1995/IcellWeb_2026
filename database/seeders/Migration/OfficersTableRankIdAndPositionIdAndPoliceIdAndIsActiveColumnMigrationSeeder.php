<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Officer;
use App\Models\Lib\Police;
use App\Models\Lib\Position;

class OfficersTableRankIdAndPositionIdAndPoliceIdAndIsActiveColumnMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->migrateRankId();
        //$this->migratePoliceId();
        //$this->migratePositionId();
        //$this->migrateIsActive();
    }

    private function migrateRankId()
    {
        $oldRank = [
            "BRIPTU" => "21",
            "AIPTU" => "17",
            "BRIGADIR" => "20",
            "AIPDA" => "18",
            "BRIPDA" => "22",
            "BRIPKA" => "19",
            "IPDA" => "16",
            "AKP" => "14",
            "AKBP" => "12",
            "IPTU" => "15",
            "KOMBES" => "11",
            "KOMBESPOL" => "11",
            "KOMJEN" => "8",
            "KOMPOL" => "13",
            "BRIGPOL" => "20",
            "NULL" => null,
            "PNS" => null,
	    "-" => null,
        ];

        DB::beginTransaction();
        try{
            $officers = Officer::all();

            foreach ($officers as $officer) {
                if(!empty($officer->rank_short_name) || $officer->rank_short_name != "NULL"){
                    $officer->rank_id = $oldRank[$officer->rank_short_name];
                    $officer->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function migratePoliceId()
    {
        DB::beginTransaction();
        try{
            $officers = Officer::all();

            foreach ($officers as $officer) {
                if(!empty($officer->polres_id)){
		    if($officer->polres_id != 0){
                    	$officer->police_id = $officer->polres_id;
                    	$officer->save();
		    }

                }else if(empty($officer->polres_id)){
		    if($officer->polres_id != 0){
                   	 $officer->police_id = $officer->polda_id;
                    	$officer->save();
		    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function migratePositionId()
    {
        DB::beginTransaction();
        try{
            $officers = Officer::all();

            foreach ($officers as $officer) {

                if(!empty($officer->sebagai_kepala) || $officer->sebagai_kepala != "null" || $officer->sebagai_kepala != "NULL" || $officer->sebagai_kepala != "-"){
                    $police_id = (!empty($officer->polres_id)) ? $officer->polres_id : $officer->polda_id;

                    switch($officer->sebagai_kepala){
                        case 'KASI LAKA':
                            $position = Position::where('position_cluster_id', '10')->where('police_id', $police_id)->first();
                            if(!empty($position)){
                                $officer->position_id = $position->id;
                                $officer->save();
                            }
                            break;

                        case 'KASAT LAKA':
                            $position = Position::where('position_cluster_id', '3')->where('police_id', $police_id)->first();
                            if(!empty($position)){
                                $officer->position_id = $position->id;
                                $officer->save();
                            }
                            break;

                        case 'KANIT LAKA':
                            $position = Position::where('position_cluster_id', '4')->where('police_id', $police_id)->first();
                            if(!empty($position)){
                                $officer->position_id = $position->id;
                                $officer->save();
                            }
                            break;

                        default:
                            break;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function migrateIsActive()
    {
        DB::beginTransaction();
        try{
            $officers = Officer::all();

            foreach ($officers as $officer) {
                $officer->is_active = ($officer->state == 1) ? true : false;
                $officer->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
