<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\DocumentClassification;

class DocumentClassificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentClassifications = [
            [
                'id' => '1',
                'code' => 'DC-SPDP-SULIT',
                'name' => 'SULIT',
                'group' => 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN'
            ],
            [
                'id' => '2',
                'code' => 'DC-SPDP-BIASA',
                'name' => 'BIASA',
                'group' => 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN'
            ],
            [
                'id' => '3',
                'code' => 'DC-SPDP-MUDAH',
                'name' => 'MUDAH',
                'group' => 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN'
            ],
        ];

        DB::beginTransaction();
        try{
            foreach ($documentClassifications as $documentClassification) {
                DocumentClassification::updateOrCreate(
                    [
                        'id' => $documentClassification['id']
                    ],
                    $documentClassification
                );
            }

            DB::commit();

            $this->command->info('Document classifications table seeded!');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
