<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KodeLainnyaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Seeding Kode Lainnya...");
        $jsonPath = base_path('master_seeder/kode_lainnya.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("File Kode Lainnya.json tidak ditemukan di direktori master_seeder");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        $groupMap = [
            'Alasan Penghentian Perkara' => 'A09',
            'Pendamping' => 'P01',
            'Jenis Lokasi' => 'L01',
            'Jenis Jaminan' => 'J01',
            'Field pendamping jaminan' => 'J02'
        ];

        $groups = [];
        // find groups
        foreach($data as $r) {
            if (!empty($r["Master Data"]) && !empty($r["Field JSON"])) {
                $name = $r["Master Data"];
                if (isset($groupMap[$name])) {
                    $groups[$name] = $groupMap[$name];
                }
            }
        }

        // insert or update groups
        foreach($groups as $name => $id) {
            if ($id == "") continue;
            
            if (!DB::table('ref_grp')->where('id', $id)->exists()) {
                DB::table('ref_grp')->insert([
                    'id' => $id,
                    'name' => $name,
                    'state' => '1',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->command->info("Created RefGroup: $id ($name)");
            } else {
                DB::table('ref_grp')->where('id', $id)->update([
                    'name' => $name,
                    'updated_at' => now()
                ]);
                $this->command->info("Updated RefGroup: $id ($name)");
            }
        }

        // insert or update refs
        $count = 0;
        $sorts = [];
        foreach($data as $r) {
            if (!empty($r["Kode"]) && !empty($r["Nama / Uraian"]) && !empty($r["Master Data"])) {
                $master_data = $r["Master Data"];
                if (!isset($groupMap[$master_data])) continue;
                
                $grp_id = $groupMap[$master_data];
                $kode = $r["Kode"];
                $name = $r["Nama / Uraian"];
                
                if (!isset($sorts[$grp_id])) $sorts[$grp_id] = 1;
                $sort = $sorts[$grp_id]++;
                
                // Format kode agar seragam
                if ($kode === '-') {
                    $kodeStr = str_pad($sort, 2, '0', STR_PAD_LEFT);
                } else {
                    $kodeStr = str_pad($kode, 2, '0', STR_PAD_LEFT);
                }
                
                $ref_id = $grp_id . $kodeStr;
                
                if (!DB::table('ref')->where('id', $ref_id)->exists()) {
                    DB::table('ref')->insert([
                        'id' => $ref_id,
                        'name' => $name,
                        'grp_id' => $grp_id,
                        'sort' => $sort,
                        'state' => '1',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                } else {
                    DB::table('ref')->where('id', $ref_id)->update([
                        'name' => $name,
                        'sort' => $sort,
                        'updated_at' => now()
                    ]);
                    $count++;
                }
            }
        }
        $this->command->info("Processed $count references.");
    }
}
