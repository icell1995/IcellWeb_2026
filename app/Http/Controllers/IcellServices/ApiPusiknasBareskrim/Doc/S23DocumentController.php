<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\IcellServices\ApiPusiknasBareskrim\S23PusiknasService;
use App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocument;
use App\Models\History\DocumentApiSyncHistory;

class S23DocumentController extends Controller
{
    protected S23PusiknasService $s23Service;

    public function __construct(S23PusiknasService $s23Service)
    {
        $this->s23Service = $s23Service;
    }

    /**
     * Endpoint API GET Dokumen S-23 (SPPT-TI / PUSIKNAS)
     * Kode Proses: DAT-5
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $service = $this->s23Service;

        // 1. Ambil Query Parameter
        $id = $request->query('id'); // Filter UUID spesifik jika ada
        $mode = $request->query('mode');
        $statusId = $request->query('status_id', '11'); // default '11' (selesai / rilis)
        $startReleaseDate = $request->query('start_release_date');
        $endReleaseDate = $request->query('end_release_date');
        $startDocDate = $request->query('start_doc_date');
        $endDocDate = $request->query('end_doc_date');
        $nomorSurat = $request->query('nomor') ?: $request->query('nomor_surat');
        $nomorSpdp = $request->query('nomor_spdp');
        $nomorS17 = $request->query('nomor_s17');
        $kodeSatker = $request->query('kode_satker') ?: $request->query('kode_satker_penerbit_spdp');
        $perPage = max(1, intval($request->query('perPage', $request->query('limit', 100))));
        $page = max(1, intval($request->query('page', 1)));

        // 2. Validasi Format Tanggal (YYYY-MM-DD)
        $dateParams = [
            'start_release_date' => $startReleaseDate,
            'end_release_date' => $endReleaseDate,
            'start_doc_date' => $startDocDate,
            'end_doc_date' => $endDocDate,
        ];

        foreach ($dateParams as $paramKey => $paramValue) {
            if (!empty($paramValue) && !$service->isValidDate($paramValue)) {
                return response()->json([
                    'code' => '400',
                    'status' => 'BAD_REQUEST',
                    'message' => "Invalid date format for '{$paramKey}'. Date format must be YYYY-MM-DD.",
                    'pagination' => [
                        'Page' => $page,
                        'TotalData' => 0,
                        'TotalPage' => 0,
                        'TotalDataSent' => 0,
                    ],
                    'data' => [],
                ], 400);
            }
        }

        try {
            $eagerRelations = [
                'accident.polres',
                'officers',
            ];

            // 3. Query Dokumen S-23 (0605)
            $query = SuratPerintahPembantaranPenahananDocument::with($eagerRelations);

            // Filter status rilis (bisa override via parameter status_id=all atau status_id=<id>)
            if ($statusId === 'all') {
                // Jangan batasi status
            } elseif (!empty($statusId)) {
                $query->where('status_id', $statusId);
                if ($statusId === '11') {
                    $query->whereNotNull('released_at');
                }
            }

            if (!empty($id)) {
                $query->where('id', $id);
            }

            if (!empty($nomorSurat)) {
                $query->where('nomor_surat', 'like', "%{$nomorSurat}%");
            }

            if (!empty($nomorSpdp)) {
                $query->where('nomor_spdp', 'like', "%{$nomorSpdp}%");
            }

            if (!empty($nomorS17)) {
                $query->where('nomor_surat_perintah_penahanan', 'like', "%{$nomorS17}%");
            }

            if (!empty($kodeSatker)) {
                $query->where(function ($q) use ($kodeSatker) {
                    $q->where('kode_satker_penerbit_spdp', $kodeSatker)
                        ->orWhereHas('accident.polres', function ($sub) use ($kodeSatker) {
                            $sub->where('satker_code', $kodeSatker)->orWhere('code', $kodeSatker);
                        });
                });
            }

            $this->applyFilters($query, $startReleaseDate, $endReleaseDate, $startDocDate, $endDocDate);

            // 4. Urutkan berdasarkan released_at terbaru / tanggal_surat
            $results = $query->get()->sortByDesc(function ($doc) {
                return $doc->released_at ?? $doc->tanggal_surat ?? $doc->created_at;
            })->values();

            $totalData = $results->count();
            $totalPage = $totalData > 0 ? (int) ceil($totalData / $perPage) : 0;

            // 5. Cek Jika Hasil Kosong
            if ($totalData === 0) {
                return response()->json([
                    'code' => '404',
                    'status' => 'NOT_FOUND',
                    'message' => 'Data not found.',
                    'pagination' => [
                        'Page' => $page,
                        'TotalData' => 0,
                        'TotalPage' => 0,
                        'TotalDataSent' => 0,
                    ],
                    'data' => [],
                ], 404);
            }

            // 6. Paginasi Manual Koleksi
            $offset = ($page - 1) * $perPage;
            $pagedItems = $results->slice($offset, $perPage)->values();

            // 7. Susun Payload S-23 Selaras Swagger SPPT-TI DAT-5
            $responseData = [];
            foreach ($pagedItems as $document) {
                $responseData[] = $service->buildPayload($document);

                // Catat sinkronisasi jika bukan mode STAGING
                if ($mode !== 'STAGING') {
                    $this->recordSyncMoment($request, $document);
                }
            }

            // 8. Kembalikan Response Standar
            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'message' => 'Success',
                'pagination' => [
                    'Page' => $page,
                    'TotalData' => $totalData,
                    'TotalPage' => $totalPage,
                    'TotalDataSent' => count($responseData),
                ],
                'data' => $responseData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An error occurred while processing your request: ' . $e->getMessage(),
                'pagination' => [
                    'Page' => $page,
                    'TotalData' => 0,
                    'TotalPage' => 0,
                    'TotalDataSent' => 0,
                ],
                'data' => [],
            ], 500);
        }
    }

    /**
     * Menerapkan filter rentang tanggal pada query
     */
    protected function applyFilters($query, $startReleaseDate, $endReleaseDate, $startDocDate, $endDocDate)
    {
        if (!empty($startReleaseDate) && !empty($endReleaseDate)) {
            $query->whereBetween('released_at', [
                date('Y-m-d 00:00:00', strtotime($startReleaseDate)),
                date('Y-m-d 23:59:59', strtotime($endReleaseDate)),
            ]);
        } elseif (!empty($startReleaseDate)) {
            $query->where('released_at', '>=', date('Y-m-d 00:00:00', strtotime($startReleaseDate)));
        } elseif (!empty($endReleaseDate)) {
            $query->where('released_at', '<=', date('Y-m-d 23:59:59', strtotime($endReleaseDate)));
        }

        if (!empty($startDocDate) && !empty($endDocDate)) {
            $query->whereBetween('tanggal_surat', [
                date('Y-m-d', strtotime($startDocDate)),
                date('Y-m-d', strtotime($endDocDate)),
            ]);
        } elseif (!empty($startDocDate)) {
            $query->where('tanggal_surat', '>=', date('Y-m-d', strtotime($startDocDate)));
        } elseif (!empty($endDocDate)) {
            $query->where('tanggal_surat', '<=', date('Y-m-d', strtotime($endDocDate)));
        }

        return $query;
    }

    /**
     * Menyimpan momen sinkronisasi dokumen ke database
     */
    protected function recordSyncMoment(Request $request, $document): void
    {
        try {
            $document->update([
                'last_synced_at' => Carbon::now(),
            ]);

            $ipAddress = $request->header('X-Forwarded-For')
                ?: ($request->header('X-Real-IP') ?: $request->ip());

            DocumentApiSyncHistory::create([
                'document_category_id' => '0605',
                'document_id' => $document->id,
                'document_type' => get_class($document),
                'accident_id' => $document->accident_id,
                'ip_address' => $ipAddress,
            ]);
        } catch (\Exception $e) {
            // Silently ignore sync log errors to avoid blocking API response
        }
    }
}
