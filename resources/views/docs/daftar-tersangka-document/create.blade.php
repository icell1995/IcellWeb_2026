@php
    $_title = 'Daftar Tersangka (S-38.3)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .table-tersangka th {
            background-color: #000080;
            color: #ffffff;
            font-size: 13px;
            vertical-align: middle;
            text-align: center;
        }
        .table-tersangka td {
            vertical-align: middle;
            padding: 6px;
        }
        .btn-dark-blue {
            background-color: #000080;
            border-color: #000080;
            color: #ffffff;
        }
        .btn-dark-blue:hover {
            background-color: #000066;
            border-color: #000066;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')

    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Daftar Tersangka (S-38.3)</h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br />
                        <br />
                        DATA INI WAJIB DIISI DENGAN LENGKAP KARENA MERUPAKAN BAGIAN DARI BERKAS ADMINISTRASI PENYIDIKAN (MINDIK)
                        DALAM SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
                    </b>
                </div>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="card-body p-0 mb-3">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="card-body p-0 mb-3">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.daftar-tersangka-document.store') }}" method="POST" id="formDaftarTersangka">
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- ─── 1. INFORMASI DASAR DOKUMEN ─── --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0"><i class="bi bi-info-circle me-1"></i> Informasi Dasar Dokumen</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nomor_lp">Nomor LP<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nomor_lp" type="text"
                                    class="form-control font-weight-bold"
                                    name="nomor_lp" value="{{ old('nomor_lp', $accident->no_lp) }}" required readonly>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="tanggal_lp">Tanggal LP<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="tanggal_lp" type="date"
                                    class="form-control"
                                    name="tanggal_lp"
                                    value="{{ old('tanggal_lp', $accident->report_date ? \Carbon\Carbon::parse($accident->report_date)->format('Y-m-d') : date('Y-m-d')) }}" required readonly>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nomor_surat" type="text"
                                    class="form-control"
                                    name="nomor_surat"
                                    value="{{ old('nomor_surat', 'S-38.3/' . ($accident->no_lp ? preg_replace('/[^0-9A-Za-z]/', '/', $accident->no_lp) : 'X/2026')) }}"
                                    required placeholder="Contoh: S-38.3/01/X/2026/Reskrim">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat">Tanggal Dokumen<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="tanggal_surat" type="date"
                                    class="form-control"
                                    name="tanggal_surat"
                                    value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="dikeluarkan_di">Dikeluarkan di<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                @php
                                    $defaultLocation = in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat');
                                @endphp
                                <input id="dikeluarkan_di" type="text"
                                    class="form-control"
                                    name="dikeluarkan_di"
                                    value="{{ old('dikeluarkan_di', $defaultLocation) }}" required placeholder="Contoh: Jambi / Jakarta">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="satker_penyidik">Kesatuan / Satker<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                @php
                                    $defaultSatker = 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? ''));
                                @endphp
                                <input id="satker_penyidik" type="text"
                                    class="form-control"
                                    name="satker_penyidik"
                                    value="{{ old('satker_penyidik', $defaultSatker) }}" required placeholder="Contoh: Satuan Lalu Lintas Resor Kota Jambi">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── 2. DATA TERSANGKA PERKARA LP ─── --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="card-title fw-bold m-0"><i class="bi bi-people me-1"></i> Data Tersangka Perkara LP<span class="text-danger fs-5">*</span></h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Tersangka Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Quick Picker Tersangka dari Perkara --}}
                        @if (!empty($availableSuspects))
                            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="me-3 mb-2 mb-md-0">
                                    <i class="bi bi-info-circle-fill me-1"></i>
                                    <strong>Pilih dari Tersangka Terdaftar:</strong>
                                    Pilih tersangka perkara di samping untuk menambahkan langsung ke tabel di bawah.
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <select id="selectQuickSuspect" class="form-select form-select-sm" style="min-width: 250px;">
                                        <option value="">-- Pilih Tersangka dari LP --</option>
                                        @foreach ($availableSuspects as $idx => $aw)
                                            <option value="{{ $idx }}">
                                                {{ $aw['nama'] }} ({{ $aw['source_label'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-sm btn-dark-blue text-nowrap" id="btnAddSelectedSuspect">
                                        <i class="bi bi-plus me-1"></i> Tambahkan
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Datalist Autokomplit Pekerjaan --}}
                        <datalist id="listJobs">
                            @foreach($libJobs as $job)
                                <option value="{{ $job }}">
                            @endforeach
                        </datalist>

                        {{-- Datalist Autokomplit Nama Tersangka Perkara --}}
                        <datalist id="listSuspectNames">
                            @foreach($availableSuspects as $aw)
                                <option value="{{ $aw['nama'] }}">{{ $aw['source_label'] }} | {{ $aw['alamat'] }}</option>
                            @endforeach
                        </datalist>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-tersangka m-0" id="tableTersangka">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">NO</th>
                                        <th style="min-width: 180px;">NAMA<span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">NO IDENTITAS<span class="text-danger">*</span></th>
                                        <th style="min-width: 110px;">KEWARGA<br>NEGARAAN<span class="text-danger">*</span></th>
                                        <th style="min-width: 120px;">JENIS<br>KELAMIN<span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">PEKERJAAN<span class="text-danger">*</span></th>
                                        <th style="min-width: 130px;">AGAMA<span class="text-danger">*</span></th>
                                        <th style="min-width: 200px;">ALAMAT<span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">KETERANGAN<span class="text-danger">*</span></th>
                                        <th style="width: 50px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyTersangka">
                                    @php
                                        $rows = old('tersangka', $initialTersangkaList);
                                        if (empty($rows)) {
                                            $rows = [
                                                [
                                                    'nama' => '',
                                                    'no_identitas' => '',
                                                    'kewarganegaraan' => '',
                                                    'jenis_kelamin' => '',
                                                    'pekerjaan' => '',
                                                    'agama' => '',
                                                    'alamat' => '',
                                                    'keterangan' => 'Tersangka Perkara',
                                                    'suspect_id' => null,
                                                    'source' => null,
                                                ]
                                            ];
                                        }
                                    @endphp

                                    @foreach ($rows as $idx => $t)
                                        <tr class="row-tersangka" data-index="{{ $idx }}">
                                            <td class="text-center fw-bold row-num">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="hidden" name="tersangka[{{ $idx }}][suspect_id]" class="input-tersangka-id" value="{{ $t['suspect_id'] ?? ($t['source_id'] ?? '') }}">
                                                <input type="hidden" name="tersangka[{{ $idx }}][source]" class="input-tersangka-source" value="{{ $t['source'] ?? '' }}">
                                                <input type="text" name="tersangka[{{ $idx }}][nama]" class="form-control form-control-sm input-tersangka-nama"
                                                    value="{{ $t['nama'] ?? '' }}" list="listSuspectNames" required placeholder="Nama lengkap tersangka">
                                            </td>
                                            <td>
                                                <input type="text" name="tersangka[{{ $idx }}][no_identitas]" class="form-control form-control-sm input-tersangka-identitas"
                                                    value="{{ $t['no_identitas'] ?? '' }}" required placeholder="NIK / KTP">
                                            </td>
                                            <td>
                                                <input type="text" name="tersangka[{{ $idx }}][kewarganegaraan]" class="form-control form-control-sm input-tersangka-kewarganegaraan"
                                                    value="{{ $t['kewarganegaraan'] ?? '' }}" required placeholder="Contoh: WNI">
                                            </td>
                                            <td>
                                                <select name="tersangka[{{ $idx }}][jenis_kelamin]" class="form-select form-select-sm input-tersangka-gender" required>
                                                    <option value="" {{ empty($t['jenis_kelamin']) ? 'selected' : '' }}>-- Pilih --</option>
                                                    <option value="Laki-laki" {{ ($t['jenis_kelamin'] ?? '') == 'Laki-laki' || ($t['jenis_kelamin'] ?? '') == 'Pria' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Perempuan" {{ ($t['jenis_kelamin'] ?? '') == 'Perempuan' || ($t['jenis_kelamin'] ?? '') == 'Wanita' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="tersangka[{{ $idx }}][pekerjaan]" class="form-control form-control-sm input-tersangka-pekerjaan"
                                                    value="{{ $t['pekerjaan'] ?? '' }}" list="listJobs" required placeholder="Pekerjaan">
                                            </td>
                                            <td>
                                                <select name="tersangka[{{ $idx }}][agama]" class="form-select form-select-sm input-tersangka-agama" required>
                                                    <option value="" {{ empty($t['agama']) ? 'selected' : '' }}>-- Pilih --</option>
                                                    @foreach($libReligions as $rel)
                                                        <option value="{{ $rel }}" {{ strcasecmp($t['agama'] ?? '', $rel) === 0 ? 'selected' : '' }}>{{ $rel }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="tersangka[{{ $idx }}][alamat]" class="form-control form-control-sm input-tersangka-alamat" rows="2"
                                                    required placeholder="Alamat tempat tinggal">{{ $t['alamat'] ?? '' }}</textarea>
                                            </td>
                                            <td>
                                                <input type="text" name="tersangka[{{ $idx }}][keterangan]" class="form-control form-control-sm input-tersangka-keterangan"
                                                    value="{{ $t['keterangan'] ?? 'Tersangka' }}" required placeholder="Contoh: Tersangka Pengemudi">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Hapus baris ini">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ─── 3. PEJABAT PENANDATANGAN ─── --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0"><i class="bi bi-pen me-1"></i> Pejabat Penandatangan (Penyidik / Penyidik Pembantu)<span class="text-danger fs-5">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="select_signatory">Pilih Pejabat<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <select id="select_signatory" class="form-control select2 select2bs4" style="width: 100%;">
                                    <option value="">-- Pilih Pejabat Penandatangan --</option>
                                    @if ($authorizedSignatories->isNotEmpty())
                                        <optgroup label="Pejabat Berwenang (Authorized Signatory)">
                                            @foreach ($authorizedSignatories as $signatory)
                                                @php
                                                    $sName = trim(($signatory->full_name ?? '') ?: ($signatory->first_name . ' ' . ($signatory->last_name ?? '')));
                                                    $sRank = is_object($signatory->rank) ? ($signatory->rank->name ?? '') : (is_string($signatory->rank) ? $signatory->rank : '');
                                                    $sRank = $sRank ?: ($signatory->rank_name ?? ($signatory->rank_short_name ?? ''));
                                                    $sPos = is_object($signatory->position) ? ($signatory->position->name ?? '') : (is_string($signatory->position) ? $signatory->position : '');
                                                    $sPos = $sPos ?: ($signatory->position_name ?? ($signatory->position_short_name ?? 'Penyidik / Penyidik Pembantu'));
                                                    $rawNrp = $signatory->register_number ?? ($signatory->nrp ?? ($signatory->officer->register_number ?? ($signatory->officer->nrp ?? '')));
                                                    if (empty($rawNrp) && is_numeric($signatory->id)) {
                                                        $rawNrp = $signatory->id;
                                                    }
                                                    $cleanNrp = preg_replace('/[^0-9]/', '', (string)$rawNrp) ?: $rawNrp;
                                                @endphp
                                                <option value="auth_{{ $signatory->id }}"
                                                    data-name="{{ $sName }}"
                                                    data-rank="{{ $sRank }}"
                                                    data-nrp="{{ $cleanNrp }}"
                                                    data-position="{{ $sPos }}"
                                                    {{ old('nama_penandatangan') == $sName ? 'selected' : '' }}>
                                                    {{ $sName }} ({{ $sRank ?: '-' }} / NRP: {{ $cleanNrp ?: '-' }}) - {{ $sPos }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if ($officers->isNotEmpty())
                                        <optgroup label="Daftar Penyidik / Penyidik Pembantu (Officers)">
                                            @foreach ($officers as $off)
                                                @php
                                                    $oName = trim(($off->full_name ?? '') ?: ($off->first_name . ' ' . ($off->last_name ?? '')));
                                                    $oRank = is_object($off->rank) ? ($off->rank->name ?? '') : (is_string($off->rank) ? $off->rank : '');
                                                    $oRank = $oRank ?: ($off->rank_name ?? ($off->rank_short_name ?? ''));
                                                    $oPos = is_object($off->position) ? ($off->position->name ?? '') : (is_string($off->position) ? $off->position : '');
                                                    $oPos = $oPos ?: ($off->position_name ?? ($off->position_short_name ?? 'Penyidik / Penyidik Pembantu'));
                                                    $rawNrp = $off->register_number ?? ($off->nrp ?? ($off->officer->register_number ?? ($off->officer->nrp ?? '')));
                                                    if (empty($rawNrp) && is_numeric($off->id)) {
                                                        $rawNrp = $off->id;
                                                    }
                                                    $cleanNrp = preg_replace('/[^0-9]/', '', (string)$rawNrp) ?: $rawNrp;
                                                @endphp
                                                <option value="off_{{ $off->id }}"
                                                    data-name="{{ $oName }}"
                                                    data-rank="{{ $oRank }}"
                                                    data-nrp="{{ $cleanNrp }}"
                                                    data-position="{{ $oPos }}"
                                                    {{ old('nama_penandatangan') == $oName ? 'selected' : '' }}>
                                                    {{ $oName }} ({{ $oRank ?: '-' }} / NRP: {{ $cleanNrp ?: '-' }}) - {{ $oPos }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nama_penandatangan">Nama Penandatangan<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nama_penandatangan" type="text"
                                    class="form-control"
                                    name="nama_penandatangan"
                                    value="{{ old('nama_penandatangan') }}" required placeholder="Nama lengkap beserta gelar">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="pangkat_penandatangan">Pangkat<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="pangkat_penandatangan" type="text"
                                    class="form-control"
                                    name="pangkat_penandatangan"
                                    value="{{ old('pangkat_penandatangan') }}" required placeholder="Contoh: AIPDA / BRIPKA / AKP">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nrp_penandatangan">NRP<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nrp_penandatangan" type="text"
                                    class="form-control"
                                    name="nrp_penandatangan"
                                    value="{{ old('nrp_penandatangan') }}" required placeholder="Contoh: 88120179">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="jabatan_penandatangan">Jabatan<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="jabatan_penandatangan" type="text"
                                    class="form-control"
                                    name="jabatan_penandatangan"
                                    value="{{ old('jabatan_penandatangan', 'Penyidik / Penyidik Pembantu') }}" required placeholder="Penyidik / Penyidik Pembantu">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-dark-blue px-4 py-2 fw-bold" id="btnSubmitForm">
                        <i class="bi bi-save me-1"></i> Simpan Dokumen Daftar Tersangka
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi select2
            $('#select_signatory').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Pejabat Penandatangan --',
                allowClear: true
            });

            var availableSuspects = @json($availableSuspects);
            var libReligions = @json($libReligions);

            // Handler select penandatangan
            function updateSignatoryFields(selectedOpt) {
                if (!selectedOpt || !selectedOpt.val()) {
                    $('#nama_penandatangan, #pangkat_penandatangan, #nrp_penandatangan, #jabatan_penandatangan')
                        .prop('readonly', false)
                        .removeClass('bg-light');
                    return;
                }

                var name = selectedOpt.attr('data-name') || selectedOpt.data('name') || '';
                var rank = selectedOpt.attr('data-rank') || selectedOpt.data('rank') || '';
                var rawNrp = (selectedOpt.attr('data-nrp') || selectedOpt.data('nrp') || '').toString();
                var nrp = rawNrp.replace(/[^0-9]/g, '') || rawNrp;
                var pos = selectedOpt.attr('data-position') || selectedOpt.data('position') || 'Penyidik / Penyidik Pembantu';

                $('#nama_penandatangan').val(name);
                $('#pangkat_penandatangan').val(rank);
                $('#nrp_penandatangan').val(nrp);
                $('#jabatan_penandatangan').val(pos);

                // Kunci input agar tidak bisa diedit secara bebas sesuai relasi
                $('#nama_penandatangan, #pangkat_penandatangan, #nrp_penandatangan, #jabatan_penandatangan')
                    .prop('readonly', true)
                    .addClass('bg-light');
            }

            $('#select_signatory').on('change select2:select', function() {
                var selected = $(this).find('option:selected');
                updateSignatoryFields(selected);
            });

            // Jalankan saat load jika sudah ada pilihan
            if ($('#select_signatory').val()) {
                updateSignatoryFields($('#select_signatory').find('option:selected'));
            }

            // Re-index row numbers
            function renumberRows() {
                $('#tbodyTersangka tr.row-tersangka').each(function(index) {
                    $(this).find('.row-num').text(index + 1);
                });
            }

            var nextIndex = {{ count($rows) }};

            // Helper untuk generate HTML baris tersangka baru
            function createRowHtml(idx, data) {
                data = data || {};
                var nama = data.nama || '';
                var noIdentitas = (data.no_identitas && data.no_identitas !== '-') ? data.no_identitas : '';
                var kewarganegaraan = data.kewarganegaraan || '';
                var gender = data.jenis_kelamin || '';
                var pekerjaan = (data.pekerjaan && data.pekerjaan !== '-') ? data.pekerjaan : '';
                var agama = (data.agama && data.agama !== '-') ? data.agama : '';
                var alamat = (data.alamat && data.alamat !== '-') ? data.alamat : '';
                var keterangan = (data.keterangan && data.keterangan !== '-') ? data.keterangan : 'Tersangka';
                var suspectId = data.source_id || data.suspect_id || '';
                var source = data.source || '';

                var religionOptions = `<option value="" ${!agama ? 'selected' : ''}>-- Pilih --</option>`;
                libReligions.forEach(function(r) {
                    var sel = (r.toLowerCase() === agama.toLowerCase()) ? 'selected' : '';
                    religionOptions += `<option value="${r}" ${sel}>${r}</option>`;
                });

                return `
                    <tr class="row-tersangka" data-index="${idx}">
                        <td class="text-center fw-bold row-num">1</td>
                        <td>
                            <input type="hidden" name="tersangka[${idx}][suspect_id]" class="input-tersangka-id" value="${suspectId}">
                            <input type="hidden" name="tersangka[${idx}][source]" class="input-tersangka-source" value="${source}">
                            <input type="text" name="tersangka[${idx}][nama]" class="form-control form-control-sm input-tersangka-nama"
                                value="${nama}" list="listSuspectNames" required placeholder="Nama lengkap tersangka">
                        </td>
                        <td>
                            <input type="text" name="tersangka[${idx}][no_identitas]" class="form-control form-control-sm input-tersangka-identitas"
                                value="${noIdentitas}" required placeholder="NIK / KTP">
                        </td>
                        <td>
                            <input type="text" name="tersangka[${idx}][kewarganegaraan]" class="form-control form-control-sm input-tersangka-kewarganegaraan"
                                value="${kewarganegaraan}" required placeholder="Contoh: WNI">
                        </td>
                        <td>
                            <select name="tersangka[${idx}][jenis_kelamin]" class="form-select form-select-sm input-tersangka-gender" required>
                                <option value="" ${!gender ? 'selected' : ''}>-- Pilih --</option>
                                <option value="Laki-laki" ${gender === 'Laki-laki' || gender === 'Pria' ? 'selected' : ''}>Laki-laki</option>
                                <option value="Perempuan" ${gender === 'Perempuan' || gender === 'Wanita' ? 'selected' : ''}>Perempuan</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="tersangka[${idx}][pekerjaan]" class="form-control form-control-sm input-tersangka-pekerjaan"
                                value="${pekerjaan}" list="listJobs" required placeholder="Pekerjaan">
                        </td>
                        <td>
                            <select name="tersangka[${idx}][agama]" class="form-select form-select-sm input-tersangka-agama" required>
                                ${religionOptions}
                            </select>
                        </td>
                        <td>
                            <textarea name="tersangka[${idx}][alamat]" class="form-control form-control-sm input-tersangka-alamat" rows="2"
                                required placeholder="Alamat tempat tinggal">${alamat}</textarea>
                        </td>
                        <td>
                            <input type="text" name="tersangka[${idx}][keterangan]" class="form-control form-control-sm input-tersangka-keterangan"
                                value="${keterangan}" required placeholder="Contoh: Tersangka Pengemudi">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Hapus baris ini">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }

            // Tambah baris kosong manual
            $('#btnAddRow').on('click', function() {
                var idx = nextIndex++;
                $('#tbodyTersangka').append(createRowHtml(idx));
                renumberRows();
            });

            // Tambah tersangka terpilih dari dropdown Quick Picker
            $('#btnAddSelectedSuspect').on('click', function() {
                var selectedIdx = $('#selectQuickSuspect').val();
                if (selectedIdx === '' || selectedIdx === null) {
                    alert('Silakan pilih tersangka dari daftar terlebih dahulu.');
                    return;
                }

                var sData = availableSuspects[selectedIdx];
                if (!sData) return;

                // Cek apakah tersangka sudah ada di tabel
                var alreadyInTable = false;
                $('#tbodyTersangka tr.row-tersangka').each(function() {
                    var curName = $(this).find('.input-tersangka-nama').val().trim().toLowerCase();
                    if (curName === sData.nama.trim().toLowerCase()) {
                        alreadyInTable = true;
                    }
                });

                if (alreadyInTable && !confirm('Tersangka "' + sData.nama + '" sudah ada di tabel. Tetap tambahkan?')) {
                    return;
                }

                // Jika baris pertama masih kosong / placeholder, ganti saja baris pertama tersebut
                var firstRow = $('#tbodyTersangka tr.row-tersangka').first();
                if ($('#tbodyTersangka tr.row-tersangka').length === 1 && firstRow.find('.input-tersangka-nama').val().trim() === '') {
                    firstRow.remove();
                }

                var idx = nextIndex++;
                $('#tbodyTersangka').append(createRowHtml(idx, sData));
                renumberRows();
            });

            // Auto-fill saat nama tersangka dipilih atau diketik sama dengan tersangka LP
            $(document).on('change input', '.input-tersangka-nama', function() {
                var typedName = $(this).val().trim().toLowerCase();
                if (!typedName) return;

                var matched = availableSuspects.find(function(s) {
                    return s.nama.trim().toLowerCase() === typedName;
                });

                if (matched) {
                    var tr = $(this).closest('tr');
                    tr.find('.input-tersangka-id').val(matched.source_id || matched.suspect_id || '');
                    tr.find('.input-tersangka-source').val(matched.source || '');
                    if (matched.no_identitas && matched.no_identitas !== '-') {
                        tr.find('.input-tersangka-identitas').val(matched.no_identitas);
                    }
                    if (matched.kewarganegaraan) {
                        tr.find('.input-tersangka-kewarganegaraan').val(matched.kewarganegaraan);
                    }
                    if (matched.jenis_kelamin) {
                        tr.find('.input-tersangka-gender').val(matched.jenis_kelamin);
                    }
                    if (matched.pekerjaan && matched.pekerjaan !== '-') {
                        tr.find('.input-tersangka-pekerjaan').val(matched.pekerjaan);
                    }
                    if (matched.agama && matched.agama !== '-') {
                        tr.find('.input-tersangka-agama').val(matched.agama);
                    }
                    if (matched.alamat && matched.alamat !== '-') {
                        tr.find('.input-tersangka-alamat').val(matched.alamat);
                    }
                    if (matched.keterangan && matched.keterangan !== '-') {
                        tr.find('.input-tersangka-keterangan').val(matched.keterangan);
                    }
                }
            });

            // Remove suspect row
            $(document).on('click', '.btn-remove-row', function() {
                if ($('#tbodyTersangka tr.row-tersangka').length <= 1) {
                    alert('Minimal harus ada 1 baris tersangka pada dokumen ini.');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
            });

            // Validasi submit form
            $('#formDaftarTersangka').on('submit', function(e) {
                if ($('#tbodyTersangka tr.row-tersangka').length === 0) {
                    e.preventDefault();
                    alert('Data Tersangka wajib diisi minimal 1 data tersangka.');
                    return false;
                }
            });
        });
    </script>
@endpush
