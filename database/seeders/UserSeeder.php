<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'username' => 'admin123',
            'password' => bcrypt('123123123'),
            'first_name' => 'Admin',
            'last_name' => 'Master',
            'officer_id'=>'999999',
            'role_id' => '1',
            'pangkat' => 'Consultant',
            'polda_id' => null,
            'polres_id' => null,
            'avatar'=>'user.png',
            'email' => 'andreasjulio11@gmail.com',
            'state' => '1'
     ]);

     \App\Models\User::create([
        'username' => 'admin_polda',
        'password' => bcrypt('123123123'),
        'first_name' => 'Admin',
        'last_name' => 'Polda',
        'officer_id'=>'000001',
        'role_id' => '2',
        'pangkat' => 'Admin',
        'polda_id' => '11',
        'polres_id' => null,
        'avatar'=>'user.png',
        'email' => 'admin@gmail.com',
        'state' => '1'
    ]);

    \App\Models\User::create([
        'username' => 'admin_polres_1',
        'password' => bcrypt('123123123'),
        'first_name' => 'Admin',
        'last_name' => 'Polres',
        'officer_id'=>'000002',
        'role_id' => '3',
        'pangkat' => 'Admin',
        'polda_id' => '11',
        'polres_id' => '1101',
        'avatar'=>'user.png',
        'email' => 'admin1@gmail.com',
        'state' => '1'
    ]);

    \App\Models\User::create([
        'username' => 'admin_polres_2',
        'password' => bcrypt('123123123'),
        'first_name' => 'Admin',
        'last_name' => 'Polres',
        'officer_id'=>'000003',
        'role_id' => '3',
        'pangkat' => 'Admin',
        'polda_id' => '11',
        'polres_id' => '1102',
        'avatar'=>'user.png',
        'email' => 'admin2@gmail.com',
        'state' => '1'
    ]);

    \App\Models\User::create([
        'username' => 'admin_polres_3',
        'password' => bcrypt('123123123'),
        'first_name' => 'Admin',
        'last_name' => 'Polres',
        'officer_id'=>'000004',
        'role_id' => '3',
        'pangkat' => 'Admin',
        'polda_id' => '11',
        'polres_id' => '1103',
        'avatar'=>'user.png',
        'email' => 'admin3@gmail.com',
        'state' => '1'
    ]);

    \App\Models\User::create([
        'username' => 'admin_polres_4',
        'password' => bcrypt('123123123'),
        'first_name' => 'Admin',
        'last_name' => 'Polres',
        'officer_id'=>'000005',
        'role_id' => '3',
        'pangkat' => 'Admin',
        'polda_id' => '11',
        'polres_id' => '1104',
        'avatar'=>'user.png',
        'email' => 'admin4@gmail.com',
        'state' => '1'
    ]);

    \App\Models\User::create([
        'username' => 'admin_polres_5',
        'password' => bcrypt('123123123'),
        'first_name' => 'Admin',
        'last_name' => 'Polres',
        'officer_id'=>'000006',
        'role_id' => '3',
        'pangkat' => 'Admin',
        'polda_id' => '11',
        'polres_id' => '1105',
        'avatar'=>'user.png',
        'email' => 'admin5@gmail.com',
        'state' => '1'
    ]);





    }
}
