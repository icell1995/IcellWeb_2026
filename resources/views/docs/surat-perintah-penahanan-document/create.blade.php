@php
    $_title = 'Surat Perintah Penahanan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penahanan (S-17)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN APARAT PENEGAK HUKUM
                        LAINNYA DALAM KERANGKA SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
                    </b>
                </div>
            </div>

            <!-- Error alert -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-penahanan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="suratPerintahPenahananForm" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <h5 class="fw-bold text-blue-dark">1. Identitas Dokumen</h5>

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            value="{{ $accident->no_lp }}" readonly>
                    </div>
                </div>

                {{-- Nomor Dokumen SP-Han --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_number">Nomor Dokumen
                        <small class="text-muted d-block font-weight-normal">(Bisa dikosongkan saat draft, diisi saat aksi "Isi Nomor")</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="document_number" type="text"
                            class="form-control @error('nomor') is-invalid @enderror font-weight-bold"
                            name="nomor" value="{{ old('nomor') }}"
                            placeholder="Contoh: SP.Han/01/X/2026/Reskrim">
                        @error('nomor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal', date('Y-m-d')) }}"
                            data-provide="datepicker">
                        @error('tanggal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Nomor SPDP (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_spdp">Nomor SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_spdp" type="text"
                            class="form-control @error('nomor_spdp') is-invalid @enderror font-weight-bold"
                            name="nomor_spdp" value="{{ old('nomor_spdp', $nomorSpdp) }}" readonly
                            placeholder="Nomor SPDP otomatis dari sistem">
                        <small class="text-muted">(*Nomor SPDP bersifat tetap dan diambil otomatis dari dokumen SPDP perkara ini)</small>
                        @error('nomor_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal SPDP (Read Only) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_spdp">Tanggal SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="tanggal_spdp" type="text"
                            class="form-control @error('tanggal_spdp') is-invalid @enderror font-weight-bold"
                            name="tanggal_spdp"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_spdp', $tanggalSpdp ? date('Y-m-d', strtotime($tanggalSpdp)) : date('Y-m-d')) }}"
                            readonly>
                        <small class="text-muted">(*Tanggal SPDP bersifat tetap dan diambil otomatis dari dokumen SPDP perkara ini)</small>
                        @error('tanggal_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Kode Satker Penerbit SPDP (Read Only sesuai Polres pada No LP) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_satker_penerbit_spdp">Kode Satker Penerbit SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="kode_satker_penerbit_spdp" type="text"
                            class="form-control @error('kode_satker_penerbit_spdp') is-invalid @enderror font-weight-bold"
                            name="kode_satker_penerbit_spdp" value="{{ old('kode_satker_penerbit_spdp', $kodeSatkerDefault) }}" readonly>
                        <small class="text-muted">(*Kode Satker Kepolisian penerbit SPDP sesuai dengan Polres pada Nomor LP: {{ $accident->polres->full_name ?? $accident->polres->name ?? '-' }})</small>
                        @error('kode_satker_penerbit_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Nomor Surat Perintah Penangkapan (Jika ada) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_surat_perintah_penangkapan">Nomor Surat Perintah Penangkapan
                        <small class="text-muted d-block font-weight-normal">(Opsional jika dilakukan penangkapan sebelumnya)</small>
                    </label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_penangkapan" type="text"
                            class="form-control" name="nomor_surat_perintah_penangkapan"
                            value="{{ old('nomor_surat_perintah_penangkapan') }}"
                            placeholder="Contoh: SP.Kap/01/X/2026/Reskrim">
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold text-blue-dark">2. Konten Dokumen & Penahanan</h5>

                {{-- Jenis Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_jenis_penahanan">Jenis Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="kode_jenis_penahanan" id="kode_jenis_penahanan" required>
                            @foreach ($masterJenisPenahanan as $val => $lbl)
                                <option value="{{ $val }}" {{ old('kode_jenis_penahanan', 1) == $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                        @error('kode_jenis_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Satker Rutan / Lapas (Khusus Rutan) --}}
                <div class="input-group row mb-3 ms-0" id="rutanSection">
                    <label class="fw-bold col-sm-3 col-form-label" for="kode_satker_tempat_penahanan">Rutan / Lapas Tempat Penahanan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="kode_satker_tempat_penahanan" id="kode_satker_tempat_penahanan">
                            <option value="">--Pilih Rutan / Lapas--</option>
                            @foreach ($prisons as $p)
                                <option value="{{ $p->emp_id ?? $p->id }}" {{ old('kode_satker_tempat_penahanan') == ($p->emp_id ?? $p->id) ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->emp_id ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Pilih Rutan/Lapas tujuan penahanan untuk pelaporan SPPT-TI)</small>
                    </div>
                </div>

                {{-- Keterangan Tempat Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="lokasi_penahanan">Keterangan Tempat Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="lokasi_penahanan" type="text"
                            class="form-control @error('lokasi_penahanan') is-invalid @enderror font-weight-bold"
                            name="lokasi_penahanan"
                            value="{{ old('lokasi_penahanan', 'RUTAN ' . strtoupper($accident->polres->full_name ?? '')) }}"
                            placeholder="Contoh: RUTAN POLRES ... / Jl. Merdeka No. 10 / KOTA ...">
                        @error('lokasi_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Mulai Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_mulai">Tanggal Mulai Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                            data-provide="datepicker" required>
                        @error('tanggal_mulai')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Akhir Penahanan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_akhir">Tanggal Akhir Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal_akhir') is-invalid @enderror" id="tanggal_akhir" name="tanggal_akhir"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_akhir', date('Y-m-d', strtotime('+20 days'))) }}"
                            data-provide="datepicker" required>
                        <small class="text-muted d-block mt-1">
                            <span id="durasiPenahananText" class="badge bg-secondary">Durasi: 21 Hari</span>
                        </small>
                        @error('tanggal_akhir')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- 3. DAFTAR TERSANGKA --}}
                <h5 class="fw-bold text-blue-dark">3. Tersangka yang Ditahan<span class="text-danger fs-5">*</span></h5>

                @if ($suspects->count() == 0)
                    <div class="alert alert-warning" role="alert">
                        Belum ada Tersangka yang terdaftar pada perkara ini. Silahkan tambahkan Tersangka pada menu Progress Perkara terlebih dahulu sebelum membuat Surat Perintah Penahanan.
                    </div>
                @else
                    <div class="table-responsive my-2">
                        <table class="table table-bordered table-striped" id="suspectTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col" width="5%">Pilih</th>
                                    <th scope="col">Nama Tersangka</th>
                                    <th scope="col">NIK / Identitas</th>
                                    <th scope="col">Tempat, Tgl Lahir</th>
                                    <th scope="col">Jenis Kelamin</th>
                                    <th scope="col">Pekerjaan</th>
                                    <th scope="col">Alamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suspects as $suspect)
                                    @php
                                        $age = $suspect->age ?? ($suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->age : '-');
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="suspects[]" value="{{ $suspect->id }}"
                                                class="suspect-checkbox" id="suspect_{{ $suspect->id }}"
                                                {{ in_array($suspect->id, old('suspects', [])) || $loop->first ? 'checked' : '' }}>
                                        </td>
                                        <td class="align-middle fw-bold">
                                            <label for="suspect_{{ $suspect->id }}" class="mb-0 cursor-pointer">
                                                {{ $suspect->name }}
                                            </label>
                                        </td>
                                        <td class="align-middle">{{ $suspect->identity_number ?? '-' }}</td>
                                        <td class="align-middle">
                                            {{ $suspect->birth_place ?? '-' }},
                                            {{ $suspect->birth_date ? date('d-m-Y', strtotime($suspect->birth_date)) : '-' }}
                                            ({{ $age }} thn)
                                        </td>
                                        <td class="align-middle">{{ $suspect->gender->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $suspect->job->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $suspect->address ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @error('suspects')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif

                <hr class="my-4">

                {{-- 4. PASAL YANG DISANGKAKAN (INHERITED AUTOMATICALLY) --}}
                <h5 class="fw-bold text-blue-dark">4. Pasal yang Disangkakan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Daftar UU & Pasal</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="p-3 border rounded bg-light">
                            @if (!empty($pasalList) && count($pasalList) > 0)
                                <ul class="mb-0 ps-3">
                                    @foreach ($pasalList as $pasal)
                                        <li><strong class="text-dark">{{ $pasal }}</strong></li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted fst-italic">Pasal akan diambil secara otomatis dari Surat Perintah Penyidikan terkait.</span>
                            @endif
                        </div>
                        <small class="text-muted">(*Pasal yang disangkakan diproses secara otomatis oleh sistem dari Surat Perintah Penyidikan)</small>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 5. PETUGAS YANG DIPERINTAHKAN --}}
                <h5 class="fw-bold text-blue-dark">5. Petugas yang Diperintahkan</h5>

                <div class="row col-12 my-2 ms-0">
                    <div id="internalOfficer">
                        <div class="alert alert-primary my-2" role="alert">
                            Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai petugas yang diperintahkan.
                        </div>

                        <div class="row my-2">
                            <div class="col-md-7">
                                <div class="input-group">
                                    <select class="custom-select select2" id="officerInternalMemberOption"
                                        aria-describedby="officerInternalMemberOptionAddButtton">
                                        <option value="">--Pilih Petugas--</option>
                                        @foreach ($internalOfficers as $data)
                                            @php
                                                $fullName = \App\Helpers\PeopleNameHelper::getFullName($data->first_title, $data->first_name, $data->last_name, $data->last_title);
                                                $positionName = $data->position->name ?? '';
                                                $rankName = $data->rank->name ?? '';
                                                $policeName = $data->police->name ?? '';
                                            @endphp
                                            <option value="{{ $data->register_number }}"
                                                data-register-number="{{ $data->register_number }}"
                                                data-rank-name="{{ $rankName }}"
                                                data-name="{{ $fullName }}"
                                                data-position-name="{{ $positionName }}"
                                                data-police-name="{{ $policeName }}">
                                                {{ $data->register_number . ' - ' . $fullName . ' | ' . $positionName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="officerInternalMemberOptionAddButtton">
                                        <i class="bi bi-plus-circle"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive my-2">
                            <table class="table table-bordered" id="internalOfficerMemberTable">
                                <thead class="table-danger">
                                    <tr class="text-center">
                                        <th scope="col">Nama</th>
                                        <th scope="col">Pangkat</th>
                                        <th scope="col">NRP</th>
                                        <th scope="col">Jabatan</th>
                                        <th scope="col">Kesatuan</th>
                                        <th scope="col">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- 6. PEJABAT PENANDATANGAN --}}
                <h5 class="fw-bold text-blue-dark">6. Pejabat Penandatangan</h5>

                <div class="input-group row mb-4 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory">Pejabat Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="signatory" id="signatory" required>
                            <option value="">--Pilih Pejabat Penandatangan--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $fullName = \App\Helpers\PeopleNameHelper::getFullName($data->first_title, $data->first_name, $data->last_name, $data->last_title);
                                    $positionName = $data->position->name ?? '';
                                    $rankName = $data->rank->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" {{ old('signatory') == $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $fullName . ' | ' . $positionName . ' (' . $rankName . ')' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Pejabat yang berwenang menandatangani Surat Perintah Penahanan)</small>
                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="row col-12 my-4 ms-0">
                    <div class="col text-end">
                        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">
                            Batal
                        </a>
                        <button class="btn btn-primary" type="button" id="btnSubmitForm">
                            <i class="bi bi-save"></i> Simpan Dokumen
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Select2 Init
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Toggle Satker Rutan based on jenis penahanan
            function toggleRutanSection() {
                var jenis = $('#kode_jenis_penahanan').val();
                if (jenis == '1') {
                    $('#rutanSection').slideDown();
                } else {
                    $('#rutanSection').slideUp();
                }
            }
            $('#kode_jenis_penahanan').on('change', toggleRutanSection);
            toggleRutanSection();

            // Hitung durasi hari otomatis
            function updateDurasi() {
                var tglMulai = $('#tanggal_mulai').val();
                var tglAkhir = $('#tanggal_akhir').val();
                if (tglMulai && tglAkhir) {
                    var d1 = new Date(tglMulai);
                    var d2 = new Date(tglAkhir);
                    var diffTime = d2.getTime() - d1.getTime();
                    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    if (diffDays > 0) {
                        $('#durasiPenahananText').text('Durasi: ' + diffDays + ' Hari').removeClass('bg-danger').addClass('bg-secondary');
                    } else {
                        $('#durasiPenahananText').text('Tanggal Akhir harus sama atau setelah Tanggal Mulai').removeClass('bg-secondary').addClass('bg-danger');
                    }
                }
            }
            $('#tanggal_mulai, #tanggal_akhir').on('change', updateDurasi);
            updateDurasi();

            // Tambah Petugas yang Diperintahkan (Poin 6)
            $('#officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerInternalMemberOption').find('option:selected');
                var registerNumber = selectedOption.val();

                if (!registerNumber) {
                    return Swal.fire({
                        title: 'Perhatian',
                        text: 'Silahkan pilih petugas terlebih dahulu',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                }

                var rankName = selectedOption.data('rank-name') || '-';
                var name = selectedOption.data('name') || '-';
                var positionName = selectedOption.data('position-name') || '-';
                var policeName = selectedOption.data('police-name') || '-';

                // Cek duplikasi
                var isAppended = false;
                $('#internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text().trim();
                    if (appendedRegisterNumber === registerNumber) {
                        isAppended = true;
                        return false;
                    }
                });

                if (isAppended) {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Petugas sudah ada dalam daftar',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                var newRow = $('<tr class="text-center"></tr>');
                newRow.append('<td>' + name + '</td>');
                newRow.append('<td>' + rankName + '</td>');
                newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                newRow.append('<td>' + positionName + '</td>');
                newRow.append('<td>' + policeName + '</td>');
                newRow.append('<td><input type="hidden" name="internalOfficers[]" value="' + registerNumber + '">' +
                    '<button class="btn btn-danger btn-sm deleteInternalOfficer" type="button"><i class="bi bi-trash"></i></button></td>'
                );

                $('#internalOfficerMemberTable tbody').append(newRow);
            });

            // Hapus baris petugas
            $(document).on('click', '.deleteInternalOfficer', function() {
                $(this).closest('tr').remove();
            });

            // Helper scroll ke error pertama
            function scrollToFirstError() {
                var $firstError = $('.frontend-error, .invalid-feedback.d-block').first();
                if (!$firstError.length) {
                    $firstError = $('.is-invalid, .border-danger').first();
                }
                if ($firstError && $firstError.length) {
                    var el = $firstError[0];
                    if (el && typeof el.scrollIntoView === 'function') {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    var topPos = $firstError.offset() ? $firstError.offset().top : 0;
                    $('html, body, .content-wrapper, .wrapper, main').stop().animate({
                        scrollTop: Math.max(0, topPos - 140)
                    }, 400);
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            // Real-time pembersihan error saat user mengetik atau memilih opsi
            $(document).on('input change', 'input, select, textarea', function() {
                $(this).removeClass('is-invalid');
                if ($(this).next('.select2-container').length) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
                $(this).siblings('.frontend-error, .invalid-feedback').remove();
                $(this).next('.frontend-error, .invalid-feedback').remove();
                var $container = $(this).closest('.table-responsive, .input-group');
                if ($container.length) {
                    $container.siblings('.frontend-error, .invalid-feedback').remove();
                    $container.next('.frontend-error, .invalid-feedback').remove();
                }
            });

            // Form Submit validation dengan pesan error di bawah masing-masing field
            $('#btnSubmitForm').on('click', function(e) {
                e.preventDefault();

                // Bersihkan pesan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                var errors = [];

                function markError(fieldSelector, message) {
                    var $field = $(fieldSelector);
                    if (!$field.length) return;

                    if ($field.is('table')) {
                        $field.addClass('border border-danger is-invalid');
                        var $wrapper = $field.closest('.table-responsive, .input-group');
                        var $container = $wrapper.length ? $wrapper : $field;
                        $container.siblings('.frontend-error, .invalid-feedback').remove();
                        $container.next('.frontend-error, .invalid-feedback').remove();
                        $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        errors.push(message);
                        return;
                    } else if ($field.is(':checkbox')) {
                        $field.addClass('is-invalid');
                        var $container = $field.closest('.table-responsive, .input-group');
                        $container.siblings('.frontend-error, .invalid-feedback').remove();
                        $container.next('.frontend-error, .invalid-feedback').remove();
                        $container.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                        errors.push(message);
                        return;
                    } else {
                        $field.addClass('is-invalid');
                    }
                    if ($field.next('.select2-container').length) {
                        $field.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                    }
                    var $target = $field.next('.select2-container').length ? $field.next('.select2-container') : $field;
                    $target.siblings('.frontend-error, .invalid-feedback').remove();
                    $target.next('.frontend-error, .invalid-feedback').remove();
                    $target.after('<div class="invalid-feedback d-block frontend-error">' + message + '</div>');
                    errors.push(message);
                }

                function checkInput(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || !$field.is(':visible')) return;
                    var raw = $field.val();
                    var val = (raw !== null && raw !== undefined) ? String(raw).trim() : '';
                    if (!val || val === '') {
                        markError(fieldSelector, label + ' harus diisi');
                    }
                }

                function checkSelect(fieldSelector, label) {
                    var $field = $(fieldSelector);
                    if ($field.is(':disabled') || (!$field.is(':visible') && !$field.next('.select2-container:visible').length)) return;
                    var raw = $field.val();
                    var hasVal = Array.isArray(raw) ? raw.length > 0 : (raw && String(raw).trim() !== '' && String(raw).trim() !== '0');
                    if (!hasVal) {
                        markError(fieldSelector, label + ' harus dipilih');
                    }
                }

                // 1. Validasi Identitas Dokumen
                checkInput('#tanggal', 'Tanggal Ditandatangani Dokumen');
                checkInput('#nomor_spdp', 'Nomor SPDP');
                checkInput('#tanggal_spdp', 'Tanggal SPDP');
                checkInput('#kode_satker_penerbit_spdp', 'Kode Satker Penerbit SPDP');

                // 2. Validasi Konten Dokumen & Penahanan
                checkSelect('#kode_jenis_penahanan', 'Jenis Penahanan');
                if ($('#kode_jenis_penahanan').val() == '1') {
                    checkSelect('#kode_satker_tempat_penahanan', 'Rutan / Lapas Tempat Penahanan');
                }
                checkInput('#lokasi_penahanan', 'Keterangan Tempat Penahanan');
                checkInput('#tanggal_mulai', 'Tanggal Mulai Penahanan');
                checkInput('#tanggal_akhir', 'Tanggal Akhir Penahanan');

                var tglMulai = $('#tanggal_mulai').val();
                var tglAkhir = $('#tanggal_akhir').val();
                if (tglMulai && tglAkhir) {
                    var d1 = new Date(tglMulai);
                    var d2 = new Date(tglAkhir);
                    if (d2 < d1) {
                        markError('#tanggal_akhir', 'Tanggal Akhir harus sama atau setelah Tanggal Mulai');
                    }
                }

                // 3. Validasi Tersangka minimal 1
                if ($('.suspect-checkbox:checked').length === 0) {
                    markError('#suspectTable', 'Tersangka yang Ditahan harus dipilih minimal 1 orang');
                }

                // 4. Validasi Petugas yang Diperintahkan minimal 1
                if ($('#internalOfficerMemberTable tbody tr').length === 0) {
                    markError('#internalOfficerMemberTable', 'Petugas yang Diperintahkan harus ditambahkan minimal 1 orang');
                }

                // 5. Validasi Pejabat Penandatangan
                checkSelect('#signatory', 'Pejabat Penandatangan');

                // Jika terdapat error di sisi frontend, scroll ke elemen pertama dan batalkan submit
                if (errors.length > 0) {
                    scrollToFirstError();
                    return false;
                }

                // Validasi AJAX ke server
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penahanan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#suratPerintahPenahananForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Silahkan menunggu proses simpan data',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#suratPerintahPenahananForm')[0].submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.code == 422 && response.errors) {
                                $.each(response.errors, function(key, messages) {
                                    var msg = Array.isArray(messages) ? messages[0] : messages;
                                    var $target = $('#' + key + ', [name="' + key + '"]');
                                    if ($target.length) {
                                        markError($target, msg);
                                    } else if (key === 'suspects') {
                                        markError('#suspectTable', msg);
                                    } else if (key === 'internalOfficers' || key === 'officers') {
                                        markError('#internalOfficerMemberTable', msg);
                                    }
                                });
                                scrollToFirstError();
                            } else {
                                var message = response.message || response.errors || 'Terjadi kesalahan saat memproses data.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Perhatian',
                                    text: typeof message === 'string' ? message : JSON.stringify(message)
                                });
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                });
            });
        });
    </script>
@endpush
