@php
    $_title = 'Perkara';
@endphp

@extends('layouts.app')

@section('content')
    <a class="btn-back" href="{{ route('produktivitas') }}"><i class="bi bi-arrow-left"></i> Kembali ke Daftar
        Produktivitas</a>
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                    <div class="d-flex flex-column">
                        <h5 class="mb-3" style="display: none">Accident Id : <strong>{{ $id }}</strong></h5>
                        <h5 class="mb-3 text-blue-dark"><strong>Nomor LP : </strong> <strong>{{ $no_lp }}</strong>
                        </h5>
                        <h6 class="mb-3">Tanggal Kejadian : <strong>{{ $accident_date }}</strong></h6>
                        <h6 class="mb-3">Tanggal Tindak Lanjut : <strong>{{ $accident_tindak_lanjut }}</strong></h6>
                        <h6 class="mb-3">Proses : <strong>{{ $accident_proses }}</strong></h6>
                        <h6 class="">Aktitvitas Terakhir : <strong>{{ $accident_last_update }} - {{ $tipe_update }}
                                {{ $tipe_berkas }}</strong></h6>
                    </div>
                </div>
                @php
                    $suratPemberitahuanDimulainyaPenyidikanDocumentsCount =
                        $countAccidentDocuments['suratPemberitahuanDimulainyaPenyidikanDocuments']['count'] ?? 0;
                    $suratPerintahPenyelidikanDocumentsCount =
                        $countAccidentDocuments['suratPerintahPenyelidikanDocuments']['count'] ?? 0;
                    $suratKetetapanTentangKetetapanTersangkaDocumentsCount =
                        $countAccidentDocuments['suratKetetapanTentangPenetapanTersangkaDocuments']['count'] ?? 0;
                @endphp

                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                    <div class="row px-2">
                        @foreach ($selra as $selras)
                            @if ($selras->id == $accident_selra_flag)
                                <button class="btn btn-danger text-white font-weight-bold mb-2 btn-block" disabled><b>Status
                                        Selra :</b> {{ $selras->name }}</button>
                                <br />
                            @elseif(empty($accident_selra_flag))
                                <button class="btn btn-danger text-white font-weight-bold mb-2 btn-block" disabled><b>Status
                                        Selra :</b> Dalam Proses</button>
                                <br />
                            @endif
                        @endforeach
                    </div>

                    <form id="upload-ketetapan-form" action="/upload-surat-ketetapan/{{ $id }}" method="POST"
                        enctype="multipart/form-data" style="display: none">

                        <div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12 mt-3 mb-1">
                                <h5 class="text-blue-dark fw-bold">Dokumen Ketetapan Selra</h5>
                            </div>
                            <div class="col-lg-10  col-md-10 col-sm-10 col-10">
                                @if ($upload_surat_ketetapan == null)
                                @else
                                    <input type="text" value="1" id="value_surat_ketetapan" hidden>
                                    <a target="_blank" href="/upload-surat-ketetapan/{{ $id }}" id=""
                                        class="btn btn-primary">LIHAT</a></span>
                                @endif
                            </div>
                        </div>
                    </form>

                    <button type="button" class="btn btn-primary text-white font-weight-bold my-2" data-bs-toggle="modal"
                        data-bs-target="#submitSelraModal">SIMPAN SELRA</button>

                    <div class="modal fade" id="submitSelraModal" tabindex="-1" aria-labelledby="submitSelraModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Selra</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('submit_selra', ['accidentId' => $id]) }}" method="POST"
                                    enctype="multipart/form-data" id="submitSelraForm">
                                    @csrf
                                    <div class="modal-body">
                                        <!-- error alert -->
                                        @if ($errors->any())
                                            <div class="card-body">
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            @if (strtotime($accident_report_date) >= strtotime('2023-10-01'))
                                                <div class="mt-3 alert attentionBox alert-warning" role="alert">
                                                    Selra menjadi POM/TNI, Diversi dan SP2LID minimal <b>Sprinlidik</b>,
                                                    sedangkan untuk P21, SP3
                                                    minimal <b>SPDP</b> <br>
                                                </div>
                                            @endif
                                            @if (
                                                $suratKetetapanTentangKetetapanTersangkaDocumentsCount < 1 &&
                                                    strtotime($accident_report_date) >= strtotime('2023-10-01'))
                                                <div class="mt-3 alert attentionBox alert-danger" role="alert">
                                                    LP Ini Belum Memenuhi Syarat Untuk Selra P21 Karena Belum Memiliki Data
                                                    Tersangka Yang Ditetapkan
                                                </div>
                                            @endif

                                            @if ($accident_md > 0 && $category_laka == 'Kontra')
                                                <div class="mt-3 alert attentionBox alert-danger" role="alert">
                                                    <h4>LP Ini Tidak Memenuhi Syarat Untuk Selra SP2LID Karena Terdapat
                                                        Korban Meninggal Dunia</h4>
                                                </div>
                                            @endif

                                            <label for="selraType" class="form-label">Jenis Selra</label>
                                            <select class="form-control select2" name="selraType" id="selraType" required>
                                                <option value="">--Pilih Selra--</option>
                                                @if (
                                                    ($suratPemberitahuanDimulainyaPenyidikanDocumentsCount > 0 &&
                                                        $suratKetetapanTentangKetetapanTersangkaDocumentsCount > 0) ||
                                                        strtotime($accident_report_date) < strtotime('2023-10-01'))
                                                    <option value="P21TAHAP2">
                                                        P21 Tahap 2
                                                    </option>
                                                @endif
                                                @foreach ($selra as $item)
                                                    @if (!in_array($item->id, ['S0107', 'S0106']))
                                                        @if (
                                                            (in_array($item->id, ['S0101']) &&
                                                                $suratPemberitahuanDimulainyaPenyidikanDocumentsCount > 0 &&
                                                                $suratKetetapanTentangKetetapanTersangkaDocumentsCount > 0) ||
                                                                (strtotime($accident_report_date) < strtotime('2023-10-01') && in_array($item->id, ['S0101', 'S0102'])))
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }}
                                                            </option>
                                                        @elseif (
                                                            (in_array($item->id, ['S0102']) && $suratPemberitahuanDimulainyaPenyidikanDocumentsCount > 0) ||
                                                                (strtotime($accident_report_date) < strtotime('2023-10-01') && in_array($item->id, ['S0101', 'S0102'])))
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }}
                                                            </option>
                                                        @elseif(
                                                            (in_array($item->id, ['S0108']) &&
                                                                $suratPerintahPenyelidikanDocumentsCount > 0 &&
                                                                (($category_laka === 'Kontra' && (int) $accident_md === 0) || $category_laka === 'Tunggal')) ||
                                                                (in_array($item->id, ['S0108']) &&
                                                                    (int) $accident_md === 0 &&
                                                                    strtotime($accident_report_date) < strtotime('2023-10-01')))
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }}
                                                            </option>
                                                        @elseif(
                                                            (in_array($item->id, ['S0103', 'S0104']) && $suratPerintahPenyelidikanDocumentsCount > 0) ||
                                                                (strtotime($accident_report_date) < strtotime('2023-10-01') && in_array($item->id, ['S0103', 'S0104'])))
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }}
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>

                                            <div class="mb-3" id="rjCompletion" style="display: none;">
                                                <div
                                                    class="p-3 border-start border-4 border-danger bg-light rounded-end shadow-sm">
                                                    <div class="form-check d-flex align-items-center">
                                                        <input class="form-check-input mt-0" type="checkbox"
                                                            name="is_completed_with_rj" id="isCompletedWithRJ"
                                                            value="1">
                                                        <label class="form-check-label ms-3" for="isCompletedWithRJ"
                                                            style="cursor: pointer;">
                                                            <strong class="text-primary d-block">Penyelesaian via
                                                                Restorative Justice (RJ)</strong>
                                                            <span class="text-muted small">Centang jika kasus ini
                                                                diselesaikan melalui jalur damai/kekeluargaan.</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            @error('selraType')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="uploadDate" class="form-label">Tanggal Upload</label>
                                            <input type="text" class="form-control" id="uploadDate" name="uploadDate"
                                                value="{{ date('Y-m-d') }}" readonly>

                                            @error('uploadDate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="selraDate" class="form-label">Tanggal Ketetapan Selra</label>
                                            <input class="form-control" id="selraDate" name="selraDate"
                                                placeholder="YYYY-MM-DD" autocomplete="off"
                                                value="{{ old('selraDate') }}" data-provide="datepicker" required>

                                            @error('selraDate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="selraNumber" class="form-label">Nomor Dokumen Ketetapan
                                                Selra</label>
                                            <input type="text" class="form-control" id="selraNumber"
                                                name="selraNumber" required>

                                            @error('selraNumber')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="selraFile" class="form-label">File Dokumen Ketetapan (pdf, max: 8
                                                MB)</label>
                                            <input type="file" class="form-control" id="selraFile" name="selraFile"
                                                required>

                                            @error('selraFile')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary"
                                            id="submitSelraButton">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Konfirmasi Submit Selra -->
                    <div class="modal fade" id="confirmSelraModal" tabindex="-1"
                        aria-labelledby="confirmSelraModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmSelraModalLabel">Konfirmasi Pembaharuan Dokumen
                                        Selra</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Mohon pastikan data berikut sudah sesuai:</p>
                                    <ul class="mb-0">
                                        <li><b>Jenis Selra:</b> <span id="cfmSelraType">-</span></li>
                                        <li><b>Tanggal Ketetapan:</b> <span id="cfmSelraDate">-</span></li>
                                        <li><b>Nomor Dokumen:</b> <span id="cfmSelraNumber">-</span></li>
                                        <br><b>1. Surat Ketetapan Sudah diberi Nomor.</b>
                                        <br><b>2. Surat Ketetapan Sudah ditanda tangan basah.</b>
                                        <br><b>3. Surat Ketetapan Sudah dilakukan setampel.</b>
                                        <br><b>4. Tanggal Ketetapan yang di input sudah sesuai dengan dokumen yang
                                            diunggah.</b>
                                        <br><b>5. Surat Ketetapan sudah sesuai dengan Selra yang dipilih.</b>
                                        <br><b>6. Nomor LP pada Dokumen yang diunggah sudah sesuai dengan nomor LP yang di
                                            Input.</b>
                                    </ul>
                                    <br>
                                    <p>Dokumen yang diunggah akan diperiksa oleh Tim Helpdesk ICELL, Jika terjadi
                                        ketidaksesuaian akan dikembalikan.</p>

                                    <div class="mb-3">
                                        <strong>RJ (Restorative Justice):</strong>
                                        <span id="cfmIsRJ" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Periksa Lagi</button>
                                    <button type="button" class="btn btn-primary" id="confirmYesBtn" disabled>Ya, simpan
                                        (<span id="confirmCountdown">7</span>)</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 alert alert-warning attentionBox" role="alert">
                        Selra menjadi POM/TNI, Diversi dan SP2LID minimal <b>Sprinlidik</b>, sedangkan untuk P21, SP3
                        minimal <b>SPDP</b> <br>
                        --------------------------------- <br>
                        <b>Perhatian</b> : <br> Untuk Selra RJ Sesuai Arahan Dari Pimpinan Mulai Saat Ini Ditiadakan
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($page) && $page == 'participants')
            @include('produktivitas.case-participants')
        @elseif(empty($page))
            <div class="box-body">

                @include('produktivitas.case-document-table')

                <div class="card mt-4">
                    <div class="card-header tahapan">
                        <h5 class="fw-bold card-title m-0">Tahapan Tidak Lanjut LP</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-4 alert alert-danger attentionBox">
                            <div class="text-center">
                                <b>
                                    PERHATIAN !
                                    <hr />
                                </b>
                                SAAT INI SEDANG DILAKUKAN PENGEMBANGAN DAN PEMBARUAN FITUR DOKUMEN MINDIK.<br />
                                TAHAP PEMINDAHAN FITUR SEDANG DILAKUKAN. UNTUK SAAT INI, GUNAKAN <b>AREA "BERKAS
                                    PERKARA"</b> DI
                                ATAS UNTUK INPUT DOKUMEN YANG SUDAH TERSEDIA.<br />
                                PEMINDAHAN FITUR AKAN DILAKUKAN SECARA BERTAHAP.<br />
                                JIKA INPUT DOKUMEN DI BAWAH INI TIDAK TERSEDIA, ANDA LANGSUNG DAPAT MELAKUKAN INPUT DI
                                <b>AREA
                                    BOX "BERKAS PERKARA"</b> YANG TERLETAK DI ATAS.<br />
                            </div>
                        </div>

                        <div class="row mb-2 accordion" id="accordionProduktivitas">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                @include('produktivitas.surat-tugas.surat-tugas')
                                @include('produktivitas.surat-saksi.saksi')
                                @include('produktivitas.surat-pemanggilan-tersangka.tersangka')
                                @include('produktivitas.surat-penahanan.penahanan')
                                @include('produktivitas.surat-penggeledahan.penggeledahan')
                                @include('produktivitas.surat-penyitaan.penyitaan')
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                @include('produktivitas.surat-penyegelan.penyegelan')
                                @include('produktivitas.surat-labfor.labfor')
                                @include('produktivitas.surat-pemblokiran-bank.pemblokiran')
                                @include('produktivitas.surat-dpo-dpb.dpo-dpb')
                                @include('produktivitas.surat-penghentian.penghentian')
                                @include('produktivitas.surat-penangkapan.penangkapan')
                            </div>
                        </div>

                        <form action="{{ route('upload.imageUpload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div>
                                <input id="form_id" name="form_id" type="text" value="imageUpload" hidden>
                            </div>
                            <div>
                                <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                    hidden>
                            </div>
                            <div>
                                <input class="mar-bot-2 mt-2" type="file" name="files[]" multiple>
                                <button id="btnImage" type="submit" class="btn btn-primary"> Upload </button>
                            </div>
                            @if ($message = Session::get('success'))
                                <strong>{{ $message }}</strong>
                            @endif
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input image.</strong>
                                </div>
                            @endif
                        </form>
                        <div class="showImage">
                            {{-- @include('produktivitas.image-show.imageShow') --}}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                showConfirmButton: false,
                timer: 2500
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: @json(session('error')),
                showConfirmButton: true,
            });
        </script>
    @endif

    @if (!empty($lastSelraReject) && !empty($shouldShowRejectModal))
        <script>
            (function() {
                const rejectId = '{{ $lastSelraReject->id }}';
                const storageKey = 'selraRejectShown:' + rejectId;
                if (sessionStorage.getItem(storageKey)) return;

                // reasons sudah array murni dari PHP
                const reasons = @json($rejectReasons ?? []);

                const reasonsHtml = reasons.length ?
                    `<ul class="list-unstyled mb-0 ps-3">
                 ${reasons.map(r => `<li class="mb-2 text-danger fw-medium">• ${r}</li>`).join('')}
               </ul>` :
                    '<em class="text-muted small">Tidak ada alasan.</em>';

                Swal.fire({
                    width: '40rem',
                    icon: 'warning',
                    title: `<div class="fw-bold" style="font-size:2.5rem">Selra Dikembalikan</div>`,
                    html: `
            <div class="text-start">
                <!-- Nomor LP -->
                <div class="p-3 rounded-3 border bg-light mb-3">
                    <div class="small text-muted mb-1">Nomor LP</div>
                    <div class="fw-semibold h6 m-0">
                        {{ $no_lp ?? ($lastSelraReject->accident->no_lp ?? '-') }}
                    </div>
                </div>

                <!-- Jenis & Tanggal -->
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 border bg-white">
                            <div class="small text-muted mb-1">Jenis Selra</div>
                            <div class="fw-semibold">{{ $lastSelraReject->type_name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 border bg-white">
                            <div class="small text-muted mb-1">Tanggal Dikembalikan</div>
                            <div class="fw-semibold">
                                {{ optional($lastSelraReject->rejected_at)->format('d-m-Y H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alasan per poin -->
                <div class="p-3 rounded-3 border bg-white">
                    <div class="small text-muted mb-2 fw-semibold text-danger">Alasan Penolakan:</div>
                    <div style="font-size:1.1rem; line-height:1.7;">
                        ${reasonsHtml}
                    </div>
                </div>
            </div>
            `,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow',
                        cancelButton: 'btn btn-danger rounded-pill px-4'
                    },
                    focusCancel: true
                }).then(() => {
                    sessionStorage.setItem(storageKey, '1');
                });
            })();
        </script>
    @endif

    <!-- Delete Button -->
    <script src="{{ asset('js/laravel.js') }}"></script>

    @stack('case-document-table-script')
    @stack('case-participants-script')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#selraDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                endDate: new Date()
            });
            $('#selraDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#selraType').select2({
                theme: 'classic',
                width: '100%',
                dropdownParent: $('#selraType').parent(),
            });
        });

        $(document).ready(function() {
            var $form = $('#submitSelraForm');
            var $btn = $('#submitSelraButton');
            var $yesBtn = $('#confirmYesBtn');
            var confirmModal = new bootstrap.Modal(document.getElementById('confirmSelraModal'));
            var isConfirmed = false; // guard agar handler submit tidak loop

            const $selraType = $('#selraType');
            const $rjWrapper = $('#rjCompletion');
            const $isRJCheckbox = $('#isCompletedWithRJ');

            function toggleRJField() {
                const selected = $selraType.val();
                const showRJ = (selected === 'S0102' || selected === 'S0108');

                $rjWrapper.toggle(showRJ);

                if (!showRJ) {
                    $isRJCheckbox.prop('checked', false);
                }else {
                    $isRJCheckbox.prop('checked', false);
                }
            }

            toggleRJField();

            $selraType.on('change', toggleRJField);

            // Helper: validasi nomor selra (huruf, angka, slash)
            function validateSelraNumber() {
                var selraNumber = $('#selraNumber').val() || '';
                var containsLetter = /[a-zA-Z]/.test(selraNumber);
                var containsNumber = /[0-9]/.test(selraNumber);
                var containsSlash = /\//.test(selraNumber);

                if (!containsLetter || !containsNumber || !containsSlash) {
                    Swal.fire({
                        title: 'Perhatian',
                        text: 'Nomor Dokumen Ketetapan Selra harus mengandung huruf, angka, dan tanda garis miring ("/").',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                return true;
            }

            // Handler submit form: hentikan submit biasa, tampilkan modal konfirmasi
            $form.on('submit', function(event) {
                if (isConfirmed) {
                    // Submit final yang sudah dikonfirmasi → biarkan lanjut
                    return;
                }

                event.preventDefault();

                // Validasi HTML5 (required, dsb)
                if (!$form[0].reportValidity()) {
                    return; // browser akan highlight field yang belum valid
                }

                // Validasi format nomor selra
                if (!validateSelraNumber()) {
                    return;
                }

                // Isi ringkasan ke modal
                var selraTypeText = $('#selraType option:selected').text() || '-';
                $('#cfmSelraType').text(selraTypeText.trim());
                $('#cfmUploadDate').text($('#uploadDate').val() || '-');
                $('#cfmSelraDate').text($('#selraDate').val() || '-');
                $('#cfmSelraNumber').text($('#selraNumber').val() || '-');

                var fileInput = document.getElementById('selraFile');
                var fileName = (fileInput && fileInput.files && fileInput.files[0]) ? fileInput.files[0]
                    .name : '-';
                $('#cfmSelraFile').text(fileName);

                const isRJChecked = $isRJCheckbox.prop('checked');
                const rjDisplay = isRJChecked ?
                    '<span class="text-success fw-bold">Ya, diselesaikan dengan RJ</span>' :
                    '<span class="text-muted">Tidak Diselesaikan dengan RJ</span>';

                $('#cfmIsRJ').html(rjDisplay);

                // Reset & mulai countdown 7 detik
                var remain = 7;

                // Kunci tombol, JANGAN ubah inner HTML tombol (agar span tetap ada)
                $yesBtn.prop('disabled', true);

                // Set angka awal di span
                $('#confirmCountdown').text(remain);

                // Buat interval untuk update angka setiap 1 detik
                var t = setInterval(function() {
                    remain--;
                    $('#confirmCountdown').text(remain);

                    if (remain <= 0) {
                        clearInterval(t);
                        // Aktifkan tombol dan pastikan span terakhir terisi 0 (opsional)
                        $yesBtn.prop('disabled', false);
                        $('#confirmCountdown').text('0');
                    }
                }, 1000);


                // Tampilkan modal
                confirmModal.show();
            });

            // Klik tombol "Simpan" di modal utama → trigger submit (akan ditangkap handler di atas)
            $btn.on('click', function(e) {
                // biarkan type="submit" bawa ke handler submit di atas
            });

            // Klik YA di modal konfirmasi → submit final
            $yesBtn.on('click', function() {
                // Lock tombol & tampilkan spinner untuk cegah double submit
                $yesBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
                isConfirmed = true; // izinkan submit lewat guard
                confirmModal.hide(); // tutup modal
                $form.trigger('submit'); // kirim form
                // Optional: juga kunci tombol Simpan utama
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('.attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);
        });

        $(function() {
            //start saksi
            $('#saksi_id').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            var saksi = $('.saksi-datatable').DataTable({
                processing: true,
                serverSide: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('get_saksi') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'birth_date',
                        name: 'birth_date'
                    },
                    {
                        data: 'citizen',
                        name: 'citizen'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        // orderable: true,
                        // searchable: true
                    },
                ]
            });


            $(".btn-saksi").click(function(e) {
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var saksi_id = $("#saksi_id").val();
                var accident_id_saksi = $("#accident_id_saksi").val();
                var name = $("#name_saksi").val();
                var gender = $("#gender").val();
                var city = $("#city").val();
                var birth_date = $("#birth_date").val();
                var religion = $("#religion").val();
                var job = $("#job").val();
                var education = $("#education").val();
                var phone = $("#phone").val();
                var citizen = $("#citizen").val();
                var address = $("#address_saksi").val();

                $.ajax({
                    url: "{{ route('add_saksi') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        saksi_id: saksi_id,
                        accident_id_saksi: accident_id_saksi,
                        name: name,
                        gender: gender,
                        city: city,
                        birth_date: birth_date,
                        religion: religion,
                        job: job,
                        education: education,
                        phone: phone,
                        citizen: citizen,
                        address: address
                    },
                    success: function(data) {
                        $('.alert-success').remove();
                        $('#saksi-form')[0].reset();
                        $('#saksi_id').val('');
                        saksi.draw();
                        printMsg(data);
                    }
                });

                function printMsg(msg) {
                    if ($.isEmptyObject(msg.error)) {
                        console.log(msg.success);
                        $('.modal-saksi').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )
                        $('.alert-block').css('display', 'block').append('<strong>' +
                            'Sukses Menambah Saksi' + '</strong>');
                    } else {
                        $.each(msg.error, function(key, value) {
                            $('.' + key + '_err').text(value);
                        });
                    }
                }
            });

            $('body').on('click', '.editBook', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('edit_saksi') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#saksi_id').val(data.id);
                        $('#name_saksi').val(data.name_saksi);
                        $('#gender').val(data.gender);
                        $('#city').val(data.city);
                        $('#birth_date').val(data.birth_date);
                        $('#religion').val(data.religion);
                        $('#job').val(data.job);
                        $('#education').val(data.education);
                        $('#phone').val(data.phone);
                        $('#citizen').val(data.citizen);
                        $('#address_saksi').val(data.address);
                    }
                })
            });

            $('body').on('click', '.deleteBook', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('delete_saksi') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#saksi_id').val(data.id);
                        saksi.draw();
                    }
                })
            });
            //end saksi


            //start tersangka
            $('#tersangka_id').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            var tersangka = $('.tersangka-datatable').DataTable({
                processing: true,
                serverSide: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('get_tersangka') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'birth_date',
                        name: 'birth_date'
                    },
                    {
                        data: 'citizen',
                        name: 'citizen'
                    },
                    {
                        data: 'identity_type',
                        name: 'identity_type',
                    },
                    {
                        data: 'identity_no',
                        name: 'identity_no'
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ]
            });

            $(".btn-tersangka").click(function(e) {
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var tersangka_id = $("#tersangka_id").val();
                var accident_id = $("#accident_id").val();
                var name = $("#name_tersangka").val();
                var gender = $("#gender_tersangka").val();
                var city = $("#city_tersangka").val();
                var birth_date = $("#birth_date_tersangka").val();
                var religion = $("#religion_tersangka").val();
                var job = $("#job_tersangka").val();
                var education = $("#education_tersangka").val();
                var phone = $("#phone_tersangka").val();
                var citizen = $("#citizen_tersangka").val();
                var address = $("#address_tersangka").val();
                var identity_type = $("#jenis_identitas_tersangka").val();
                var identity_no = $("#identity_no").val();

                $.ajax({
                    url: "{{ route('add_tersangka') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        tersangka_id: tersangka_id,
                        accident_id: accident_id,
                        name: name,
                        gender: gender,
                        city: city,
                        birth_date: birth_date,
                        religion: religion,
                        job: job,
                        education: education,
                        phone: phone,
                        citizen: citizen,
                        address: address,
                        identity_type: identity_type,
                        identity_no: identity_no
                    },
                    success: function(data) {
                        // $('.alert-block').remove();
                        $('.modal-tersangka').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )

                        $('#tersangka-form')[0].reset();
                        $('#tersangka_id').val('');
                        tersangka.draw();

                        printMsg(data);
                    }
                });

                function printMsg(msg) {
                    if ($.isEmptyObject(msg.error)) {
                        console.log(msg.success);
                        $('.modal-tersangka').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )
                        $('.alert-block').css('display', 'block').append('<strong>' +
                            'Sukses Menambah Tersangka' + '</strong>');
                    } else {
                        $.each(msg.error, function(key, value) {
                            $('.' + key + '_err').text(value);
                        });
                    }
                }
            });



            $('body').on('click', '.editTersangka', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('edit_tersangka') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#tersangka_id').val(data.id);
                        $('#name_tersangka').val(data.name);
                        $('#gender_tersangka').val(data.gender);
                        $('#city_tersangka').val(data.city);
                        $('#birth_date_tersangka').val(data.birth_date);
                        $('#religion_tersangka').val(data.religion);
                        $('#job_tersangka').val(data.job);
                        $('#education_tersangka').val(data.education);
                        $('#phone_tersangka').val(data.phone);
                        $('#citizen_tersangka').val(data.citizen);
                        $('#address_tersangka').val(data.address);
                        $('#jenis_identitas_tersangka').val(data.identity_type);
                        $('#identity_no').val(data.identity_no);
                    }
                })
            });
            $('body').on('click', '.deleteTersangka', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('delete_tersangka') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#tersangka_id').val(data.id);
                        tersangka.draw();
                    }
                })
            });
            //end tersangka

            //start barang bukti
            $('#barang_bukti_id').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            var barang_bukti = $('.barang-bukti-datatable').DataTable({
                processing: true,
                serverSide: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('get_barang_bukti') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'jumlah_barang',
                        name: 'jumlah_barang'
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ]
            });

            $(".btn-barang-bukti").click(function(e) {
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var barang_bukti_id = $("#barang_bukti_id").val();
                var accident_id_barang_bukti = $("#accident_id_barang_bukti").val();
                var nama_barang = $("#nama_barang").val();
                var jumlah_barang = $("#jumlah_barang").val();

                $.ajax({
                    url: "{{ route('add_barang_bukti') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        barang_bukti_id: barang_bukti_id,
                        accident_id_barang_bukti: accident_id_barang_bukti,
                        nama_barang: nama_barang,
                        jumlah_barang: jumlah_barang
                    },
                    success: function(data) {
                        // $('.modal-saksi').remove();
                        $('.modal-barang-bukti').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )
                        $('#barang-bukti-form')[0].reset();
                        $('#barang_bukti_id').val('');
                        barang_bukti.draw();
                    }
                });
            });
            //end barang bukti

            //start dpo
            $('#dpo_id').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            var dpo = $('.dpo-datatable').DataTable({
                processing: true,
                serverSide: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('get_dpo') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name_dpo'
                    },
                    {
                        data: 'gender',
                        name: 'gender_dpo'
                    },
                    {
                        data: 'deskripsi_dpo',
                        name: 'deskripsi_dpo'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        // orderable: true,
                        // searchable: true
                    },
                ]
            });

            $('#dpo_tangkaps').change(function() {

                if ($('#dpo_tangkaps').is(':checked')) {
                    $("#dpo_tangkaps").val('1');
                } else {
                    $("#dpo_tangkaps").val('0');
                }
            });
            $(".btn-dpo").click(function(e) {
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var dpo_id = $("#dpo_id").val();
                var accident_id_dpo = $("#accident_id_dpo").val();
                var name_dpo = $("#name_dpo").val();
                var gender_dpo = $("#gender_dpo").val();
                var deskripsi_dpo = $("#deskripsi_dpo").val();
                var dpo_tangkaps = $("#dpo_tangkaps").val();
                $.ajax({
                    url: "{{ route('add_dpo') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        dpo_id: dpo_id,
                        accident_id_dpo: accident_id_dpo,
                        name_dpo: name_dpo,
                        gender_dpo: gender_dpo,
                        deskripsi_dpo: deskripsi_dpo,
                        dpo_tangkaps: dpo_tangkaps
                    },
                    success: function(data) {
                        $('.modal-dpo').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )
                        $('#dpo-form')[0].reset();
                        $('#dpo_id').val('');
                        $('#dpo_tangkaps').val('0');
                        $("#dpo_tangkaps").prop('checked', false);
                        dpo.draw();
                    }
                });
            });
            $('body').on('click', '.editDpo', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('edit_dpo') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#dpo_id').val(data.id);
                        $('#name_dpo').val(data.name);
                        $('#gender_dpo').val(data.gender);
                        $('#deskripsi_dpo').val(data.deskripsi_dpo);
                        check_state_dpo = data.state;

                        if (check_state_dpo == '1') {
                            $("#dpo_tangkaps").prop('checked', true);
                            $("#dpo_tangkaps").val('1');
                        } else {
                            $("#dpo_tangkaps").prop('checked', false);
                            $("#dpo_tangkaps").val('0');
                        }
                    }
                })
            });

            $('body').on('click', '.deleteDpo', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('delete_dpo') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#dpo_id').val(data.id);
                        dpo.draw();
                    }
                })
            });
            //end dpo

            //start dpb
            $('#dpb_id').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            var dpb = $('.dpb-datatable').DataTable({
                processing: true,
                serverSide: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('get_dpb') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis_dpb'
                    },
                    {
                        data: 'no_tnkb',
                        name: 'no_tnkb'
                    },
                    {
                        data: 'deskripsi_dpb',
                        name: 'deskripsi_dpb'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        // orderable: true,
                        // searchable: true
                    },
                ]
            });

            $(".btn-dpb").click(function(e) {
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var dpb_id = $("#dpb_id").val();
                var accident_id_dpb = $("#accident_id_dpb").val();
                var jenis_dpb = $("#jenis_dpb").val();
                var no_tnkb = $("#no_tnkb").val();
                var deskripsi_dpb = $("#deskripsi_dpb").val();

                $.ajax({
                    url: "{{ route('add_dpb') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        dpb_id: dpb_id,
                        accident_id_dpb: accident_id_dpb,
                        jenis_dpb: jenis_dpb,
                        no_tnkb: no_tnkb,
                        deskripsi_dpb: deskripsi_dpb
                    },
                    success: function(data) {
                        $('.modal-dpb').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )
                        $('#dpb-form')[0].reset();
                        $('#dpb_id').val('');
                        dpb.draw();
                    }
                });
            });

            $('body').on('click', '.editDpb', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('edit_dpb') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#dpb_id').val(data.id);
                        $('#jenis_dpb').val(data.jenis);
                        $('#no_tnkb').val(data.no_tnkb);
                        $('#deskripsi_dpb').val(data.deskripsi_dpb);
                    }
                })
            });

            $('body').on('click', '.deleteDpb', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('delete_dpb') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#dpb_id').val(data.id);
                        dpb.draw();
                    }
                })
            });
            //end dpb

            //start sp2hp
            $('#sp2hp').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            var sp2hp = $('.sp2hp-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('get_sp2hp') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'tipe',
                        name: 'tipe'
                    },
                    {
                        data: 'tingkat',
                        name: 'tingkat'
                    },
                    {
                        data: 'nomor_surat',
                        name: 'nomor_surat'
                    },
                    {
                        data: 'kota',
                        name: 'kota'
                    },
                    {
                        data: 'tanggal_terbit',
                        name: 'tanggal_terbit'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address',
                        // orderable: true,
                        // searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ]
            });

            $(".btn-sp2hp").click(function(e) {
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var sp2hp_id = $("#sp2hp_id").val();
                var accident_id_sp2hp = $("#accident_id_sp2hp").val();
                var tipe_sp2hp = $("#tipe_sp2hp").val();
                var tingkat_kasus = $("#tingkat_kasus").val();
                var kota = $("#kota").val();
                var tanggal_terbit = $("#tgl_terbit").val();
                var nomor_surat_1 = $("#nomor_surat_1").val();
                var nomor_surat_2 = $("#nomor_surat_2").val();
                var nomor_surat_3 = $("#nomor_surat_3").val();
                var nomor_surat_4 = $("#nomor_surat_4").val();
                var nomor_surat_5 = $("#nomor_surat_5").val();
                var name = $("#name_sp2hp").val();
                var address = $("#address_sp2hp").val();
                var about = $("#about").val();

                $.ajax({
                    url: "{{ route('add_sp2hp') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        sp2hp_id: sp2hp_id,
                        accident_id_sp2hp: accident_id_sp2hp,
                        tipe: tipe_sp2hp,
                        tingkat: tingkat_kasus,
                        kota: kota,
                        tanggal_terbit: tanggal_terbit,
                        nomor_surat_1: nomor_surat_1,
                        nomor_surat_2: nomor_surat_2,
                        nomor_surat_3: nomor_surat_3,
                        nomor_surat_4: nomor_surat_4,
                        nomor_surat_5: nomor_surat_5,
                        name: name,
                        address: address,
                        deskripsi: about
                    },
                    success: function(data) {
                        $('.alert-success').remove();
                        // $('.modal-saksi').append('<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>')

                        // $('#table-saksi tbody').prepend('<td scope="row">'+aaaaaa+'</td><td>'+data.name+'</td><td>'+data.gender+'</td><td>'+data.city, data.birt_date+'</td><td>'+data.religion+'</td><td>'+data.job+'</td><td>'+data.education+'</td><td>'+data.phone+'</td><td>'+data.citizen+'</td><td>'+data.address+'</td><td>'+test+'</td>');
                        // $('#table-saksi tbody').append('<tr><td>'+data.name+'</td><td>'+data.gender+'</td><td>'+data.city+','+ data.birth_date+'</td><td>'+data.religion+'</td><td>'+data.job+'</td><td>'+data.education+'</td><td>'+data.phone+'</td><td>'+data.citizen+'</td><td>'+data.address+'</td><td>'+'test'+'</td></tr>');
                        $('#sp2hp-form')[0].reset();
                        $('#sp2hp_id').val('');
                        sp2hp.draw();
                        // table.draw();
                        // $('#table-saksi tbody').remove();
                        // append('<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="test">×</button><strong class="success-msg"></strong></div>');
                        printMsg(data);
                    }
                });

                function printMsg(msg) {
                    if ($.isEmptyObject(msg.error)) {
                        console.log(msg.success);
                        $('.alert-sp2hp').append(
                            '<div class="alert alert-success alert-block" style="display: none;"><button type="button" class="close" data-dismiss="alert">×</button><strong class="success-msg"></strong></div>'
                        )
                        $('.alert-block').css('display', 'block').append('<strong>' +
                            'Sukses' + '</strong>');
                    } else {
                        $.each(msg.error, function(key, value) {
                            $('.' + key + '_err').text(value);
                        });
                    }
                }
            });

            $('body').on('click', '.editSp2hp', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('edit_sp2hp') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#sp2hp_id').val(data.id);
                        $('#tipe_sp2hp').val(data.tipe);
                        $('#tingkat_kasus').val(data.tingkat);
                        $('#nomor_surat_1').val(data.nomor_surat_1);
                        $('#nomor_surat_2').val(data.nomor_surat_2);
                        $('#nomor_surat_3').val(data.nomor_surat_3);
                        $('#nomor_surat_4').val(data.nomor_surat_4);
                        $('#nomor_surat_5').val(data.nomor_surat_5);
                        $('#kota').val(data.kota);
                        $('#tgl_terbit').val(data.tanggal_terbit);
                        $('#name_sp2hp').val(data.name);
                        $('#address_sp2hp').val(data.address);
                        $('#about').val(data.deskripsi);
                    }
                })
            });

            $('body').on('click', '.deleteSp2hp', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('delete_sp2hp') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#sp2hp_id').val(data.id);
                        sp2hp.draw();
                    }
                })
            });

            $('.btn-reset-sp2hp').click(function() {
                $('#sp2hp_id').val("");
                $('#tipe_sp2hp').val(" ");
                $('#tingkat_kasus').val(" ");
                $('#nomor_surat_1').val("");
                $('#nomor_surat_2').val("");
                $('#nomor_surat_3').val("");
                $('#nomor_surat_4').val("");
                $('#nomor_surat_5').val("");
                $('#kota').val("");
                $('#tgl_terbit').val("");
                $('#name_sp2hp').val("");
                $('#address_sp2hp').val("");
                $('#about').val("");
            });

            //end sp2hp

        });
        //dpb
        //tersangka
        //    $(function () {
        //     });
        $(".btn-saksi").click(function() {
            var name = $("#name_saksi").val();
            var gender = $("#gender").val();
            var birth_date = $("#birth_date").val();
            var religion = $("#religion").val();
            var job = $("#job").val();
            var education = $("#education").val();
            var phone = $("#phone").val();
            var citizen = $("#citizen").val();
            var address = $("#address").val();
            var array = [name, gender, city, birth_date, religion, job, education, phone, citizen, address];
            for (var x = 0; x < array.length; x++) {
                if (array[x] == '') {
                    alert("Mohon untuk di isi semua form nya");
                    break;
                }
            }
        });
        $(document).ready(function() {
            var check_surat_ketetapan = $('#value_surat_ketetapan').val();
            var selra_flag = $("#selra_flag").val();
            var check_state_selra_flag = $("#check_state_selra_flag_1").val();
            if (check_surat_ketetapan == 1) {
                $('#upload-ketetapan-form').show();
            } else {
                $('#upload-ketetapan-form').hide();
            }

            if (selra_flag == 'S0101') {
                $('#p21_tahap2').show();
                $('.p21-section').removeClass('d-none');

            }
            if (check_state_selra_flag == '1') {
                $("#p21_tahap2_1").attr('checked', true);
                $('.row-p21-tahap-2').removeClass('d-none');
            }
            $("#dpo_Tangkaps").prop('checked', false);
            //---FOR MENU ARROWS--//

            //modal notification if null
            $('#notification_modal_sprindik').click(function() {
                $('#notificationModalSprindik').modal('show');
            })

            //modal kategori 1

            $('#perintah_tugas').click(function() {
                $('#myModalSuratTugas').modal('show');
                // $('#myModal').modal({backdrop: 'static', keyboard: false});
                // setTimeout(function () {
                //     $('.modal-backdrop').remove();
                // }, 100);
            });

            $('#edit_tugas').click(function() {
                $('#myEditModalSuratTugas').modal('show');
            });

            $('#perintah_penyelidikan').click(function() {
                $('#myModalSuratPenyelidikan').modal('show');
            });

            $('#edit_penyelidikan').click(function() {
                $('#myEditModalSuratPenyelidikan').modal('show');
            });


            $('#perintah_penyidikan').click(function() {
                $('#myModalSuratPenyidikan').modal('show');
            });

            $('#edit_penyidikan').click(function() {
                $('#myEditModalSuratPenyidikan').modal('show');
            });

            $('#perintah_spdp').click(function() {
                $('#myModalSuratSpdp').modal('show');
                // $('#myModal').modal({backdrop: 'static', keyboard: false});
                // setTimeout(function () {
                //     $('.modal-backdrop').remove();
                // }, 100);
            });

            $('#spdp_upload').click(function() {
                $('#myModalSPDPUpload').modal('show');
            });

            $('#edit_spdp').click(function() {
                $('#myEditModalSuratSPDP').modal('show');
            });

            $('#sp2hp').click(function() {
                $('#myModalSp2hp').modal('show');
            });

            $('#laporan_polisi').click(function() {
                $('#ModalLaporanPolisi').modal('show');
            });

            $('#BA_Penangkapan').click(function() {
                $('#ModalBAPenangkapan').modal('show');
            });
            $('#BA_Pemotretan').click(function() {
                $('#ModalBAPemotretan').modal('show');
            });
            $('#BA_PengambilanDarah').click(function() {
                $('#BAPengambilanDarah').modal('show');
            });
            $('#laporan_hasil_penyelidikan').click(function() {
                $('#ModalHasilPenyelidikan').modal('show');
            });
            $('#Berita_acara_introgasi').click(function() {
                $('#ModalBeritaAcaraIntrogasi').modal('show');
            });
            //end kategori 1

            //start kategori 2
            $('#surat_saksi_1').click(function() {
                $('#myModalSaksi1').modal('show');
            });

            $('#surat_perintah_membawa_saksi').click(function() {
                $('#myModalSaksi2').modal('show');
            });

            $('#berita_acara_membawa_saksi').click(function() {
                $('#myModalSaksi3').modal('show');
            });

            $('#edit_berita_acara_membawa_saksi').click(function() {
                $('#myModalSaksi3').modal('show');
            });

            $('#berita_acara_penyumpahan_saksi').click(function() {
                $('#myModalSaksi4').modal('show');
            });

            $('#surat_saksi_5').click(function() {
                $('#myModalSaksi5').modal('show');
            });

            $('#surat_saksi_6').click(function() {
                $('#myModalSaksi6').modal('show');
            });
            //end kategori 2

            //start kategori 3
            $('#daftar_tersangka').click(function() {
                $('#myModalTersangka').modal('show');
            });

            $('#surat_pemanggilan_tersangka_1').click(function() {
                $('#myModalTersangka1').modal('show');
            });

            $('#surat_pemanggilan_tersangka_2').click(function() {
                $('#myModalTersangka2').modal('show');
            });

            $('#surat_pemanggilan_tersangka_3').click(function() {
                $('#myModalTersangka3').modal('show');
            });

            $('#surat_pemanggilan_tersangka_4').click(function() {
                $('#myModalTersangka4').modal('show');
            });
            $('#surat_pemanggilan_tersangka_5').click(function() {
                $('#myModalTersangka5').modal('show');
            });
            $('#surat_pemanggilan_tersangka_6').click(function() {
                $('#myModalTersangka6').modal('show');
            });
            $('#surat_pemanggilan_tersangka_7').click(function() {
                $('#myModalTersangka7').modal('show');
            });
            $('#surat_pemanggilan_tersangka_8').click(function() {
                $('#myModalTersangka8').modal('show');
            });
            $('#surat_pemanggilan_tersangka_9').click(function() {
                $('#myModalTersangka9').modal('show');
            });
            //end kategori 3

            //start kategori 4
            $('#surat_penahanan_1').click(function() {
                $('#myModalPenahanan1').modal('show');
            });

            $('#surat_penahanan_2').click(function() {
                $('#myModalPenahanan2').modal('show');
            });

            $('#surat_penahanan_3').click(function() {
                $('#myModalPenahanan3').modal('show');
            });

            $('#surat_penahanan_4').click(function() {
                $('#myModalPenahanan4').modal('show');
            });

            $('#surat_penahanan_5').click(function() {
                $('#myModalPenahanan5').modal('show');
            });

            $('#surat_penahanan_6').click(function() {
                $('#myModalPenahanan6').modal('show');
            });

            $('#surat_penahanan_7').click(function() {
                $('#myModalPenahanan7').modal('show');
            });

            $('#surat_penahanan_8').click(function() {
                $('#myModalPenahanan8').modal('show');
            });

            $('#surat_penahanan_9').click(function() {
                $('#myModalPenahanan9').modal('show');
            });

            $('#surat_penahanan_10').click(function() {
                $('#myModalPenahanan10').modal('show');
            });
            //end kategori 4

            //start kategori 5
            $('#surat_penggeledahan_1').click(function() {
                $('#myModalPenggeledahan1').modal('show');
            });
            $('#surat_penggeledahan_2').click(function() {
                $('#myModalPenggeledahan2').modal('show');
            });
            $('#surat_penggeledahan_3').click(function() {
                $('#myModalPenggeledahan3').modal('show');
            });
            $('#surat_penggeledahan_4').click(function() {
                $('#myModalPenggeledahan4').modal('show');
            });
            //end kategori 5

            //start katgeri 6
            $('#surat_penyitaan_1').click(function() {
                $('#myModalPenyitaan1').modal('show');
            });
            $('#surat_penyitaan_2').click(function() {
                $('#myModalPenyitaan2').modal('show');
            });
            $('#surat_penyitaan_3').click(function() {
                $('#myModalPenyitaan3').modal('show');
            });
            $('#surat_penyitaan_4').click(function() {
                $('#myModalPenyitaan4').modal('show');
            });

            $('#edit_surat_penyitaan_4').click(function() {
                $('#myEditModalPenyitaan4').modal('show');
            });

            $('#surat_penyitaan_5').click(function() {
                $('#myModalPenyitaan5').modal('show');
            });

            $('#surat_penyitaan_6').click(function() {
                $('#myModalPenyitaan6').modal('show');
            });

            $('#surat_penyitaan_7').click(function() {
                $('#myModalPenyitaan7').modal('show');
            });

            $('#surat_penyitaan_8').click(function() {
                $('#myModalPenyitaan8').modal('show');
            });

            $('#surat_penyitaan_9').click(function() {
                $('#myModalPenyitaan9').modal('show');
            });

            $('#surat_penyitaan_10').click(function() {
                $('#myModalPenyitaan10').modal('show');
            });

            $('#surat_penyitaan_11').click(function() {
                $('#myModalPenyitaan11').modal('show');
            });

            $('#surat_penyitaan_12').click(function() {
                $('#myModalPenyitaan12').modal('show');
            });

            $('#surat_penyitaan_13').click(function() {
                $('#myModalPenyitaan13').modal('show');
            });

            $('#surat_penyitaan_14').click(function() {
                $('#myModalPenyitaan14').modal('show');
            });

            $('#surat_penyitaan_15').click(function() {
                $('#myModalPenyitaan15').modal('show');
            });

            $('#surat_penyitaan_16').click(function() {
                $('#myModalPenyitaan16').modal('show');
            });

            $('#surat_penyitaan_17').click(function() {
                $('#myModalPenyitaan17').modal('show');
            });

            $('#surat_penyitaan_18').click(function() {
                $('#myModalPenyitaan18').modal('show');
            });

            $('#surat_penyitaan_19').click(function() {
                $('#myModalPenyitaan19').modal('show');
            });

            $('#surat_penyitaan_20').click(function() {
                $('#myModalPenyitaan20').modal('show');
            });

            $('#surat_penyitaan_21').click(function() {
                $('#myModalPenyitaan21').modal('show');
            });



            //end kategori 6

            //start kategori 7
            $('#surat_penyegelan_1').click(function() {
                $('#myModalPenyegelan1').modal('show');
            });

            $('#surat_penyegelan_2').click(function() {
                $('#myModalPenyegelan2').modal('show');
            });

            $('#edit_surat_penyegelan_2').click(function() {
                $('#myEditModalPenyegelan2').modal('show');
            });

            $('#surat_penyegelan_3').click(function() {
                $('#myModalPenyegelan3').modal('show');
            });
            //end kategori 7

            //start kategori 8
            $('#surat_labfor_1').click(function() {
                $('#myModalLabfor1').modal('show');
            });

            $('#surat_labfor_2').click(function() {
                $('#myModalLabfor2').modal('show');
            });

            $('#surat_labfor_3').click(function() {
                $('#myModalLabfor3').modal('show');
            });

            $('#surat_labfor_4').click(function() {
                $('#myModalLabfor4').modal('show');
            });

            $('#surat_labfor_5').click(function() {
                $('#myModalLabfor5').modal('show');
            });

            $('#surat_labfor_6').click(function() {
                $('#myModalLabfor6').modal('show');
            });

            $('#surat_labfor_7').click(function() {
                $('#myModalLabfor7').modal('show');
            });
            //end kategori 8

            //start kategori 9
            $('#surat_pemblokiran_bank_1').click(function() {
                $('#myModalPemblokiranBank1').modal('show');
            });

            $('#surat_pemblokiran_bank_2').click(function() {
                $('#myModalPemblokiranBank2').modal('show');
            });

            $('#surat_pemblokiran_bank_3').click(function() {
                $('#myModalPemblokiranBank3').modal('show');
            });

            $('#surat_pemblokiran_bank_4').click(function() {
                $('#myModalPemblokiranBank4').modal('show');
            });
            //end kategori 9

            //start kategori 10
            $('#dpo_1').click(function() {
                $('#myModalDpo1').modal('show');
            });

            $('#dpo_2').click(function() {
                $('#myModalDpo2').modal('show');
            });

            $('#dpb_1').click(function() {
                $('#myModalDpb1').modal('show');
            });

            $('#dpb_2').click(function() {
                $('#myModalDpb2').modal('show');
            });
            //end kategori 10

            //start kategori 11
            $('#surat_penghentian_1').click(function() {
                $('#myModalPenghentian1').modal('show');
            });

            $('#surat_penghentian_2').click(function() {
                $('#myModalPenghentian2').modal('show');
            });

            $('#surat_penghentian_3').click(function() {
                $('#myModalPenghentian3').modal('show');
            });

            $('#surat_penghentian_4').click(function() {
                $('#myModalPenghentian4').modal('show');
            });

            $('#surat_penghentian_5').click(function() {
                $('#myModalPenghentian5').modal('show');
            });

            $('#surat_penghentian_6').click(function() {
                $('#myModalPenghentian6').modal('show');
            });

            $('#surat_penghentian_7').click(function() {
                $('#myModalPenghentian7').modal('show');
            });

            $('#surat_penghentian_8').click(function() {
                $('#myModalPenghentian8').modal('show');
            });

            $('#surat_penghentian_9').click(function() {
                $('#myModalPenghentian9').modal('show');
            });

            $('#surat_penghentian_10').click(function() {
                $('#myModalPenghentian10').modal('show');
            });

            $('#surat_penghentian_11').click(function() {
                $('#myModalPenghentian11').modal('show');
            });

            $('#surat_penghentian_12').click(function() {
                $('#myModalPenghentian12').modal('show');
            });

            $('#surat_penghentian_13').click(function() {
                $('#myModalPenghentian13').modal('show');
            });

            $('#edit_sp3').click(function() {
                $('#myEditModalSuratSP3').modal('show');
            });
            $('#surat_penghentian_14').click(function() {
                $('#myModalPenghentian14').modal('show');
            });
            $('#surat_penghentian_15').click(function() {
                $('#myModalPenghentian15').modal('show');
            });
            //end kategori 11

            //start kategori 12
            $('#surat_penangkapan_1').click(function() {
                $('#myModalPenangkapan1').modal('show');
            });

            $('#surat_penangkapan_2').click(function() {
                $('#myModalPenangkapan2').modal('show');
            });

            $('#surat_penangkapan_3').click(function() {
                $('#myModalPenangkapan3').modal('show');
            });

            $('#surat_penangkapan_4').click(function() {
                $('#myModalPenangkapan4').modal('show');
            });

            $('#surat_penangkapan_5').click(function() {
                $('#myModalPenangkapan5').modal('show');
            });

            $('#surat_penangkapan_6').click(function() {
                $('#myModalPenangkapan6').modal('show');
            });
            //end kategori 12

            $('#selra_flag').change(function() {
                var _token = $("input[name='_token']").val();
                var selra_flag = $(this).val();
                var accident_id = $('#accident_id_selra').val();
                var state_selra_flag = null;
                $('#update_selra').val(selra_flag);

                $('#update-ketetapan-form').show();
                alert("Untuk Merubah Selra, Diwajibkan Untuk Mengupload Surat Ketetapannya");

                if (selra_flag == 'S0101') {
                    $("#p21_tahap2").show();
                    $("#p21_tahap2_1").prop('checked', false);
                } else {
                    $("#p21_tahap2").hide();
                }
                if (selra_flag == 'S0101') {
                    $("#p21_tahap2").show();
                    $("#p21_tahap2_1").prop('checked', false);
                } else {
                    $("#p21_tahap2").hide();
                }

                if (selra_flag == 'S0107') {
                    $("#upload-ketetapan-form").hide();
                    $("#case-reset-resolution-form").show();
                    //upload - ketetapan - form
                } else {
                    $("#upload-ketetapan-form").show();
                    $("#case-reset-resolution-form").hide();
                }
            });

            $('#p21_tahap2_1').change(function() {
                var _token = $("input[name='_token']").val();
                var accident_id = $('#accident_id_selra').val();

                if ($('#p21_tahap2_1').is(':checked')) {
                    var state_selra_flag = 1;
                } else {
                    var state_selra_flag = null;
                }
                $("#loader").fadeIn();
                $(".loaderbg").fadeIn();
                $.ajax({
                    url: "{{ route('update_state_selra') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        accident_id: accident_id,
                        state_selra_flag: state_selra_flag
                    },
                    success: function(data) {
                        $("#loader").fadeOut();
                        $(".loaderbg").fadeOut();
                        alert("Sukses Mengubah Status Selra P21");

                    }
                });

            });

            $("#p21_tahap2").prop('checked', true);


            $('#test2').click(function() {
                $('#myModalSuratTugasPenyidikan').modal('show');
            });

            $('#edit').click(function() {
                alert("Data edit");
            });

            $('#lihat').click(function() {
                alert("Data lihat");
            });

            $('#delete').click(function() {
                $('#myModalDelete').modal('show');
            });

            var i = 1;
            //tambah input field
            $('#add_dasar').click(function() {
                i++;
                $('#dynamic_field_dasar').append('<tr id="row_dasar' + i +
                    '" class="dynamic-added"><td><input type="text" name="dasar[]" placeholder="Dasar Hukum" class="form-control dasar_list" /></td><td><button type="button" name="remove" id="' +
                    i + '" class="btn btn-danger btn_remove_dasar">X</button></td></tr>');
            });

            $(document).on('click', '.btn_remove_dasar', function() {
                var button_id = $(this).attr("id");
                $('#row_dasar' + button_id + '').remove();
            });

            //start surat tugas
            $('#add_officer').click(function() {
                addRowSuratTugas();
            });

            $('#add_officer_edit').click(function() {
                addRowEditSuratTugas();
            });

            // var j=1;
            function addRowSuratTugas() {
                // j++;
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="officer_id[]" name="officer_id[]" class="form-control" required>' +
                    '<option value="" }} class="option">Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    '<option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_officer">X</button>' +
                    '</div>' +
                    '</div>'

                $('#add_surat_tugas').append(div);
            }

            function addRowEditSuratTugas() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="edit_officer_surat_tugas[]" name="edit_officer_surat_tugas[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_edit_officer">X</button>' +
                    '</div>' +
                    '</div>'
                $('#edit_surat_tugas').append(div);
            }



            $(document).on('click', '.btn_remove_officer', function() {
                var test = $('#add_surat_tugas div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            $(document).on('click', '.btn_remove_edit_officer', function() {
                var test = $('#edit_surat_tugas div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            //end surat tugas

            //start add surat penyelidikan
            $('#add_officer_penyelidikan').click(function() {
                addRowSuratPenyelidikan();
            });

            $('#add_officer_edit_penyelidikan').click(function() {
                addRowEditSuratPenyelidikan();
            });

            function addRowSuratPenyelidikan() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="officer_id_penyelidikan[]" name="officer_id_penyelidikan[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_penyelidikan">X</button>' +
                    '</div>' +
                    '</div>'

                $('#add_surat_penyelidikan').append(div);
            }

            function addRowEditSuratPenyelidikan() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="edit_officer_surat_penyelidikan[]" name="edit_officer_surat_penyelidikan[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_edit_penyelidikan">X</button>' +
                    '</div>' +
                    '</div>'
                $('#edit_surat_penyelidikan').append(div);
            }

            $(document).on('click', '.btn_remove_penyelidikan', function() {
                var test = $('#add_surat_penyelidikan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            $(document).on('click', '.btn_remove_edit_penyelidikan', function() {
                var test = $('#edit_surat_penyelidikan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });


            //start add surat penyidikan
            $('#add_officer_penyidikan').click(function() {
                addRowSuratPenyidikan();
            });

            $('#add_officer_edit_penyidikan').click(function() {
                addRowEditSuratPenyidikan();
            });

            function addRowSuratPenyidikan() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="officer_id_penyidikan[]" name="officer_id_penyidikan[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_penyidikan">X</button>' +
                    '</div>' +
                    '</div>'
                $('#add_surat_penyidikan').append(div);
            }

            function addRowEditSuratPenyidikan() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="edit_officer_surat_penyidikan[]" name="edit_officer_surat_penyidikan[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_edit_penyidikan">X</button>' +
                    '</div>' +
                    '</div>'
                $('#edit_surat_penyidikan').append(div);
            }

            $(document).on('click', '.btn_remove_penyidikan', function() {
                var test = $('#add_surat_penyidikan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            $(document).on('click', '.btn_remove_edit_penyidikan', function() {
                var test = $('#edit_surat_penyidikan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            //end surat penyidikan

            //start add surat penyitaan
            $('#add_officer_penyitaan').click(function() {
                addRowSuratPenyitaan();
            });

            $('#add_officer_edit_penyitaan').click(function() {
                addRowEditSuratPenyitaan();
            });

            function addRowSuratPenyitaan() {
                var div = '<div class="add-row mb-3">' +
                    '<div class="input-group">' +
                    '<select id="officer_id_penyitaan[]" name="officer_id_penyitaan[]" class="form-select" aria-desribedby="#btn-remove-penyitaan">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '<button type="button" name="remove" class="remove btn btn-danger btn_remove_penyitaan" id="btn-remove-penyitaan"><i class="bi bi-x-square"></i></button>' +
                    '</div>' +
                    '</div>'
                $('#add_surat_penyitaan').append(div);
            }

            function addRowEditSuratPenyitaan() {
                var div = '<div class="add-row mb-3">' +
                    '<div class="input-group">' +
                    '<select id="edit_officer_surat_penyitaan[]" name="edit_officer_surat_penyitaan[]" class="form-select" aria-desribedby="#btn-remove-edit-penyitaan">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '<button type="button" name="remove" class="btn btn-danger remove btn_remove_edit_penyitaan" id="btn-remove-edit-penyitaan"><i class="bi bi-x-square"></i></button>' +
                    '</div>' +
                    '</div>'
                $('#edit_surat_penyitaan').append(div);
            }

            $(document).on('click', '.btn_remove_penyitaan', function() {
                var test = $('#add_surat_penyitaan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            $(document).on('click', '.btn_remove_edit_penyitaan', function() {
                var test = $('#edit_surat_penyitaan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });
            //end surat penyitaan


            //start surat persetujuan penyegelan
            $('#add_officer_penyegelan').click(function() {
                addRowSuratPenyegelan();
            });

            $('#add_officer_edit_penyegelan').click(function() {
                addRowEditSuratPenyegelan();
            });

            function addRowSuratPenyegelan() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="officer_id_penyegelan[]" name="officer_id_penyegelan[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_penyegelan">X</button>' +
                    '</div>' +
                    '</div>'
                $('#add_surat_penyegelan').append(div);
            }

            function addRowEditSuratPenyegelan() {
                var div = '<div class="row add-row">' +
                    '<div class="input-group col-lg-11">' +
                    '<select id="edit_officer_surat_penyegelan[]" name="edit_officer_surat_penyegelan[]" class="form-control">' +
                    '<option value="" }}>Pilih Petugas</option>' +
                    '@foreach ($officer as $officers)' +
                    ' <option value="{{ $officers->id }}"' +
                    '{{ old('officer') == $officers->id ? 'selected' : '' }}>' +
                    '{{ $officers->id }} - {{ $officers->first_name }} {{ $officers->last_name }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</div>' +
                    '<div class="remove col-lg-1">' +
                    '<button type="button" name="remove" class="btn btn_removeX btn_remove_edit_penyegelan">X</button>' +
                    '</div>' +
                    '</div>'
                $('#edit_surat_penyegelan').append(div);
            }

            $(document).on('click', '.btn_remove_penyegelan', function() {
                var test = $('#add_surat_penyegelan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });

            $(document).on('click', '.btn_remove_edit_penyegelan', function() {
                var test = $('#edit_surat_penyegelan div.add-row').length;
                if (test == 1) {
                    alert("You Can not Remove Last Row");
                } else {
                    $(this).parent().parent().remove();
                }
            });
            //end surat penyitaan
            //end surat persetujuan penyegelan


        });

        $('.header-item').click(function() {
            $(this).next('.item-content').slideToggle();
            $(this).find('.dropdown-side').toggleClass('rotate');
        });

        $('#birth_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            container: "#myModalSaksi1",
            orientation: 'auto bottom'
        });

        $('#birth_date_tersangka').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            container: "#myModalTersangka",
            orientation: 'auto bottom'
        });

        $('#tgl_terbit').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            container: "#myModalSp2hp",
            orientation: 'auto bottom'
        });

        //check form ada atau tidak
        $('#check_berita_acara_membawa_saksi').change(function() {
            alert('test');
        });
    </script>
@endpush
