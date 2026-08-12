@php
    $_title = 'Tambah Pejabat TTE';
@endphp


@extends('layouts.app')

@push('style')
    <!-- Select2 CSS-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark">Tambah Pejabat TTE</h3>

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
        </div>

        <div class="box-body mt-4">
            <div class="my-3">
                <a href="{{ route('signatories') }}" class="btn btn-danger fw-bold"><i class="bi bi-arrow-left"></i>
                    Back</a>
            </div>
            <form action="{{ route('signatories.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold" for="districtPoliceId">Polres</label>
                            <select class="form-control @error('districtPoliceId') is-invalid @enderror select2"
                                id="districtPoliceId" name="districtPoliceId">
                                <option value="">--Pilih Polres--</option>
                                @foreach ($regionalPolices as $regionalPolice)
                                    <optgroup label="{{ $regionalPolice->name }}">
                                        @foreach ($regionalPolice->polres as $districtPolice)
                                            <option value="{{ $districtPolice->id }}"
                                                {{ old('districtPoliceId') == $districtPolice->id ? 'selected' : '' }}>
                                                {{ '[' . $districtPolice->id . '] ' . $districtPolice->name . ' (' . $regionalPolice->name . ')' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>

                            <!-- Foot Note -->
                            @error('districtPoliceId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="fw-bold" for="firstTitle">Gelar Pendidikan Depan</label>
                                    <input type="text" class="form-control @error('firstTitle') is-invalid @enderror"
                                        id="firstTitle" name="firstTitle" value="{{ old('firstTitle') }}"
                                        placeholder="Gelar Depan">

                                    <!-- Foot Note -->
                                    <small id="firstTitleHelp" class="form-text text-muted">Perhatikan Huruf Dan Tanda
                                        Baca</small>
                                    @error('firstTitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="fw-bold" for="firstName">Nama Depan</label>
                                    <input type="text" class="form-control @error('firstName') is-invalid @enderror"
                                        id="firstName" name="firstName" value="{{ old('firstName') }}"
                                        placeholder="Nama Depan">

                                    <!-- Foot Note -->
                                    <small id="firstNameHelp" class="form-text text-muted">Perhatikan Penulisan Huruf Dan
                                        Tanda Baca</small>
                                    @error('firstName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="fw-bold" for="lastName">Nama Belakang (Opsional Jika Ada)</label>
                                    <input type="text" class="form-control @error('lastName') is-invalid @enderror"
                                        id="lastName" name="lastName" value="{{ old('lastName') }}"
                                        placeholder="Nama Belakang">

                                    <!-- Foot Note -->
                                    <small id="lastNameHelp" class="form-text text-muted">Perhatikan Penulisan Huruf Dan
                                        Tanda Baca</small>
                                    @error('lastName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="fw-bold" for="lastTitle">Gelar Pendidikan Belakang</label>
                                    <input type="text" class="form-control @error('lastTitle') is-invalid @enderror"
                                        id="lastTitle" name="lastTitle" value="{{ old('lastTitle') }}"
                                        placeholder="Gelar Belakang">

                                    <!-- Foot Note -->
                                    <small id="lastTitleHelp" class="form-text text-muted">Perhatikan Huruf Dan Tanda
                                        Baca</small>
                                    @error('lastTitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="rankId">Pangkat</label>
                            <select class="form-control @error('rankId') is-invalid @enderror select2" id="rankId"
                                name="rankId">
                                <option value="">--Pilih Pangkat--</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank['id'] }}"
                                        {{ old('rankId') == $rank['id'] ? 'selected' : '' }}>
                                        {{ $rank['name'] . ' (' . $rank['id'] . ')' }}</option>
                                @endforeach
                            </select>

                            <!-- Foot Note -->
                            @error('rankId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="registerNumber">NRP</label>
                            <input type="text" class="form-control @error('registerNumber') is-invalid @enderror"
                                id="registerNumber" name="registerNumber" value="{{ old('rank') }}" placeholder="NRP">
                            @error('registerNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Foot Note -->
                            <small id="registerNumberHelp" class="form-text text-muted">Perhatikan Jumlah Dan Format
                                Angka</small>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="positionId">Jabatan</label>
                            <select class="form-control @error('positionId') is-invalid @enderror select2" id="positionId"
                                name="positionId">
                                <option value="">--Pilih Jabatan--</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position['id'] }}"
                                        {{ old('positionId') == $position['id'] ? 'selected' : '' }}>
                                        {{ $position['name'] }}</option>
                                @endforeach
                            </select>

                            <!-- Foot Note -->
                            @error('positionId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="identityNumber">NIK</label>
                            <input type="text" class="form-control @error('identityNumber') is-invalid @enderror"
                                id="identityNumber" name="identityNumber" value="{{ old('identityNumber') }}"
                                placeholder="Nomor Induk Kependudukan">

                            <!-- Foot Note -->
                            <small id="identityNumberHelp" class="form-text text-muted">Perhatikan Format Angka Nomor
                                Induk Kependudukan Pejabatan Penandatangan.</small>
                            @error('identityNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="email">Email Polri (OPSIONAL Jika Ada)</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="xxxxxxxxxx@polri.go.id">

                            <!-- Foot Note -->
                            <small id="emailHelp" class="form-text text-muted">Email Official Polri Pejabatan
                                Penandatangan.</small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="phone">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">

                            <!-- Foot Note -->
                            <small id="phoneHelp" class="form-text text-muted">Nomor Telepon Pejabatan
                                Penandatangan.</small>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="isValid" name="isValid">
                                    <label class="fw-bold" class="form-check-label" for="isValid">Tandai Valid</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="isOpenUserForm"
                                        name="isOpenUserForm">
                                    <label class="fw-bold" class="form-check-label" for="isOpenUserForm">Buka Form Pengisian Pejabat
                                        TTE</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue"><i class="bi bi-save text-white"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <!-- Select2 -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Own Scripts -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic'
            });
        });
    </script>
@endpush
