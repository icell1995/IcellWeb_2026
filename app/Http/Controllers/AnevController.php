<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Polda;
use App\Models\Polres;
use App\Exports\ExportAnev;
use App\Exports\ExportReportIndividu;
use App\Models\Officer;
use Carbon\Carbon;
// use App\Http\Controllers\Excel;
// use Excel;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use App\Traits\AnevQueryTraits;
use App\Helpers\AppModeHelper;

class AnevController extends Controller
{
    use AnevQueryTraits;

    public function index_anev()
    {
        $user = Auth::user();
        $roleData = Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                $polda = Polda::where('id', '=', $user->polda_id)->whereNotIn('id', ['77', '90', '99', '80'])->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                break;
            case 3:
                $polda = Polda::where('id', '=', $user->polda_id)->whereNotIn('id', ['77', '90', '99', '80'])->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                break;
            case 4:
                $polda = Polda::where('id', '=', $user->polda_id)->whereNotIn('id', ['77', '90', '99', '80'])->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                break;
            case 5:
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                break;
            default:
                $polda = Polda::whereNotIn('id', ['77', '90', '99', '80'])->get();
                $polres = Polres::all();
                break;
        }

        return view('anev/anev', compact('polda', 'polres'));
    }

    public function get_report_anev(Request $request)
    {
        try {
            // Sanitisasi input polda & polres dari karakter non-alfanumerik
            $rawPolda  = $request->input('polda') ?? '-';
            $poldaId   = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$rawPolda);
            if (empty($poldaId)) {
                $poldaId = '-';
            }

            $rawPolres = $request->input('polres');
            $polresId  = !empty($rawPolres) ? preg_replace('/[^A-Za-z0-9_-]/', '', (string)$rawPolres) : '-';
            if (empty($polresId)) {
                $polresId = '-';
            }

            $start_date_then = date('Y-m-d', strtotime($request->input('date_from')));
            $end_date_then   = date('Y-m-d', strtotime($request->input('date_to')));
            $start_date_now  = date('Y-m-d', strtotime($request->input('date_from_now')));
            $end_date_now    = date('Y-m-d', strtotime($request->input('date_to_now')));

            switch ((int) $request->input('type', 1)) {
                case 2:
                    $dasar = "TANGGAL DILAPORKAN";
                    $fieldTanggal = "report_date";
                    break;
                default:
                    $dasar = "TANGGAL KEJADIAN";
                    $fieldTanggal = "accident_date";
            }

            // Jika semua polda dan semua polres dipilih, abaikan filter polres
            if ($poldaId === '-' && $polresId === '-') {
                $polresId = '-';
            }

            // Validasi kombinasi polda & polres (polres harus milik polda)
            if ($poldaId !== '-' && $polresId !== '-') {
                $validCombo = DB::table('polres')
                    ->where('id', $polresId)
                    ->where('polda_id', $poldaId)
                    ->exists();

                if (!$validCombo) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Polres tidak sesuai dengan Polda yang dipilih.'
                    ], 422);
                }
            }

            // -------------------------------
            // 2) Ambil data API IRSMS (Hanya jika APP_MODE=PRODUCTION)
            // -------------------------------
            if (AppModeHelper::isApiEnabled('AnevController@get_report_anev')) {
                $totalLakaUrl = rtrim((string) config('services.irsms.base_url'), '/') . '/getTotalLaka';
                $totalLakaKey = (string) config('services.irsms.total_laka_key');

                $apiLalu = Http::withHeaders([
                    'Key' => $totalLakaKey,
                    'Content-Type' => 'application/json'
                ])->timeout(10)->withQueryParameters([
                    'start_date' => $start_date_then,
                    'end_date'   => $end_date_then
                ])->get($totalLakaUrl)->json();

                $apiIni = Http::withHeaders([
                    'Key' => $totalLakaKey,
                    'Content-Type' => 'application/json'
                ])->timeout(10)->withQueryParameters([
                    'start_date' => $start_date_now,
                    'end_date'   => $end_date_now
                ])->get($totalLakaUrl)->json();
            } else {
                $apiLalu = ['result' => []];
                $apiIni  = ['result' => []];
            }

            $dataApiLalu = $apiLalu['result'] ?? [];
            $dataApiIni  = $apiIni['result'] ?? [];

            // Helper group data API
            $groupApi = function ($data, $byPolda = true) {
                $result = [];
                foreach ($data as $item) {
                    $key = $byPolda ? ($item['polda'] ?? null) : ($item['polres'] ?? null);
                    if ($key === null) continue;
                    if (!isset($result[$key])) {
                        $result[$key] = [
                            'kode' => $key,
                            'jumlah_laka' => 0
                        ];
                    }
                    $result[$key]['jumlah_laka'] += (int) ($item['jumlah_laka'] ?? 0);
                }
                return $result;
            };

            // Terapkan filter API agar konsisten dengan pilihan polda/polres
            if ($poldaId === '-' && $polresId === '-') {
                // Semua polda dan semua polres: group by polda
                $apiLaluGrouped = $groupApi($dataApiLalu, true);
                $apiIniGrouped  = $groupApi($dataApiIni, true);
            } else if ($poldaId !== '-' && $polresId === '-') {
                // Polda spesifik, semua polres: filter by polda, group by polres
                $filteredLalu = array_filter($dataApiLalu, function ($d) use ($poldaId) {
                    return ($d['polda'] ?? null) === $poldaId;
                });
                $filteredIni = array_filter($dataApiIni, function ($d) use ($poldaId) {
                    return ($d['polda'] ?? null) === $poldaId;
                });
                $apiLaluGrouped = $groupApi($filteredLalu, false);
                $apiIniGrouped  = $groupApi($filteredIni, false);
            } else if ($poldaId !== '-' && $polresId !== '-') {
                // Polda dan polres spesifik: filter by polda & polres, group by polres
                $filteredLalu = array_filter($dataApiLalu, function ($d) use ($poldaId, $polresId) {
                    return ($d['polda'] ?? null) === $poldaId && ($d['polres'] ?? null) === $polresId;
                });
                $filteredIni = array_filter($dataApiIni, function ($d) use ($poldaId, $polresId) {
                    return ($d['polda'] ?? null) === $poldaId && ($d['polres'] ?? null) === $polresId;
                });
                $apiLaluGrouped = $groupApi($filteredLalu, false);
                $apiIniGrouped  = $groupApi($filteredIni, false);
            } else {
                // fallback, group by polda
                $apiLaluGrouped = $groupApi($dataApiLalu, true);
                $apiIniGrouped  = $groupApi($dataApiIni, true);
            }

            // -------------------------------
            // 3) Query DB ICELL (Parameterized & Consolidated)
            // -------------------------------
            $safeFieldTanggal = $fieldTanggal === 'report_date' ? 'report_date' : 'accident_date';
            $periodSql = "
                SELECT
                    CASE WHEN ? = '-' THEN polda.id   ELSE polres.id   END AS kode,
                    CASE WHEN ? = '-' THEN polda.name ELSE polres.name END AS polda_polres,
                    agg.total_sidik_laka,
                    agg.p21,
                    agg.sp3,
                    agg.diversi,
                    agg.pom_tni,
                    agg.dalam_proses,
                    agg.sp2lid
                FROM polda
                JOIN polres ON polda.id = polres.polda_id
                LEFT JOIN (
                    SELECT
                        CASE WHEN ? = '-' THEN polda.id ELSE polres.id END AS id,
                        COUNT(accidents.id) AS total_sidik_laka,
                        COUNT(accidents.id) FILTER (WHERE selra_flag = 'S0101') AS p21,
                        COUNT(accidents.id) FILTER (WHERE selra_flag = 'S0102') AS sp3,
                        COUNT(accidents.id) FILTER (WHERE selra_flag = 'S0103') AS diversi,
                        COUNT(accidents.id) FILTER (WHERE selra_flag = 'S0104') AS pom_tni,
                        COUNT(accidents.id) FILTER (WHERE selra_flag = 'S0107') AS dalam_proses,
                        COUNT(accidents.id) FILTER (WHERE selra_flag = 'S0108') AS sp2lid
                    FROM polda
                    JOIN polres    ON polda.id = polres.polda_id
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE
                        (CASE WHEN ? <> '-' THEN polda.id = ? ELSE TRUE END) AND
                        (CASE WHEN ? <> '-' AND ? <> '-' THEN polres.id = ? ELSE TRUE END) AND
                        {$safeFieldTanggal} BETWEEN ? AND ?
                        AND polda.state    <> 0
                        AND polres.state   <> 0
                        AND accidents.state <> 0
                    GROUP BY 1
                ) agg ON polda.id = agg.id OR polres.id = agg.id
                WHERE
                    (CASE WHEN ? <> '-' THEN polda.id = ? ELSE TRUE END) AND
                    (CASE WHEN ? <> '-' AND ? <> '-' THEN polres.id = ? ELSE TRUE END) AND
                    polda.state <> 0
                    AND polda.id NOT IN ('90','99')
                    AND polres.state <> 0
                GROUP BY 1,2,3,4,5,6,7,8,9
                ORDER BY polda_polres
            ";

            $tahun_lalu_rows = DB::select($periodSql, [
                $poldaId, $poldaId,
                $poldaId,
                $poldaId, $poldaId,
                $poldaId, $polresId, $polresId,
                $start_date_then, $end_date_then,
                $poldaId, $poldaId,
                $poldaId, $polresId, $polresId,
            ]);

            $tahun_ini_rows = DB::select($periodSql, [
                $poldaId, $poldaId,
                $poldaId,
                $poldaId, $poldaId,
                $poldaId, $polresId, $polresId,
                $start_date_now, $end_date_now,
                $poldaId, $poldaId,
                $poldaId, $polresId, $polresId,
            ]);

            // Buat map tahun_ini by kode utk akses cepat
            $tahun_ini_map = [];
            foreach ($tahun_ini_rows as $row) {
                $tahun_ini_map[$row->kode] = $row;
            }

            // -------------------------------
            // 4) Response
            // -------------------------------
            $tahun_lalu_data = [];
            foreach ($tahun_lalu_rows as $tl) {
                $kode = $tl->kode;

                // Ambil angka laka dari API
                $jumlahLakaLalu = (int) ($apiLaluGrouped[$kode]['jumlah_laka'] ?? 0);
                $jumlahLakaIni  = (int) ($apiIniGrouped[$kode]['jumlah_laka'] ?? 0);

                // Pair data tahun_ini berdasarkan kode
                $ti = $tahun_ini_map[$kode] ?? null;

                $p21_lalu     = (int) ($tl->p21 ?? 0);
                $sp3_lalu     = (int) ($tl->sp3 ?? 0);
                $diversi_lalu = (int) ($tl->diversi ?? 0);
                $sp2lid_lalu  = (int) ($tl->sp2lid ?? 0);
                $pom_tni_lalu = (int) ($tl->pom_tni ?? 0);

                $p21_ini      = (int) ($ti->p21 ?? 0);
                $sp3_ini      = (int) ($ti->sp3 ?? 0);
                $diversi_ini  = (int) ($ti->diversi ?? 0);
                $sp2lid_ini   = (int) ($ti->sp2lid ?? 0);
                $pom_tni_ini  = (int) ($ti->pom_tni ?? 0);

                $total_selra_lalu = $p21_lalu + $sp3_lalu + $diversi_lalu + $sp2lid_lalu; // tanpa pom_tni
                $total_selra_ini  = $p21_ini  + $sp3_ini  + $diversi_ini  + $sp2lid_ini;  // tanpa pom_tni

                $tahun_lalu_data[] = [
                    'polda'             => $tl->polda_polres,
                    'total_laka_lalu'   => $jumlahLakaLalu,
                    'p21_lalu'          => $p21_lalu,
                    'sp3_lalu'          => $sp3_lalu,
                    'diversi_lalu'      => $diversi_lalu,
                    'sp2lid_lalu'       => $sp2lid_lalu,
                    'pom_tni_lalu'      => $pom_tni_lalu,
                    'total_selra_lalu'  => $total_selra_lalu,
                    'persen_selra_lalu' => $this->countSelra(
                        $jumlahLakaLalu,
                        $pom_tni_lalu,
                        $total_selra_lalu
                    ),
                    // 'persen_selra_lalu' => $jumlahLakaLalu > 0 ? round($total_selra_lalu / $jumlahLakaLalu * 100, 2) : 0,

                    'total_laka_ini'    => $jumlahLakaIni,
                    'p21_tahun_ini'     => $p21_ini,
                    'sp3_tahun_ini'     => $sp3_ini,
                    'diversi_tahun_ini' => $diversi_ini,
                    'sp2lid_tahun_ini'  => $sp2lid_ini,
                    'pom_tni_tahun_ini' => $pom_tni_ini,
                    'total_selra_ini'   => $total_selra_ini,
                    'persen_selra_ini' => $this->countSelra(
                        $jumlahLakaIni,
                        $pom_tni_ini,
                        $total_selra_ini
                    )
                    // 'persen_selra_ini'  => $jumlahLakaIni > 0 ? round($total_selra_ini / $jumlahLakaIni * 100, 2) : 0,
                ];
            }

            return response()->json([
                'data' => [
                    'laporan_berdasarkan' => $dasar,
                    'summary' => [
                        'tahun_lalu' => $tahun_lalu_data
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function export_report_anev(Request $request)
    {

        $poldaId            = $request->input('polda');
        $polresId           = $request->input('polres');
        $start_date_then    = date('Y-m-d', strtotime($request->input('start_date_then')));
        $end_date_then      = date('Y-m-d', strtotime($request->input('end_date_then')));
        $start_date_now     = date('Y-m-d', strtotime($request->input('start_date_now')));
        $end_date_now       = date('Y-m-d', strtotime($request->input('end_date_now')));

        // Check if $polresId is null, set it to a default value or handle accordingly
        if (is_null($polresId)) {
            $polresId = '-'; // You can set this to any default value you need
        }

        // Proceed with the export
        $check = Excel::download(new ExportAnev($poldaId, $polresId, $start_date_then, $end_date_then, $start_date_now, $end_date_now), "Anev " . $poldaId . date("Y-m-d H-i-s") . ".xlsx");

        return $check;
    }

    //
    public function report_individu(Request $request)
    {
        $null = '-';
        $user = Auth::user();
        $roleData = Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                $poldas = $user->polda_id;
                $polress = '-';
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                $level = '2';
                $officer = Auth::user()->officer_id;
                // dd($polres);
                break;
            case 3:
                $poldas = $user->polda_id;
                $polress = '-';
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                $level = '3';
                $officer = Auth::user()->officer_id;
                break;
            case 4:
                $poldas = $user->polda_id;
                $polress = $user->polres_id;
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                $level = '4';
                $user = Auth::user()->id;
                $officer = Auth::user()->officer_id;
                // dd($user);
                break;
            default:
                $poldas = '-';
                $polress = '-';
                $polda = Polda::all();
                $polres = Polres::all();
                $level = '1';
                $officer = Auth::user()->officer_id;
                break;
        }

        $userId = Auth::id();
        $data = DB::select("select
        officers.id,
        officers.first_name,
        officers.last_name,
        officers.rank_short_name,
        polres.name as polres_name,
        coalesce(sidik_agg.total_p21, 0) as total_p21_lalu,
        coalesce(sidik_agg.total_sp3, 0) as total_sp3_lalu,
        coalesce(sidik_agg.total_diversi, 0) as total_diversi_lalu,
        coalesce(sidik_agg.total_pom_tni, 0) as total_pom_tni_lalu,
        coalesce(sidik_agg.total_rj, 0) as total_rj_lalu,
        coalesce(sidik_agg.total_dalam_proses, 0) as total_dalam_proses_lalu,
        coalesce(sidik_agg.total_sp2lid, 0) as total_sp2lid_lalu,

        coalesce(sidik_agg.total_p21, 0) as total_p21_kini,
        coalesce(sidik_agg.total_sp3, 0) as total_sp3_kini,
        coalesce(sidik_agg.total_diversi, 0) as total_diversi_kini,
        coalesce(sidik_agg.total_pom_tni, 0) as total_pom_tni_kini,
        coalesce(sidik_agg.total_rj, 0) as total_rj_kini,
        coalesce(sidik_agg.total_dalam_proses, 0) as total_dalam_proses_kini,
        coalesce(sidik_agg.total_sp2lid, 0) as total_sp2lid_kini,
        foto.avatars from officers

        LEFT JOIN (
            SELECT
                surat_penyidikan.officer_id as sidik_id,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0101') as total_p21,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0102') as total_sp3,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0103') as total_diversi,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0104') as total_pom_tni,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0106') as total_rj,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0107') as total_dalam_proses,
                COUNT(accident_id) FILTER (WHERE accidents.selra_flag = 'S0108') as total_sp2lid
            FROM surat_penyidikan
            JOIN accidents ON accidents.id = surat_penyidikan.accident_id
            GROUP BY surat_penyidikan.officer_id
        ) as sidik_agg on officers.id = sidik_agg.sidik_id

        LEFT JOIN
        (select users.avatar as avatars, users.officer_id as users_id from users
        where
        CASE WHEN ? = '4' THEN users.officer_id = ? AND role_id = '4' ELSE TRUE END
        ) as foto on officers.id = foto.users_id
        LEFT JOIN polda on polda.id = officers.polda_id
        LEFT JOIN polres on polres.id = officers.polres_id
        where
        CASE WHEN ? = '4' THEN officers.id = ? ELSE TRUE END
        AND
        CASE WHEN ? <> ? THEN officers.polda_id = ? ELSE TRUE END
        AND
        CASE WHEN ? <> ? THEN officers.polres_id = ? ELSE TRUE END
        AND
        foto.avatars <> 'user.png'
        ", [
            (string) $level, (string) $userId,
            (string) $level, (string) $officer,
            (string) $poldas, $null, (string) $poldas,
            (string) $polress, $null, (string) $polress,
        ]);

        $format_start_date_then = null;
        $format_end_date_then = null;
        $format_start_date_now = null;
        $format_end_date_now = null;
        // dd($data);
        $accident = $this->paginate_individu($data);
        $accident->appends($request->all());
        return view('anev/anev-individu', compact('accident', 'polda', 'polres', 'format_start_date_then', 'format_end_date_then', 'format_start_date_now', 'format_end_date_now'));
    }

    public function get_report_individu(Request $request)
    {
        $user = Auth::user();
        $roleData = Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                // dd($polda);
                break;
            case 3:
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                break;
            case 4:
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                break;
            default:
                $polda = Polda::all();
                $polres = Polres::all();
                break;
        }

        $poldas = $request->polda_id;
        $polress = $request->polres_id;
        if ($polress == null) {
            $polress = '-';
        }
        $start_date_then    = date('Y-m-d', strtotime($request->input('date_from')));
        $format_start_date_then = Carbon::parse($request->date_from)->format('d-m-y');
        $end_date_then      = date('Y-m-d', strtotime($request->input('date_to')));
        $format_end_date_then = Carbon::parse($request->date_to)->format('d-m-y');
        $start_date_now     = date('Y-m-d', strtotime($request->input('date_from_now')));
        $format_start_date_now = Carbon::parse($request->date_from_now)->format('d-m-y');
        $end_date_now       = date('Y-m-d', strtotime($request->input('date_to_now')));
        $format_end_date_now = Carbon::parse($request->date_to_now)->format('d-m-y');
        $null = '-';
        $poldas = $poldas !== null && $poldas !== '' ? (string) $poldas : '-';
        $polress = $polress !== null && $polress !== '' ? (string) $polress : '-';

        $periodUnionSql = "
            SELECT lidik_id,
                   SUM(total_p21) AS total_p21,
                   SUM(total_sp3) AS total_sp3,
                   SUM(total_diversi) AS total_diversi,
                   SUM(total_pom_tni) AS total_pom_tni,
                   SUM(total_sp2lid) AS total_sp2lid
            FROM (
                SELECT petugas_lidik.officer_id AS lidik_id,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS total_p21,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS total_sp3,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS total_diversi,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0104') AS total_pom_tni,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS total_sp2lid
                FROM legacy.investigation_warrants
                JOIN accidents ON accidents.id = legacy.investigation_warrants.accident_id
                JOIN legacy.investigation_warrant_officer AS petugas_lidik ON petugas_lidik.investigation_warrant_id = legacy.investigation_warrants.id
                WHERE accidents.accident_date BETWEEN ? AND ?
                GROUP BY petugas_lidik.officer_id

                UNION ALL

                SELECT surat_penyelidikan.officer_id AS lidik_id,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS total_p21,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag IN ('S0102', 'S0106') AND accidents.md > 0) AS total_sp3,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS total_diversi,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0104') AS total_pom_tni,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS total_sp2lid
                FROM surat_penyelidikan
                JOIN accidents ON accidents.id = surat_penyelidikan.accident_id
                WHERE accidents.accident_date BETWEEN ? AND ?
                GROUP BY surat_penyelidikan.officer_id

                UNION ALL

                SELECT petugas_lidik.register_number AS lidik_id,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS total_p21,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS total_sp3,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS total_diversi,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0104') AS total_pom_tni,
                    COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS total_sp2lid
                FROM doc.surat_perintah_penyelidikan_documents AS surat_lidik
                JOIN accidents ON accidents.id = surat_lidik.accident_id
                JOIN doc.surat_perintah_penyelidikan_document_officers AS petugas_lidik ON petugas_lidik.surat_perintah_penyelidikan_document_id = surat_lidik.id
                WHERE accidents.accident_date BETWEEN ? AND ?
                GROUP BY petugas_lidik.register_number
            ) AS combined_results
            GROUP BY lidik_id
        ";

        $data = DB::select("
        SELECT officers.id,
               officers.first_name,
               officers.last_name,
               officers.rank_short_name,
               polres.name AS polres_name,
               COALESCE(agg_lalu.total_p21, 0) AS total_p21_lalu,
               COALESCE(agg_lalu.total_sp3, 0) AS total_sp3_lalu,
               COALESCE(agg_lalu.total_diversi, 0) AS total_diversi_lalu,
               COALESCE(agg_lalu.total_pom_tni, 0) AS total_pom_tni_lalu,
               COALESCE(agg_lalu.total_sp2lid, 0) AS total_sp2lid_lalu,

               COALESCE(agg_kini.total_p21, 0) AS total_p21_kini,
               COALESCE(agg_kini.total_sp3, 0) AS total_sp3_kini,
               COALESCE(agg_kini.total_diversi, 0) AS total_diversi_kini,
               COALESCE(agg_kini.total_pom_tni, 0) AS total_pom_tni_kini,
               COALESCE(agg_kini.total_sp2lid, 0) AS total_sp2lid_kini,
               foto.avatars
        FROM officers
        LEFT JOIN ({$periodUnionSql}) AS agg_lalu ON officers.id = agg_lalu.lidik_id
        LEFT JOIN ({$periodUnionSql}) AS agg_kini ON officers.id = agg_kini.lidik_id
        LEFT JOIN
        (SELECT users.avatar AS avatars, users.officer_id AS users_id FROM users
        ) as foto on officers.id = foto.users_id
        LEFT JOIN users on users.id = officers.user_id
        LEFT JOIN polda on polda.id = officers.polda_id
        LEFT JOIN polres on polres.id = officers.polres_id
        WHERE
        CASE WHEN ? <> ? THEN officers.polda_id = ? ELSE TRUE END
        AND
        CASE WHEN ? <> ? THEN officers.polres_id = ? ELSE TRUE END
        AND officers.state <> '0'
        AND officers.is_active IS true
        AND users.is_active IS true
        GROUP BY 1,5,6,7,8,9,10,11,12,13,14,15,16
        ", [
            $start_date_then, $end_date_then,
            $start_date_then, $end_date_then,
            $start_date_then, $end_date_then,
            $start_date_now, $end_date_now,
            $start_date_now, $end_date_now,
            $start_date_now, $end_date_now,
            $poldas, $null, $poldas,
            $polress, $null, $polress,
        ]);
        // dd($data);
        $accident = $this->paginate_individu($data);
        $accident->appends($request->all());
        return view('anev/anev-individu', compact('accident', 'polda', 'polres', 'format_start_date_then', 'format_end_date_then', 'format_start_date_now', 'format_end_date_now'));
    }

    public function paginate_individu($items, $perPage = 12, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 5);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function export_report_individu(Request $request)
    {
        $poldaId            = $request->input('polda');
        $polresId           = $request->input('polres');
        $start_date_then    = date('Y-m-d', strtotime($request->input('start_date_then')));
        $end_date_then      = date('Y-m-d', strtotime($request->input('end_date_then')));
        $start_date_now     = date('Y-m-d', strtotime($request->input('start_date_now')));
        $end_date_now       = date('Y-m-d', strtotime($request->input('end_date_now')));
        $month              = null;
        $year               = null;

        return Excel::download(new ExportReportIndividu($poldaId, $polresId, $start_date_then, $end_date_then, $start_date_now, $end_date_now), "Report Individu  " . $poldaId . date("Y-m-d H-i-s") . " .xlsx");
    }

    // public function get_report_kinerja_polda_polres(Request $request){
    //     $poldaId = $request->input('polda');
    //     $polresId = $request->input('polres');
    //     $start_date = date('Y-m-d', strtotime($request->input('date_from')));
    //     $end_date= date('Y-m-d', strtotime($request->input('date_to')));

    //     if($poldaId != null){
    //         $poldaId = $poldaId;
    //     }else{
    //         $poldaId = '-';
    //     }
    //     if($polresId != null){
    //         $polresId = $polresId;
    //     }else{
    //         $polresId = '-';
    //     }
    // }

}
