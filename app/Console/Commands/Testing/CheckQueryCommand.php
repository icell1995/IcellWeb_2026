<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Lib\Location;
use App\Models\InvolvedPeople;
use App\Models\Officer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CheckQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:query';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$log = DB::connection('mongodb')->collection('log')->get();
        $query = Officer::select(
            'officers.id',
            \App\Helpers\PeopleNameHelper::getFullNameQueryExpression(),
            'officers.rank_short_name',
            'polres.name as polres_name',
            DB::raw('COALESCE(p21_lalu.jumlah_lidik, 0) AS total_p21_lalu'),
            DB::raw('COALESCE(sp3_lalu.jumlah_lidik, 0) AS total_sp3_lalu'),
            DB::raw('COALESCE(diversi_lalu.jumlah_lidik, 0) AS total_diversi_lalu'),
            DB::raw('COALESCE(pom_tni_lalu.jumlah_lidik, 0) AS total_pom_tni_lalu'),
            DB::raw('COALESCE(sp2lid_lalu.jumlah_lidik, 0) AS total_sp2lid_lalu'),
            'foto.avatars'
        )
        ->leftJoin(
            DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik 
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id 
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0101' 
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS p21_lalu"),
            'officers.register_number',
            '=',
            'p21_lalu.lidik_id'
        )
        ->leftJoin(
            DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik 
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id 
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0102' 
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS sp3_lalu"),
            'officers.register_number',
            '=',
            'sp3_lalu.lidik_id'
        )
        ->leftJoin(
            DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik 
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id 
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0103' 
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS diversi_lalu"),
            'officers.register_number',
            '=',
            'diversi_lalu.lidik_id'
        )
        ->leftJoin(
            DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik 
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id 
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0104' 
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS pom_tni_lalu"),
            'officers.register_number',
            '=',
            'pom_tni_lalu.lidik_id'
        )
        ->leftJoin(
            DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik 
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id 
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0108' 
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS sp2lid_lalu"),
            'officers.register_number',
            '=',
            'sp2lid_lalu.lidik_id'
        )
        ->leftJoin(
            DB::raw("(SELECT surat_perintah_penyelidikan_document_officers.register_number AS lidik_id, COUNT(accident_id) AS jumlah_lidik 
                      FROM doc.surat_perintah_penyelidikan_documents
                      JOIN doc.surat_perintah_penyelidikan_document_officers ON doc.surat_perintah_penyelidikan_documents.id = doc.surat_perintah_penyelidikan_document_officers.surat_perintah_penyelidikan_document_id
                      JOIN accidents ON accidents.id = surat_perintah_penyelidikan_documents.accident_id 
                      WHERE doc.surat_perintah_penyelidikan_documents.deleted_at IS NULL AND doc.surat_perintah_penyelidikan_documents.status_id = '86' AND accidents.selra_flag = 'S0108' 
                      GROUP BY surat_perintah_penyelidikan_document_officers.register_number) AS sp2lid_lalu"),
            'officers.register_number',
            '=',
            'sp2lid_lalu.lidik_id'
        )
        ->leftJoin(
            DB::raw('(SELECT users.avatar AS avatars, users.officer_id AS users_id FROM users) AS foto'),
            'officers.id',
            '=',
            'foto.users_id'
        )
        ->leftJoin('polda', 'polda.id', '=', 'officers.polda_id')
        ->leftJoin('polres', 'polres.id', '=', 'officers.polres_id')
        ->limit(5)->get();

        dd($query);
      
        die;
        $districts = File::get(base_path('master_seeder/districts.json'));
        $districts = json_decode($districts, true);

        // count all districts with multiple same name with in the database
        foreach($districts as $district) {
            $count = Location::where('class', 'DISTRICT')
                ->where('name', $district['Nama_Kecamatan'])
                ->count();

            if($count < 1) {
                // $this->info('Not Exist: ' . $district['Nama_Kecamatan'] . ' - ' . $count);
            }elseif($count > 1) {
                $this->info('Same Name: ' .$district['Nama_Kecamatan'] . ' - ' . $count);
            }
        }

    }
}
