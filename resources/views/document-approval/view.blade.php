@php
    $_title = 'Lihat Dokumen';
@endphp

@extends('layouts.app')

@section('content')
    <div class="loaderbg" style="display:none"></div>
    <div class="box">
        <a class="btn-back" href="{{ route('document-approval.index') }}"><i class="bi bi-arrow-left"></i>Kembali Halaman
            Persetujuan</a>

        <div class="card">
            <div class="card-body">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold text-blue-dark">
                            Unduh/Lihat Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a target="_blank"
                                href="{{ route($document->documentCategory->base_route . '.download', ['id' => $document->id, 'accident_id' => $document->accident_id, 'document_category_id' => $document->documentCategory->id]) }}"
                                class="btn btn-primary btn-lg">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <hr />

                <form
                    action="{{ route('document-approval.save', ['accident_id' => $accidentId, 'document_id' => $documentId, 'document_category_id' => $documentCategoryId]) }}"
                    method="post" enctype="multipart/form-data" id="approvalForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="isApproved" id="isApproved" value="">
                    <input type="hidden" name="message" id="message" value="">

                    <div class="form-group text-center">
                        <button class="btn btn-dark-blue" type="button" id="approvedButton">
                            <i class="bi bi-check-circle"></i> Setuju
                        </button>

                        <button class="btn btn-danger" type="button" id="rejectedButton">
                            <i class="bi bi-x-circle"></i> Kembalikan
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

        $('#approvedButton').on('click', function(e) {
            e.preventDefault();
            $('#isApproved').val('true');

            //sweetalert confirm
            Swal.fire({
                title: 'Apakah Anda yakin menyetujui dokumen ini?',
                text: "Setelah menyetujui, dokumen TIDAK DAPAT melakukan EDIT atau HAPUS!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                const confirmString = Math.floor(Math.random() * 100000).toString().padStart(5, '0');
                
                if (result.value) {
                    Swal.fire({
                        title: confirmString,
                        text: 'ketik ulang teks diatas untuk meyakinkan bahwa menyetujui dokumen ini (Setelah disetujui, dokumen TIDAK DAPAT melakukan EDIT atau HAPUS!)',
                        input: 'text',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Setuju Dokumen Ini',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        showLoaderOnConfirm: true,
                        willOpen: () => {
                            const input = Swal.getInput();
                            input.addEventListener('paste', (e) => {
                                e.preventDefault();
                            });
                        },
                        preConfirm: (confirmText) => {
                            if (confirmText === confirmString) {
                                $('#approvalForm').submit();
                            } else {
                                Swal.getPopup().querySelector('input').value = '';
                                
                                Swal.showValidationMessage('Teks yang anda masukkan tidak sesuai');
                            }   
                        }
                    });
                }
            });
        });

        $('#rejectedButton').on('click', function(e) {
            e.preventDefault();
            $('#isApproved').val('false');

            Swal.fire({
                title: 'Catatan',
                input: 'textarea',
                inputLabel: 'Message',
                inputAttributes: {
                    onkeydown: 'disableEnterKey(event)', // Panggil fungsi untuk menonaktifkan Enter key
                },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: function(message) {
                    // Lanjutkan dengan submit form dan tambahkan input message ke form data
                    $('#message').val(message);
                    $('#approvalForm').submit();
                },
            });
        });

        function disableEnterKey(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        }
    </script>
@endpush
