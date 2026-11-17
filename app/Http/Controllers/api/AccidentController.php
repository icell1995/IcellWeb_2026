<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
// use Illuminate\Pagination\Paginator;
// use Illuminate\Support\Collection;
// use Illuminate\Pagination\LengthAwarePaginator;
// use App\Models\User;
// use App\Models\Polres;

// use App\Models\SuratTugas;
// use App\Models\SuratTugasDetail;
// use App\Models\SuratTugasOfficerDetail;
// use Auth;
// use Http;
// use DB;
// use Validator;
use App\Models\Accident;
use App\Http\Resources\Accident as AccidentResource;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class AccidentController extends BaseController
{
    // public function index()

    // {
    //     $user = Auth::user();
    //     $a = ' ';
    //     switch($user->role_id){
    //         case 2:
    //             $polda = $user->polda_id;
    //             $polres = '-';
    //             $accident = DB::select('select 
    //                                         accidents.id as id
    //                                         , no_lp,concat_ws(\''.$a.'\'
    //                                         , officer_first_name,officer_last_name) as name
    //                                         , polres.name as polres_name
    //                                         , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
    //                                         , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
    //                                         , to_char(report_date, \'DD-MM-YYYY\') as report_date
    //                                         , road_name
    //                                         , damage_lose_desc as description
    //                                         , damage_lose_desc as tindak_lanjut
    //                                         , (select ref.name from ref where ref.id = selra_flag) as status
    //                                     from accidents
    //                                         left join polres on accidents.polres_id = polres.id
    //                                         left join polda on polres.polda_id = polda.id
    //                                     where polda.id = \''.$polda.'\' ');
    //         break;
        
    //         case 3:
    //             $polda = $user->polda_id;
    //             $polres = $user->polres_id;
    //             $accident = DB::select('select 
    //                                         accidents.id as id
    //                                         , no_lp,concat_ws(\''.$a.'\'
    //                                         , officer_first_name,officer_last_name) as name
    //                                         , polres.name as polres_name
    //                                         , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
    //                                         , to_char(accident_date, \'DD-MM-YYYY\') as accident_date
    //                                         , to_char(report_date, \'DD-MM-YYYY\') as report_date
    //                                         , road_name
    //                                         , damage_lose_desc as description
    //                                         , damage_lose_desc as tindak_lanjut
    //                                         , (select ref.name from ref where ref.id = selra_flag) as status
    //                                     from accidents
    //                                         left join polres on accidents.polres_id = polres.id
    //                                     where polres.id = \''.$polres.'\' ');
    //         break;
    //         default:
    //             $accident = DB::select('select 
    //                                         accidents.id as id
    //                                         , no_lp,concat_ws(\''.$a.'\'
    //                                         , officer_first_name,officer_last_name) as name
    //                                         , polres.name as polres_name
    //                                         , to_char(accidents.created_at, \'DD-MM-YYYY\') as created_at
    //                                         , to_char(accident_date, \'DD-Mon-YYYY\') as accident_date
    //                                         , to_char(report_date, \'DD-MM-YYYY\') as report_date
    //                                         , road_name
    //                                         , damage_lose_desc as description
    //                                         , damage_lose_desc as tindak_lanjut
    //                                         , (select ref.name from ref where ref.id = selra_flag) as status
    //                                     from accidents 
    //                                         left join polres on accidents.polres_id = polres.id');
    //     }
       
    //     // $accident = DB::select('select * from accidents');
    //     // dd($accident);
    //     // return $this->sendResponse(AccidentResource::collection($accident), 'Products retrieved successfully.');
    //     return $this->sendResponse($accident, 'Products retrieved successfully.');

    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $b = '-';
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $limitPhotoSql = in_array((int) ($user->role_id ?? 0), [2, 3], true) ? 'limit 1' : '';

        $sql = "
            SELECT (
                SELECT (
                    SELECT array_to_json(array_agg(row_to_json(w)))
                    FROM (
                        SELECT
                            accidents.id,
                            no_lp,
                            polres.name as polres_name,
                            concat_ws(' ', officer_first_name, officer_last_name) as name,
                            to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                            to_char(accident_date, 'DD-MM-YYYY') as accident_date,
                            to_char(report_date, 'DD-MM-YYYY') as report_date,
                            road_name,
                            damage_lose_desc as description,
                            damage_lose_desc as tindak_lanjut,
                            (select ref.name from ref where ref.id = selra_flag) as status,
                            (
                                SELECT array_to_json(array_agg(row_to_json(s)))
                                FROM (
                                    SELECT upload_image.id, name
                                    FROM upload_image
                                    WHERE upload_image.accident_id = accidents.id
                                    {$limitPhotoSql}
                                ) s
                            ) as photo
                        FROM accidents
                        LEFT JOIN polres on accidents.polres_id = polres.id
                        LEFT JOIN polda on polres.polda_id = polda.id
                        WHERE
                            CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                            AND
                            CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
                    ) w
                ) AS data
            )
        ";

        $result = DB::select($sql, [$polda, $b, $polda, $polres, $b, $polres]);
        $accident = !empty($result) ? json_decode($result[0]->data ?? '[]') : [];

        return $this->sendResponse($accident, 'Products retrieved successfully.');
    }

    public function search(Request $request)
    {
        $no_lp = (string) $request->input('no_lp');
        $user = Auth::user();
        $likeNoLp = '%' . $no_lp . '%';

        switch ($user->role_id) {
            case 2:
                $polda = (string) $user->polda_id;
                $accident = DB::select("
                    select
                        accidents.id as id,
                        no_lp,
                        concat_ws(' ', officer_first_name, officer_last_name) as name,
                        polres.name as polres_name,
                        to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                        to_char(accident_date, 'DD-MM-YYYY') as accident_date,
                        road_name,
                        damage_lose_desc as description,
                        damage_lose_desc as tindak_lanjut,
                        (select ref.name from ref where ref.id = selra_flag) as status
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    left join polda on polres.polda_id = polda.id
                    where polda.id = ? and accidents.no_lp ilike ?
                ", [$polda, $likeNoLp]);
                break;

            case 3:
                $polres = (string) $user->polres_id;
                $accident = DB::select("
                    select
                        accidents.id as id,
                        no_lp,
                        concat_ws(' ', officer_first_name, officer_last_name) as name,
                        polres.name as polres_name,
                        to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                        to_char(accident_date, 'DD-MM-YYYY') as accident_date,
                        road_name,
                        damage_lose_desc as description,
                        damage_lose_desc as tindak_lanjut,
                        (select ref.name from ref where ref.id = selra_flag) as status
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    where polres.id = ? and accidents.no_lp ilike ?
                ", [$polres, $likeNoLp]);
                break;

            default:
                $accident = DB::select("
                    select
                        accidents.id as id,
                        no_lp,
                        concat_ws(' ', officer_first_name, officer_last_name) as name,
                        polres.name as polres_name,
                        to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                        to_char(accident_date, 'DD-Mon-YYYY') as accident_date,
                        road_name,
                        damage_lose_desc as description,
                        damage_lose_desc as tindak_lanjut,
                        (select ref.name from ref where ref.id = selra_flag) as status
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    where accidents.no_lp ilike ?
                ", [$likeNoLp]);
        }

        return $this->sendResponse($accident, 'Products retrieved successfully.');
    }

    public function search_id(Request $request)
    {
        $id = (string) $request->input('id');
        $user = Auth::user();

        switch ($user->role_id) {
            case 2:
                $polda = (string) $user->polda_id;
                $accident = DB::select("
                    select
                        accidents.id as id,
                        no_lp,
                        concat_ws(' ', officer_first_name, officer_last_name) as name,
                        polres.name as polres_name,
                        to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                        to_char(accident_date, 'DD-MM-YYYY') as accident_date,
                        road_name,
                        damage_lose_desc as description,
                        damage_lose_desc as tindak_lanjut,
                        (select ref.name from ref where ref.id = selra_flag) as status
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    left join polda on polres.polda_id = polda.id
                    where polda.id = ? and accidents.id = ?
                ", [$polda, $id]);
                break;

            case 3:
                $polres = (string) $user->polres_id;
                $accident = DB::select("
                    select
                        accidents.id as id,
                        no_lp,
                        concat_ws(' ', officer_first_name, officer_last_name) as name,
                        polres.name as polres_name,
                        to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                        to_char(accident_date, 'DD-MM-YYYY') as accident_date,
                        road_name,
                        damage_lose_desc as description,
                        damage_lose_desc as tindak_lanjut,
                        (select ref.name from ref where ref.id = selra_flag) as status
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    where polres.id = ? and accidents.id = ?
                ", [$polres, $id]);
                break;

            default:
                $accident = DB::select("
                    select
                        accidents.id as id,
                        no_lp,
                        concat_ws(' ', officer_first_name, officer_last_name) as name,
                        polres.name as polres_name,
                        to_char(accidents.created_at, 'DD-MM-YYYY') as created_at,
                        to_char(accident_date, 'DD-Mon-YYYY') as accident_date,
                        road_name,
                        damage_lose_desc as description,
                        damage_lose_desc as tindak_lanjut,
                        (select ref.name from ref where ref.id = selra_flag) as status
                    from accidents
                    left join polres on accidents.polres_id = polres.id
                    where accidents.id = ?
                ", [$id]);
        }

        return $this->sendResponse($accident, 'Products retrieved successfully.');
    }

    public function search_dokumen(Request $request)
    {
        $id = (string) $request->input('id');
        $tables = [
            'berita_acara_membawa_saksi',
            'berita_acara_penyumpahan_saksi',
            'surat_panggilan_tersangka',
            'surat_perintah_penangkapan',
            'berita_acara_pemeriksaan_tersangka',
            'berita_acara_konfrontasi',
            'berita_acara_rekonstruksi',
            'sket_tkp',
            'surat_bantuan_penangkapan',
            'berita_penyerahan_tersangka',
            'berita_pelepasan_tersangka',
            'surat_perintah_penahanan',
            'berita_acara_penahanan',
            'permintaan_perpanjangan_penahanan',
            'surat_perpanjangan_penahanan',
            'berita_pengeluaran_penahanan',
            'surat_pembatalan_penahanan',
            'surat_pencabutan_pembatalan_penahanan',
            'berita_pencabutan_pembatalan_penahanan',
            'surat_penahanan_lanjutan',
            'berita_penahanan_lanjutan',
            'surat_izin_penggeledahan',
            'surat_perintah_penggeledahan',
            'surat_persetujuan_penggeledahan',
            'berita_acara_penggeledahan',
            'surat_izin_penyitaan',
            'surat_persetujuan_penyitaan',
            'surat_pengiriman_berkas_perkara',
            'tanda_terima_berkas_perkara',
            'surat_pengiriman_tersangka_barang_bukti',
            'berita_acara_serah_terima_tersangka',
            'surat_bantuan_penyelidikan',
            'surat_persetujuan_penyegelan',
            'berita_acara_penyegelan',
            'surat_permintaan_bantuan_labfor',
            'surat_hasil_pemeriksaan_labfor',
            'surat_permintaan_bantuan_identifikasi',
            'surat_hasil_pemeriksaan_identifikasi',
            'surat_blokir_rekening_bank',
            'berita_acara_blokir_rekening_bank',
            'surat_pembukaan_blokir_rekening_bank',
            'berita_acara_pembukaan_blokir_rekening_bank',
        ];

        $unions = [
            "select a.id as id, accident_id, a.created_at as created_at, a.name as dok_name, initial, ref.name ref_name, users.first_name || ' ' || users.last_name as officer_name, category, ref_grp.name as path from surat_perintah_membawa_saksi a left join ref on a.category = ref.id left join users on a.created_by = users.username left join ref_grp on ref.grp_id = ref_grp.id where accident_id = ?"
        ];
        $bindings = [$id];

        foreach ($tables as $tbl) {
            $unions[] = "select a.id as id, accident_id, a.created_at as created_at, a.name as dok_name, initial, ref.name ref_name, officer.first_name || ' ' || officer.last_name as officer_name, category, ref_grp.name as path from {$tbl} a left join ref on a.category = ref.id left join officer on a.created_by = officer.id left join ref_grp on ref.grp_id = ref_grp.id where accident_id = ?";
            $bindings[] = $id;
        }

        $sql = 'select * from (' . implode(' union all ', $unions) . ') dokumen order by dokumen.created_at desc';
        $dokumen = DB::select($sql, $bindings);

        return $this->sendResponse($dokumen, 'Products retrieved successfully.');
    }

    public function getPenyidik(Request $request){
        $id = (string) $request->input('id');
        $getdata = DB::select("select a.accident_id, a.officer_id, a.created_at, a.updated_at, concat_ws(' ', officer.first_name, officer.last_name) as name from surat_penyidikan a left join officer on a.officer_id = officer.id where accident_id = ?", [$id]);
        return $this->sendResponse($getdata, 'Products retrieved successfully.');
    }

    public function getCountAccident(Request $request){
        $b = '-';
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $jumlahTotalLaka = '0';
        $jumlahLakaInput = '0';

        $counts = DB::selectOne("
            select
                coalesce(count(*) filter (where accidents.selra_flag = 'S0106'), 0) as jumlah_sidik,
                coalesce(count(*) filter (where accidents.selra_flag <> 'S0106'), 0) as jumlah_selra
            from accidents
            left join polres on accidents.polres_id = polres.id
            left join polda on polres.polda_id = polda.id
            where
                CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                AND
                CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
        ", [$polda, $b, $polda, $polres, $b, $polres]);

        $data['jumlahTotalLaka'] = $jumlahTotalLaka;
        $data['jumlahLakaInput'] = $jumlahLakaInput;
        $data['jumlahLakaSidik'] = $counts->jumlah_sidik ?? 0;
        $data['jumlahLakaSelra'] = $counts->jumlah_selra ?? 0;
        return $this->sendResponse($data, 'Products retrieved successfully.');
    }

    public function getCountPersonal(){
        $user = Auth::user();
        $officerId = (string) ($user->officer_id ?? '');
        $counts = DB::selectOne("
            select
                coalesce(count(*), 0) as laka_terkirim,
                coalesce(count(*) filter (where a.selra_flag = 'S0107'), 0) as laka_proses,
                coalesce(count(*) filter (where a.selra_flag <> 'S0107'), 0) as laka_selesai
            from surat_penyidikan as sidik
            left join accidents as a on sidik.accident_id = a.id
            where sidik.officer_id = ?
        ", [$officerId]);

        $data['laka_terkirim'] = $counts->laka_terkirim ?? 0;
        $data['laka_proses'] = $counts->laka_proses ?? 0;
        $data['laka_selesai'] = $counts->laka_selesai ?? 0;
        return $this->sendResponse($data, 'Products retrieved successfully.');
    }

    public function getStatistik(Request $request){
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $dateFrom = !empty($request->dateFrom) && $request->dateFrom !== '-' ? Carbon::parse($request->dateFrom)->format('Y-m-d') : '-';
        $dateTo = !empty($request->dateTo) && $request->dateTo !== '-' ? Carbon::parse($request->dateTo)->format('Y-m-d') : '-';
        $kode = 'S01';
        $b = '-';

        $data = DB::select("
            select ref.name, coalesce(total.count, 0) as count
            from ref
            left join (
                select ref.id, ref.name, coalesce(count(accidents.id), 0) as count
                from accidents
                left join ref on accidents.selra_flag = ref.id
                left join polres on polres.id = accidents.polres_id
                left join polda on polda.id = polres.polda_id
                where
                    CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                    AND
                    CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
                    AND
                    CASE WHEN ? <> ? THEN accident_date BETWEEN ? AND ? ELSE TRUE END
                    AND polres.state = 1
                group by ref.id, ref.name
                order by ref.id
            ) as total on total.id = ref.id
            where grp_id = ?
        ", [
            $polda, $b, $polda,
            $polres, $b, $polres,
            $dateFrom, $b, $dateFrom, $dateTo,
            $kode,
        ]);

        return $this->sendResponse($data, 'Products retrieved successfully.');
    }

    public function getPhotoAccident(Request $request){
        $id = (string) $request->id;
        $photo = DB::select('select name from upload_image left join accidents on accidents.id = upload_image.accident_id where accidents.id = ?', [$id]);
        return $this->sendResponse($photo, 'Products retrieved successfully.');
    }
}
