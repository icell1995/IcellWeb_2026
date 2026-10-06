{{-- 1. Laporan Polisi (LP) --}}
@php
    $lpCreator = null;
    if ($laporanPolisi && !empty($laporanPolisi->created_by)) {
        $lpCreator = \App\Models\User::where('username', $laporanPolisi->created_by)
            ->orWhere('register_number', $laporanPolisi->created_by)
            ->first();
    }
@endphp
<tr class="{{ $laporanPolisi ? 'table-light' : 'table-warning' }}" data-pending="0" data-status-id="{{ $laporanPolisi ? '86' : '0' }}" data-step="1">
    <td class="text-center align-middle">
        <h6>{{ $accident->no_lp ?? '-' }}</h6>
        @if($accident)
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}" target="_blank" class="btn btn-sm btn-primary mb-2">
                Visit <i class="bi bi-arrow-up-right-square-fill"></i>
            </a>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-sm btn-danger" disabled>
                    {{ "Accident Date : " . ($accident->accident_date ? Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-') }}
                    {{ "; Report Date : " . ($accident->report_date ? Carbon\Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-') }}
                </button>
                <button type="button" class="btn btn-sm btn-success" disabled>
                    {{ "Satker : " . ($accident->police->full_name ?? '-') }}
                </button>
            </div>
        @endif
    </td>
    <td class="text-center align-middle">
        <div class="fw-bold text-dark">1. Laporan Polisi (LP)</div>
        <span class="badge bg-secondary">Tahap 1</span>
    </td>
    <td class="text-center align-middle">
        <h6>{{ $accident->no_lp ?? '-' }}</h6>
        @if($laporanPolisi)
            <div class="d-grid gap-2 mt-2">
                <a href="{{ url('/laporan_polisi/' . $accident->id) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>{{ $laporanPolisi->name }}
                </a>
            </div>
        @else
            <span class="text-muted"><i class="bi bi-x-circle text-danger me-1"></i>Berkas belum diunggah</span>
        @endif
    </td>
    <td class="text-center align-middle">
        <div class="d-grid gap-2 flex-column">
            @if($lpCreator)
                <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                    {{ App\Helpers\PeopleNameHelper::getFullName($lpCreator->first_title, $lpCreator->first_name, $lpCreator->last_name, $lpCreator->last_title) }}
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                    {{ $lpCreator->register_number ?? $lpCreator->username }}
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                    {{ $lpCreator->rank->name ?? '-' }}
                </button>
            @elseif($laporanPolisi && !empty($laporanPolisi->created_by))
                <button type="button" class="btn btn-sm btn-secondary btn-block" disabled>
                    {{ $laporanPolisi->created_by }}
                </button>
            @else
                -
            @endif
        </div>
    </td>
    <td class="text-center align-middle">
        {{ $laporanPolisi && $laporanPolisi->created_at ? Carbon\Carbon::parse($laporanPolisi->created_at)->locale('id')->translatedFormat('d F Y') : '-' }}
    </td>
    <td class="text-center align-middle">
        @if($laporanPolisi)
            <span class="badge bg-success py-2 px-2">
                <i class="bi bi-check-circle me-1"></i>Sudah Diunggah
            </span>
        @else
            <span class="badge bg-danger py-2 px-2">
                <i class="bi bi-x-circle me-1"></i>Belum Diunggah
            </span>
        @endif
    </td>
    <td class="text-center align-middle">
        @if($laporanPolisi)
            <a href="{{ url('/laporan_polisi/' . $accident->id) }}" target="_blank" class="btn btn-sm btn-info text-white">
                <i class="bi bi-eye"></i> Lihat Berkas
            </a>
        @else
            <button type="button" class="btn btn-sm btn-secondary" disabled title="Dokumen Laporan Polisi (LP) belum diunggah oleh penyidik">
                <i class="bi bi-clock"></i> Belum Diunggah
            </button>
        @endif
    </td>
</tr>

{{-- 2 s/d 7. Dokumen Mindik Terurut --}}
@foreach ($documents as $document)
    @php
        $caseDegreeType = $document->caseDegreeType ?? NULL;
        $isLegacy = $document->is_legacy ?? false;
        $isApproved = in_array((string)$document->status_id, ['86', '85']);
        $isPending = ((string)$document->status_id === '12');
        $canValidate = $document->can_validate ?? true;
        $disabledReason = $document->disabled_reason ?? null;
        $workflowStep = $document->workflow_step ?? 2;
    @endphp
    <tr class="{{ $isLegacy ? 'table-primary' : '' }}" data-pending="{{ $isPending ? '1' : '0' }}" data-status-id="{{ $document->status_id }}" data-step="{{ $workflowStep }}">
        <td class="text-center align-middle">
            <h6>{{ $document->accident->no_lp ?? '' }} </h6>
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $document->accident_id]) }}" target="_blank" class="btn btn-sm btn-primary mb-2">
                Visit <i class="bi bi-arrow-up-right-square-fill"></i>
            </a>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-sm btn-danger" disabled>
                    @if (isset($document->accident))
                        {{ "Accident Date : " . ($document->accident->accident_date ? Carbon\Carbon::parse($document->accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-') }}
                        {{ "; Report Date : " . ($document->accident->report_date ? Carbon\Carbon::parse($document->accident->report_date)->locale('id')->translatedFormat('d F Y') : '-') }}
                    @endif
                </button>
                <button type="button" class="btn btn-sm btn-success" disabled>
                    @if (isset($document->accident->police->full_name))
                        {{ "Satker : " . $document->accident->police->full_name }}
                    @endif
                </button>
            </div>
        </td>
        <td class="text-center align-middle">
            <div class="fw-bold text-dark">{{ $workflowStep }}. {{ $document->documentCategory->name ?? '' }}</div>
            @if(!empty($caseDegreeType)) 
                <small class="text-muted">({{ $caseDegreeType->name ?? '' }})</small>
            @endif

            @if($isLegacy == true) 
                <br/>
                <h5><span class="badge badge-primary">Legacy</span></h5>
            @endif
        </td>
        <td class="text-center align-middle">
            <h6>{{ $document->document_number ?? '' }}</h6>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                    @if (isset($document->document_date))
                        {{ "Document Date : " . Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') }}
                    @endif
                </button>
            </div>
        </td>
        <td class="text-center align-middle">
            <div class="d-grid gap-2 flex-column">
                @php
                    $createdBy = $document->createdByUser ?? NULL;
                @endphp
                @if($createdBy)
                    <button type="button" class="btn btn-sm btn-danger btn-block"
                        disabled>{{ isset($createdBy) ? App\Helpers\PeopleNameHelper::getFullName($createdBy->first_title, $createdBy->first_name, $createdBy->last_name, $createdBy->last_title) : '' }}</button>
                    <button type="button" class="btn btn-sm btn-danger btn-block"
                        disabled>{{ isset($createdBy) ? $createdBy->register_number : '' }}</button>
                    <button type="button" class="btn btn-sm btn-danger btn-block"
                        disabled>{{ isset($createdBy) ? ($createdBy->rank->name ?? '') : '' }}</button>
                @endif
            </div>
        </td>
        <td class="text-center align-middle">
            @if (isset($document->created_at))
                {{ Carbon\Carbon::parse($document->created_at)->locale('id')->translatedFormat('d F Y') }}
            @endif
        </td>
        <td class="text-center align-middle">
            @if ($isApproved)
                <span class="badge bg-success py-2 px-2">
                    <i class="bi bi-check-circle-fill me-1"></i>{{ $document->status->name ?? 'Disetujui' }} ({{ $document->status->id }})
                </span>
            @elseif ($isPending)
                <span class="badge bg-warning text-dark py-2 px-2">
                    <i class="bi bi-hourglass-split me-1"></i>{{ $document->status->name ?? 'Menunggu Validasi' }} ({{ $document->status->id }})
                </span>
            @elseif ($document->status_id == '4')
                <span class="badge bg-danger py-2 px-2">
                    <i class="bi bi-exclamation-octagon me-1"></i>Revisi (4)
                </span>
            @else
                <span class="badge bg-secondary py-2 px-2">
                    {{ ($document->status->name ?? '-') . ' (' . ($document->status->id ?? '-') . ')' }}
                </span>
            @endif
        </td>

        <td class="text-center align-middle">
            @php
                $documentCategory = $document->documentCategory ?? NULL;
                $documentCategoryAltCode = $documentCategory->alt_code ?? NULL;
                $documentValidationUrl = (!empty($documentCategoryAltCode)) ? route('cms.case-document-validation.module.' . $documentCategoryAltCode . '.validation', ['accident_id' => $document->accident->id, 'id' => $document->id, 'document_category_id' => $document->document_category_id]) : '#';
            @endphp

            @if ($isApproved)
                <button type="button" class="btn btn-success btn-sm" disabled>
                    <i class="bi bi-check-circle"></i> Tervalidasi
                </button>
            @elseif ($isPending && $canValidate)
                <a href="{{ $documentValidationUrl }}" target="_blank" rel="opener"
                    class="btn btn-primary btn-validate-doc">
                    <i class="bi bi-eye"></i> Validasi
                </a>
            @elseif ($isPending && !$canValidate)
                <button type="button" class="btn btn-secondary" disabled data-bs-toggle="tooltip" title="{{ $disabledReason }}">
                    <i class="bi bi-lock-fill"></i> Validasi
                </button>
                <small class="text-danger d-block mt-1 fw-bold text-wrap" style="max-width: 190px; font-size: 11px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $disabledReason }}
                </small>
            @else
                <button type="button" class="btn btn-secondary btn-sm" disabled>
                    <i class="bi bi-lock"></i> Belum Siap
                </button>
            @endif
        </td>
    </tr>
@endforeach