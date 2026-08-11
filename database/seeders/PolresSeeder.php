<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Polres;

class PolresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // \App\Models\Polres::create([
        //     'id' => '1101',
        //     'name' => 'Metro Jakarta Pusat',
        //     'sort' => '1',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1102',
        //     'name' => 'Metro Jakarta Utara',
        //     'sort' => '2',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1103',
        //     'name' => 'Metro Jakarta Barat',
        //     'sort' => '3',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1104',
        //     'name' => 'Metro Jakarta Selatan',
        //     'sort' => '4',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1105',
        //     'name' => 'Metro Jakarta Timur',
        //     'sort' => '5',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1106',
        //     'name' => 'Pelabuhan Tanjung Priuk',
        //     'sort' => '6',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1107',
        //     'name' => 'Metro Tangerang',
        //     'sort' => '7',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1108',
        //     'name' => 'Bekasi Kota',
        //     'sort' => '8',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1109',
        //     'name' => 'Depok Kota',
        //     'sort' => '9',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1110',
        //     'name' => 'Bandara Soekarno Hatta',
        //     'sort' => '10',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1112',
        //     'name' => 'Bekasi Kabupaten',
        //     'sort' => '11',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1115',
        //     'name' => 'Tangerang Selatan',
        //     'sort' => '12',
        //     'polda_id' => '11',
        //     'provinsi_id' => '11',
        //     'state' => '1',
        // ]);

        // \App\Models\Polres::create([
        //     'id' => '1201',
        //     'name' => 'Bandung',
        //     'sort' => '13',
        //     'polda_id' => '12',
        //     'provinsi_id' => '12',
        //     'state' => '1',
        // ]);

        $polresFullAddress = [];

        foreach($polresFullAddress as $row){
            Polres::where('id', $row['id'])->update([
                'address' => $row['address'],
            ]);
        }
    }
}
