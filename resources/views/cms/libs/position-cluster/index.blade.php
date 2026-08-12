@extends('cms.layouts.app')

@section('_title', 'Jabatan')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')    
    <div class="loaderbg" style="display:none"></div>
        <div class="box">
            <div class="box-header">
                <h3 class="fw-bold text-blue-dark">Cluster Jabatan</h3>
            </div>
            <div class="boxy-body mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <a href="{{route('cms.libs.position-cluster.create')}}" class="btn btn-dark-blue">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Cluster Jabatan
                            </a>
                        </div>
                        <div class="mt-3 table-responsive">
                            <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Code</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Sort</th>
                                        <th class="text-center">Active?</th>
                                        <th class="text-center">Dapat TTE?</th>
                                        <th class="text-center">Opsi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($positionClusters as $positionCluster)
                                        <tr class="">
                                            <td class="text-center align-middle">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-center align-middle">
                                                {{ $positionCluster->id }}
                                            </td>
                                            <td>
                                                {{ $positionCluster->code }}
                                            </td>
                                            <td>
                                                {{ $positionCluster->name }}
                                            </td>
                                            <td>
                                                {{ $positionCluster->sort }}
                                            </td>
                                            <td>
                                                {{ ($positionCluster->is_active) ? 'Ya' : 'Tidak' }}
                                            </td>
                                            <td>
                                                {{ ($positionCluster->is_can_signatory) ? 'Ya' : 'Tidak' }}
                                            </td>
                                            
                                            <td>
                                                <a href="{{ route('cms.libs.position-cluster.edit', ['id' => $positionCluster->id]) }}" class="btn btn-sm btn-warning m-1">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a href="{{ route('cms.libs.position-cluster.delete', ['id' => $positionCluster->id]) }}"
                                                    class="btn btn-danger btn-sm m-1" data-method="delete"
                                                    data-token="{{ csrf_token() }}"
                                                    data-confirm="Apakah Anda yakin ingin menghapus ini?"><i
                                                        class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@endpush
