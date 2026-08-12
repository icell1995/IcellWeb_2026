<?php

namespace Database\Seeders\Personals;

use Illuminate\Database\Seeder;

use App\Models\Meta\Personals\Religion;

class ReligionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $religions = [
            [
                'id' => '1',
                'name' => 'TIDAK DIKETAHUI',
            ],
            [
                'id' => '2',
                'name' => 'ISLAM',
            ],
            [
                'id' => '3',
                'name' => 'KRISTEN PROTESTAN',
            ],
            [
                'id' => '4',
                'name' => 'KRISTEN KATOLIK',
            ],
            [
                'id' => '5',
                'name' => 'HINDU',
            ],
            [
                'id' => '6',
                'name' => 'BUDHA',
            ],
            [
                'id' => '7',
                'name' => 'KONG HU CU',
            ],
            [
                'id' => '8',
                'name' => 'LAINNYA',
            ],
            [
                'id' => '9',
                'name' => 'PENGHAYAT KEPERCAYAAN',
            ],
        ];

        foreach ($religions as $religion) {
            Religion::create([
                'id' => $religion['id'],
                'name' => $religion['name'],
            ]);
        }
    }
}
