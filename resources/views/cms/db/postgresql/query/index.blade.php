@extends('cms.layouts.app')

@section('_title', 'Postgresql - Query')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css"> --}}
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')    
    <div class="loaderbg" style="display:none"></div>
        <div class="box">
            <div class="box-header">
                <h3 class="fw-bold text-blue-dark">Postgresql - Query</h3>
            </div>
            <div class="boxy-body mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="fw-bold text-blue-dark mt-1">Query Execute</h4>
                    </div>
                    <div class="card-body">
                        <form id="queryForm" action="{{ route('cms.db.postgresql.query.execute') }}" method="POST">
                            @csrf
                            <input type="hidden" name="password" id="password">
                                <label for="connection" class="form-label">Connection</label>
                                <select class="form-select" aria-label="Default select" name="connection" id="connection">
                                    <option value="icell" {{(empty(old('connection')) || old('connection') == 'icell') ? 'selected' : ''}}>ICELL-DB</option>
                                </select>
                            <div class="mb-3">
                            </div>

                            <div class="mb-3">
                                <label for="queryText" class="form-label">Query</label>
                                <textarea class="form-control" id="queryText" name="queryText" rows="10">{{$queryText ?? null}}</textarea>
                            </div>

                            <button type="button" id="queryFormSave" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#saveModal"><i class="bi bi-save"></i> Save</button>
                            <button type="button" id="queryFormLoad" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#loadModal"><i class="bi bi-folder2-open"></i> Load</button>
                            <button type="button" id="queryFormSubmit" class="btn btn-primary"><i class="bi bi-command"></i> Execute</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="fw-bold text-blue-dark mt-1">Console</h4>
                    </div>
                    <div class="card-body">
                        @if(isset($message))
                            @dump($message)
                        @endif
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="fw-bold text-blue-dark mt-1">Result</h4>
                    </div>
                    <div class="card-body">
                        <div class="mt-3 table-responsive">
                            <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%" id="result">
                                <thead>
                                    <tr>
                                        @if(!empty($results))
                                            @foreach($results[0] as $key => $value)
                                                <th class="text-center">{{$key}}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @if(!empty($results))
                                        @foreach ($results as $result)
                                            <tr>
                                            @foreach ($result as $value)
                                                <td class="text-center align-middle">
                                                    {{ $value }}
                                                </td>
                                            @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveModalLabel">Save</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="form-group mb-4">
                        <label for="saved-query">Query Tersimpan</label>
                        <select class="form-control" id="saved-query">
                            <option value="" data-save-name="">--Pilih Query Tersimpan Jika Untuk Simpan Update--</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="save-id" class="col-form-label">ID</label>
                        <input type="text" class="form-control" id="save-id" name="save-id" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="save-name" class="col-form-label">Name</label>
                        <input type="text" class="form-control" id="save-name" name="save-name">
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="queryFormSaveSubmit">Save</button>
                </div>
            </div>
        </div>
    </div>
   
    <div class="modal fade" id="loadModal" tabindex="-1" aria-labelledby="loadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loadModalLabel">Load</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="form-group mb-4">
                        <label for="Load-query">Query List</label>
                        <select class="form-control" id="load-query">
                            <option value="" data-load-name="">--Pilih Query Tersimpan--</option>
                        </select>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="queryFormLoadSubmit">Load</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    {{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    {{-- <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script> --}}
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.js"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#result').DataTable({
                responsive: true,
                ordering: false,
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ]
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('#saved-query').select2({
                dropdownParent: $('#saved-query').parent(),
                theme: 'bootstrap4',
                width: '100%'
            });

            $('#load-query').select2({
                dropdownParent: $('#load-query').parent(),
                theme: 'bootstrap4',
                width: '100%'
            });
        });
        
    </script>

    <script>
        $(document).ready(function() {
            $('#queryFormLoad').on('click', function(e) {
                e.preventDefault();
                
                //ajax call saved query list
                $.ajax({
                    type: 'GET',
                    url: "{{ route('cms.db.postgresql.query.saved') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#load-query').empty();
                        $('#load-query').append('<option value="">--Pilih Query Tersimpan--</option>');
                        data = response.data;
                        //loop data
                        $.each(data, function(key, value) {
                            $('#load-query').append('<option value="' + value.id + '" data-save-name="' + value.title + '">' + value.title + '</option>');
                        });

                        /*$('#saved-query').select2({
                            dropdownParent: $('#savedQuery').parent(),
                            theme: 'bootstrap4',
                            width: '100%'
                        });*/
                    }
                });
            });

            $('#queryFormLoadSubmit').on('click', function(e) {
                e.preventDefault();
                var id = $('#load-query').val();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('cms.db.postgresql.query.saved') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(response) {
                        data = response.data;
                        $('#queryText').val(data.query);
                        $('#loadModal').modal('hide');
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#queryFormSave').on('click', function(e) {
                e.preventDefault();
                
                //ajax call saved query list
                $.ajax({
                    type: 'GET',
                    url: "{{ route('cms.db.postgresql.query.saved') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#saved-query').empty();
                        $('#saved-query').append('<option value="">--Pilih Query Tersimpan Jika Untuk Simpan Update--</option>');
                        data = response.data;
                        //loop data
                        $.each(data, function(key, value) {
                            $('#saved-query').append('<option value="' + value.id + '" data-save-name="' + value.title + '">' + value.title + '</option>');
                        });

                        /*$('#saved-query').select2({
                            dropdownParent: $('#savedQuery').parent(),
                            theme: 'bootstrap4',
                            width: '100%'
                        });*/
                    }
                });
            });

            $('#saved-query').on('change', function() {
                $('#save-id').val($(this).find(':selected').val());
                $('#save-name').val($(this).find(':selected').data('save-name'));
            });

            $('#queryFormSaveSubmit').click(function(e) {
                e.preventDefault();
                
                var queryText = $('#queryText').val();
                queryText = queryText.replace(/\r\n/g, "\n");
                var saveName = $('#save-name').val();
                var saveId = $('#save-id').val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('cms.db.postgresql.query.save') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        queryText: queryText,
                        saveId: saveId,
                        saveName: saveName,
                    },
                    success: function(response) {
                        $('#saveModal').modal('hide');
                    }
                });
            });
        });

        //if queryFormSubmit is clicked show sweetalert input form
        $(document).ready(function() {
            $('#queryFormSubmit').click(function(e) {
                e.preventDefault();

                //sweetalert input password
                Swal.fire({
                    title: 'Password',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Execute',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: (password) => {
                        $('#password').val(password);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#queryForm').submit();
                    }
                });
            });
        });
    </script>
@endpush
