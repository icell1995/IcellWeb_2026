@php
    $_title = 'Tambah Daftar Ahli (S-38.2)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .table-ahli th {
            background-color: #000080;
            color: #ffffff;
            font-size: 13px;
            vertical-align: middle;
            text-align: center;
        }
        .table-ahli td {
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
            <h5 class="fw-bold text-blue-dark">Tambah Daftar Ahli (S-38.2)</h5>

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
            <form action="{{ route('doc.daftar-ahli-document.store') }}" method="POST" id="formDaftarAhli">
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
                                    value="{{ old('nomor_surat') }}" required placeholder="Contoh: S-38.2/01/X/2026/Lantas">
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

                {{-- ─── 2. TABEL 9 KOLOM DAFTAR AHLI (SPESIMEN S-38.2) ─── --}}
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title fw-bold m-0"><i class="bi bi-person-badge me-1"></i> Data Ahli<span class="text-danger fs-5">*</span></h6>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Ahli
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Datalist untuk Autocomplete Keahlian --}}
                        <datalist id="listExpertJobs">
                            @foreach($expertJobs as $job)
                                <option value="{{ $job }}"></option>
                            @endforeach
                        </datalist>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-ahli m-0" id="tableAhli">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">NO</th>
                                        <th style="min-width: 180px;">NAMA<span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">NO IDENTITAS<span class="text-danger">*</span></th>
                                        <th style="min-width: 110px;">KEWARGA<br>NEGARAAN<span class="text-danger">*</span></th>
                                        <th style="min-width: 120px;">JENIS<br>KELAMIN<span class="text-danger">*</span></th>
                                        <th style="min-width: 160px;">PEKERJAAN<span class="text-danger">*</span></th>
                                        <th style="min-width: 130px;">AGAMA<span class="text-danger">*</span></th>
                                        <th style="min-width: 200px;">ALAMAT<span class="text-danger">*</span></th>
                                        <th style="min-width: 160px;">KETERANGAN<span class="text-danger">*</span></th>
                                        <th style="width: 50px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyAhli">
                                    @php
                                        $rows = old('ahli', []);
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
                                                    'keterangan' => 'Saksi Ahli',
                                                ]
                                            ];
                                        }
                                    @endphp

                                    @foreach ($rows as $idx => $a)
                                        <tr class="row-ahli" data-index="{{ $idx }}">
                                             <td class="text-center fw-bold row-num">{{ $loop->iteration }}</td>
                                             <td>
                                                 <input type="text" name="ahli[{{ $idx }}][nama]" class="form-control form-control-sm input-ahli-nama"
                                                     value="{{ $a['nama'] ?? '' }}" required placeholder="Nama lengkap & gelar ahli">
                                             </td>
                                             <td>
                                                 <input type="text" name="ahli[{{ $idx }}][no_identitas]" class="form-control form-control-sm input-ahli-identitas"
                                                     value="{{ $a['no_identitas'] ?? '' }}" required placeholder="NIK / NIP / SIP Dokter">
                                             </td>
                                             <td>
                                                 <input type="text" name="ahli[{{ $idx }}][kewarganegaraan]" class="form-control form-control-sm input-ahli-kewarganegaraan"
                                                     value="{{ $a['kewarganegaraan'] ?? '' }}" required placeholder="Contoh: WNI">
                                             </td>
                                             <td>
                                                 <select name="ahli[{{ $idx }}][jenis_kelamin]" class="form-select form-select-sm input-ahli-gender" required>
                                                     <option value="" {{ empty($a['jenis_kelamin']) ? 'selected' : '' }}>-- Pilih --</option>
                                                     <option value="Laki-laki" {{ ($a['jenis_kelamin'] ?? '') == 'Laki-laki' || ($a['jenis_kelamin'] ?? '') == 'Pria' ? 'selected' : '' }}>Laki-laki</option>
                                                     <option value="Perempuan" {{ ($a['jenis_kelamin'] ?? '') == 'Perempuan' || ($a['jenis_kelamin'] ?? '') == 'Wanita' ? 'selected' : '' }}>Perempuan</option>
                                                 </select>
                                             </td>
                                             <td>
                                                 <input type="text" name="ahli[{{ $idx }}][pekerjaan]" class="form-control form-control-sm input-ahli-pekerjaan"
                                                     value="{{ $a['pekerjaan'] ?? '' }}" list="listExpertJobs" required placeholder="Bidang Keahlian / Pekerjaan">
                                             </td>
                                             <td>
                                                 <select name="ahli[{{ $idx }}][agama]" class="form-select form-select-sm input-ahli-agama" required>
                                                     <option value="" {{ empty($a['agama']) ? 'selected' : '' }}>-- Pilih --</option>
                                                     @foreach($libReligions as $rel)
                                                         <option value="{{ $rel }}" {{ strcasecmp($a['agama'] ?? '', $rel) === 0 ? 'selected' : '' }}>{{ $rel }}</option>
                                                     @endforeach
                                                 </select>
                                             </td>
                                             <td>
                                                 <textarea name="ahli[{{ $idx }}][alamat]" class="form-control form-control-sm input-ahli-alamat" rows="2"
                                                     required placeholder="Instansi / RS / Alamat domisili">{{ $a['alamat'] ?? '' }}</textarea>
                                             </td>
                                             <td>
                                                 <input type="text" name="ahli[{{ $idx }}][keterangan]" class="form-control form-control-sm input-ahli-keterangan"
                                                     value="{{ $a['keterangan'] ?? 'Saksi Ahli' }}" required placeholder="Contoh: Ahli Kedokteran Kehakiman / Ahli TAA">
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
            var availableExperts = {!! json_encode($availableExperts ?? []) !!};
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

                $('#nama_penandatangan, #pangkat_penandatangan, #nrp_penandatangan, #jabatan_penandatangan')
                    .prop('readonly', true)
                    .addClass('bg-light');
            }

            $('#select_signatory').on('change select2:select', function() {
                var selected = $(this).find('option:selected');
                updateSignatoryFields(selected);
            });

            if ($('#select_signatory').val()) {
                updateSignatoryFields($('#select_signatory').find('option:selected'));
            }

            // Re-index row numbers
            function renumberRows() {
                $('#tbodyAhli tr.row-ahli').each(function(index) {
                    $(this).find('.row-num').text(index + 1);
                });
            }

            var nextIndex = {{ count($rows) }};

            // Helper untuk generate HTML baris ahli baru
            function createRowHtml(idx, data) {
                data = data || {};
                var nama = data.nama || '';
                var noIdentitas = (data.no_identitas && data.no_identitas !== '-') ? data.no_identitas : '';
                var kewarganegaraan = data.kewarganegaraan || '';
                var gender = data.jenis_kelamin || '';
                var pekerjaan = (data.pekerjaan && data.pekerjaan !== '-') ? data.pekerjaan : '';
                var agama = (data.agama && data.agama !== '-') ? data.agama : '';
                var alamat = (data.alamat && data.alamat !== '-') ? data.alamat : '';
                var keterangan = (data.keterangan && data.keterangan !== '-') ? data.keterangan : 'Saksi Ahli';

                var religionOptions = `<option value="" ${!agama ? 'selected' : ''}>-- Pilih --</option>`;
                libReligions.forEach(function(r) {
                    var sel = (r.toLowerCase() === agama.toLowerCase()) ? 'selected' : '';
                    religionOptions += `<option value="${r}" ${sel}>${r}</option>`;
                });

                return `
                    <tr class="row-ahli" data-index="${idx}">
                        <td class="text-center fw-bold row-num">1</td>
                        <td>
                            <input type="text" name="ahli[${idx}][nama]" class="form-control form-control-sm input-ahli-nama"
                                value="${nama}" required placeholder="Nama lengkap & gelar ahli">
                        </td>
                        <td>
                            <input type="text" name="ahli[${idx}][no_identitas]" class="form-control form-control-sm input-ahli-identitas"
                                value="${noIdentitas}" required placeholder="NIK / NIP / SIP Dokter">
                        </td>
                        <td>
                            <input type="text" name="ahli[${idx}][kewarganegaraan]" class="form-control form-control-sm input-ahli-kewarganegaraan"
                                value="${kewarganegaraan}" required placeholder="Contoh: WNI">
                        </td>
                        <td>
                            <select name="ahli[${idx}][jenis_kelamin]" class="form-select form-select-sm input-ahli-gender" required>
                                <option value="" ${!gender ? 'selected' : ''}>-- Pilih --</option>
                                <option value="Laki-laki" ${gender === 'Laki-laki' || gender === 'Pria' ? 'selected' : ''}>Laki-laki</option>
                                <option value="Perempuan" ${gender === 'Perempuan' || gender === 'Wanita' ? 'selected' : ''}>Perempuan</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="ahli[${idx}][pekerjaan]" class="form-control form-control-sm input-ahli-pekerjaan"
                                value="${pekerjaan}" list="listExpertJobs" required placeholder="Bidang Keahlian / Pekerjaan">
                        </td>
                        <td>
                            <select name="ahli[${idx}][agama]" class="form-select form-select-sm input-ahli-agama" required>
                                ${religionOptions}
                            </select>
                        </td>
                        <td>
                            <textarea name="ahli[${idx}][alamat]" class="form-control form-control-sm input-ahli-alamat" rows="2"
                                required placeholder="Instansi / RS / Alamat domisili">${alamat}</textarea>
                        </td>
                        <td>
                            <input type="text" name="ahli[${idx}][keterangan]" class="form-control form-control-sm input-ahli-keterangan"
                                value="${keterangan}" required placeholder="Contoh: Ahli Kedokteran Kehakiman / Ahli TAA">
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
                $('#tbodyAhli').append(createRowHtml(idx));
                renumberRows();
            });

            // Remove row
            $(document).on('click', '.btn-remove-row', function() {
                if ($('#tbodyAhli tr.row-ahli').length <= 1) {
                    alert('Minimal harus ada 1 baris ahli pada dokumen ini.');
                    return;
                }
                $(this).closest('tr').remove();
                renumberRows();
            });

            // Validasi submit form
            $('#formDaftarAhli').on('submit', function(e) {
                if ($('#tbodyAhli tr.row-ahli').length === 0) {
                    e.preventDefault();
                    alert('Data Ahli wajib diisi minimal 1 data ahli.');
                    return false;
                }
            });
        });
    </script>
@endpush
