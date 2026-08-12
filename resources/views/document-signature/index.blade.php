@php
    $_title = 'DOC TTE'
@endphp

@extends('layouts.app')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
@endpush

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="fw-bold text-center text-blue-dark">DOKUMEN BELUM DITANDATANGAN</h3>

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
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($documents as $document)
                                @if(in_array($document->status_id, ['9', '85']))
                                    <tr>
                                        <td class="text-center align-middle">
                                            <h6>{{$document->accident->no_lp ?? ''}}</h6>
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
                                                {{ Carbon\Carbon::parse($document->created_at)->locale('id')->translatedFormat('d F Y' )}}
                                            @endif
                                        </td>
                                        <td class="text-center align-middle"></td>
                                        <td class="text-center align-middle">
                                            {{-- <a href="#"
                                                class="btn btn-primary">
                                                <i class="bi bi-pencil-square text-white"></i> [Masih Dalam Konstruksi] Tanda Tangani Dokumen
                                            </a> --}}
                                            <a href="{{route('document-signature.sign', ['accident_id' => $document->accident->id, 'document_id' => $document->id, 'document_category_id' => $document->document_category_id])}}"
                                                class="btn btn-primary">
                                                <i class="bi bi-pencil-square text-white"></i> Tanda Tangani Dokumen
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
                <h4 class="fw-bold text-center text-blue-dark">DOKUMEN SUDAH DITANDATANGAN</h4>

                <div class="mt-4">
                    <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Laporan Polisi</th>
                                <th class="text-center">Jenis Dokumen</th>
                                <th class="text-center">Berkas Surat</th>
                                <th class="text-center">Dibuat Oleh</th>
                                <th class="text-center">Tanggal Dibuat</th>
                                <th class="text-center">Tanggal Di TTE</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($documents as $document)
                                 @if (in_array($document->status_id, ['10', '11', '86']))
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
                                            <a href="{{route('document-signature.view', ['accident_id' => $document->accident->id, 'document_id' => $document->id, 'document_category_id' => $document->document_category_id])}}"
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
        $('.dataTable').DataTable({
            responsive: true,
        });
    });
</script>
@endpush
