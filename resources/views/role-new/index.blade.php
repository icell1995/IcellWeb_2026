@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Role Management</h3>
        </div>
        <div class="box-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="text-end">
                @if(Auth::user()->hasPermission('role.C'))
                <a href="{{ route('role-new-add') }}" class="btn btn-dark-blue">Tambah Role Baru</a>
                @endif
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" width="100%" id="dataTable" name="dataTable">
                    <thead style="background-color: #2F4288; color:#fff; position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th class="text-center align-middle" width="10%" style="background-color: #2F4288;">Level Role</th>
                            <th class="text-center align-middle" width="25%" style="background-color: #2F4288;">Nama Role</th>
                            <th class="text-center align-middle" width="40%" style="background-color: #2F4288;">Deskripsi</th>
                            <th class="text-center align-middle" width="15%" style="background-color: #2F4288;">Jumlah User Aktif</th>
                            <th class="text-center align-middle" width="10%" style="background-color: #2F4288;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $dt)
                        <tr>
                            <td class="text-center">{{ $dt['id'] }}</td>
                            <td class="text-center fw-semibold text-blue-dark">{{ $dt['name'] }}</td>
                            <td>{{ $dt['description'] }}</td>
                            <td class="text-center">
                                {{ $dt['user_count'] }} User
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if(($dt['id'] ?? 0) == 2)
                                        {{-- Level 2: hanya bisa view, tidak bisa diedit --}}
                                        <a href="{{ route('role-new-view', $dt['id']) }}" title="Lihat Detail"><i class="bi bi-eye text-blue-dark fs-5"></i></a>
                                    @elseif(Auth::user()->hasPermission('role.U'))
                                        <a href="{{ route('role-new-edit', $dt['id']) }}" title="Edit Role"><i class="bi bi-pencil-square text-blue-dark fs-5"></i></a>
                                        
                                        @if(!in_array($dt['id'], [1, 2, 3, 4, 5]))
                                            <form action="{{ route('role-new-delete', $dt['id']) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role &quot;{{ $dt['name'] }}&quot;?');">
                                                @csrf
                                                <button type="submit" class="btn p-0 border-0 bg-transparent lh-1" title="Hapus Role">
                                                    <i class="bi bi-trash text-danger fs-5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('role-new-view', $dt['id']) }}" title="Lihat Detail"><i class="bi bi-eye text-blue-dark fs-5"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
