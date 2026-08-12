<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Permission::create([
            //   'id'=>1,
            'name' => 'view-data',
            'state'=> 1 // id 1,
     ]);
     \App\Models\Permission::create([
        // 'id'=>2,
            'name' => 'create-data',
            'state'=> 1 // id 2
     ]);
    \App\Models\Permission::create([
        // 'id'=>1,
            'name' => 'edit-data',
            'state'=> 1 // id 3
     ]);
     \App\Models\Permission::create([
        // 'id'=>1,
            'name' => 'update-data',
            'state'=> 1 // id 4
     ]);
     \App\Models\Permission::create([
        // 'id'=>1,
            'name' => 'delete-data',
            'state'=> 1 // id 5
     ]);
    
     \App\Models\Permission::create([
        // 'id'=>1,
        'name' => 'manage-users',
        'state'=> 1 // id 6
    ]);

    \App\Models\Permission::create([
        // 'id'=>1,
        'name' => 'manage-permissions',
        'state'=> 1 // id 7
    ]);

    \App\Models\Permission::create([
        // 'id'=>1,
        'name' => 'manage-roles',
        'state'=> 1 // id 8
    ]);
     
     $level1 = \App\Models\Lib\Role::where('name', 'Level 1')->first();
     $level1->permissions()->attach([1, 2, 3,4,5,6,7,8]);
     
     $level2 = \App\Models\Lib\Role::where('name', 'Level 2')->first();
     $level2->permissions()->attach([1, 2, 3, 4,5,6]);
     
     $level3 = \App\Models\Lib\Role::where('name', 'Level 3')->first();
     $level3->permissions()->attach([1, 2, 3, 4, 6 ]);
    
      
     $level4 = \App\Models\Lib\Role::where('name', 'Level 4')->first();
     $level4->permissions()->attach([1, 2, 3 ]);
    
      
     $level5 = \App\Models\Lib\Role::where('name', 'Level 5')->first();
     $level5->permissions()->attach([1,2]);
    
      
     $level6 = \App\Models\Lib\Role::where('name', 'Level 6')->first();
     $level6->permissions()->attach([1]);
    }
}
