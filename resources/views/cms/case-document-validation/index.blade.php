@extends('cms.layouts.app')

@section('_title', 'Validasi Berkas')

@section('content')
    <div class="loaderbg" style="display:none"></div>

    <div class="box">
        <div class="card">
            <div class="card rounded-2">
                {{-- NAVBAR 2 BUTTON / ROUTE SWITCH --}}
                @php
                $isMindik = request()->routeIs('cms.case-document-validation.*');    
                $isSelra  = request()->routeIs('cms.case-resolutions-validations.*');
                @endphp

                <div class="p-2 bg-white">
                    <div class="d-flex gap-2">
                        <a href="{{ route('cms.case-document-validation.index') }}"
                        class="flex-fill text-center py-3 rounded-3 border d-flex align-items-center justify-content-center gap-2 text-decoration-none
                                {{ $isMindik ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary' }}">
                        <i class="bi bi-file-earmark"></i>
                        <span class="fw-semibold">Review Mindik</span>
                        </a>
                        <a href="{{ route('cms.case-resolutions-validations.index') }}"
                        class="flex-fill text-center py-3 rounded-3 border d-flex align-items-center justify-content-center gap-2 text-decoration-none
                                {{ $isSelra ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary' }}">
                        <i class="bi bi-people"></i>
                        <span class="fw-semibold">Review Selra</span>
                        </a>

                    </div>
                </div>
            </div>

            <div class="card-body">
                <h3 class="fw-bold text-center text-primary mb-2">
                    REVIEW TECHNICAL BERKAS PERKARA
                </h3>

                <div class="mt-4 row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                        <div class="card shadow rounded-3 card-dash">
                            <div class="d-flex p-2">
                                <div class="col-lg-3 col-md-3 col-sm-12 col-12 icons-dashboard">
                                    <i class="bi bi-list-task"></i>
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                    <div class="">
                                        <span class="fw-7">Total Antrian LP</span>
                                    </div>
                                    <h2 class="text-danger fw-bolder m-0">{{$accidents->count()}}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Laporan Polisi</th>
                                <th class="text-center">Total Dokumen</th>
                
                                <th class="text-center">Opsi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($accidents as $accident)
                                <tr>
                                    <td class="text-center align-middle">
                                        <h6>{{ $accident->no_lp ?? '' }} </h6>
                                        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accident->id]) }}" target="_blank" class="btn btn-sm btn-primary mb-2">
                                            Visit <i class="bi bi-arrow-up-right-square-fill"></i>
                                        </a>

                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-sm btn-danger" disabled>
                                                {{ "Accident Date : " . Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') }}
                                                {{ "; Report Date : " . Carbon\Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" disabled>
                                                @if (isset($accident->police->full_name))
                                                    {{ "Satker : " . $accident->police->full_name }}
                                                @endif
                                            </button>
                                        </div>
                                    </td>

                                    <td class="text-center align-middle">
                                        @php
                                            $totalDocumentCreated = count($accident->suratPerintahPenyelidikanDocuments) +
                                            count($accident->suratPerintahPenyidikanDocuments) +
                                            count($accident->suratPerintahTugasDocuments) +
                                            count($accident->laporanHasilGelarPerkaraDocuments) +
                                            count($accident->suratKetetapanTentangPenetapanTersangkaDocuments) +
                                            count($accident->suratPemberitahuanDimulainyaPenyidikanDocuments);

                                            $totalDocumentFinal = count($accident->suratPerintahPenyelidikanDocuments->whereIn('status_id', ['86', '85'])) +
                                            count($accident->suratPerintahPenyidikanDocuments->whereIn('status_id', ['86', '85'])) +
                                            count($accident->suratPerintahTugasDocuments->whereIn('status_id', ['86', '85'])) +
                                            count($accident->laporanHasilGelarPerkaraDocuments->whereIn('status_id', ['86', '85'])) +
                                            count($accident->suratKetetapanTentangPenetapanTersangkaDocuments->whereIn('status_id', ['86', '85'])) +
                                            count($accident->suratPemberitahuanDimulainyaPenyidikanDocuments->whereIn('status_id', ['86', '85']));
                                            
                                            $totalDocumentEntry = count($accident->suratPerintahPenyelidikanDocuments->whereIn('status_id', ['12', '11', '10', '9'])) +
                                            count($accident->suratPerintahPenyidikanDocuments->whereIn('status_id', ['12', '11', '10', '9'])) +
                                            count($accident->suratPerintahTugasDocuments->whereIn('status_id', ['12', '11', '10', '9'])) +
                                            count($accident->laporanHasilGelarPerkaraDocuments->whereIn('status_id', ['12', '11', '10', '9'])) +
                                            count($accident->suratKetetapanTentangPenetapanTersangkaDocuments->whereIn('status_id', ['12', '11', '10', '9'])) +
                                            count($accident->suratPemberitahuanDimulainyaPenyidikanDocuments->whereIn('status_id', ['12', '11', '10', '9']));
                                        @endphp

                                        Dokumen Dibuat : {{ $totalDocumentCreated }}
                                        <br/>
                                        <b>Dokumen Masuk :</b> {{ $totalDocumentEntry }}
                                        <br/>
                                        <b>Dokumen Final :</b> {{ $totalDocumentFinal }}
                                    </td>

                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-primary documentListModalButton" data-accident-id="{{ $accident->id }}" data-accident-number="{{ $accident->no_lp }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="documentListModal" tabindex="-1" aria-labelledby="documentListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="documentListModalTitle">LP : </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-users dataTable" name="dataTable" width="100%" id="documentsTable">
                    <thead>
                        <tr>
                            <th class="text-center">Laporan Polisi</th>
                            <th class="text-center">Jenis Dokumen</th>
                            <th class="text-center">Berkas Surat</th>
                            <th class="text-center">Dibuat Oleh</th>
                            <th class="text-center">Tanggal Dibuat</th>
                            <th class="text-center">Status</th>
            
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                stateSave: true,
            });
        });

        //when document list modal opened
        $('.documentListModalButton').on('click', function() {
            var accidentId = $(this).data('accident-id');
            var accidentNumber = $(this).data('accident-number');
            
            //call ajax to get document list
            $.ajax({
                url: "{{route('cms.case-document-validation.api.documents')}}",
                type: 'GET',
                data: {
                    accidentId: accidentId
                },
                success: function(response) {
                    $('#documentsTable').DataTable().destroy();
                    $('#documentsTable tbody').empty();
                    $('#documentsTable tbody').append(response);

                    $('#documentsTable').DataTable({
                        responsive: true
                    });

                    $('#documentListModalTitle').text('LP : ' + accidentNumber);
                    $('#documentListModal').modal('show');
                }
            });

        });

        // reload page when modal is closed
        $('#documentListModal').on('hidden.bs.modal', function () {
            location.reload();
        });
    </script>
@endpush
