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

    <title>Konfirmasi Kelengkapan Data Untuk Syarat SPDP Online</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Konfirmasi Kelengkapan Data Untuk Syarat SPDP Online
                    <a class="btn btn-primary float-right" href="#" onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </h3>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>

            </div>
            <div class="card-body">
                @if($isInvalidIdentityNumber == true)
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <p>
                            <b>Perhatian :</b><br>
                            {!!$invalidIdentityNumber['message']!!}
                        </p>
                    </div>
                </div>
                @else
                <div class="card-body">
                    <p>
                        <b>Perhatian : </b><br>
                        Konfirmasi Data Berikut Untuk Dapat Melanjutkan Masuk Ke Beranda ICELL.
                    </p>
                </div>
                @endif
                <div class="card-body">
                    <p>
                        <b>Deskripsi : </b><br> 
                        Data berikut digunakan untuk mengkonfirmasi kelengkapan data yang diperlukan untuk merealisasikan fitur SPDP Online.
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
                
                @if (session('error'))
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
                @endif
                    
                <form action="{{route('forms.confirmation.store')}}" method="POST">
                    @csrf
                    <div class="card">
                        <h5 class="ml-3 mt-3">DATA POLRES</h5>
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
                                <input type="text" class="form-control @error('polresAddress') is-invalid @enderror" id="polresAddress" name="polresAddress" value="{{$polres->address}}" placeholder="Nama Jalan, Dan Lain-Lain" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                @error('polresAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="polresProvinceId">Provinsi</label>
                                <select class="form-control @error('polresProvinceId') is-invalid @enderror select2" id="polresProvinceId" name="polresProvinceId" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" {{ $currentProvince->id == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
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
                                <select class="form-control @error('polresRegencyId') is-invalid @enderror select2" id="polresRegencyId" name="polresRegencyId" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                    <option value="">Pilih Kabupaten/Kota (Silahkan Pilih Provinsi Terlebih Dahulu)</option>
                                    @foreach ($regencies as $regency)
                                        <option value="{{ $regency->id }}" {{ $currentRegency->id == $regency->id ? 'selected' : '' }}>{{ $regency->name }}</option>
                                    @endforeach
                                </select>

                                 <!-- Foot Note -->
                                <small id="polresRegencyIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kabupaten/kota tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresRegencyId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresDistrictId">Kecamatan</label>
                                <select class="form-control @error('polresDistrictId') is-invalid @enderror select2" id="polresDistrictId" name="polresDistrictId" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                    <option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" {{ $currentDistrict->id == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="polresDistrictIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kecamatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresDistrictId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresVillageId">Kelurahan/Desa</label>
                                <select class="form-control @error('polresVillageId') is-invalid @enderror select2" id="polresVillageId" name="polresVillageId" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                    <option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>
                                    @foreach ($villages as $village)
                                        <option value="{{ $village->id }}" {{ $currentVillage->id == $village->id ? 'selected' : '' }}>{{ $village->name }}</option>
                                    @endforeach
                                </select>

                                <!-- Foot Note -->
                                <small id="polresVillageIdHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar kelurahan/desa tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                @error('polresVillageId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="polresZipcode">Kode Pos</label>
                                <input type="text" class="form-control @error('polresZipcode') is-invalid @enderror" id="polresZipcode" name="polresZipcode" value="{{ $polres->polres_zipcode }}" placeholder="Kode Pos" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                @error('polresZipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="card">
                        <h5 class="ml-3 mt-3">DATA PEJABAT PENANDATANGAN DOKUMEN</h5>
                        <div class="card-body">
                            <div class="card-body">
                                <p>
                                    <b>Perhatian :</b><br>
                                    Data ini diperlukan oleh Badan Sertifikasi Elektronik (BSrE) sebagai syarat wajib untuk di daftarkan agar dapat membuat Tanda Tangan Elektronik (TTE)
                                </p>
                            </div>
                            
                            @foreach($authorizedSignatories as $data)
                                @php $xNo = 0; @endphp
                            <div class="card">
                                <h6 class="ml-3 mt-3"><b>Pejabat {{$loop->iteration}}</b></h6>
                                <input type="hidden" class="form-control @error('idAuthorizedSignatory[]') is-invalid @enderror" id="idAuthorizedSignatory{{$loop->iteration}}" name="idAuthorizedSignatory[]" value="{{$data->id}}">
                                <div class="card-body">
                                    <div class="alert alert-warning" role="alert">
                                        <b>PERHATIAN</b>
                                        <br>
                                        <p>Berdasarkan arahan yang dikeluarkan oleh Bapak Kasubditlaka Korlantas, KOMBES POL C.F. HOTMAN SIRAIT, S.I.K., S.H., kami sampaikan bahwa para Kasat Lantas/Pejabat Penandatangan diharapkan untuk memiliki gelar <b>minimal</b> Strata 1 (Sarjana/S1) sebagai syarat untuk pejabat Tanda Tangan Elektronik. Apabila Kasat Lantas/Pejabat Penandatangan belum memenuhi syarat tersebut <b>atau juga</b> belum ada Kasat Lantas, maka pejabat penandatangan akan naik menjadi KAPOLRES atau WAKAPOLRES @if($isOpenKanitGakkum == true) atau boleh juga KANIT GAKKUM <b>(Minimal Sarjana/Strata 1)</b>@endif</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="firstTitleAuthorizedSignatory">Gelar Pendidikan Depan</label>
                                                <input type="text" class="form-control @error('firstTitleAuthorizedSignatory') is-invalid @enderror" id="firstTitleAuthorizedSignatory{{$loop->iteration}}" name="firstTitleAuthorizedSignatory[]" value="{{$data->first_title}}" placeholder="Gelar Depan" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                                
                                                <!-- Foot Note -->
                                                <small id="firstTitleAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Huruf Dan Tanda Baca</small>
                                                @error('firstTitleAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>        
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstNameAuthorizedSignatory">Nama Depan</label>
                                                <input type="text" class="form-control @error('firstNameAuthorizedSignatory') is-invalid @enderror" id="firstNameAuthorizedSignatory{{$loop->iteration}}" name="firstNameAuthorizedSignatory[]" value="{{$data->first_name}}" placeholder="Nama Depan" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                                
                                                <!-- Foot Note -->
                                                <small id="firstNameAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Penulisan Huruf Dan Tanda Baca</small>
                                                @error('firstNameAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>        
                                        </div>
                                            
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lastNameAuthorizedSignatory">Nama Belakang (Opsional Jika Ada)</label>
                                                <input type="text" class="form-control @error('lastNameAuthorizedSignatory') is-invalid @enderror" id="lastNameAuthorizedSignatory{{$loop->iteration}}" name="lastNameAuthorizedSignatory[]" value="{{$data->last_name}}" placeholder="Nama Belakang" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                                
                                                <!-- Foot Note -->
                                                <small id="lastNameAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Penulisan Huruf Dan Tanda Baca</small>
                                                @error('lastNameAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>            
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="lastTitleAuthorizedSignatory">Gelar Pendidikan Belakang</label>
                                                <input type="text" class="form-control @error('lastTitleAuthorizedSignatory') is-invalid @enderror" id="lastTitleAuthorizedSignatory{{$loop->iteration}}" name="lastTitleAuthorizedSignatory[]" value="{{$data->last_title}}" placeholder="Gelar Belakang" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                                
                                                <!-- Foot Note -->
                                                <small id="lastTitleAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Huruf Dan Tanda Baca</small>
                                                @error('lastTitleAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>        
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <label for="rankAuthorizedSignatory">Pangkat</label>
                                        <select class="form-control @error('rankAuthorizedSignatory') is-invalid @enderror select2" id="rankAuthorizedSignatory{{$loop->iteration}}" name="rankAuthorizedSignatory[]" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                            <option value="">--Pilih Pangkat--</option>
                                            @foreach ($ranks as $rank)
                                                <option value="{{ $rank['id'] }}" {{ $data->rank_id == $rank['id'] ? 'selected' : '' }}>{{ $rank['name'] . ' (' . $rank['id'] . ')' }}</option>
                                            @endforeach
                                        </select>

                                        <!-- Foot Note -->
                                        <small id="rankAuthorizedSignatoryHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar pangkat tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                        @error('rankAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="registerNumberAuthorizedSignatory">NRP</label>
                                        <input type="text" class="form-control @error('registerNumberAuthorizedSignatory') is-invalid @enderror" id="registerNumberAuthorizedSignatory{{$loop->iteration}}" name="registerNumberAuthorizedSignatory[]" value="{{$data->register_number}}" placeholder="NRP">
                                        @error('registerNumberAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- Foot Note -->
                                        <small id="registerNumberAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Jumlah Dan Format Angka</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="positionAuthorizedSignatory">Jabatan</label>
                                        <select class="form-control @error('positionAuthorizedSignatory') is-invalid @enderror select2" id="positionAuthorizedSignatory{{$loop->iteration}}" name="positionAuthorizedSignatory[]" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                            <option value="">--Pilih Jabatan--</option>
                                            @foreach ($positions as $position)
                                                @if($position['id'] == 'KANIT GAKKUM' || $position['id'] == 'PS. KANIT GAKKUM')
                                                    @if($data->position_id == 'KANIT GAKKUM' || $data->position_id == 'PS. KANIT GAKKUM' || $isOpenKanitGakkum == true)
                                                        <option value="{{ $position['id'] }}" {{ $data->position_id == $position['id'] ? 'selected' : '' }}>{{ $position['name'] }}</option>
                                                    @endif
                                                @else
                                                    <option value="{{ $position['id'] }}" {{ $data->position_id == $position['id'] ? 'selected' : '' }}>{{ $position['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                        <!-- Foot Note -->
                                        <small id="positionAuthorizedSignatoryHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar jabatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                        @error('positionAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="identityNumberAuthorizedSignatory">NIK</label>
                                        <input type="text" class="form-control @error('identityNumberAuthorizedSignatory') is-invalid @enderror" id="identityNumberAuthorizedSignatory{{$loop->iteration}}" name="identityNumberAuthorizedSignatory[]" value="{{($data->identity_number) ?? old('identityNumberAuthorizedSignatory.' . $xNo)}}" placeholder="Nomor Induk Kependudukan">
                                        
                                        @if($isInvalidIdentityNumber == true)
                                        <div class="card-body">
                                            <div class="alert alert-warning" role="alert">
                                                <p>
                                                    <b>Perhatian :</b><br>
                                                    {!!$invalidIdentityNumber['message']!!}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        <!-- Foot Note -->
                                        <small id="identityNumberAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Format Angka Nomor Induk Kependudukan Pejabatan Penandatangan.</small>
                                        @error('identityNumberAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="emailAuthorizedSignatory">Email Polri (OPSIONAL Jika Ada)</label>
                                        <input type="text" class="form-control @error('emailAuthorizedSignatory') is-invalid @enderror" id="emailAuthorizedSignatory{{$loop->iteration}}" name="emailAuthorizedSignatory[]" value="{{($data->email) ?? old('emailAuthorizedSignatory.' . $xNo)}}" placeholder="xxxxxxxxxx@polri.go.id" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                        
                                        <!-- Foot Note -->
                                        <small id="emailAuthorizedSignatoryHelp" class="form-text text-muted">Email Official Polri Pejabatan Penandatangan.</small>
                                        @error('emailAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phoneAuthorizedSignatory">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phoneAuthorizedSignatory') is-invalid @enderror" id="phoneAuthorizedSignatory{{$loop->iteration}}" name="phoneAuthorizedSignatory[]" value="{{($data->phone) ?? old('phoneAuthorizedSignatory.' . $xNo)}}" placeholder="08xxxxxxxxxx" @if($isInvalidIdentityNumber == true){{'readonly'}}@endif>
                                        
                                        <!-- Foot Note -->
                                        <small id="phoneAuthorizedSignatoryHelp" class="form-text text-muted">Nomor Telepon Pejabatan Penandatangan.</small>
                                        @error('phoneAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <br>
                                @php $xNo++; @endphp
                            @endforeach
                        </div>
                    </div>

                    <br>
			        <div class="text-center">
                        <button type="submit" class="btn btn-primary">Konfirmasi & Submit</button>
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