@extends('layouts.app')

@section('content')
    <div class="back-button mb-1">
        <a href="{{ route('role-new') }}"><i class="bi bi-arrow-left"></i> Kembali ke Role New Management</a>
    </div>
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">View Role</h3>
        </div>
        <div class="box-body">
            
            <div class="row form-group align-items-center my-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold m-0">Level :</label>
                </div>
                <div class="col-md-10">
                    <p class="m-0 form-control-plaintext">{{ $role['level'] ?? '' }}</p>
                </div>
            </div>

            <div class="row form-group align-items-center my-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold m-0">Nama Role :</label>
                </div>
                <div class="col-md-10">
                    <p class="m-0 form-control-plaintext fw-bold text-primary">{{ $role['name'] ?? '' }}</p>
                </div>
            </div>

            <div class="row form-group my-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold mt-2">Deskripsi :</label>
                </div>
                <div class="col-md-10">
                    <p class="m-0 form-control-plaintext">{{ $role['description'] ?? '-' }}</p>
                </div>
            </div>



            @include('role-new.partials.permission-matrix', [
                'isReadOnly'      => true,
                'rolePermissions' => $rolePermissions ?? []
            ])

        </div>
    </div>
@endsection
