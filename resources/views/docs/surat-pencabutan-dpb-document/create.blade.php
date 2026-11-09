@php
    $_title = 'Surat Pencabutan Daftar Pencarian Barang (S-24.6.2)';
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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Pencabutan Daftar Pencarian Barang (S-24.6.2)</h5>

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
            <form action="{{ route('doc.surat-pencabutan-dpb-document.store') }}"
                method="POST" id="formSuratPencabutanDpb" novalidate>
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- ─── 1. INFORMASI DASAR DOKUMEN & DASAR PENCABUTAN ─── --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_lp">Nomor LP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_lp" type="text"
                            class="form-control font-weight-bold @error('nomor_lp') is-invalid @enderror"
                            name="nomor_lp" value="{{ old('nomor_lp', $accident->no_lp) }}" required placeholder="Nomor LP" readonly>
                        <input type="hidden" name="accidentNumber" id="accidentNumber" value="{{ $accident->no_lp }}">
                        <input type="hidden" name="tanggal_lp" id="tanggal_lp" value="{{ old('tanggal_lp', $accident->report_date ? \Carbon\Carbon::parse($accident->report_date)->format('Y-m-d') : date('Y-m-d')) }}">
                        @error('nomor_lp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat">Nomor Surat Pencabutan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat" type="text" class="form-control @error('nomor_surat') is-invalid @enderror" name="nomor_surat"
                            value="{{ old('nomor_surat', 'B/PENCABUTAN-DPB/001/' . date('m') . '/RES.0.0/' . date('Y')) }}" required placeholder="Contoh: B/PENCABUTAN-DPB/001/{{ date('m') }}/RES.0.0/{{ date('Y') }}">
                        @error('nomor_surat')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat">Tanggal Surat Pencabutan<span class="text-danger fs-5">*</span></label>
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
                    <label class="fw-bold col-sm-2 col-form-label" for="dikeluarkan_di">Dikeluarkan di<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="dikeluarkan_di" type="text" class="form-control @error('dikeluarkan_di') is-invalid @enderror" name="dikeluarkan_di"
                            value="{{ old('dikeluarkan_di', $accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat')) }}" required placeholder="Contoh: Jakarta / Surabaya / Medan">
                        @error('dikeluarkan_di')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="satker_penyidik">Satuan Kerja Penyidik<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="satker_penyidik" type="text" class="form-control @error('satker_penyidik') is-invalid @enderror" name="satker_penyidik"
                            value="{{ old('satker_penyidik', 'Satuan Reserse Kriminal Resor ' . ucwords(strtolower($accident->polres->name ?? ''))) }}" required placeholder="Contoh: Satuan Reserse Kriminal Resor ...">
                        @error('satker_penyidik')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_instansi_dasar">Instansi / Pejabat Dasar Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_instansi_dasar" type="text" class="form-control @error('nama_instansi_dasar') is-invalid @enderror" name="nama_instansi_dasar"
                            value="{{ old('nama_instansi_dasar', 'Kepala Kepolisian Resor ' . ucwords(strtolower($accident->polres->name ?? ''))) }}" required placeholder="Contoh: Kepala Kepolisian Resor ...">
                        @error('nama_instansi_dasar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_surat_dasar">Nomor Surat Dasar Permintaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_surat_dasar" type="text" class="form-control @error('nomor_surat_dasar') is-invalid @enderror" name="nomor_surat_dasar"
                            value="{{ old('nomor_surat_dasar', 'B/001/' . date('m') . '/RES.0.0/' . date('Y')) }}" required placeholder="Nomor surat permintaan pencarian dasar">
                        @error('nomor_surat_dasar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_surat_dasar">Tanggal Surat Dasar Permintaan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_surat_dasar" type="text" class="form-control @error('tanggal_surat_dasar') is-invalid @enderror" name="tanggal_surat_dasar"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_surat_dasar', date('Y-m-d')) }}" data-provide="datepicker" required>
                        @error('tanggal_surat_dasar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_dpb">Nomor DPB yang Dicabut<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nomor_dpb" type="text" class="form-control @error('nomor_dpb') is-invalid @enderror" name="nomor_dpb"
                            value="{{ old('nomor_dpb', 'DPB/01/' . date('m') . '/' . date('Y') . '/Reskrim') }}" required placeholder="Contoh: DPB/01/I/2026/Reskrim">
                        @error('nomor_dpb')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tanggal_dpb">Tanggal DPB yang Dicabut<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <input id="tanggal_dpb" type="text" class="form-control @error('tanggal_dpb') is-invalid @enderror" name="tanggal_dpb"
                            placeholder="YYYY-MM-DD" autocomplete="off"
                            value="{{ old('tanggal_dpb', date('Y-m-d')) }}" data-provide="datepicker" required>
                        @error('tanggal_dpb')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- ─── 2. KETERANGAN BARANG YANG DICABUT DARI DPB (12 POIN SPESIFIKASI S-24.6.2) ─── --}}
                <h5 class="fw-bold text-blue-dark mb-3">Data Keterangan Barang Yang Dicabut</h5>

                @if(isset($existingDpbItems) && $existingDpbItems->count() > 0)
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label" for="select_dpb_item">Pilih dari DPB Perkara</label>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                            <select id="select_dpb_item" class="form-select select2">
                                <option value="">-- Pilih Barang dari Catatan DPB Perkara (Isi Otomatis) --</option>
                                @foreach ($existingDpbItems as $dpb)
                                    <option value="{{ $dpb->id }}"
                                        data-jenis="{{ $dpb->jenis ?? '' }}"
                                        data-tnkb="{{ $dpb->no_tnkb ?? '' }}"
                                        data-deskripsi="{{ $dpb->deskripsi ?? '' }}">
                                        {{ $dpb->jenis ?? 'Barang' }} - {{ $dpb->no_tnkb ?? '-' }} ({{ Str::limit($dpb->deskripsi ?? '', 45) }})
                                    </option>
                                @endforeach
                                <option value="manual">-- Input Manual --</option>
                            </select>
                            <small class="text-muted">Pilih barang jika sebelumnya sudah pernah didaftarkan pada Daftar Pencarian Barang (DPB).</small>
                        </div>
                    </div>
                @endif

                {{-- 1. Nama Barang --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_barang">1. Nama Barang<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_barang" type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang"
                            value="{{ old('nama_barang') }}" required placeholder="Contoh: Sepeda Motor / Mobil">
                        @error('nama_barang')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 2. Jumlah Barang --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="jumlah_barang">2. Jumlah Barang<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="jumlah_barang" type="text" class="form-control @error('jumlah_barang') is-invalid @enderror" name="jumlah_barang"
                            value="{{ old('jumlah_barang') }}" required placeholder="Contoh: 1 (satu) unit">
                        @error('jumlah_barang')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 3. Tanda-tanda Khusus --}}
                <div class="card p-3 mb-3 bg-light border">
                    <h6 class="fw-bold text-dark mb-3">3. Tanda-tanda Khusus</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="no_registrasi">a. No. Registrasi / No. Polisi (TNKB)</label>
                            <input type="text" class="form-control" id="no_registrasi" name="no_registrasi"
                                value="{{ old('no_registrasi') }}" placeholder="Contoh: BK 1234 ABC">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="isi_silinder">b. Isi Silinder / Kapasitas Mesin</label>
                            <input type="text" class="form-control" id="isi_silinder" name="isi_silinder"
                                value="{{ old('isi_silinder') }}" placeholder="Contoh: 1500 CC / 125 CC">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="bahan_bakar">c. Bahan Bakar</label>
                            <input type="text" class="form-control" id="bahan_bakar" name="bahan_bakar"
                                value="{{ old('bahan_bakar') }}" placeholder="Contoh: Bensin / Solar / Listrik">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="warna_tnkb">d. Warna TNKB</label>
                            <input type="text" class="form-control" id="warna_tnkb" name="warna_tnkb"
                                value="{{ old('warna_tnkb') }}" placeholder="Contoh: Putih Tulisan Hitam / Hitam">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="tahun_registrasi">e. Tahun Registrasi</label>
                            <input type="text" class="form-control" id="tahun_registrasi" name="tahun_registrasi"
                                value="{{ old('tahun_registrasi') }}" placeholder="Contoh: 2023">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="no_bpkb">f. Nomor BPKB</label>
                            <input type="text" class="form-control" id="no_bpkb" name="no_bpkb"
                                value="{{ old('no_bpkb') }}" placeholder="Contoh: N-12345678-B">
                        </div>
                        <div class="col-12 mb-1">
                            <label class="form-label fw-bold small" for="tanda_khusus_lain">g. Tanda-tanda Khusus Lainnya</label>
                            <textarea class="form-control" id="tanda_khusus_lain" name="tanda_khusus_lain" rows="2"
                                placeholder="Goresan, stiker khusus, variasi aksesoris, cacat fisik barang, dll.">{{ old('tanda_khusus_lain') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 4. Ciri-ciri Barang --}}
                <div class="card p-3 mb-3 bg-light border">
                    <h6 class="fw-bold text-dark mb-3"> 4. Ciri-ciri Barang</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="tipe_barang">a. Tipe Barang</label>
                            <input type="text" class="form-control" id="tipe_barang" name="tipe_barang"
                                value="{{ old('tipe_barang') }}" placeholder="Contoh: Minibus / Sepeda Motor Bebek">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="jenis_barang">b. Jenis Barang</label>
                            <input type="text" class="form-control" id="jenis_barang" name="jenis_barang"
                                value="{{ old('jenis_barang') }}" placeholder="Contoh: Kendaraan Roda 4 / Elektronik">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="model_barang">c. Model Barang</label>
                            <input type="text" class="form-control" id="model_barang" name="model_barang"
                                value="{{ old('model_barang') }}" placeholder="Contoh: All New Avanza 1.5 G / Vario 125">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold small" for="ciri_lain">d. Ciri-ciri Lainnya</label>
                            <input type="text" class="form-control" id="ciri_lain" name="ciri_lain"
                                value="{{ old('ciri_lain') }}" placeholder="Ciri khas fisik lainnya">
                        </div>
                    </div>
                </div>

                {{-- 5. Warna Barang --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="warna_barang">5. Warna Barang<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="warna_barang" type="text" class="form-control @error('warna_barang') is-invalid @enderror" name="warna_barang"
                            value="{{ old('warna_barang') }}" required placeholder="Contoh: Hitam Metalik / Putih / Silver">
                        @error('warna_barang')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 6. Tahun Pembuatan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tahun_pembuatan">6. Tahun Pembuatan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="tahun_pembuatan" type="text" class="form-control @error('tahun_pembuatan') is-invalid @enderror" name="tahun_pembuatan"
                            value="{{ old('tahun_pembuatan') }}" required placeholder="Contoh: 2022">
                        @error('tahun_pembuatan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 7. Taksiran Harga --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="taksiran_harga">7. Taksiran Harga<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold" style="user-select: none;">Rp.</span>
                            <input id="taksiran_harga" type="text" class="form-control @error('taksiran_harga') is-invalid @enderror" name="taksiran_harga"
                                value="{{ old('taksiran_harga') ? preg_replace('/^Rp\.?\s*/i', '', old('taksiran_harga')) : '' }}" required placeholder="150.000.000,-">
                        </div>
                        @error('taksiran_harga')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 8. Merek Dagang --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="merek_dagang">8. Merek Dagang<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="merek_dagang" type="text" class="form-control @error('merek_dagang') is-invalid @enderror" name="merek_dagang"
                            value="{{ old('merek_dagang') }}" required placeholder="Contoh: Toyota / Honda / Yamaha">
                        @error('merek_dagang')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 9. Nomor Pembuatan / Seri / Rangka / Mesin --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nomor_pembuatan">9. Nomor Pembuatan / Seri / Rangka / Mesin<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="nomor_pembuatan" class="form-control @error('nomor_pembuatan') is-invalid @enderror" name="nomor_pembuatan"
                            rows="2" required placeholder="Contoh: No. Rangka: MH3123456789, No. Mesin: JB1234567 / IMEI: 356789012345678">{{ old('nomor_pembuatan') }}</textarea>
                        @error('nomor_pembuatan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 10. Tempat Pembuatan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tempat_pembuatan">10. Tempat Pembuatan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="tempat_pembuatan" type="text" class="form-control @error('tempat_pembuatan') is-invalid @enderror" name="tempat_pembuatan"
                            value="{{ old('tempat_pembuatan') }}" required placeholder="Contoh: Indonesia / Jepang">
                        @error('tempat_pembuatan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 11. Cara Hilangnya --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="cara_hilangnya">11. Cara Hilangnya<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="cara_hilangnya" class="form-control @error('cara_hilangnya') is-invalid @enderror" name="cara_hilangnya"
                            rows="2" required placeholder="Contoh: Dicuri saat terparkir di teras rumah korban dengan cara merusak kunci kontak">{{ old('cara_hilangnya') }}</textarea>
                        @error('cara_hilangnya')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- 12. Keterangan Lain (Alasan Pencabutan) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="keterangan_lain">12. Keterangan Lain (Alasan Pencabutan)<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <textarea id="keterangan_lain" class="form-control @error('keterangan_lain') is-invalid @enderror" name="keterangan_lain"
                            rows="3" required placeholder="Contoh: Barang telah berhasil ditemukan dan telah dilakukan penyitaan sebagai barang bukti guna proses penyidikan lebih lanjut.">{{ old('keterangan_lain', 'Barang telah berhasil ditemukan dan telah dilakukan penyitaan sebagai barang bukti guna kepentingan penyidikan.') }}</textarea>
                        @error('keterangan_lain')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- ─── 3. DISTRIBUSI / KEPADA & TEMBUSAN (Dinamis S-22 / S-23) ─── --}}
                <h5 class="fw-bold text-blue-dark mb-3">Distribusi Surat (Kepada Yth)</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="tembusan">Distribusi Surat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-12">
                        <div id="carbonCopiesContainer">
                            @php
                                $defaultDistribusi = [
                                    'Ketua Pengadilan Negeri ' . ucwords(strtolower($accident->polres->name ?? '')),
                                    'Kepala Kejaksaan Negeri ' . ucwords(strtolower($accident->polres->name ?? '')),
                                    'Penyidik / Pemohon'
                                ];
                                $initialTembusan = old('tembusan', $defaultDistribusi);
                            @endphp
                            @if(!empty($initialTembusan) && is_array($initialTembusan))
                                @foreach($initialTembusan as $index => $cc)
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light fw-bold dist-number">{{ $loop->iteration }}.</span>
                                        <input type="text" class="form-control" name="tembusan[]" value="{{ $cc }}" required placeholder="Tujuan distribusi / tembusan">
                                        <button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button class="btn btn-primary mb-2 addCarbonCopiesButton" type="button"><i class="bi bi-plus-circle me-1"></i> Tambah Tujuan</button>

                        @error('tembusan')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>


                <hr>

                {{-- ─── 4. ATASAN PENYIDIK / PEJABAT PENANDATANGAN ─── --}}
                <h5 class="fw-bold text-blue-dark mb-3">Atasan Penyidik Selaku Penandatangan</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="select_pejabat_penandatangan">Pilih Pejabat</label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <select id="select_pejabat_penandatangan" class="form-select select2">
                            <option value="">-- Pilih Pejabat Penandatangan (Atasan Penyidik) --</option>
                            @if(isset($authorizedSignatories) && $authorizedSignatories->count() > 0)
                                <optgroup label="Pejabat Berwenang">
                                    @foreach ($authorizedSignatories as $signatory)
                                        @php
                                            $sigName = $signatory->full_name ?? ($signatory->name ?? trim(($signatory->first_name ?? '') . ' ' . ($signatory->last_name ?? '')));
                                            $sigRank = $signatory->rank->name ?? ($signatory->rank_id ?? ($signatory->rank ?? ''));
                                            $sigNrp = $signatory->register_number ?? ($signatory->nrp ?? '-');
                                            $sigPos = $signatory->position->name ?? ($signatory->position ?? ($signatory->position_id ?? ''));
                                        @endphp
                                        <option value="{{ $signatory->id }}"
                                            data-name="{{ $sigName }}"
                                            data-rank="{{ $sigRank }}"
                                            data-nrp="{{ $sigNrp }}"
                                            data-position="{{ $sigPos }}">
                                            {{ $sigName }} - {{ $sigRank }} (NRP: {{ $sigNrp }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if(isset($officers) && $officers->count() > 0)
                                <optgroup label="Daftar Petugas">
                                    @foreach ($officers as $officer)
                                        @php
                                            $offName = $officer->full_name ?? trim(($officer->first_name ?? '') . ' ' . ($officer->last_name ?? ''));
                                            $offRank = $officer->rank->name ?? ($officer->rank_id ?? '');
                                            $offNrp = $officer->register_number ?? '-';
                                            $offPos = $officer->position->name ?? ($officer->position ?? ($officer->position_id ?? ''));
                                        @endphp
                                        <option value="{{ $officer->id }}"
                                            data-name="{{ $offName }}"
                                            data-rank="{{ $offRank }}"
                                            data-nrp="{{ $offNrp }}"
                                            data-position="{{ $offPos }}">
                                            {{ $offName }} - {{ $offRank }} (NRP: {{ $offNrp }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <option value="manual">-- Input Manual --</option>
                        </select>
                        <small class="text-muted">Pilih pejabat penandatangan untuk otomatis mengisi nama, pangkat, NRP, dan jabatan.</small>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nama_penandatangan">Nama Pejabat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nama_penandatangan" type="text" class="form-control @error('nama_penandatangan') is-invalid @enderror" name="nama_penandatangan"
                            value="{{ old('nama_penandatangan') }}" placeholder="Nama Pejabat Penandatangan" required>
                        @error('nama_penandatangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="pangkat_penandatangan">Pangkat Pejabat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="pangkat_penandatangan" type="text" class="form-control @error('pangkat_penandatangan') is-invalid @enderror" name="pangkat_penandatangan"
                            value="{{ old('pangkat_penandatangan') }}" placeholder="Contoh: AJUN KOMISARIS BESAR POLISI" required>
                        @error('pangkat_penandatangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="nrp_penandatangan">NRP Pejabat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="nrp_penandatangan" type="text" class="form-control @error('nrp_penandatangan') is-invalid @enderror" name="nrp_penandatangan"
                            value="{{ old('nrp_penandatangan') }}" placeholder="Contoh: 75010123" required>
                        @error('nrp_penandatangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-2 col-form-label" for="jabatan_penandatangan">Jabatan Pejabat<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-12">
                        <input id="jabatan_penandatangan" type="text" class="form-control @error('jabatan_penandatangan') is-invalid @enderror" name="jabatan_penandatangan"
                            value="{{ old('jabatan_penandatangan') }}" placeholder="Contoh: a.n. KEPALA KEPOLISIAN RESOR ... KASAT RESKRIM" required>
                        @error('jabatan_penandatangan')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="text-center my-4">
                    <button type="submit" class="btn btn-dark-blue me-2" id="btnSubmitDpb">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger">
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
            // Animasi Attention Box
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            // Inisialisasi Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Inisialisasi Datepicker
            $('[data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            $('[data-provide="datepicker"]').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            // Helper validasi frontend
            function hasFieldValue($field) {
                if (!$field || $field.length === 0) return true;
                var val = $field.val();
                if (typeof val === 'string') return val.trim().length > 0;
                if (Array.isArray(val)) return val.length > 0;
                return val !== null && val !== undefined && val !== '';
            }

            function clearFieldError(selector) {
                var $el = $(selector);
                $el.removeClass('is-invalid');
                $el.next('.invalid-feedback.frontend-error').remove();
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.next('.select2-container').find('.select2-selection').removeClass('border border-danger is-invalid');
                }
            }

            function showFieldError(selector, message) {
                var $el = $(selector);
                if ($el.length === 0) return;
                $el.addClass('is-invalid');
                if ($el.next('.invalid-feedback.frontend-error').length === 0) {
                    $el.after('<span class="invalid-feedback frontend-error d-block" role="alert"><strong>' + message + '</strong></span>');
                }
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.next('.select2-container').find('.select2-selection').addClass('border border-danger is-invalid');
                }
            }

            function checkInput(selector, label, errors) {
                var $el = $(selector);
                if ($el.length === 0) return;
                if (!hasFieldValue($el)) {
                    showFieldError(selector, label + ' wajib diisi.');
                    errors.push(label);
                } else {
                    clearFieldError(selector);
                }
            }

            function scrollToFirstError() {
                var $first = $('.is-invalid, .select2-selection.border-danger').first();
                if ($first.length > 0) {
                    $('html, body').animate({
                        scrollTop: Math.max(0, $first.offset().top - 120)
                    }, 400, function() {
                        if ($first.is('input, textarea, select')) {
                            $first.focus();
                        }
                    });
                }
            }

            // Real-time input cleaner
            $('input, textarea').on('input change', function() {
                var $el = $(this);
                if (hasFieldValue($el)) {
                    clearFieldError($el);
                }
            });

            $('select').on('change select2:select', function() {
                var $el = $(this);
                if (hasFieldValue($el)) {
                    clearFieldError($el);
                }
            });

            // Continuous watcher untuk input yang diupdate oleh plugin popover / datepicker
            setInterval(function() {
                $('input.is-invalid, textarea.is-invalid, select.is-invalid').each(function() {
                    var $field = $(this);
                    if (hasFieldValue($field)) {
                        clearFieldError($field);
                    }
                });
            }, 200);

            // Auto-Fill Barang dari DPB Perkara
            $('#select_dpb_item').on('change select2:select', function() {
                var $opt = $(this).find('option:selected');
                var val = $(this).val();

                if (val && val !== 'manual') {
                    var jenis = $opt.data('jenis') || '';
                    var tnkb = $opt.data('tnkb') || '';
                    var deskripsi = $opt.data('deskripsi') || '';

                    if (jenis) {
                        $('#nama_barang').val(jenis);
                        $('#jenis_barang').val(jenis);
                    }
                    if (tnkb) {
                        $('#no_registrasi').val(tnkb);
                    }
                    if (deskripsi) {
                        $('#tanda_khusus_lain').val(deskripsi);
                    }
                    clearFieldError('#nama_barang');
                } else if (val === 'manual') {
                    $('#nama_barang').val('');
                    $('#no_registrasi').val('');
                    $('#tanda_khusus_lain').val('');
                }
            });

            // Auto-Fill Pejabat Penandatangan saat opsi dipilih
            $('#select_pejabat_penandatangan').on('change select2:select', function() {
                var $opt = $(this).find('option:selected');
                var val = $(this).val();

                if (val && val !== 'manual') {
                    $('#nama_penandatangan').val($opt.data('name') || '');
                    $('#pangkat_penandatangan').val($opt.data('rank') || '');
                    $('#nrp_penandatangan').val($opt.data('nrp') || '');
                    var pos = $opt.data('position');
                    if (pos) {
                        $('#jabatan_penandatangan').val(pos);
                    }
                    clearFieldError('#nama_penandatangan');
                    clearFieldError('#pangkat_penandatangan');
                    clearFieldError('#nrp_penandatangan');
                    clearFieldError('#jabatan_penandatangan');
                } else if (val === 'manual') {
                    $('#nama_penandatangan').val('');
                    $('#pangkat_penandatangan').val('');
                    $('#nrp_penandatangan').val('');
                    $('#jabatan_penandatangan').val('');
                }
            });

            // Taksiran Harga: pastikan input tetap bersih dari 'Rp.' ganda jika user mengetik/paste
            $('#taksiran_harga').on('input', function() {
                var val = $(this).val();
                if (/^Rp\.?\s*/i.test(val)) {
                    $(this).val(val.replace(/^Rp\.?\s*/i, ''));
                }
            });

            // Distribusi Surat Dinamis (Tembusan)
            function updateDistNumbers() {
                $('#carbonCopiesContainer .dist-number').each(function(index) {
                    $(this).text((index + 1) + '.');
                });
            }

            $('.addCarbonCopiesButton').on('click', function() {
                var count = $('#carbonCopiesContainer .input-group').length + 1;
                var newRow = $(
                    '<div class="input-group mb-2">' +
                    '  <span class="input-group-text bg-light fw-bold dist-number">' + count + '.</span>' +
                    '  <input type="text" class="form-control" name="tembusan[]" value="" required placeholder="Tujuan distribusi / tembusan">' +
                    '  <button class="btn btn-outline-danger removeCarbonCopiesButton" type="button">Hapus</button>' +
                    '</div>'
                );
                $('#carbonCopiesContainer').append(newRow);
                updateDistNumbers();
            });

            $(document).on('click', '.removeCarbonCopiesButton', function() {
                if ($('#carbonCopiesContainer .input-group').length > 1) {
                    $(this).closest('.input-group').remove();
                    updateDistNumbers();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Minimal harus ada 1 tujuan distribusi surat.'
                        });
                    } else {
                        alert('Minimal harus ada 1 tujuan distribusi surat.');
                    }
                }
            });

            // Validasi Submit Form
            $('#formSuratPencabutanDpb').on('submit', function(e) {
                e.preventDefault();

                var $btn = $('#btnSubmitDpb');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memvalidasi...');

                // Bersihkan pesan error sebelumnya
                $('.is-invalid').removeClass('is-invalid');
                $('.border.border-danger').removeClass('border border-danger');
                $('.select2-selection').removeClass('border border-danger is-invalid');
                $('.frontend-error').remove();
                $('.invalid-feedback').remove();

                var errors = [];

                // 1. Informasi Dasar Dokumen & Dasar Pencabutan
                checkInput('#nomor_lp', 'Nomor LP', errors);
                checkInput('#nomor_surat', 'Nomor Surat Pencabutan', errors);
                checkInput('#tanggal_surat', 'Tanggal Surat Pencabutan', errors);
                checkInput('#dikeluarkan_di', 'Dikeluarkan di', errors);
                checkInput('#satker_penyidik', 'Satuan Kerja Penyidik', errors);
                checkInput('#nama_instansi_dasar', 'Instansi / Pejabat Dasar Surat', errors);
                checkInput('#nomor_surat_dasar', 'Nomor Surat Dasar Permintaan', errors);
                checkInput('#tanggal_surat_dasar', 'Tanggal Surat Dasar Permintaan', errors);
                checkInput('#nomor_dpb', 'Nomor DPB yang Dicabut', errors);
                checkInput('#tanggal_dpb', 'Tanggal DPB yang Dicabut', errors);

                // 2. Data Barang (12 Poin)
                checkInput('#nama_barang', 'Nama Barang', errors);
                checkInput('#jumlah_barang', 'Jumlah Barang', errors);
                checkInput('#warna_barang', 'Warna Barang', errors);
                checkInput('#tahun_pembuatan', 'Tahun Pembuatan', errors);
                checkInput('#taksiran_harga', 'Taksiran Harga', errors);
                checkInput('#merek_dagang', 'Merek Dagang', errors);
                checkInput('#nomor_pembuatan', 'Nomor Pembuatan / Seri / Mesin', errors);
                checkInput('#tempat_pembuatan', 'Tempat Pembuatan', errors);
                checkInput('#cara_hilangnya', 'Cara Hilangnya', errors);
                checkInput('#keterangan_lain', 'Keterangan Lain / Alasan Pencabutan', errors);

                // 3. Distribusi / Kepada Yth (Dinamis)
                var tembusanInputs = $('input[name="tembusan[]"]');
                if (tembusanInputs.length === 0) {
                    errors.push('Distribusi Surat minimal harus ada 1 tujuan.');
                } else {
                    tembusanInputs.each(function(idx) {
                        var val = $(this).val();
                        if (!val || val.trim() === '') {
                            $(this).addClass('is-invalid');
                            errors.push('Tujuan Distribusi ke-' + (idx + 1) + ' wajib diisi.');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });
                }

                // 4. Atasan Penyidik
                checkInput('#nama_penandatangan', 'Nama Pejabat Penandatangan', errors);
                checkInput('#pangkat_penandatangan', 'Pangkat Pejabat', errors);
                checkInput('#nrp_penandatangan', 'NRP Pejabat', errors);
                checkInput('#jabatan_penandatangan', 'Jabatan Pejabat', errors);

                // Jika ada field kosong di frontend, tampilkan SweetAlert lalu auto-scroll & fokus ke field pertama
                if (errors.length > 0) {
                    $btn.prop('disabled', false).html(originalHtml);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Formulir Belum Lengkap',
                            text: 'Terdapat ' + errors.length + ' bidang yang wajib diisi dan belum lengkap. Mohon periksa kembali isian form Anda.',
                            confirmButtonText: 'Periksa Isian'
                        }).then(function() {
                            scrollToFirstError();
                        });
                    }
                    scrollToFirstError();
                    return false;
                }

                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                this.submit();

                setTimeout(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }, 4000);
            });
        });
    </script>
@endpush
