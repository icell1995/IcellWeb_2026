@php
    $isCanEntryDocument = Auth::user()->canEntryDocument();
    $canCreateDoc = Auth::user()->hasPermission('productivity-lp.C') && $isCanEntryDocument;
    $canUpdateDoc = Auth::user()->hasPermission('productivity-lp.U') && $isCanEntryDocument;
    $canDeleteDoc = Auth::user()->hasPermission('productivity-lp.D') && $isCanEntryDocument;
    $canActionDoc = Auth::user()->hasPermission('document-action.R') && $isCanEntryDocument;
@endphp

@include('produktivitas.components.case-navigation')

<div class="card">
    <div class="card-header tahapan d-flex justify-content-between align-items-center">
        <h5 class="fw-bold card-title m-0">
            Berkas Perkara
        </h5>
        @if($canCreateDoc)
            <a href="#" data-bs-toggle="modal" data-bs-target="#addDocument" class="btn btn-dark-blue">
                <i class="bi bi-plus-circle me-2"></i>Tambah Dokumen
            </a>
        @endif
    </div>

    <div class="card-body">
        <div class="alert alert-danger mb-4 attentionBox">
            <div class="text-center">
                <b>
                    PERHATIAN !
                    <hr />
                </b>
                SAAT INI SEDANG DILAKUKAN PENGEMBANGAN DAN PEMBARUAN FITUR DOKUMEN MINDIK.<br />
                TAHAP PEMINDAHAN FITUR SEDANG DILAKUKAN. UNTUK SAAT INI, GUNAKAN <b>AREA "BERKAS PERKARA"</b> UNTUK
                INPUT DOKUMEN YANG SUDAH TERSEDIA.<br />
                PEMINDAHAN FITUR AKAN DILAKUKAN SECARA BERTAHAP.<br />
                JIKA INPUT DOKUMEN BELUM TERSEDIA, ANDA MASIH DAPAT MELAKUKAN INPUT DI <b>AREA BOX "TAHAPAN TIDAK LANJUT
                    LP"</b> YANG TERLETAK DI BAWAH INI.<br />
            </div>
        </div>

        @if (session()->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif


        <table class="table table-striped table-bordered table-users" id="dataTable" name="dataTable" width="100%">
            <thead>
                <tr>
                    <th class="text-center">Nama Dokumen</th>
                    <th class="text-center">No Dokumen</th>
                    <th class="text-center">Tanggal Dibuat</th>
                    <th class="text-center">Tanggal Ditandatangani</th>
                    <th class="text-center">Durasi</th>
                    <th class="text-center">Total Durasi</th>
                    <th class="text-center">Dibuat Oleh</th>
                    <th class="text-center">Unduh/View</th>
                    <th class="text-center">Opsi</th>
                </tr>
            </thead>

            <tbody>
                @if (!empty($accidentDocuments))
                    @foreach ($accidentDocuments as $accidentDocument)
                        @php
                            $isExistsDocumentCategory = !empty($accidentDocument->documentCategory);
                            $isExistsDocumentNumber = !empty($accidentDocument->document_number);

                            $isLegacy = $accidentDocument->is_legacy ?? false;
                            
                            // Check if this is SP2HP document
                            $isSp2hpDocument = is_a($accidentDocument, 'App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument\SuratPemberitahuanPerkembanganHasilPenyidikanDocument');

                            // Check if this is SP3 document
                            $isSp3Document = is_a($accidentDocument, 'App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument');

                            // Check if this is Tahap I document
                            $isTahap1Document = is_a($accidentDocument, 'App\Models\Doc\Tahap1Document\Tahap1Document');
                            

                            // Skip SP2HP documents if user is not role_id 1
                            if ($isSp2hpDocument && Auth::getUser()->role_id != 1) {
                                continue;
                            }
                        @endphp
                        <tr class="{{ $isLegacy ? 'table-primary' : '' }}">
                            <td class="text-center align-middle">
                                @if($isSp2hpDocument)
                                    Surat Pemberitahuan Perkembangan Hasil Penyidikan (SP2HP)
                                    @if(!empty($accidentDocument->tipe_sp2hp))
                                        - Tipe {{ strtoupper($accidentDocument->tipe_sp2hp) }}
                                    @endif
                                @elseif ($isExistsDocumentCategory && !empty($accidentDocument->caseDegreeType))
                                    {{ mb_strtoupper($accidentDocument->documentCategory->name.' ('.$accidentDocument->caseDegreeType->name.')', 'UTF-8') }}
                                @elseif($isExistsDocumentCategory)
                                    @if ($accidentDocument->documentCategory->id == '0702')
                                        @if($accidentDocument->related_type == 'App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument')
                                            {{ mb_strtoupper($accidentDocument->documentCategory->name.' (PENYELIDIKAN)', 'UTF-8') }}
                                        @elseif($accidentDocument->related_type == 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument')
                                            {{ mb_strtoupper($accidentDocument->documentCategory->name.' (PENYIDIKAN)', 'UTF-8') }}
                                        @endif
                                    @else
                                        {{ mb_strtoupper($accidentDocument->documentCategory->name, 'UTF-8') }}
                                    @endif
                                @endif

                                @if($isLegacy == true) 
                                    <br/>
                                    <h5><span class="badge badge-primary">Legacy</span></h5>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($isSp2hpDocument)
                                    {{ $accidentDocument->nomor_surat ?? '-' }}
                                @else
                                    {{ $accidentDocument->document_number }}
                                @endif

                                <br/>
                                
                                @if(!empty($accidentDocument->released_at))
                                <button type="button" class="btn btn-sm btn-secondary btn-block text-bold mt-2" disabled>
                                    {{'Dilepas: ' . Carbon\Carbon::parse($accidentDocument->released_at)->locale('id')->translatedFormat('d F Y H:i:s') . ' WIB'}}
                                </button>
                                @endif
                              
                                @if(!empty($accidentDocument->last_synced_at))
                                <button type="button" class="btn btn-sm btn-danger btn-block text-bold mt-2" disabled>
                                    {{'Terakhir Ditarik: ' . Carbon\Carbon::parse($accidentDocument->last_synced_at)->locale('id')->translatedFormat('d F Y H:i:s') . ' WIB'}}
                                </button>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                {{ Carbon\Carbon::parse($accidentDocument->created_at)->locale('id')->translatedFormat('d F Y') }}
                            </td>
                            <td class="text-center align-middle">
                                @if($isSp2hpDocument && !empty($accidentDocument->tanggal_surat))
                                    {{ Carbon\Carbon::parse($accidentDocument->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
                                @elseif(!empty($accidentDocument->document_date))
                                    {{ Carbon\Carbon::parse($accidentDocument->document_date)->locale('id')->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center align-middle"></td>
                            <td class="text-center align-middle"></td>
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column">
                                    @php
                                        //$document->createdByUser
                                        $createdBy = $accidentDocument->createdByUser ?? NULL;
                                    @endphp
                                    @if($createdBy)
                                        <button type="button" class="btn btn-sm btn-danger btn-block text-bold mb-2"
                                            disabled>{{ isset($createdBy) ? App\Helpers\PeopleNameHelper::getFullName($createdBy->first_title, $createdBy->first_name, $createdBy->last_name, $createdBy->last_title) : '' }}</button>
                                        <button type="button" class="btn btn-sm btn-danger btn-block text-bold mb-2"
                                            disabled>{{ isset($createdBy) ? $createdBy->register_number : '' }}</button>
                                        <button type="button" class="btn btn-sm btn-danger btn-block text-bold mb-2"
                                            disabled>{{ isset($createdBy) ? ($createdBy->rank->name ?? '') : '' }}</button>
                                    @endif
                                </div>
                            </td>
                           <td class="text-center align-middle">
                                @if($isSp2hpDocument)
                                    {{-- SP2HP tidak punya tombol unduh dokumen (belum ada PDF generator) --}}
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="bi bi-printer"></i>
                                        <h6 class="" style="font-size: 14px!important;">
                                            Belum Tersedia
                                        </h6>
                                    </button>
                                @else
                                    <a target="_blank"
                                        href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.download', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->documentCategory->id]) }} @endif"
                                        class="btn btn-primary btn-lg">
                                        <i class="bi bi-printer"></i>
                                        
                                        @if($isExistsDocumentCategory && $accidentDocument->documentCategory->is_digital_signature == true)
                                            <h6 class="" style="font-size: 12px!important;">
                                                Unduh Dokumen
                                            </h6>
                                        @else
                                            <h6 class="" style="font-size: 14px!important;">
                                                Unduh Dokumen
                                            </h6>
                                        @endif
                                    </a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($isSp2hpDocument)
                                    {{-- Tombol action untuk SP2HP --}}
                                    <a href="{{ route('doc.sp2hp-document.show', ['id' => $accidentDocument->id]) }}"
                                        class="btn btn-info btn-sm m-1" target="_blank"><i class="bi bi-eye"></i> Lihat</a>
                                    <br>
                                    @if ($canUpdateDoc)
                                        <a href="{{ route('doc.sp2hp-document.edit', ['id' => $accidentDocument->id, 'accident_id' => $id]) }}"
                                            class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                        <br>
                                    @endif
                                    @if ($canDeleteDoc)
                                        <a href="javascript:void(0)" 
                                            class="btn btn-danger btn-sm m-1 delete-sp2hp" 
                                            data-id="{{ $accidentDocument->id }}"
                                            data-accident-id="{{ $id }}">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    @endif
                                @else
                                    @switch($accidentDocument->status_id)
                                        @case('1')

                                            @if ($canUpdateDoc)
                                                <a href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.edit', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->documentCategory->id]) }} @endif"
                                                    class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                                <br>
                                            @endif
                                            @if ($canDeleteDoc)
                                                <a href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.delete', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->documentCategory->id]) }} @endif"
                                                    class="btn btn-danger btn-sm m-1" data-method="delete"
                                                    data-token="{{ csrf_token() }}"
                                                    data-confirm="Apakah Anda yakin ingin menghapus ini?"><i
                                                        class="bi bi-trash"></i> Hapus</a>
                                            @endif
                                        @break

                                    @case('2')
                                        <h6>Dokumen Dibuat</h6>

                                        @if ($canActionDoc)
                                            @if ($isExistsDocumentNumber)
                                                <a href="{{ route('document-action.request-approval.request', ['accident_id' => $id, 'document_id' => $accidentDocument->id, 'form_type' => '1', 'document_category_id' => $accidentDocument->document_category_id]) }}"
                                                    class="btn btn-success btn-sm m-1"><i class="bi bi-file-text"></i> Isi
                                                    Nomor</a>
                                                <br>
                                            @else
                                                <a href="{{ route('document-action.request-approval.request', ['accident_id' => $id, 'document_id' => $accidentDocument->id, 'form_type' => '2', 'document_category_id' => $accidentDocument->document_category_id]) }}"
                                                    class="btn btn-success btn-sm m-1"><i class="bi bi-file-check"></i>
                                                    Meminta Persetujuan</a>
                                                <br>
                                            @endif
                                        @endif

                                        @if ($canUpdateDoc)
                                            <a href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.edit', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->documentCategory->id]) }} @endif"
                                                class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <br>
                                        @endif
                                        @if ($canDeleteDoc)
                                            <a href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.delete', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->documentCategory->id]) }} @endif"
                                                class="btn btn-danger btn-sm m-1" data-method="delete"
                                                data-token="{{ csrf_token() }}"
                                                data-confirm="Apakah Anda yakin ingin menghapus ini?"><i
                                                    class="bi bi-trash"></i> Hapus</a>
                                        @endif
                                    @break

                                    @case('3')
                                        <h6>Menunggu Persetujuan Admin Satker</h6>
                                    @break

                                    @case('4')
                                        <h6>Dokumen Dikembalikan</h6>

                                        <button type="button" class="btn btn-primary btn-sm m-1 reject-notes"
                                            data-message="{{ $accidentDocument->messages['reason_approval_rejected'] ?? '' }}"><i
                                                class="bi bi-file-text"></i> Catatan Dikembalikan</button>
                                        <br>

                                        @if ($canActionDoc)
                                            @if ($isExistsDocumentNumber)
                                                <a href="{{ route('document-action.request-approval.request', ['accident_id' => $id, 'document_id' => $accidentDocument->id, 'form_type' => '1', 'document_category_id' => $accidentDocument->document_category_id]) }}"
                                                    class="btn btn-success btn-sm m-1"><i class="fa fa-edit text-white"></i>
                                                    Selesai/Isi Nomor</a>
                                                <br>
                                            @else
                                                <a href="{{ route('document-action.request-approval.request', ['accident_id' => $id, 'document_id' => $accidentDocument->id, 'form_type' => '2', 'document_category_id' => $accidentDocument->document_category_id]) }}"
                                                    class="btn btn-success btn-sm m-1"><i class="fa fa-edit text-white"></i>
                                                    Selesai/Meminta Persetujuan</a>
                                                <br>
                                            @endif
                                        @endif

                                        @if ($canUpdateDoc)
                                            <a href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.edit', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->documentCategory->id]) }} @endif"
                                                class="btn btn-warning btn-sm m-1"><i class="fa fa-edit"></i> Revisi</a>
                                            <br>
                                        @endif
                                        @if ($canDeleteDoc)
                                            <a href="@if ($isExistsDocumentCategory) {{ route($accidentDocument->documentCategory->base_route . '.delete', ['id' => $accidentDocument->id, 'accident_id' => $id, 'document_category_id' => $accidentDocument->document_category_id]) }} @endif"
                                                class="btn btn-danger btn-sm m-1" data-method="delete"
                                                data-token="{{ csrf_token() }}"
                                                data-confirm="Apakah Anda yakin ingin menghapus ini?"><i
                                                    class="bi bi-trash"></i> Hapus</a>
                                        @endif
                                    @break

                                    @case('5')
                                        <h6 class="bg-success rounded-1 text-white" style="font-size: 14px">Dokumen Valid</h6>

                                        @if(isset($accidentDocument->messages['reason_approval_file_rejected']))
                                            <button type="button" class="btn btn-primary btn-sm m-1 reject-notes"
                                                data-message="{{ $accidentDocument->messages['reason_approval_file_rejected'] ?? '' }}"><i
                                                    class="bi bi-file-text"></i> Catatan Dikembalikan</button>
                                            <br>
                                        @endif
                                        
                                        @if ($canActionDoc)
                                            <a href="{{ route('document-action.upload-document.upload', ['accident_id' => $id, 'document_id' => $accidentDocument->id, 'form_type' => 'pdf', 'document_category_id' => $accidentDocument->document_category_id]) }}"
                                                class="btn btn-danger btn-sm m-1"><i class="bi bi-upload"></i>
                                                Upload</a>
                                        @endif
                                    @break

                                    @case('6')
                                        @if(isset($accidentDocument->messages['reason_approval_file_rejected']))
                                        <h6 class="bg-warning rounded-1 fw-bold" style="font-size: 14px!important;">Dokumen Dikembalikan</h6>
                                        
                                        <button type="button" class="btn btn-primary btn-sm m-1 reject-notes"
                                            data-message="{{ $accidentDocument->messages['reason_approval_file_rejected'] ?? '' }}"><i
                                                class="bi bi-file-text"></i> Catatan Dikembalikan</button>
                                        <br>
                                        @endif

                                        @if ($canActionDoc)
                                        <a href="{{ route('document-action.upload-document.upload', ['accident_id' => $id, 'document_id' => $accidentDocument->id, 'form_type' => 'word', 'document_category_id' => $accidentDocument->document_category_id]) }}"
                                            class="btn btn-danger btn-sm m-1"><i class="bi bi-upload"></i>
                                            Upload</a>
                                        @endif
                                    @break

                                    @case('7')
                                        <h6 class="bg-success rounded-1 text-white fw-bold" style="font-size: 14px">Dokumen
                                            Valid</h6>
                                        <h6 class="bg-primary rounded-1 text-white fw-bold" style="font-size: 14px">
                                            Dokumen Upload</h6>
                                        <h6 class="bg-warning rounded-1 text-white fw-bold"
                                            style="font-size: 14px!important;">Menunggu Persetujuan Admin Satker</h6>
                                    @break

                                    @case('8')
                                        <h6>Menunggu Verifikasi Admin Satker</h6>
                                    @break

                                    @case('9')
                                        <h6>Menunggu Ditandatangani</h6>
                                    @break

                                    @case('10')
                                        <h6>Dokumen Ditandatangani</h6>
                                    @break

                                    @case('11')
                                        <h6>Dokumen Selesai</h6>

                                        @php
                                            $attachmentFIleName = $accidentDocument->attachment->name ?? NULL;
                                        @endphp
                                        <a href="{{ asset('documents/attachments/' . $attachmentFIleName) }}"
                                            class="btn btn-secondary btn-sm m-1" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen Unggah</a>
                                    @break

                                    @case('12')
                                        <h6 class="bg-danger rounded-1 text-white fw-bold">Menunggu Review Technical Administrator Korlantas</h6>

                                        <h6 class="bg-success rounded-1 text-white fw-bold">Harap Menunggu Karena Dokumen Dalam Antrian</h6>
                                        {{-- <h6 class="bg-warning rounded-1 fw-bold">Hubungi Helpdesk Jika Setelah 1x24 Jam Belum Ada Perkembangan</h6> --}}
                                    @break

                                    @case('85')
                                        <h6>Dokumen Sudah Final (Tanpa TTE)</h6>
                                        @php
                                            $attachmentFIleName = $accidentDocument->attachment->name ?? NULL;
                                        @endphp
                                        <a href="{{ asset('documents/attachments/' . $attachmentFIleName) }}"
                                            class="btn btn-secondary btn-sm m-1" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen Unggah</a>
                                    @break

                                    @case('86')
                                        <h6>Dokumen Sudah Final</h6>

                                        @php
                                            $attachmentFIleName = $accidentDocument->attachment->name ?? NULL;
                                        @endphp
                                        <a href="{{ asset('documents/attachments/' . $attachmentFIleName) }}"
                                            class="btn btn-secondary btn-sm m-1" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen Unggah</a>
                                    @break

                                    @default
                                        <h6>-</h6>
                                    @break
                                @endswitch
                                @endif

                                @if (Auth::getUser()->role_id == 1 || Auth::getUser()->role_id == 2)
                                    <button type="button" data-document-id="{{$accidentDocument->id}}"
                                            class="btn btn-primary btn-sm m-1 copy-document-id"><i class="bi bi-clipboard"></i>
                                            Copy ID
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div id="addDocument" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title">Dokumen</h4>
            </div>
            <div class="alert alert-danger mb-4 attentionBox">
                <div class="text-center">
                    Harap Membuat Surat Perintah Penyelidikan Terlebih Dahulu Sampai Tahap Di Setujui Level Admin Satker. Untuk Memunculkan Opsi Dokumen Yang Lain
                </div>
            </div>

            <form action="{{ route('doc.createDocumentRouter') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="accidentId" value="{{ $id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="sub-field col-sm-12 col-md-12" style="display: table">
                            <div class="add-row row">
                                <div class="input-group col-lg-11">
                                    <select id="classDocument" name="classDocument" class="form-select">
                                        <option value="">---Pilih Tahapan Dokumen---</option>
                                        @php
                                            $suratPerintahPenyelidikanDocumentsCountRequiredUnlockForm = $countAccidentDocuments['suratPerintahPenyelidikanDocumentsRequiredUnlockForm']['count'] ?? 0;
                                            // SKET approved → stage 03 (Penangkapan) boleh tampil
                                            $sketApprovedCount = $countAccidentDocuments['suratKetetapanTentangPenetapanTersangkaDocuments']['count'] ?? 0;
                                            // SPP approved → stage 06 (Penahanan) boleh tampil
                                            $sppApprovedCount = $countAccidentDocuments['suratPerintahPenangkapanDocuments']['count'] ?? 0;
                                        @endphp
                                        @foreach ($documentStages as $documentStage)
                                            @php
                                                $showStage = false;
                                                if ($documentStage->id == '01') {
                                                    $showStage = true;
                                                } elseif ($suratPerintahPenyelidikanDocumentsCountRequiredUnlockForm > 0) {
                                                    if ($documentStage->id == '03') {
                                                        // Stage Penangkapan: SKET harus approved
                                                        $showStage = $sketApprovedCount > 0;
                                                    } elseif ($documentStage->id == '06') {
                                                        // Stage Penahanan: SPP harus approved
                                                        $showStage = $sppApprovedCount > 0;
                                                    } else {
                                                        $showStage = true;
                                                    }
                                                }
                                            @endphp
                                            @if($showStage)
                                                <option value="{{ $documentStage->id }}">
                                                    {{ $documentStage->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group col-lg-11 mt-4">
                                    <select id="typeDocument" name="typeDocument" class="form-select">
                                        <option value="">---Pilih Jenis Dokumen---</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark-blue" id="documentSubmit">
                        <i class="bi bi-blus-circle"></i> <b>{{ 'Buat Dokumen' }}</b>
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('case-document-table-script')
    <script>
        function loadTypeDocumentOptions() {
            var classDocumentID = $('#classDocument').val();
            if (!classDocumentID) {
                $('#typeDocument').empty();
                $('#typeDocument').append('<option value="">---Pilih Jenis Dokumen---</option>');
                return;
            }
            $.ajax({
                url: "/document/type-document/" + classDocumentID + "?accident_id={{ $id }}&_=" + Date.now(),
                type: "GET",
                dataType: "json",
                cache: false,
                success: function(data) {
                    $('#typeDocument').empty();
                    $('#typeDocument').append(
                        '<option value="">---Pilih Jenis Dokumen---</option>');
                    $.each(data, function(key, value) {
                        $('#typeDocument').append('<option value="' + value.id + '">' +
                            value.name + '</option>');
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                "order": []
            });

            $('#documentSubmit').prop('disabled', true);
        });

        $('#addDocument').on('shown.bs.modal', function() {
            loadTypeDocumentOptions();
        });

        $('#classDocument').on('change', function() {
            loadTypeDocumentOptions();
        });

        $('#typeDocument').on('change', function() {
            var classDocumentID = $('#classDocument').val();
            var typeDocumentID = $('#typeDocument').val();
            if (classDocumentID && typeDocumentID) {
                $('#documentSubmit').prop('disabled', false);
            } else {
                $('#documentSubmit').prop('disabled', true);
            }
        });

        $('.reject-notes').on('click', function() {
            var message = $(this).data('message');
            return Swal.fire({
                title: 'Catatan Dikembalikan',
                text: message,
                icon: 'info',
                confirmButtonText: 'Tutup',
            });
        });

        $('.copy-document-id').on('click', function() {
            var copyText = $(this).data('document-id');
            navigator.clipboard.writeText(copyText);
            return Swal.fire({
                title: 'Copied Document ID',
                text: copyText,
                icon: 'success',
                confirmButtonText: 'Tutup',
            });
        });

        // Handler untuk tombol hapus SP2HP
        $('.delete-sp2hp').on('click', function() {
            var sp2hpId = $(this).data('id');
            var accidentId = $(this).data('accident-id');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus dokumen SP2HP ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("doc.sp2hp-document.destroy") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: sp2hpId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Gagal menghapus dokumen',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
