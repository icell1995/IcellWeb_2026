@php
    $_title = 'Surat Perintah Penahanan Lanjutan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        /* Seperti pola SPH: Select2 di input-group mengisi sisa lebar agar baris tidak turun saat buka/tutup dropdown */
        #s22_internalOfficer .input-group > .select2-container {
            flex: 1 1 auto;
            min-width: 0;
            width: 1% !important;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah {{ $_title }}</h5>

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

            @if (session('error'))
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form method="POST" action="{{ route('doc.perpanjangan-lanjutan-document.store', ['accident_id' => $accidentId, 'document_category_id' => '0604']) }}">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="accidentNumber" type="text" class="form-control font-weight-bold" readonly
                            value="{{ $accident->no_lp ?? '-' }}">
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input id="documentNumber" type="text" name="documentNumber"
                            class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                            value="{{ old('documentNumber') }}" required>
                        @error('documentNumber')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input class="form-control s22-date @error('documentDate') is-invalid @enderror" id="documentDate"
                            name="documentDate" placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('documentDate') }}" data-provide="datepicker" required readonly>
                        @error('documentDate')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratPerintahPenyidikanDocument">Surat Perintah Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $ref = $defaults['references'] ?? [];
                            $sprindikSelected = old('suratPerintahPenyidikanDocument');
                        @endphp
                        <select class="form-control select2" name="suratPerintahPenyidikanDocument" id="suratPerintahPenyidikanDocument">
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $d)
                                <option value="{{ $d->id }}" {{ $sprindikSelected == $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suratKetetapanTentangPenetapanTersangkaDocument">Surat Ketetapan tentang Penetapan Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $sketSelected = old('suratKetetapanTentangPenetapanTersangkaDocument');
                        @endphp
                        <select class="form-control select2" name="suratKetetapanTentangPenetapanTersangkaDocument" id="suratKetetapanTentangPenetapanTersangkaDocument">
                            <option value="">--Pilih No Surat Ketetapan tentang Penetapan Tersangka--</option>
                            @foreach ($suratKetetapanTentangPenetapanTersangkaDocuments as $d)
                                <option value="{{ $d->id }}" {{ $sketSelected == $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $sphSelected = old('suratPerintahPenahanan');
                        @endphp
                        <select class="form-control select2 @error('suratPerintahPenahanan') is-invalid @enderror"
                            name="suratPerintahPenahanan" id="suratPerintahPenahanan" required>
                            <option value="">--Pilih Surat Perintah Penahanan--</option>
                            @foreach (($suratPerintahPenahananDocuments ?? collect()) as $d)
                                <option value="{{ $d->id }}" {{ (string) $sphSelected === (string) $d->id ? 'selected' : '' }}>
                                    {{ $d->document_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('suratPerintahPenahanan')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Pengeluaran Tahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" class="form-control" name="releaseOrderNumber" value="{{ old('releaseOrderNumber') }}" placeholder="Nomor"></div>
                            <div class="col-md-6"><input type="text" class="form-control s22-date" name="releaseOrderDate" value="{{ old('releaseOrderDate') }}" placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly></div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Pembantaran Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" class="form-control" name="hospitalizationOrderNumber" value="{{ old('hospitalizationOrderNumber') }}" placeholder="Nomor"></div>
                            <div class="col-md-6"><input type="text" class="form-control s22-date" name="hospitalizationOrderDate" value="{{ old('hospitalizationOrderDate') }}" placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly></div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Pencabutan Pembantaran Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" class="form-control" name="revokeHospitalizationOrderNumber" value="{{ old('revokeHospitalizationOrderNumber') }}" placeholder="Nomor"></div>
                            <div class="col-md-6"><input type="text" class="form-control s22-date" name="revokeHospitalizationOrderDate" value="{{ old('revokeHospitalizationOrderDate') }}" placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly></div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Pemindahan Tempat Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" class="form-control" name="transferDetentionPlaceOrderNumber" value="{{ old('transferDetentionPlaceOrderNumber') }}" placeholder="Nomor"></div>
                            <div class="col-md-6"><input type="text" class="form-control s22-date" name="transferDetentionPlaceOrderDate" value="{{ old('transferDetentionPlaceOrderDate') }}" placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly></div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Surat Perintah Pengalihan Jenis Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="row g-2">
                            <div class="col-md-6"><input type="text" class="form-control" name="transferDetentionTypeOrderNumber" value="{{ old('transferDetentionTypeOrderNumber') }}" placeholder="Nomor"></div>
                            <div class="col-md-6"><input type="text" class="form-control s22-date" name="transferDetentionTypeOrderDate" value="{{ old('transferDetentionTypeOrderDate') }}" placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly></div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Identitas Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                        <select class="form-control select2 @error('suspect') is-invalid @enderror" name="suspect" id="suspect" required>
                            <option value="">--Pilih Tersangka--</option>
                            @foreach ($suspects as $s)
                                <option value="{{ $s->id }}" {{ old('suspect') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}{{ !empty($s->identity_number) ? ' — ' . $s->identity_number : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('suspect')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small id="suspectHelpText" class="text-muted" style="display:none;">
                            (*Pilih Surat Perintah Penyidikan dan Surat Ketetapan Tersangka terlebih dahulu untuk membuka pilihan tersangka)
                        </small>
                    </div>
                </div>

                <hr>

                @php
                    $plLeaderId = old('officerLeader', $kepadaPrefillLeaderId ?? '');
                @endphp

                <h5 class="fw-bold text-blue-dark">Tim Penyidik</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="s22_officerLeader">Ketua Tim Penyidik <span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9">
                        <select name="officerLeader" id="s22_officerLeader" class="form-control select2" required>
                            <option value="">--Pilih Ketua Penyidik--</option>
                            @foreach ($leaderOfficers as $data)
                                @php
                                    $positionName = $data->position->name ?? '';
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}"
                                    {{ (string) $plLeaderId === (string) $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('officerLeader')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="row col-12 my-2 ms-0">
                    <label class="fw-bold">Penyidik<small> (*Pilihan Penyidik akan tampil setelah Ketua Tim
                            Penyidik dipilih)</small></label>

                    <div id="s22_internalOfficer">
                        <div class="alert alert-primary my-2" role="alert">
                            1. Pilihan Penyidik akan tampil setelah Ketua Tim Penyidik dipilih. <br />
                            2. Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai penyidik.
                        </div>

                        <div class="row my2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="s22_officerInternalMemberOption"
                                        aria-describedby="s22_officerInternalMemberOptionAddButtton">
                                        <option value="">--Pilih Penyidik--</option>
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="s22_officerInternalMemberOptionAddButtton"><i class="bi bi-plus-circle"></i>
                                        Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive my-2">
                            <table class="table table-bordered" id="s22_internalOfficerMemberTable">
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
                                    @foreach ($kepadaInternalRows ?? [] as $o)
                                        @php
                                            $rankName = $o->rank->name ?? '-';
                                            $positionName = $o->position->name ?? '-';
                                            $policeName = '-';
                                            if ($o->police) {
                                                $policeName = $o->police->full_name ?? ($o->police->name ?? '-');
                                            }
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $o->full_name }}</td>
                                            <td>{{ $rankName }}</td>
                                            <td class="registerNumber">{{ $o->register_number }}</td>
                                            <td>{{ $positionName }}</td>
                                            <td>{{ $policeName }}</td>
                                            <td>
                                                <input type="hidden" name="internalOfficers[]" value="{{ $o->register_number }}">
                                                <button class="btn btn-danger btn-sm s22_deleteInternalOfficer" type="button"><i class="bi bi-trash"></i> Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('internalOfficers')
                            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="signatoryOfficerId">Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $sigSelected = old('signatoryOfficerId');
                        @endphp
                        <select class="form-control select2 @error('signatoryOfficerId') is-invalid @enderror"
                            name="signatoryOfficerId" id="signatoryOfficerId" required>
                            <option value="">--Pilih Penandatangan--</option>
                            @foreach (($authorizedSignatories ?? collect()) as $o)
                                @php
                                    $positionName = $o->position->name ?? '-';
                                @endphp
                                <option value="{{ $o->id }}" {{ (string) $sigSelected === (string) $o->id ? 'selected' : '' }}>
                                    {{ !empty($o->register_number) ? $o->register_number : '' }} - {{ $o->full_name ?? $o->name ?? '' }} | {{ $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('signatoryOfficerId')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="submittedOfficerId">Yang Menyerahkan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        @php
                            $subSelected = old('submittedOfficerId');
                        @endphp
                        <select class="form-control select2 @error('submittedOfficerId') is-invalid @enderror"
                            name="submittedOfficerId" id="submittedOfficerId" required>
                            <option value="">--Pilih--</option>
                            @foreach (($submitterOfficers ?? collect()) as $o)
                                @php
                                    $positionName = $o->position->name ?? '-';
                                @endphp
                                <option value="{{ $o->id }}" {{ (string) $subSelected === (string) $o->id ? 'selected' : '' }}>
                                    {{ !empty($o->register_number) ? $o->register_number : '' }} - {{ $o->full_name ?? $o->name ?? '' }} | {{ $positionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('submittedOfficerId')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">KETERANGAN PENAHANAN</h5>

                @php
                    $alasanSelected = old('alasan');
                    $penempatanTypeOld = old('penempatan_type');
                    $penempatanOld = old('penempatan_detail');
                    $rutanSelected = old('penempatan_rutan_id');

                    $jenisPenahananOld = old('jenis_penahanan');
                    if ($jenisPenahananOld === null && $penempatanTypeOld) {
                        if ($penempatanTypeOld === 'rutan') {
                            $jenisPenahananOld = 'Penahanan Rumah Tahanan Negara';
                        } elseif ($penempatanTypeOld === 'rumah') {
                            $jenisPenahananOld = 'Penahanan Rumah';
                        } elseif ($penempatanTypeOld === 'kota') {
                            $jenisPenahananOld = 'Penahanan Kota';
                        }
                    }

                    $lokasiOld = old('lokasi_penahanan');
                    $cabangOld = old('cabang_penahanan');
                    if (($lokasiOld === null || $lokasiOld === '') && $rutanSelected && isset($prisons)) {
                        $prSel = $prisons->firstWhere('id', $rutanSelected);
                        if ($prSel) {
                            $lokasiOld = $prSel->name;
                            $cabangOld = $prSel->branch ?? '';
                        }
                    }

                    $alamatOld = old('alamat_penahanan');
                    if (($alamatOld === null || $alamatOld === '') && $penempatanTypeOld === 'rumah') {
                        $alamatOld = $penempatanOld;
                    }
                    $kotaOld = old('kota_penahanan');
                    if (($kotaOld === null || $kotaOld === '') && $penempatanTypeOld === 'kota') {
                        $kotaOld = $penempatanOld;
                    }
                @endphp

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alasan">Alasan Penahanan Lanjutan <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <select name="alasan" id="alasan" class="form-control select2" required>
                            <option value="">--Pilih--</option>
                            <option value="dikeluarkan" {{ $alasanSelected === 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                            <option value="melarikan_diri" {{ $alasanSelected === 'melarikan_diri' ? 'selected' : '' }}>Melarikan diri</option>
                            <option value="dibantarkan" {{ $alasanSelected === 'dibantarkan' ? 'selected' : '' }}>Dibantarkan</option>
                            <option value="ditangguhkan" {{ $alasanSelected === 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                            <option value="dipindahkan" {{ $alasanSelected === 'dipindahkan' ? 'selected' : '' }}>Dipindahkan</option>
                            <option value="dialihkan" {{ $alasanSelected === 'dialihkan' ? 'selected' : '' }}>Dialihkan jenis</option>
                        </select>
                    </div>
                </div>

                <!-- Jenis Penahanan (sama struktur dengan SPH create) -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_penahanan">Jenis Penahanan <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <select name="jenis_penahanan" id="jenis_penahanan" class="form-control select2" required>
                            <option value="">--Pilih Jenis Penahanan--</option>
                            @foreach (($detentionTypes ?? collect()) as $detention)
                                <option value="{{ $detention->type_name }}" {{ ($jenisPenahananOld ?? '') === $detention->type_name ? 'selected' : '' }}>
                                    {{ $detention->type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="rutanFields" class="penahanan-fields">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="lokasi_penahanan">Lokasi Penahanan <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select name="lokasi_penahanan" id="lokasi_penahanan" class="form-control select2">
                                <option value="">--Pilih Lokasi Penahanan--</option>
                                @foreach (($prisons ?? collect()) as $prison)
                                    <option value="{{ $prison->name }}" data-branch="{{ $prison->branch ?? '' }}"
                                        data-prison-id="{{ $prison->id }}"
                                        {{ (string) $rutanSelected === (string) $prison->id ? 'selected' : '' }}>
                                        {{ $prison->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="cabang_penahanan">Cabang Penahanan <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select name="cabang_penahanan" id="cabang_penahanan" class="form-control select2" disabled>
                                <option value="">--Pilih Cabang--</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="rumahFields" class="penahanan-fields" style="display: none;">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Alamat Penahanan</label>
                        <div class="col-lg-9">
                            <textarea name="alamat_penahanan" id="alamat_penahanan" class="form-control" rows="3" readonly>{{ $alamatOld ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div id="kotaFields" class="penahanan-fields" style="display: none;">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Kota/Kabupaten Penahanan</label>
                        <div class="col-lg-9">
                            <input type="text" name="kota_penahanan" id="kota_penahanan" class="form-control" value="{{ $kotaOld ?? '' }}" readonly>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="penempatan_type" id="s22_penempatan_type" value="{{ old('penempatan_type') }}">
                <input type="hidden" name="penempatan_detail" id="s22_penempatan_detail" value="{{ old('penempatan_detail') }}">
                <input type="hidden" name="penempatan_rutan_id" id="s22_penempatan_rutan_id" value="{{ old('penempatan_rutan_id') }}">

                <hr>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Perpanjangan Ke<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        <input type="hidden" name="extensionTo" value="{{ (int) old('extensionTo', $defaults['extension_to'] ?? 1) }}">
                        <input type="text" class="form-control" value="{{ (int) old('extensionTo', $defaults['extension_to'] ?? 1) }}" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Lama Perpanjangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                        @php
                            $range = $defaults['extension_range'] ?? [];
                            $extStart = old('extensionStartDate', $defaults['extension_start_date'] ?? ($range['start'] ?? null));
                            $extEnd = old('extensionEndDate', $range['end'] ?? null);
                        @endphp
                        <div class="row g-2 w-100">
                            <div class="col-md-4">
                                @if (!empty($extStart))
                                    <input type="hidden" name="extensionStartDate" value="{{ $extStart }}">
                                    <input type="text" id="extensionStartDateLocked" class="form-control" value="{{ $extStart }}" readonly>
                                @else
                                    <input type="text" name="extensionStartDate" id="extensionStartDate"
                                        class="form-control s22-date @error('extensionStartDate') is-invalid @enderror"
                                        value="{{ $extStart }}" placeholder="YYYY-MM-DD" autocomplete="off" readonly required>
                                    @error('extensionStartDate')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="extensionEndDate" id="extensionEndDate"
                                    class="form-control s22-date @error('extensionEndDate') is-invalid @enderror"
                                    value="{{ $extEnd }}" placeholder="YYYY-MM-DD" autocomplete="off" readonly required>
                                @error('extensionEndDate')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="extensionDaysPreview" class="form-control" readonly placeholder="Jumlah hari">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label">Penyerahan Surat Perintah Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input type="text" class="form-control s22-date" name="handoverDate" id="handoverDate"
                            value="{{ old('handoverDate') }}"
                            placeholder="Tanggal (YYYY-MM-DD)" autocomplete="off" readonly>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            // Sama seperti SPH: dropdown "Pilih Penyidik" pakai Select2 sejak awal (class select2-input-group).
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });
            $('#s22_officerInternalMemberOption').prop('disabled', true).trigger('change');

            function s22ToggleSuspectEnabled() {
                var ok = !!$('#suratPerintahPenyidikanDocument').val() &&
                    !!$('#suratKetetapanTentangPenetapanTersangkaDocument').val();

                $('#suspect').prop('disabled', !ok).trigger('change.select2');

                if (!ok) {
                    $('#suspectHelpText').show();
                } else {
                    $('#suspectHelpText').hide();
                }
            }

            $('#suratPerintahPenyidikanDocument, #suratKetetapanTentangPenetapanTersangkaDocument')
                .on('change', s22ToggleSuspectEnabled);
            s22ToggleSuspectEnabled();

            var s22DpOpts = { format: 'yyyy-mm-dd', autoclose: true };
            $('.s22-date').datepicker(s22DpOpts);
            $('.s22-date').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            var suspectAddresses = @json($suspectAddresses ?? []);
            var suspectRegencies = @json($suspectRegencies ?? []);
            var s22CabangPrefill = @json($cabangOld ?? '');

            var jenisPenahananSelect = $('#jenis_penahanan');
            var tersangkaSelect = $('#suspect');
            var rutanFields = $('#rutanFields');
            var rumahFields = $('#rumahFields');
            var kotaFields = $('#kotaFields');
            var lokasiPenahanan = $('#lokasi_penahanan');
            var cabangPenahanan = $('#cabang_penahanan');

            function s22SyncPenempatanHidden() {
                var jenis = jenisPenahananSelect.val();
                $('#s22_penempatan_type').val('');
                $('#s22_penempatan_detail').val('');
                $('#s22_penempatan_rutan_id').val('');
                if (jenis === 'Penahanan Rumah Tahanan Negara') {
                    $('#s22_penempatan_type').val('rutan');
                    var pid = lokasiPenahanan.find('option:selected').attr('data-prison-id');
                    $('#s22_penempatan_rutan_id').val(pid || '');
                } else if (jenis === 'Penahanan Rumah') {
                    $('#s22_penempatan_type').val('rumah');
                    $('#s22_penempatan_detail').val($('#alamat_penahanan').val());
                } else if (jenis === 'Penahanan Kota') {
                    $('#s22_penempatan_type').val('kota');
                    $('#s22_penempatan_detail').val($('#kota_penahanan').val());
                }
            }

            function loadAlamatTersangka() {
                var suspectId = tersangkaSelect.val();
                if (suspectId && suspectAddresses[suspectId]) {
                    $('#alamat_penahanan').val(suspectAddresses[suspectId]);
                } else {
                    $('#alamat_penahanan').val('');
                }
            }

            function loadKotaTersangka() {
                var suspectId = tersangkaSelect.val();
                if (suspectId && suspectRegencies[suspectId]) {
                    $('#kota_penahanan').val(suspectRegencies[suspectId]);
                } else {
                    $('#kota_penahanan').val('');
                }
            }

            $('.penahanan-fields').hide();

            jenisPenahananSelect.on('change', function() {
                var jenis = $(this).val();
                $('.penahanan-fields').hide();
                lokasiPenahanan.prop('required', false);
                cabangPenahanan.prop('required', false);
                if (jenis === 'Penahanan Rumah Tahanan Negara') {
                    rutanFields.show();
                    lokasiPenahanan.prop('required', true);
                    cabangPenahanan.prop('required', true);
                } else if (jenis === 'Penahanan Rumah') {
                    rumahFields.show();
                    loadAlamatTersangka();
                } else if (jenis === 'Penahanan Kota') {
                    kotaFields.show();
                    loadKotaTersangka();
                }
            });

            tersangkaSelect.on('change', function() {
                var jenis = jenisPenahananSelect.val();
                if (jenis === 'Penahanan Rumah') {
                    loadAlamatTersangka();
                } else if (jenis === 'Penahanan Kota') {
                    loadKotaTersangka();
                }
            });

            lokasiPenahanan.on('change', function() {
                var branch = $(this).find('option:selected').data('branch');
                if (branch) {
                    cabangPenahanan.prop('disabled', false)
                        .empty()
                        .append('<option value="' + branch + '">' + branch + '</option>')
                        .val(branch)
                        .trigger('change');
                } else {
                    cabangPenahanan.prop('disabled', true)
                        .empty()
                        .append('<option value="">--Pilih Cabang--</option>');
                }
            });

            (function s22InitialPenahananFromOld() {
                var jenis = jenisPenahananSelect.val();
                if (jenis === 'Penahanan Rumah Tahanan Negara') {
                    rutanFields.show();
                    lokasiPenahanan.prop('required', true);
                    cabangPenahanan.prop('required', true);
                    if (lokasiPenahanan.val()) {
                        lokasiPenahanan.trigger('change');
                        if (s22CabangPrefill) {
                            cabangPenahanan.val(s22CabangPrefill).trigger('change');
                        }
                    }
                } else if (jenis === 'Penahanan Rumah') {
                    rumahFields.show();
                    if (!$('#alamat_penahanan').val()) {
                        loadAlamatTersangka();
                    }
                } else if (jenis === 'Penahanan Kota') {
                    kotaFields.show();
                    if (!$('#kota_penahanan').val()) {
                        loadKotaTersangka();
                    }
                }
            })();

            function parseYmdLocal(str) {
                var m = String(str || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (!m) return null;
                var y = parseInt(m[1], 10), mo = parseInt(m[2], 10) - 1, d = parseInt(m[3], 10);
                var dt = new Date(y, mo, d);
                return isNaN(dt.getTime()) ? null : dt;
            }

            function getExtensionStartDateObj() {
                var $s = $('#extensionStartDate');
                if ($s.length) {
                    var gd = $s.datepicker('getDate');
                    if (gd) return gd;
                    return parseYmdLocal($s.val());
                }
                var hid = $('input[name="extensionStartDate"]').val() || $('#extensionStartDateLocked').val();
                return parseYmdLocal(hid);
            }

            function getExtensionEndDateObj() {
                var $e = $('#extensionEndDate');
                var gd = $e.datepicker('getDate');
                if (gd) return gd;
                return parseYmdLocal($e.val());
            }

            function dateAtMidnight(d) {
                return new Date(d.getFullYear(), d.getMonth(), d.getDate());
            }

            function updateDaysPreview() {
                var sd = getExtensionStartDateObj();
                var ed = getExtensionEndDateObj();
                if (!sd || !ed) {
                    $('#extensionDaysPreview').val('');
                    return;
                }
                var sd0 = dateAtMidnight(sd);
                var ed0 = dateAtMidnight(ed);
                if (ed0 < sd0) {
                    $('#extensionDaysPreview').val('');
                    return;
                }
                var diffDays = Math.round((ed0 - sd0) / 864e5) + 1;
                $('#extensionDaysPreview').val(diffDays + ' hari');
            }

            /** Tanggal "Sampai" tidak boleh sebelum tanggal "Dari" */
            function syncExtensionEndPickerMin() {
                var sd = getExtensionStartDateObj();
                try {
                    if (!$('#extensionEndDate').length) {
                        return;
                    }
                    if (sd && !isNaN(sd.getTime())) {
                        $('#extensionEndDate').datepicker('setStartDate', sd);
                        var ed = getExtensionEndDateObj();
                        if (ed && dateAtMidnight(ed) < dateAtMidnight(sd)) {
                            $('#extensionEndDate').val('');
                            $('#extensionEndDate').datepicker('update', '');
                        }
                    } else {
                        $('#extensionEndDate').datepicker('setStartDate', null);
                    }
                } catch (e) {}
                updateDaysPreview();
            }

            if ($('#extensionEndDate').data('datepicker')) {
                $('#extensionEndDate').datepicker('destroy');
            }
            var s0 = getExtensionStartDateObj();
            var endDpOpts = $.extend({}, s22DpOpts, s0 ? { startDate: s0 } : {});
            $('#extensionEndDate').datepicker(endDpOpts).on('changeDate hide change', updateDaysPreview);
            $('#extensionEndDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            if ($('#extensionStartDate').length) {
                if ($('#extensionStartDate').data('datepicker')) {
                    $('#extensionStartDate').datepicker('destroy');
                }
                $('#extensionStartDate').datepicker(s22DpOpts).on('changeDate hide change', function() {
                    syncExtensionEndPickerMin();
                });
            }

            syncExtensionEndPickerMin();

            $('form').on('submit', function(e) {
                s22SyncPenempatanHidden();
                if ($('#suspect').prop('disabled')) {
                    e.preventDefault();
                    alert('Isi dulu 2 surat referensi (Sprindik dan SKET) sebelum memilih tersangka.');
                    return false;
                }
            });
        });
    </script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var $internalMemberSelect = $('#s22_officerInternalMemberOption');
            var s22CachedInternalMembers = [];

            function s22GetAppendedRegisterSet() {
                var set = {};
                $('#s22_internalOfficerMemberTable tbody tr .registerNumber').each(function() {
                    var r = $(this).text().trim();
                    if (r) {
                        set[r] = true;
                    }
                });
                return set;
            }

            /** Sama seperti SPH populateMemberDropdown: jangan select2('destroy') supaya layout tidak loncat. */
            function s22EnsureMemberSelect2() {
                if (!$internalMemberSelect.hasClass('select2-hidden-accessible')) {
                    $internalMemberSelect.select2({
                        theme: 'bootstrap4'
                    });
                }
            }

            function s22RebuildMemberDropdown() {
                $internalMemberSelect.empty();
                $internalMemberSelect.append(new Option('--Pilih Penyidik--', ''));
                var appended = s22GetAppendedRegisterSet();
                s22CachedInternalMembers.forEach(function(member) {
                    var reg = member.register_number;
                    if (!reg || appended[reg]) {
                        return;
                    }
                    var rankName = (member.rank) ? member.rank.name : '-';
                    var positionName = (member.position) ? member.position.name : '-';
                    var policeName = (member.police) ? (member.police.full_name || member.police.name || '-') : '-';
                    $internalMemberSelect.append($('<option>', {
                        value: member.id,
                        text: member.register_number + ' - ' + member.full_name + ' | ' + positionName,
                        'data-register-number': member.register_number,
                        'data-rank-name': rankName,
                        'data-name': member.full_name,
                        'data-position-name': positionName,
                        'data-police-name': policeName,
                    }));
                });
                s22EnsureMemberSelect2();
                $internalMemberSelect.prop('disabled', false);
                $internalMemberSelect.val(null).trigger('change');
            }

            function s22LoadInternalOfficers(registerNumber) {
                if (!registerNumber) {
                    s22CachedInternalMembers = [];
                    $internalMemberSelect.empty();
                    $internalMemberSelect.append(new Option('--Pilih Penyidik--', ''));
                    s22EnsureMemberSelect2();
                    $internalMemberSelect.prop('disabled', true);
                    $internalMemberSelect.val(null).trigger('change');
                    return;
                }
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penyidikan-document.api.internal-officers', ['accident_id' => $accidentId]) }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        selectedLeaderOfficerRegisterNumber: registerNumber
                    },
                    success: function(response) {
                        s22CachedInternalMembers = response.data || [];
                        s22RebuildMemberDropdown();
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Maaf, terjadi kesalahan teknis.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });
            }

            $('#s22_officerLeader').on('change', function() {
                var registerNumber = $(this).find(':selected').data('register-number');
                var tablesToCheck = [{
                        tableSelector: '#s22_internalOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil, hapus terlebih dahulu untuk memilih sebagai ketua'
                    }
                ];
                var isAppended = false;
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var appendedRegisterNumber = $(this).find('.registerNumber').text().trim();
                        if (appendedRegisterNumber && appendedRegisterNumber == registerNumber) {
                            isAppended = true;
                            Swal.fire({
                                title: 'Gagal',
                                text: table.errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return false;
                        }
                    });
                });
                if (!isAppended) {
                    s22LoadInternalOfficers(registerNumber || '');
                }
            });

            if ($('#s22_officerLeader').val()) {
                s22LoadInternalOfficers($('#s22_officerLeader').find(':selected').data('register-number'));
            }

            $('#s22_officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#s22_officerInternalMemberOption').find('option:selected');
                if (selectedOption.val() == '' || selectedOption.val() == undefined) {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih personel penyidik terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
                if (!$('#s22_officerLeader').val()) {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Ketua Tim Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');
                var isDup = false;
                $('#s22_internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text().trim();
                    if (appendedRegisterNumber == registerNumber) {
                        isDup = true;
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Personil sudah ada dalam daftar',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        return false;
                    }
                });
                if (isDup) return;
                var newRow = $('<tr class="text-center"></tr>');
                newRow.append('<td>' + name + '</td>');
                newRow.append('<td>' + rankName + '</td>');
                newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                newRow.append('<td>' + positionName + '</td>');
                newRow.append('<td>' + policeName + '</td>');
                newRow.append('<td><input type="hidden" name="internalOfficers[]" value="' + registerNumber +
                    '"><button class="btn btn-danger btn-sm s22_deleteInternalOfficer" type="button"><i class="bi bi-trash"></i> Hapus</button></td>'
                );
                $('#s22_internalOfficerMemberTable tbody').append(newRow);
                s22RebuildMemberDropdown();
            });

            $(document).on('click', '.s22_deleteInternalOfficer', function() {
                $(this).closest('tr').remove();
                if ($('#s22_officerLeader').val() && s22CachedInternalMembers.length) {
                    s22RebuildMemberDropdown();
                }
            });
        });
    </script>
@endpush

