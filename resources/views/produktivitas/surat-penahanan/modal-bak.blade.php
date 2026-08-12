<div data-backdrop="false" id="myModalPenahanan4" name="myModalPenahanan4" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat perintah perpanjangan penahanan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_perintah_perpanjangan_penahanan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenahanan5" name="myModalPenahanan5" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Berita Acara Pengeluaran Penahanan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_pengeluaran_penahanan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenahanan6" name="myModalPenahanan6" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat Pembatalan Penahanan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_pembatalan_penahanan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModalPenahanan7" name="myModalPenahanan7" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat Perintah Pencabutan Pembatalan Penahanan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_pencabutan_pembatalan_penahanan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenahanan8" name="myModalPenahanan8" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Berita Acara Pencabutan Pembatalan Penahanan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_pencabutan_pembatalan_penahanan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenahanan9" name="myModalPenahanan9" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat Perintah Penahanan Lanjutan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_perintah_penahanan_lanjutan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenahanan10" name="myModalPenahanan10" class="modal fade bd-example-modal-lg"
    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Berita Acara Penahanan Lanjutan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_penahanan_lanjutan" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
