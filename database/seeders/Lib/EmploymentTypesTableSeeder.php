<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\EmploymentType;

class EmploymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->getData();

        DB::beginTransaction();
        try{
            foreach($data as $item){
                EmploymentType::updateOrCreate(
                    [
                        'id' => $item['id']
                    ],
                    [
                        'emp_id' => $item['emp_id'],
                        'name' => $item['name'],
                        'full_name' => $item['full_name'],
                    ]
                );
            }
            DB::commit();

            $this->command->info('Table lib_employment_types seeded');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getData(){
        return collect([
            [
                'id' => '1',
                'emp_id' => '1',
                'name' => 'Anggota Polri',
                'full_name' => 'Anggota Polri',
            ],
            [
                'id' => '2',
                'emp_id' => '2',
                'name' => 'PNS Polri',
                'full_name' => 'Pegawai Negeri Sipil Polri',
            ]
        ]);
    }
}
