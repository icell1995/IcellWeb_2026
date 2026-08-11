<form
    action="{{ route('document-action.upload-document.upload.save', [
        'accident_id' => $accidentId,
        'document_id' => $documentId,
        'form_type' => $formType,
        'document_category_id' => $documentCategoryId,
    ]) }}"
    method="POST" enctype="multipart/form-data" id="">
    @csrf
    @method('PUT')
    <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">
    <input type="hidden" name="requiredFileType" id="requiredFileType" value="WORD">

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP :</label>
        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
            <input id="accidentNumber" type="text"
                class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold"
                name="accidentNumber" value="{{ $accident->no_lp }}" required placeholder="" readonly>
            @error('accidentNumber')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="input-group row mb-3 ms-0">
        <label class="fw-bold col-sm-2 col-form-label">File Word :</label>
        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
            <input type="file" class="form-control" id="file" name="file">

            <h6 class="text-muted mt-2">(*Jenis File Dokumen yang dapat diupload : (.DOCX))</h6>

            @error('file')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <hr />

    <div class="d-flex justify-content-center">
        <div class="m-1">
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                class="btn btn-danger btn-lg">
                <i class="bi bi-x-circle"></i> Batal
            </a>
        </div>
        <div class="m-1">
            <button class="btn btn-primary btn-lg" type="submit">
                <i class="bi bi-save"></i> Simpan
            </button>
        </div>
    </div>
</form>
