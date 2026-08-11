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
                <h4 class="m-0 fw-semibold text-blue-dark">Ubah Data Pengguna</h4>
            </div>

            <form method="POST" action="{{ route('pengguna_edit') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="first_name_edit" type="text"
                                    class="form-control input-form @error('first_name_edit') is-invalid @enderror"
                                    name="first_name_edit" value="{{ old('first_name_edit') }}" required
                                    autocomplete="first_name" autofocus placeholder="Nama Depan*">
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
                                <input id="username_edit" type="text"
                                    class="form-control input-form @error('username_edit') is-invalid @enderror"
                                    name="username_edit" value="{{ old('username_edit') }}" required
                                    autocomplete="username_edit" autofocus placeholder="Username">

                                @error('username_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="email_edit" type="email"
                                    class="form-control input-form @error('email_edit') is-invalid @enderror" name="email_edit"
                                    value="{{ old('email_edit') }}" required autocomplete="email_edit"
                                    placeholder="Email">
                                @error('email_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="nrp_add" type="text"
                                    class="form-control input-form input-form @error('nrp_add') is-invalid @enderror"
                                    name="nrp_add" value="{{ old('nrp_add') }}" required autocomplete="nrp_add"
                                    autofocus placeholder="NRP">
                                @error('nrp_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="phone_edit" type="text"
                                    class="form-control input-form @error('phone_edit') is-invalid @enderror" name="phone_edit"
                                    value="{{ old('phone_edit') }}" required autocomplete="phone_edit"
                                    placeholder="Nomor Telepon">
                                @error('phone_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="role_id_edit"
                                    class="form-select input-form @error('role_id_edit') is-invalid @enderror" name="role_id_edit">
                                    <option value="">Pilih Level</option>
                                </select>
                                @error('role_id_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="pangkat_edit"
                                    class="form-select input-form @error('pangkat_edit') is-invalid @enderror" name="pangkat_edit">
                                    <option value="" class="option">Pilih Pangkat</option>
                                    @foreach ($rank as $ranks)
                                        <option value="{{ $ranks->id }}"
                                            {{ old('pangkat_edit') == $ranks->id ? 'selected' : '' }}>
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
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3" id="poldas-name_edit"
                            name="poldas-name_add">
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
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3" id="polress-name_edit"
                            name="polress-name_add">
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
                                <input id="password_edit" type="password"
                                    class="form-control input-form @error('password_edit') is-invalid @enderror"
                                    name="password_edit" autocomplete="new-password_edit" placeholder="Password">
                                @error('password_edit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="password-confirm_edit" type="password" class="form-control input-form"
                                    name="password_edit_confirmation" autocomplete="new-password_edit"
                                    placeholder="Confirm Password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-dark-blue">
                        {{ __('Simpan') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
