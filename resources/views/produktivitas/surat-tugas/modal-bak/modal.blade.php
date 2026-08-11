{{-- Perintah Tugas --}}
<div id="myModalSuratTugas" name="myModalSuratTugas" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="dialog-modal modal-lg">
        <div class="modal-content ">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h5>Silahkan Masukan Petugas Untuk Perintah Tugas</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('add_surat_tugas') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="accident_id_surat_tugas" name="accident_id_surat_tugas"
                                                    type="text" value="{{$id}}" hidden>
                                            </div>
                                            {{-- <div class="col-md-10">
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-success">Upload</button>
                                        </div> --}}
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <td><button type="button" name="add_officer" id="add_officer"
                                                            class="btn btn-success">Add More</button></td>
                                                </table>
                                                <table class="table table-bordered" id="add_surat_tugas">

                                                    {{-- <tr>   --}}
                                                    {{-- <td>
                                                        <input type="text" id="officer_id0" name="officer_id[]" placeholder="NRP - Nama Petugas" class="form-control officer_list" />
                                                        <input type="text" id="officer0" name="officer[]" placeholder="NRP - Nama Petugas" class="form-control officer_list" />
                                                        <div id="officer_list0"></div>
                                                    </td>
                                                    <td><input type="text" id='officer_id' name="officer_id" readonly></td> --}}
                                                    {{-- <td><button type="button" name="add_officer" id="add_officer" class="btn btn-success">Add More</button></td>   --}}
                                                    {{-- </tr>   --}}
                                                    {{-- <tr>
                                                        <td>
                                                            <select id="officer[]" name="officer" class="form-control">
                                                            <option value="" }}>Pilih Petugas</option>
                                                    @foreach($officer as $officers)
                                                    <option value="{{$officers->id}}"
                                                        {{ old('officer') == $officers->id ?'selected' : '' }}>
                                                        {{$officers->id}} - {{$officers->first_name}}
                                                        {{$officers->last_name}}</option>
                                                    @endforeach
                                                    </td>
                                                    <td>
                                                        <button type="button" name="remove"
                                                            class="btn btn-danger btn_remove_officer">X</button>
                                                    </td>
                                                    </tr> --}}
                                                </table>
                                            </div>
                                            {{-- <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>

                                                    <th>Officer</th>
                                                    <th><a href="#" class="add_officer">add</a></th>
                                                </tr>
                                            </table>
                                            <table class="table table-bordered" id="add_surat_petugas">
                                                <tr>
                                                    <td>
                                                        <input type="text" name="officer_id[]" class="form-control" hidden>
                                                        <input type="text" name="officer[]" class="form-control" required="" autocomplete="off">
                                                        <div id="test_officer_list"></div>
                                                    </td>
                                                    <td><a href="#" class="btn btn-danger btn_remove_officer" id="test">delete</a></td>
                                                </tr>
                                            </table>
                                        </div> --}}
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

<div class="modal fade" id="myEditModalSuratTugas" name="myEditModalSuratTugas" tabindex="-1"
    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="dialog-modal" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Perintah Surat Tugas</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('edit_surat_tugas') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_edit_surat_tugas" name="accident_id_edit_surat_tugas" type="text"
                                value="{{$id}}" hidden>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td><button type="button" name="add_officer_edit" id="add_officer_edit"
                                        class="btn btn-success">Add More</button></td>
                            </table>
                            <table class="table table-bordered" id="edit_surat_tugas">
                                @foreach($surat_perintah_tugas as $tugas)
                                <tr>
                                    <td>
                                        <select id="edit_officer_surat_tugas[]" name="edit_officer_surat_tugas[]"
                                            class="form-control">
                                            <option value="">Pilih Petugas</option>
                                            @foreach ($officer as $officers )


                                            <option value="{{$officers->id}}"
                                                {{ $officers->id == $tugas->id ?'selected' : '' }}>
                                                {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                            </option>

                                            @endforeach
                                    </td>
                                    <td>
                                        <button type="button" name="remove"
                                            class="btn btn-danger btn_remove_officer_edit_surat_tugas">X</button>
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
{{-- end perintah tugas --}}

{{-- perintah penyelidikan --}}
<div data-backdrop="false" id="myModalSuratPenyelidikan" name="myModalSuratPenyelidikan"
    class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h5>Silahkan Masukan Nama Petugas Untuk Penyelidikan</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('add_surat_penyelidikan') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="accident_id_surat_penyelidikan"
                                                    name="accident_id_surat_penyelidikan" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <td><button type="button" name="add_officer_penyelidikan"
                                                            id="add_officer_penyelidikan" class="btn btn-success">Add
                                                            More</button></td>
                                                </table>
                                                <table class="table table-bordered" id="add_surat_penyelidikan">

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

<div data-backdrop="false" class="modal fade" id="myEditModalSuratPenyelidikan" name="myEditModalSuratPenyelidikan"
    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Perintah Surat Penyelidikan</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('edit_surat_penyelidikan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_edit_surat_penyelidikan" name="accident_id_edit_surat_penyelidikan"
                                type="text" value="{{$id}}" hidden>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td><button type="button" name="add_officer_edit_penyelidikan"
                                        id="add_officer_edit_penyelidikan" class="btn btn-success">Add More</button>
                                </td>
                            </table>
                            <table class="table table-bordered" id="edit_surat_penyelidikan">
                                @foreach($surat_perintah_penyelidikan as $penyelidikan)
                                <tr>
                                    <td>
                                        <select id="edit_officer_surat_penyelidikan[]"
                                            name="edit_officer_surat_penyelidikan[]" class="form-control">
                                            <option value="">Pilih Petugas</option>
                                            @foreach ($officer as $officers )


                                            <option value="{{$officers->id}}"
                                                {{ $officers->id == $penyelidikan->id ?'selected' : '' }}>
                                                {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                            </option>

                                            @endforeach
                                    </td>
                                    <td>
                                        <button type="button" name="remove"
                                            class="btn btn-danger btn_remove_officer_edit_penyelidikan">X</button>
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
{{-- end perintah penyelidikan --}}

{{-- perintah penyidikan --}}
<div data-backdrop="false" class="modal fade" id="myModalSuratPenyidikan" name="myModalSuratPenyidikan" tabindex="-1"
    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Masukan Nama Petugas Untuk Penyidikan</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('add_surat_penyidikan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_surat_penyidikan" name="accident_id_surat_penyidikan" type="text"
                                value="{{$id}}" hidden>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td><button type="button" name="add_officer_penyidikan" id="add_officer_penyidikan"
                                        class="btn btn-success">Add More</button></td>
                            </table>
                            <table class="table table-bordered" id="add_surat_penyidikan">

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

<div data-backdrop="false" class="modal fade" id="myEditModalSuratPenyidikan" name="myEditModalSuratPenyidikan"
    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Perintah Surat Penyidikan</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('edit_surat_penyidikan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_edit_surat_penyidikan" name="accident_id_edit_surat_penyidikan"
                                type="text" value="{{$id}}" hidden>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <td><button type="button" name="add_officer_edit_penyidikan"
                                        id="add_officer_edit_penyidikan" class="btn btn-success">Add More</button></td>
                            </table>
                            <table class="table table-bordered" id="edit_surat_penyidikan">
                                @foreach($surat_perintah_penyidikan as $penyidikan)
                                <tr>
                                    <td>
                                        <select id="edit_officer_surat_penyidikan[]"
                                            name="edit_officer_surat_penyidikan[]" class="form-control">
                                            <option value="">Pilih Petugas</option>
                                            @foreach ($officer as $officers )


                                            <option value="{{$officers->id}}"
                                                {{ $officers->id == $penyidikan->id ?'selected' : '' }}>
                                                {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                            </option>

                                            @endforeach
                                    </td>
                                    <td>
                                        <button type="button" name="remove"
                                            class="btn btn-danger btn_remove_officer_edit_penyidikan">X</button>
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
{{-- end perintah penyidikan --}}

{{-- pemberitahuan penyidikan --}}
<div data-backdrop="false" class="modal fade" id="myModalSuratSpdp" name="myModalSuratSpdp" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Masukan Klasifikasi dan Lampiran untuk SPDP</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('add_surat_spdp') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_spdp" name="accident_id_spdp" type="text" value="{{$id}}" hidden>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Klasifikasi" class="col-md-4 col-form-label text-md-right">{{ __('Klasifikasi') }}
                        </label>

                        <div class="col-md-6">
                            <input id="klasifikasi" type="text"
                                class="form-control @error('klasifikasi') is-invalid @enderror" name="klasifikasi"
                                value="{{ old('klasifikasi') }}" required autocomplete="klasifikasi" autofocus>

                            @error('klasifikasi')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="Lampiran" class="col-md-4 col-form-label text-md-right">{{ __('Lampiran') }}</label>

                        <div class="col-md-6">
                            <input id="lampiran" type="text"
                                class="form-control @error('lampiran') is-invalid @enderror" name="lampiran"
                                value="{{ old('lampiran') }}" required autocomplete="lampiran" autofocus>

                            @error('lampiran')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
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

<div data-backdrop="false" class="modal fade" id="myEditModalSuratSPDP" name="myEditModalSuratSPDP" tabindex="-1"
    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Klasifikasi dan Lampiran untuk SPDP</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('edit_surat_spdp') }}" method="POST" enctype="multipart/form-data">
                    @foreach ($surat_spdp as $spdp )
                    @csrf
                    <div class="row">
                        <div>
                            <input id="accident_id_edit_spdp" name="accident_id_edit_spdp" type="text" value="{{$id}}"
                                hidden>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Klasifikasi"
                            class="col-md-4 col-form-label text-md-right">{{ __('Klasifikasi') }}</label>

                        <div class="col-md-6">
                            <input id="edit_klasifikasi" type="text"
                                class="form-control @error('klasifikasi') is-invalid @enderror" name="klasifikasi"
                                value="{{$spdp->klasifikasi}}" required autocomplete="klasifikasi" autofocus>

                            @error('klasifikasi')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="Lampiran" class="col-md-4 col-form-label text-md-right">{{ __('Lampiran') }}</label>

                        <div class="col-md-6">
                            <input id="edit_lampiran" type="text"
                                class="form-control @error('lampiran') is-invalid @enderror" name="lampiran"
                                value="{{ $spdp->lampiran}}" required autocomplete="lampiran" autofocus>

                            @error('lampiran')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Submit</button>
                    </div>
                    @endforeach
                </form>
            </div>

        </div>
    </div>
</div>

{{-- end pemberitahuan penyidikan --}}
