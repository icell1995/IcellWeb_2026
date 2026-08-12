@extends('layouts.app')

@section('content')
    <div class="back-button mb-1">
        <a href="{{ route('role') }}"><i class="bi bi-arrow-left"></i> Kembali ke Roles Management</a>
    </div>
    <div class="box">
        <div class="box-header">
            @if (session('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ session('error') }}</strong>
                </div>
            @endif

            <h3 class="text-blue-dark fw-semibold mb-2">Edit Role Management</h3>
        </div>
        <div class="box-body">
            <form method="POST" action="{{ route('update') }}">
                @csrf

                <input id="id" type="text" class="form-control @error('name') is-invalid @enderror" name="id"
                    value="{{ $data2->id }}" required autocomplete="name" autofocus hidden>
                <div class="row form-group align-items-center my-3">
                    <div class="col-auto">
                        <label class="form-label fw-semibold m-0" for="">Name Role :</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" class="form-control" value="{{ $data2->name }}" disabled>
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
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($roles as $role)
                                @foreach ($permissions as $perm)
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="cb" name="permissions[]" id="permissions"
                                            style="margin-right: 10px" value="{{ $perm->id }}"
                                            {{ in_array($perm->id, $role->permissions->pluck('id')->toArray()) ? 'checked="checked"' : '' }}>
                                    </td>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-dark-blue" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection
