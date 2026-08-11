<?php

namespace Database\Seeders\Personals;

use Illuminate\Database\Seeder;

use App\Models\Meta\Personals\Gender;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = [
            [
                'id' => '1',
                'name' => 'Laki-Laki',
            ],
            [
                'id' => '2',
                'name' => 'Perempuan',
            ],
        ];

        foreach($genders as $gender){
            Gender::create([
                'id' => $gender['id'],
                'name' => $gender['name'],
            ]);
        }
    }
}
