@extends('layouts.app')

@section('content')
    <div class="back-button mb-1">
        <a href="{{ route('role-new') }}"><i class="bi bi-arrow-left"></i> Kembali ke Role Management</a>
    </div>
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Edit Role</h3>
        </div>
        <div class="box-body">
            <form id="roleForm" action="{{ route('role-new-update', $role['id'] ?? $role->id) }}" method="POST">
                @csrf
                <div class="row form-group align-items-center my-3">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold m-0">Level :</label>
                    </div>
                    <div class="col-md-10">
                        <input type="number" class="form-control" value="{{ $role['level'] ?? '' }}" disabled>
                    </div>
                </div>

                <div class="row form-group align-items-center my-3">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold m-0">Nama Role :</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $role['name'] ?? '') }}" required>
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
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $role['description'] ?? '') }}</textarea>
                    </div>
                </div>

                @include('role-new.partials.permission-matrix', [
                    'rolePermissions' => $rolePermissions ?? []
                ])

                <div id="actionButtons" class="mt-4">
                    <button type="submit" class="btn btn-dark-blue me-2">Simpan Perubahan</button>
                    <a href="{{ route('role-new') }}" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
