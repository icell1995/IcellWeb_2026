<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use Illuminate\Support\Carbon;

trait DocumentCategoryTraits
{
    protected array $documentCategories = [];

    public function initializeDocumentTrait(): void
    {
        $this->documentCategories = $this->getDocumentCategories();
    }

    protected function getDocumentCategories(): array
    {
        return [
            [
                'name'        => 'Surat Perintah Tugas',
                'model'       => SuratPerintahTugasDocument::class,
                'category_id' => '0702',
            ],
            [
                'name'        => 'Surat Perintah Penyelidikan',
                'model'       => SuratPerintahPenyelidikanDocument::class,
                'category_id' => '0101',
            ],
            [
                'name'        => 'Surat Perintah Penyidikan',
                'model'       => SuratPerintahPenyidikanDocument::class,
                'category_id' => '0201',
            ],
            [
                'name'        => 'Laporan Hasil Gelar Perkara',
                'model'       => LaporanHasilGelarPerkaraDocument::class,
                'category_id' => '0706',
            ],
            [
                'name'        => 'Surat Ketetapan Tentang Penetapan Tersangka',
                'model'       => SuratKetetapanTentangPenetapanTersangkaDocument::class,
                'category_id' => '0215',
            ],
            [
                'name'        => 'Surat Pemberitahuan Dimulainya Penyidikan',
                'model'       => SuratPemberitahuanDimulainyaPenyidikanDocument::class,
                'category_id' => '0204',
            ],
        ];
    }

    public function getAllDocumentsByAccident(string $accidentId): Collection
    {
        $allDocs = collect();

        // Cek SPDP global satu kali
        $spdpCollection = $this->getSPDP($accidentId);
        $spdp = $spdpCollection->first();
        $canReturnAny = true;
        $spdpSyncedMessage = null;

        if ($spdp && $spdp->last_synced_at !== null) {
            $canReturnAny = false;
            $spdpSyncedMessage = 'Tidak dapat mengembalikan dokumen apapun karena SPDP sudah dipertukarkan';
        }

        foreach ($this->documentCategories as $cat) {
            $model = $cat['model'];
            $baseCategoryId = $cat['category_id'];
            $baseName = $cat['name'];

            $query = $model::where('accident_id', $accidentId)
                ->whereNull('deleted_at');

            $docs = $query->get()->map(function ($doc) use ($baseCategoryId, $baseName, $model, $canReturnAny, $spdpSyncedMessage) {
                $categoryCode = $baseCategoryId;
                $categoryName = $baseName;

                if ($baseCategoryId === '0702' && $doc->related_type) {
                    if (str_contains($doc->related_type, 'Penyidikan')) {
                        $categoryCode = '0702-sidik';
                        $categoryName = 'Surat Perintah Tugas Penyidikan';
                    } elseif (str_contains($doc->related_type, 'Penyelidikan')) {
                        $categoryCode = '0702-lidik';
                        $categoryName = 'Surat Perintah Tugas Penyelidikan';
                    }
                }

                $isSynced = $doc->last_synced_at !== null;
                $isReturned = $doc->status_id === 4 || $doc->status_id === '4'; 

                if ($isSynced) {
                    $statusInfo = 'Sudah Dipertukarkan';
                    $badgeStyle = 'bg-danger';
                } elseif ($isReturned) {
                    $statusInfo = 'Sudah Dikembalikan';
                    $badgeStyle = 'bg-danger';
                } else {
                    $statusInfo = 'Tersedia';
                    $badgeStyle = 'bg-info';
                }

                if ($isSynced) {
                    $textDate = 'Tanggal Dipertukarkan';
                    $dateInfo = $doc->last_synced_at
                        ? Carbon::parse($doc->last_synced_at)->format('d M Y H:i')
                        : '-';
                } else {
                    $textDate = 'Tanggal Dibuat';
                    $dateInfo = $doc->created_at
                        ? Carbon::parse($doc->created_at)->format('d M Y H:i')
                        : '-';
                }

                $isEligible = $canReturnAny && !$isSynced && !$isReturned;

                return [
                    'id'                => $doc->id,
                    'category_code'     => $categoryCode,
                    'category_name'     => $categoryName,
                    'title'             => $doc->title ?? $doc->document_number ?? $doc->no_dokumen ?? $doc->nomor_surat ?? 'Dokumen tanpa judul',
                    'type'              => class_basename($model),
                    'created_at'        => $doc->created_at?->format('d M Y H:i') ?? '-',
                    'status_text'       => $statusInfo,
                    'badge_class'       => $badgeStyle,
                    'is_eligible_return' => $isEligible,
                    'cannot_return_reason' => $canReturnAny ? null : $spdpSyncedMessage,
                    'information_date' => $dateInfo,
                    'text_date' => $textDate,
                ];
            });

            $allDocs = $allDocs->merge($docs);
        }

        return $allDocs->sortByDesc('created_at')->values();
    }


    public function getCascadeDocument($documentType, $accidentId, $documentId = null)
    {
        $documents = collect();

        $mainDocument = $this->getDocumentById($documentId, $this->getCategoryIdByType($documentType));

        if (!$mainDocument) {
            throw new \Exception('Dokumen Utama Tidak Ditemukan');
        }

        $documents->push($mainDocument);

        $normalizedType = $this->normalizeDocumentType($documentType);

        switch ($normalizedType) {
            case 'sprinlidik':
                $documents = $documents->merge($this->getSpringasLidik($accidentId));
                break;

            case 'sprindik':
            case 'springas_sidik':
                $documents = $documents->merge($this->getSpringasSidik($accidentId));
                $documents = $documents->merge($this->getSprindik($accidentId));
                $documents = $documents->merge($this->getSpringasSidik($accidentId));
                $documents = $documents->merge($this->getLHGP($accidentId));
                $documents = $documents->merge($this->getTapTersangka($accidentId));
                $documents = $documents->merge($this->getSPDP($accidentId));
                break;

            case 'lhgp':
                $documents = $documents->merge($this->getTapTersangka($accidentId));
                $documents = $documents->merge($this->getSPDP($accidentId));
                break;

            case 'tap_tersangka':
                $documents = $documents->merge($this->getSPDP($accidentId));
                break;

            case 'springas_sidik':
            case 'springas_lidik':
                break;
        }

        return $documents->unique(fn($doc) => get_class($doc) . $doc->id)
            ->values();
    }

    private function normalizeDocumentType($type): string
    {
        $map = [
            '0101' => 'sprinlidik',
            '0201' => 'sprindik',
            '0706' => 'lhgp',
            '0215' => 'tap_tersangka',
            '0204' => 'spdp',
            '0702' => 'springas',
            '0702-sidik' => 'springas_sidik',
            '0702-lidik' => 'springas_lidik',
        ];

        return $map[$type] ?? $type;
    }

    private function getSprindik($accidentId)
    {
        $model = app('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument');

        return $model::where('accident_id', $accidentId)
            ->get();
    }

    private function getSpringasSidik($accidentId)
    {
        $model = app('App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument');

        return $model::where('accident_id', $accidentId)
            ->where('related_type', 'LIKE', '%Penyidikan%')
            ->get();
    }

    private function getSpringasLidik($accidentId)
    {
        $model = app('App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument');

        return $model::where('accident_id', $accidentId)
            ->where('related_type', 'LIKE', '%Penyelidikan%')
            ->get();
    }

    private function getLHGP($accidentId)
    {
        $model = app('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument');

        return $model::where('accident_id', $accidentId)
            ->get();
    }

    private function getTapTersangka($accidentId)
    {
        $model = app('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument');

        return $model::where('accident_id', $accidentId)
            ->get();
    }

    private function getSPDP($accidentId)
    {
        $model = app('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument');

        return $model::where('accident_id', $accidentId)
            ->get();
    }

    private function getDocumentById($documentId, $categoryId = null)
    {
        $categories = $this->getDocumentCategories();

        if ($categoryId) {
            $category = collect($categories)->firstWhere('category_id', $categoryId);

            if ($category) {
                $model = app($category['model']);
                return $model::find($documentId);
            }
        }

        foreach ($categories as $category) {
            $model = app($category['model']);
            $document = $model::find($documentId);
            if ($document) {
                return $document;
            }
        }

        return null;
    }

    private function getCategoryIdByType($documentType)
    {
        $mapping = [
            'sprinlidik'     => '0101',
            'sprindik'       => '0201',
            'springas_sidik' => '0702',
            'springas_lidik' => '0702',
            'lhgp'           => '0706',
            'tap_tersangka'  => '0215',
            'spdp'           => '0204',
            'springas'       => '0702',
            '0702-sidik'     => '0702',
            '0702-lidik'     => '0702',
        ];

        if (preg_match('/^\d{4}$/', $documentType)) {
            return $documentType;
        }

        return $mapping[$documentType] ?? null;
    }
    private function getDocumentTypeByModel(string $modelClass): ?string
    {
        $mapping = [
            SuratPerintahPenyelidikanDocument::class => 'sprinlidik',
            SuratPerintahPenyidikanDocument::class => 'sprindik',
            LaporanHasilGelarPerkaraDocument::class => 'lhgp',
            SuratKetetapanTentangPenetapanTersangkaDocument::class => 'tap_tersangka',
            SuratPemberitahuanDimulainyaPenyidikanDocument::class => 'spdp',
            SuratPerintahTugasDocument::class => 'springas',
        ];

        return $mapping[$modelClass] ?? null;
    }

    public function canReturnDocument($document, string $accidentId): array
    {
        $type = $this->getDocumentTypeByModel(get_class($document));
        if (!$type) {
            return [false, 'Tipe dokumen tidak dikenali'];
        }

        // Ambil semua dokumen yang akan dikembalikan (utama + cascading)
        $documentsToCheck = $this->getCascadeDocument($type, $accidentId, $document->id);

        // Cek SPDP di accident
        $spdpCollection = $this->getSPDP($accidentId);
        $spdp = $spdpCollection->first();

        if ($spdp) {
            if ($spdp->last_synced_at !== null) {
                return [false, 'Tidak dapat mengembalikan karena SPDP sudah dipertukarkan'];
            }
        }

        // Cek last_synced_at semua dokumen yang akan dikembalikan
        foreach ($documentsToCheck as $doc) {
            if ($doc->last_synced_at !== null) {
                return [false, 'Tidak dapat mengembalikan karena ada dokumen yang sudah dipertukarkan'];
            }

            if ($doc->status_id === 4) {
                return [false, 'Dokumen sudah pernah dikembalikan'];
            }
        }

        return [true, ''];
    }

    private function getDocumentCategoryInfo($categoryId)
    {
        $categories = $this->getDocumentCategories();

        $modelClass = get_class($categoryId);

        foreach ($categories as $category) {
            if ($category['category_id'] == $modelClass) {
                return $category['category_id'];
            };
        }

        $tableName = $categoryId->getTable();
        foreach ($categories as $category) {
            if (str_contains($category['table'], $tableName)) {
                return $category['category_id'];
            }
        }

        return null;
    }
}
