<?php

namespace Database\Seeders\Lib;

use App\Models\Lib\DetentionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetentionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $type = [
            ["type_name" => "Penahanan Rumah Tahanan Negara"],
            ["type_name" => "Penahanan Rumah"],
            ["type_name" => "Penahanan Kota"]
        ];

        foreach ($type as $data){
            DetentionType::updateOrCreate(
                [
                    'type_name' => $data['type_name'],
                ],
            );
        }
    }
}
