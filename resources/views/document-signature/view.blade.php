@php
    $_title = 'VIEW SIGN DOC TTE'
@endphp

@extends('layouts.app')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
@endpush

@section('content')
<div class="loaderbg" style="display:none"></div>

    <div class="box">   
        <a class="btn-back" href="{{route('document-signature.index')}}"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div class="card">
            <div class="card-body">
                @if ($attachment->extension == 'pdf')
                    <div class="text-center overflow-auto">                    
                        <iframe src="{{ asset('documents/attachments/'. $attachment->name) }}" width="1480px" height="1180px">
                        </iframe>
                    </div>
                @endif
                @if ($attachment->extension != 'pdf')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold text-blue-dark">
                                Unduh/Lihat Preview
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a target="_blank" href="{{ asset('documents/attachments/'. $attachment->name) }}" class="btn btn-primary btn-lg btn-block">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                <hr/>
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
<!-- Delete Button -->
<script src="{{asset('js/laravel.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive: true,
        });
    });
</script>
@endpush
