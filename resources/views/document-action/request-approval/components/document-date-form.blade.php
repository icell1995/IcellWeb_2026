<form
    action="{{ route('document-action.request-approval.request.save', [
        'accident_id' => $accidentId,
        'document_id' => $documentId,
        'form_type' => $formType,
        'document_category_id' => $documentCategoryId,
    ]) }}"
    method="POST" enctype="multipart/form-data" id="">
    @csrf
    @method('PUT')
    <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">
    <input type="hidden" name="formType" id="formType" value="{{ $formType }}">

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP :</label>
        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
            <input id="accidentNumber" type="text"
                class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold"
                name="accidentNumber" value="{{ $document->accident->no_lp ?? '' }}" required placeholder="" readonly>

            @error('accidentNumber')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-3 col-form-label">Tanggal Ditandatangani Dokumen : </label>
        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
            <input class="form-control" id="documentDate" name="documentDate" placeholder="YYYY-MM-DD"
                autocomplete="off" value="{{ date('Y-m-d', strtotime($document->document_date)) }}"
                data-provide="datepicker">

            @error('documentDate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="d-flex justfy-content-center">
        <div class="m-1">
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                class="btn btn-danger">
                <i class="bi bi-x-circle"></i> Batal
            </a>
        </div>
        <div class="m-1">
            <button class="btn btn-dark-blue" type="submit">
                <i class="bi bi-save"></i> Simpan
            </button>
        </div>
    </div>
</form>
