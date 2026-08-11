<?php

namespace Database\Seeders\Migration;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Police;
use App\Models\Lib\Position;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Officer;

class AuthorizedSigantoriesAttachOfficersMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateAttachAuthorizedSignatriesToOfficers();
    }

    private function migrateAttachAuthorizedSignatriesToOfficers()
    {
        $positionClusters = [
            'KASAT LANTAS' => '3',
            'PS. KASAT LANTAS' => '3',
            'PLT. KASAT LANTAS' => '3',
            'KAPOLRES' => '1',
            'WAKAPOLRES' => '2',
            'KASUBDITGAKKUM' => '9',
            'PS. KASUBDITGAKKUM' => '9',
            'KANIT GAKKUM' => '4',
            'PS. KANIT GAKKUM' => '4',
        ];

        $ranks = [
            'BRIPTU' => '21',
            'AIPTU' => '17',
            'BRIGADIR' => '20',
            'AIPDA' => '18',
            'BRIPDA' => '22',
            'BRIPKA' => '19',
            'IPDA' => '16',
            'AKP' => '14',
            'AKBP' => '12',
            'IPTU' => '15',
            'KOMBESPOL' => '11',
            'KOMJENPOL' => '8',
            'KOMPOL' => '13',
            'BRIGPOL' => '20',
            "" => null
        ];

        
        
        DB::beginTransaction();
        try{
            $authorizedSigantories = AuthorizedSignatory::all();
            
            foreach ($authorizedSigantories as $authorizedSigantory) {              
                if(!empty($authorizedSigantory->register_number)){
                    $regionalPolice = Police::with('parent')->find($authorizedSigantory->polres_id);
                    $position = Position::where('position_cluster_id', $positionClusters[$authorizedSigantory->position_id])->where('police_id', $authorizedSigantory->polres_id)->first();
                    
                    Officer::updateOrCreate(
                        [
                            'register_number' => $authorizedSigantory->register_number,
                        ],
                        [
                            'id' => $authorizedSigantory->register_number,

                            'first_title' => $authorizedSigantory->first_title,
                            'first_name' => $authorizedSigantory->first_name,
                            'last_name' => $authorizedSigantory->last_name,
                            'last_title' => $authorizedSigantory->last_title,
        
                            'register_number' => $authorizedSigantory->register_number,
                            'police_id' => $authorizedSigantory->polres_id,
                            'identity_number' => $authorizedSigantory->identity_number,
        
                            'email' => $authorizedSigantory->email,
                            'phone_number' => $authorizedSigantory->phone_number,
        
                            'position_id' => $position->id ?? null,
                            'rank_id' => $ranks[$authorizedSigantory->rank_id],
                            
                            'polda_id' => $regionalPolice->parent->id ?? null,
                            'polres_id' => $authorizedSigantory->polres_id,
                            
                            'state' => '1',
                            'is_active' => true,
                            'class' => ($positionClusters[$authorizedSigantory->position_id] != '04') ? 'SIGNATORY' : 'MEMBER',

                            'rank_short_name' => $authorizedSigantory->rank_id,
                            'position_short_name' => '-',
                            'sebagai_kepala' => '-',
                        ]
                    );
                }
                
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
