<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        \App\Models\Officer::create([
            'id' => '000001',
            'first_name' => 'Jaenal',
            'last_name' => 'Abidin',
            'polda_id' => '11',
            'polres_id' => '1101',
            'rank_short_name' => 'BRIPTU',
            'position' => 'Admin',
            'sebagai_kepala' => null,
            'state' => '1'
     ]);

     \App\Models\Officer::create([
        'id' => '000002',
        'first_name' => 'Asep',
        'last_name' => 'Sunandar',
        'polda_id' => '11',
        'polres_id' => '1102',
        'rank_short_name' => 'BRIPTU',
        'position' => 'Admin',
        'sebagai_kepala' => null,
        'state' => '1'
 ]);

 \App\Models\Officer::create([
    'id' => '000003',
    'first_name' => 'Jajang',
    'last_name' => 'Sukmara',
    'polda_id' => '11',
    'polres_id' => '1103',
    'rank_short_name' => 'BRIPTU',
    'position' => 'Admin',
    'sebagai_kepala' => null,
    'state' => '1'
]);

\App\Models\Officer::create([
    'id' => '000004',
    'first_name' => 'Zaenal',
    'last_name' => 'Arif',
    'polda_id' => '11',
    'polres_id' => '1104',
    'rank_short_name' => 'BRIPTU',
    'position' => 'Admin',
    'sebagai_kepala' => null,
    'state' => '1'
]);

\App\Models\Officer::create([
    'id' => '000005',
    'first_name' => 'Nadeo',
    'last_name' => 'Agarwinata',
    'polda_id' => '11',
    'polres_id' => '1105',
    'rank_short_name' => 'BRIPTU',
    'position' => 'Admin',
    'sebagai_kepala' => null,
    'state' => '1'
]);

\App\Models\Officer::create([
    'id' => '000006',
    'first_name' => 'Muhammad',
    'last_name' => 'Robby',
    'polda_id' => '11',
    'polres_id' => '1106',
    'rank_short_name' => 'BRIPTU',
    'position' => 'Admin',
    'sebagai_kepala' => null,
    'state' => '1'
]);

    }
}
