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

    <!-- Datatables CSS -->
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.1/r-2.4.0/datatables.min.css" rel="stylesheet" />

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

                <form action="{{route('forms.store')}}" method="post">
                    @csrf

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="polda">Polda</label>
                                {{--<input type="text" class="form-control @error('polda') is-invalid @enderror" id="polda" name="polda" value="{{ $poldaName }}" readonly>
                                <!-- Hidden Input Polda ID -->
                                <input type="hidden" name="poldaId" value="{{ $poldaId }}">--}}

                                <select class="form-control @error('poldaId') is-invalid @enderror select2" id="poldaId" name="poldaId">
                                    <option value="">Pilih Polda</option>
                                    @foreach ($getPolda as $polda)
                                        <option value="@php echo $polda->id @endphp" {{ ($poldaId == $polda->id) ? 'selected' : '' }}>{{ $polda->name }}</option>
                                    @endforeach
                                </select>
                                @error('poldaId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polres">Polres</label>
                                {{--<input type="text" class="form-control @error('polres') is-invalid @enderror" id="polres" name="polres" value="{{ $polresName }}" readonly>
                                <!-- Hidden Input Polres ID -->
                                <input type="hidden" name="polresId" value="{{ $polresId }}">--}}

                                <select class="form-control @error('polresId') is-invalid @enderror select2" id="polresId" name="polresId">
                                    <option value="">Pilih Polres</option>
                                    @foreach ($getPolres as $polres)
                                        <option value="@php echo $polres->id @endphp" {{ ($polresId == $polres->id) ? 'selected' : '' }}>{{ $polres->name }}</option>
                                    @endforeach
                                </select>
                                @error('polresId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresAddress">Alamat Polres</label>
                                <input type="text" class="form-control @error('polresAddress') is-invalid @enderror" id="polresAddress" name="polresAddress" value="{{ old('polresAddress') }}" placeholder="Nama Jalan, Dan Lain-Lain">
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
                        <div class="card-body">
                            <div class="form-group">
                                <label for="kejaksaan">Nama Kejaksaan Negeri Di Wilayah Polres</label>
                                <select class="form-control @error('kejaksaan') is-invalid @enderror select2" id="kejaksaan" name="kejaksaan">
                                    <option value="">Pilih Kejaksaan Negeri</option>
                                    @foreach ($kejaksaanGet as $kejaksaan)
                                        <option value="{{ $kejaksaan->id }}" {{ old('kejaksaan') == $kejaksaan->id ? 'selected' : '' }}>{{ $kejaksaan->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kejaksaan negeri tidak ada dalam pilihan Anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanAddress">Alamat Kejaksaan Negeri</label>
                                <input type="text" class="form-control @error('kejaksaanAddress') is-invalid @enderror" id="kejaksaanAddress" name="kejaksaanAddress" value="{{ old('kejaksaanAddress') }}" placeholder="Nama Jalan, Dan Lain-Lain">
                                @error('kejaksaanAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kejaksaanProvinceId">Provinsi Kejaksaan Negeri</label>
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
                                <label for="kejaksaanRegencyId">Kabupaten/Kota Kejaksaan Negeri</label>
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
                                <label for="kejaksaanDistrictId">Kecamatan Kejaksaan Negeri</label>
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
                                <label for="kejaksaanVillageId">Kelurahan/Desa Kejaksaan Negeri</label>
                                <select class="form-control @error('kejaksaanVillageId') is-invalid @enderror select2" id="kejaksaanVillageId" name="kejaksaanVillageId">
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="kejaksaanVillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('kejaksaanVillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="kejaksaanZipcode">Kode Pos Kejaksaan Negeri</label>
                                <input type="text" class="form-control @error('kejaksaanZipcode') is-invalid @enderror" id="kejaksaanZipcode" name="kejaksaanZipcode" value="{{ old('kejaksaanZipcode') }}" placeholder="Kode Pos Kejaksaan">
                                @error('kejaksaanZipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="pengadilan">Nama Pengadilan Negeri Di Wilayah Polres</label>
                                <select class="form-control @error('pengadilan') is-invalid @enderror select2" id="pengadilan" name="pengadilan">
                                    <option value="">Pilih Pengadilan Negeri</option>
                                    @foreach ($kejaksaanGet as $kejaksaan)
                                        <option value="{{ $kejaksaan->id }}" {{ old('kejaksaan') == $kejaksaan->id ? 'selected' : '' }}></option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="pengadilanHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar pengadilan negeri tidak ada dalam pilihan Anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('pengadilan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengadilanAddress">Alamat Pengadilan Negeri</label>
                                <input type="text" class="form-control @error('pengadilanAddress') is-invalid @enderror" id="pengadilanAddress" name="pengadilanAddress" value="{{ old('pengadilanAddress') }}" placeholder="Nama Jalan, Dan Lain-Lain">
                                @error('pengadilanAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengadilanProvinceId">Provinsi Pengadilan Negeri</label>
                                <select class="form-control @error('pengadilanProvinceId') is-invalid @enderror select2" id="pengadilanProvinceId" name="pengadilanProvinceId">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" {{ old('kejaksaanProvinceId') == $province->id ? 'selected' : '' }}></option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="pengadilanProvinceIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar provinsi tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('pengadilanProvinceId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengadilanRegencyId">Kabupaten/Kota Pengadilan Negeri</label>
                                <select class="form-control @error('pengadilanRegencyId') is-invalid @enderror select2" id="pengadilanRegencyId" name="pengadilanRegencyId">
                                    <option value="">Pilih Kabupaten/Kota (Silahkan Pilih Provinsi Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="pengadilanRegencyIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kabupaten/kota tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('pengadilanRegencyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengadilanDistrictId">Kecamatan Pengadilan Negeri</label>
                                <select class="form-control @error('pengadilanDistrictId') is-invalid @enderror select2" id="pengadilanDistrictId" name="pengadilanDistrictId">
                                    <option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="pengadilanDistrictIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kecamatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('pengadilanDistrictId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengadilanVillageId">Kelurahan/Desa Pengadilan Negeri</label>
                                <select class="form-control @error('pengadilanVillageId') is-invalid @enderror select2" id="pengadilanVillageId" name="pengadilanVillageId">
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                </select>

                                <!-- Foot Note -->
                                <small id="pengadilanVillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('pengadilanVillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengadilanZipcode">Kode Pos Pengadilan Negeri</label>
                                <input type="text" class="form-control @error('pengadilanZipcode') is-invalid @enderror" id="pengadilanZipcode" name="pengadilanZipcode" value="{{ old('pengadilanZipcode') }}" placeholder="Kode Pos Pengadilan">
                                @error('pengadilanZipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Kirim Data</button>
			        </div>
                </form>

            </div>
            
        </div>
        <br>
        <div class="card mb-4">
            <div class="card-header">
                <h5>
                    Updated Data Polres
                </h5>
            </div>
            <div class="card-body">
                <!-- Table Result -->
                <table class="table table-bordered" id="updatedPolresTable">
                    <thead>
                        <tr>
                            <td><b>Polda</b></td>
                            <td><b>Polres</b></td>
                            <td><b>Alamat Polres</b></td>
                            <td><b>Provinsi</b></td>
                            <td><b>Kabupaten/Kota</b></td>
                            <td><b>Kecamatan</b></td>
                            <td><b>Desa/Kelurahan</b></td>
                            <td><b>Kode Pos</b></td>
                            <td><b>Kejaksaan Negeri</b></td>
                            <td><b>Alamat Kejaksaan</b></td>
                            <td><b>Provinsi</b></td>
                            <td><b>Kabupaten/Kota</b></td>
                            <td><b>Kecamatan</b></td>
                            <td><b>Desa/Kelurahan</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($getUpdatedPolres as $polres)
                        <tr>
                            <td>{{$polres->polda_name}}</td>
                            <td>{{$polres->name}}</td>
                            <td>{{$polres->address}}</td>
                            <td>{{$polres->polres_province}}</td>
                            <td>{{$polres->polres_regency}}</td>
                            <td>{{$polres->polres_district}}</td>
                            <td>{{$polres->polres_village}}</td>
                            <td>{{$polres->polres_zipcode}}</td>
                            <td>{{$polres->kejaksaan_name}}</td>
                            <td>{{$polres->kejaksaan_address}}</td>
                            <td>{{$polres->kejaksaan_province}}</td>
                            <td>{{$polres->kejaksaan_regency}}</td>
                            <td>{{$polres->kejaksaan_district}}</td>
                            <td>{{$polres->kejaksaan_village}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
    
    <!-- Datatables -->
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.1/r-2.4.0/datatables.min.js"></script>

    <!-- Own Scripts -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
            });

            $('#updatedPolresTable').DataTable({
                responsive: true
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

        // ====================================================================================================

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

        // ====================================================================================================

        // Get Pengadilan Regency Data
        $('#pengadilanProvinceId').on('change', function() {
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
                        $('#pengadilanRegencyId').empty();
                        $('#pengadilanRegencyId').append('<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#pengadilanRegencyId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#pengadilanRegencyId').select2({
                            theme: 'classic',
                        });

                        // Reset District, and Village Dropdown
                        $('#pengadilanDistrictId').empty();
                        $('#pengadilanDistrictId').append('<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>');
                        $('#pengadilanVillageId').empty();
                        $('#pengadilanVillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#pengadilanRegencyId').empty();
            }
        });

        // Get Pengadilan District Data
        $('#pengadilanRegencyId').on('change', function() {
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
                        $('#pengadilanDistrictId').empty();
                        $('#pengadilanDistrictId').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#pengadilanDistrictId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#pengadilanDistrictId').select2({
                            theme: 'classic',
                        });

                        // Reset Village Dropdown
                        $('#pengadilanVillageId').empty();
                        $('#pengadilanVillageId').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#pengadilanDistrictId').empty();
            }
        });

        // Get Pengadilan Village Data
        $('#pengadilanDistrictId').on('change', function() {
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
                        $('#pengadilanVillageId').empty();
                        $('#pengadilanVillageId').append('<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#pengadilanVillageId').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#pengadilanVillageId').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#pengadilanVillageId').empty();
            }
        });
    </script>
</body>
</html>