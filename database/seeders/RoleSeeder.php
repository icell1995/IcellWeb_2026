<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\Lib\Role::create([
        //     'id'=>1,
        //     'name' => 'Level 1',
        //     'description' => 'Level Admin Korlantas'
        // ]);

        // \App\Models\Lib\Role::create([
        //     'id'=>2,
        //     'name' => 'Level 2',
        //     'description' => 'Level Admin Polda'
        // ]);

        // \App\Models\Lib\Role::create([
        //     'id'=>3,
        //     'name' => 'Level 3',
        //     'description' => 'Level Admin Polres'
        // ]);

        // \App\Models\Lib\Role::create([
        //     'id'=>4,
        //     'name' => 'Level 4',
        //     'description' => 'Level Polres'
        // ]);


        // \App\Models\Lib\Role::create([
        //     'id'=>5,
        //     'name' => 'Level 5',
        //     'description' => 'Level Guesr'
        // ]);

        // \App\Models\Lib\Role::create([
        //     'id'=>6,
        //     'name' => 'Level 6',
        //     'description' => 'Level Guesr'
        // ]);

        \App\Models\Lib\Role::create([
            // 'id'=>1,
            'name' => 'Level 1',
            'state'=> 1
      ]);
      
      \App\Models\Lib\Role::create([
        // 'id'=>2,
            'name' => 'Level 2',
            'state'=> 1
      ]);
      
     \App\Models\Lib\Role::create([
        // 'id'=>3,
            'name' => 'Level 3',
            'state'=> 1
      ]);

      \App\Models\Lib\Role::create([
        // 'id'=>4,
            'name' => 'Level 4',
            'state'=> 1
      ]);

      \App\Models\Lib\Role::create([
        // 'id'=>5,
            'name' => 'Level 5',
            'state'=> 1
      ]);

      \App\Models\Lib\Role::create([
        // 'id'=>6,
            'name' => 'Level 6',
            'state'=> 1
      ]);

     
    }
}
