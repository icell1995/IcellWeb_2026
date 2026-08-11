<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Polda;

class PoldaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $poldaFullName = [
            ["id" => "01", "full_name" => "ACEH"],
            ["id" => "02", "full_name" => "SUMATRA UTARA"],
            ["id" => "03", "full_name" => "SUMATRA BARAT"],
            ["id" => "04", "full_name" => "RIAU"],
            ["id" => "05", "full_name" => "BENGKULU"],
            ["id" => "06", "full_name" => "JAMBI"],
            ["id" => "07", "full_name" => "SUMATRA SELATAN"],
            ["id" => "08", "full_name" => "LAMPUNG"],
            ["id" => "09", "full_name" => "KEPULAUAN BANGKA BELITUNG"],
            ["id" => "10", "full_name" => "KEPULAUAN RIAU"],
            ["id" => "11", "full_name" => "METRO JAYA"],
            ["id" => "12", "full_name" => "JAWA BARAT"],
            ["id" => "13", "full_name" => "JAWA TENGAH"],
            ["id" => "14", "full_name" => "D.I YOGYAKARTA"],
            ["id" => "15", "full_name" => "JAWA TIMUR"],
            ["id" => "16", "full_name" => "BANTEN"],
            ["id" => "17", "full_name" => "BALI"],
            ["id" => "18", "full_name" => "NUSA TENGGARA BARAT"],
            ["id" => "19", "full_name" => "NUSA TENGGARA TIMUR"],
            ["id" => "20", "full_name" => "KALIMANTAN BARAT"],
            ["id" => "21", "full_name" => "KALIMANTAN TENGAH"],
            ["id" => "22", "full_name" => "KALIMANTAN SELATAN"],
            ["id" => "23", "full_name" => "KALIMANTAN TIMUR"],
            ["id" => "24", "full_name" => "SULAWESI UTARA"],
            ["id" => "25", "full_name" => "SULAWESI TENGAH"],
            ["id" => "26", "full_name" => "SULAWESI SELATAN"],
            ["id" => "27", "full_name" => "SULAWESI TENGGARA"],
            ["id" => "28", "full_name" => "GORONTALO"],
            ["id" => "29", "full_name" => "MALUKU"],
            ["id" => "30", "full_name" => "MALUKU UTARA"],
            ["id" => "31", "full_name" => "PAPUA"],
            ["id" => "32", "full_name" => "SULAWESI BARAT"],
            ["id" => "33", "full_name" => "PAPUA BARAT"],
            ["id" => "34", "full_name" => "KALIMANTAN UTARA"],
        ];

        foreach ($poldaFullName as $polda) {
            Polda::where('id', $polda['id'])->update(['full_name' => $polda['full_name']]);
        }
    }
}
