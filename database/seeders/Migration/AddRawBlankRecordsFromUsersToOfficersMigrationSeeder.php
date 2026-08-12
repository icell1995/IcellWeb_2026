<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

use App\Models\User;

use App\Models\Officer;

class AddRawBlankRecordsFromUsersToOfficersMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->migrateAttachUserToOfficer();
        $this->migrateAttachOfficerToUser();
    }

    private function migrateAttachUserToOfficer(){
        DB::beginTransaction();
        try{
            $users = User::with(['rank'])->get();
    
            foreach ($users as $user) {
                $officer = Officer::where('register_number', $user->register_number)
                    ->orWhere('id', $user->register_number)
                    ->first();

                if(empty($officer)){
                    dump($user->register_number);

                    Officer::updateOrCreate(
                        [
                            'register_number' => $user->register_number,
                        ],
                        [
                            'user_id' => $user->id,
                            'id' => $user->register_number,
                            'register_number' => $user->register_number,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'first_title' => $user->first_title,
                            'last_title' => $user->last_title,
                            'polda_id' => $user->polda_id ?? 0,
                            'polres_id' => $user->polres_id ?? 0,
                            'police_id' => (!empty($user->polres_id)) ? $user->polres_id : ((!empty($user->polda_id)) ? $user->polda_id : null),
                            'rank_short_name' => $user->pangkat,
                            'position_short_name' => '-',
                            'sebagai_kepala' => null,
                            'state' => ($user->state == 1) ? 1 : 0,
                            'is_active' => ($user->state == 1) ? true : false,
                            'is_valid' => true,
                            'class' => 'MEMBER',
                            'rank_id' => $user->rank_id,
                            'status' => 'PRESENT',
                        ]
                    );
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
            $officers = Officer::where('user_id', NULL)->get();
    
            foreach ($officers as $officer) {
                dump($officer->register_number);

                $email = $officer->register_number . '@blank';

                if(!empty($officer->email)){
                    $userEmail = User::where('email', $officer->email)->first();

                    if(empty($userEmail)){
                        $email = $officer->email;
                    }
                }

                $lastUser = User::max('id');
                

                User::updateOrCreate(
                    [
                        'register_number' => $officer->register_number,
                    ],
                    [
                        'id' => $lastUser + 1,
                        'username' => $officer->register_number,
                        'password' => Hash::make(uniqid()),
                        'register_number' => $officer->register_number,
                        'officer_id' => $officer->register_number,

                        'first_name' => $officer->first_name,
                        'last_name' => $officer->last_name,
                        'first_title' => $officer->first_title,
                        'last_title' => $officer->last_title,
                        'email' => $email,
                        'phone' => $officer->phone,
                        'avatar' => 'user.png',

                        'role_id' => 4,
                        'rank_id' => $officer->rank_id,
                        'pangkat' => $officer->rank_short_name,
                        'polda_id' => $officer->polda_id,
                        'polres_id' => $officer->polres_id,
                        'police_id' => (!empty($officer->polres_id)) ? (($officer->polres_id == '0000') ? null : $officer->polres_id) : ((!empty($officer->polda_id)) ? $officer->polda_id : null),

                        'state' => ($officer->state == 1) ? 1 : 0,
                        'is_active' => ($officer->state == 1) ? true : false,
                        'is_password_changed' => false,
                    ]
                );

                $officer->user_id = $lastUser + 1;
                $officer->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
