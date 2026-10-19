<?php

namespace App\Services\IcellServices;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class PusiknasDocDataService
{
    /**
     * Mapping antara document_category_id atau kode_jenis_doc ke Controller API
     *
     * @var array<string, class-string|array{class-string, string}>
     */
    protected array $controllerMap = [
        // 1. Surat Permintaan Penggeledahan (DB ID: 0404 | Pusiknas Code: s10)
        '0404' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPermintaanPenggeledahan::class,
        's10'  => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPermintaanPenggeledahan::class,

        // 2. Surat Persetujuan Penggeledahan (DB ID: 0405 | Pusiknas Code: s11)
        '0405' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPersetujuanPenggeledahan::class,
        's11'  => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPersetujuanPenggeledahan::class,

        // 3. Surat Permintaan Izin Penyitaan (DB ID: 0504 | Pusiknas Code: s12)
        '0504' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPermintaanIzinPenyitaanPusiknasDocumentController::class,
        's12'  => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPermintaanIzinPenyitaanPusiknasDocumentController::class,

        // 4. Surat Laporan Persetujuan Penyitaan (DB ID: 0505 | Pusiknas Code: s13)
        '0505' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratLaporanPersetujuanPenyitaanPusiknasDocumentController::class,
        's13'  => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratLaporanPersetujuanPenyitaanPusiknasDocumentController::class,

        // SPDP & SP3 Pusiknas (DB ID: 0204, 0205, 0216 | Pusiknas Code: spdp, sp3)
        '0204' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentController::class,
        'spdp' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentController::class,
        '0205' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanPenghentianPenyidikanDocumentController::class,
        '0216' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanPenghentianPenyidikanDocumentController::class,
        'sp3'  => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanPenghentianPenyidikanDocumentController::class,

        // Diversi Pusiknas (SPUD & PPD-POL)
        '0211'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SpudDocumentController::class,
        'spud'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SpudDocumentController::class,
        '0212'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\PpdPolDocumentController::class,
        'ppd'     => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\PpdPolDocumentController::class,
        'ppd-pol' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\PpdPolDocumentController::class,

        // Tahap 1 & Tahap 2 Pusiknas
        '0805' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\Tahap1PusiknasDocumentController::class,
        '0806' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\Tahap1PusiknasDocumentController::class,
        '0807' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\Tahap2PusiknasDocumentController::class,

        // Penahanan (BA-HAN, S17, S18, S19, S21, S22, S23)
        '0609'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\BaHanDocumentController::class,
        'ba-han' => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\BaHanDocumentController::class,
        'bahan'  => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\BaHanDocumentController::class,
        '0601'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S17DocumentController::class,
        's17'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S17DocumentController::class,
        '0603'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S18DocumentController::class,
        's18'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S18DocumentController::class,
        '0604'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S19DocumentController::class,
        's19'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S19DocumentController::class,
        '0605'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S21DocumentController::class,
        's21'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S21DocumentController::class,
        '0606'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S22DocumentController::class,
        '0607'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S22DocumentController::class,
        's22'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S22DocumentController::class,
        '0608'   => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S23DocumentController::class,
        's23'    => \App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S23DocumentController::class,

        // Dokumen Bareskrim EMP (jika dibutuhkan)
        '0201' => \App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratPerintahPenyidikanDocumentController::class,
        '0702' => \App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratPerintahTugasDocumentController::class,
        '0706' => \App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\LaporanHasilGelarPerkaraDocumentController::class,
        '0215' => \App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratKetetapanTentangPenetapanTersangkaDocumentController::class,
    ];

    /**
     * Cek apakah ada controller terdaftar untuk kategori ini
     */
    public function hasMapping(string $key): bool
    {
        return isset($this->controllerMap[$key]);
    }

    /**
     * Mengambil $responseData dari Controller API secara internal
     *
     * @param string $documentCategoryId
     * @param string $noLp
     * @param string|null $accidentId
     * @param string|null $documentId
     * @param string|null $kodeJenisDoc
     * @return array|null
     */
    public function getResponseData(
        string $documentCategoryId,
        string $noLp,
        ?string $accidentId = null,
        ?string $documentId = null,
        ?string $kodeJenisDoc = null
    ): ?array {
        $controllerClass = $this->controllerMap[$documentCategoryId]
            ?? $this->controllerMap[$kodeJenisDoc]
            ?? null;

        if (!$controllerClass || !class_exists($controllerClass)) {
            Log::warning("PusiknasDocDataService: Controller tidak terdaftar untuk category_id [{$documentCategoryId}]");
            return null;
        }

        try {
            $queryParams = [
                'no_lp'          => $noLp,
                'kode_jenis_doc' => $kodeJenisDoc ?? $documentCategoryId,
            ];

            if ($accidentId) {
                $queryParams['accident_id'] = $accidentId;
            }

            if ($documentId) {
                $queryParams['id'] = $documentId;
            }

            // Buat request internal seolah-olah dipanggil dari API GET
            $subRequest = Request::create('', 'GET', $queryParams);

            $method = 'index';
            if (is_array($controllerClass)) {
                [$controllerClass, $method] = $controllerClass;
            }

            /** @var object $controllerInstance */
            $controllerInstance = App::make($controllerClass);

            $response = $controllerInstance->$method($subRequest);

            if ($response instanceof JsonResponse) {
                $content = $response->getData(true);
                return $content['data'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("PusiknasDocDataService: Error saat resolve response data untuk category [{$documentCategoryId}]: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }
}
