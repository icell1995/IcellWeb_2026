<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Religion;

class ReligionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $religions = File::get(base_path('master_seeder/religions.json'));
        $religions = json_decode($religions, true);

        DB::beginTransaction();
        try{
            foreach($religions as $religion){
                Religion::updateOrCreate(
                    [
                        'id' => $religion['IdEMP']
                    ],
                    [ 
                        'id' => $religion['IdEMP'],
                        'emp_id' => $religion['IdEMP'],
                        
                        'name' => $religion['Nama'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Religions table seeded!');
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
}
