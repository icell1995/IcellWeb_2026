@php
    $_title = 'Register Perkara Jatanlin';
@endphp


@extends('layouts.app')

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card">
            <div class="card-body">
                <h3 class="fw-bold text-center text-blue-dark">REGISTER PERKARA JATANLIN</h3>

                <div class="mt-4">
                    <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                        <thead>
                            @php
                                $user_polres_id = auth()->user()->polres_id;
                                $user_role_id = auth()->user()->role_id;
                            @endphp
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nomor LP</th>
                                <th class="text-center">Nama Polres</th>
                                @if ($user_role_id === 1 || $user_role_id === 2)
                                    <th class="text-center">Selra</th>
                                @endif
                                {{-- <th class="text-center">Nama Petugas Pelapor</th> --}}
                                <th class="text-center">Tanggal Kejadian</th>
                                <th class="text-center">Tanggal Dilaporkan</th>
                                <th class="text-center">No Plat</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $user_polres_id = auth()->user()->polres_id;
                                $user_role_id = auth()->user()->role_id;
                            @endphp

                            @foreach ($cases['result'] as $case)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="text-center">
                                        {{ $case['no_lp'] ?? '' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $case['polres_name'] ?? '' }}
                                    </td>

                                    @if ($user_role_id === 1 || $user_role_id === 2)
                                        <td class="text-center align-middle">
                                            {{ $case['selra_name'] ?? '-' }}
                                        </td>
                                    @endif
                                   
                                    {{-- <td class="text-center align-middle">

                                    </td> --}}

                                    <td class="text-center align-middle">
                                        {{ $case['accident_date'] ? Carbon\Carbon::parse($case['accident_date'])->locale('id')->translatedFormat('d F Y') : '' }}

                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $case['report_date'] ? Carbon\Carbon::parse($case['report_date'])->locale('id')->translatedFormat('d F Y') : '' }}

                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $case['plate_no'] ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if($user_polres_id == $case['polres_id'] || in_array($user_role_id, [1]))
                                            <a href="{{ route('case.show', ['id' => $case['id']]) }}"
                                                class="btn btn-primary">
                                                <i class="bi bi-eye bi-lg"></i> Tindak Lanjut
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    {{-- DataTables Buttons CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endpush

{{-- @push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
            });
        });
    </script>
@endpush --}}

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            let dtButtons = [];
            @if(Auth::user()->hasPermission('case.E'))
                dtButtons.push({
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    titleAttr: 'Export ke Excel',
                    title: 'Register Perkara Jatanlin',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                });
            @endif

            $('.dataTable').DataTable({
                responsive: true,
                dom: dtButtons.length > 0 ? 'Bfrtip' : 'frtip', 
                buttons: dtButtons
            });
        });
    </script>
@endpush
