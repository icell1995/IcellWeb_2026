<div data-backdrop="false" id="myModalPenyitaan1" name="myModalPenyitaan1" class="modal fade bd-example-modal-lg"
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
                                    <h5>Silahkan masukan file Surat Permintaan Izin / Izin Khusus Penyitaan Kepada Ketua
                                        Pengadilan</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_izin_penyitaan" hidden>
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

<div data-backdrop="false" id="myModalPenyitaan2" name="myModalPenyitaan2" class="modal fade bd-example-modal-lg"
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
                                    <h5>Silahkan masukan file Surat Permintaan Persetujuan Penyitaan Kepada Ketua
                                        Pengadilan</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="surat_persetujuan_penyitaan" hidden>
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

<div data-backdrop="false" id="myModalPenyitaan3" name="myModalPenyitaan3" id="myModal" class="modal fade"
    role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title fw-bold">Tambah Barang Bukti</h5>

            </div>
            <div class="modal-body ">
                <div class="modal-barang-bukti">
                    <form id="barang-bukti-form">
                        @csrf
                        <div class=col-md-12>
                            <div>
                                <input type="hidden" name="barang_bukti_id" id="barang_bukti_id">
                                <input id="accident_id_barang_bukti" name="accident_id_barang_bukti" type="text"
                                    value="{{$id}}" hidden>
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group row">
                                        <label for="nama_barang"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Nama') }}</label>

                                        <div class="col-md-9">
                                            <input id="nama_barang" type="text"
                                                class="form-control @error('nama_barang') is-invalid @enderror"
                                                name="nama_barang" value="{{ old('nama_barang')}}"
                                                autocomplete="nama_barang">
                                            <span class="text-danger error-text name_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="jumlah_barang"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Jumlah Barang') }}</label>

                                        <div class="col-md-9">
                                            <input id="jumlah_barang" type="text"
                                                class="form-control @error('jumlah_barang') is-invalid @enderror"
                                                name="jumlah_barang" value="{{ old('jumlah_barang')}}"
                                                autocomplete="jumlah_barang">
                                            <span class="text-danger error-text gender_err"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="centered">
                            <button type="submit" class="btn btn-primary btn-barang-bukti">Tambah Barang Bukti</button>
                        </div>
                    </form>
                    <table class="table table-bordered barang-bukti-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Barang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                {{-- <div class="alert alert-success alert-block" style="display: none;">
          <button type="button" class="close" data-dismiss="test">×</button>
          <strong class="success-msg"></strong>
      </div>   --}}

            </div>
            <div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="myModalPenyitaan4" name="myModalPenyitaan4" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
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
                                    <h5>Silahkan Masukan Petugas Untuk Perintah Penyitaan</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('add_surat_penyitaan') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="accident_id_surat_penyitaan"
                                                    name="accident_id_surat_penyitaan" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <td><button type="button" name="add_officer_penyitaan"
                                                            id="add_officer_penyitaan" class="btn btn-success">Add
                                                            More</button></td>
                                                </table>
                                                <table class="table table-bordered" id="add_surat_penyitaan">

                                                </table>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" data-backdrop="false" class="btn btn-danger"
                                                data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" class="modal fade" id="myEditModalPenyitaan4" name="myEditModalPenyitaan4" tabindex="-1"
    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Perintah Surat Penyitaan</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('edit_surat_penyitaan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_edit_surat_penyitaan" name="accident_id_edit_surat_penyitaan"
                                type="text" value="{{$id}}" hidden>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td><button type="button" name="add_officer_edit_penyitaan"
                                        id="add_officer_edit_penyitaan" class="btn btn-success">Add More</button></td>
                            </table>
                            <table class="table table-bordered" id="edit_surat_penyitaan">
                                @foreach($surat_penyitaan as $penyitaan)
                                <tr>
                                    <td>
                                        <select id="edit_officer_surat_penyitaan[]"
                                            name="edit_officer_surat_penyitaan[]" class="form-control">
                                            <option value="">Pilih Petugas</option>
                                            @foreach ($officer as $officers )


                                            <option value="{{$officers->id}}"
                                                {{ $officers->id == $penyitaan->id ?'selected' : '' }}>
                                                {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                            </option>

                                            @endforeach
                                    </td>
                                    <td>
                                        <button type="button" name="remove"
                                            class="btn btn-danger btn_remove_officer_edit_surat_penyitaan">X</button>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenyitaan5" name="myModalPenyitaan5" class="modal fade bd-example-modal-lg"
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
                                    <h5>Silahkan masukan file Berita Acara Penyitaan</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_penyitaan" hidden>
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
