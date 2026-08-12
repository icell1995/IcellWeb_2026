@extends('layouts.app')

@section('content')
    <div class="back-button mb-1">
        <a href="{{ route('role-new') }}"><i class="bi bi-arrow-left"></i> Kembali ke Role New Management</a>
    </div>
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Tambah Role Baru</h3>
        </div>
        <div class="box-body">
            <form id="roleForm" action="{{ route('role-new-store') }}" method="POST">
                @csrf
                <div class="row form-group align-items-center my-3">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold m-0">Level :</label>
                    </div>
                    <div class="col-md-10">
                        <input type="number" name="level" class="form-control {{ $errors->has('level') ? 'is-invalid' : '' }}" placeholder="Contoh: 6" value="{{ old('level') }}" required>
                        @error('level')
                            <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-group align-items-center my-3">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold m-0">Nama Role :</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Masukkan nama Role" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-group my-3">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold mt-2">Deskripsi :</label>
                    </div>
                    <div class="col-md-10">
                        <textarea name="description" class="form-control" rows="3" placeholder="Deskripsikan hak akses role ini..." required>{{ old('description') }}</textarea>
                    </div>
                </div>

                @include('role-new.partials.permission-matrix', ['isSuperAdmin' => false])

                <div class="mt-4">
                    <button type="submit" class="btn btn-dark-blue me-2">Save</button>
                    <a href="{{ route('role-new') }}" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
