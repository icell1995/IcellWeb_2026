@extends('layouts.app')

@section('content')
    <div class="back-button mb-1">
        <a href="{{ route('role') }}"><i class="bi bi-arrow-left"></i> Kembali ke Roles Management</a>
    </div>
    <div class="box">
        <div class="box-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ups! </strong> Ada beberapa masalah dengan pengisian form yang Anda masukkan.
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3 class="text-blue-dark fw-semibold mb-2">Tambah Role Management</h3>
        </div>
        <div class="box-body">
            <form method="POST" action="{{ route('role_add') }}">
                @csrf
                <div class="row form-group align-items-center my-3">
                    <div class="col-auto">
                        <label class="form-label fw-semibold m-0" for="">Name Role :</label>
                    </div>
                    <div class="col-auto">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" id="dataTable" name="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">View Data</th>
                                <th class="text-center align-middle">Create Data</th>
                                <th class="text-center align-middle">Edit Data</th>
                                <th class="text-center align-middle">Update Data</th>
                                <th class="text-center align-middle">Delete Data</th>
                                <th class="text-center align-middle">Manage Users</th>
                                <th class="text-center align-middle">Manage Permissions</th>
                                <th class="text-center align-middle">Manage Roles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permission as $perm)
                                <td class="text-center align-middle">
                                    <input type="checkbox" value="{{ $perm->id }}" name="permission[]"><label
                                        for="{{ $perm->id }}">
                                </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-dark-blue" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
