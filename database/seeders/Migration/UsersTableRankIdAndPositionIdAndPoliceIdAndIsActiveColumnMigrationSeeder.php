<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;
Use App\Models\Officer;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;

class UsersTableRankIdAndPositionIdAndPoliceIdAndIsActiveColumnMigrationSeeder extends Seeder
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
       $this->migratePositionId();
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
            "KOMJEN" => "8",
            "KOMPOL" => "13",
            "BRIGPOL" => "20",
            "NULL" => null,
            "PNS" => null,

            // NON POLRI
            'Admin' => '52',
            'Consultant' => '52',
        ];

        DB::beginTransaction();
        try{
            $users = User::all();
    
            foreach ($users as $user) {
                if(!empty($user->pangkat) || $user->pangkat != "NULL"){
                    $user->rank_id = $oldRank[$user->pangkat];
                    $user->save();
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
            $users = User::all();
    
            foreach ($users as $user) {
                if(!empty($user->polres_id)){
			if($user->polres_id != '0000'){
                    		$user->police_id = $user->polres_id;
                    		$user->save();
			}
                }else if(empty($user->polres_id)){
			if($user->polda_id != '0000'){
                    		$user->police_id = $user->polda_id;
                    		$user->save();
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
            $users = User::all();
    
            foreach ($users as $user) {

                if(!empty($user->sebagai_kepala) || $user->sebagai_kepala != "null" || $user->sebagai_kepala != "NULL" || $user->sebagai_kepala != "-"){
                    $police_id = (!empty($user->polres_id)) ? $user->polres_id : $user->polda_id;

                    switch($user->sebagai_kepala){
                        case 'KASI LAKA':
                            $position = Position::where('position_cluster_id', '10')->where('police_id', $police_id)->first();
                            if(!empty($position)){
                                $user->position_id = $position->id;
                                $user->save();
                            }
                            break;
                            
                        case 'KASAT LAKA':
                            $position = Position::where('position_cluster_id', '3')->where('police_id', $police_id)->first();
                            if(!empty($position)){
                                $user->position_id = $position->id;
                                $user->save();
                            }
                            break;
                        
                        case 'KANIT LAKA':
                            $position = Position::where('position_cluster_id', '4')->where('police_id', $police_id)->first();
                            if(!empty($position)){
                                $user->position_id = $position->id;
                                $user->save();
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
            $users = User::all();
    
            foreach ($users as $user) {
                $user->is_active = ($user->state == 1) ? true : false;
                $user->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
