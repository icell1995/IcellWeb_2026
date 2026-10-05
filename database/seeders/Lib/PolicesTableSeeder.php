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

                        'sort' => isset($regionalPolice['sort']) ? $regionalPolice['sort'] : 0,

                        // 'puskarda_code' => $regionalPolice['puskarda_code'],
                        // 'emp_id' => $regionalPolice['spptti_id'],
                        // 'spptti_id' => $regionalPolice['spptti_id'],
                        // 'satker_code' => $regionalPolice['satker_code'], // 'satker_code' => '00000000000000000000
                        'timezone' => isset($regionalPolice['timezone']) ? $regionalPolice['timezone'] : '+7',

                        'state' => (isset($regionalPolice['state']) && $regionalPolice['state'] == '1' ? 1 : 0),
                    ]
                );

                Police::updateOrCreate(
                    [
                        'id' => $regionalPolice['id'],
                    ],
                    [
                        'id' => $regionalPolice['id'],
                        'parent_id' => $regionalPolice['parent_id'],
                        
                        'class' => 'DAERAH',
                        
                        'puskarda_id' => isset($regionalPolice['puskarda_code']) ? $regionalPolice['puskarda_code'] : null,
                        // 'emp_id' => isset($regionalPolice['spptti_id']) ? $regionalPolice['spptti_id'] : null,
                        'spptti_id' => isset($regionalPolice['spptti_id']) ? $regionalPolice['spptti_id'] : null,
                        'satker_code' => isset($regionalPolice['satker_code']) ? $regionalPolice['satker_code'] : null,

                        'name' => $regionalPolice['name'],
                        'full_name' => $regionalPolice['full_name'],

                        'address' => isset($regionalPolice['address']) ? $regionalPolice['address'] : null,
                        'timezone' => isset($regionalPolice['timezone']) ? $regionalPolice['timezone'] : '+7',

                        'is_active' => (isset($regionalPolice['state']) && $regionalPolice['state'] == '1' ? true : false),
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
                        'id' => $resortPolice['id'],
                    ],
                    [
                        'id' => $resortPolice['id'],
                        'name' => $resortPolice['name'],
                        'polda_id' => $resortPolice['polda_id'],

                        'sort' => isset($resortPolice['sort']) ? $resortPolice['sort'] : 0,

                        // 'puskarda_code' => $resortPolice['puskarda_code'],
                        // 'emp_id' => $resortPolice['spptti_id'],
                        // 'spptti_id' => $resortPolice['spptti_id'],
                        // 'satker_code' => $resortPolice['satker_code'],

                        'state' => (isset($resortPolice['state']) && $resortPolice['state'] == '1' ? 1 : 0),
                    ]
                );

                Police::updateOrCreate(
                    [
                        'id' => $resortPolice['id'],
                    ],
                    [
                        'id' => $resortPolice['id'],
                        'parent_id' => $resortPolice['polda_id'],
                        
                        'class' => 'RESOR',
                        
                        'puskarda_id' => isset($resortPolice['puskarda_code']) ? $resortPolice['puskarda_code'] : null,
                        // 'emp_id' => isset($resortPolice['spptti_id']) ? $resortPolice['spptti_id'] : null,
                        'spptti_id' => isset($resortPolice['spptti_id']) ? $resortPolice['spptti_id'] : null,
                        'satker_code' => isset($resortPolice['satker_code']) ? $resortPolice['satker_code'] : null,

                        'name' => $resortPolice['name'],
                        'full_name' => $resortPolice['full_name'],

                        'address' => isset($resortPolice['address']) ? $resortPolice['address'] : null,
                        'postal_code' => isset($resortPolice['polres_zipcode']) ? $resortPolice['polres_zipcode'] : null,

                        'category' => isset($resortPolice['category']) ? $resortPolice['category'] : null,

                        'is_active' => (isset($resortPolice['state']) && $resortPolice['state'] == '1' ? true : false),
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
        $regionalPolices = File::get(base_path('master_seeder/regional_polices-new1.json'));

        return $regionalPolices;
    }
   
    private function getResortPolices()
    {
        $resortPolices = File::get(base_path('master_seeder/resort_polices-new1.json'));

        return $resortPolices;
    }
   
  
}
