<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class AddRowToOfficerForUserSuperAdminColumnMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userSuperAdmin = User::with('officer')
            ->where('role_id', 1)
            ->get();

        DB::beginTransaction();
        try{
            foreach ($userSuperAdmin as $user) {
                if(empty($user->officer)){
                    $user->officer()->create([
                        'id' => uniqid(),
                        'polda_id' => 0,
                        'polres_id' => 0,
                        'rank_short_name' => '-',
                        'state' => 1,
                        'is_active' => true,
                        'sort' => 0,
                        'is_valid' => true,
                        'user_id' => $user->id,
    
                        'first_title' => $user->first_title,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'last_title' => $user->last_title,
    
                        'position_short_name' => '-',
                        'class' => 'ADMIN',
                        'status' => 'PRESENT',
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
