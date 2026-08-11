<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Lib\Police;
use App\Models\Polda;
use App\Models\Polres;

class PolicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try{
            Police::updateOrCreate(
                [
                    'id' => "001",
                ],
                [
                    'id' => "001",
                    
                    'class' => 'PUSAT',
                    
                    'puskarda_id' => NULL,
                    'emp_id' => "1",
                    'spptti_id' => "1",
                    'satker_code' => NULL,

                    'name' => "MABES",
                    'full_name' => 'MARKAS BESAR POLRI',

                    'address' => "Jl. Trunojoyo Kebayoran Baru, Jakarta Selatan, DKI Jakarta",
                    "postal_code" => "12110",

                    'is_active' => true,
                ]
            );
            Police::updateOrCreate(
                [
                    'id' => "002",
                ],
                [
                    'id' => "002",
                    'parent_id' => '001',
                    
                    'class' => 'PUSAT',
                    
                    'puskarda_id' => NULL,
                    'emp_id' => NULL,
                    'spptti_id' => NULL,
                    'satker_code' => NULL,

                    'name' => "KORLANTAS",
                    'full_name' => 'KORPS LALU LINTAS POLRI',

                    'address' => "Jl. MT Haryono Kav37-38, Jakarta Selatan, DKI Jakarta",
                    "postal_code" => "12770",

                    'is_active' => true,
                ]
            );

            $this->command->info('Pusat polices table seeded!');

            $regionalPolices = json_decode($this->getRegionalPolices(), true);

            foreach($regionalPolices as $regionalPolice){
                Polda::updateOrCreate(
                    [
                        'id' => $regionalPolice['id'],
                    ],
                    [
                        'id' => $regionalPolice['id'],
                        'name' => $regionalPolice['name'],

                        // 'sort' => 0,

                        // 'puskarda_code' => $regionalPolice['puskarda_code'],
                        // 'emp_id' => $regionalPolice['spptti_id'],
                        // 'spptti_id' => $regionalPolice['spptti_id'],
                        // 'satker_code' => $regionalPolice['satker_code'], // 'satker_code' => '00000000000000000000
                        // 'timezone' => $regionalPolice['timezone'],

                        // 'state' => ($regionalPolice['state'] == '1' ? 1 : 0),
                    ]
                );

                Police::updateOrCreate(
                    [
                        'id' => $regionalPolice['id'],
                    ],
                    [
                        'id' => $regionalPolice['id'],
                        'parent_id' => $regionalPolice['parent_id'],
                        
                        // 'class' => 'DAERAH',
                        
                        // 'puskarda_id' => $regionalPolice['puskarda_code'],
                        // 'emp_id' => $regionalPolice['spptti_id'],
                        // 'spptti_id' => $regionalPolice['spptti_id'],
                        // 'satker_code' => $regionalPolice['satker_code'], // 'satker_code' => '00000000000000000000

                        'name' => $regionalPolice['name'],
                        'full_name' => $regionalPolice['full_name'],

                        // 'address' => $regionalPolice['address'],
                        // 'timezone' => $regionalPolice['timezone'],

                        // 'is_active' => ($regionalPolice['state'] == '1' ? true : false),
                    ]
                );
            }
            DB::commit();

            $this->command->info('Regional polices table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

        DB::beginTransaction();
        try{
            $resortPolices = json_decode($this->getResortPolices(), true);

            foreach($resortPolices as $resortPolice){
                Polres::updateOrCreate(
                    [
                        'id' => $regionalPolice['id'],
                    ],
                    [
                        'id' => $regionalPolice['id'],
                        'name' => $regionalPolice['name'],
                        // 'polda_id' => $resortPolice['polda_id'],

                        // 'sort' => 0,

                        // 'puskarda_code' => $resortPolice['puskarda_code'],
                        // 'emp_id' => $resortPolice['spptti_id'],
                        // 'spptti_id' => $resortPolice['spptti_id'],
                        // 'satker_code' => $resortPolice['satker_code'],

                        // 'state' => ($regionalPolice['state'] == '1' ? 1 : 0),
                    ]
                );

                Police::updateOrCreate(
                    [
                        'id' => $resortPolice['id'],
                    ],
                    [
                        'id' => $resortPolice['id'],
                        'parent_id' => $resortPolice['polda_id'],
                        
                        // 'class' => 'RESOR',
                        
                        // 'puskarda_id' => $resortPolice['puskarda_code'],
                        // 'emp_id' => $resortPolice['spptti_id'],
                        // 'spptti_id' => $resortPolice['spptti_id'],
                        // 'satker_code' => $resortPolice['satker_code'],

                        'name' => $resortPolice['name'],
                        'full_name' => $resortPolice['full_name'],

                        // 'address' => $resortPolice['address'],
                        // 'postal_code' => $resortPolice['polres_zipcode'],

                        'category' => isset($resortPolice['category']) ? $resortPolice['category'] : null,

                        // 'is_active' => ($resortPolice['state'] == '1' ? true : false),
                    ]
                );
            }
            DB::commit();

            $this->command->info('Resort polices table seeded!');
        } catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getRegionalPolices()
    {
        $regionalPolices = File::get(base_path('master_seeder/regional_polices.json'));

        return $regionalPolices;
    }
   
    private function getResortPolices()
    {
        $resortPolices = File::get(base_path('master_seeder/resort_polices.json'));

        return $resortPolices;
    }
   
  
}
