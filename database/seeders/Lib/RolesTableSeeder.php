<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = $this->getRoles();
        
        DB::beginTransaction();
        try{
            foreach ($roles as $role) {
                Role::updateOrCreate(
                    [
                        'id' => $role['id']
                    ],
                    $role
                );
            }

            DB::commit();

            $this->command->info('Berhasil menambahkan '.count($roles).' data roles');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }                      
    }

    public function getRoles(){
        $roles = collect([
            [
                'id' => 0,
                'code' => '',
                'name' => 'Level 0',
                'full_name' => 'ICELL ADMINISTRATOR',
                'state' => 1,
                'is_active' => true,
                'level' => 4
            ],
            [
                'id' => 1,
                'code' => '',
                'name' => 'Level 1',
                'full_name' => 'ADMIN KORLANTAS',
                'state' => 1,
                'is_active' => true,
                'level' => 1
            ],
            [
                'id' => 2,
                'code' => '',
                'name' => 'Level 2',
                'full_name' => 'ADMIN POLDA',
                'state' => 1,
                'is_active' => true,
                'level' => 2
            ],
            [
                'id' => 3,
                'code' => '',
                'name' => 'Level 3',
                'full_name' => 'ADMIN SATKER POLRES',
                'state' => 1,
                'is_active' => true,
                'level' => 3
            ],
            [
                'id' => 4,
                'code' => '',
                'name' => 'PENYIDIK',
                'full_name' => 'KANIT GAKKUM/PENYIDIK SATKER POLRES',
                'state' => 1,
                'is_active' => true,
                'level' => 4
            ],
            [
                'id' => 5,
                'code' => '',
                'name' => 'PENANDATANGAN',
                'full_name' => 'KASAT LANTAS/WAKAPOLRES/KAPOLRES SATKER POLRES/KASUBDIT GAKKUM DIT LANTAS POLDA',
                'state' => 1,
                'is_active' => true,
                'level' => 5
            ],
            [
                'id' => 6,
                'code' => '',
                'name' => 'Level 6',
                'full_name' => '',
                'state' => 1,
                'is_active' => true,
                'level' => 6
            ],
        ]);

        return $roles;
    }
}
