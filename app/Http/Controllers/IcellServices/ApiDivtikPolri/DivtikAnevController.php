<?php

namespace App\Http\Controllers\IcellServices\ApiDivtikPolri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Log\ApiDivtikPolri;

class DivtikAnevController extends Controller
{
    public function getDivtik(Request $request)
    {
        try {
            $this->logAccess($request);

            // --- Ambil param (dukung snake_case & camelCase) ---
            $year     = $request->query('year',  $request->input('year'));
            $date     = $request->query('date',  $request->input('date'));

            $poldaId  = $request->query('polda_id',
                        $request->input('polda_id',
                        $request->query('poldaId',
                        $request->input('poldaId'))));

            $polresId = $request->query('polres_id',
                        $request->input('polres_id',
                        $request->query('polresId',
                        $request->input('polresId'))));

            // 1) Validasi year/date
            if (empty($year) && empty($date)) {
                return response()->json(['status'=>'failed','message'=>'harap isi tahun atau tanggal'], 422);
            }
            if (!empty($year) && !preg_match('/^\d{4}$/', (string)$year)) {
                return response()->json(['status'=>'error','message'=>'Format year tidak valid. Gunakan 4 digit (contoh: 2024)'], 422);
            }
            if (!empty($date)) {
                $dObj = \DateTime::createFromFormat('Y-m-d', $date);
                if (!$dObj || $dObj->format('Y-m-d') !== $date) {
                    return response()->json(['status'=>'error','message'=>'Format date tidak valid. Gunakan Y-m-d (contoh: 2025-09-15)'], 422);
                }
            }

            // 2) VALIDASI: polresId tak boleh tanpa poldaId
            if (!empty($polresId) && empty($poldaId)) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'polresId tidak boleh tanpa poldaId. Harap isi poldaId terlebih dahulu.'
                ], 422);
            }

            // 3) Kondisi tanggal & 4) MODE (Parameterized & Consolidated)
            $groupMode = !empty($poldaId) ? 'polres' : 'polda';
            $yearParam = !empty($year) ? (int) $year : null;
            $dateParam = !empty($date) ? (string) $date : null;
            $poldaParam = !empty($poldaId) ? (string) $poldaId : null;
            $polresParam = !empty($polresId) ? (string) $polresId : null;

            // 8) SQL & response sesuai mode
            if ($groupMode === 'polres') {
                $sql = "
                    SELECT
                        lp_polda.satker_code  AS polda_satker_code,
                        polda.id              AS polda_id,
                        polda.name            AS polda,

                        lp_polres.satker_code AS polres_satker_code,
                        polres.id             AS polres_id,
                        polres.name           AS polres,

                        COALESCE(agg.p21, 0)     AS p21,
                        COALESCE(agg.sp3, 0)     AS sp3,
                        COALESCE(agg.diversi, 0) AS diversi,
                        COALESCE(agg.sp2lid, 0)  AS sp2lid
                    FROM polda
                    JOIN polres ON polres.polda_id = polda.id

                    LEFT JOIN lib.polices lp_polda
                           ON lp_polda.id = polda.id
                          AND lp_polda.is_active = TRUE
                    LEFT JOIN lib.polices lp_polres
                           ON lp_polres.id = polres.id
                          AND lp_polres.is_active = TRUE

                    LEFT JOIN (
                        SELECT
                            polres.id AS polres_id,
                            COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS p21,
                            COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS sp3,
                            COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS diversi,
                            COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS sp2lid
                        FROM polda
                        JOIN polres    ON polres.polda_id    = polda.id
                        JOIN accidents ON accidents.polres_id = polres.id
                        WHERE (?::int IS NULL OR EXTRACT(YEAR FROM accidents.accident_date) = ?::int)
                          AND (?::date IS NULL OR DATE(accidents.accident_date) = ?::date)
                          AND (?::text IS NULL OR polda.id = ? OR polda.satker_code = ?)
                          AND (?::text IS NULL OR polres.id = ? OR polres.satker_code = ?)
                          AND polda.state     <> 0
                          AND polres.state    <> 0
                          AND accidents.state <> 0
                        GROUP BY polres.id
                    ) agg ON agg.polres_id = polres.id
                    WHERE polda.state <> 0
                      AND polres.state <> 0
                      AND polda.id NOT IN ('90','99','80')
                      AND (?::text IS NULL OR polda.id = ? OR polda.satker_code = ?)
                      AND (?::text IS NULL OR polres.id = ? OR polres.satker_code = ?)
                    ORDER BY lp_polres.satker_code ASC NULLS LAST, polres.name ASC
                ";
                $rows = DB::select($sql, [
                    $yearParam, $yearParam,
                    $dateParam, $dateParam,
                    $poldaParam, $poldaParam, $poldaParam,
                    $polresParam, $polresParam, $polresParam,
                    $poldaParam, $poldaParam, $poldaParam,
                    $polresParam, $polresParam, $polresParam,
                ]);

                // Response (SatkerCode = satker Polda, sesuai formatmu)
                $result = array_map(function ($r) {
                    return [
                        // pakai satker polres saat mode POLRES
                        'SatkerCode'  => $r->polres_satker_code,
                        'PoldaId'     => $r->polda_id,
                        'PoldaName'   => $r->polda,
                        'PolresId'    => $r->polres_id,
                        'PolresName'  => $r->polres,
                        'p21'         => (int)$r->p21,
                        'sp3'         => (int)$r->sp3,
                        'diversi'     => (int)$r->diversi,
                        'sp2lid'      => (int)$r->sp2lid,
                    ];
                }, $rows);

                return response()->json([
                    'status' => 'success',
                    'data'   => [
                        'laporan_berdasarkan' => 'TANGGAL KEJADIAN',
                        'periode'             => $date ?: $year,
                        'filter'              => [
                            'polda_id_or_satker'  => $poldaId,
                            'polres_id_or_satker' => $polresId,
                        ],
                        'summary'             => $result,
                    ]
                ]);
            }

            // Mode POLDA (default) — JOIN lib.polices aktif untuk satker polda
            $sql = "
                SELECT
                    lp_polda.satker_code AS polda_satker_code,
                    polda.id             AS polda_id,
                    polda.name           AS polda,
                    COALESCE(agg.p21, 0)     AS p21,
                    COALESCE(agg.sp3, 0)     AS sp3,
                    COALESCE(agg.diversi, 0) AS diversi,
                    COALESCE(agg.sp2lid, 0)  AS sp2lid
                FROM polda

                LEFT JOIN lib.polices lp_polda
                       ON lp_polda.id = polda.id
                      AND lp_polda.is_active = TRUE

                LEFT JOIN (
                    SELECT
                        polda.id AS polda_id,
                        COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0101') AS p21,
                        COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0102') AS sp3,
                        COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0103') AS diversi,
                        COUNT(accidents.id) FILTER (WHERE accidents.selra_flag = 'S0108') AS sp2lid
                    FROM polda
                    JOIN polres    ON polres.polda_id    = polda.id
                    JOIN accidents ON accidents.polres_id = polres.id
                    WHERE (?::int IS NULL OR EXTRACT(YEAR FROM accidents.accident_date) = ?::int)
                      AND (?::date IS NULL OR DATE(accidents.accident_date) = ?::date)
                      AND polda.state     <> 0
                      AND polres.state    <> 0
                      AND accidents.state <> 0
                    GROUP BY polda.id
                ) agg ON agg.polda_id = polda.id
                WHERE polda.state <> 0
                  AND polda.id NOT IN ('90','99','80')
                ORDER BY lp_polda.satker_code ASC NULLS LAST, polda.name ASC
            ";
            $rows = DB::select($sql, [
                $yearParam, $yearParam,
                $dateParam, $dateParam,
            ]);

            // Samakan bentuk dengan mode POLRES (PolresId/Name = null)
            $result = array_map(function ($r) {
                return [
                    // pakai satker polda saat mode POLDA
                    'SatkerCode'  => $r->polda_satker_code,
                    'PoldaId'     => $r->polda_id,
                    'PoldaName'   => $r->polda,
                    'PolresId'    => null,
                    'PolresName'  => null,
                    'p21'         => (int)$r->p21,
                    'sp3'         => (int)$r->sp3,
                    'diversi'     => (int)$r->diversi,
                    'sp2lid'      => (int)$r->sp2lid,
                ];
            }, $rows);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'laporan_berdasarkan' => 'TANGGAL KEJADIAN',
                    'periode'             => $date ?: $year,
                    'summary'             => $result,
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'trace'   => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        }
    }

    private function logAccess(Request $request): void
    {
        try {
            $ip = $request->header('X-Forwarded-For')
                ?: $request->header('X-Real-IP')
                ?: $request->ip();

            ApiDivtikPolri::create([
                'class_model' => self::class,
                'ip_address'  => is_array($ip) ? implode(',', $ip) : (string)$ip,
            ]);
        } catch (\Throwable $e) {}
    }
}
