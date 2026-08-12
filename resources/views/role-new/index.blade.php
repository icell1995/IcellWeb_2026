@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Role Management</h3>
        </div>
        <div class="box-body">
            <div class="text-end">
                @if(Auth::user()->hasPermission('role.C'))
                <a href="{{ route('role-new-add') }}" class="btn btn-dark-blue">Tambah Role Baru</a>
                @endif
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" width="100%" id="dataTable" name="dataTable">
                    <thead style="background-color: #2F4288; color:#fff; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th class="text-center align-middle" width="5%" style="background-color: #2F4288;">No</th>
                            <th class="text-center align-middle" width="10%" style="background-color: #2F4288;">Level Role</th>
                            <th class="text-center align-middle" width="20%" style="background-color: #2F4288;">Nama Role</th>
                            <th class="text-center align-middle" width="40%" style="background-color: #2F4288;">Deskripsi</th>
                            <th class="text-center align-middle" width="15%" style="background-color: #2F4288;">Jumlah User</th>
                            <th class="text-center align-middle" width="10%" style="background-color: #2F4288;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $dt)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $dt['level'] }}</td>
                            <td class="text-center fw-semibold text-blue-dark">{{ $dt['name'] }}</td>
                            <td>{{ $dt['description'] }}</td>
                            <td class="text-center">
                                {{ $dt['user_count'] }} User
                            </td>
                            <td class="text-center">
                                @if(($dt['level'] ?? 0) == 2)
                                    {{-- Level 2: hanya bisa view, tidak bisa diedit --}}
                                    <a href="{{ route('role-new-view', $dt['id']) }}" title="Lihat Detail"><i class="bi bi-eye text-blue-dark fs-5"></i></a>
                                @elseif(Auth::user()->hasPermission('role.U'))
                                    <a href="{{ route('role-new-edit', $dt['id']) }}" title="Edit Role"><i class="bi bi-pencil-square text-blue-dark fs-5"></i></a>
                                @else
                                    <a href="{{ route('role-new-view', $dt['id']) }}" title="Lihat Detail"><i class="bi bi-eye text-blue-dark fs-5"></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
