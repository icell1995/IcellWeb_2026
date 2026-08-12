@extends('layouts.app')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
@endpush

@section('content')
<div class="loaderbg" style="display:none"></div>

<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="box">
        <div class="back-button">
            <a href="{{route('view_produktivitas_accident', ['accident_id' => $accidentId])}}"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="card">
                    <a target="_blank" href="{{route($document->documentCategory->base_route . '.download', ['id' => $document->id,'accident_id' => $id, 'document_category_id'=> $document->documentCategory->id])}}" class="btn btn-primary btn-lg">
                        <i class="bi bi-printer"></i>
                    </a>
                </div>
                <hr/>
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
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

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
