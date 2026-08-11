<div data-backdrop="false" id="myModalDpo1" name="myModalDpo1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah DPO</h5>
            </div>
            <div class="modal-body">
                <div class="modal-dpo">
                    <form id="dpo-form">
                        @csrf
                        <div class=col-12>
                            <div>
                                <input type="hidden" name="dpo_id" id="dpo_id">
                                <input id="accident_id_dpo" name="accident_id_dpo" type="text" value="{{ $id }}"
                                    hidden>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label for="name_dpo"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Nama') }}</label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="name_dpo" type="text"
                                                class="form-control @error('name_dpo') is-invalid @enderror"
                                                name="name_dpo" value="{{ old('name_dpo') }}" autocomplete="name_dpo">
                                            <span class="text-danger error-text name_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="gender_dpo"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Jenis Kelamin') }}</label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <select name="gender_dpo" id="gender_dpo" class="form-select">
                                                <option value="G0101">Pilih Jenis Kelamin</option>
                                                @foreach ($gender as $genders)
                                                    <option value="{{ $genders->id }}">
                                                        {{ old('gender') == $genders->id ? 'selected' : '' }}
                                                        {{ $genders->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- <input id="gender_dpo" type="text"
                                                class="form-control @error('gender_dpo') is-invalid @enderror"
                                                name="gender_dpo" value="{{ old('gender_dpo') }}"
                                                autocomplete="gender_dpo"> --}}
                                            <span class="text-danger error-text gender_err"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label for="deskripsi_dpo"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Ciri Khusus') }}</label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            {{-- <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address')}}" required autocomplete="address"> --}}
                                            <textarea class="form-control" name="deskripsi_dpo" id="deskripsi_dpo"
                                                placeholder="Ciri-ciri DPO"></textarea>
                                            <span class="text-danger error-text address_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <div class="form-check" id="dpo_tangkap" name="dpo_tangkap" style="">
                                                <input class="form-check-input" type="checkbox" value="0" id="dpo_tangkaps" name="dpo_tangkaps"><label>
                                                    Sudah Tertangkap</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-2">
                            <button type="submit" class="btn btn-dark-blue btn-dpo">Simpan</button>
                        </div>
                    </form>
                    <table class="table table-bordered dpo-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Ciri Khusus</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                {{-- <div class="alert alert-success alert-block" style="display: none;">
                  <button type="button" class="close" data-dismiss="test">×</button>
                  <strong class="success-msg"></strong>
          </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="myModalDpo2" name="myModalDpo2" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Surat pencabutan permintaan penangkapan tersangka yang masuk Daftar Pencarian Orang (DPO); (jika ada)</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pencabutan_tersangka" hidden>
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
</div>



{{-- modal dpb --}}

<div data-backdrop="false" id="myModalDpb1" name="myModalDpb1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah DPB</h5>
            </div>
            <div class="modal-body">
                <div class="modal-dpb">
                    <form id="dpb-form">
                        @csrf
                        <div class=col-12>
                            <div>
                                <input type="hidden" name="dpb_id" id="dpb_id">
                                <input id="accident_id_dpb" name="accident_id_dpb" type="text" value="{{ $id }}"
                                    hidden>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label for="jenis_dpb"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Jenis') }}</label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="jenis_dpb" type="text"
                                                class="form-control @error('jenis_dpb') is-invalid @enderror"
                                                name="jenis_dpb" value="{{ old('jenis_dpb') }}" autocomplete="jenis_dpb">
                                            <span class="text-danger error-text jenis_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="no_tnkb"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Nomor Kendaraan') }}</label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="no_tnkb" type="text"
                                                class="form-control @error('no_tnkb') is-invalid @enderror"
                                                name="no_tnkb" value="{{ old('no_tnkb') }}"
                                                autocomplete="no_tnkb">
                                            <span class="text-danger error-text tnkb_err"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label for="deskripsi_dpb"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Ciri Khusus') }}</label>
                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            {{-- <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address')}}" required autocomplete="address"> --}}
                                            <textarea class="form-control" name="deskripsi_dpb" id="deskripsi_dpb"
                                                placeholder="Ciri-ciri kendaraan"></textarea>
                                            <span class="text-danger error-text address_err"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-dark-blue btn-dpb">Simpan</button>
                        </div>
                    </form>
                    <table class="table table-bordered dpb-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Nomor Kendaraan</th>
                                <th>Deskripsi Kendaraan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                {{-- <div class="alert alert-success alert-block" style="display: none;">
                  <button type="button" class="close" data-dismiss="test">×</button>
                  <strong class="success-msg"></strong>
                </div> --}}

            </div>
            <div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="myModalDpb2" name="myModalDpb2" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Surat pencabutan permintaan pencarian barang sesuai Daftar Pencarian Barang (DPB); (jika ada)</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pencabutan_barang" hidden>
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
</div>
