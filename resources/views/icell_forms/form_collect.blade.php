<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Select2 CSS-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <title>Collect Data Master ICELL</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Collect Data Master ICELL (Untuk Kop Surat)</h3>
            </div>
            <div class="card-body">
                <div class="card-body">
                    <p>
                        <b>Deskripsi : </b> Form ini dibuat bertujuan untuk melengkapi data polres untuk Database di ICELL, yang akan digunakan pada fitur generate surat. Mohon untuk mengisi semua field yang diperlukan dengan benar agar fitur tersebut dapat berjalan dengan efektif. Terima kasih atas kerjasamanya.
                    </p>
                </div>

                <!-- error alert -->
                @if ($errors->any())
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{($mode == 'edit') ? route('forms.store', ['mode'=>'edit']) : route('forms.store')}}" method="post">
                    @csrf

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="polda">Polda</label>
                                <input type="text" class="form-control @error('polda') is-invalid @enderror" id="polda" name="polda" value="{{ $poldaName }}" readonly>
                                <!-- Hidden Input Polda ID -->
                                <input type="hidden" name="poldaId" value="{{ $poldaId }}">
                                @error('poldaId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polres">Polres</label>
                                <input type="text" class="form-control @error('polres') is-invalid @enderror" id="polres" name="polres" value="{{ $polresName }}" readonly>
                                <!-- Hidden Input Polres ID -->
                                <input type="hidden" name="polresId" value="{{ $polresId }}">
                                @error('polresId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresAddress">Alamat Polres</label>
                                <input type="text" class="form-control @error('polresAddress') is-invalid @enderror" id="polresAddress" name="polresAddress" value="{{ ($mode == 'edit') ? $polres->address : old('polresAddress') }}" placeholder="Nama Jalan, Dan Lain-Lain">
                                @error('polresAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="polresProvinceId">Provinsi</label>
                                <select class="form-control @error('polresProvinceId') is-invalid @enderror select2" id="polresProvinceId" name="polresProvinceId">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" {{ old('polresProvinceId') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="polresProvinceIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar provinsi tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresProvinceId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresRegencyId">Kabupaten/Kota</label>
                                <select class="form-control @error('polresRegencyId') is-invalid @enderror select2" id="polresRegencyId" name="polresRegencyId">
                                    <option value="">Pilih Kabupaten/Kota (Silahkan Pilih Provinsi Terlebih Dahulu)</option>
                                </select>

                                 <!-- Foot Note -->
                                <small id="polresRegencyIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kabupaten/kota tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresRegencyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresDistrictId">Kecamatan</label>
                                <select class="form-control @error('polresDistrictId') is-invalid @enderror select2" id="polresDistrictId" name="polresDistrictId">
                                    <option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="polresDistrictIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kecamatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresDistrictId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresVillageId">Kelurahan/Desa</label>
                                <select class="form-control @error('polresVillageId') is-invalid @enderror select2" id="polresVillageId" name="polresVillageId">
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="polresVillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresVillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresZipcode">Kode Pos</label>
                                <input type="text" class="form-control @error('polresZipcode') is-invalid @enderror" id="polresZipcode" name="polresZipcode" value="{{ old('polresZipcode') }}" placeholder="Kode Pos">
                                @error('polresZipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <h5 class="ml-3 mt-3">KEJAKSAAN PERTAMA</h5>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="kejaksaan">Nama Kejaksaan Negeri/Tinggi Di Wilayah Polres</label>
                                <select class="form-control @error('kejaksaan') is-invalid @enderror select2" id="kejaksaan" name="kejaksaan">
                                    <option value="">Pilih Kejaksaan Negeri/Tinggi</option>
                                    @foreach ($kejaksaanGet as $kejaksaan)
                                        <option value="{{ $kejaksaan->id }}" {{ old('kejaksaan') == $kejaksaan->id ? 'selected' : '' }}>{{ $kejaksaan->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kejaksaan negeri/tinggi tidak ada dalam pilihan Anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanAddress">Alamat Kejaksaan Negeri/Tinggi</label>
                                <input type="text" class="form-control @error('kejaksaanAddress') is-invalid @enderror" id="kejaksaanAddress" name="kejaksaanAddress" value="{{ ($mode == 'edit') ? $polres->kejaksaan_address : old('kejaksaanAddress') }}" placeholder="Nama Jalan, Dan Lain-Lain">
                                @error('kejaksaanAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanProvinceId">Provinsi</label>
                                <select class="form-control @error('kejaksaanProvinceId') is-invalid @enderror select2" id="kejaksaanProvinceId" name="kejaksaanProvinceId">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" {{ old('kejaksaanProvinceId') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanProvinceIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar provinsi tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaanProvinceId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanRegencyId">Kabupaten/Kota</label>
                                <select class="form-control @error('kejaksaanRegencyId') is-invalid @enderror select2" id="kejaksaanRegencyId" name="kejaksaanRegencyId">
                                    <option value="">Pilih Kabupaten/Kota (Silahkan Pilih Provinsi Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanRegencyIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kabupaten/kota tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaanRegencyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanDistrictId">Kecamatan</label>
                                <select class="form-control @error('kejaksaanDistrictId') is-invalid @enderror select2" id="kejaksaanDistrictId" name="kejaksaanDistrictId">
                                    <option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanDistrictIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kecamatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaanDistrictId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanVillageId">Kelurahan/Desa</label>
                                <select class="form-control @error('kejaksaanVillageId') is-invalid @enderror select2" id="kejaksaanVillageId" name="kejaksaanVillageId">
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanVillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaanVillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <h5 class="ml-3 mt-3">KEJAKSAAN KEDUA <b>(OPSIONAL : Jika Dalam 1 Polres Memiliki 2 Kejaksaan)</b></h5>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="kejaksaan2">Nama Kejaksaan Negeri/Tinggi Kedua Di Wilayah Polres</label>
                                <select class="form-control @error('kejaksaan2') is-invalid @enderror select2" id="kejaksaan2" name="kejaksaan2">
                                    <option value="">Pilih Kejaksaan Negeri/Tinggi</option>
                                    @foreach ($kejaksaanGet as $kejaksaan)
                                        <option value="{{ $kejaksaan->id }}" {{ old('kejaksaan2') == $kejaksaan->id ? 'selected' : '' }}>{{ $kejaksaan->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan2Help" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kejaksaan negeri/tinggi tidak ada dalam pilihan Anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan2Address">Alamat Kejaksaan Negeri/Tinggi Kedua</label>
                                <input type="text" class="form-control @error('kejaksaan2Address') is-invalid @enderror" id="kejaksaan2Address" name="kejaksaan2Address" value="{{ ($mode == 'edit') ? $polres->kejaksaan2_address : old('kejaksaan2Address') }}" placeholder="Nama Jalan, Dan Lain-Lain">
                                @error('kejaksaan2Address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan2ProvinceId">Provinsi</label>
                                <select class="form-control @error('kejaksaan2ProvinceId') is-invalid @enderror select2" id="kejaksaan2ProvinceId" name="kejaksaan2ProvinceId">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" {{ old('kejaksaan2ProvinceId') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan2ProvinceIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar provinsi tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan2ProvinceId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan2RegencyId">Kabupaten/Kota</label>
                                <select class="form-control @error('kejaksaan2RegencyId') is-invalid @enderror select2" id="kejaksaan2RegencyId" name="kejaksaan2RegencyId">
                                    <option value="">Pilih Kabupaten/Kota (Silahkan Pilih Provinsi Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan2RegencyIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kabupaten/kota tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan2RegencyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan2DistrictId">Kecamatan</label>
                                <select class="form-control @error('kejaksaan2DistrictId') is-invalid @enderror select2" id="kejaksaan2DistrictId" name="kejaksaan2DistrictId">
                                    <option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan2DistrictIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kecamatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan2DistrictId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan2VillageId">Kelurahan/Desa</label>
                                <select class="form-control @error('kejaksaan2VillageId') is-invalid @enderror select2" id="kejaksaan2VillageId" name="kejaksaan2VillageId">
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan2VillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan2VillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <h5 class="ml-3 mt-3">KEJAKSAAN KETIGA <b>(OPSIONAL : Jika Dalam 1 Polres Memiliki 3 Kejaksaan)</b></h5>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="kejaksaan3">Nama Kejaksaan Negeri/Tinggi Kedua Di Wilayah Polres</label>
                                <select class="form-control @error('kejaksaan3') is-invalid @enderror select2" id="kejaksaan3" name="kejaksaan3">
                                    <option value="">Pilih Kejaksaan Negeri/Tinggi</option>
                                    @foreach ($kejaksaanGet as $kejaksaan)
                                        <option value="{{ $kejaksaan->id }}" {{ old('kejaksaan3') == $kejaksaan->id ? 'selected' : '' }}>{{ $kejaksaan->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan3Help" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kejaksaan negeri/tinggi tidak ada dalam pilihan Anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan3')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan3Address">Alamat Kejaksaan Negeri/Tinggi Kedua</label>
                                <input type="text" class="form-control @error('kejaksaan3Address') is-invalid @enderror" id="kejaksaan3Address" name="kejaksaan3Address" value="{{ ($mode == 'edit') ? $polres->kejaksaan3_address : old('kejaksaan3Address') }}" placeholder="Nama Jalan, Dan Lain-Lain">
                                @error('kejaksaan3Address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan3ProvinceId">Provinsi</label>
                                <select class="form-control @error('kejaksaan3ProvinceId') is-invalid @enderror select2" id="kejaksaan3ProvinceId" name="kejaksaan3ProvinceId">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" {{ old('kejaksaan3ProvinceId') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan3ProvinceIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar provinsi tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan3ProvinceId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan3RegencyId">Kabupaten/Kota</label>
                                <select class="form-control @error('kejaksaan3RegencyId') is-invalid @enderror select2" id="kejaksaan3RegencyId" name="kejaksaan3RegencyId">
                                    <option value="">Pilih Kabupaten/Kota (Silahkan Pilih Provinsi Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan3RegencyIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kabupaten/kota tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan3RegencyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan3DistrictId">Kecamatan</label>
                                <select class="form-control @error('kejaksaan3DistrictId') is-invalid @enderror select2" id="kejaksaan3DistrictId" name="kejaksaan3DistrictId">
                                    <option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan3DistrictIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kecamatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan3DistrictId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaan3VillageId">Kelurahan/Desa</label>
                                <select class="form-control @error('kejaksaan3VillageId') is-invalid @enderror select2" id="kejaksaan3VillageId" name="kejaksaan3VillageId">
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaan3VillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan3VillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <br>
			        <div class="text-center">
                        <a href="{{route('forms.collect')}}" class="btn btn-danger">Kembali</a>
                        <button type="submit" class="btn btn-primary">Kirim Data</button>
			        </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- Select2 -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Own Scripts -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
            });
        });

        // Initialize Ajax CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get Polres Regency Data
        $('#polresProvinceId').on('change', function() {
            var provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: '/api/forms/regency',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'provinceId': provinceId
                    },
                    success: function(data) {
                        $('#polresRegencyId').empty();
                        $('#polresRegencyId').append('<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#polresRegencyId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });

                        // Reset District, and Village Dropdown
                        $('#polresDistrictId').empty();
                        $('#polresDistrictId').append('<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>');
                        $('#polresVillageId').empty();
                        $('#polresVillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                        $('#polresRegencyId').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#polresRegencyId').empty();
            }
        });

        // Get Polres District Data
        $('#polresRegencyId').on('change', function() {
            var regencyId = $(this).val();
            if (regencyId) {
                $.ajax({
                    url: '/api/forms/district',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'regencyId': regencyId
                    },
                    success: function(data) {
                        $('#polresDistrictId').empty();
                        $('#polresDistrictId').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#polresDistrictId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#polresDistrictId').select2({
                            theme: 'classic',
                        });

                        // Reset Village Dropdown
                        $('#polresVillageId').empty();
                        $('#polresVillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#polresDistrictId').empty();
            }
        });

        // Get Polres Village Data
        $('#polresDistrictId').on('change', function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/api/forms/village',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'districtId': districtId
                    },
                    success: function(data) {
                        $('#polresVillageId').empty();
                        $('#polresVillageId').append('<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#polresVillageId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#polresVillageId').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#polresVillageId').empty();
            }
        });

        // Get Kejaksaan Regency Data
        $('#kejaksaanProvinceId').on('change', function() {
            var provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: '/api/forms/regency',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'provinceId': provinceId
                    },
                    success: function(data) {
                        $('#kejaksaanRegencyId').empty();
                        $('#kejaksaanRegencyId').append('<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaanRegencyId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaanRegencyId').select2({
                            theme: 'classic',
                        });

                        // Reset District, and Village Dropdown
                        $('#kejaksaanDistrictId').empty();
                        $('#kejaksaanDistrictId').append('<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>');
                        $('#kejaksaanVillageId').empty();
                        $('#kejaksaanVillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#kejaksaanRegencyId').empty();
            }
        });

        // Get Kejaksaan District Data
        $('#kejaksaanRegencyId').on('change', function() {
            var regencyId = $(this).val();
            if (regencyId) {
                $.ajax({
                    url: '/api/forms/district',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'regencyId': regencyId
                    },
                    success: function(data) {
                        $('#kejaksaanDistrictId').empty();
                        $('#kejaksaanDistrictId').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaanDistrictId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaanDistrictId').select2({
                            theme: 'classic',
                        });

                        // Reset Village Dropdown
                        $('#kejaksaanVillageId').empty();
                        $('#kejaksaanVillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#kejaksaanDistrictId').empty();
            }
        });

        // Get Kejaksaan Village Data
        $('#kejaksaanDistrictId').on('change', function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/api/forms/village',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'districtId': districtId
                    },
                    success: function(data) {
                        $('#kejaksaanVillageId').empty();
                        $('#kejaksaanVillageId').append('<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaanVillageId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaanVillageId').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#kejaksaanVillageId').empty();
            }
        });

          // Get Kejaksaan 2 Regency Data
        $('#kejaksaan2ProvinceId').on('change', function() {
            var provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: '/api/forms/regency',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'provinceId': provinceId
                    },
                    success: function(data) {
                        $('#kejaksaan2RegencyId').empty();
                        $('#kejaksaan2RegencyId').append('<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaan2RegencyId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaan2RegencyId').select2({
                            theme: 'classic',
                        });

                        // Reset District, and Village Dropdown
                        $('#kejaksaan2DistrictId').empty();
                        $('#kejaksaan2DistrictId').append('<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>');
                        $('#kejaksaan2VillageId').empty();
                        $('#kejaksaan2VillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#kejaksaan2RegencyId').empty();
            }
        });

        // Get Kejaksaan 2 District Data
        $('#kejaksaan2RegencyId').on('change', function() {
            var regencyId = $(this).val();
            if (regencyId) {
                $.ajax({
                    url: '/api/forms/district',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'regencyId': regencyId
                    },
                    success: function(data) {
                        $('#kejaksaan2DistrictId').empty();
                        $('#kejaksaan2DistrictId').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaan2DistrictId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaan2DistrictId').select2({
                            theme: 'classic',
                        });

                        // Reset Village Dropdown
                        $('#kejaksaan2VillageId').empty();
                        $('#kejaksaan2VillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#kejaksaan2DistrictId').empty();
            }
        });

        // Get Kejaksaan 2 Village Data
        $('#kejaksaan2DistrictId').on('change', function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/api/forms/village',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'districtId': districtId
                    },
                    success: function(data) {
                        $('#kejaksaan2VillageId').empty();
                        $('#kejaksaan2VillageId').append('<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaan2VillageId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaan2VillageId').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#kejaksaan2VillageId').empty();
            }
        });

        // Get Kejaksaan 3 Regency Data
        $('#kejaksaan3ProvinceId').on('change', function() {
            var provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: '/api/forms/regency',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'provinceId': provinceId
                    },
                    success: function(data) {
                        $('#kejaksaan3RegencyId').empty();
                        $('#kejaksaan3RegencyId').append('<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaan3RegencyId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaan3RegencyId').select2({
                            theme: 'classic',
                        });

                        // Reset District, and Village Dropdown
                        $('#kejaksaan3DistrictId').empty();
                        $('#kejaksaan3DistrictId').append('<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>');
                        $('#kejaksaan3VillageId').empty();
                        $('#kejaksaan3VillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#kejaksaan3RegencyId').empty();
            }
        });

        // Get Kejaksaan 3 District Data
        $('#kejaksaan3RegencyId').on('change', function() {
            var regencyId = $(this).val();
            if (regencyId) {
                $.ajax({
                    url: '/api/forms/district',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'regencyId': regencyId
                    },
                    success: function(data) {
                        $('#kejaksaan3DistrictId').empty();
                        $('#kejaksaan3DistrictId').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaan3DistrictId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaan3DistrictId').select2({
                            theme: 'classic',
                        });

                        // Reset Village Dropdown
                        $('#kejaksaan3VillageId').empty();
                        $('#kejaksaan3VillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#kejaksaan3DistrictId').empty();
            }
        });

        // Get Kejaksaan 3 Village Data
        $('#kejaksaan3DistrictId').on('change', function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/api/forms/village',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'districtId': districtId
                    },
                    success: function(data) {
                        $('#kejaksaan3VillageId').empty();
                        $('#kejaksaan3VillageId').append('<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#kejaksaan3VillageId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#kejaksaan3VillageId').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#kejaksaan3VillageId').empty();
            }
        });
    </script>
</body>
</html>