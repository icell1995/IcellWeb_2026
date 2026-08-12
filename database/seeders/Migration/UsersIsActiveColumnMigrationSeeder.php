<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersIsActiveColumnMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateIsActive();
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
