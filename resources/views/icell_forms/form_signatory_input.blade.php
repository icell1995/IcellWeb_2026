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

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/v/bs4/jq-3.6.0/dt-1.13.3/r-2.4.0/datatables.css"/>

    <title>Authorized Signatory</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Authorized Signatory</h3>
            </div>
            <div class="card-body">
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

                <!-- success alert -->
                @if (session('success'))
                <div class="card-body">
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                </div>
                @endif

                <form action="{{route('forms.signatory.store')}}" method="POST">
                    @csrf
                    <br>
                    <div class="card">
                        <h5 class="ml-3 mt-3">DATA PEJABAT PENANDATANGAN DOKUMEN</h5>
                        <div class="card-body">
                            
                            <div class="card">
                                <h6 class="ml-3 mt-3"><b>Pejabat</b></h6>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="polresAuthorizedSignatory"><b>Polres</b></label>
                                        <select class="form-control @error('polresAuthorizedSignatory') is-invalid @enderror select2" id="polresAuthorizedSignatory" name="polresAuthorizedSignatory">
                                            <option value="">--Pilih Polres--</option>
                                            @foreach ($polda as $data)
                                                <option value=""><b>=====(Polda {{ $data->name }})=====</b></option>
                                                @foreach ($data->polres as $item)
                                                    <option value="{{ $item->id }}" {{ old('polresAuthorizedSignatory') == $item->id ? 'selected' : '' }}>{{ $item->name . ' --- ' . $data->name }}</option>
                                                @endforeach
                                            @endforeach
                                        </select>

                                        <!-- Foot Note -->
                                        <small id="polresAuthorizedSignatoryHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar polres tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                        @error('polresAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="firstTitleAuthorizedSignatory"><b>Gelar Depan</b></label>
                                                <input type="text" class="form-control @error('firstTitleAuthorizedSignatory') is-invalid @enderror" id="firstTitleAuthorizedSignatory" name="firstTitleAuthorizedSignatory" value="{{old('firstTitleAuthorizedSignatory')}}" placeholder="Gelar Depan">
                                                
                                                <!-- Foot Note -->
                                                <small id="firstTitleAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Huruf Dan Tanda Baca</small>
                                                @error('firstTitleAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>        
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstNameAuthorizedSignatory"><b>Nama Depan</b></label>
                                                <input type="text" class="form-control @error('firstNameAuthorizedSignatory') is-invalid @enderror" id="firstNameAuthorizedSignatory" name="firstNameAuthorizedSignatory" value="{{old('firstNameAuthorizedSignatory')}}" placeholder="Nama Depan">
                                                
                                                <!-- Foot Note -->
                                                <small id="firstNameAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Penulisan Huruf Dan Tanda Baca</small>
                                                @error('firstNameAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>        
                                        </div>
                                            
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lastNameAuthorizedSignatory"><b>Nama Belakang</b> (Opsional Jika Ada)</label>
                                                <input type="text" class="form-control @error('lastNameAuthorizedSignatory') is-invalid @enderror" id="lastNameAuthorizedSignatory" name="lastNameAuthorizedSignatory" value="{{old('lastNameAuthorizedSignatory')}}" placeholder="Nama Belakang">
                                                
                                                <!-- Foot Note -->
                                                <small id="lastNameAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Penulisan Huruf Dan Tanda Baca</small>
                                                @error('lastNameAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>            
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="lastTitleAuthorizedSignatory"><b>Gelar Belakang</b></label>
                                                <input type="text" class="form-control @error('lastTitleAuthorizedSignatory') is-invalid @enderror" id="lastTitleAuthorizedSignatory" name="lastTitleAuthorizedSignatory" value="{{old('lastTitleAuthorizedSignatory')}}" placeholder="Gelar Belakang">
                                                
                                                <!-- Foot Note -->
                                                <small id="lastTitleAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Huruf Dan Tanda Baca</small>
                                                @error('lastTitleAuthorizedSignatory')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>        
                                        </div>
                                    </div>
                                      
                                    <div class="form-group">
                                        <label for="rankAuthorizedSignatory"><b>Pangkat</b></label>
                                        <select class="form-control @error('rankAuthorizedSignatory') is-invalid @enderror select2" id="rankAuthorizedSignatory" name="rankAuthorizedSignatory">
                                            <option value="">--Pilih Pangkat--</option>
                                            @foreach ($ranks as $rank)
                                                <option value="{{ $rank['id'] }}" {{ old('rankAuthorizedSignatory') == $rank['id'] ? 'selected' : '' }}>{{ $rank['name'] . ' (' . $rank['id'] . ')' }}</option>
                                            @endforeach
                                        </select>

                                        <!-- Foot Note -->
                                        <small id="rankAuthorizedSignatoryHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar pangkat tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                        @error('rankAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="registerNumberAuthorizedSignatory"><b>NRP</b></label>
                                        <input type="text" class="form-control @error('registerNumberAuthorizedSignatory') is-invalid @enderror" id="registerNumberAuthorizedSignatory" name="registerNumberAuthorizedSignatory" value="{{old('registerNumberAuthorizedSignatory')}}" placeholder="NRP">
                                        @error('registerNumberAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- Foot Note -->
                                        <small id="registerNumberAuthorizedSignatoryHelp" class="form-text text-muted">Perhatikan Jumlah Angka</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="positionAuthorizedSignatory"><b>Jabatan</b></label>
                                        <select class="form-control @error('positionAuthorizedSignatory') is-invalid @enderror select2" id="positionAuthorizedSignatory" name="positionAuthorizedSignatory">
                                            <option value="">--Pilih Pangkat--</option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position['id'] }}" {{ old('positionAuthorizedSignatory') == $position['id'] ? 'selected' : '' }}>{{ $position['name'] }}</option>
                                            @endforeach
                                        </select>

                                        <!-- Foot Note -->
                                        <small id="positionAuthorizedSignatoryHelp" class="form-text text-muted">Jika opsi yang tersedia untuk daftar jabatan tidak mencakup pilihan anda, silakan hubungi tim helpdesk untuk mendapatkan bantuan.</small>
                                        @error('positionAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="identityNumberAuthorizedSignatory"><b>NIK</b></label>
                                        <input type="text" class="form-control @error('identityNumberAuthorizedSignatory') is-invalid @enderror" id="identityNumberAuthorizedSignatory" name="identityNumberAuthorizedSignatory" value="{{old('identityNumberAuthorizedSignatory')}}" placeholder="Nomor Induk Kependudukan">
                                        
                                        <!-- Foot Note -->
                                        <small id="identityNumberAuthorizedSignatoryHelp" class="form-text text-muted">Nomor Induk Kependudukan Pejabatan Penandatangan.</small>
                                        @error('identityNumberAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="emailAuthorizedSignatory"><b>Email Polri</b> (OPSIONAL Jika Ada)</label>
                                        <input type="text" class="form-control @error('emailAuthorizedSignatory') is-invalid @enderror" id="emailAuthorizedSignatory" name="emailAuthorizedSignatory" value="{{old('emailAuthorizedSignatory')}}" placeholder="xxxxxxxxxx@polri.go.id">
                                        
                                        <!-- Foot Note -->
                                        <small id="emailAuthorizedSignatoryHelp" class="form-text text-muted">Email Official Polri Pejabatan Penandatangan.</small>
                                        @error('emailAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phoneAuthorizedSignatory"><b>Nomor Telepon</b></label>
                                        <input type="text" class="form-control @error('phoneAuthorizedSignatory') is-invalid @enderror" id="phoneAuthorizedSignatory" name="phoneAuthorizedSignatory" value="{{old('phoneAuthorizedSignatory')}}" placeholder="08xxxxxxxxxx">
                                        
                                        <!-- Foot Note -->
                                        <small id="phoneAuthorizedSignatoryHelp" class="form-text text-muted">Nomor Telepon Pejabatan Penandatangan.</small>
                                        @error('phoneAuthorizedSignatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
			        <div class="text-center">
                        <button type="submit" class="btn btn-primary">Save Data</button>
			        </div>
                </form>
            </div>
        </div>

        <br>
        <div class="card">
            <div class="card-header">
                <h4>Table List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-1">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No
                                </th>
                                <th>Polres</th>
                                <th>Nama</th>
                                <th>Pangkat</th>
                                <th>NRP</th>
                                <th>Jabatan</th>
                                <th>NIK</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($authorizedSignatories as $item)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>{{ $item->polres_name . ' - ' . $item->polda_name}}</td>
                                    <td>{{ $item->first_title . ' ' . $item->first_name . ' ' . $item->last_name . ', ' . $item->last_title}}</td>
                                    <td>{{ $item->rank_id }}</td>
                                    <td>{{ $item->register_number }}</td>
                                    <td>{{ $item->position_short_name }}</td>
                                    <td>{{ $item->identity_number }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>
                                        <form action="{{ route('forms.signatory.delete', [$item->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are You Sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/v/bs4/jq-3.6.0/dt-1.13.3/r-2.4.0/datatables.js"></script>

    <!-- Select2 -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Own Scripts -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
            });
        });

        $(document).ready(function() {
            $('#table-1').DataTable({
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