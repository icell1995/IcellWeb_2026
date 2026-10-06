@php
    $_title = 'Surat Perintah Pembantaran Penahanan (S-23)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')

    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Pembantaran Penahanan (S-23)</h5>

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
            
            @if ($errors->any())
                <div class="card-body">
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
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-pembantaran-penahanan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" id="s23Form" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="accidentNumber" type="text"
                            class="form-control font-weight-bold"
                            name="accidentNumber" value="{{ $accident->no_lp }}" required placeholder="" readonly>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Surat S-23<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat"
                            value="{{ old('nomor_surat') }}" placeholder="Contoh: SP.Bantar.Han/0001/I/RES.0.0./2026/Satker" required>
                        @error('nomor_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat">Tanggal Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat" type="text" class="form-control @error('tanggal_surat') is-invalid @enderror" name="tanggal_surat"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat', date('Y-m-d')) }}" data-provide="datepicker" required>
                        @error('tanggal_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="dikeluarkan_di">Dikeluarkan Di<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="dikeluarkan_di" type="text" class="form-control @error('dikeluarkan_di') is-invalid @enderror" name="dikeluarkan_di"
                            value="{{ old('dikeluarkan_di', $defaultDikeluarkanDi) }}" required placeholder="Contoh: Simalungun">
                        @error('dikeluarkan_di')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pertimbangan">Pertimbangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="pertimbangan" class="form-control @error('pertimbangan') is-invalid @enderror" name="pertimbangan" rows="3"
                            placeholder="Alasan / Pertimbangan dikeluarkannya surat perintah pembantaran penahanan..." required>{{ old('pertimbangan', $defaultPertimbangan) }}</textarea>
                        @error('pertimbangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Data SPDP & Satker (SPPT-TI)</h5>

                @if($spdpDocument)
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle-fill me-1"></i> SPDP Terdeteksi: Dokumen SPDP <b>{{ $spdpDocument->document_number }}</b> tanggal <b>{{ date('d-m-Y', strtotime($spdpDocument->document_date)) }}</b> telah dihubungkan otomatis.
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_spdp">Nomor SPDP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_spdp" type="text" class="form-control @error('nomor_spdp') is-invalid @enderror" name="nomor_spdp"
                            value="{{ old('nomor_spdp', $spdpDocument->document_number ?? '') }}" placeholder="Nomor SPDP">
                        @error('nomor_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_spdp">Tanggal SPDP</label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_spdp" type="text" class="form-control @error('tanggal_spdp') is-invalid @enderror" name="tanggal_spdp"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_spdp', isset($spdpDocument->document_date) ? Carbon\Carbon::parse($spdpDocument->document_date)->format('Y-m-d') : '') }}" data-provide="datepicker">
                        @error('tanggal_spdp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_satker_penerbit_spdp">Satker Penerbit SPDP</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_satker_penerbit_spdp" type="text" class="form-control font-weight-bold" name="nama_satker_penerbit_spdp"
                            value="{{ old('nama_satker_penerbit_spdp', $satkerName ?? $accident->police->full_name ?? $accident->polres->full_name ?? $accident->polres->name ?? '') }}" readonly>
                        <input type="hidden" name="kode_satker_penerbit_spdp" id="kode_satker_penerbit_spdp"
                            value="{{ old('kode_satker_penerbit_spdp', $satkerCode ?? $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '') }}">
                        <small class="text-muted">Nama kesatuan penerbit SPDP disesuaikan otomatis berdasarkan data Satker Laporan Polisi.</small>
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Rujukan Dasar Perkara & Surat Perintah</h5>

                @if($sprinSidik)
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle-fill me-1"></i> Sprint Sidik Terdeteksi: Nomor <b>{{ $sprinSidik->document_number }}</b> tanggal <b>{{ date('d-m-Y', strtotime($sprinSidik->document_date)) }}</b> telah dihubungkan otomatis.
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_perintah_penyidikan">No. Sprint Sidik</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_penyidikan" type="text" class="form-control @error('nomor_surat_perintah_penyidikan') is-invalid @enderror" name="nomor_surat_perintah_penyidikan"
                            value="{{ old('nomor_surat_perintah_penyidikan', $sprinSidik->document_number ?? '') }}" placeholder="Contoh: Sp.Sidik/12/I/2026/Lantas">
                        @error('nomor_surat_perintah_penyidikan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_perintah_penyidikan">Tgl. Sprint Sidik</label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_perintah_penyidikan" type="text" class="form-control @error('tanggal_surat_perintah_penyidikan') is-invalid @enderror" name="tanggal_surat_perintah_penyidikan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_perintah_penyidikan', isset($sprinSidik->document_date) ? Carbon\Carbon::parse($sprinSidik->document_date)->format('Y-m-d') : '') }}" data-provide="datepicker">
                        @error('tanggal_surat_perintah_penyidikan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if($sketTersangkaDocument)
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle-fill me-1"></i> Penetapan Tersangka Terdeteksi: S.Ket Penetapan Tersangka <b>{{ $sketTersangkaDocument->document_number }}</b> tanggal <b>{{ date('d-m-Y', strtotime($sketTersangkaDocument->document_date)) }}</b> telah dihubungkan otomatis.
                    </div>
                @endif

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_sket_tersangka">No. S.Ket Tersangka</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_sket_tersangka" type="text" class="form-control @error('nomor_sket_tersangka') is-invalid @enderror" name="nomor_sket_tersangka"
                            value="{{ old('nomor_sket_tersangka', $sketTersangkaDocument->document_number ?? '') }}" placeholder="Nomor Surat Ketetapan Tersangka">
                        @error('nomor_sket_tersangka')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_sket_tersangka">Tgl. S.Ket Tersangka</label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_sket_tersangka" type="text" class="form-control @error('tanggal_sket_tersangka') is-invalid @enderror" name="tanggal_sket_tersangka"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_sket_tersangka', isset($sketTersangkaDocument->document_date) ? Carbon\Carbon::parse($sketTersangkaDocument->document_date)->format('Y-m-d') : '') }}" data-provide="datepicker">
                        @error('tanggal_sket_tersangka')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="surat_perintah_penahanan_document_id">Dokumen S-17 Terkait</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('surat_perintah_penahanan_document_id') is-invalid @enderror" name="surat_perintah_penahanan_document_id" id="surat_perintah_penahanan_document_id">
                            <option value="">-- Hubungkan Dokumen S-17 (Otomatis Isi / Tertaut) --</option>
                            @if(isset($s17Documents))
                                @foreach($s17Documents as $s17)
                                    @php
                                        $s17SuspectId = ($s17->suspects && $s17->suspects->isNotEmpty()) ? ($s17->suspects->first()->id ?? $s17->suspects->first()->suspect_id) : '';
                                    @endphp
                                    <option value="{{ $s17->id }}" 
                                        data-nomor="{{ $s17->nomor ?? $s17->document_number }}" 
                                        data-tanggal="{{ $s17->tanggal ?? $s17->document_date }}"
                                        data-suspect-id="{{ $s17SuspectId }}"
                                        {{ (old('surat_perintah_penahanan_document_id', $defaultS17Id ?? null) == $s17->id) ? 'selected' : '' }}>
                                        {{ ($s17->nomor ?? $s17->document_number ?? 'S-17') . ' (' . ($s17->tanggal ? Carbon\Carbon::parse($s17->tanggal)->format('d/m/Y') : ($s17->document_date ? Carbon\Carbon::parse($s17->document_date)->format('d/m/Y') : '-')) . ')' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Pilih surat perintah penahanan (S-17) untuk menautkan relasi dan mengisi otomatis nomor, tanggal, serta tersangka yang dibantarkan.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_perintah_penahanan">No. Sprint Penahanan (S-17)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_perintah_penahanan" type="text" class="form-control @error('nomor_surat_perintah_penahanan') is-invalid @enderror" name="nomor_surat_perintah_penahanan"
                            value="{{ old('nomor_surat_perintah_penahanan', $defaultNomorSuratPerintahPenahanan ?? '') }}" placeholder="Contoh: Sp.Han/01/I/2026/Lantas" required>
                        @error('nomor_surat_perintah_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_perintah_penahanan">Tgl. Sprint Penahanan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_perintah_penahanan" type="text" class="form-control @error('tanggal_surat_perintah_penahanan') is-invalid @enderror" name="tanggal_surat_perintah_penahanan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_perintah_penahanan', $defaultTanggalSuratPerintahPenahanan ? Carbon\Carbon::parse($defaultTanggalSuratPerintahPenahanan)->format('Y-m-d') : '') }}" data-provide="datepicker" required>
                        @error('tanggal_surat_perintah_penahanan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Data Dokter & Rumah Sakit (SPPT-TI)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_dokter">Nama Dokter Pemeriksa/Rawat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_dokter" type="text" class="form-control @error('nama_dokter') is-invalid @enderror" name="nama_dokter"
                            value="{{ old('nama_dokter') }}" placeholder="Contoh: dr. Ahmad Santoso, Sp.B" required>
                        @error('nama_dokter')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_dokter">No. Surat Keterangan Dokter</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_dokter" type="text" class="form-control @error('nomor_surat_dokter') is-invalid @enderror" name="nomor_surat_dokter"
                            value="{{ old('nomor_surat_dokter') }}" placeholder="Contoh: SK/012/RSUD/I/2026">
                        @error('nomor_surat_dokter')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_dokter">Tgl. Surat Keterangan Dokter</label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_dokter" type="text" class="form-control @error('tanggal_surat_dokter') is-invalid @enderror" name="tanggal_surat_dokter"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_dokter') }}" data-provide="datepicker">
                        @error('tanggal_surat_dokter')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tempat_rawat_inap">Rumah Sakit Tempat Opname<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="tempat_rawat_inap" type="text" class="form-control @error('tempat_rawat_inap') is-invalid @enderror" name="tempat_rawat_inap"
                            value="{{ old('tempat_rawat_inap') }}" placeholder="Contoh: RSUD Djasamen Saragih" required>
                        @error('tempat_rawat_inap')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="kota_rumah_sakit">Kota Rumah Sakit<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="kota_rumah_sakit" type="text" class="form-control @error('kota_rumah_sakit') is-invalid @enderror" name="kota_rumah_sakit"
                            value="{{ old('kota_rumah_sakit', $defaultKotaRumahSakit) }}" placeholder="Contoh: Pematangsiantar" required>
                        @error('kota_rumah_sakit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_mulai_rawat_inap">Tgl. Mulai Rawat Inap<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_mulai_rawat_inap" type="text" class="form-control @error('tanggal_mulai_rawat_inap') is-invalid @enderror" name="tanggal_mulai_rawat_inap"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_mulai_rawat_inap', date('Y-m-d')) }}" data-provide="datepicker" required>
                        @error('tanggal_mulai_rawat_inap')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Undang-Undang & Pasal yang Dikenakan<span class="text-danger fs-5">*</span></h5>

                <div class="row col-12 my-2 ms-0">
                    <div id="law">
                        <div class="row mb-2">
                            <div class="col">
                                <button class="btn btn-primary float-right" id="addLawButton" type="button"
                                    data-bs-toggle="modal" data-bs-target="#addLawModal"><i class="bi bi-plus-circle"></i>
                                    Tambah</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="lawTable">
                                <thead class="table-danger">
                                    <tr class="text-center">
                                        <th scope="col">Jenis Kejahatan</th>
                                        <th scope="col">Golongan Kejahatan</th>
                                        <th scope="col">Undang-Undang</th>
                                        <th scope="col">Pasal</th>
                                        <th scope="col">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row col-12 my-2 ms-0">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label" for="additionalLaw">Undang-Undang Khusus Tambahan</label>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                            <input id="additionalLaw" type="text"
                                class="form-control font-weight-bold"
                                name="additionalLaw" value=""
                                placeholder="(Jika Ada) Contoh: Undang-Undang nomor 22 tahun 2009 LLAJ tentang Pengemudi mabuk">
                            <div class="row mt-2">
                                <div class="col">
                                    <button class="btn btn-primary" id="saveAdditionalLawButton" type="button"><i
                                            class="bi bi-plus-circle"></i> Tambah</button>
                                    <button class="btn btn-secondary" id="clearAdditionalLawButton" type="button"><i
                                            class="bi bi-trash"></i> Bersihkan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="additionalLawTable">
                            <thead class="table-danger">
                                <tr class="text-center">
                                    <th scope="col">Nama</th>
                                    <th scope="col">Opsi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Tersangka yang Dibantarkan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="suspects">Pilih Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspects') is-invalid @enderror" name="suspects[]" id="suspects" multiple="multiple" data-placeholder="Pilih Tersangka..." required>
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ (is_array(old('suspects')) && in_array($suspect->id, old('suspects'))) || (!old('suspects') && (isset($defaultSuspectId) && $defaultSuspectId == $suspect->id)) || (!old('suspects') && !isset($defaultSuspectId) && $loop->first) ? 'selected' : '' }}>
                                    {{ $suspect->name }} (NIK: {{ $suspect->identity_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @if($suspects->isEmpty())
                            <small class="text-muted fst-italic mt-1 d-block"><i class="bi bi-info-circle"></i> Belum ada data Tersangka yang terdaftar pada Laporan Polisi ini.</small>
                        @endif
                        @error('suspects')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Personil & Pejabat Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="signatory">Penyidik Penandatangan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('signatory') is-invalid @enderror" name="signatory" id="signatory" required>
                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                            @foreach ($authorizedSignatories as $signatory)
                                <option value="{{ $signatory->id }}" {{ old('signatory') == $signatory->id ? 'selected' : '' }}>
                                    {{ $signatory->full_name }} ({{ $signatory->register_number }}) - {{ $signatory->position->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="receiver_officer_id">Petugas Penerima Perintah</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('receiver_officer_id') is-invalid @enderror" name="receiver_officer_id" id="receiver_officer_id">
                            <option value="">-- (Opsional) Pilih Petugas Penerima Perintah --</option>
                            @foreach ($memberOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ old('receiver_officer_id') == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->full_name }} ({{ $officer->register_number }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Personil yang ditugaskan membantar, mengawal, atau mengawasi tersangka di Rumah Sakit.</small>
                        @error('receiver_officer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="deliverer_officer_id">Petugas Penyerah Surat</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('deliverer_officer_id') is-invalid @enderror" name="deliverer_officer_id" id="deliverer_officer_id">
                            <option value="">-- (Opsional) Pilih Petugas Penyerah --</option>
                            @foreach ($memberOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ old('deliverer_officer_id') == $officer->id ? 'selected' : '' }}>
                                    {{ $officer->full_name }} ({{ $officer->register_number }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Petugas yang menyerahkan lembar surat perintah kepada tersangka / keluarga tersangka.</small>
                        @error('deliverer_officer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold text-blue-dark">Lembar Penyerahan Surat</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="hari_penyerahan">Hari Penyerahan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="hari_penyerahan" type="text" class="form-control @error('hari_penyerahan') is-invalid @enderror" name="hari_penyerahan"
                            value="{{ old('hari_penyerahan', $defaultHariPenyerahan) }}" placeholder="Contoh: Senin / Selasa / dll" required>
                        @error('hari_penyerahan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_penyerahan">Tanggal Penyerahan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_penyerahan" type="text" class="form-control @error('tanggal_penyerahan') is-invalid @enderror" name="tanggal_penyerahan"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_penyerahan', $defaultTanggalPenyerahan) }}" data-provide="datepicker" required>
                        @error('tanggal_penyerahan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_penerima_keluarga">Nama Penerima</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_penerima_keluarga" type="text" class="form-control @error('nama_penerima_keluarga') is-invalid @enderror" name="nama_penerima_keluarga"
                            value="{{ old('nama_penerima_keluarga') }}" placeholder="Contoh: Budi (Keluarga) / Tersangka Sendiri">
                        <small class="text-muted">Nama keluarga atau tersangka yang menerima tembusan/lembar surat.</small>
                        @error('nama_penerima_keluarga')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="hubungan_penerima">Hubungan Penerima</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select class="form-control select2 @error('hubungan_penerima') is-invalid @enderror" name="hubungan_penerima" id="hubungan_penerima">
                            <option value="Tersangka" {{ old('hubungan_penerima', 'Tersangka') == 'Tersangka' ? 'selected' : '' }}>Tersangka Sendiri</option>
                            <option value="Istri" {{ old('hubungan_penerima') == 'Istri' ? 'selected' : '' }}>Istri</option>
                            <option value="Suami" {{ old('hubungan_penerima') == 'Suami' ? 'selected' : '' }}>Suami</option>
                            <option value="Orang Tua" {{ old('hubungan_penerima') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua (Ayah/Ibu)</option>
                            <option value="Anak" {{ old('hubungan_penerima') == 'Anak' ? 'selected' : '' }}>Anak</option>
                            <option value="Saudara Kandung" {{ old('hubungan_penerima') == 'Saudara Kandung' ? 'selected' : '' }}>Saudara Kandung</option>
                            <option value="Kuasa Hukum" {{ old('hubungan_penerima') == 'Kuasa Hukum' ? 'selected' : '' }}>Penasihat Hukum / Advokat</option>
                            <option value="Lainnya" {{ old('hubungan_penerima') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('hubungan_penerima')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue" id="s23FormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>
            </form>
    </div>

    <!-- Modal Add Law -->
    <div class="modal fade" id="addLawModal" tabindex="-1" role="dialog" aria-labelledby="addLawModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modalContent">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-blue-dark" id="addLawModalLabel">Tambah Kejahatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary my-3" role="alert">
                        Jika tidak terdapat opsi yang sesuai, silahkan menghubungi Helpdesk ICELL untuk koordinasi.
                    </div>
                    <form id="addLawForm">
                        <div class="mb-3 form-validate">
                            <label class="fw-bold" for="crimeTypeLawForm">Jenis Kejahatan</label>
                            <select class="form-control" id="crimeTypeLawForm" name="crimeTypeLawForm">
                                <option value="">--Pilih Jenis Kejahatan--</option>
                                @foreach ($crimeTypes as $crimeType)
                                    <option value="{{ $crimeType->id }}" data-crime-type-name="{{ $crimeType->name }}"
                                        data-crime-class-id="{{ $crimeType->crimeClass->id ?? '' }}"
                                        data-crime-class-name="{{ $crimeType->crimeClass->name ?? '' }}"
                                        data-crime-constitution-id="{{ $crimeType->crimeConstitution->id ?? '' }}"
                                        data-crime-constitution-name="{{ $crimeType->crimeConstitution->name ?? '' }}"
                                        data-chapter="{{ $crimeType->crimeConstitution->chapter ?? '' }}">
                                        {{ $crimeType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 form-validate">
                            <label class="fw-bold" for="crimeClassLawForm">Golongan Kejahatan</label>
                            <select class="form-control" id="crimeClassLawForm" disabled>
                                <option value="">--Pilih Golongan Kejahatan--</option>
                                @foreach ($crimeClasses as $crimeClass)
                                    <option value="{{ $crimeClass->id }}"
                                        data-crime-class-name="{{ $crimeClass->name }}">{{ $crimeClass->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 form-validate">
                            <label class="fw-bold" for="crimeConstitutionLawForm">Undang-Undang</label>
                            <select class="form-control" id="crimeConstitutionLawForm" disabled>
                                <option value="">--Pilih Undang-Undang--</option>
                                @foreach ($crimeConstitutions as $crimeConstitution)
                                    <option value="{{ $crimeConstitution->id }}"
                                        data-crime-constitution-name="{{ $crimeConstitution->name }}"
                                        data-chapter="{{ $crimeConstitution->chapter }}">{{ $crimeConstitution->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="fw-bold" for="constitutionChapterLawForm">Pasal</label>
                            <select class="form-control" id="constitutionChapterLawForm" name="constitutionChapterLawForm" disabled>
                                <option value="">--Pilih Pasal-Ayat--</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                    <button type="button" class="btn btn-primary" id="saveAddLawFormButton"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('[data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            $('[data-provide="datepicker"]').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            // Mapping pasal options per Jenis Kejahatan (crime_type_id)
            const pasalOptions = {
                "1": [
                    { id: "Pasal 273 Ayat (1)", text: "Pasal 273 Ayat (1) [Kecelakaan Karena Jalan Rusak]" },
                    { id: "Pasal 273 Ayat (2)", text: "Pasal 273 Ayat (2) [Kecelakaan Karena Jalan Rusak]" },
                    { id: "Pasal 273 Ayat (3)", text: "Pasal 273 Ayat (3) [Kecelakaan Karena Jalan Rusak]" }
                ],
                "2": [
                    { id: "Pasal 275 Ayat (2)", text: "Pasal 275 Ayat (2) [Merusak Rambu-Rambu Dan Fasilitas Jalan]" }
                ],
                "3": [
                    { id: "Pasal 277", text: "Pasal 277 [Kecelakaan Karena Overdimensi Dan Overload]" }
                ],
                "4": [
                    { id: "Pasal 310 Ayat (1)", text: "Pasal 310 Ayat (1) [Kecelakaan Karena Lalai]" },
                    { id: "Pasal 310 Ayat (2)", text: "Pasal 310 Ayat (2) [Kecelakaan Karena Lalai]" },
                    { id: "Pasal 310 Ayat (3)", text: "Pasal 310 Ayat (3) [Kecelakaan Karena Lalai]" },
                    { id: "Pasal 310 Ayat (4)", text: "Pasal 310 Ayat (4) [Kecelakaan Karena Lalai]" }
                ],
                "5": [
                    { id: "Pasal 311 Ayat (1)", text: "Pasal 311 Ayat (1) [Kesengajaan Yang Mengakibatkan Kecelakaan]" },
                    { id: "Pasal 311 Ayat (2)", text: "Pasal 311 Ayat (2) [Kesengajaan Yang Mengakibatkan Kecelakaan]" },
                    { id: "Pasal 311 Ayat (3)", text: "Pasal 311 Ayat (3) [Kesengajaan Yang Mengakibatkan Kecelakaan]" },
                    { id: "Pasal 311 Ayat (4)", text: "Pasal 311 Ayat (4) [Kesengajaan Yang Mengakibatkan Kecelakaan]" },
                    { id: "Pasal 311 Ayat (5)", text: "Pasal 311 Ayat (5) [Kesengajaan Yang Mengakibatkan Kecelakaan]" }
                ],
                "6": [
                    { id: "Pasal 312", text: "Pasal 312 [Tabrak Lari / Kecelakaan Karena Tidak Melakukan Pertolongan]" }
                ]
            };

            // Inisialisasi baris tabel dari initialMainLaws
            var initialMainLaws = @json($initialMainLaws ?? []);
            if (initialMainLaws && initialMainLaws.length > 0) {
                initialMainLaws.forEach(function(law) {
                    $('#lawTable tbody').append(
                        '<tr class="text-center">' +
                        '<td>' + (law.crime_type_name || '') + '</td>' +
                        '<td>' + (law.crime_class_name || '') + '</td>' +
                        '<td>' + (law.crime_constitution_name || '') + '</td>' +
                        '<td>' + (law.constitution_chapter || '') + '</td>' +
                        '<td>' +
                        '<input type="hidden" name="lawCrimeTypeIds[]" value="' + (law.crime_type_id || '') + '">' +
                        '<input type="hidden" name="lawCrimeClassIds[]" value="' + (law.crime_class_id || '') + '">' +
                        '<input type="hidden" name="lawCrimeConstitutionIds[]" value="' + (law.crime_constitution_id || '') + '">' +
                        '<input type="hidden" name="lawCrimeConstitutionChapters[]" value="' + (law.constitution_chapter || '') + '">' +
                        '<button type="button" class="btn btn-danger btn-sm deleteLaw"><i class="bi bi-trash"></i></button>' +
                        '</td>' +
                        '</tr>'
                    );
                });
            }

            // Inisialisasi baris tabel dari initialAdditionalLaws
            var initialAdditionalLaws = @json($initialAdditionalLaws ?? []);
            if (initialAdditionalLaws && initialAdditionalLaws.length > 0) {
                initialAdditionalLaws.forEach(function(law) {
                    $('#additionalLawTable tbody').append(
                        '<tr class="text-center">' +
                        '<td>' + (law.constitution || '') + '</td>' +
                        '<td>' +
                        '<input type="hidden" name="lawAdditionalNames[]" value="' + (law.constitution || '') + '">' +
                        '<button type="button" class="btn btn-danger btn-sm deleteAdditionalLaw"><i class="bi bi-trash"></i></button>' +
                        '</td>' +
                        '</tr>'
                    );
                });
            }

            // Change event jenis kejahatan di modal
            $('#crimeTypeLawForm').on('change', function() {
                var lawCrimeTypeId = $(this).find(':selected').val();
                var lawCrimeClassId = $(this).find(':selected').data('crime-class-id');
                var lawCrimeConstitutionId = $(this).find(':selected').data('crime-constitution-id');

                $('#crimeClassLawForm').val(lawCrimeClassId).trigger('change');
                $('#crimeConstitutionLawForm').val(lawCrimeConstitutionId).trigger('change');

                $("#constitutionChapterLawForm").empty().append('<option value="">--Pilih Pasal-Ayat--</option>');
                if (lawCrimeTypeId && pasalOptions[lawCrimeTypeId]) {
                    $.each(pasalOptions[lawCrimeTypeId], function (index, pasal) {
                        $("#constitutionChapterLawForm").append(`<option value="${pasal.id}">${pasal.text}</option>`);
                    });
                    $("#constitutionChapterLawForm").prop("disabled", false);
                } else {
                    $("#constitutionChapterLawForm").prop("disabled", true);
                }
            });

            // Simpan dari modal ke tabel
            $('#saveAddLawFormButton').on('click', function(e) {
                e.preventDefault();
                var lawCrimeTypeId = $('#crimeTypeLawForm').find(':selected').val();
                var lawCrimeTypeName = $('#crimeTypeLawForm').find(':selected').data('crime-type-name');
                var lawCrimeClassId = $('#crimeClassLawForm').find(':selected').val();
                var lawCrimeClassName = $('#crimeClassLawForm').find(':selected').data('crime-class-name');
                var lawCrimeConstitutionId = $('#crimeConstitutionLawForm').find(':selected').val();
                var lawCrimeConstitutionName = $('#crimeConstitutionLawForm').find(':selected').data('crime-constitution-name');
                var lawCrimeConstitutionChapter = $('#constitutionChapterLawForm').find(':selected').val();

                $('#addLawForm small.text-danger').remove();
                if (!lawCrimeTypeId || !lawCrimeClassId || !lawCrimeConstitutionId || !lawCrimeConstitutionChapter) {
                    if (!lawCrimeTypeId) $('#crimeTypeLawForm').parent().append('<small class="text-danger">Inputan ini wajib diisi</small>');
                    if (!lawCrimeClassId) $('#crimeClassLawForm').parent().append('<small class="text-danger">Inputan ini wajib diisi</small>');
                    if (!lawCrimeConstitutionId) $('#crimeConstitutionLawForm').parent().append('<small class="text-danger">Inputan ini wajib diisi</small>');
                    if (!lawCrimeConstitutionChapter) $('#constitutionChapterLawForm').parent().append('<small class="text-danger">Inputan ini wajib diisi</small>');
                    return false;
                }

                $('#lawTable tbody').append(
                    '<tr class="text-center">' +
                    '<td>' + lawCrimeTypeName + '</td>' +
                    '<td>' + lawCrimeClassName + '</td>' +
                    '<td>' + lawCrimeConstitutionName + '</td>' +
                    '<td>' + lawCrimeConstitutionChapter + '</td>' +
                    '<td>' +
                    '<input type="hidden" name="lawCrimeTypeIds[]" value="' + lawCrimeTypeId + '">' +
                    '<input type="hidden" name="lawCrimeClassIds[]" value="' + lawCrimeClassId + '">' +
                    '<input type="hidden" name="lawCrimeConstitutionIds[]" value="' + lawCrimeConstitutionId + '">' +
                    '<input type="hidden" name="lawCrimeConstitutionChapters[]" value="' + lawCrimeConstitutionChapter + '">' +
                    '<button type="button" class="btn btn-danger btn-sm deleteLaw"><i class="bi bi-trash"></i></button>' +
                    '</td>' +
                    '</tr>'
                );

                $('#addLawModal').modal('hide');
                $('#crimeTypeLawForm').val('').trigger('change');
            });

            // Delete row law
            $(document).on('click', '.deleteLaw', function() {
                $(this).closest('tr').remove();
            });

            // Additional Law
            $('#saveAdditionalLawButton').on('click', function(e) {
                e.preventDefault();
                var lawAdditionalName = $('#additionalLaw').val().trim();
                $('#additionalLaw').parent().find('small.text-danger').remove();

                if (!lawAdditionalName) {
                    $('#additionalLaw').parent().append('<small class="text-danger">Inputan ini wajib diisi</small>');
                    return false;
                }

                $('#additionalLawTable tbody').append(
                    '<tr class="text-center">' +
                    '<td>' + lawAdditionalName + '</td>' +
                    '<td>' +
                    '<input type="hidden" name="lawAdditionalNames[]" value="' + lawAdditionalName + '">' +
                    '<button type="button" class="btn btn-danger btn-sm deleteAdditionalLaw"><i class="bi bi-trash"></i></button>' +
                    '</td>' +
                    '</tr>'
                );
                $('#additionalLaw').val('');
            });

            $('#clearAdditionalLawButton').on('click', function(e) {
                e.preventDefault();
                $('#additionalLaw').val('');
                $('#additionalLaw').parent().find('small.text-danger').remove();
            });

            $(document).on('click', '.deleteAdditionalLaw', function() {
                $(this).closest('tr').remove();
            });

            // Auto-fill saat memilih dokumen relasi S-17
            $('#surat_perintah_penahanan_document_id').on('change', function() {
                var $opt = $(this).find('option:selected');
                var nomor = $opt.data('nomor');
                var tanggal = $opt.data('tanggal');
                var suspectId = $opt.data('suspect-id');
                if (nomor) {
                    $('#nomor_surat_perintah_penahanan').val(nomor);
                }
                if (tanggal) {
                    $('#tanggal_surat_perintah_penahanan').val(tanggal.toString().substring(0, 10));
                }
                if (suspectId) {
                    $('#suspects').val([suspectId]).trigger('change');
                }
            });



            // AJAX Validation handling on form submit with SweetAlert2
            $('#s23Form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#s23FormSubmit');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

                var formData = $(this).serialize();
                var url = "{{ route('doc.surat-perintah-pembantaran-penahanan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}";
                
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message || 'Data valid, menyimpan dokumen...',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#s23Form')[0].submit();
                            });
                        } else {
                            $btn.prop('disabled', false).html(originalHtml);
                            Swal.fire({
                                icon: 'error',
                                title: 'Perhatian',
                                text: response.message || 'Gagal memvalidasi form.'
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        try {
                            var res = JSON.parse(xhr.responseText);
                            if (xhr.status === 422 && res.errors) {
                                var errorMessages = '';
                                $.each(res.errors, function(key, value) {
                                    var msg = Array.isArray(value) ? value[0] : value;
                                    errorMessages += '- ' + msg + '<br>';
                                });

                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Mohon Periksa Kembali Isian Anda',
                                    html: errorMessages,
                                });
                            }
                        } catch (err) {}

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal memvalidasi form dokumen S-23 (Kode: ' + xhr.status + ').'
                        });
                    }
                });
            });
        });
    </script>
@endpush
