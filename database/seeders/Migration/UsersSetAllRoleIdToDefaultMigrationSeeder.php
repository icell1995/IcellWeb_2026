<?php

namespace Database\Seeders\Migration;

use App\Models\Officer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class UsersSetAllRoleIdToDefaultMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->migrateSetAllRoleIdToDefault();
    }

    private function migrateSetAllRoleIdToDefault(){
        DB::beginTransaction();
        try{
            $users = User::with('officer')
                ->where('role_id', 3)
                ->get();

            foreach ($users as $user) {
                dump($user->register_number);

                $user->update(['role_id' => 4]);

                if(isset($user->officer)){
                    Officer::where('user_id', $user->id)
                        ->update(['class' => 'MEMBER']);
                }
            }

            DB::commit();

            $this->command->info('Success');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
