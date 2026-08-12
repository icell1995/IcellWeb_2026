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
                <h3 class="fw-bold text-blue-dark">Jabatan</h3>
            </div>
            <div class="boxy-body mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <a href="{{route('cms.libs.position.create')}}" class="btn btn-dark-blue">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                            </a>
                        </div>
                        <div class="mt-3 table-responsive">
                            <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%" id="positionsTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">EMP ID</th>
                                        <th class="text-center">Code</th>
                                        <th class="text-center">Jenis</th>
                                        <th class="text-center">Cluster</th>
                                        <th class="text-center">Sort</th>
                                        <th class="text-center">Active?</th>
                                        <th class="text-center">Dapat TTE?</th>
                                        <th class="text-center">Police</th>
                                        <th class="text-center">Opsi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {{-- @foreach ($positions as $position)
                                        <tr class="">
                                            <td class="text-center align-middle">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-center align-middle">
                                                {{ $position->id }}
                                            </td>
                                            <td>
                                                {{ $position->name }}
                                            </td>
                                            <td>
                                                {{ $position->emp_id }}
                                            </td>
                                            <td>
                                                {{ $position->code }}
                                            </td>
                                            <td>
                                                {{ $position->employmentType->name ?? '' }}
                                            </td>
                                            <td>
                                                {{ $position->positionCluster->name ?? '' }}
                                            </td>
                                            <td>
                                                {{ $position->sort }}
                                            </td>
                                            <td>
                                                {{ ($position->is_active) ? 'Ya' : 'Tidak' }}
                                            </td>
                                            <td>
                                                Position : {{ ($position->is_can_signatory) ? 'Ya' : 'Tidak' }} <br/>
                                                Cluster :  {{ ($position->positionCluster->is_can_signatory) ? 'Ya' : 'Tidak' }}
                                            </td>
                                            <td>
                                                {{ ($position->police->name) ?? '' }} - {{ ($position->police->id) ?? '' }}
                                            </td>
                                            
                                            <td>
                                                <a href="{{ route('cms.libs.position.edit', ['id' => $position->id]) }}" class="btn btn-sm btn-warning m-1">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a href="{{ route('cms.libs.position.delete', ['id' => $position->id]) }}"
                                                    class="btn btn-danger btn-sm m-1" data-method="delete"
                                                    data-token="{{ csrf_token() }}"
                                                    data-confirm="Apakah Anda yakin ingin menghapus ini?"><i
                                                        class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach --}}
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

    <script>
        $(document).ready(function() {
            $('#positionsTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ url()->current() }}',
                stateSave: true,
                columns: [
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            // Calculate the iteration number
                            var pageInfo = $('#positionsTable').DataTable().page.info();
                            var iteration = meta.row + 1 + pageInfo.start;
                            return iteration;
                        }
                    },
                    { data: 'id'},
                    { data: 'name'},
                    { data: 'emp_id'},
                    { data: 'code'},
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            return (data.employment_type) ? data.employment_type.name : '';
                        }
                    },
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            return (data.position_cluster) ? data.position_cluster.name : '';
                        }
                    },
                    { data: 'sort'},
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            return (data.is_active) ? 'Ya' : 'Tidak';
                        }
                    },
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            var position = (data.is_can_signatory) ? 'Ya' : 'Tidak';
                            var cluster = (data.position_cluster.is_can_signatory) ? 'Ya' : 'Tidak'; 

                            return 'Position : ' + position + '<br/> Cluster : ' + cluster;
                        }
                    },
                    { 
                        data: null,
                        render: function (data, type, row, meta) {
                            var policeName = (data.police) ? data.police.name : '';
                            var policeId = (data.police) ? data.police.id : '';
                            return policeName + ' - ' + policeId;
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            // Render multiple buttons
                            var buttons = '';

                            // Generate the URLs dynamically using the data from the row
                            var editUrl = '{{ route("cms.libs.position.edit", ":id") }}'.replace(':id', data.id);
                            var deleteUrl = '{{ route("cms.libs.position.delete", ":id") }}'.replace(':id', data.id);

                            buttons += '<a href="' + editUrl + '" class="btn btn-sm btn-warning m-1">';
                            buttons += '<i class="bi bi-pencil-square"></i>';
                            buttons += '</a>';

                            buttons += '<button class="btn btn-danger btn-sm m-1 delete-button" data-id="' + data.id + '" data-url="' + deleteUrl + '" data-token="{{ csrf_token() }}" data-confirm="Apakah Anda yakin ingin menghapus ini?">';
                            buttons += '<i class="bi bi-trash"></i>';
                            buttons += '</button>';

                            return buttons;
                        }
                    }
                ]
            });
        });

        $(document).on('click', '.delete-button', function (e) {
            e.preventDefault();

            var button = $(this);
            var deleteUrl = button.data('url');
            var token = button.data('token');
            var confirmMessage = button.data('confirm');

            Swal.fire({
                title: 'Are you sure?',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: token
                        },
                        success: function (response) {
                            Swal.fire(
                                'Deleted!',
                                'The record has been deleted.',
                                'success'
                            );
                            $('#positionsTable').DataTable().ajax.reload();
                        },
                        error: function (xhr, status, error) {
                            Swal.fire(
                                'Failed!',
                                'There was an error deleting the record.',
                                'error'
                            );
                        }
                    });
                }
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

        <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>
@endpush
