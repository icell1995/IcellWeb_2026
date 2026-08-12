<div class="modal fade" id="myModalSuratPenyidikan" name="myModalSuratPenyidikan" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Masukan Petugas Untuk Penyidikan</h3>
            </div>

            <form action="{{ route('add_surat_penyidikan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_surat_penyidikan" name="accident_id_surat_penyidikan" type="text"
                        value="{{$id}}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_penyidikan"
                        id="add_officer_penyidikan">Tambah
                        Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="add_surat_penyidikan" style="display: table">

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

<div class="modal fade" class="modal fade" id="myEditModalSuratPenyidikan" name="myEditModalSuratPenyidikan"
tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Perintah Surat Tugas</h3>
            </div>

            <form action="{{ route('edit_surat_penyidikan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_surat_penyidikan" name="accident_id_edit_surat_penyidikan" type="text"
                        value="{{$id}}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_edit_penyidikan"
                        id="add_officer_edit_penyidikan">Tambah
                        Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="edit_surat_penyidikan" style="display: table">
                        @foreach($surat_perintah_penyidikan as $penyidikan)
                        <div class="add-row row">
                            <div class="input-group col-lg-11">
                                <select id="edit_officer_surat_penyidikan[]" name="edit_officer_surat_penyidikan[]"
                                    class="form-control">
                                    <option value="">Pilih Petugas</option>
                                    @foreach ($officer as $officers )

                                    <option value="{{$officers->id}}"
                                        {{ $officers->id == $penyidikan->id ?'selected' : '' }}>
                                        {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                    </option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="remove col-lg-1">
                                <button type="button" name="remove"
                                    class="btn btn_removeX btn_remove_edit_penyidikan">X</button>
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
