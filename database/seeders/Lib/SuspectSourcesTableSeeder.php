<?php

namespace Database\Seeders\Lib;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\SuspectSource;

class SuspectSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suspectSources = [
            [
                'id' => '1',
                'code' => 'SS-LHGP-TASDL',
                'group'=> 'LAPORAN_HASIL_GELAR_PERKARA',
                'name' => 'Tersangka adalah saksi dalam LP'
            ],       
            [
                'id' => '2',
                'code' => 'SS-LHGP-TATDL',
                'group' => 'LAPORAN_HASIL_GELAR_PERKARA',
                'name' => 'Tersangka adalah terlapor dalam LP'
            ], 
            [
                'id' => '3',
                'code' => 'SS-LHGP-TB',
                'group' => 'LAPORAN_HASIL_GELAR_PERKARA',
                'name' => 'Tersangka baru'
            ],
            
            [
                'id' => '4',
                'code' => 'SS-SKTPT-TDDDLP',
                'group' => 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA',
                'name' => 'Tersangka disebutkan di dalam Laporan Polisi'
            ],
            [
                'id' => '5',
                'code' => 'SS-SKTPT-TDMGPPT',
                'group' => 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA',
                'name' => 'Tersangka ditetapkan melalui Gelar Perkara Penetapan Tersangka'
            ]
        ];
        
        DB::beginTransaction();
        try{
            foreach ($suspectSources as $suspectSource) {
                SuspectSource::updateOrCreate(
                    [
                        'id' => $suspectSource['id']
                    ],
                    [
                        'id' => $suspectSource['id'],
                        'code' => $suspectSource['code'],
                        'name' => $suspectSource['name'],
                        'group' => $suspectSource['group'],
                    ]
                );
            }

            DB::commit();

            $this->command->info('Success');
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
