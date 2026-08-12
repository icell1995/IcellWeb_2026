@foreach ($documents as $document)
    @php
        $caseDegreeType = $document->caseDegreeType ?? NULL;
        $isLegacy = $document->is_legacy ?? false;
    @endphp
    <tr class="{{ $isLegacy ? 'table-primary' : '' }}">
        <td class="text-center align-middle">
            <h6>{{ $document->accident->no_lp ?? '' }} </h6>
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $document->accident_id]) }}" target="_blank" class="btn btn-sm btn-primary mb-2">
                Visit <i class="bi bi-arrow-up-right-square-fill"></i>
            </a>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-sm btn-danger" disabled>
                    @if (isset($document->accident))
                        {{ "Accident Date : " . Carbon\Carbon::parse($document->accident->accident_date)->locale('id')->translatedFormat('d F Y') }}
                        {{ "; Report Date : " . Carbon\Carbon::parse($document->accident->report_date)->locale('id')->translatedFormat('d F Y') }}
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
            {{ $document->documentCategory->name ?? '' }}
            @if(!empty($caseDegreeType)) 
                <br>
                ({{ $caseDegreeType->name ?? '' }})
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
                    //$document->createdBy
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
            @if (isset($document->status->name))
                {{ $document->status->name . ' (' . $document->status->id . ')' }}
            @endif
        </td>

        <td class="text-center align-middle">
            @php
                $documentCategory = $document->documentCategory ?? NULL;
                $documentCategoryAltCode = $documentCategory->alt_code ?? NULL;
                $documentValidationUrl = (!empty($documentCategoryAltCode)) ? route('cms.case-document-validation.module.' . $documentCategoryAltCode . '.validation', ['accident_id' => $document->accident->id, 'id' => $document->id, 'document_category_id' => $document->document_category_id]) : '#';
            @endphp
            <a href="{{ $documentValidationUrl }}" target="_blank"
                class="btn btn-primary">
                <i class="bi bi-eye"></i> Validasi
            </a>
        </td>
    </tr>
@endforeach