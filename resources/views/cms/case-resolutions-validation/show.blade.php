@extends('cms.layouts.app')

@section('_title', 'Detail Selra')

@section('content')
@php
$isApproved = !empty($resolution->approved_at);
$statusClass = $isApproved ? 'success' : 'warning text-dark';
$statusText = $isApproved ? 'Disetujui' : 'Belum Divalidasi';

try {
$docUrl = route('upload-surat-ketetapan.show', [
'upload_surat_ketetapan' => $resolution->accident_id,
]);
} catch (\InvalidArgumentException $e) {
$docUrl = url('/upload-surat-ketetapan/' . $resolution->accident_id);
}

$fileName = $resolution->file_name ?? null;
$tryIframe = !empty($docUrl);
$accident = $resolution->accident ?? null;
// Data untuk validasi
$validationData = [
'no_lp' => $accident->no_lp ?? '',
'tanggal_ketetapan' => optional($resolution->date)->locale('id')->isoFormat('D MMMM YYYY') ?? '',
'nomor_dokumen' => $resolution->number ?? '',
'tersangka' => $personLine ? trim(explode('|', $personLine)[0]) : '',
];

// Jenis Selra untuk validasi khusus
$jenisSelra = $resolution->type_name ?? '';

// Tentukan kata kunci yang harus ada berdasarkan jenis Selra
$requiredKeyword = '';
$forbiddenKeyword = '';
if (stripos($jenisSelra, 'SP2LID') !== false) {
$requiredKeyword = 'Penyelidikan';
$forbiddenKeyword = 'Penyidikan';
} elseif (stripos($jenisSelra, 'SP3') !== false) {
$requiredKeyword = 'Penyidikan';
$forbiddenKeyword = 'Penyelidikan';
} elseif (stripos($jenisSelra, 'P21') !== false) {
$requiredKeyword = 'Kejaksaan';
}
@endphp

<div class="container-fluid px-3 px-md-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('cms.case-resolutions-validations.index') }}"
                class="btn btn-outline-primary back-btn d-inline-flex align-items-center gap-2 rounded-pill shadow-sm"
                role="button" aria-label="Kembali ke daftar">
                <i class="bi bi-arrow-left-circle-fill"></i>
                <span class="fw-semibold">Kembali</span>
            </a>

            <h3 class="m-0 fw-bold text-primary">
                <i class="bi bi-patch-check-fill me-2"></i> Detail Selra
            </h3>
        </div>
    </div>
</div>


{{-- Detail --}}
<div class="row g-4 align-items-stretch">
    <div class="col-lg-8 order-lg-1">
        <div class="card border-0 shadow-sm h-100 pdf-preview" id="pdfPreview">
            <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
                <h6 class="m-0 text-secondary"><i class="bi bi-file-earmark-text me-2"></i>Pratinjau Dokumen</h6>
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{ $docUrl }}">
                        <i class="bi bi-box-arrow-up-right"></i> Buka di Tab
                    </a>
                    <button type="button" class="btn btn-outline-primary js-toggle-full">
                        <i class="bi bi-arrows-fullscreen"></i> Perbesar
                    </button>
                </div>
            </div>
            <div class="card-body p-0 position-relative">
                {{-- PDF Container for PDF.js --}}
                <div id="pdfContainer" style="display: none;">
                    <canvas id="pdfCanvas"></canvas>
                    <div id="textLayer" class="textLayer"></div>
                </div>

                {{-- Fallback: iframe --}}
                @if ($docUrl)
                <iframe src="{{ $docUrl }}" class="pdf-embed" id="pdfIframe" title="Pratinjau Dokumen"
                    referrerpolicy="no-referrer"></iframe>
                @else
                <div class="p-4 text-center text-muted">Tidak ada file yang diunggah.</div>
                @endif
                {{-- Loading Indicator --}}
                <div id="pdfLoading" class="position-absolute top-50 start-50 translate-middle" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2 text-muted">Memuat PDF...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- INFORMASI DOKUMEN --}}
    <div class="col-lg-4 order-lg-2">
        <div class="d-flex flex-column gap-2 h-100">
            <div class="p-3 rounded-3 border bg-light">
                <div class="h5 mb-1">Nomor LP :</div>
                @if ($accident && $accident->id)
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}" target="_blank"
                    rel="noopener" class="h5 m-0 fw-bold text-decoration-none link-primary text-break">
                    {{ $accident->no_lp ?? '-' }}
                    <i class="bi bi-arrow-up-right-square ms-1"></i>
                </a>
                @else
                <div class="h5 m-0 fw-bold">-</div>
                @endif
            </div>
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="m-0 text-secondary"><i class="bi bi-info-circle me-2"></i>Informasi Dokumen</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="small text-muted">Jenis Selra</div>
                            <div class="h5 m-0 fw-bold">{{ $resolution->type_name ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted">Tanggal Upload</div>
                            <div class="fw-semibold">
                                {{ optional($resolution->uploaded_at)->format('d-m-Y H:i') ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <table class="table table-sm table-bordered mt-2 mb-2 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small text-muted fw-normal">Tgl Kejadian</th>
                                        <th class="small text-muted fw-normal">Tgl Dilapor</th>
                                        <th class="small text-muted fw-normal">Tgl Ketetapan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">{{ optional($resolution->accident)->accident_date ? \Carbon\Carbon::parse($resolution->accident->accident_date)->format('d-m-Y') : '-' }}</td>
                                        <td class="fw-semibold">{{ optional($resolution->accident)->report_date ? \Carbon\Carbon::parse($resolution->accident->report_date)->format('d-m-Y') : '-' }}</td>
                                        <td class="fw-semibold">{{ optional($resolution->date)->format('d-m-Y') ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted">Nomor Dokumen</div>
                            <div class="fw-semibold">{{ $resolution->number ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted">Tersangka / Terlapor</div>
                            <div class="fw-semibold">{{ $personLine ? trim(explode('|', $personLine)[0]) : '-' }}</div>
                        </div>
                        <div class="col-12" style="font-size: 1.2rem;">
                            <div class="small text-muted"><i class="bi bi-shield-check me-1"></i>Status Saat Ini</div>
                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                            <br>
                            <span>
                                @if ($isApproved)
                                <span class="badge bg-light text-secondary border">
                                    Tanggal: {{ optional($resolution->approved_at)->format('d-m-Y H:i') }}
                                </span>
                                @endif
                            </span>
                            </br>
                        </div>
                        <div class="col-12" style="font-size: 1.2rem;">
                            @if ($resolution->was_rejected_before)
                            <span class="badge rounded-pill text-bg-danger" data-bs-toggle="tooltip"
                                data-bs-title="{{ $resolution->last_reject_info }}">
                                Pernah Dikembalikan
                            </span>
                            <div class="mrt-2 mt-1 medium text-muted">
                                <span>{{ $resolution->last_reject_info }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
    
                    @if ($docUrl)
                    <hr>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{ $docUrl }}">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Dokumen
                        </a>
                        @if ($fileName)
                        <span class="small text-muted">Nama berkas: <code>{{ $fileName }}</code></span>
                        @endif
                    </div>
                    <div class="small text-muted mt-2">
                        * Jika pratinjau tidak tampil, klik "Buka Dokumen" untuk membuka di tab baru.
                    </div>
                    @endif
                </div>
    
                <div class="card-body d-flex justify-content-between align-items-center gap-2 px-3 py-2">
                    <div class="flex align-items-start">
                        {{-- Tombol Cek Validasi --}}
                        <button type="button" class="btn cursor-pointer" id="checkValidationBtn">
                            <i class="bi bi-ui-checks me-0n text-white"></i>
                        </button>
                    </div>
                    <div class="d-flex gap-2 justify-content-end align-items-center">
                        {{-- Tombol Setujui / Kembalikan --}}
                        @unless ($isApproved)
                        <form action="{{ route('cms.case-resolutions-validations.approve', $resolution->id) }}"
                            method="POST" class="js-approve">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check2-circle me-1"></i> Setujui
                            </button>
                        </form>
                        @endunless
    
                        <form action="{{ route('cms.case-resolutions-validations.reject', $resolution->id) }}" method="POST"
                            class="js-reject">
                            @csrf @method('PUT')
                            <input type="hidden" name="reject_reason" value="">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Hasil Validasi --}}
<div class="modal fade" id="validationResultModal" tabindex="-1" aria-labelledby="validationResultModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="validationResultModalLabel">
                    <i class="bi bi-clipboard-check me-2"></i>Hasil Validasi Dokumen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Loading State --}}
                <div id="validationLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Memvalidasi...</span>
                    </div>
                    <p class="text-muted">Memvalidasi dokumen, mohon tunggu...</p>
                </div>

                {{-- Hasil Validasi --}}
                <div id="validationResults" style="display: none;">
                    {{-- Overall Score --}}
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold mb-2" id="overallScore">0%</div>
                        <p class="text-muted mb-0">Kecocokan Keseluruhan</p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div id="overallProgressBar" class="progress-bar" role="progressbar" style="width: 0%">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Detail Per Field --}}
                    <h6 class="mb-3 fw-bold"><i class="bi bi-list-check me-2"></i>Detail Validasi</h6>
                    <div class="list-group" id="validationDetailList">
                        {{-- Will be populated by JavaScript --}}
                    </div>

                    {{-- Summary --}}
                    <div class="mt-4 p-3 rounded" id="validationSummaryBox">
                        <h6 class="mb-2 fw-bold"><i class="bi bi-info-circle me-2"></i>Ringkasan</h6>
                        <ul class="mb-0 small" id="validationSummaryContent">
                            {{-- Will be populated by JavaScript --}}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        {{-- Tombol Setujui / Kembalikan --}}
                        @unless ($isApproved)
                        <form action="{{ route('cms.case-resolutions-validations.approve', $resolution->id) }}" method="POST"
                            class="js-approve">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check2-circle me-1"></i> Setujui
                            </button>
                        </form>
                        @endunless
            
                        <form action="{{ route('cms.case-resolutions-validations.reject', $resolution->id) }}" method="POST"
                            class="js-reject">
                            @csrf @method('PUT')
                            <input type="hidden" name="reject_reason" value="">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan
                            </button>
                        </form>
                    </div>
                    
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* ========================================
    * Back Button Styles
    * ======================================== */
    .back-btn {
        border-width: 2px;
        padding: .6rem 1rem;
        transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .back-btn:hover {
        transform: translateX(-2px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08);
    }

    .back-btn:active {
        transform: translateX(-1px) translateY(1px);
    }

    .cursor-pointer {
    cursor: default !important;
    }

    /* ========================================
    * PDF Preview Styles
    * ======================================== */
    .pdf-preview .pdf-embed {
        width: 100%;
        height: 95vh;
        border: 0;
    }

    @media (max-width: 991.98px) {
        .pdf-preview .pdf-embed {
            height: 62vh;
        }
    }

    /* ========================================
    * Fullscreen Mode
    * ======================================== */
    .pdf-preview.fullscreen {
        position: fixed;
        inset: 12px;
        z-index: 1080;
        width: auto;
        height: auto;
        box-shadow: 0 .75rem 2rem rgba(0, 0, 0, .2);
    }

    .pdf-preview.fullscreen .pdf-embed {
        height: calc(100vh - 96px);
    }

    body.no-scroll {
        overflow: hidden;
    }

    /* ========================================
    * VALIDATION PANEL STYLES
    * ======================================== */
    #validationPanel {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .validation-item {
        transition: all 0.2s ease;
    }

    .validation-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .validation-item.validated {
        border-color: #198754 !important;
        background-color: #f0f9f4;
    }

    .validation-item.not-found {
        border-color: #dc3545 !important;
        background-color: #fdf0f0;
    }

    /* Status badges */
    .badge.status-pending {
        background-color: #ffc107 !important;
        color: #000;
    }

    .badge.status-found {
        background-color: #198754 !important;
        color: #fff;
    }

    .badge.status-not-found {
        background-color: #dc3545 !important;
        color: #fff;
    }

    /* ========================================
    * VALIDATION MODAL STYLES
    * ======================================== */
    #validationResultModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .validation-field-item {
        transition: all 0.3s ease;
    }

    .validation-field-item:hover {
        background-color: #f8f9fa;
    }

    .validation-field-item .percentage {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .validation-field-item .badge {
        font-size: 0.875rem;
    }

    /* Progress bar colors */
    .progress-bar.bg-success {
        background-color: #198754 !important;
    }

    .progress-bar.bg-warning {
        background-color: #ffc107 !important;
    }

    .progress-bar.bg-danger {
        background-color: #dc3545 !important;
    }

    /* Overall score animation */
    #overallScore {
        animation: fadeInScale 0.5s ease;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Summary box styling */
    #validationSummaryBox {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
    }

    #validationSummaryBox.success {
        background-color: #d1e7dd;
        border-left-color: #198754;
    }

    #validationSummaryBox.warning {
        background-color: #fff3cd;
        border-left-color: #ffc107;
    }

    #validationSummaryBox.danger {
        background-color: #f8d7da;
        border-left-color: #dc3545;
    }

    /* ========================================
    * SWEETALERT MODAL FIX - NO SCROLL
    * ======================================== */
    .swal-no-overflow {
        max-height: 90vh !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .swal-no-overflow .swal2-html-container {
        max-height: none !important;
        overflow: visible !important;
        margin: 0 !important;
        padding: 0 16px !important;
    }

    .swal-scrollable-content {
        overflow-y: auto !important;
        max-height: 60vh !important;
    }

    /* Custom scrollbar untuk modal */
    .swal-scrollable-content::-webkit-scrollbar {
        width: 8px;
    }

    .swal-scrollable-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .swal-scrollable-content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .swal-scrollable-content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Pastikan checkbox list tidak terlalu tinggi */
    .swal-no-overflow .border.rounded.p-3 {
        max-height: 200px !important;
        overflow-y: auto !important;
    }

    /* Responsive untuk layar kecil */
    @media (max-height: 700px) {
        .swal-scrollable-content {
            max-height: 50vh !important;
        }

        .swal-no-overflow .border.rounded.p-3 {
            max-height: 150px !important;
        }
    }

    @media (max-height: 600px) {
        .swal-scrollable-content {
            max-height: 40vh !important;
        }

        .swal-no-overflow .border.rounded.p-3 {
            max-height: 120px !important;
        }
    }
</style>
@endpush

@push('script')
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>
<script>
    /**
     * ========================================
     * GLOBAL CONFIGURATION
     * ========================================
     */
    const APP_CONFIG = {
        routes: {
            index: "{{ route('cms.case-resolutions-validations.index') }}"
        },
        timing: {
            successDelay: 1000,
        },

        pdfUrl: "{{ $docUrl }}",
        validationData: @json($validationData),
        jenisSelra: "{{ $jenisSelra }}",
        requiredKeyword: "{{ $requiredKeyword }}",
        forbiddenKeyword: "{{ $forbiddenKeyword }}"
    };
        
    /**
    * ========================================
    * PDF VALIDATOR CLASS
    * ========================================
    */
    class PDFValidator {
        constructor(pdfUrl) {
            this.pdfUrl = pdfUrl;
            this.pdfDoc = null;
            this.textContent = '';
        }

        async loadPDF() {
            try {
                const loadingTask = pdfjsLib.getDocument(this.pdfUrl);
                this.pdfDoc = await loadingTask.promise;
                await this.extractAllText();
                return true;
            } catch (error) {
                console.error('Error loading PDF:', error);
                return false;
            }
        }

        async extractAllText() {
            let fullText = '';
            for (let pageNum = 1; pageNum <= this.pdfDoc.numPages; pageNum++) {
                const page = await this.pdfDoc.getPage(pageNum);
                const textContent = await page.getTextContent();
                const pageText = textContent.items.map(item => item.str).join(' ');
                fullText += pageText + '\n';
            }
            this.textContent = fullText;
            this.isImageBased = fullText.replace(/\s+/g, '').length < 30;
        }
        
        countOccurrences(text, search) {
            if (!search) return 0;
            const regex = new RegExp(search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
            const matches = text.match(regex);
            return matches ? matches.length : 0;
        }
        
        searchText(searchValue) {
            if (!searchValue || !this.textContent) {
                return {
                    found: false,
                    count: 0,
                    percentage: 0,
                    message: 'Tidak ada teks'
                };
            }
        
            const normalizedSearch = searchValue
                .trim()
                .replace(/\s+/g, ' ')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        
            const normalizedText = this.textContent
                .replace(/\s+/g, ' ')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        
            // 1. EXACT MATCH
            const exactMatches = this.countOccurrences(normalizedText, normalizedSearch);
            if (exactMatches > 0) {
                return {
                    found: true,
                    count: exactMatches,
                    percentage: 100,
                    matchType: 'exact',
                    message: `✓ Ditemukan ${exactMatches}x (100% cocok)`,
                    searchValue: searchValue
                };
            }
        
            // 2. NOMOR LP SPECIAL HANDLING
            if (/LP[\/\-\s]/i.test(searchValue) || /^[A-Z]+\//.test(searchValue)) {
                const lpResult = this.searchNomorLP(searchValue, normalizedText);
                if (lpResult.percentage >= 50) {
                    return lpResult;
                }
            }
        
            // 3. FUZZY MATCH
            const words = normalizedSearch.split(/\s+/).filter(w => w.length > 2);
            let partialMatches = 0;
            let foundWords = [];
        
            words.forEach(word => {
                const count = this.countOccurrences(normalizedText, word);
                if (count > 0) {
                    partialMatches += count;
                    foundWords.push(word);
                }
            });
        
            const wordMatchPercentage = words.length > 0 ? Math.round((foundWords.length / words.length) * 100) : 0;
        
            if (foundWords.length >= Math.ceil(words.length * 0.6)) {
                return {
                    found: true,
                    count: partialMatches,
                    percentage: wordMatchPercentage,
                    matchType: 'partial',
                    partial: true,
                    message: `✓ ${foundWords.length}/${words.length} kata (${wordMatchPercentage}% cocok)`,
                    searchValue: searchValue,
                    debug: {
                    foundWords: foundWords,
                    missingWords: words.filter(w => !foundWords.includes(w))
                    }
                };
            }
        
            // 4. NO SPACE MATCH
            const noSpaceSearch = normalizedSearch.replace(/\s/g, '');
            const noSpaceText = normalizedText.replace(/\s/g, '');
        
            if (noSpaceText.includes(noSpaceSearch)) {
                return {
                    found: true,
                    count: 1,
                    percentage: 95,
                    matchType: 'no-space',
                    partial: true,
                    message: `✓ Ditemukan (95% cocok - tanpa spasi)`,
                    searchValue: searchValue
                };
            }
        
            return {
                found: false,
                count: 0,
                percentage: 0,
                matchType: 'none',
                message: `✗ Tidak ditemukan (0% cocok)`,
                debug: {
                    searched: normalizedSearch,
                    words: words,
                    foundWords: foundWords,
                    percentage: wordMatchPercentage
                }
            };
        }
        
        searchNomorLP(lpNumber, normalizedText) {
            const normalizedLP = lpNumber
                .trim()
                .replace(/\s+/g, ' ')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        
            // 1. EXACT MATCH (100%)
            if (normalizedText.includes(normalizedLP)) {
                return {
                    found: true,
                    count: 1,
                    percentage: 100,
                    matchType: 'lp-exact',
                    message: `✓ Nomor LP ditemukan (100% cocok)`,
                    searchValue: lpNumber
                };
            }
        
            // 2. MATCH WITHOUT SPACES (95%)
            const lpNoSpace = normalizedLP.replace(/\s/g, '');
            const textNoSpace = normalizedText.replace(/\s/g, '');
        
            if (textNoSpace.includes(lpNoSpace)) {
                return {
                    found: true,
                    count: 1,
                    percentage: 95,
                    matchType: 'lp-no-space',
                    message: `✓ Nomor LP ditemukan (95% cocok - tanpa spasi)`,
                    searchValue: lpNumber
                };
            }
        
            // 3. SPLIT AND MATCH PARTS
            const lpParts = normalizedLP.split(/[\/\-\s]+/).filter(p => p.length > 0);
        
            const significantParts = lpParts.filter(part => {
                if (part.length > 2) return true;
                if (/^\d+$/.test(part)) return true;
                const importantKeywords = ['lp', 'b', 'a', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'];
                if (importantKeywords.includes(part.toLowerCase())) return true;
                if (part.length === 2 && /^[a-z]+$/i.test(part)) return true;
                return false;
            });
        
            if (significantParts.length === 0) {
                return {
                    found: false,
                    percentage: 0,
                    message: '✗ Format nomor LP tidak valid'
                };
            }
        
            // Find matches for each significant part
            let matchedParts = [];
            let partialMatches = [];
        
            significantParts.forEach(part => {
                let bestMatch = {
                    found: false,
                    similarity: 0,
                    method: ''
                };
        
            // Strategy 1: Exact match
            if (normalizedText.includes(part)) {
                bestMatch = { found: true, similarity: 100, method: 'exact' };
            }
            // Strategy 2: No space match in no-space text
            else if (textNoSpace.includes(part.replace(/\s/g, ''))) {
                bestMatch = { found: true, similarity: 95, method: 'no-space' };
            }
            // Strategy 3: Part without space in normal text
            else if (normalizedText.includes(part.replace(/\s/g, ''))) {
                bestMatch = { found: true, similarity: 90, method: 'normalized' };
            }
            // Strategy 4: Fuzzy matching
            else {
                const words = normalizedText.split(/\s+/);
                words.forEach(word => {
                    const similarity = calculateSimilarity(part, word);
                    if (similarity > bestMatch.similarity && similarity >= 70) {
                        bestMatch = { found: true, similarity: similarity, method: 'fuzzy', matchedWord: word };
                    }
                });
            }
        
            // Add to matched parts if found (>= 70% similarity)
            if (bestMatch.found && bestMatch.similarity >= 70) {
                matchedParts.push(part);
                partialMatches.push({
                    part: part,
                    similarity: bestMatch.similarity,
                    method: bestMatch.method,
                    matchedWord: bestMatch.matchedWord || part
                });
            }
        });
        
        // Calculate percentage
        const matchPercentage = significantParts.length > 0
            ? Math.round((matchedParts.length / significantParts.length) * 100)
            : 0;
        
        // 4. ADDITIONAL CHECK: Try to find consecutive parts
        if (matchPercentage < 70 && matchedParts.length > 0) {
            const matchedSequence = matchedParts.join('.*');
            const sequenceRegex = new RegExp(matchedSequence, 'i');
        
            if (sequenceRegex.test(normalizedText) || sequenceRegex.test(textNoSpace)) {
                const boostedPercentage = Math.min(100, matchPercentage + 15);
        
                if (boostedPercentage >= 70) {
                    return {
                        found: true,
                        count: 1,
                        percentage: boostedPercentage,
                        matchType: 'lp-sequential',
                        partial: true,
                        message: '✓ Nomor LP ditemukan (' + boostedPercentage + '% cocok - urutan)',
                        searchValue: lpNumber,
                        debug: {
                            matchedParts: matchedParts,
                            totalParts: significantParts.length,
                            missingParts: significantParts.filter(p => !matchedParts.includes(p))
                        }
                    };
                }
            }
        }
    
        
        // 5. RETURN RESULT BASED ON THRESHOLD
        if (matchPercentage >= 70) {
            const detailsArray = partialMatches.map(pm => {
                const matchedWord = pm.matchedWord || pm.part;
                return pm.part + ' → ' + matchedWord + ' (' + pm.similarity + '% - ' + pm.method + ')';
            });
            
            return {
                found: true,
                count: 1,
                percentage: matchPercentage,
                matchType: 'lp-partial',
                partial: true,
                message: '✓ Nomor LP ditemukan (' + matchPercentage + '% cocok)',
                searchValue: lpNumber,
                debug: {
                    matchedParts: matchedParts,
                    totalParts: significantParts.length,
                    missingParts: significantParts.filter(p => !matchedParts.includes(p)),
                    partialMatchDetails: detailsArray
                }
            };
        } else if (matchPercentage >= 50) {
            return {
                found: true,
                count: 1,
                percentage: matchPercentage,
                matchType: 'lp-weak',
                partial: true,
                message: '⚠ Nomor LP kemungkinan ditemukan (' + matchPercentage + '% cocok)',
                searchValue: lpNumber,
                debug: {
                    matchedParts: matchedParts,
                    totalParts: significantParts.length,
                    missingParts: significantParts.filter(p => !matchedParts.includes(p))
                }
            };
        }
            
        // 6. NOT FOUND
        return {
            found: false,
            percentage: matchPercentage,
            matchType: 'lp-not-found',
            message: '✗ Nomor LP tidak ditemukan (' + matchPercentage + '% cocok)',
            searchValue: lpNumber,
            debug: {
                matchedParts: matchedParts,
                totalParts: significantParts.length,
                missingParts: significantParts.filter(p => !matchedParts.includes(p)),
                searchedIn: normalizedLP,
                lpParts: lpParts,
                significantParts: significantParts
            }
        };
    }
    }

    /**
     * ========================================
     * VALIDATION UI CONTROLLER
     * ========================================
     */
    class ValidationController {
        constructor() {
            this.validator = null;
            this.results = {};
            this.validationData = APP_CONFIG.validationData;
            this.init();
        }

        async init() {
            if (APP_CONFIG.pdfUrl) {
                this.validator = new PDFValidator(APP_CONFIG.pdfUrl);
                const loadingEl = document.getElementById('pdfLoading');
                if (loadingEl) loadingEl.style.display = 'block';

                const loaded = await this.validator.loadPDF();

                if (loadingEl) loadingEl.style.display = 'none';

                if (!loaded) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'PDF tidak dapat dimuat. Validasi mungkin tidak akurat.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Jika PDF berbasis gambar (scan), tampilkan notifikasi
                    if (this.validator.isImageBased) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Dokumen Terdeteksi Berbasis Gambar',
                            html: '<small>Dokumen ini adalah hasil scan atau foto. Sistem tidak dapat mengekstrak teks untuk validasi otomatis.</small>',
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                        });
                    }
                    // Auto-validate silently on load (otomatis)
                    this.runValidation(true);
                }
            }
            this.bindEvents();
        }

        bindEvents() {
            const checkBtn = document.getElementById('checkValidationBtn');
            if (checkBtn) {
                checkBtn.addEventListener('click', () => {
                    try {
                        this.runValidation();
                    } catch (error) {
                        console.error('Validation error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat validasi: ' + error.message,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        }

    async runValidation(silent = false) {
        if (!this.validator) {
            if (!silent) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'PDF Validator belum siap. Silakan refresh halaman.',
                    confirmButtonText: 'OK'
                });
            }
            return;
        }

        // Jika mode manual, langsung tampilkan modal manual
        if (this.isManualMode) {
            if (!silent) {
                this.showManualValidationModal();
            }
            return;
        }

        if (!silent) {
            const modal = new bootstrap.Modal(document.getElementById('validationResultModal'));
            modal.show();
            document.getElementById('validationLoading').style.display = 'block';
            document.getElementById('validationResults').style.display = 'none';
        }

        this.results = {};

        const fields = Object.keys(this.validationData);
        for (const field of fields) {
            const value = this.validationData[field];
            if (value) {
                await this.validateField(field, value);
                if (!silent) await new Promise(resolve => setTimeout(resolve, 300));
            }
        }

        if (APP_CONFIG.requiredKeyword) {
            await this.validateJenisSelra();
        }

        if (!silent) {
            document.getElementById('validationLoading').style.display = 'none';
            document.getElementById('validationResults').style.display = 'block';
        }

        this.displayResults();
    }
        
    async validateField(field, value) {
        let result;
        if (field === 'tanggal_ketetapan') {
            result = await this.validateTanggal(value);
        } else if (field === 'tersangka') {
            result = await this.validateTersangka(value);
        } else if (field === 'nomor_dokumen') {
            result = await this.validateNomorDokumen(value);
        } else {
            result = await this.validator.searchText(value);
        }
        this.results[field] = {
            ...result,
            label: this.getFieldLabel(field),
            value: value
        };
    }

        async validateTanggal(tanggal) {
            // Try exact match first
            const exact = await this.validator.searchText(tanggal);
            if (exact.found) return exact;

            // Generate all format variants
            const variants = this.generateDateVariants(tanggal);
            for (const v of variants) {
                const r = await this.validator.searchText(v);
                if (r.found) return { ...r, value: tanggal };
            }

            // Best effort: return lowest found
            return { found: false, percentage: 0, matchType: 'date-not-found', value: tanggal };
        }

        generateDateVariants(tanggal) {
            const monthMapId = {
                'januari':  '01', 'februari': '02', 'maret':    '03',
                'april':    '04', 'mei':       '05', 'juni':     '06',
                'juli':     '07', 'agustus':  '08', 'september':'09',
                'oktober':  '10', 'november': '11', 'desember': '12'
            };
            const monthNames = [
                '', 'Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'
            ];
            let variants = [tanggal];
            const lc = tanggal.toLowerCase().trim();

            // Case A: Indonesian format  "27 Juni 2026"
            const mA = lc.match(/^(\d{1,2})\s+([a-z]+)\s+(\d{4})$/);
            if (mA) {
                const d = mA[1].padStart(2,'0');
                const m = monthMapId[mA[2]];
                const y = mA[3];
                if (m) {
                    variants.push(`${d}-${m}-${y}`, `${d}/${m}/${y}`, `${d}.${m}.${y}`,
                                  `${d} ${m} ${y}`, `${mA[1]} ${mA[2]} ${y}`,
                                  `${mA[1]}-${m}-${y}`, `${d}${m}${y}`);
                }
            }

            // Case B: numeric  "27-06-2026" or "27/06/2026"
            const mB = lc.match(/^(\d{1,2})[\-\/\.](\d{1,2})[\-\/\.](\d{4})$/);
            if (mB) {
                const d = mB[1].padStart(2,'0');
                const m = mB[2].padStart(2,'0');
                const mi = parseInt(mB[2], 10);
                const y = mB[3];
                variants.push(`${d}-${m}-${y}`, `${d}/${m}/${y}`, `${d}.${m}.${y}`,
                              `${d} ${m} ${y}`, `${d}${m}${y}`);
                if (mi >= 1 && mi <= 12) {
                    variants.push(`${mB[1]} ${monthNames[mi]} ${y}`,
                                  `${d} ${monthNames[mi]} ${y}`);
                }
            }

            return [...new Set(variants)];
        }

        async validateTersangka(nama) {
            const exact = await this.validator.searchText(nama);
            if (exact.found) return exact;

            // Match by individual words (length > 2)
            const words = nama.trim().split(/\s+/).filter(w => w.length > 2);
            let found = [];
            for (const w of words) {
                const r = await this.validator.searchText(w);
                if (r.found) found.push(w);
            }
            const pct = words.length > 0 ? Math.round((found.length / words.length) * 100) : 0;
            return {
                found: found.length >= Math.ceil(words.length * 0.5),
                percentage: pct,
                matchType: found.length > 0 ? 'name-partial' : 'none',
                debug: { matchedParts: found, missingParts: words.filter(w => !found.includes(w)) }
            };
        }

        async validateNomorDokumen(nomor) {
            const exact = await this.validator.searchText(nomor);
            if (exact.found) return exact;

            // Split by common separators and try segment matching
            const segments = nomor.split(/[\/\-\s,]+/).filter(s => s.length > 0);
            let found = [];
            for (const s of segments) {
                const r = await this.validator.searchText(s);
                if (r.found) found.push(s);
            }
            const pct = segments.length > 0 ? Math.round((found.length / segments.length) * 100) : 0;
            return {
                found: found.length >= Math.ceil(segments.length * 0.6),
                percentage: pct,
                matchType: found.length > 0 ? 'doc-partial' : 'none',
                debug: { matchedParts: found, missingParts: segments.filter(s => !found.includes(s)) }
            };
        }
        
        async validateJenisSelra() {
        const requiredKeyword = APP_CONFIG.requiredKeyword;
        const forbiddenKeyword = APP_CONFIG.forbiddenKeyword;
        const jenisSelra = APP_CONFIG.jenisSelra;
        
        if (!requiredKeyword && !forbiddenKeyword) {
        return;
        }
        
        let requiredResult = { found: false, count: 0, percentage: 0 };
        if (requiredKeyword) {
        requiredResult = await this.validator.searchText(requiredKeyword);
        }
        
        let forbiddenResult = { found: false, count: 0, percentage: 0 };
        if (forbiddenKeyword) {
        forbiddenResult = await this.validator.searchText(forbiddenKeyword);
        }
        
        let finalPercentage = 0;
        let isValid = false;
        let matchType = 'invalid';
        let message = '';
        let validationDetails = {
        required: requiredKeyword,
        requiredFound: requiredResult.found,
        requiredCount: requiredResult.count,
        requiredPercentage: requiredResult.percentage || 0,
        forbidden: forbiddenKeyword,
        forbiddenFound: forbiddenResult.found,
        forbiddenCount: forbiddenResult.count,
        forbiddenPercentage: forbiddenResult.percentage || 0
        };
        
        if (requiredKeyword && forbiddenKeyword) {
        if (requiredResult.found && !forbiddenResult.found) {
        isValid = true;
        finalPercentage = requiredResult.percentage || 100;
        matchType = 'valid-complete';
        message = `✓ Sesuai - Mengandung "${requiredKeyword}" dan tidak mengandung "${forbiddenKeyword}" (${finalPercentage}%
        cocok)`;
        } else if (requiredResult.found && forbiddenResult.found) {
        isValid = false;
        finalPercentage = 0;
        matchType = 'invalid-conflict';
        message = `✗ Tidak sesuai - Mengandung "${requiredKeyword}" tetapi juga mengandung "${forbiddenKeyword}" yang seharusnya
        tidak ada`;
        validationDetails.conflictReason = `Dokumen mengandung kedua kata: "${requiredKeyword}" (${requiredResult.count}x) dan
        "${forbiddenKeyword}" (${forbiddenResult.count}x)`;
        } else if (!requiredResult.found && forbiddenResult.found) {
        isValid = false;
        finalPercentage = 0;
        matchType = 'invalid-wrong-type';
        message = `✗ Tidak sesuai - Tidak mengandung "${requiredKeyword}" tetapi mengandung "${forbiddenKeyword}"`;
        validationDetails.wrongTypeReason = `Dokumen kemungkinan bukan ${jenisSelra}`;
        } else {
        isValid = false;
        finalPercentage = requiredResult.percentage || 0;
        matchType = finalPercentage >= 50 ? 'invalid-partial' : 'invalid-not-found';
        
        if (finalPercentage >= 50) {
        message = `⚠ Kemungkinan sesuai - Kata "${requiredKeyword}" kemungkinan ditemukan (${finalPercentage}% cocok)`;
        } else {
        message = `✗ Tidak sesuai - Tidak mengandung "${requiredKeyword}" (${finalPercentage}% cocok)`;
        }
        }
        } else if (requiredKeyword && !forbiddenKeyword) {
        if (requiredResult.found) {
        isValid = true;
        finalPercentage = requiredResult.percentage || 100;
        matchType = requiredResult.percentage >= 100 ? 'valid-exact' : 'valid-partial';
        message = `✓ Sesuai - Mengandung "${requiredKeyword}" (${finalPercentage}% cocok)`;
        } else {
        isValid = false;
        finalPercentage = requiredResult.percentage || 0;
        matchType = finalPercentage >= 50 ? 'invalid-partial' : 'invalid-not-found';
        
        if (finalPercentage >= 50) {
        message = `⚠ Kemungkinan sesuai - Kata "${requiredKeyword}" kemungkinan ditemukan (${finalPercentage}% cocok)`;
        } else {
        message = `✗ Tidak sesuai - Tidak mengandung "${requiredKeyword}" (${finalPercentage}% cocok)`;
        }
        }
        } else if (!requiredKeyword && forbiddenKeyword) {
        if (!forbiddenResult.found) {
        isValid = true;
        finalPercentage = 100;
        matchType = 'valid-no-forbidden';
        message = `✓ Sesuai - Tidak mengandung kata terlarang "${forbiddenKeyword}"`;
        } else {
        isValid = false;
        finalPercentage = 0;
        matchType = 'invalid-has-forbidden';
        message = `✗ Tidak sesuai - Mengandung kata terlarang "${forbiddenKeyword}" (${forbiddenResult.count}x)`;
        }
        }
        
        this.results['jenis_selra'] = {
        found: isValid,
        percentage: finalPercentage,
        count: requiredResult.count,
        matchType: matchType,
        message: message,
        label: 'Validasi Jenis Selra',
        value: jenisSelra,
        details: validationDetails
        };
        }
        
        displayResults() {
            const overallScore = this.calculateOverallScore();
            const detailList = document.getElementById('validationDetailList');
            const summaryContent = document.getElementById('validationSummaryContent');
            const summaryBox = document.getElementById('validationSummaryBox');
            
            document.getElementById('overallScore').textContent = `${overallScore}%`;
            const progressBar = document.getElementById('overallProgressBar');
            progressBar.style.width = `${overallScore}%`;
            progressBar.className = `progress-bar ${this.getScoreColor(overallScore)}`;
            
            let detailHTML = '';

            // Tampilkan scan warning jika ada
            if (this.results['_scan_warning']) {
                detailHTML += `
                <div class="list-group-item list-group-item-warning">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5 mt-1"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">${this.results['_scan_warning'].label}</h6>
                            <small class="text-muted">${this.results['_scan_warning'].value}</small>
                        </div>
                    </div>
                </div>`;
            } else {
                const fieldOrder = ['no_lp', 'jenis_selra', 'tanggal_ketetapan', 'nomor_dokumen', 'tersangka'];
                fieldOrder.forEach(field => {
                    if (this.results[field]) {
                        detailHTML += this.buildFieldItem(this.results[field]);
                    }
                });
            }
            detailList.innerHTML = detailHTML;

            
            const totalFields = Object.keys(this.results).length;
            const foundFields = Object.values(this.results).filter(r => r.found).length;
            const notFoundFields = totalFields - foundFields;
            
            summaryContent.innerHTML = `
                <li><strong>Total item divalidasi:</strong> ${totalFields}</li>
                <li class="text-success"><strong>Ditemukan/Sesuai:</strong> ${foundFields}</li>
                <li class="text-danger"><strong>Tidak ditemukan/Tidak sesuai:</strong> ${notFoundFields}</li>
                <li><strong>Tingkat kecocokan:</strong> ${overallScore}%</li>
            `;
            
            summaryBox.className = `mt-4 p-3 rounded ${this.getSummaryBoxClass(overallScore)}`;
        }

        buildFieldItem(result) {
            const percentage = result.percentage || 0;
            
            let iconClass, statusBadge;
            
            if (percentage >= 70) {
                iconClass = 'bi-check-circle-fill text-success';
                statusBadge = `<span class="badge bg-success">Ditemukan</span>`;
            } else if (percentage >= 50) {
                iconClass = 'bi-exclamation-circle-fill text-warning';
                statusBadge = `<span class="badge bg-warning">Kemungkinan</span>`;
            } else {
                iconClass = 'bi-x-circle-fill text-danger';
                statusBadge = `<span class="badge bg-danger">Tidak Ditemukan</span>`;
            }
            
            let detailInfo = '';
            
            if (result.details && (result.details.required || result.details.forbidden)) {
                detailInfo = `<div class="mt-2 small">`;
                
                if (result.details.required) {
                    const reqPercentage = result.details.requiredPercentage || 0;
                    if (result.details.requiredFound) {
                        detailInfo += `
                        <div class="text-success mb-1">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Mengandung kata "<strong>${result.details.required}</strong>"
                            (${result.details.requiredCount}x, ${reqPercentage}% cocok)
                        </div>
                        `;
                    } else {
                        detailInfo += `
                        <div class="text-danger mb-1">
                            <i class="bi bi-x-circle-fill me-1"></i>
                            Tidak mengandung kata "<strong>${result.details.required}</strong>"
                            ${reqPercentage > 0 ? `(${reqPercentage}% cocok)` : ''}
                        </div>
                        `;
                    }
                }
                
                if (result.details.forbidden) {
                    if (result.details.forbiddenFound) {
                        detailInfo += `
                        <div class="text-danger mb-1">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Mengandung kata terlarang "<strong>${result.details.forbidden}</strong>"
                            (${result.details.forbiddenCount}x)
                        </div>
                        `;
                    } else {
                        detailInfo += `
                        <div class="text-success mb-1">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            Tidak mengandung kata terlarang "<strong>${result.details.forbidden}</strong>"
                        </div>
                        `;
                    }
                }
                
                if (result.details.conflictReason) {
                    detailInfo += `
                    <div class="alert alert-danger py-2 px-2 mb-1 mt-2">
                        <small><strong>⚠️ Konflik:</strong> ${result.details.conflictReason}</small>
                    </div>
                    `;
                }
                
                if (result.details.wrongTypeReason) {
                    detailInfo += `
                    <div class="alert alert-warning py-2 px-2 mb-1 mt-2">
                        <small><strong>⚠️ Perhatian:</strong> ${result.details.wrongTypeReason}</small>
                    </div>
                    `;
                }
                
                detailInfo += `</div>`;
            }
            else if (result.debug) {
                if (result.debug.matchedParts && result.debug.matchedParts.length > 0) {
                    detailInfo += `
                    <div class="mt-2 small">
                        <div class="text-success mb-1">
                            <i class="bi bi-check-circle me-1"></i>
                            Bagian yang cocok: <strong class="text-uppercase">${result.debug.matchedParts.join(', ')}</strong>
                        </div>
                    </div>
                    `;
                }
                if (result.debug.missingParts && result.debug.missingParts.length > 0) {
                    detailInfo += `
                    <div class="mt-1 small">
                        <div class="text-danger">
                            <i class="bi bi-x-circle me-1"></i>
                            Bagian tidak cocok: ${result.debug.missingParts.join(', ')}
                        </div>
                    </div>
                    `;
                }
            }
            
            return `
            <div class="list-group-item validation-field-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">
                            <i class="bi ${iconClass} me-2"></i>${result.label}
                        </h6>
                        <small class="text-muted">${result.value || '-'}</small>
                    </div>
                    <div class="text-end">
                        <div class="percentage ${this.getPercentageColor(percentage)}">${percentage}%</div>
                        ${statusBadge}
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar ${this.getScoreColor(percentage)}" role="progressbar" style="width: ${percentage}%"
                        aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                ${detailInfo}
            </div>
            `;
        }

        calculateOverallScore() {
            const results = Object.entries(this.results)
                .filter(([key]) => !key.startsWith('_'))
                .map(([, r]) => r);
            if (results.length === 0) return 0;
            const totalPercentage = results.reduce((sum, r) => sum + (r.percentage || 0), 0);
            return Math.round(totalPercentage / results.length);
        }
            
            getFieldLabel(field) {
            const labels = {
            'no_lp': 'Nomor LP',
            'tanggal_ketetapan': 'Tanggal Ketetapan',
            'nomor_dokumen': 'Nomor Dokumen',
            'tersangka': 'Tersangka/Terlapor'
            };
            return labels[field] || field;
            }
            
            getScoreColor(percentage) {
            if (percentage >= 80) return 'bg-success';
            if (percentage >= 50) return 'bg-warning';
            return 'bg-danger';
            }
            
            getPercentageColor(percentage) {
            if (percentage >= 80) return 'text-success';
            if (percentage >= 50) return 'text-warning';
            return 'text-danger';
            }
            
            getSummaryBoxClass(percentage) {
            if (percentage >= 80) return 'success';
            if (percentage >= 50) return 'warning';
            return 'danger';
            }
            }

            /**
            * Initialize keyboard shortcuts
            */

            function initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
            // Ignore if user is typing in input/textarea
            const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName);
            if (isTyping) return;
            
            // Ignore if modal is open (except validation modal)
            const hasOpenModal = document.querySelector('.modal.show:not(#validationResultModal)');
            if (hasOpenModal) return;
            
            // Ignore if Swal is open
            if (document.querySelector('.swal2-container')) return;
            
            // Q key - Trigger Cek Validasi
            if (e.key.toLowerCase() === 'q') {
            e.preventDefault();
            const checkBtn = document.getElementById('checkValidationBtn');
            if (checkBtn) {
            checkBtn.click();
            // Visual feedback
            checkBtn.classList.add('active');
            setTimeout(() => checkBtn.classList.remove('active'), 200);
            }
            }
            
            // ESC key - Close validation modal if open
            if (e.key === 'Escape') {
            const validationModal = document.getElementById('validationResultModal');
            const modalInstance = bootstrap.Modal.getInstance(validationModal);
            if (modalInstance && document.body.classList.contains('modal-open')) {
            modalInstance.hide();
            }
            }
            });
            }

/**
    * ========================================
* UTILITY FUNCTIONS
    * ========================================
    */
    function closeCurrentTab() {
    setTimeout(() => {
window.close();
setTimeout(() => {
            window.open('about:blank', '_self');
            window.close();
            }, 100);
            }, APP_CONFIG.timing.successDelay);
            }

function submitFormAjax(form, loadingMessage = 'Memproses...') {
    const formData = new FormData(form);
    const url = form.action;
    const method = form.method || 'POST';

Swal.fire({
        title: loadingMessage,
        html: 'Mohon tunggu sebentar...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
        Swal.showLoading();
        }
        });

fetch(url, {
        method: method,
        body: formData,
        headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        }
        })
        .then(response => response.json())
        .then(data => {
        if (data.success) {
Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                showConfirmButton: false,
                timer: APP_CONFIG.timing.successDelay,
                timerProgressBar: true,
                allowOutsideClick: false
                }).then(() => {
if (data.action === 'close_tab') {
                    closeCurrentTab();
                    } else if (data.action === 'redirect' && data.url) {
                    window.location.href = data.url;
} else {
                    window.location.href = APP_CONFIG.routes.index;
}
                    });
                    } else {
Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Terjadi kesalahan',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
});
                }
                })
                .catch(error => {
console.error('AJAX Error:', error);
            Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Tidak dapat memproses permintaan',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
            });
            });
            }

/**
    * ========================================
* FORM HANDLERS
    * ========================================
    */
function initApproveForm() {
    const approveForms = document.querySelectorAll('form.js-approve');
    
    approveForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Guard: Check Validation Score before approving
            let score = 0;
            if (window.validationController && window.validationController.results && Object.keys(window.validationController.results).length > 0) {
                score = window.validationController.calculateOverallScore();
            }

            if (score > 0 && score < 70) {
                Swal.fire({
                    icon: 'warning',
                    title: `Skor Validasi Rendah (${score}%)`,
                    html: `
                        <div class="alert alert-warning text-start small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Banyak data yang tidak cocok dengan dokumen PDF.
                        </div>
                        <p class="mb-2">Untuk tetap menyetujui, ketik <strong>SETUJUI</strong> di bawah ini:</p>
                    `,
                    input: 'text',
                    inputPlaceholder: 'Ketik SETUJUI',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Tetap Setujui',
                    cancelButtonText: '<i class="bi bi-x-circle me-1"></i> Batal',
                    confirmButtonColor: '#dc3545', // Use red color to emphasize danger
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    preConfirm: (value) => {
                        if (value !== 'SETUJUI') {
                            Swal.showValidationMessage('Anda harus mengetik SETUJUI (huruf besar) untuk melanjutkan');
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        submitFormAjax(form, 'Menyetujui dokumen secara paksa...');
                    }
                });
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Setujui Selra ini?',
                    text: 'Pastikan data sudah benar.',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Setujui',
                    cancelButtonText: '<i class="bi bi-x-circle me-1"></i> Batal',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        submitFormAjax(form, 'Menyetujui dokumen...');
                    }
                });
            }
        });
    });
}

function initRejectForm() {
    const rejectForms = document.querySelectorAll('form.js-reject');

const rejectReasons = [
        'Surat Ketetapan yang diupload tidak sesuai',
        'Judul Surat Ketetapan berbeda dengan SELRA yang dipilih',
        'No Dokumen Surat Ketetapan tidak sesuai dengan Dokumen Upload',
        'Nomor LP tidak sesuai / tidak ada pada Dokumen yang di Upload',
        'Nama tersangka/terlapor tidak sesuai',
        'Tanggal ketetapan tidak sesuai / tidak ada pada Dokumen yang di Upload',
        'Belum ada tanda tangan pada Dokumen yang di Upload',
        'Belum ada stempel pada Dokumen yang di Upload',
        'Dokumen yang di Upload masih mengadung kata Penyidikan',
        'Dokumen yang di Upload masih mengadung kata Penyelidikan',
        ];

    rejectForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Auto-Map Reject Reasons based on Validation Results
            const autoCheckReasons = [];
            if (window.validationController && window.validationController.results) {
                const results = window.validationController.results;
                if (results['no_lp'] && !results['no_lp'].found) {
                    autoCheckReasons.push('Nomor LP tidak sesuai / tidak ada pada Dokumen yang di Upload');
                }
                if (results['nomor_dokumen'] && !results['nomor_dokumen'].found) {
                    autoCheckReasons.push('No Dokumen Surat Ketetapan tidak sesuai dengan Dokumen Upload');
                }
                if (results['tersangka'] && !results['tersangka'].found) {
                    autoCheckReasons.push('Nama tersangka/terlapor tidak sesuai');
                }
                if (results['tanggal_ketetapan'] && !results['tanggal_ketetapan'].found) {
                    autoCheckReasons.push('Tanggal ketetapan tidak sesuai / tidak ada pada Dokumen yang di Upload');
                }
                if (results['jenis_selra'] && !results['jenis_selra'].found) {
                    autoCheckReasons.push('Judul Surat Ketetapan berbeda dengan SELRA yang dipilih');
                }
            }

            const checkboxList = rejectReasons.map((reason, idx) => {
                const isChecked = autoCheckReasons.includes(reason) ? 'checked' : '';
                return `
                <div class="form-check mb-2">
                    <input class="form-check-input my-auto reject-reason-checkbox" type="checkbox" value="${reason}"
                        id="reject-reason-${idx}" ${isChecked}>
                    <label class="form-check-label ${isChecked ? 'fw-bold text-danger' : ''}" for="reject-reason-${idx}">
                        ${reason} ${isChecked ? '<i class="bi bi-robot text-muted ms-1" title="Tercentang otomatis dari hasil validasi"></i>' : ''}
                    </label>
                </div>
                `;
            }).join('');

Swal.fire({
                icon: 'warning',
title: 'Kembalikan Dokumen',
                    html: `
<div class="text-start" style="max-height: 50vh; overflow-y: auto; padding-right: 8px;">
                            <div class="alert alert-warning mb-1">
                                <small class="text-muted">
<i class="bi bi-exclamation-triangle me-1"></i>
LP akan di-set menjadi <strong>Dalam Proses</strong> dan data Selra akan <strong>dihapus</strong>.
                                    </small>
                                    </div>
                                    <hr class="my-3">
<p class="mb-3 fw-semibold">Pilih alasan pengembalian dokumen:</p>
                            <div class="border rounded p-3 mb-3 small" style="max-height: 200px; overflow-y: auto; background:#f8f9fa;">
                                ${checkboxList}
                            </div>
<hr class="my-3">
                            <label for="custom-reject-reason" class="form-label fw-semibold">
                                Alasan lainnya (opsional):
                            </label>
<textarea id="custom-reject-reason" class="form-control" rows="3"
                                placeholder="Tulis alasan tambahan jika diperlukan..."></textarea>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle"></i> Minimal pilih 1 alasan atau tulis alasan lain.
                            </small>
                            </div>
                            `,
width: '650px',
showCancelButton: true,
confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan',
                    cancelButtonText: '<i class="bi bi-x-circle me-1"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                    popup: 'swal-no-overflow',
                    htmlContainer: 'swal-scrollable-content'
                    },
                    preConfirm: () => {
const selected = Array.from(document.querySelectorAll('.reject-reason-checkbox:checked'))
                        .map(cb => cb.value);
const custom = document.getElementById('custom-reject-reason')?.value.trim() || '';

if (selected.length === 0 && !custom) {
                        Swal.showValidationMessage(`
                        <div class="text-danger small">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Harap pilih <strong>setidaknya satu alasan</strong> atau tulis alasan lain.
                        </div>
                        `);
                        return false;
                        }

return { reasons: selected, custom: custom };
                        },
                        didOpen: () => {
                        const firstCheckbox = document.getElementById('reject-reason-0');
                        if (firstCheckbox) firstCheckbox.focus();
                        }
                        }).then(result => {
                        if (result.isConfirmed) {
const { reasons, custom } = result.value;
                        let allReasons = [...reasons];
                        if (custom) allReasons.push(custom);
                        
                        const formattedReasons = allReasons.map((reason, index) => `${index + 1}. ${reason}`).join('\n');

form.querySelectorAll('input[name^="reject_reasons"], input[name="custom_reason"]')
                        .forEach(el => el.remove());

reasons.forEach(reason => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'reject_reasons[]';
                        input.value = reason;
                        form.appendChild(input);
                        });

if (custom) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'custom_reason';
                        input.value = custom;
                        form.appendChild(input);
                        }

const reasonInput = form.querySelector('input[name="reject_reason"]');
                        if (reasonInput) reasonInput.value = formattedReasons;
                        
                        submitFormAjax(form, 'Mengembalikan dokumen...');
                        }
                        });
                        });
                        });
}
    
    function initFullscreenToggle() {
    const preview = document.getElementById('pdfPreview');
    const toggleBtn = document.querySelector('.js-toggle-full');
    
    if (!preview || !toggleBtn) {
    return;
    }
    
    function toggleFullscreen() {
    const isFullscreen = preview.classList.toggle('fullscreen');
    document.body.classList.toggle('no-scroll', isFullscreen);
    toggleBtn.innerHTML = isFullscreen ?
    '<i class="bi bi-fullscreen-exit"></i> Kecilkan' :
    '<i class="bi bi-arrows-fullscreen"></i> Perbesar';
    }
    
    toggleBtn.addEventListener('click', toggleFullscreen);
    
    document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && preview.classList.contains('fullscreen')) {
    toggleFullscreen();
    }
    });
    }
    
    /**
    * Calculate similarity between two strings (0-100%)
    * Using Dice Coefficient algorithm
    */
    function calculateSimilarity(str1, str2) {
    if (!str1 || !str2) return 0;
    if (str1 === str2) return 100;
    
    const len1 = str1.length;
    const len2 = str2.length;
    
    if (Math.abs(len1 - len2) > Math.max(len1, len2) * 0.5) {
    return 0;
    }
    
    const bigrams1 = new Set();
    const bigrams2 = new Set();
    
    for (let i = 0; i < len1 - 1; i++) { bigrams1.add(str1.substring(i, i + 2)); } for (let i=0; i < len2 - 1; i++) {
        bigrams2.add(str2.substring(i, i + 2)); } let intersection=0; bigrams1.forEach(bigram=> {
        if (bigrams2.has(bigram)) {
        intersection++;
        }
        });
    
        const similarity = (2 * intersection) / (bigrams1.size + bigrams2.size);
        return Math.round(similarity * 100);
        }

/**
    * ========================================
* INITIALIZE APPLICATION
    * ========================================
    */
let validationController;
document.addEventListener('DOMContentLoaded', function() {
validationController = new ValidationController();
        window.validationController = validationController;
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
        new bootstrap.Tooltip(element);
        });
initApproveForm();
initRejectForm();
initFullscreenToggle();
initKeyboardShortcuts();
});
</script>
@endpush