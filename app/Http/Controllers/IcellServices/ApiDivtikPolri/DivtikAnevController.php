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

            // 3) Kondisi tanggal (PostgreSQL)
            $conds = [];
            if (!empty($year)) $conds[] = "EXTRACT(YEAR FROM accidents.accident_date) = '{$year}'";
            if (!empty($date)) $conds[] = "DATE(accidents.accident_date) = '{$date}'";
            $dateCondition = implode(' AND ', $conds);

            // 4) MODE: hanya POLRES bila poldaId ada (meski polresId kosong)
            $groupMode = !empty($poldaId) ? 'polres' : 'polda';

            // 5) Filter polda/polres (dukung id atau satker_code)
            $fPolda  = '';
            if (!empty($poldaId)) {
                $v = addslashes($poldaId);
                $fPolda = "AND (polda.id = '{$v}' OR polda.satker_code = '{$v}')";
            }
            $fPolres = '';
            if (!empty($polresId)) {
                $v = addslashes($polresId);
                $fPolres = "AND (polres.id = '{$v}' OR polres.satker_code = '{$v}')";
            }

            // 6) Flags
            $flags = [
                'p21'     => 'S0101',
                'sp3'     => 'S0102',
                'diversi' => 'S0103',
                'sp2lid'  => 'S0108',
            ];

            // 7) JOIN builder per-flag
            $joinFor = function (string $alias, string $flag) use ($dateCondition, $groupMode, $fPolda, $fPolres) {
                if ($groupMode === 'polres') {
                    return "
                        LEFT JOIN (
                            SELECT polres.id AS polres_id, COUNT(accidents.id) AS total
                            FROM polda
                            JOIN polres    ON polres.polda_id    = polda.id
                            JOIN accidents ON accidents.polres_id = polres.id
                            WHERE accidents.selra_flag = '$flag'
                              AND {$dateCondition}
                              {$fPolda}
                              {$fPolres}
                              AND polda.state     <> 0
                              AND polres.state    <> 0
                              AND accidents.state <> 0
                            GROUP BY polres.id
                        ) $alias ON $alias.polres_id = polres.id
                    ";
                }
                // mode polda
                return "
                    LEFT JOIN (
                        SELECT polda.id AS polda_id, COUNT(accidents.id) AS total
                        FROM polda
                        JOIN polres    ON polres.polda_id    = polda.id
                        JOIN accidents ON accidents.polres_id = polres.id
                        WHERE accidents.selra_flag = '$flag'
                          AND {$dateCondition}
                          AND polda.state     <> 0
                          AND polres.state    <> 0
                          AND accidents.state <> 0
                        GROUP BY polda.id
                    ) $alias ON $alias.polda_id = polda.id
                ";
            };

            $joins = '';
            foreach ($flags as $alias => $code) {
                $joins .= $joinFor("total_$alias", $code);
            }

            // 8) SQL & response sesuai mode
            if ($groupMode === 'polres') {
                // JOIN lib.polices (yang aktif) untuk ambil satker_code Polda & Polres
                $sql = "
                    SELECT
                        lp_polda.satker_code  AS polda_satker_code,
                        polda.id              AS polda_id,
                        polda.name            AS polda,

                        lp_polres.satker_code AS polres_satker_code,
                        polres.id             AS polres_id,
                        polres.name           AS polres,

                        COALESCE(total_p21.total, 0)     AS p21,
                        COALESCE(total_sp3.total, 0)     AS sp3,
                        COALESCE(total_diversi.total, 0) AS diversi,
                        COALESCE(total_sp2lid.total, 0)  AS sp2lid
                    FROM polda
                    JOIN polres ON polres.polda_id = polda.id

                    LEFT JOIN lib.polices lp_polda
                           ON lp_polda.id = polda.id
                          AND lp_polda.is_active = TRUE
                    LEFT JOIN lib.polices lp_polres
                           ON lp_polres.id = polres.id
                          AND lp_polres.is_active = TRUE

                    $joins
                    WHERE polda.state <> 0
                      AND polres.state <> 0
                      AND polda.id NOT IN ('90','99','80')
                      {$fPolda}
                      {$fPolres}
                    ORDER BY lp_polres.satker_code ASC NULLS LAST, polres.name ASC
                ";
                $rows = DB::select($sql);

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
                    COALESCE(total_p21.total, 0)     AS p21,
                    COALESCE(total_sp3.total, 0)     AS sp3,
                    COALESCE(total_diversi.total, 0) AS diversi,
                    COALESCE(total_sp2lid.total, 0)  AS sp2lid
                FROM polda

                LEFT JOIN lib.polices lp_polda
                       ON lp_polda.id = polda.id
                      AND lp_polda.is_active = TRUE

                $joins
                WHERE polda.state <> 0
                  AND polda.id NOT IN ('90','99','80')
                ORDER BY lp_polda.satker_code ASC NULLS LAST, polda.name ASC
            ";
            $rows = DB::select($sql);

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
