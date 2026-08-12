@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Role Management</h3>
        </div>
        <div class="box-body">
            <div class="text-end">
                @if(Auth::user()->hasPermission('role.C'))
                <a href={{route('role-add')}} class="btn btn-dark-blue">Tambah Roles Management</a>
                @endif
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">View Data</th>
                            <th class="text-center">Create Data</th>
                            <th class="text-center">Edit Data</th>
                            <th class="text-center">Update Data</th>
                            <th class="text-center">Delete Data</th>
                            <th class="text-center">Manage User</th>
                            <th class="text-center">Manage Permissions</th>
                            <th class="text-center">Manage Role</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($data as $dt)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="text-center">{{ $dt->name }}</td>
                                <td class="text-center">{!! $dt->permissions->contains('id', '1')
                                    ? '<i class="bi bi-check-square-fill text-success"></i>'
                                    : '<i class="bi bi-x-square-fill text-danger"></i>' !!}
                                </td>
                                <td class="text-center">{!! $dt->permissions->contains('id', '2')
                                    ? '<i class="bi bi-check-square-fill text-success"></i>'
                                    : '<i class="bi bi-x-square-fill text-danger"></i>' !!}
                                </td>
                                <td class="text-center">{!! $dt->permissions->contains('id', '3')
                                    ? '<i class="bi bi-check-square-fill text-success"></i>'
                                    : '<i class="bi bi-x-square-fill text-danger"></i>' !!}
                                </td>
                                <td class="text-center">{!! $dt->permissions->contains('id', '4')
                                    ? '<i class="bi bi-check-square-fill text-success"></i>'
                                    : '<i class="bi bi-x-square-fill text-danger"></i>' !!}
                                </td>
                                <td class="text-center">{!! $dt->permissions->contains('id', '5')
                                    ? '<i class="bi bi-check-square-fill text-success"></i>'
                                    : '<i class="bi bi-x-square-fill text-danger"></i>' !!}
                                </td>
                                <td class="text-center">
                                    {!! $dt->permissions->contains('id', '6')
                                        ? '<i class="bi bi-check-square-fill text-success"></i>'
                                        : '<i class="bi bi-x-square-fill text-danger"></i>' !!}</td>
                                <td class="text-center">
                                    {!! $dt->permissions->contains('id', '7')
                                        ? '<i class="bi bi-check-square-fill text-success"></i>'
                                        : '<i class="bi bi-x-square-fill text-danger"></i>' !!}</td>
                                <td class="text-center">
                                    {!! $dt->permissions->contains('id', '8')
                                        ? '<i class="bi bi-check-square-fill text-success"></i>'
                                        : '<i class="bi bi-x-square-fill text-danger"></i>' !!}</td>
                                <td class="text-center">
                                    <a href="{{ url('role/edit/' . $dt->id) }}" title="Ubah Data"><i
                                            class="bi bi-pencil-square text-blue-dark fs-5"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
