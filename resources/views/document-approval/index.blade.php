@php
    $_title = 'Permintaan Persetujuan Dokumen';
@endphp


@extends('layouts.app')

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card">
            <div class="card-body">
                <h3 class="fw-bold text-center text-blue-dark">PERSETUJUAN DOKUMEN</h3>

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
                                            <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                                                @if (isset($document->document_date))
                                                    {{ Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') }}
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-grid gap-2 flex-column">
                                            @php
                                                //$document->createdBy
                                                $createdBy = $document->createdByUser ?? NULL;
                                            @endphp
                                            @if($createdBy)
                                                <button type="button" class="btn btn-sm btn-danger btn-block"
                                                    disabled>{{ isset($createdBy) ? App\Helpers\PeopleNameHelper::getFullName($createdBy->first_title, $createdBy->first_name, $createdBy->last_name, $createdBy->last_title) : '' }}</button>
                                                <button type="button" class="btn btn-sm btn-danger btn-block"
                                                    disabled>{{ isset($createdBy) ? $createdBy->register_number : '' }}</button>
                                                <button type="button" class="btn btn-sm btn-danger btn-block"
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
                                        <a href="{{ route('document-approval.view', ['accident_id' => $document->accident->id, 'document_id' => $document->id, 'document_category_id' => $document->document_category_id]) }}"
                                            class="btn btn-primary">
                                            <i class="bi bi-eye"></i> Lihat Dokumen
                                        </a>
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

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                scrollX: true,
            });
        });
    </script>
@endpush
