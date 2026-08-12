<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Lib\Rank;

class RanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ranks = collect([
            [
                'id' => '7',
                'emp_id' => '7',
                'name' => 'JEND. POL',
                'full_name' => "JENDERAL POLISI",
                'code' => 'RANK-0001',
                'sort' => 1,
                "employment_type_id" => "1",
                "is_active" => false
            ],
            [
                'id' => '8',
                'emp_id' => '8',
                'name' => 'KOMJEN POL.',
                'full_name' => "KOMISARIS JENDERAL POLISI",
                'code' => 'RANK-0002',
                'sort' => 2,
                "employment_type_id" => "1",
                "is_active" => false
            ],
            [
                'id' => '9',
                'emp_id' => '9',
                'name' => 'IRJEN POL.',
                'full_name' => "INSPEKTUR JENDERAL POLISI",
                'code' => 'RANK-0003',
                'sort' => 3,
                "employment_type_id" => "1",
                "is_active" => false
            ],
            [
                'id' => '10',
                'emp_id' => '10',
                'name' => 'BRIGJEN POL.',
                'full_name' => "BRIGADIR JENDERAL POLISI",
                'code' => 'RANK-0004',
                'sort' => 4,
                "employment_type_id" => "1",
                "is_active" => false
            ],
            [
                'id' => '11',
                'emp_id' => '11',
                'name' => 'KOMBES POL.',
                'full_name' => "KOMISARIS BESAR POLISI",
                'code' => 'RANK-0005',
                'sort' => 5,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '12',
                'emp_id' => '12',
                'name' => 'AKBP',
                'full_name' => "AJUN KOMISARIS BESAR POLISI",
                'code' => 'RANK-0006',
                'sort' => 6,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '13',
                'emp_id' => '13',
                'name' => 'KOMPOL',
                'full_name' => "KOMISARIS POLISI",
                'code' => 'RANK-0007',
                'sort' => 7,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '14',
                'emp_id' => '14',
                'name' => 'AKP',
                'full_name' => "AJUN KOMISARIS POLISI",
                'code' => 'RANK-0008',
                'sort' => 8,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '15',
                'emp_id' => '15',
                'name' => 'IPTU',
                'full_name' => "INSPEKTUR POLISI SATU",
                'code' => 'RANK-0009',
                'sort' => 9,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '16',
                'emp_id' => '16',
                'name' => 'IPDA',
                'full_name' => "INSPEKTUR POLISI DUA",
                'code' => 'RANK-0010',
                'sort' => 10,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '17',
                'emp_id' => '17',
                'name' => 'AIPTU',
                'full_name' => "AJUN INSPEKTUR POLISI SATU",
                'code' => 'RANK-0011',
                'sort' => 11,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '18',
                'emp_id' => '18',
                'name' => 'AIPDA',
                'full_name' => "AJUN INSPEKTUR POLISI DUA",
                'code' => 'RANK-0012',
                'sort' => 12,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '19',
                'emp_id' => '19',
                'name' => 'BRIPKA',
                'full_name' => "BRIGADIR POLISI KEPALA",
                'code' => 'RANK-0013',
                'sort' => 13,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '20',
                'emp_id' => '20',
                'name' => 'BRIGPOL',
                'full_name' => "BRIGADIR POLISI",
                'code' => 'RANK-0014',
                'sort' => 14,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '21',
                'emp_id' => '21',
                'name' => 'BRIPTU',
                'full_name' => "BRIGADIR POLISI SATU",
                'code' => 'RANK-0015',
                'sort' => 15,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '22',
                'emp_id' => '22',
                'name' => 'BRIPDA',
                'full_name' => "BRIGADIR POLISI DUA",
                'code' => 'RANK-0016',
                'sort' => 16,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '23',
                'emp_id' => '23',
                'name' => 'ABRIGPOL',
                'full_name' => "AJUN BRIGADIR POLISI",
                'code' => 'RANK-0017',
                'sort' => 17,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '24',
                'emp_id' => '24',
                'name' => 'ABRIPTU',
                'full_name' => "AJUN BRIGADIR POLISI SATU",
                'code' => 'RANK-0018',
                'sort' => 18,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '25',
                'emp_id' => '25',
                'name' => 'ABRIPDA',
                'full_name' => "AJUN BRIGADIR POLISI DUA",
                'code' => 'RANK-0019',
                'sort' => 19,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '26',
                'emp_id' => '26',
                'name' => 'BHARAKA',
                'full_name' => "BHAYANGKARA KEPALA",
                'code' => 'RANK-0020',
                'sort' => 20,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '27',
                'emp_id' => '27',
                'name' => 'BHARATU',
                'full_name' => "BHAYANGKARA SATU",
                'code' => 'RANK-0021',
                'sort' => 21,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '28',
                'emp_id' => '28',
                'name' => 'BHARADA',
                'full_name' => "BHAYANGKARA DUA",
                'code' => 'RANK-0022',
                'sort' => 22,
                "employment_type_id" => "1",
                "is_active" => true
            ],
            [
                'id' => '52',
                'emp_id' => '52',
                'name' => 'NON POLRI',
                'full_name' => "NON POLRI",
                'code' => 'RANK-0023',
                'sort' => 23,
                "employment_type_id" => "1",
                "is_active" => true
            ],

            [
                "id" => '33',
                "emp_id" => '33',
                "name" => "IV E",
                "full_name" => "PEMBINA UTAMA",
                "code" => 'RANK-0024',
                "sort" => 24,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '34',
                "emp_id" => '34',
                "name" => "IV D",
                "full_name" => "PEMBINA UTAMA MADYA",
                "code" => 'RANK-0025',
                "sort" => 25,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '35',
                "emp_id" => '35',
                "name" => "IV C",
                "full_name" => "PEMBINA UTAMA MUDA",
                "code" => 'RANK-0026',
                "sort" => 26,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '36',
                "emp_id" => '36',
                "name" => "IV B",
                "full_name" => "PEMBINA TINGKAT I",
                "code" => 'RANK-0027',
                "sort" => 27,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '37',
                "emp_id" => '37',
                "name" => "IV A",
                "full_name" => "PEMBINA",
                "code" => 'RANK-0028',
                "sort" => 28,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '38',
                "emp_id" => '38',
                "name" => "III D",
                "full_name" => "PENATA TINGKAT I",
                "code" => 'RANK-0029',
                "sort" => 29,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '39',
                "emp_id" => '39',
                "name" => "III C",
                "full_name" => "PENATA",
                "code" => 'RANK-0030',
                "sort" => 30,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '40',
                "emp_id" => '40',
                "name" => "III B",
                "full_name" => "PENATA MUDA TINGKAT I",
                "code" => 'RANK-0031',
                "sort" => 31,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '41',
                "emp_id" => '41',
                "name" => "III A",
                "full_name" => "PENATA MUDA",
                "code" => 'RANK-0032',
                "sort" => 32,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '42',
                "emp_id" => '42',
                "name" => "II D",
                "full_name" => "PENGATUR TINGKAT I",
                "code" => 'RANK-0033',
                "sort" => 33,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '43',
                "emp_id" => '43',
                "name" => "II C",
                "full_name" => "PENGATUR",
                "code" => 'RANK-0034',
                "sort" => 34,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '44',
                "emp_id" => '44',
                "name" => "II B",
                "full_name" => "PENGATUR MUDA TINGKAT I",
                "code" => 'RANK-0035',
                "sort" => 35,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '45',
                "emp_id" => '45',
                "name" => "II A",
                "full_name" => "PENGATUR MUDA",
                "code" => 'RANK-0036',
                "sort" => 36,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '46',
                "emp_id" => '46',
                "name" => "I D",
                "full_name" => "JURU TINGKAT I",
                "code" => 'RANK-0037',
                "sort" => 37,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '47',
                "emp_id" => '47',
                "name" => "I C",
                "full_name" => "JURU",
                "code" => 'RANK-0038',
                "sort" => 38,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '48',
                "emp_id" => '48',
                "name" => "I B",
                "full_name" => "JURU MUDA TINGKAT I",
                "code" => 'RANK-0039',
                "sort" => 39,
                "employment_type_id" => "2",
                "is_active" => true
            ],
            [
                "id" => '49',
                "emp_id" => '49',
                "name" => "I A",
                "full_name" => "JURU MUDA",
                "code" => 'RANK-0040',
                "sort" => 40,
                "employment_type_id" => "2",
                "is_active" => true
            ],
        ]);

        // Insert to table ranks
        DB::beginTransaction();
        try{
            foreach ($ranks as $rank) {
                Rank::updateOrCreate(
                    [
                        'id' => $rank['id']
                    ],
                    [
                        'id' => $rank['id'],
                        'name' => $rank['name'],
                        'full_name' => $rank['full_name'],
                        'sort' => $rank['sort'],
                        'emp_id' => $rank['emp_id'],
                        'code' => $rank['code'],
                        'employment_type_id' => $rank['employment_type_id'],
                        'is_active' => $rank['is_active'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Success inserting data to table ranks');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
