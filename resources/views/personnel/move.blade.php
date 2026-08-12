@php
    $_title = 'Mutasi Personel';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="box">
        <div class="box-header">
            <h4 class="fw-bold text-blue-dark">Mutasi</h4>

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
        </div>

        <div class="card">
            <div class="card-body">
                <form
                    action="{{ route('personnel.move.update', [
                        'id' => $id,
                        'policeId' => $policeId,
                    ]) }}"
                    method="POST" enctype="multipart/form-data" id="personnelMoveForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="id" value="{{ $id }}">
                    <div class="input-group row mb-3">
                        <label class="fw-bold col-sm-2 col-form-label" for="name">Nama</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="name" type="text"
                                class="form-control @error('name') is-invalid @enderror font-weight-bold" name="name"
                                value="{{ $officer->full_name }}" placeholder="" disabled>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3">
                        <label class="fw-bold col-sm-2 col-form-label" for="registerNumber">NRP</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="registerNumber" type="text"
                                class="form-control @error('registerNumber') is-invalid @enderror font-weight-bold"
                                name="registerNumber" value="{{ $officer->register_number }}" placeholder="" disabled>

                            @error('registerNumber')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3">
                        <label class="fw-bold col-sm-2 col-form-label" for="rankName">Pangkat</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <input id="rankName" type="text"
                                class="form-control @error('rankName') is-invalid @enderror font-weight-bold"
                                name="rankName" value="{{ $officer->rank->full_name ?? '-' }}" placeholder="" disabled>

                            @error('rankName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3">
                        <label class="fw-bold col-sm-2 col-form-label">Jenis Mutasi </label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <div class="d-flex mb-3">
                                <div class="form-check m-1">
                                    <input class="form-check-input" type="radio" id="presentMutationType" name="mutationType" value="PRESENT"
                                        @if (old('mutationType') == 'PRESENT' || empty(old('mutationType'))) {{ 'checked' }} @endif>
                                    <label for="presentMutationType">
                                        Kesatuan Gakkum Lantas Lain
                                    </label>
                                </div>

                                <div class="form-check m-1">
                                    <input class="form-check-input" type="radio" id="exitMutationType" name="mutationType" value="EXIT"
                                        @if (old('mutationType') == 'EXIT') {{ 'checked' }} @endif>
                                    <label for="exitMutationType">
                                        Keluar Gakkum Lantas
                                    </label>
                                </div>

                                <div class="form-check m-1">
                                    <input class="form-check-input" type="radio" id="retireMutationType" name="mutationType" value="RETIRE"
                                        @if (old('mutationType') == 'RETIRE') {{ 'checked' }} @endif>
                                    <label for="retireMutationType">
                                        Pensiun
                                    </label>
                                </div>
                               
                                <div class="form-check m-1">
                                    <input class="form-check-input" type="radio" id="assistanceMutationType" name="mutationType" value="ASSISTANCE"
                                        @if (old('mutationType') == 'ASSISTANCE') {{ 'checked' }} @endif>
                                    <label for="assistanceMutationType">
                                        BKO
                                    </label>
                                </div>
                            </div>

                            @error('mutationType')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div id="presentMutationTypeSection" @if(old('mutationType') != 'PRESENT' && !empty(old('mutationType'))) style="display:none;" @endif>
                        <h5 class="fw-bold text-blue-dark">WILAYAH HUKUM</h4>

                        <div class="col-12 my-2">
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-2 col-form-label" for="presentMutationTypePolice">Satker Tujuan Mutasi</label>
                                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                    <input id="presentMutationTypePoliceName" type="text"
                                        class="form-control @error('presentMutationTypePoliceName') is-invalid @enderror font-weight-bold" name="presentMutationTypePoliceName"
                                        value="{{old('presentMutationTypePoliceName')}}" readonly>

                                    @error('presentMutationTypePoliceName')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    <input id="presentMutationTypePoliceId" type="hidden"
                                        class="form-control font-weight-bold" name="presentMutationTypePoliceId"
                                        value="{{old('presentMutationTypePoliceId')}}">
                                </div>
                            </div>

                            <div id="presentMutationTypePoliceSection" class="mt-4">
                                <h6 class="fw-bold">CARI (SATKER TUJUAN)</h6>
                                <div class="input-group row mb-3 ms-0">
                                    <label class="fw-bold col-sm-2 col-form-label" for="presentMutationTypePolice">Masukkan Satker tujuan Mutasi</label>
                                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                        <div class="row">
                                            <div class="col-12">
                                                {{-- <div class="icheck-primary d-inline mx-1">
                                                    <input type="radio" id="presentMutationTypePolicePusatClass" name="presentMutationTypePoliceClass" value="PUSAT" checked>
                                                    <label for="presentMutationTypePolicePusatClass">
                                                        Korlantas
                                                    </label>
                                                </div> --}}

                                                <div class="icheck-primary d-inline mx-1">
                                                    <input type="radio" id="presentMutationTypePoliceDaerahClass" name="presentMutationTypePoliceClass" value="DAERAH">
                                                    <label for="presentMutationTypePoliceDaerahClass">
                                                        Polda
                                                    </label>
                                                </div>
                                            
                                                <div class="icheck-primary d-inline mx-1">
                                                    <input type="radio" id="presentMutationTypePoliceResorClass" name="presentMutationTypePoliceClass" value="RESOR" checked>
                                                    <label for="presentMutationTypePoliceResorClass">
                                                        Polres
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <br/>
                                            <div class="row mt-1">
                                            <div class="col-md-6">
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="presentMutationTypePoliceSearchField"
                                                        placeholder="Cari Nama Satker tujuan" aria-label="Cari Nama Satker tujuan" aria-describedby="basic-addon2"
                                                        aria-describedby="presentMutationTypePoliceSearch">
                                                    <button class="btn btn-primary" id="presentMutationTypePoliceSearchButton" type="button"><i
                                                            class="bi bi-search"></i> Cari </button>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <select class="custom-select select2-input-group" id="presentMutationTypePoliceSearchOption"
                                                        aria-describedby="presentMutationTypePoliceSearchOptionAddButtton">
                                                        <option value="">--Pilih Satker tujuan--</option>
                                                    </select>
                                                    <button class="btn btn-primary" id="presentMutationTypePoliceSearchOptionAddButtton"
                                                        type="button"><i class="bi bi-plus-circle"></i> Pilih</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="operationControlAssistanceSection" @if(old('mutationType') != 'ASSISTANCE') style="display:none;" @endif>
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="operationControlAssistanceNumber">No.Surat BKO</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="operationControlAssistanceNumber" type="text"
                                    class="form-control @error('operationControlAssistanceNumber') is-invalid @enderror font-weight-bold" name="operationControlAssistanceNumber"
                                    value="{{old('operationControlAssistanceNumber')}}" placeholder="">

                                @error('operationControlAssistanceNumber')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="operationControlAssistanceDate">Tanggal Penugasan BKO</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="operationControlAssistanceDate" type="text"
                                    class="form-control @error('operationControlAssistanceDate') is-invalid @enderror font-weight-bold" name="operationControlAssistanceDate"
                                    value="{{old('operationControlAssistanceDate')}}" placeholder="YYYY-MM-DD">

                                @error('operationControlAssistanceDate')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="operationControlAssistancePolice">Satker Tujuan BKO</label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="operationControlAssistancePoliceName" type="text"
                                    class="form-control @error('operationControlAssistancePoliceName') is-invalid @enderror font-weight-bold" name="operationControlAssistancePoliceName"
                                    value="{{old('operationControlAssistancePoliceName')}}" readonly placeholder="">

                                @error('operationControlAssistancePoliceName')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <input id="operationControlAssistancePoliceId" type="hidden"
                                    class="form-control font-weight-bold" name="operationControlAssistancePoliceId"
                                    value="{{old('operationControlAssistancePoliceId')}}">
                            </div>
                        </div>
                        
                        <hr/>
                        <div id="operationControlAssistancePoliceSection" class="mt-4">
                            <h6 class="fw-bold">WILAYAH HUKUM (SATKER TUJUAN)</h6>
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-2 col-form-label" for="operationControlAssistancePolice">Masukkan Satker tujuan Mutasi</label>
                                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                    <div class="row">
                                        <div class="col-12">
                                            {{-- <div class="icheck-primary d-inline mx-1">
                                                <input type="radio" id="operationControlAssistancePolicePusatClass" name="operationControlAssistancePoliceClass" value="PUSAT" checked>
                                                <label for="operationControlAssistancePolicePusatClass">
                                                    Korlantas
                                                </label>
                                            </div> --}}

                                            <div class="icheck-primary d-inline mx-1">
                                                <input type="radio" id="operationControlAssistancePoliceDaerahClass" name="operationControlAssistancePoliceClass" value="DAERAH">
                                                <label for="operationControlAssistancePoliceDaerahClass">
                                                    Polda
                                                </label>
                                            </div>
                                        
                                            <div class="icheck-primary d-inline mx-1">
                                                <input type="radio" id="operationControlAssistancePoliceResorClass" name="operationControlAssistancePoliceClass" value="RESOR" checked>
                                                <label for="operationControlAssistancePoliceResorClass">
                                                    Polres
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <br/>
                                        <div class="row mt-1">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="operationControlAssistancePoliceSearchField"
                                                    placeholder="Cari Nama Satker tujuan" aria-label="Cari Nama Satker tujuan" aria-describedby="basic-addon2"
                                                    aria-describedby="operationControlAssistancePoliceSearch">
                                                <button class="btn btn-primary" id="operationControlAssistancePoliceSearchButton" type="button"><i
                                                        class="bi bi-search"></i> Cari </button>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <select class="custom-select select2-input-group" id="operationControlAssistancePoliceSearchOption"
                                                    aria-describedby="operationControlAssistancePoliceSearchOptionAddButtton">
                                                    <option value="">--Pilih Satker tujuan--</option>
                                                </select>
                                                <button class="btn btn-primary" id="operationControlAssistancePoliceSearchOptionAddButtton"
                                                    type="button"><i class="bi bi-plus-circle"></i> Pilih</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-center">
                        <div class="m-1">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                        <div class="m-1">
                            <a href="{{ route('personnel.index', ['policeId' => $policeId]) }}" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            
            $('.select2-input-group').select2({
                theme: 'bootstrap4',
            });
        });

        //datepicker
        $(document).ready(function() {
            $('#operationControlAssistanceDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
            });
            $('#operationControlAssistanceDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });
        });

        $(document).ready(function() {
            $('input[type=radio][name=mutationType]').change(function() {
                if (this.value == 'PRESENT') {
                    $('#presentMutationTypeSection').show();
                    $('#operationControlAssistanceSection').hide();
                }else if(this.value == 'ASSISTANCE') {
                    $('#operationControlAssistanceSection').show();
                    $('#presentMutationTypeSection').hide();
                }else{
                    $('#presentMutationTypeSection').hide();
                    $('#operationControlAssistanceSection').hide();
                }
            });

            $('#regionalPolice').change(function() {
                var regionalPoliceId = $(this).val();
                var url = "{{ route('personnel.api.polices', ['policeId' => $policeId]) }}";

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        policeId: regionalPoliceId,
                        policeClass: 'RESOR'
                    },
                    success: function(response) {
                        $('#resortPolice').empty();
                        $('#resortPolice').append('<option value="">--Pilih Polres--</option>');

                        $.each(response.data, function(key, value) {
                            $('#resortPolice').append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                        });
                    }
                });
            });

            $('#operationControlAssistancePoliceSearchButton').on('click', function() {
                var searchField = $('#operationControlAssistancePoliceSearchField').val();
                var searchOption = $('#operationControlAssistancePoliceSearchOption').val();
                var policeClass = $('input[name="operationControlAssistancePoliceClass"]:checked').val();

                if (searchField == '') {
                    //return sweet alert
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Silahkan isi kolom pencarian terlebih dahulu!'
                    });
                } 

                //call ajax to get ranks
                $.ajax({
                    url: "{{ route('personnel.api.polices.search', ['policeId' => $policeId]) }}",
                    type: "GET",
                    data: {
                        policeNameKeyword: searchField,
                        policeClass: policeClass,
                    },
                    success: function(response) {
                        $('#operationControlAssistancePoliceSearchOption').empty();

                        if (response.data.length == 0) {
                            $('#operationControlAssistancePoliceSearchOption').append(
                                '<option value="" selected disabled>--Tidak ada data--</option>');

                            return Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Data tidak ditemukan!'
                            });
                        }

                        $.each(response.data, function(key, value) {
                            var parent = (value.parent != null) ? value.parent.full_name : '';
                            var text =  (parent != '') ? value.full_name + ' - ' + parent : value.full_name;

                            $('#operationControlAssistancePoliceSearchOption').append(
                                '<option value="' + value.id + '" data-police-name="' + text + '">' + text + '</option>');
                        });
                    },
                    error: function(xhr) {
                        //sweet alert
                        return Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong! Please try again later.'
                        });
                    }
                });
            });
            $('#operationControlAssistancePoliceSearchOptionAddButtton').on('click', function() {
                var policeId = $('#operationControlAssistancePoliceSearchOption').find(':selected').val();
                var policeName = $('#operationControlAssistancePoliceSearchOption').find(':selected').data('police-name');

                if (policeId == '') {
                    //return sweet alert
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Silahkan pilih opsi pencarian terlebih dahulu!'
                    });
                } 

                $('#operationControlAssistancePoliceName').val(policeName);
                $('#operationControlAssistancePoliceId').val(policeId);
            });

            $('#presentMutationTypePoliceSearchButton').on('click', function() {
                var searchField = $('#presentMutationTypePoliceSearchField').val();
                var searchOption = $('#presentMutationTypePoliceSearchOption').val();
                var policeClass = $('input[name="presentMutationTypePoliceClass"]:checked').val();

                if (searchField == '') {
                    //return sweet alert
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Silahkan isi kolom pencarian terlebih dahulu!'
                    });
                } 

                //call ajax to get ranks
                $.ajax({
                    url: "{{ route('personnel.api.polices.search', ['policeId' => $policeId]) }}",
                    type: "GET",
                    data: {
                        policeNameKeyword: searchField,
                        policeClass: policeClass,
                    },
                    success: function(response) {
                        $('#presentMutationTypePoliceSearchOption').empty();

                        if (response.data.length == 0) {
                            $('#presentMutationTypePoliceSearchOption').append(
                                '<option value="" selected disabled>--Tidak ada data--</option>');

                            return Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Data tidak ditemukan!'
                            });
                        }

                        $.each(response.data, function(key, value) {
                            var parent = (value.parent != null) ? value.parent.full_name : '';
                            var text =  (parent != '') ? value.full_name + ' - ' + parent : value.full_name;

                            $('#presentMutationTypePoliceSearchOption').append(
                                '<option value="' + value.id + '" data-police-name="' + text + '">' + text + '</option>');
                        });
                    },
                    error: function(xhr) {
                        //sweet alert
                        return Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong! Please try again later.'
                        });
                    }
                });
            });
            $('#presentMutationTypePoliceSearchOptionAddButtton').on('click', function() {
                var policeId = $('#presentMutationTypePoliceSearchOption').find(':selected').val();
                var policeName = $('#presentMutationTypePoliceSearchOption').find(':selected').data('police-name');

                if (policeId == '') {
                    //return sweet alert
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Silahkan pilih opsi pencarian terlebih dahulu!'
                    });
                } 

                $('#presentMutationTypePoliceName').val(policeName);
                $('#presentMutationTypePoliceId').val(policeId);
            });
        });
    </script>
@endpush
