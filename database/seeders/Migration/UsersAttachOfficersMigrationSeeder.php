<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Officer;

class UsersAttachOfficersMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //$this->migrateAttachUserToOfficer();
        //$this->migrateAttachOfficerToUser();
    }

    private function migrateAttachUserToOfficer(){
        DB::beginTransaction();
        try{
            $users = User::all();
    
            foreach ($users as $user) {
                $officer = Officer::where('register_number', $user->register_number)->first();

                if(!empty($officer)){
                    $officer->user_id = $user->id;

                    $officer->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    private function migrateAttachOfficerToUser(){
        DB::beginTransaction();
        try{
            $officers = Officer::all();
    
            foreach ($officers as $officer) {
                $user = User::where('register_number', $officer->register_number)->first();

                if(!empty($user)){
                    $officer->user_id = $user->id;

                    $officer->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
