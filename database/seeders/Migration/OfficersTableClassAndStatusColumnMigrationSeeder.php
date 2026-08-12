<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Officer;

class OfficersTableClassAndStatusColumnMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try{
            $officers = Officer::all();

            foreach ($officers as $officer) {
                if(empty($officer->class) || $officer->class == "false"){
                    if(isset($officer->user->role_id)){
                        if(in_array($officer->user->role_id, [1,2,3])){
                            Officer::where('id', $officer->id)->update(['class' => 'ADMIN']);
                        }else{
                            Officer::where('id', $officer->id)->update(['class' => 'MEMBER']);
                        }
                    }
                }
            }
            
            foreach ($officers as $officer) {
                if(empty($officer->status)){
                    Officer::where('id', $officer->id)->update(['status' => 'PRESENT']);
                }
            }

            DB::commit();

            $this->command->info('Success');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
