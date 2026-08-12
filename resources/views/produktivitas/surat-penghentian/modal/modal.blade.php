<div id="myModalPenghentian1" name="myModalPenghentian1" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penghentian Penyelidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_perintah_penyelidikan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

{{-- <div id="myModalPenghentian1" name="myModalPenghentian1" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penghentian Penyelidikan</h5>
            </div>
            <form action="">
                @csrf
                <div class="modal-body">
                </div>
            </form>
        </div>
    </div>
</div> --}}

<div id="myModalPenghentian2" name="myModalPenghentian2" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Ketetapan Penghentian Penyelidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_ketetapan_penyelidikan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian3" name="myModalPenghentian3" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Pencabutan Penghentian Penyelidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="surat_pencabutan_penyelidikan" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian4" name="myModalPenghentian4" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penyelidikan Lanjutan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_penyelidikan_lanjutan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian5" name="myModalPenghentian5" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Berita Acara Penghentian Penyelidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="berita_penghentian_penyelidikan" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian6" name="myModalPenghentian6" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Persetujuan/Disposisi/Arahan Pejabat Yang Berwenang</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="persetujuan_pejabat_berwenang" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

{{-- <div id="myModalPenghentian7" name="myModalPenghentian7" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penghentian Penyidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_perintah_penyidikan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{$id}}" hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"  required>
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
</div> --}}


<div id="myModalPenghentian7" name="myModalPenghentian7" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penghentian Penyidikan</h5>
            </div>

            <form action="{{ route('add_sp3') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_sp3" name="accident_id_sp3" type="text" value="{{ $id }}"
                        hidden>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">No LP</span>
                                <input type="text" class="form-control" id="accident_no_lp_sp3"
                                    name="accident_no_lp_sp3" value="{{ $no_lp }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">No SPPP</span>
                                <input id="no_sp3" class="form-control" type="text" name="no_sp3"
                                    value="{{ old('no_sp3') }}" required autocomplete="no_sp3" autofocus
                                    placeholder="contoh: SPPP/.../.../...">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">No Surat Perintah Penyidikan </span>
                                <input id="no_surat_perintah_penyidikan" type="text"
                                    class="form-control"
                                    name="no_surat_perintah_penyidikan"
                                    value="{{ old('no_surat_perintah_penyidikan') }}" required
                                    autocomplete="no_surat_perintah_penyidikan" autofocus
                                    placeholder="contoh: Sp.Dik/.../.../.../.../...">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">Tanggal Sp.dik</span>
                                <input class="form-control datepickers" type="text" id="tanggal_sp_dik"
                                    name="tanggal_sp_dik" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker">
                            </div>
                        </div>
                        {{-- <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">No SPRINDIK</span>
                                <input id="no_sprindik" type="text" name="no_sprindik"
                                    class="form-control"
                                    value="{{ old('no_sprindik') }}" required autocomplete="no_sprindik" autofocus
                                    placeholder="contoh: sprin dik /.../...">
                            </div>
                        </div> --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">No SPDP</span>
                                <input id="no_spdp" type="text"
                                    class="form-control"
                                    value="{{$value_spdp}}" required autocomplete="no_spdp" autofocus readonly
                                    placeholder="Silahkan Upload SPDP">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">Penerima Surat</span>
                                <input id="penerima_surat" type="text"
                                    class="form-control"
                                    name="penerima_surat" value="{{ old('penerima_surat') }}" required
                                    autocomplete="penerima_surat" autofocus placeholder="contoh: KEJAKSAAN NEGRI ...">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">Klasifikasi</span>
                                <input id="klasifikasi" type="text"
                                    class="form-control" name="klasifikasi"
                                    value="{{ old('klasifikasi') }}" required autocomplete="klasifikasi" autofocus
                                    placeholder="Klasifikasi">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">Tanggal Berlaku</span>
                                <input class="form-control datepickers" type="text" id="tanggal_berlaku"
                                    name="tanggal_berlaku" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">Alasan Diberhentikan</span>
                                <input id="alasan" type="text" class="form-control" name="alasan"
                                    value="{{ old('alasan') }}" required autocomplete="alasan" autofocus
                                    placeholder="Alasan Diberhentikan">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <span class="fw-bold">Lampiran</span>
                                <textarea id="lampiran" type="text" class="form-control" name="lampiran" value="{{ old('lampiran') }}"
                                    required autocomplete="lampiran" autofocus placeholder="Alasan Diberhentikan"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-simpan btn-dark-blue">
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

<div id="myEditModalSuratSP3" name="myEditModalSuratSP3" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Surat Perintah Penghentian Penyidikan</h5>
            </div>

            <form action="{{ route('edit_sp3') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_sp3" name="accident_id_sp3" type="text" value="{{ $id }}"
                        hidden>
                    @foreach ($surat_penghentian_penyidikan as $sp3)
                    <input type="text" value="{{$sp3->id}}" name="id_sp3" hidden>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No LP</span>
                                    <input type="text" class="form-control" id="accident_no_lp_sp3"
                                    name="accident_no_lp_sp3" value="{{ $no_lp }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No SPPP</span>
                                    <input id="no_sp3" class="form-control" type="text" name="no_sp3"
                                    value="{{$sp3->no_sp3}}" required autocomplete="no_sp3" autofocus
                                    placeholder="contoh: SPPP/.../.../...">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No Surat Perintah Penyidikan </span>
                                    <input id="no_surat_perintah_penyidikan" type="text"
                                    class="form-control "
                                    name="no_surat_perintah_penyidikan"
                                    value="{{$sp3->no_surat_perintah_penyidikan}}" required
                                    autocomplete="no_surat_perintah_penyidikan" autofocus
                                    placeholder="contoh: Sp.Dik/.../.../.../.../...">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">Tanggal Sp.dik</span>
                                    <input class="form-control datepicker1" type="text" id="tanggal_sp_dik"
                                    name="tanggal_sp_dik" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker" value="{{Carbon\Carbon::parse($sp3->tanggal_sp_dik)->format('d-m-Y')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No SPRINDIK</span>
                                    <input id="no_sprindik" type="text"
                                    class="form-control" name="no_sprindik"
                                    value="{{$sp3->no_sprindik ?? null}}" required autocomplete="no_sprindik" autofocus
                                    placeholder="contoh: sprin dik /.../...">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No SPDP</span>
                                    <input id="no_spdp" type="text"
                                    class="form-control"
                                    value="{{ $no_spdp }}" required autocomplete="no_spdp" autofocus readonly
                                    placeholder="Silahkan Upload SPDP">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">Penerima Surat</span>
                                    <input type="text" class="form-control" value="{{$sp3->penerima_surat}}" required
                                        autocomplete="penerima_surat" autofocus name="penerima_surat" id="penerima_surat"
                                        placeholder="contoh: KEJAKSAAN NEGRI ...">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">Klasifikasi</span>
                                    <input id="klasifikasi" type="text" class="form-control"
                                        name="klasifikasi" value="{{$sp3->klasifikasi}}" required
                                        autocomplete="klasifikasi" autofocus placeholder="Klasifikasi">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">Tanggal Berlaku</span>
                                    <input class="form-control datepicker2" type="text" id="tanggal_berlaku"
                                        name="tanggal_berlaku" placeholder="dd/mm/yyyy" autocomplete="off" value="{{Carbon\Carbon::parse($sp3->tanggal_berlaku)->format('d-m-Y')}}"
                                        data-provide="datepicker">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">Alasan Diberhentikan</span>
                                    <input id="alasan" type="text" class="form-control" name="alasan"
                                        value="{{$sp3->alasan}}" required autocomplete="alasan" autofocus
                                        placeholder="Alasan Diberhentikan">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">Lampiran</span>
                                    <textarea id="lampiran" type="text" class="form-control" name="lampiran"
                                        required autocomplete="lampiran" autofocus placeholder="Lampiran">{{$sp3->lampiran}}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-simpan btn-dark-blue">
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

<div id="myEditModalSuratSP" name="myEditModalSuratSP" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Surat Perintah Penghentian Penyelidikan</h5>
            </div>

            <form action="{{ route('edit_sp3') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_sp3" name="accident_id_edit_sp3" type="text"
                        value="{{ $id }}" hidden>
                    @foreach ($surat_penghentian_penyidikan as $sp3)
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No LP</span>
                                    <input type="text" class="form-control" value="{{ $no_lp }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold">No SPPP</span>
                                    <input type="text" class="form-control"
                                        value="{{ $sp3->no_surat_perintah_penyidikan }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="fw-bold"></span>
                                </div>
                            </div>
                        </div>
                        <div class="sub-field" style="padding: 0px">
                            <div class="input-group col-md-12 col-sm-12" style="padding: 0px">
                                <input id="edit-no-surat-penghentian-penyidikan" type="text"
                                    class="form-control @error('no_surat_perintah_penyidikan') is-invalid @enderror"
                                    name="no_surat_perintah_penyidikan"
                                    value="{{ $sp3->no_surat_perintah_penyidikan }}" required
                                    autocomplete="no_surat_perintah_penyidikan" autofocus
                                    placeholder="contoh: Sp.Dik/123/XX/2000/LL/GNK">

                                @error('klasifikasi')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-simpan btn-dark-blue">
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

<div id="myModalPenghentian8" name="myModalPenghentian8" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Ketetapan Penghentian Penyidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_ketetapan_penyidikan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian9" name="myModalPenghentian9" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Putusan Pra Peradilan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="putusan_pra_peradilan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian10" name="myModalPenghentian10" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Ketetapan Pencabutan Penghentian Penyidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pencabutan_penyidikan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian11" name="myModalPenghentian11" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penyidikan Lanjutan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_penyidikan_lanjutan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian12" name="myModalPenghentian12" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Berita Acara Penghentian Penyidikan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="berita_penghentian_penyidikan" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian13" name="myModalPenghentian13" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Pernyataan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pernyataan" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian14" name="myModalPenghentian14" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Kesepakatan Perdamaian</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_kesepakatan_perdamaian"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<div id="myModalPenghentian15" name="myModalPenghentian15" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Upload Surat Ketetapan</h5>
            </div>
            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="upload_surat_ketetapan"
                                hidden>
                            <input type="text" id='update_selra' name='update_selra' hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control"
                                required>
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

<script type="text/javascript">
    $('#tanggal_berlaku').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myModalPenghentian7'
    });
    $('.datepicker2').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myEditModalSuratSP3'
    });
    $('#tanggal_sp_dik').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myModalPenghentian7'
    });
    $('.datepicker1').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myEditModalSuratSP3'
    });
</script>
