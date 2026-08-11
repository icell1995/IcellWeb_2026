<?php

namespace Database\Seeders\Opt;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Opt\Group;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = $this->getGroups();

        DB::beginTransaction();
        try{
            foreach ($groups as $group) {
                Group::updateOrCreate(
                    [
                        'id' => $group['id']
                    ],
                    [
                        'id' => $group['id'],
                        'name' => $group['name'],
                        'code' => $group['code']
                    ]
                );
            }
        
            DB::commit();

            $this->command->info('Groups table seeded!');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }   

    private function getGroups(){
        return collect([
            [
                'id' => "1",
                'name' => 'DOCUMENT_STATUS',
                'code' => 'DOCUMENT-STATUS'
            ],
        ]);
    }
}
