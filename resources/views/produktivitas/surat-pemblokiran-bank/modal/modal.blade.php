<div id="myModalPemblokiranBank1" name="myModalPemblokiranBank1" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Surat Permintaan Pemblokiran Rekening Bank</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_blokir_rekening_bank"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPemblokiranBank2" name="myModalPemblokiranBank2" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Berita Acara Pemblokiran Rekening Bank</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="berita_acara_blokir_rekening_bank" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPemblokiranBank3" name="myModalPemblokiranBank3" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Surat Permintaan Pembukaan Pemblokiran Rekening Bank</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="surat_pembukaan_blokir_rekening_bank" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPemblokiranBank4" name="myModalPemblokiranBank4" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Berita Acara Pembukaan Pemblokiran Rekening Bank</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="berita_acara_pembukaan_blokir_rekening_bank" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
