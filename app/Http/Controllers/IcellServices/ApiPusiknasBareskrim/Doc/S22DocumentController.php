<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\S22PusiknasService;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument\SuratPermintaanPerpanjanganPenahananLanjutanDocument;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument;
use App\Models\History\DocumentApiSyncHistory;

class S22DocumentController extends Controller
{
    protected S22PusiknasService $s22Service;

    public function __construct(S22PusiknasService $s22Service)
    {
        $this->s22Service = $s22Service;
    }

    /**
     * Endpoint API GET Dokumen S-22 (SPPT-TI / PUSIKNAS)
     * Kode Proses: HAN-10.30
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $service = $this->s22Service;

        // 1. Ambil Query Parameter
        $type = strtolower($request->query('type', 'all')); // 'all', 'pertama', 'kedua', '0603', '0604'
        $id = $request->query('id'); // Filter UUID spesifik jika ada
        $mode = $request->query('mode');
        $startReleaseDate = $request->query('start_release_date');
        $endReleaseDate = $request->query('end_release_date');
        $startDocDate = $request->query('start_doc_date');
        $endDocDate = $request->query('end_doc_date');
        $perPage = max(1, intval($request->query('perPage', 100)));
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
                'accident.suratPerintahPenyidikanDocuments.attachment',
                'officers',
                'attachment',
                'prison',
            ];

            $fetchPertama = in_array($type, ['all', 'pertama', '0603', '']);
            $fetchKedua = in_array($type, ['all', 'kedua', '0604', '']);

            $results = collect();

            // 3. Query S-22 Pertama (0603)
            if ($fetchPertama) {
                $q1 = SuratPermintaanPerpanjanganPenahananLanjutanDocument::with($eagerRelations)
                    ->where('status_id', '11')
                    ->whereNotNull('released_at');

                if (!empty($id)) {
                    $q1->where('id', $id);
                }

                $this->applyFilters($q1, $startReleaseDate, $endReleaseDate, $startDocDate, $endDocDate);
                $docsPertama = $q1->get();
                $results = $results->merge($docsPertama);
            }

            // 4. Query S-22 Kedua (0604)
            if ($fetchKedua) {
                $q2 = SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::with($eagerRelations)
                    ->where('status_id', '11')
                    ->whereNotNull('released_at');

                if (!empty($id)) {
                    $q2->where('id', $id);
                }

                $this->applyFilters($q2, $startReleaseDate, $endReleaseDate, $startDocDate, $endDocDate);
                $docsKedua = $q2->get();
                $results = $results->merge($docsKedua);
            }

            // 5. Urutkan berdasarkan released_at terbaru
            $sortedResults = $results->sortByDesc(function ($doc) {
                return $doc->released_at ?? $doc->tanggal_surat;
            })->values();

            $totalData = $sortedResults->count();
            $totalPage = $totalData > 0 ? (int) ceil($totalData / $perPage) : 0;

            // 6. Cek Jika Hasil Kosong
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

            // 7. Paginasi Manual Koleksi
            $offset = ($page - 1) * $perPage;
            $pagedItems = $sortedResults->slice($offset, $perPage)->values();

            // 8. Susun Payload S-22 Selaras Swagger SPPT-TI
            $responseData = [];
            foreach ($pagedItems as $document) {
                $responseData[] = $service->buildPayload($document);

                // Catat sinkronisasi jika bukan mode STAGING
                if ($mode !== 'STAGING') {
                    $this->recordSyncMoment($request, $document);
                }
            }

            // 9. Kembalikan Response Standar
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

            $categoryId = $document->document_category_id ?? '0603';

            DocumentApiSyncHistory::create([
                'document_category_id' => $categoryId,
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
