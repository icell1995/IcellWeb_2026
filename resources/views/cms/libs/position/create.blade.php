@extends('cms.layouts.app')

@section('_title', 'Tambah Jabatan')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')

<a class="btn-back" href="{{ route('cms.libs.position.index') }}"><i class="bi bi-arrow-left"></i>Kembali ke Halaman Daftar Jabatan</a>

<div class="box">
    <div class="box-header">
        <h4 class="fw-bold text-blue-dark">Tambah Jabatan</h4>

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

    <div class="boxy-body">
        <form
            action="{{ route('cms.libs.position.store') }}"
            method="POST" enctype="multipart/form-data" id="positionForm">
            @csrf

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="id">ID</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id"
                        value="{{ old('id', $createPositionId) }}" required
                        placeholder="ID">
                    @error('id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="empId">EMP ID</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="empId" type="text" class="form-control @error('empId') is-invalid @enderror" name="empId"
                        value="{{ old('empId') }}"
                        placeholder="EMP ID">
                    @error('empId')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="name">Name</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" required
                        placeholder="Name">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="code">Code</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                        value="{{ old('code') }}"
                        placeholder="Code">
                    @error('code')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

             <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Jenis Kepegawaian</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <div class="d-flex">
                        <div class="form-check mx-1">
                            <input class="form-check-input" type="radio" id="typePoliceEmployment" name="employmentType"
                                value="1"
                                @if (old('employmentType') == 1 || empty(old('employmentType'))) checked @endif>
                            <label for="typePoliceEmployment">
                                Anggota Polri
                            </label>
                        </div>

                        <div class="form-check mx-1">
                            <input class="form-check-input" type="radio" id="typeCivilEmployment" name="employmentType"
                                value="2"
                                @if (old('employmentType') == 2)
                                        checked @endif>
                            <label for="typeCivilEmployment">
                                PNS Polri
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Cluster Jabatan</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <select class="form-control select2" name="positionCluster" id="positionCluster">
                        <option value="">--Pilih Cluster Jabatan--</option>
                        @foreach ($positionClusters as $positionCluster)
                            <option value="{{ $positionCluster->id }}"
                                {{ (old('positionCluster') == $positionCluster->id) ? 'selected' : '' }}>
                                {{ $positionCluster->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('positionCluster')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Police</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                    <select class="form-control select2" name="police" id="police">
                        <option value="">--Pilih Police--</option>
                        @foreach ($polices as $police)
                            <option value="{{ $police->id }}"
                                {{ (old('police') == $police->id) ? 'selected' : '' }}>
                                {{ $police->name }} - ({{ $police->class }})
                            </option>
                        @endforeach
                    </select>

                    @error('police')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="sort">Sort</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input id="sort" type="text" class="form-control @error('sort') is-invalid @enderror" name="sort"
                        value="{{ old('sort', 0) }}"
                        placeholder="Sort">
                    @error('sort')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Active?</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isActive"
                            name="isActive" value="true" aria-label="..."
                            @if (old('isActive', 'true') == 'true') checked @endif>
                        <label for="isActive">
                            Aktif
                        </label>
                    </div>

                    @error('isActive')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Pejabat TTE?</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isCanSignatory"
                            name="isCanSignatory" value="true" aria-label="..."
                            @if (old('isCanSignatory') == 'true') checked @endif>
                        <label for="isCanSignatory">
                            Daftar sebagai pejabat TTE
                        </label>
                    </div>

                    @error('isCanSignatory')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary" id="positionFormSubmit">
                    <i class="bi bi-save"></i> {{ __('Simpan') }}
                </button>
                <a href="{{ route('cms.libs.position.index') }}" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                </a>
            </div>
        </form>
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
            $('.dataTable').DataTable({
                responsive: true,
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@endpush