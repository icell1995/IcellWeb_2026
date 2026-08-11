<div id="myModalSPDPUpload" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">SILAKAN UPLOAD FILE YANG SUDAH DI EDIT DALAM BENTUK PDF</h4>
            </div>

            <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="SPDP-Upload" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{$id}}" hidden>
                        </div>
                    </div>
                    <div class="row">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"  required>
                        </div>
                        <div class="col-lg-2" >
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
