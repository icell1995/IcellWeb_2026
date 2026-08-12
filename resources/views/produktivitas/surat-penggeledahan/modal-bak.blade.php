<div data-backdrop="false" id="myModalPenggeledahan1" name="myModalPenggeledahan1"
    class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat permintaan izin/izin khusus penggeledahan kepada ketua pengadilan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_perintah_izin_penggeledahan" hidden>
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

<div data-backdrop="false" id="myModalPenggeledahan2" name="myModalPenggeledahan2"
    class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat perintah penggeledahan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_perintah_penggeledahan" hidden>
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

<div data-backdrop="false" id="myModalPenggeledahan3" name="myModalPenggeledahan3"
    class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Surat permintaan persetujuan penggeledahan kepada ketua pengadilan</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_persetujuan_penggeledahan" hidden>
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

<div data-backdrop="false" id="myModalPenggeledahan4" name="myModalPenggeledahan4"
    class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Berita acara penggeledahan rumah tinggal/tempat tertutup lainnya</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_penggeledahan" hidden>
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
