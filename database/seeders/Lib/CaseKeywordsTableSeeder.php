<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\CaseKeyword;

class CaseKeywordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $caseKeywords = $this->getCaseKeywords();

        // Insert to table ranks
        DB::beginTransaction();
        try{
            foreach($caseKeywords as $caseKeyword){
                CaseKeyword::updateOrCreate(
                    [
                        'id' => $caseKeyword['id']
                    ],
                    [
                        'id' => $caseKeyword['id'],
                        'code' => $caseKeyword['code'],
                        'name' => $caseKeyword['name'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Table case_keywords seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getCaseKeywords(){
        $caseKeywords = collect([
            [
                'id' => '1',
                'code' => 'CKW-0001',
                'name' => 'TABRAK LARI',
            ],
            [
                'id' => '2',
                'code' => 'CKW-0002',
                'name' => 'KECELAKAAN MENONJOL',
            ],
            [
                'id' => '3',
                'code' => 'CKW-0003',
                'name' => 'MENINGGAL DUNIA',
            ],
            [
                'id' => '4',
                'code' => 'CKW-0004',
                'name' => 'LUKA BERAT',
            ],
            [
                'id' => '5',
                'code' => 'CKW-0005',
                'name' => 'LUKA RINGAN',
            ],
            [
                'id' => '6',
                'code' => 'CKW-0006',
                'name' => 'OVERLOAD',
            ],
            [
                'id' => '7',
                'code' => 'CKW-0007',
                'name' => 'OVER DIMENSI',
            ],
            [
                'id' => '8',
                'code' => 'CKW-0008',
                'name' => 'LAKA TUNGGAL',
            ],
            [
                'id' => '9',
                'code' => 'CKW-0009',
                'name' => 'LAKA KONTRA',
            ]
        ]);

        return $caseKeywords;
    }
}
