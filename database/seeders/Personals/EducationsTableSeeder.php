<?php

namespace Database\Seeders\Personals;

use Illuminate\Database\Seeder;

use App\Models\Meta\Personals\Education;

class EducationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $educations = [
            ["id" => "1", "name" => "TIDAK DIKETAHUI"],
            ["id" => "2", "name" => "SD / SEDERAJAT"],
            ["id" => "3", "name" => "SMP / SEDERAJAT"],
            ["id" => "4", "name" => "SMA / SEDERAJAT"],
            ["id" => "5", "name" => "D1"],
            ["id" => "6", "name" => "D2"],
            ["id" => "7", "name" => "D3"],
            ["id" => "8", "name" => "D4"],
            ["id" => "9", "name" => "S1"],
            ["id" => "10", "name" => "S2"],
            ["id" => "11", "name" => "S3"],
            ["id" => "12", "name" => "Tidak Sekolah"],
        ];
        
        foreach ($educations as $education) {
            Education::create([
                'id' => $education['id'],
                'name' => $education['name'],
            ]);
        }
    }
}
