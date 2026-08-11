<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Province::create([
            'id' => '10',
            'name' => 'Banten',
            'sort' => '10',
            'timezone' => '+7',
            'state' => '1',
        ]);

        \App\Models\Province::create([
            'id' => '11',
            'name' => 'DKI Jakarta',
            'sort' => '11',
            'timezone' => '+7',
            'state' => '1',
        ]);

        \App\Models\Province::create([
            'id' => '12',
            'name' => 'Jawa Barat',
            'sort' => '12',
            'timezone' => '+7',
            'state' => '1',
        ]);
    }
}
