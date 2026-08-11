@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Hak Akses</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-officer" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center align-middle" width="5%">No</th>
                            <th class="text-center align-middle">Hak Akses</th>
                            <th class="text-center align-middle">Action</th>
                        </tr>
                    </thead>
                    <?php $no = 0; ?>

                    @foreach ($permission as $permissions)
                        <?php $no++; ?>
                        <tbody>
                            <tr>
                                <td class="text-center" scope="row">{{ $no }}</td>
                                <td>{{ $permissions->name }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#edit-data" data-id="{{ $permissions->id }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
            <div class="text-end">
                <button type="button" class="material-icons floating-btn" data-bs-toggle="modal"
                    data-bs-target="#add-data">add</button>
            </div>
        </div>

        {{-- Modal Add --}}
        @include('permission.modal.permission-modal-add')
        {{-- End Modal Add --}}

        {{-- Modal Edit --}}
        @include('permission.modal.permission-modal-edit')
        {{-- End Modal Edit --}}
    </div>

    @push('script')
        <script type="text/javascript">
            $(function() {
                $('.btn-edit').on("click", function() {
                    var _token = $("input[name='_token']").val();
                    var id = $(this).data('id');

                    $.ajax({
                        url: "{{ route('edit_modal_permission') }}",
                        type: 'GET',
                        data: {
                            _token: _token,
                            id: id
                        },
                        success: function(data) {
                            console.log(data);
                            $('#id_edit').val(data.permisiion.id);
                            $('#name_edit').val(data.permisiion.name);
                            $('modal').show();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
