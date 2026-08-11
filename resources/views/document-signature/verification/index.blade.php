@php
    $_title = 'TTE (Verifikasi)';
@endphp

@extends('layouts.app')

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="fw-bold text-center text-blue-dark">MENUNGGU DIVERIFIKASI</h4>

                <div class="mt-4">
                    <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Laporan Polisi</th>
                                <th class="text-center">Jenis Dokumen</th>
                                <th class="text-center">Berkas Surat</th>
                                <th class="text-center">Dibuat Oleh</th>
                                <th class="text-center">Tanggal Dibuat</th>
                                <th class="text-center">Unduh/View</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($documents as $document)
                                @if ($document->status_id == '8')
                                    <tr>
                                        <td class="text-center align-middle">
                                            <h6>{{ $document->accident->no_lp ?? '' }}</h6>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                    @if (isset($document->accident))
                                                        {{ Carbon\Carbon::parse($document->accident->accident_date)->locale('id')->translatedFormat('d F Y') }}
                                                    @endif
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $document->documentCategory->name ?? '' }}
                                        </td>
                                        <td class="text-center align-middle">
                                            <h6>{{ $document->document_number ?? '' }}</h6>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                    @if (isset($document->document_date))
                                                        {{ Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') }}
                                                    @endif
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-grid gap-2">
                                                @php
                                                    //$document->createdByUser
                                                    $createdBy = $document->createdByUser ?? NULL;
                                                @endphp
                                                @if($createdBy)
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>{{ isset($createdBy) ? App\Helpers\PeopleNameHelper::getFullName($createdBy->first_title, $createdBy->first_name, $createdBy->last_name, $createdBy->last_title) : '' }}</button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>{{ isset($createdBy) ? $createdBy->register_number : '' }}</button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>{{ isset($createdBy) ? ($createdBy->rank->name ?? '') : '' }}</button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if (isset($document->created_at))
                                                {{ Carbon\Carbon::parse($document->created_at)->locale('id')->translatedFormat('d F Y') }}
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <a target="_blank"
                                                href="{{ route($document->documentCategory->base_route . '.download', ['id' => $document->id, 'accident_id' => $document->accident_id, 'document_category_id' => $document->documentCategory->id]) }}"
                                                class="btn btn-primary btn-lg">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="{{ route('document-signature.verification.view', ['accident_id' => $document->accident->id, 'document_id' => $document->id, 'document_category_id' => $document->document_category_id]) }}"
                                                class="btn btn-primary">
                                                <i class="bi bi-eye"></i> Lihat Dokumen
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="fw-bold text-center text-blue-dark">DOKUMEN SUDAH DIVERIFIKASI</h4>

                <div class="mt-4">
                    <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Laporan Polisi</th>
                                <th class="text-center">Jenis Dokumen</th>
                                <th class="text-center">Berkas Surat</th>
                                <th class="text-center">Dibuat Oleh</th>
                                <th class="text-center">Tanggal Dibuat</th>
                                <th class="text-center">Tanggal Diverifikasi</th>
                                <th class="text-center">Unduh/View</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($documents as $document)
                                @if ($document->status_id == '9' || $document->status_id == '12' || $document->status_id == '10' || $document->status_id == '86')
                                    <tr>
                                        <td class="text-center align-middle">
                                            <h6>{{ $document->accident->no_lp ?? '' }}</h6>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                    @if (isset($document->accident))
                                                        {{ Carbon\Carbon::parse($document->accident->accident_date)->locale('id')->translatedFormat('d F Y') }}
                                                    @endif
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $document->documentCategory->name ?? '' }}
                                        </td>
                                        <td class="text-center align-middle">
                                            <h6>{{ $document->document_number ?? '' }}</h6>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-sm btn-danger" disabled>
                                                    @if (isset($document->document_date))
                                                        {{ Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') }}
                                                    @endif
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-grid gap-2">
                                                @php
                                                    //$document->createdByUser
                                                    $createdBy = $document->createdByUser ?? NULL;
                                                @endphp
                                                @if($createdBy)
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>{{ isset($createdBy) ? App\Helpers\PeopleNameHelper::getFullName($createdBy->first_title, $createdBy->first_name, $createdBy->last_name, $createdBy->last_title) : '' }}</button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>{{ isset($createdBy) ? $createdBy->register_number : '' }}</button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        disabled>{{ isset($createdBy) ? ($createdBy->rank->name ?? '') : '' }}</button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if (isset($document->created_at))
                                                {{ Carbon\Carbon::parse($document->created_at)->locale('id')->translatedFormat('d F Y') }}
                                            @endif
                                        </td>
                                        <td class="text-center align-middle"></td>
                                        <td class="text-center align-middle">
                                            <a target="_blank"
                                                href="{{ route($document->documentCategory->base_route . '.download', ['id' => $document->id, 'accident_id' => $document->accident_id, 'document_category_id' => $document->documentCategory->id]) }}"
                                                class="btn btn-primary btn-lg">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="{{ route('document-signature.verification.view', ['document_id' => $document->id, 'accident_id' => $document->accident_id, 'document_category_id' => $document->documentCategory->id]) }}"
                                                class="btn btn-primary">
                                                <i class="bi bi-eye"></i> Lihat Dokumen
                                            </a>

                                            @if (in_array($document->status_id, [9, 12]))
                                                <br><br>
                                                <button type="button" class="btn btn-success rollbackButton"
                                                    data-accident-id="{{ $document->accident->id ?? '' }}"
                                                    data-document-id="{{ $document->id }}"
                                                    data-category-id="{{ $document->documentCategory->id ?? '' }}">
                                                    <i class="bi bi-eye"></i> Rollback Dokumen
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display:none;">
                    <form action="{{ route('document-signature.verification.rollback') }}" method="post"
                        id="rollbackForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="accidentId" id="accidentId" value="">
                        <input type="hidden" name="documentId" id="documentId" value="">
                        <input type="hidden" name="documentCategoryId" id="documentCategoryId" value="">
                    </form>
                </div>
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
            $('.dataTable').DataTable({
                responsive: true,
            });
        });

        $('.rollbackButton').on('click', function(e) {
            e.preventDefault();

            var documentId = $(this).data('document-id');
            var documentCategoryId = $(this).data('category-id');
            var accidentId = $(this).data('accident-id');

            $('#documentId').val(documentId);
            $('#documentCategoryId').val(documentCategoryId);
            $('#accidentId').val(accidentId);

            //sweetalert confirm
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengebalikan dokumen ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#rollbackForm').submit();
                }
            });
        });
    </script>
@endpush
