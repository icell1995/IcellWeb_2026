<?php

namespace Database\Seeders\Opt;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Opt\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = $this->getStatuses();

        DB::beginTransaction();
        try{
            foreach ($statuses as $status) {
                Status::updateOrCreate(
                    [
                        'id' => $status['id']
                    ],
                    [
                        'id' => $status['id'],
                        'name' => $status['name'],
                        'code' => $status['code'],
                        'group_id' => $status['group_id']
                    ]
                );
            }
            DB::commit();

            $this->command->info('Berhasil menambahkan '.count($statuses).' data status');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    private function getStatuses(){
        return collect([
            [
                'id' => '1',
                'name' => 'DRAFT',
                'code' => 'DRAFT',
                'group_id' => '1'
            ],
            [
                'id' => '2',
                'name' => 'DIBUAT',
                'code' => 'CREATED',
                'group_id' => '1'
            ],
            [
                'id' => '3',
                'name' => 'MENUNGGU PERSETUJUAN',
                'code' => 'WAITING-APPROVAL',
                'group_id' => '1'
            ],
            [
                'id' => '4',
                'name' => 'REVISI',
                'code' => 'REVISION',
                'group_id' => '1'
            ],
            [
                'id' => '5',
                'name' => 'DISETUJUI',
                'code' => 'APPROVED',
                'group_id' => '1'
            ],
            [
                'id' => '6',
                'name' => 'DIAJUKAN',
                'code' => 'SUBMITTED',
                'group_id' => '1'
            ],
            [
                'id' => '7',
                'name' => 'MENUNGGU PERSETUJUAN UPLOAD',
                'code' => 'WAITING-APPROVAL-UPLOAD',
                'group_id' => '1'
            ],
            [
                'id' => '8',
                'name' => 'MENUNGGU VERIFIKASI',
                'code' => 'WAITING-VARIFICATION',
                'group_id' => '1'
            ],
            [
                'id' => '9',
                'name' => 'MENUNGGU DITANDATANGANI',
                'code' => 'WAITING-SIGNED',
                'group_id' => '1'
            ],
            [
                'id' => '10',
                'name' => 'DITANDATANGANI',
                'code' => 'SIGNED',
                'group_id' => '1'
            ],
            [
                'id' => '11',
                'name' => 'FINAL',
                'code' => 'FINAL',
                'group_id' => '1'
            ],
            [
                'id' => '86',
                'name' => 'COMPLETE VALID',
                'code' => 'COMPLETE VALID',
                'group_id' => '1'
            ],
        ]);
    }
}
