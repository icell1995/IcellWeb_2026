@php
    $_title = 'Ubah Surat Daftar Pencarian Orang (DPO)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .card-header.bg-light {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #ebedf2;
            padding: 12px 20px;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .section-subhead {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .btn-dark-blue {
            background-color: rgb(34, 37, 119) !important;
            border-color: rgb(34, 37, 119) !important;
            color: #ffffff !important;
        }
        .btn-dark-blue:hover {
            background-color: rgb(39, 43, 139) !important;
            border-color: rgb(39, 43, 139) !important;
            color: #ffffff !important;
        }
    </style>
@endpush

@section('content')

    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold m-0">Ubah Surat Daftar Pencarian Orang (DPO)</h5>

            <div class="alert alert-danger mt-3" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        SELURUH DATA WAJIB DIISI LENGKAP KARENA MERUPAKAN BAGIAN DARI BERKAS ADMINISTRASI PENYIDIKAN (MINDIK)
                        DALAM SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).
                    </b>
                </div>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="card-body p-0 mb-3 mt-3">
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
                <div class="card-body p-0 mb-3 mt-3">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-daftar-pencarian-orang-document.update', ['id' => $doc->id]) }}" method="POST" id="formDpo" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                {{-- ─── I. IDENTITAS SURAT & KEPOLISIAN ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">I. Identitas Surat & Kesatuan Kepolisian<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kopstuk_1">Kop Baris 1<span class="text-danger">*</span></label>
                                <input type="text" name="kopstuk_1" id="kopstuk_1" class="form-control"
                                    value="{{ old('kopstuk_1', $doc->kopstuk_1 ?: 'KEPOLISIAN NEGARA REPUBLIK INDONESIA') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kopstuk_2">Kop Baris 2 (Polda)<span class="text-danger">*</span></label>
                                <input type="text" name="kopstuk_2" id="kopstuk_2" class="form-control"
                                    value="{{ old('kopstuk_2', $doc->kopstuk_2) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kopstuk_3">Kop Baris 3 (Kesatuan / Polres)<span class="text-danger">*</span></label>
                                <input type="text" name="kopstuk_3" id="kopstuk_3" class="form-control"
                                    value="{{ old('kopstuk_3', $doc->kopstuk_3) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="kopstuk_4">Kop Baris 4 (Alamat & Kode Pos)<span class="text-danger">*</span></label>
                                <input type="text" name="kopstuk_4" id="kopstuk_4" class="form-control"
                                    value="{{ old('kopstuk_4', $doc->kopstuk_4) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="nomor_surat">Nomor Surat DPO<span class="text-danger">*</span></label>
                                <input type="text" name="nomor_surat" id="nomor_surat" class="form-control"
                                    value="{{ old('nomor_surat', $doc->nomor_surat) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="tanggal_surat">Tanggal Surat DPO<span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_surat" id="tanggal_surat" class="form-control"
                                    value="{{ old('tanggal_surat', $doc->tanggal_surat ? \Carbon\Carbon::parse($doc->tanggal_surat)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="dikeluarkan_di">Dikeluarkan di (Kota)<span class="text-danger">*</span></label>
                                <input type="text" name="dikeluarkan_di" id="dikeluarkan_di" class="form-control"
                                    value="{{ old('dikeluarkan_di', $doc->dikeluarkan_di ?: 'Muaradua') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── II. KONTAK PENYIDIK & RUJUKAN PERMINTAAN ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">II. Kontak Petugas & Rujukan Surat Permintaan<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nomor_hp_penyidik">Nomor HP Penyidik / Kantor Kepolisian<span class="text-danger">*</span></label>
                                <input type="text" name="nomor_hp_penyidik" id="nomor_hp_penyidik" class="form-control"
                                    value="{{ old('nomor_hp_penyidik', $doc->nomor_hp_penyidik ?: '+6280000000000') }}" required>
                            </div>
                        </div>

                        <div class="section-subhead">Surat Permintaan dari Instansi / Satuan Lain (Bila Tidak Ada Isi "-")<span class="text-danger">*</span></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="rujukan_instansi">Surat Permintaan Dari<span class="text-danger">*</span></label>
                                <textarea name="rujukan_instansi" id="rujukan_instansi" class="form-control" rows="2" required>{{ old('rujukan_instansi', $doc->rujukan_instansi ?: '-') }}</textarea>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="rujukan_nomor">Nomor Surat Permintaan<span class="text-danger">*</span></label>
                                <input type="text" name="rujukan_nomor" id="rujukan_nomor" class="form-control"
                                    value="{{ old('rujukan_nomor', $doc->rujukan_nomor ?: '-') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="rujukan_tanggal">Tanggal Surat Permintaan<span class="text-danger">*</span></label>
                                <input type="date" name="rujukan_tanggal" id="rujukan_tanggal" class="form-control"
                                    value="{{ old('rujukan_tanggal', $doc->rujukan_tanggal ? \Carbon\Carbon::parse($doc->rujukan_tanggal)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── III. IDENTITAS LENGKAP TERSANGKA & PASFOTO 4X6 ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">III. Identitas Lengkap Tersangka (Poin 1 s/d 8) & Pasfoto 4x6<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="tersangka_id" id="tersangka_id" value="{{ old('tersangka_id', $doc->tersangka_id) }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nama_tersangka">1. Nama Tersangka<span class="text-danger">*</span></label>
                                <input type="text" name="nama_tersangka" id="nama_tersangka" class="form-control"
                                    value="{{ old('nama_tersangka', $doc->tersangka_nama) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="jenis_identitas">Jenis Identitas<span class="text-danger">*</span></label>
                                <select name="jenis_identitas" id="jenis_identitas" class="form-select" required>
                                    <option value="KTP" {{ old('jenis_identitas', $doc->tersangka_jenis_identitas) == 'KTP' ? 'selected' : '' }}>KTP</option>
                                    <option value="SIM" {{ old('jenis_identitas', $doc->tersangka_jenis_identitas) == 'SIM' ? 'selected' : '' }}>SIM</option>
                                    <option value="Paspor" {{ old('jenis_identitas', $doc->tersangka_jenis_identitas) == 'Paspor' ? 'selected' : '' }}>Paspor</option>
                                    <option value="Lainnya" {{ old('jenis_identitas', $doc->tersangka_jenis_identitas) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="nomor_identitas">2. Nomor Identitas<span class="text-danger">*</span></label>
                                <input type="text" name="nomor_identitas" id="nomor_identitas" class="form-control"
                                    value="{{ old('nomor_identitas', $doc->tersangka_nomor_identitas) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="kewarganegaraan">3. Kewarganegaraan<span class="text-danger">*</span></label>
                                <input type="text" name="kewarganegaraan" id="kewarganegaraan" class="form-control"
                                    value="{{ old('kewarganegaraan', $doc->tersangka_kewarganegaraan ?: 'Indonesia') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="jenis_kelamin">4. Jenis Kelamin<span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $doc->tersangka_jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $doc->tersangka_jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="pekerjaan">6. Pekerjaan Terakhir/Dahulu<span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control"
                                    value="{{ old('pekerjaan', $doc->tersangka_pekerjaan ?: '-') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="tempat_lahir">5. Tempat Lahir<span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                                    value="{{ old('tempat_lahir', $doc->tersangka_tempat_lahir ?: 'Muaradua') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="tanggal_lahir">Tanggal Lahir<span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                    value="{{ old('tanggal_lahir', $doc->tersangka_tanggal_lahir ? \Carbon\Carbon::parse($doc->tersangka_tanggal_lahir)->format('Y-m-d') : '1995-01-01') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label" for="alamat">7. Alamat Tempat Tinggal<span class="text-danger">*</span></label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="2" required>{{ old('alamat', $doc->tersangka_alamat) }}</textarea>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label" for="kontak_tersangka">8. No. (Telp./Hp.) / Email / Medsos<span class="text-danger">*</span></label>
                                <textarea name="kontak_tersangka" id="kontak_tersangka" class="form-control" rows="2" required>{{ old('kontak_tersangka', $doc->tersangka_kontak ?: '-') }}</textarea>
                            </div>
                        </div>

                        @php
                            $hasExistingPhoto = $doc->foto_tersangka_path && file_exists(public_path($doc->foto_tersangka_path));
                            $currentPhotoUrl = $hasExistingPhoto ? asset($doc->foto_tersangka_path) : '#';
                        @endphp
                        <div class="row align-items-start">
                            <div class="col-md-7 mb-3">
                                <label class="form-label" for="foto_tersangka">Pasfoto Tersangka (Ukuran 4 X 6)<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="file" name="foto_tersangka" id="foto_tersangka" class="form-control" accept="image/*"
                                        {{ !$hasExistingPhoto ? 'required' : '' }}>
                                    <button type="button" class="btn {{ $hasExistingPhoto ? 'btn-primary' : 'btn-outline-secondary' }}" id="btnPreviewFoto">
                                        <i class="bi bi-eye me-1"></i> Preview Foto
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    {{ $hasExistingPhoto ? 'Biarkan kosong bila tidak ingin mengganti foto yang sudah ada.' : 'Format: JPG, JPEG, PNG (Maksimal 5MB). Pasfoto akan dipasang di kotak foto template DPO.' }}
                                </small>
                            </div>
                            <div class="col-md-5 mb-3" id="wrapperInlinePreview" style="{{ !$hasExistingPhoto ? 'display: none;' : '' }}">
                                <label class="form-label d-block">Preview Pasfoto (4 x 6):</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="imgInlinePreview" src="{{ $currentPhotoUrl }}" alt="Preview Pasfoto"
                                        style="width: 90px; height: 125px; object-fit: cover; border: 2px solid #0d6efd; border-radius: 4px; padding: 2px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-open-modal-preview">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> Ukuran Penuh
                                        </button>
                                        <div class="text-muted small mt-1" id="inlineFileName">{{ $hasExistingPhoto ? 'Foto saat ini tersimpan' : '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── IV. CATATAN HUKUMAN & CIRI-CIRI KHUSUS FISIK ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">IV. Catatan Hukuman, Daktiloskopi, & Ciri-Ciri Khusus Fisik (Poin 9 s/d 11)<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="section-subhead mt-0">9. Catatan Pernah Dihukum dan Sebagainya<span class="text-danger">*</span></div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="pernah_dihukum">a. Status Hukuman<span class="text-danger">*</span></label>
                                <select name="pernah_dihukum" id="pernah_dihukum" class="form-select" required>
                                    <option value="Tidak Pernah" {{ old('pernah_dihukum', $doc->pernah_dihukum) == 'Tidak Pernah' ? 'selected' : '' }}>Tidak Pernah</option>
                                    <option value="Pernah" {{ old('pernah_dihukum', $doc->pernah_dihukum) == 'Pernah' ? 'selected' : '' }}>Pernah</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="hukuman_tempat">b. Tempat Menjalani Hukuman<span class="text-danger">*</span></label>
                                <input type="text" name="hukuman_tempat" id="hukuman_tempat" class="form-control"
                                    value="{{ old('hukuman_tempat', $doc->hukuman_tempat ?: '-') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="hukuman_lama">c. Lama Masa Hukuman<span class="text-danger">*</span></label>
                                <input type="text" name="hukuman_lama" id="hukuman_lama" class="form-control"
                                    value="{{ old('hukuman_lama', $doc->hukuman_lama ?: '-') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="hukuman_keterangan">d. Keterangan Terkait Hukuman<span class="text-danger">*</span></label>
                                <input type="text" name="hukuman_keterangan" id="hukuman_keterangan" class="form-control"
                                    value="{{ old('hukuman_keterangan', $doc->hukuman_keterangan ?: '-') }}" required>
                            </div>
                        </div>

                        <div class="section-subhead">10. Rumus Dactiloscopy<span class="text-danger">*</span></div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="rumus_daktiloskopi">Rumus Dactiloscopy / No. Reg. Dactiloscopy<span class="text-danger">*</span></label>
                                <input type="text" name="rumus_daktiloskopi" id="rumus_daktiloskopi" class="form-control"
                                    value="{{ old('rumus_daktiloskopi', $doc->rumus_daktiloskopi ?: '-') }}" required>
                            </div>
                        </div>

                        <div class="section-subhead">11. Ciri-Ciri Khusus Fisik<span class="text-danger">*</span></div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="tinggi_badan">a. Tinggi Badan (cm)<span class="text-danger">*</span></label>
                                <input type="text" name="tinggi_badan" id="tinggi_badan" class="form-control"
                                    value="{{ old('tinggi_badan', $doc->tinggi_badan ?: '165') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="berat_badan">Berat Badan (kg)<span class="text-danger">*</span></label>
                                <input type="text" name="berat_badan" id="berat_badan" class="form-control"
                                    value="{{ old('berat_badan', $doc->berat_badan ?: '60') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="rambut">b. Rambut<span class="text-danger">*</span></label>
                                <input type="text" name="rambut" id="rambut" class="form-control"
                                    value="{{ old('rambut', $doc->rambut ?: 'Hitam pendek lurus') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="bentuk_tubuh">c. Bentuk Tubuh<span class="text-danger">*</span></label>
                                <input type="text" name="bentuk_tubuh" id="bentuk_tubuh" class="form-control"
                                    value="{{ old('bentuk_tubuh', $doc->bentuk_tubuh ?: 'Sedang') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="warna_kulit">d. Warna Kulit<span class="text-danger">*</span></label>
                                <input type="text" name="warna_kulit" id="warna_kulit" class="form-control"
                                    value="{{ old('warna_kulit', $doc->warna_kulit ?: 'Sawo Matang') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="mata">e. Mata<span class="text-danger">*</span></label>
                                <input type="text" name="mata" id="mata" class="form-control"
                                    value="{{ old('mata', $doc->mata ?: 'Bulat hitam') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="hidung">f. Hidung<span class="text-danger">*</span></label>
                                <input type="text" name="hidung" id="hidung" class="form-control"
                                    value="{{ old('hidung', $doc->hidung ?: 'Mancung / Sedang') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="bibir">g. Bibir<span class="text-danger">*</span></label>
                                <input type="text" name="bibir" id="bibir" class="form-control"
                                    value="{{ old('bibir', $doc->bibir ?: 'Sedang') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="ciri_khusus_lainnya">h. Ciri-Ciri Khusus Lainnya<span class="text-danger">*</span></label>
                                <textarea name="ciri_khusus_lainnya" id="ciri_khusus_lainnya" class="form-control" rows="2" required>{{ old('ciri_khusus_lainnya', $doc->ciri_khusus_lainnya ?: '-') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── V. URAIAN TINDAK PIDANA & LOKASI KEJADIAN ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">V. Uraian Tindak Pidana & Lokasi Kejadian (Poin 12 s/d 15)<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="uraian_tindak_pidana">12. Uraian Tindak Pidana yang Diduga Dilakukan<span class="text-danger">*</span></label>
                            <textarea name="uraian_tindak_pidana" id="uraian_tindak_pidana" class="form-control" rows="3" required>{{ old('uraian_tindak_pidana', $doc->uraian_tindak_pidana) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="pasal_pidana">13. Melanggar Pasal<span class="text-danger">*</span></label>
                                <input type="text" name="pasal_pidana" id="pasal_pidana" class="form-control"
                                    value="{{ old('pasal_pidana', $doc->pasal_pidana) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for="uu_pidana_nomor">UU Nomor<span class="text-danger">*</span></label>
                                <input type="text" name="uu_pidana_nomor" id="uu_pidana_nomor" class="form-control"
                                    value="{{ old('uu_pidana_nomor', $doc->uu_pidana_nomor) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label" for="uu_pidana_tahun">Tahun UU<span class="text-danger">*</span></label>
                                <input type="text" name="uu_pidana_tahun" id="uu_pidana_tahun" class="form-control"
                                    value="{{ old('uu_pidana_tahun', $doc->uu_pidana_tahun) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="uu_pidana_tentang">Tentang UU<span class="text-danger">*</span></label>
                                <input type="text" name="uu_pidana_tentang" id="uu_pidana_tentang" class="form-control"
                                    value="{{ old('uu_pidana_tentang', $doc->uu_pidana_tentang) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="tkp">Tempat Kejadian Perkara (TKP)<span class="text-danger">*</span></label>
                                <input type="text" name="tkp" id="tkp" class="form-control"
                                    value="{{ old('tkp', $doc->tkp) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="waktu_kejadian">Waktu Kejadian (Hari, Tanggal / Kurun Waktu)<span class="text-danger">*</span></label>
                                <input type="text" name="waktu_kejadian" id="waktu_kejadian" class="form-control"
                                    value="{{ old('waktu_kejadian', $doc->waktu_kejadian) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="no_reg_kejahatan">14. No. Reg. Kejahatan / Pelanggaran (No LP)<span class="text-danger">*</span></label>
                                <input type="text" name="no_reg_kejahatan" id="no_reg_kejahatan" class="form-control"
                                    value="{{ old('no_reg_kejahatan', $doc->no_reg_kejahatan) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="keterangan_lain_penting">15. Keterangan Lain yang Penting<span class="text-danger">*</span></label>
                                <input type="text" name="keterangan_lain_penting" id="keterangan_lain_penting" class="form-control"
                                    value="{{ old('keterangan_lain_penting', $doc->keterangan_lain_penting ?: 'Apabila melihat atau menemukan segera hubungi kantor polisi terdekat.') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── VI. DISTRIBUSI & ATASAN PENYIDIK ─── --}}
                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">VI. Distribusi Surat & Penandatangan Atasan Penyidik<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Kiri: Distribusi / Kepada --}}
                            <div class="col-md-6 pe-md-4 border-end">
                                <div class="section-subhead mt-0 mb-3 text-center">Kepada (Distribusi Surat)<span class="text-danger">*</span></div>
                                <div class="mb-3">
                                    <label class="form-label">Daftar Penerima Surat:</label>
                                    <p class="text-muted small mb-1">1. Direskrimum/Sus/Narkoba jajaran.</p>
                                    <p class="text-muted small mb-2">2. Kapolres jajaran.</p>
                                    <label class="form-label" for="distribusi_kepada">3. Penerima Tambahan / Penyidik:<span class="text-danger">*</span></label>
                                    <input type="text" name="distribusi_kepada" id="distribusi_kepada" class="form-control"
                                        value="{{ old('distribusi_kepada', $doc->distribusi_kepada ?: 'Kasat Lantas / Penyidik Pembantu.') }}" required>
                                </div>
                            </div>

                            {{-- Kanan: ATASAN PENYIDIK --}}
                            <div class="col-md-6 ps-md-4">
                                <div class="section-subhead mt-0 mb-3 text-center">ATASAN PENYIDIK<span class="text-danger">*</span></div>

                                @if ($authorizedSignatories->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label" for="selectSignatory">Pilih Pejabat Penandatangan</label>
                                        <select class="form-select" id="selectSignatory">
                                            <option value="">-- Pilih Pejabat Penandatangan --</option>
                                            @foreach ($authorizedSignatories as $sig)
                                                @php
                                                    $posSig = is_object($sig->position) ? ($sig->position->name ?? 'ATASAN PENYIDIK') : (is_array($sig->position) ? ($sig->position['name'] ?? 'ATASAN PENYIDIK') : ($sig->position ?? 'ATASAN PENYIDIK'));
                                                    if (is_string($posSig) && str_starts_with(trim($posSig), '{')) {
                                                        $dec = json_decode(trim($posSig), true);
                                                        $posSig = $dec['name'] ?? $posSig;
                                                    }
                                                    $sigNrp = $sig->nrp ?: ($sig->register_number ?: ($sig->id ?: ''));
                                                @endphp
                                                <option value="{{ $sig->id }}"
                                                    data-nama="{{ $sig->full_name ?? ($sig->name ?? '') }}"
                                                    data-pangkat="{{ $sig->rank->name ?? ($sig->rank_name ?? '') }}"
                                                    data-nrp="{{ $sigNrp }}"
                                                    data-jabatan="{{ $posSig }}">
                                                    {{ $sig->full_name ?? ($sig->name ?? '') }} ({{ $sigNrp ?: '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label" for="atasan_nama">Nama Atasan Penyidik<span class="text-danger">*</span></label>
                                    <input type="text" name="atasan_nama" id="atasan_nama" class="form-control"
                                        value="{{ old('atasan_nama', $doc->atasan_nama) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="atasan_pangkat">Pangkat<span class="text-danger">*</span></label>
                                    <input type="text" name="atasan_pangkat" id="atasan_pangkat" class="form-control"
                                        value="{{ old('atasan_pangkat', $doc->atasan_pangkat) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="atasan_nrp">NRP<span class="text-danger">*</span></label>
                                    <input type="text" name="atasan_nrp" id="atasan_nrp" class="form-control"
                                        value="{{ old('atasan_nrp', $doc->atasan_nrp ?: ($authorizedSignatories->first()->nrp ?? '')) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="atasan_jabatan">Jabatan<span class="text-danger">*</span></label>
                                    <input type="text" name="atasan_jabatan" id="atasan_jabatan" class="form-control"
                                        value="{{ old('atasan_jabatan', $doc->atasan_jabatan) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── ACTION BUTTONS ─── --}}
                <div class="text-end mb-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" name="action" value="save" class="btn btn-dark-blue px-4 py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Preview Pasfoto -->
    <div class="modal fade" id="modalPreviewFoto" tabindex="-1" aria-labelledby="modalPreviewFotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="modalPreviewFotoLabel">
                        <i class="bi bi-person-bounding-box me-1"></i> Preview Pasfoto Tersangka
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4 bg-light">
                    <img id="imgModalPreview" src="{{ $currentPhotoUrl }}" alt="Pasfoto Tersangka"
                        class="img-fluid rounded shadow-sm" style="max-height: 420px; border: 1px solid #dee2e6;">
                    <div class="mt-2 text-muted small fw-semibold" id="previewFileName">
                        {{ $hasExistingPhoto ? 'Foto tersimpan saat ini' : '' }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var existingPhotoUrl = '{{ $hasExistingPhoto ? $currentPhotoUrl : "" }}';

            function showPhotoModal() {
                var modalEl = document.getElementById('modalPreviewFoto');
                if (window.bootstrap && bootstrap.Modal) {
                    var m = bootstrap.Modal.getOrCreateInstance(modalEl);
                    m.show();
                } else if (typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('show');
                }
            }

            // Click listener on Preview button
            $('#btnPreviewFoto, .btn-open-modal-preview').on('click', function(e) {
                e.preventDefault();
                var fileInput = document.getElementById('foto_tersangka');
                var hasFile = fileInput && fileInput.files && fileInput.files[0];
                var modalImg = document.getElementById('imgModalPreview');
                var hasSrc = modalImg && modalImg.getAttribute('src') && modalImg.getAttribute('src') !== '#' && modalImg.getAttribute('src') !== '';

                if (!hasFile && !hasSrc) {
                    alert('Silakan pilih pasfoto tersangka terlebih dahulu.');
                    $('#foto_tersangka').focus().click();
                    return;
                }

                showPhotoModal();
            });

            // File input preview foto
            $('#foto_tersangka').on('change', function() {
                var file = this.files && this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        $('#imgInlinePreview').attr('src', evt.target.result);
                        $('#imgModalPreview').attr('src', evt.target.result);
                        var fileInfo = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                        $('#previewFileName').text(fileInfo);
                        $('#inlineFileName').text(fileInfo);
                        $('#wrapperInlinePreview').slideDown();
                        $('#btnPreviewFoto')
                            .removeClass('btn-outline-secondary')
                            .addClass('btn-primary');
                    };
                    reader.readAsDataURL(file);
                } else {
                    if (existingPhotoUrl) {
                        $('#imgInlinePreview').attr('src', existingPhotoUrl);
                        $('#imgModalPreview').attr('src', existingPhotoUrl);
                        $('#previewFileName').text('Foto tersimpan saat ini');
                        $('#inlineFileName').text('Foto saat ini tersimpan');
                        $('#wrapperInlinePreview').slideDown();
                        $('#btnPreviewFoto')
                            .removeClass('btn-outline-secondary')
                            .addClass('btn-primary');
                    } else {
                        $('#wrapperInlinePreview').slideUp();
                        $('#btnPreviewFoto')
                            .removeClass('btn-primary')
                            .addClass('btn-outline-secondary');
                    }
                }
            });

            // Check if file is already populated on ready
            var existingInput = document.getElementById('foto_tersangka');
            if (existingInput && existingInput.files && existingInput.files[0]) {
                $('#foto_tersangka').trigger('change');
            }

            // Signatory dropdown auto fill
            $('#selectSignatory').on('change', function() {
                var selected = $(this).find(':selected');
                if (selected.val()) {
                    $('#atasan_nama').val(selected.data('nama') || '');
                    $('#atasan_pangkat').val(selected.data('pangkat') || '');
                    $('#atasan_nrp').val(selected.data('nrp') || '');
                    $('#atasan_jabatan').val(selected.data('jabatan') || 'ATASAN PENYIDIK');
                }
            });
        });
    </script>
@endpush
