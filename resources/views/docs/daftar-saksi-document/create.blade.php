@php
    $_title = 'Daftar Saksi (S-38.1)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .table-saksi th {
            background-color: #000080;
            color: #ffffff;
            font-size: 13px;
            vertical-align: middle;
            text-align: center;
        }
        .table-saksi td {
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
            <h5 class="fw-bold text-blue-dark">Tambah Daftar Saksi (S-38.1)</h5>

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
            <form action="{{ route('doc.daftar-saksi-document.store') }}" method="POST" id="formDaftarSaksi">
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
                            <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nomor_surat" type="text" class="form-control" name="nomor_surat"
                                    value="{{ old('nomor_surat', 'S-38.1/01/' . date('m') . '/RES.0.0/' . date('Y')) }}" required placeholder="Contoh: S-38.1/01/X/2026/Lantas">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat">Tanggal Dokumen<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-4 col-md-6 col-sm-12 col-12">
                                <input id="tanggal_surat" type="date" class="form-control" name="tanggal_surat"
                                    value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="dikeluarkan_di">Dikeluarkan di<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="dikeluarkan_di" type="text" class="form-control" name="dikeluarkan_di"
                                    value="{{ old('dikeluarkan_di', $defaultLocation) }}" required placeholder="Contoh: Simalungun">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="satker_penyidik">Satuan Kerja Penyidik<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="satker_penyidik" type="text" class="form-control" name="satker_penyidik"
                                    value="{{ old('satker_penyidik', $defaultSatker) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── 2. TABEL 9 KOLOM DAFTAR SAKSI (SPESIMEN S-38.1) ─── --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title fw-bold m-0"><i class="bi bi-people me-1"></i> Data Saksi Perkara<span class="text-danger fs-5">*</span></h6>
                            <small class="text-muted d-block mt-1">Data otomatis terhubung dengan saksi pada LP perkara ini.</small>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            @if(!empty($availableWitnesses))
                                <div class="input-group input-group-sm" style="width: auto;">
                                    <select class="form-select form-select-sm" id="selectQuickWitness" style="max-width: 280px;">
                                        <option value="">-- Pilih Saksi dari Data LP --</option>
                                        @foreach($availableWitnesses as $idxW => $aw)
                                            <option value="{{ $idxW }}">
                                                {{ $aw['nama'] }} ({{ $aw['no_identitas'] != '-' ? $aw['no_identitas'] : 'Tanpa NIK' }}) - {{ $aw['source_label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-sm btn-info text-white" id="btnAddSelectedWitness" title="Tambahkan saksi terpilih ke tabel">
                                        <i class="bi bi-person-plus-fill me-1"></i> Tambah ke Tabel
                                    </button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnLoadAllWitnesses" title="Tarik seluruh saksi terdaftar pada LP ini">
                                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Muat Semua Saksi LP
                                </button>
                            @endif
                            <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Manual
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Datalist untuk Autocomplete Pekerjaan & Saksi LP --}}
                        <datalist id="listJobs">
                            @foreach($libJobs as $job)
                                <option value="{{ $job }}"></option>
                            @endforeach
                        </datalist>
                        <datalist id="listWitnessNames">
                            @foreach($availableWitnesses as $aw)
                                <option value="{{ $aw['nama'] }}">{{ $aw['source_label'] }} | {{ $aw['alamat'] }}</option>
                            @endforeach
                        </datalist>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-saksi m-0" id="tableSaksi">
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
                                <tbody id="tbodySaksi">
                                    @php
                                        $rows = old('saksi', $initialSaksiList);
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
                                                    'keterangan' => 'Saksi di TKP',
                                                    'witness_id' => null,
                                                    'source' => null,
                                                ]
                                            ];
                                        }
                                    @endphp

                                    @foreach ($rows as $idx => $s)
                                        <tr class="row-saksi" data-index="{{ $idx }}">
                                            <td class="text-center fw-bold row-num">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="hidden" name="saksi[{{ $idx }}][witness_id]" class="input-saksi-witness-id" value="{{ $s['witness_id'] ?? ($s['source_id'] ?? '') }}">
                                                <input type="hidden" name="saksi[{{ $idx }}][source]" class="input-saksi-source" value="{{ $s['source'] ?? '' }}">
                                                <input type="text" name="saksi[{{ $idx }}][nama]" class="form-control form-control-sm input-saksi-nama"
                                                    value="{{ $s['nama'] ?? '' }}" list="listWitnessNames" required placeholder="Nama lengkap saksi">
                                            </td>
                                            <td>
                                                <input type="text" name="saksi[{{ $idx }}][no_identitas]" class="form-control form-control-sm input-saksi-identitas"
                                                    value="{{ $s['no_identitas'] ?? '' }}" required placeholder="NIK / KTP">
                                            </td>
                                            <td>
                                                <input type="text" name="saksi[{{ $idx }}][kewarganegaraan]" class="form-control form-control-sm input-saksi-kewarganegaraan"
                                                    value="{{ $s['kewarganegaraan'] ?? '' }}" required placeholder="Contoh: WNI">
                                            </td>
                                            <td>
                                                <select name="saksi[{{ $idx }}][jenis_kelamin]" class="form-select form-select-sm input-saksi-gender" required>
                                                    <option value="" {{ empty($s['jenis_kelamin']) ? 'selected' : '' }}>-- Pilih --</option>
                                                    <option value="Laki-laki" {{ ($s['jenis_kelamin'] ?? '') == 'Laki-laki' || ($s['jenis_kelamin'] ?? '') == 'Pria' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Perempuan" {{ ($s['jenis_kelamin'] ?? '') == 'Perempuan' || ($s['jenis_kelamin'] ?? '') == 'Wanita' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="saksi[{{ $idx }}][pekerjaan]" class="form-control form-control-sm input-saksi-pekerjaan"
                                                    value="{{ $s['pekerjaan'] ?? '' }}" list="listJobs" required placeholder="Pekerjaan">
                                            </td>
                                            <td>
                                                <select name="saksi[{{ $idx }}][agama]" class="form-select form-select-sm input-saksi-agama" required>
                                                    <option value="" {{ empty($s['agama']) ? 'selected' : '' }}>-- Pilih --</option>
                                                    @foreach($libReligions as $rel)
                                                        <option value="{{ $rel }}" {{ strcasecmp($s['agama'] ?? '', $rel) === 0 ? 'selected' : '' }}>{{ $rel }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="saksi[{{ $idx }}][alamat]" class="form-control form-control-sm input-saksi-alamat" rows="2"
                                                    required placeholder="Alamat tempat tinggal">{{ $s['alamat'] ?? '' }}</textarea>
                                            </td>
                                            <td>
                                                <input type="text" name="saksi[{{ $idx }}][keterangan]" class="form-control form-control-sm input-saksi-keterangan"
                                                    value="{{ $s['keterangan'] ?? 'Saksi' }}" required placeholder="Contoh: Saksi TKP / Korban">
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
                        <h6 class="card-title fw-bold m-0"><i class="bi bi-pen me-1"></i> Pejabat Penandatangan (Penyidik / Penyidik Pembantu)</h6>
                    </div>
                    <div class="card-body">

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="select_signatory">Pilih Pejabat<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <select class="form-control select2" id="select_signatory" required>
                                    <option value="">-- Pilih Pejabat Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $sig)
                                        @php
                                            $sName = $sig->full_name ?: trim(($sig->first_name ?? '') . ' ' . ($sig->last_name ?? ''));
                                            $sRank = $sig->rank->name ?? ($sig->rank_short_name ?? ($sig->rank_name ?? ''));
                                            $rawNrp = $sig->register_number ?? ($sig->nrp ?? ($sig->id ?? ''));
                                            $sNrp = preg_replace('/[^0-9]/', '', (string)$rawNrp) ?: $rawNrp;
                                            $sPos = $sig->position->name ?? ($sig->position_short_name ?? ($sig->position ?? 'Penyidik Pembantu'));
                                        @endphp
                                        <option value="{{ $sig->id }}"
                                            data-name="{{ $sName }}"
                                            data-rank="{{ $sRank }}"
                                            data-nrp="{{ $sNrp }}"
                                            data-position="{{ $sPos }}">
                                            {{ $sName }} ({{ $sRank ?: '-' }} NRP {{ $sNrp ?: '-' }})
                                        </option>
                                    @endforeach
                                    @foreach ($officers as $off)
                                        @php
                                            $oName = $off->full_name ?: trim(($off->first_name ?? '') . ' ' . ($off->last_name ?? ''));
                                            $oRank = $off->rank->name ?? ($off->rank_short_name ?? ($off->rank_name ?? ''));
                                            $rawNrp = $off->register_number ?? ($off->nrp ?? ($off->id ?? ''));
                                            $oNrp = preg_replace('/[^0-9]/', '', (string)$rawNrp) ?: $rawNrp;
                                            $oPos = $off->position->name ?? ($off->position_short_name ?? 'Penyidik / Penyidik Pembantu');
                                        @endphp
                                        <option value="{{ $off->id }}"
                                            data-name="{{ $oName }}"
                                            data-rank="{{ $oRank }}"
                                            data-nrp="{{ $oNrp }}"
                                            data-position="{{ $oPos }}">
                                            {{ $oName }} ({{ $oRank ?: '-' }} NRP {{ $oNrp ?: '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nama_penandatangan">Nama Penandatangan<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nama_penandatangan" type="text" class="form-control bg-light" name="nama_penandatangan"
                                    value="{{ old('nama_penandatangan') }}" required readonly placeholder="Otomatis dari Pilih Pejabat">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="pangkat_penandatangan">Pangkat<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="pangkat_penandatangan" type="text" class="form-control bg-light" name="pangkat_penandatangan"
                                    value="{{ old('pangkat_penandatangan') }}" required readonly placeholder="Otomatis dari Pilih Pejabat">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="nrp_penandatangan">NRP<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="nrp_penandatangan" type="text" class="form-control bg-light" name="nrp_penandatangan"
                                    value="{{ old('nrp_penandatangan') ? preg_replace('/[^0-9]/', '', (string)old('nrp_penandatangan')) : '' }}" required readonly placeholder="Otomatis dari Pilih Pejabat">
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-2 col-form-label" for="jabatan_penandatangan">Jabatan<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                                <input id="jabatan_penandatangan" type="text" class="form-control bg-light" name="jabatan_penandatangan"
                                    value="{{ old('jabatan_penandatangan', 'Penyidik / Penyidik Pembantu') }}" required readonly placeholder="Otomatis dari Pilih Pejabat">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="text-end mb-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-dark-blue px-4 py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Dokumen
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
            var availableWitnesses = {!! json_encode($availableWitnesses ?? []) !!};
            var libReligions = {!! json_encode($libReligions ?? []) !!};

            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Pejabat Penandatangan --"
            });

            // Relasi Pejabat Penandatangan: Auto-fill dan lock input readonly
            function updateSignatoryFields(selectedOpt) {
                if (!selectedOpt || !selectedOpt.val()) {
                    return;
                }
                var name = selectedOpt.data('name') || '';
                var rank = selectedOpt.data('rank') || '';
                var rawNrp = (selectedOpt.data('nrp') || '').toString();
                var nrp = rawNrp.replace(/[^0-9]/g, '') || rawNrp;
                var pos = selectedOpt.data('position') || 'Penyidik / Penyidik Pembantu';

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
                $('#tbodySaksi tr.row-saksi').each(function(index) {
                    $(this).find('.row-num').text(index + 1);
                });
            }

            var nextIndex = {{ count($rows) }};

            // Helper untuk generate HTML baris saksi baru
            function createRowHtml(idx, data) {
                data = data || {};
                var nama = data.nama || '';
                var noIdentitas = (data.no_identitas && data.no_identitas !== '-') ? data.no_identitas : '';
                var kewarganegaraan = data.kewarganegaraan || '';
                var gender = data.jenis_kelamin || '';
                var pekerjaan = (data.pekerjaan && data.pekerjaan !== '-') ? data.pekerjaan : '';
                var agama = (data.agama && data.agama !== '-') ? data.agama : '';
                var alamat = (data.alamat && data.alamat !== '-') ? data.alamat : '';
                var keterangan = (data.keterangan && data.keterangan !== '-') ? data.keterangan : 'Saksi';
                var witnessId = data.source_id || data.witness_id || '';
                var source = data.source || '';

                var religionOptions = `<option value="" ${!agama ? 'selected' : ''}>-- Pilih --</option>`;
                libReligions.forEach(function(r) {
                    var sel = (r.toLowerCase() === agama.toLowerCase()) ? 'selected' : '';
                    religionOptions += `<option value="${r}" ${sel}>${r}</option>`;
                });

                return `
                    <tr class="row-saksi" data-index="${idx}">
                        <td class="text-center fw-bold row-num">1</td>
                        <td>
                            <input type="hidden" name="saksi[${idx}][witness_id]" class="input-saksi-witness-id" value="${witnessId}">
                            <input type="hidden" name="saksi[${idx}][source]" class="input-saksi-source" value="${source}">
                            <input type="text" name="saksi[${idx}][nama]" class="form-control form-control-sm input-saksi-nama"
                                value="${nama}" list="listWitnessNames" required placeholder="Nama lengkap saksi">
                        </td>
                        <td>
                            <input type="text" name="saksi[${idx}][no_identitas]" class="form-control form-control-sm input-saksi-identitas"
                                value="${noIdentitas}" required placeholder="NIK / KTP">
                        </td>
                        <td>
                            <input type="text" name="saksi[${idx}][kewarganegaraan]" class="form-control form-control-sm input-saksi-kewarganegaraan"
                                value="${kewarganegaraan}" required placeholder="Contoh: WNI">
                        </td>
                        <td>
                            <select name="saksi[${idx}][jenis_kelamin]" class="form-select form-select-sm input-saksi-gender" required>
                                <option value="" ${!gender ? 'selected' : ''}>-- Pilih --</option>
                                <option value="Laki-laki" ${gender === 'Laki-laki' || gender === 'Pria' ? 'selected' : ''}>Laki-laki</option>
                                <option value="Perempuan" ${gender === 'Perempuan' || gender === 'Wanita' ? 'selected' : ''}>Perempuan</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="saksi[${idx}][pekerjaan]" class="form-control form-control-sm input-saksi-pekerjaan"
                                value="${pekerjaan}" list="listJobs" required placeholder="Pekerjaan">
                        </td>
                        <td>
                            <select name="saksi[${idx}][agama]" class="form-select form-select-sm input-saksi-agama" required>
                                ${religionOptions}
                            </select>
                        </td>
                        <td>
                            <textarea name="saksi[${idx}][alamat]" class="form-control form-control-sm input-saksi-alamat" rows="2"
                                required placeholder="Alamat tempat tinggal">${alamat}</textarea>
                        </td>
                        <td>
                            <input type="text" name="saksi[${idx}][keterangan]" class="form-control form-control-sm input-saksi-keterangan"
                                value="${keterangan}" required placeholder="Contoh: Saksi TKP / Korban">
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
                $('#tbodySaksi').append(createRowHtml(idx));
                renumberRows();
            });

            // Tambah saksi terpilih dari dropdown Quick Picker
            $('#btnAddSelectedWitness').on('click', function() {
                var selectedIdx = $('#selectQuickWitness').val();
                if (selectedIdx === '' || selectedIdx === null) {
                    alert('Silakan pilih saksi dari daftar terlebih dahulu.');
                    return;
                }

                var wData = availableWitnesses[selectedIdx];
                if (!wData) return;

                // Cek apakah saksi sudah ada di tabel
                var alreadyInTable = false;
                $('#tbodySaksi tr.row-saksi').each(function() {
                    var curName = $(this).find('.input-saksi-nama').val().trim().toLowerCase();
                    if (curName === wData.nama.trim().toLowerCase()) {
                        alreadyInTable = true;
                    }
                });

                if (alreadyInTable && !confirm('Saksi "' + wData.nama + '" sudah ada di tabel. Tetap tambahkan?')) {
                    return;
                }

                // Jika baris pertama masih kosong / placeholder, ganti saja baris pertama tersebut
                var firstRow = $('#tbodySaksi tr.row-saksi').first();
                if ($('#tbodySaksi tr.row-saksi').length === 1 && firstRow.find('.input-saksi-nama').val().trim() === '') {
                    firstRow.remove();
                }

                var idx = nextIndex++;
                $('#tbodySaksi').append(createRowHtml(idx, wData));
                renumberRows();

                // Reset dropdown
                $('#selectQuickWitness').val('');
            });

            // Muat seluruh saksi yang terdaftar pada LP ini sekaligus
            $('#btnLoadAllWitnesses').on('click', function() {
                if (!availableWitnesses || availableWitnesses.length === 0) {
                    alert('Tidak ada data saksi terdaftar pada LP ini.');
                    return;
                }

                if (!confirm('Apakah Anda ingin memuat seluruh ' + availableWitnesses.length + ' saksi yang terdaftar pada LP ini ke tabel?')) {
                    return;
                }

                $('#tbodySaksi').empty();
                availableWitnesses.forEach(function(wData) {
                    var idx = nextIndex++;
                    $('#tbodySaksi').append(createRowHtml(idx, wData));
                });
                renumberRows();
            });

            // Auto-fill saat nama saksi dipilih atau diketik sama dengan saksi LP
            $(document).on('change input', '.input-saksi-nama', function() {
                var typedName = $(this).val().trim().toLowerCase();
                if (!typedName) return;

                var matched = availableWitnesses.find(function(w) {
                    return w.nama.trim().toLowerCase() === typedName;
                });

                if (matched) {
                    var tr = $(this).closest('tr');
                    tr.find('.input-saksi-witness-id').val(matched.source_id || matched.witness_id || '');
                    tr.find('.input-saksi-source').val(matched.source || '');
                    if (matched.no_identitas && matched.no_identitas !== '-') {
                        tr.find('.input-saksi-identitas').val(matched.no_identitas);
                    }
                    if (matched.kewarganegaraan) {
                        tr.find('.input-saksi-kewarganegaraan').val(matched.kewarganegaraan);
                    }
                    if (matched.jenis_kelamin) {
                        tr.find('.input-saksi-gender').val(matched.jenis_kelamin);
                    }
                    if (matched.pekerjaan && matched.pekerjaan !== '-') {
                        tr.find('.input-saksi-pekerjaan').val(matched.pekerjaan);
                    }
                    if (matched.agama && matched.agama !== '-') {
                        tr.find('.input-saksi-agama').val(matched.agama);
                    }
                    if (matched.alamat && matched.alamat !== '-') {
                        tr.find('.input-saksi-alamat').val(matched.alamat);
                    }
                    if (matched.keterangan && matched.keterangan !== '-') {
                        tr.find('.input-saksi-keterangan').val(matched.keterangan);
                    }
                }
            });

            // Remove witness row
            $(document).on('click', '.btn-remove-row', function() {
                if ($('#tbodySaksi tr.row-saksi').length <= 1) {
                    alert('Minimal harus ada 1 baris saksi pada dokumen ini.');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
            });

            // Validasi submit form
            $('#formDaftarSaksi').on('submit', function(e) {
                if ($('#tbodySaksi tr.row-saksi').length === 0) {
                    e.preventDefault();
                    alert('Data Saksi wajib diisi minimal 1 data saksi.');
                    return false;
                }
            });
        });
    </script>
@endpush
