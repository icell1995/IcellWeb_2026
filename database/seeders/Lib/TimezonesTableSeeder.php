<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\Timezone;

class TimezonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezones = json_decode($this->getTimezones(), true);

        DB::beginTransaction();
        try{
            foreach($timezones as $timezone){
                Timezone::updateOrCreate(
                    [
                        'id' => $timezone['id']
                    ],
                    [
                        'id' => $timezone['id'],
                        'code' => $timezone['code'],

                        'name' => $timezone['name'],
                        'full_name' => $timezone['full_name'],

                        'country' => $timezone['country'],
                        'utc' => $timezone['utc'],
                        'dst' => $timezone['dst'],
                        'olson' => $timezone['olson'],
                        'zone' => $timezone['zone'],
                    ]
                );
            }
            
            DB::commit();

            $this->command->info('Timezones table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
    
    private function getTimezones(){
        $timezones = File::get(base_path('master_seeder/timezones.json'));

        return $timezones;
    }

}
