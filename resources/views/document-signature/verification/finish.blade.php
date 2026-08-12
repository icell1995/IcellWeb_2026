@php
    $_title = 'Persetujuan Dokumen';
@endphp


@extends('layouts.app')

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <a class="btn-back" href="{{ route('document-signature.verification.index') }}"><i class="bi bi-arrow-left"></i>
            Kembali</a>

        <div class="card">
            <div class="card-body">
                <div class="card">
                    <div class="d-grid gap-2">
                        <a target="_blank"
                            href="{{ route($document->documentCategory->base_route . '.download', ['id' => $document->id, 'accident_id' => $id, 'document_category_id' => $document->documentCategory->id]) }}"
                            class="btn btn-primary btn-lg">
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                </div>

                <form
                    action="{{ route('document-signature.verification.finish.save', ['accident_id' => $accidentId, 'document_id' => $documentId, 'document_category_id' => $documentCategoryId]) }}"
                    method="post" enctype="multipart/form-data" id="verifyForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="isVerified" id="isVerified" value="">
                    <input type="hidden" name="message" id="message" value="">

                    <div class="form-group text-center">
                        <button class="btn btn-success" type="button" id="verifiedButton">
                            <i class="bi bi-check-circle"></i> Setuju
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
            });
        });

        $('#verifiedButton').on('click', function(e) {
            e.preventDefault();
            $('#isVerified').val('true');

            //sweetalert confirm
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menyetujui dokumen ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#verifyForm').submit();
                }
            });
        });
    </script>
@endpush
