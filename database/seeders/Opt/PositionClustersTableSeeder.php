<?php

namespace Database\Seeders\Opt;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Opt\PositionCluster;

class PositionClustersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positionClusters = $this->getData();

        DB::beginTransaction();
        try{
            foreach ($positionClusters as $positionCluster) {
                PositionCluster::updateOrCreate(
                    [
                        'id' => $positionCluster['id'],
                    ],
                    [
                        'id' => $positionCluster['id'],
                        'name' => $positionCluster['name'],
                        'is_can_signatory' => $positionCluster['is_can_signatory'],
                    ]
                );
            }
            DB::commit();

            $this->command->info('Berhasil menambahkan '.count($positionClusters).' data cluster jabatan');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getData()
    {
        return collect([
            [
                'id' => '1',
                'name' => 'KAPOLRES',
                'is_can_signatory' => true,
            ],
            [
                'id' => '2',
                'name' => 'WAKAPOLRES',
                'is_can_signatory' => true,
            ],
            [
                'id' => '3',
                'name' => 'KASAT LANTAS POLRES',
                'is_can_signatory' => true,
            ],
            [
                'id' => '4',
                'name' => 'KANIT GAKKUM SAT LANTAS POLRES',
                'is_can_signatory' => false,
            ],
            [
                'id' => '5',
                'name' => 'BANIT GAKKUM SAT LANTAS POLRES',
                'is_can_signatory' => false,
            ],
            [
                'id' => '6',
                'name' => 'ADMIN SAT LANTAS POLRES',
                'is_can_signatory' => false,
            ],
            [
                'id' => '7',
                'name' => 'DIRLANTAS POLDA',
                'is_can_signatory' => true,
            ],
            [
                'id' => '8',
                'name' => 'WADIRLANTAS POLDA',
                'is_can_signatory' => true,
            ],
            [
                'id' => '9',
                'name' => 'KASUBDIT GAKKUM DIT LANTAS POLDA',
                'is_can_signatory' => true,
            ],
            [
                'id' => '10',
                'name' => 'KASI LAKA SUBDIT GAKKUM DIT LANTAS POLDA',
                'is_can_signatory' => true,
            ],
            [
                'id' => '11',
                'name' => 'BAMIN LAKA SUBDIT GAKKUM DIT LANTAS POLDA',
                'is_can_signatory' => false,
            ],
            [
                'id' => '12',
                'name' => 'ADMIN DIT LANTAS POLDA',
                'is_can_signatory' => false,
            ],
            [
                'id' => '13',
                'name' => 'KAPOLDA',
                'is_can_signatory' => true,
            ],
            [
                'id' => '14',
                'name' => 'WAKAPOLDA',
                'is_can_signatory' => true,
            ],            
        ]);
    }
}
