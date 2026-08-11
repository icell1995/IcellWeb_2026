<div id="myModalSuratPenyelidikan" name="myModalSuratPenyelidikan"
    class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    @csrf
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Masukan Nama Petugas Untuk Penyelidikan</h3>
            </div>

            <form action="{{ route('add_surat_penyelidikan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class=" modal-body"modal-title>
                    <input id="accident_id_surat_penyelidikan" name="accident_id_surat_penyelidikan" type="text"
                        value="{{$id}}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_penyelidikan" id="add_officer_penyelidikan">Tambah
                        Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="add_surat_penyelidikan" style="display: table">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-simpan">
                        {{ __('Simpan') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myEditModalSuratPenyelidikan" name="myEditModalSuratPenyelidikan" tabindex="-1"
    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Perintah Surat Penyelidikan</h3>
            </div>

            <form action="{{ route('edit_surat_penyelidikan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class=" modal-body"modal-title>
                    <input id="accident_id_edit_surat_penyelidikan" name="accident_id_edit_surat_penyelidikan"
                        type="text" value="{{$id}}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_edit_penyelidikan"
                        id="add_officer_edit_penyelidikan">Tambah
                        Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="edit_surat_penyelidikan" style="display: table">
                        @foreach($surat_perintah_penyelidikan as $penyelidikan)
                        <div class="add-row row">
                            <div class="input-group col-lg-11">
                                <select id="edit_officer_surat_penyelidikan[]" name="edit_officer_surat_penyelidikan[]"
                                    class="form-control">
                                    <option value="">Pilih Petugas</option>
                                    @foreach ($officer as $officers )


                                    <option value="{{$officers->id}}"
                                        {{ $officers->id == $penyelidikan->id ?'selected' : '' }}>
                                        {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                    </option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="remove col-lg-1">
                                <button type="button" name="remove"
                                    class="btn btn_removeX btn_remove_edit_penyelidikan">X</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-simpan">
                        {{ __('Simpan') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
