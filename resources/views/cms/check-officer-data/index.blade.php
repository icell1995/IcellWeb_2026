@extends('cms.layouts.app')

@section('_title', 'Check Officer Data')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')    
    <div class="loaderbg" style="display:none"></div>
        <div class="box">
            <div class="box-header">
                <h3 class="fw-bold text-blue-dark">Check Officer Data</h3>
            </div>
            <div class="boxy-body mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="fw-bold text-blue-dark mt-1">Check Data</h4>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-4">
                            {{-- <div class=""> --}}
                                <input type="text" class="form-control" id="registerNumberSearchField"
                                    placeholder="Cari NRP" aria-label="Cari NRP" aria-describedby="basic-addon2"
                                    aria-describedby="registerNumberSearch">
                                <button class="btn btn-primary" id="registerNumberSearchButton" type="button"><i
                                        class="bi bi-search"></i> Cari </button>
                            {{-- </div> --}}
                        </div>

                        <h5 class="fw-bold text-blue-dark mb-4">HASIL PENCARIAN</h4>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="registerNumber">NRP</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="registerNumber" type="text" class="form-control @error('registerNumber') is-invalid @enderror" name="registerNumber"
                                    value="{{ old('registerNumber') }}" disabled
                                    placeholder="NRP">
                               
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="name">Nama</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" disabled
                                    placeholder="Nama">
                                
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="rankName">Pangkat</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="rankName" type="text" class="form-control @error('rankName') is-invalid @enderror" name="rankName"
                                    value="{{ old('rankName') }}" disabled
                                    placeholder="Pangkat">
                               
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="positionName">Jabatan</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="positionName" type="text" class="form-control @error('positionName') is-invalid @enderror" name="positionName"
                                    value="{{ old('positionName') }}" disabled
                                    placeholder="Jabatan">
                                
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="unitName">Satuan</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="unitName" type="text" class="form-control @error('unitName') is-invalid @enderror" name="unitName"
                                    value="{{ old('unitName') }}" disabled
                                    placeholder="Satuan">
                                
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="phoneNumber">Telp</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="phoneNumber" type="text" class="form-control @error('phoneNumber') is-invalid @enderror" name="phoneNumber"
                                    value="{{ old('phoneNumber') }}" disabled
                                    placeholder="Telp">
                                
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="genderName">Gender</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="genderName" type="text" class="form-control @error('genderName') is-invalid @enderror" name="genderName"
                                    value="{{ old('genderName') }}" disabled
                                    placeholder="Gender">
                                
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="workEmail">Email Dinas</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="workEmail" type="text" class="form-control @error('workEmail') is-invalid @enderror" name="workEmail"
                                    value="{{ old('workEmail') }}" disabled
                                    placeholder="Email Dinas">
                               
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="investigatorCertificate">Sertifikasi Kepenyidikan</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="investigatorCertificate" type="text" class="form-control @error('investigatorCertificate') is-invalid @enderror" name="investigatorCertificate"
                                    value="{{ old('investigatorCertificate') }}" disabled
                                    placeholder="Sertifikasi Kepenyidikan">
                                
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="investigatorNumber">Nomor Kepenyidikan</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                                <input id="investigatorNumber" type="text" class="form-control @error('investigatorNumber') is-invalid @enderror" name="investigatorNumber"
                                    value="{{ old('investigatorNumber') }}" disabled
                                    placeholder="Nomor Kepenyidikan">
                               
                            </div>
                        </div>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="DetailWorkUnits">Detail Satker</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                
                                <input id="DetailWorkUnits1" type="text" class="form-control @error('DetailWorkUnits1') is-invalid @enderror" name="DetailWorkUnits1"
                                    value="{{ old('DetailWorkUnits1') }}" disabled
                                    placeholder="Satuan 1">
                                <input id="DetailWorkUnits2" type="text" class=" mt-3 form-control @error('DetailWorkUnits2') is-invalid @enderror" name="DetailWorkUnits2"
                                    value="{{ old('DetailWorkUnits2') }}" disabled
                                    placeholder="Satuan 2">
                                <input id="DetailWorkUnits3" type="text" class="mt-3 form-control @error('DetailWorkUnits3') is-invalid @enderror" name="DetailWorkUnits3"
                                    value="{{ old('DetailWorkUnits3') }}" disabled
                                    placeholder="Satuan 3">
                                <input id="DetailWorkUnits4" type="text" class="mt-3 form-control @error('DetailWorkUnits4') is-invalid @enderror" name="DetailWorkUnits4"
                                    value="{{ old('DetailWorkUnits4') }}" disabled
                                    placeholder="Satuan 4">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="fw-bold text-blue-dark mt-1">Riwayat Pengecekan</h4>
                    </div>
                    <div class="card-body">
                        <div class="mt-3 table-responsive">
                            <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%" id="checkofficerDataHistories">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">NRP</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Pangkat</th>
                                        <th class="text-center">Jabatan</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Telp</th>
                                        <th class="text-center">Gender</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Status Kepenyidikan</th>
                                        <th class="text-center">Nomor Penyidik</th>
                                        <th class="text-center">Satker</th>
                                        <th class="text-center">Searched By</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {{--
                                    @foreach ($checkofficerDataHistories as $checkofficerDataHistory)
                                        <tr class="">
                                            <td class="text-center align-middle">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-center align-middle">
                                                {{ $checkofficerDataHistory->register_number }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->name }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->rank_name }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->position_name }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->unit_name }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->phone_number }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->gender_name }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->work_email }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->investigator_certificate }}
                                            </td>
                                            <td>
                                                {{ $checkofficerDataHistory->investigator_number }}
                                            </td>
                                            
                                            <td>
                                                <ul>
                                                    <li>Satuan 1 : {{ $checkofficerDataHistory->work_units['unit1'] ?? '' }}</li>
                                                    <li>Satuan 2 : {{ $checkofficerDataHistory->work_units['unit2'] ?? '' }}</li>
                                                    <li>Satuan 3 : {{ $checkofficerDataHistory->work_units['unit3'] ?? '' }}</li>
                                                    <li>Satuan 4 : {{ $checkofficerDataHistory->work_units['unit4'] ?? '' }}</li>
                                                </ul>
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $checkofficerDataHistory->createdByUser->first_name ?? '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#checkofficerDataHistories').DataTable({
                responsive: true,
                rocessing: true,
                serverSide: true,
                ajax: '{{ url()->current() }}',
                stateSave: true,
                columns: [
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            // Calculate the iteration number
                            var pageInfo = $('#checkofficerDataHistories').DataTable().page.info();
                            var iteration = meta.row + 1 + pageInfo.start;
                            return iteration;
                        }
                    },
                    { data: 'register_number'},
                    { data: 'name'},
                    { data: 'rank_name'},
                    { data: 'position_name'},
                    { data: 'unit_name'},
                    { data: 'phone_number'},
                    { data: 'gender_name'},
                    { data: 'work_email'},
                    { data: 'investigator_certificate'},
                    { data: 'investigator_number'},
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            // render json data
                            var json = data.work_units;

                            var html = '';
                            html += '<ul>';
                            html += '<li>Satuan 1 : ' + json.unit1 + '</li>';
                            html += '<li>Satuan 2 : ' + json.unit2 + '</li>';
                            html += '<li>Satuan 3 : ' + json.unit3 + '</li>';
                            html += '<li>Satuan 4 : ' + json.unit4 + '</li>';
                            html += '</ul>';

                            return html;
                        }
                    },
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            // render json data
                            var createdByUser = data.created_by_user;

                            if(createdByUser){
                                var html = createdByUser.first_name + ' ' + createdByUser.last_name;
                            }else{
                                var html = data.created_by_name ? data.created_by_name : '-';
                            }

                            return html;
                        }
                    }
                ]
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });


        $(document).ready(function() {
            $('#registerNumberSearchButton').click(function() {
                var registerNumber = $('#registerNumberSearchField').val();

                if (registerNumber === '') {
                    //return sweet alert
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Kolom ini wajib diisi',
                    });
                }

                if (isNaN(registerNumber)) {
                    //return sweet alert
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Kolom ini hanya boleh diisi dengan angka',
                    });
                }

                //ajax csrf token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: "{{route('cms.check-officer-data.api.officer-data')}}",
                    method: "POST",
                    data: {
                        registerNumber: registerNumber
                    },
                    success: function(response) {
                        //if data is found
                        // show swal
                        var data = response.Data;

                        //if data is not empty
                        if (data == null || data == undefined || data == '') {
                            //return sweet alert
                            return Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Data tidak ditemukan',
                            });
                        }

                        //append data to input
                        $('#registerNumber').val(data.nrp);
                        $('#name').val(data.nama);
                        $('#rankName').val(data.pangkat);
                        $('#positionName').val(data.jabatan);
                        $('#unitName').val(data.satuan);
                        $('#phoneNumber').val(data.handphone);
                        $('#genderName').val(data.jenis_kelamin);
                        $('#workEmail').val(data.email_dinas);
                        $('#investigatorCertificate').val(data.sertifikasi_penyidikan);
                        $('#investigatorNumber').val(data.nomor_penyidik);
                        $('#DetailWorkUnits1').val(data.satuan1);
                        $('#DetailWorkUnits2').val(data.satuan2);
                        $('#DetailWorkUnits3').val(data.satuan3);
                        $('#DetailWorkUnits4').val(data.satuan4);            
                    },
                    error: function(error, xhr, status) {
                        //sweet alert

                        return Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi Error',
                        });
                    }
                });

            });
        });
    </script>
@endpush
