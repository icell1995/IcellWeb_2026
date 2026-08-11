<div id="add-data" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                <h4 class="m-0 fw-semibold text-blue-dark">Tambah Pengguna Baru</h4>
            </div>

            <form method="POST" action="{{ route('pengguna_add') }}" name="form" id="form">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="first_name_add" type="text"
                                    class="form-control input-form @error('first_name_add') is-invalid @enderror"
                                    name="first_name_add" value="{{ old('first_name_add') }}" required
                                    autocomplete="first_name_add" autofocus placeholder="Nama Depan*">
                                @error('first_name_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="last_name_add" type="text"
                                    class="form-control input-form @error('last_name_add') is-invalid @enderror"
                                    name="last_name_add" value="{{ old('last_name_add') }}" autocomplete="last_name_add"
                                    autofocus placeholder="Nama Belakang">
                                @error('last_name_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="username_add" type="text"
                                    class="form-control input-form @error('username_add') is-invalid @enderror" name="username_add"
                                    value="{{ old('username_add') }}" required autocomplete="username_add" autofocus
                                    placeholder="Username">
                                @error('username_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="email_add" type="email"
                                    class="form-control input-form @error('email_add') is-invalid @enderror" name="email_add"
                                    value="{{ old('email_add') }}" required autocomplete="email_add"
                                    placeholder="Email">
                                @error('email_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="nrp_add" type="text"
                                    class="form-control input-form @error('nrp_add') is-invalid @enderror" name="nrp_add"
                                    value="{{ old('nrp_add') }}" required autocomplete="nrp_add" autofocus
                                    placeholder="NRP">
                                @error('nrp_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="phone_add" type="text"
                                    class="form-control input-form @error('phone_add') is-invalid @enderror" name="phone_add"
                                    value="{{ old('phone_add') }}" required autocomplete="phone_add"
                                    placeholder="Nomor Telepon">
                                @error('phone_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="role_id_add" class="form-select input-form @error('role_id_add') is-invalid @enderror"
                                    name="role_id_add">
                                    <option value="">Pilih Level</option>
                                    @foreach ($role as $roles)
                                        <option value="{{ $roles->id }}"
                                            {{ old('role_id_add') == $roles->id ? 'selected' : '' }}>
                                            {{ $roles->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <select id="pangkat_add" class="form-select input-form @error('pangkat_add') is-invalid @enderror"
                                    name="pangkat_add">
                                    <option value="" class="option">Pilih Pangkat</option>
                                    @foreach ($rank as $ranks)
                                        <option value="{{ $ranks->id }}"
                                            {{ old('pangkat_add') == $ranks->id ? 'selected' : '' }}>
                                            {{ $ranks->name }}</option>
                                    @endforeach
                                </select>
                                @error('pangkat_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3" id="poldas-name_add" name="poldas-name_add">
                            <div class="form-group">
                                <select id="polda_id_add"
                                    class="form-select input-form @error('polda_id_add') is-invalid @enderror"
                                    name="polda_id_add">
                                    <option value="">Pilih Polda</option>
                                    @foreach ($polda as $poldas)
                                        <option value="{{ $poldas->id }}"
                                            {{ old('polda_id_add') == $poldas->id ? 'selected' : '' }}>
                                            {{ $poldas->name }}</option>
                                    @endforeach
                                </select>
                                @error('polda_id_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3" id="polress-name_add" name="polress-name_add">
                            <div class="form-group">
                                <select id="polres_id_add"
                                    class="form-select input-form @error('polres_id_add') is-invalid @enderror"
                                    name="polres_id_add">
                                    <option value="">Pilih Polres</option>
                                    @foreach ($polres as $polress)
                                        <option value="{{ $polress->id }}"
                                            {{ old('polres_id_add') == $polress->id ? 'selected' : '' }}>
                                            {{ $polress->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('polres_id_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="password_add" type="password"
                                    class="form-control input-form @error('password_add') is-invalid @enderror"
                                    name="password_add" required autocomplete="new-password_add"
                                    placeholder="Password">

                                @error('password_add')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-3">
                            <div class="form-group">
                                <input id="password-confirm_add" type="password" class="form-control input-form"
                                    name="password_add_confirmation" required autocomplete="new-password_add"
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
