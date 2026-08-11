<div id="myModalSp2hp" name="myModalSp2hp" class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="alert-sp2hp">

            </div>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">SP2HP</h5>
            </div>
            <div class="modal-body ">
                <div class="modal-sp2hp">
                    <form id="sp2hp-form">
                        @csrf
                        <div class=col-md-12>
                            <div>
                                <input type="hidden" name="sp2hp_id" id="sp2hp_id">
                                <input id="accident_id_sp2hp" name="accident_id_sp2hp" type="text"
                                    value="{{ $id }}" hidden>
                            </div>
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="mb-3 row">
                                        <label for="tipe_sp2hp"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Tipe SP2HP') }}</label>

                                        <div class="col-md-9">
                                            <select id="tipe_sp2hp" name="tipe_sp2hp" class="form-control">
                                                <option selected value=" ">PILIH TIPE SP2HP</option>
                                                <option value="A1">A1</option>
                                                <option value="A2">A2</option>
                                                <option value="A3">A3</option>
                                                <option value="A4">A4</option>
                                                <option value="A5">A5</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="tingkat_kasus"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Tingkat Kasus') }}</label>

                                        <div class="col-md-9">
                                            <select id="tingkat_kasus" name="tingkat_kasus" class="form-control">
                                                <option selected value=" ">PILIH TINGKAT KASUS</option>
                                                <option value="RINGAN">RINGAN</option>
                                                <option value="SEDANG">SEDANG</option>
                                                <option value="BERAT">BERAT</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="nomor_surat"
                                            class="col-md-2 fw-bold col-form-label">{{ __('Nomor Surat') }}</label>

                                        {{-- <div class="col-md-9">
                                            SP2HP / 00 / II / RES.0.0 / 2021 / RES
                                        </div> --}}
                                        <div class="col-md-1">
                                            <input id="nomor_surat_1" type="text"
                                                class="form-control @error('nomor_surat_1') is-invalid @enderror"
                                                name="nomor_surat_1" value="{{ old('nomor_surat_1') }}"
                                                autocomplete="nomor_surat_2">
                                            <span class="text-danger error-text nomor_surat_1_err"></span>
                                        </div>

                                        /

                                        <div class="col-md-1">
                                            <input id="nomor_surat_2" type="text"
                                                class="form-control @error('nomor_surat_2') is-invalid @enderror"
                                                name="nomor_surat_2" value="{{ old('nomor_surat_2') }}"
                                                autocomplete="nomor_surat_2">
                                            <span class="text-danger error-text nomor_surat_2_err"></span>
                                        </div>

                                        /

                                        <div class="col-md-1">
                                            <input id="nomor_surat_3" type="text"
                                                class="form-control @error('nomor_surat_3') is-invalid @enderror"
                                                name="nomor_surat_3" value="{{ old('nomor_surat_3') }}"
                                                autocomplete="nomor_surat_3">
                                            <span class="text-danger error-text nomor_surat_3_err"></span>
                                        </div>

                                        /

                                        <div class="col-md-2">
                                            <input id="nomor_surat_4" type="text"
                                                class="form-control @error('nomor_surat_4') is-invalid @enderror"
                                                name="nomor_surat_4" value="{{ old('nomor_surat_4') }}"
                                                autocomplete="nomor_surat_4">
                                            <span class="text-danger error-text nomor_surat_4_err"></span>
                                        </div>

                                        /

                                        <div class="col-md-2">
                                            <input id="nomor_surat_5" type="text"
                                                class="form-control @error('nomor_surat_5') is-invalid @enderror"
                                                name="nomor_surat_5" value="{{ old('nomor_surat_5') }}"
                                                autocomplete="nomor_surat_5">
                                            <span class="text-danger error-text nomor_surat_5_err"></span>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="kota"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Tempat') }}</label>

                                        <div class="col-md-2">
                                            <input id="kota" type="text"
                                                class="form-control @error('kota') is-invalid @enderror" name="kota"
                                                value="{{ old('kota') }}" autocomplete="kota">
                                            <span class="text-danger error-text kota_err"></span>
                                        </div>

                                        <label for="tgl_terbit"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Tanggal Terbit') }}</label>
                                        <div class="col-md-2">
                                            <input id="tgl_terbit" type="text"
                                                class="form-control @error('tgl_terbit') is-invalid @enderror"
                                                name="tgl_terbit" value="{{ old('tgl_terbit') }}"
                                                placeholder="dd-mm-yyyy" autocomplete="off">
                                            {{-- <input class="form-control datepicker" type="text" id=" "
                                                name=" " placeholder="dd/mm/yyyy" autocomplete="off"> --}}
                                            <span class="text-danger error-text birth_date_err"></span>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="mb-3 row">
                                        <label
                                            class="col-sm-2 fw-bold col-form-label">{{ __('KEPADA : ') }}</label>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="name"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Nama') }}</label>

                                        <div class="col-md-3">
                                            <input id="name_sp2hp" type="text"
                                                class="form-control @error('name_sp2hp') is-invalid @enderror" name="name_sp2hp"
                                                value="{{ old('name_sp2hp') }}">
                                            <span class="text-danger error-text name_sp2hp_err"></span>
                                        </div>

                                        <label for="address"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Beralamat di') }}</label>

                                        <div class="col-md-4">
                                            <input id="address_sp2hp" type="text"
                                                class="form-control @error('address_sp2hp') is-invalid @enderror"
                                                name="address_sp2hp" value="{{ old('address_sp2hp') }}">
                                            <span class="text-danger error-text address_sp2hp_err"></span>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="about"
                                            class="col-sm-2 fw-bold col-form-label">{{ __('Tentang') }}</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" name="about" id="about"
                                                placeholder="Laka lantas yang terjadi ....."></textarea>
                                            <span class="text-danger error-text about_err"></span>
                                        </div>
                                        <span class="text-danger error-text religion_err"></span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </form>
                    <div class="d-flex mb-2 justify-content-center">
                        <div class="ms-2">
                            <button type="submit" class="btn btn-dark-blue btn-sp2hp">SIMPAN</button>
                        </div>
                        <div class="ms-2">
                            <button type="submit" class="btn btn-secondary btn-reset-sp2hp">RESET</button>
                        </div>
                    </div>

                    {{-- <form action="{{route('daftarSaksi', $id)}}" method="GET">
                        <button type="submit" class="btn btn-primary btn-saksi-word" value="{{$id}}" style="float:right;">Save to Word </button>
                    </form> --}}
                    <table class="table table-bordered sp2hp-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tipe</th>
                                <th>Tingkat Kasus</th>
                                <th>Nomor Surat</th>
                                <th>Kota</th>
                                <th>Tanggal Terbit</th>
                                <th>Nama</th>
                                <th>Alamat</th>
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
          </div> --}}

            </div>
            <div class="modal-footer">
                <button class="modalsp2hp btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
