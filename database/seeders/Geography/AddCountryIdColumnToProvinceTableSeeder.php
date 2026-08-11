<?php

namespace Database\Seeders\Geography;

use Illuminate\Database\Seeder;

use App\Models\Geography\Province;

class AddCountryIdColumnToProvinceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Province::where('country_id', null)->update(['country_id' => '101']);
    }
}
