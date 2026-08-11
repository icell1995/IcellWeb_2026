<div id="myModalPenyegelan1" name="myModalPenyegelan1" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan Upload Surat permintaan persetujuan Presiden, Mendagri,
                    Jaksa Agung, Gubernur, Majelis Pengawas Daerah (Notaris) untuk
                    melakukan pemanggilan/pemeriksaan terhadap pejabat tertentu; (jika
                    ada, sesuai perkaranya)</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_persetujuan_penyegelan"
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

<div id="myModalPenyegelan2" name="myModalPenyegelan2" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Masukan Petugas Untuk Perintah Penyegelan</h5>
            </div>

            <form action="{{ route('add_surat_perintah_penyegelan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_surat_penyegelan" name="accident_id_surat_penyegelan" type="text"
                        value="{{ $id }}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_penyegelan"
                        id="add_officer_penyegelan">Tambah Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="add_surat_penyegelan" style="display: table">

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

<div class="modal fade" id="myEditModalPenyegelan2" name="myEditModalPenyegelan2" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Perintah Surat Penyegelan</h5>
            </div>

            <form action="{{ route('edit_surat_penyegelan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_surat_penyegelan" name="accident_id_edit_surat_penyegelan"
                        type="text" value="{{ $id }}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_edit_penyegelan"
                        id="add_officer_edit_penyegelan">Tambah Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="edit_surat_penyegelan" style="display: table">
                        @foreach ($surat_penyegelan as $penyegelan)
                            <div class="add-row row">
                                <div class="input-group col-lg-11">
                                    <select id="edit_officer_surat_penyegelan[]" name="edit_officer_surat_penyegelan[]"
                                        class="form-control">
                                        <option value="">Pilih Petugas</option>
                                        @foreach ($officer as $officers)
                                            <option value="{{ $officers->id }}"
                                                {{ $officers->id == $penyegelan->id ? 'selected' : '' }}>
                                                {{ $officers->id }} - {{ $officers->first_name }}
                                                {{ $officers->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="remove col-lg-1">
                                    <button type="button" name="remove"
                                        class="btn btn_removeX btn_remove_edit_penyegelan">X</button>
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

<div id="myModalPenyegelan3" name="myModalPenyegelan3" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan Upload Berita Acara Pembungkusan, Penyegelan, dan Pelabelan Barang
                    Bukti</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="berita_acara_penyegelan"
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
