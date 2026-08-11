<?php

namespace Database\Seeders\Personals;

use Illuminate\Database\Seeder;

use App\Models\Meta\Personals\MaritalStatus;

class MaritalStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maritalStatuses = [
            [
                'id' => '1',
                'name' => 'Belum Kawin',
            ],
            [
                'id' => '2',
                'name' => 'Kawin',
            ],
            // [
            //     'id' => '3',
            //     'name' => 'Cerai Hidup',
            // ],
            // [
            //     'id' => '4',
            //     'name' => 'Cerai Mati',
            // ],
        ];
        
        foreach ($maritalStatuses as $maritalStatus) {
            MaritalStatus::create([
                'id' => $maritalStatus['id'],
                'name' => $maritalStatus['name'],
            ]);
        }
    }
}
