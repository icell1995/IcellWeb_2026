<div id="edit-data" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content light-blue">
            @if ($errors->any())
                <div class="alert alert-danger m-0" role="alert">
                    <strong>Ups! </strong> Ada beberapa masalah dengan pengisian form yang Anda masukkan.
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="modal-header border-0">
                <h4 class="m-0 fw-semibold text-blue-dark">Ubah Anggota Baru</h4>
            </div>

            <form method="POST" action="{{ route('petugas_edit') }}">
                @csrf
                <input type="hidden" id="nrp_editt" name="nrp_editt" type="text" value="000007">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="first_name_edit" type="text"
                                    class="form-control input-form @error('first_name_edit') is-invalid @enderror"
                                    name="first_name_edit" value="{{ old('first_name_edit') }}" required
                                    autocomplete="first_name" autofocus placeholder="Nama Depan">
                                @error('first_name_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="last_name_edit" type="text"
                                    class="form-control input-form @error('last_name_edit') is-invalid @enderror"
                                    name="last_name_edit" value="{{ old('last_name_edit') }}" autocomplete="last_name"
                                    autofocus placeholder="Nama Belakang">
                                @error('last_name_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="nrp_edit" type="text"
                                    class="form-control input-form @error('nrp_edit') is-invalid @enderror"
                                    name="nrp_edit" value="{{ old('nrp_edit') }}" autocomplete="nrp_edit" autofocus
                                    placeholder="NRP*">
                                @error('nrp_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="posisi_edit" name="posisi_edit" class="form-select input-form">
                                    <option selected value=null>Posisi Sebagai</option>
                                    <option value="ADMIN">ADMIN</option>
                                    <option value="PENYIDIK">PENYIDIK</option>
                                    <option value="PENYIDIK PEMBANTU">PENYIDIK PEMBANTU</option>
                                </select>
                                @error('posisi_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="polda_id_edit"
                                    class="form-select input-form @error('polda_id_edit') is-invalid @enderror"
                                    name="polda_id_edit">
                                    <option value="">Pilih Polda</option>
                                </select>
                                @error('polda_id_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="polres_id_edit"
                                    class="form-select input-form @error('polres_id_edit') is-invalid @enderror"
                                    name="polres_id_edit">
                                    <option value="">Pilih Polres</option>
                                </select>
                                @error('polres_id_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="kepala_edit" name="kepala_edit" class="form-select input-form">
                                    <option selected value=->Sebagai Kepala</option>
                                    <option value="KANIT LAKA">KANIT GAKKUM</option>
                                    <option value="KASAT LAKA">KASAT LANTAS</option>
                                    <option value="KASI LAKA">KASI LAKA</option>
                                </select>
                                @error('kepala_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="pangkat_edit"
                                    class="form-select input-form @error('pangkat_edit') is-invalid @enderror"
                                    name="pangkat_edit">
                                    <option value="" class="option">Pilih Pangkat</option>
                                    @foreach ($rank as $ranks)
                                        <option value="{{ $ranks->id }}"
                                            {{ old('pangkat_add') == $ranks->id ? 'selected' : '' }}>
                                            {{ $ranks->name }}</option>
                                    @endforeach
                                </select>
                                @error('pangkat_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-dark-blue">
                        {{ __('Update') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
