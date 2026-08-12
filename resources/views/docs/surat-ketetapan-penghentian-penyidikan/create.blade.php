@php
    $_title = 'S.Ket Penghentian Penyidikan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i class="bi bi-arrow-left"></i>
        Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Ketetapan Penghentian Penyidikan</h5>

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
            <form action="{{ route('doc.surat-ketetapan-penghentian-penyidikan-document.store', ['accident_id' => $accidentId]) }}"
                method="POST" enctype="multipart/form-data" id="suratKetetapanPenghentianPenyidikanForm">
                @csrf
                
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                {{-- Nomor LP --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="no_lp">Nomor LP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="no_lp" type="text"
                            class="form-control @error('no_lp') is-invalid @enderror"
                            name="no_lp" value="{{ $accident->no_lp }}" required
                            placeholder="LP/001/I/RES.0.0.1/2026/Satker" readonly>

                        @error('no_lp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Nomor Dokumen --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_number">Nomor Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="document_number" type="text"
                            class="form-control @error('document_number') is-invalid @enderror"
                            name="document_number" value="{{ old('document_number') }}" required
                            placeholder="S.Tap/Pen/Dik/KR/001/I/RES.0.0.1/2026/Satker">

                        @error('document_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Surat --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_date">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('document_date') is-invalid @enderror" id="document_date" name="document_date"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('document_date') }}"
                            data-provide="datepicker" required>

                        @error('document_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Terhitung Mulai Tanggal --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="effective_date">Terhitung Mulai Tanggal<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('effective_date') }}"
                            data-provide="datepicker" required>

                        @error('effective_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Surat Perintah Penyidikan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_id">Nomor Surat Perintah Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_perintah_penyidikan_id" id="surat_perintah_penyidikan_id">
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $sp)
                                <option value="{{ $sp->id }}" {{ old('surat_perintah_penyidikan_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->document_number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Surat Perintah Penyidikan yang terkait</small>

                        @error('surat_perintah_penyidikan_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Laporan Hasil Gelar Perkara --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="laporan_hasil_gelar_perkara_id">Tanggal Gelar Perkara<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="laporan_hasil_gelar_perkara_id" id="laporan_hasil_gelar_perkara_id">
                            <option value="">--Pilih Tanggal LHGP--</option>
                            @foreach ($laporanHasilGelarPerkaraDocuments as $lhgp)
                                <option value="{{ $lhgp->id }}" {{ old('laporan_hasil_gelar_perkara_id') == $lhgp->id ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($lhgp->tanggal_gelar_perkara)->locale('id')->translatedFormat('l, d F Y') }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Laporan Hasil Gelar Perkara yang terkait</small>

                        @error('laporan_hasil_gelar_perkara_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Surat Pemberitahuan Dimulainya Penyidikan (SPDP) --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_pemberitahuan_dimulainya_penyidikan_id">Nomor SPDP<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2" name="surat_pemberitahuan_dimulainya_penyidikan_id" id="surat_pemberitahuan_dimulainya_penyidikan_id">
                            <option value="">--Pilih No SPDP--</option>
                            @foreach ($suratPemberitahuanDimulainyaPenyidikanDocuments as $spdp)
                                <option value="{{ $spdp->id }}" {{ old('surat_pemberitahuan_dimulainya_penyidikan_id') == $spdp->id ? 'selected' : '' }}>
                                    {{ $spdp->document_number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Surat Pemberitahuan Dimulainya Penyidikan yang terkait</small>

                        @error('surat_pemberitahuan_dimulainya_penyidikan_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Alasan Penghentian --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="alasan_penghentian">Alasan Penghentian Penyidikan<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('alasan_penghentian') is-invalid @enderror" 
                                name="alasan_penghentian" id="alasan_penghentian" required
                                onchange="toggleRJFields(this.value)">
                            <option value="">--Pilih Alasan Penghentian--</option>
                            @foreach($alasanPenghentianOptions as $key => $label)
                                <option value="{{ $key }}" {{ old('alasan_penghentian') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih alasan penghentian penyidikan sesuai ketentuan</small>

                        @error('alasan_penghentian')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Penjelasan Tambahan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="menetapkan_alasan">Penjelasan Tambahan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <textarea class="form-control" id="menetapkan_alasan" name="menetapkan_alasan" rows="3"
                                  placeholder="Jelaskan detail tambahan terkait alasan penghentian jika diperlukan">{{ old('menetapkan_alasan') }}</textarea>
                    </div>
                </div>

                {{-- Nama Kejaksaan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="prosecutor_id">Nama Kejaksaan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('prosecutor_id') is-invalid @enderror" 
                                name="prosecutor_id" id="prosecutor_id">
                            <option value="">--Pilih Kejaksaan--</option>
                            @foreach ($prosecutors as $prosecutor)
                                <option value="{{ $prosecutor->id }}" {{ old('prosecutor_id') == $prosecutor->id ? 'selected' : '' }}>
                                    {{ $prosecutor->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Kejaksaan terkait</small>

                        @error('prosecutor_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Nama Pengadilan --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="court_id">Nama Pengadilan</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('court_id') is-invalid @enderror" 
                                name="court_id" id="court_id">
                            <option value="">--Pilih Pengadilan--</option>
                            @foreach ($courts as $court)
                                <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Pengadilan terkait</small>

                        @error('court_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Daftar Tersangka --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="suspects">Daftar Tersangka<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('suspects') is-invalid @enderror" 
                                name="suspects[]" id="suspects" multiple="multiple" required>
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ (is_array(old('suspects')) && in_array($suspect->id, old('suspects'))) ? 'selected' : '' }}>
                                    {{ $suspect->name }} - {{ $suspect->identity_number }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih Tersangka (hanya berlaku bagi yang telah di terbitkan S.Tap Tersangka)</small>

                        @error('suspects')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">DATA KELENGKAPAN INTEGRASI EMP</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="nomor_serah_terima">Nomor Serah Terima</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="nomor_serah_terima" type="text"
                            class="form-control @error('nomor_serah_terima') is-invalid @enderror"
                            name="nomor_serah_terima" value="{{ old('nomor_serah_terima') }}"
                            placeholder="Nomor Serah Terima dari/ke Kejaksaan">

                        @error('nomor_serah_terima')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tanggal_serah_terima">Tanggal Serah Terima</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input class="form-control @error('tanggal_serah_terima') is-invalid @enderror" id="tanggal_serah_terima" name="tanggal_serah_terima"
                            placeholder="YYYY-MM-DD" autocomplete="off" value="{{ old('tanggal_serah_terima') }}"
                            data-provide="datepicker">

                        @error('tanggal_serah_terima')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="barang_bukti">Barang Bukti</label>
                    <div class="col-lg-7 col-md-7 col-sm-10 col-10">
                        <select class="form-control select2 @error('barang_bukti') is-invalid @enderror" 
                                id="barang_bukti" name="barang_bukti[]" multi-select multiple="multiple" 
                                data-placeholder="Pilih Barang Bukti">
                            @foreach($daftarBarangBukti as $bb)
                                <option value="{{ $bb->nama_barang }} ({{ $bb->jumlah_barang }})" 
                                    {{ (is_array(old('barang_bukti')) && in_array($bb->nama_barang . ' (' . $bb->jumlah_barang . ')', old('barang_bukti'))) ? 'selected' : '' }}>
                                    {{ $bb->nama_barang }} ({{ $bb->jumlah_barang }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih barang bukti yang sudah ada atau tambah baru lewat tombol di samping</small>

                        @error('barang_bukti')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-2 py-1">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="$('#myModalPenyitaan3').modal('show')">
                            <i class="bi bi-plus-circle me-1"></i> Kelola
                        </button>
                    </div>
                </div>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jumlah_bb">Jumlah Barang Bukti</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <input id="jumlah_bb" type="number" min="0" step="1"
                            class="form-control @error('jumlah_bb') is-invalid @enderror"
                            name="jumlah_bb" value="{{ old('jumlah_bb', 0) }}"
                            placeholder="0" readonly>
                        <small class="text-muted">Dihitung otomatis dari jumlah pilihan di atas</small>

                        @error('jumlah_bb')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">PEJABAT PENANDATANGAN</h5>

                {{-- Yang Menandatangani --}}
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <select class="form-control select2 @error('signatory') is-invalid @enderror" name="signatory" id="signatory" required>
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php
                                    $positionName = $data->position->name ?? '';
                                    $selectedId = old('signatory');
                                @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}" {{ $selectedId == $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk
                            untuk mendapat bantuan)</small>

                        @error('signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Summary indicator untuk RJ data --}}
                <div id="rjDataSummary" class="input-group row mb-3 ms-0" style="display:none;">
                    <label class="fw-bold col-sm-3 col-form-label">Data Keadilan Restoratif</label>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Data Keadilan Restoratif sudah diisi. 
                            <a href="#" class="alert-link" onclick="$('#rjModal').modal('show'); return false;">Klik di sini untuk edit</a>
                        </div>
                    </div>
                </div>

                {{-- Hidden inputs untuk RJ data --}}
                <input type="hidden" name="rj_nomor_kesepakatan" id="hidden_rj_nomor_kesepakatan" value="{{ old('rj_nomor_kesepakatan') }}">
                <input type="hidden" name="rj_tanggal_kesepakatan" id="hidden_rj_tanggal_kesepakatan" value="{{ old('rj_tanggal_kesepakatan') }}">
                <input type="hidden" name="rj_pihak_korban" id="hidden_rj_pihak_korban" value="{{ old('rj_pihak_korban') }}">
                <input type="hidden" name="rj_pihak_pelaku" id="hidden_rj_pihak_pelaku" value="{{ old('rj_pihak_pelaku') }}">
                <input type="hidden" name="rj_bentuk_ganti_rugi" id="hidden_rj_bentuk_ganti_rugi" value="{{ old('rj_bentuk_ganti_rugi') }}">
                <input type="hidden" name="rj_nilai_ganti_rugi" id="hidden_rj_nilai_ganti_rugi" value="{{ old('rj_nilai_ganti_rugi') }}">
                <input type="hidden" name="rj_keterangan_tambahan" id="hidden_rj_keterangan_tambahan" value="{{ old('rj_keterangan_tambahan') }}">
                <div id="hidden_rj_dokumen_container"></div>

                <div class="mt-4 d-flex justify-content-center">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Restorative Justice --}}
    <div class="modal fade" id="rjModal" tabindex="-1" role="dialog" aria-labelledby="rjModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rjModalLabel">Data Keadilan Restoratif</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_rj_nomor_kesepakatan">Nomor Kesepakatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_rj_nomor_kesepakatan" placeholder="Masukkan nomor kesepakatan">
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_tanggal_kesepakatan">Tanggal Kesepakatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="modal_rj_tanggal_kesepakatan" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_pihak_korban">Pihak Korban <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="modal_rj_pihak_korban" rows="2" placeholder="Nama dan identitas pihak korban"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_pihak_pelaku">Pihak Pelaku <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="modal_rj_pihak_pelaku" rows="2" placeholder="Nama dan identitas pihak pelaku"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_bentuk_ganti_rugi">Bentuk Ganti Rugi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="modal_rj_bentuk_ganti_rugi" rows="2" placeholder="Jelaskan bentuk ganti rugi yang disepakati"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_nilai_ganti_rugi">Nilai Ganti Rugi</label>
                        <input type="text" class="form-control" id="modal_rj_nilai_ganti_rugi" placeholder="Contoh: Rp 10.000.000">
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_keterangan_tambahan">Keterangan Tambahan</label>
                        <textarea class="form-control" id="modal_rj_keterangan_tambahan" rows="3" placeholder="Informasi tambahan lainnya"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_rj_dokumen_pendukung">Dokumen Pendukung (PDF)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="modal_rj_dokumen_pendukung" multiple accept=".pdf">
                            <label class="custom-file-label" for="modal_rj_dokumen_pendukung">Pilih file PDF...</label>
                        </div>
                        <small class="text-muted">Upload dokumen kesepakatan, foto, atau dokumen pendukung lainnya (PDF, max 5MB per file)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveRJData">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>
    @include('produktivitas.surat-penyitaan.modal.modal', ['id' => $accidentId])
@endsection

@push('script')
    <script>
        function toggleRJFields(value) {
            if (value === 'RESTORATIF_JUSTICE') {
                if (!document.getElementById('hidden_rj_nomor_kesepakatan').value) {
                    setTimeout(function() {
                        $('#rjModal').modal('show');
                    }, 300);
                } else {
                    document.getElementById('rjDataSummary').style.display = 'flex';
                }
            } else {
                document.getElementById('rjDataSummary').style.display = 'none';
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize Datepicker
            $('.datepicker, [data-provide="datepicker"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            
            // Save RJ Data
            $('#saveRJData').on('click', function() {
                $('#hidden_rj_nomor_kesepakatan').val($('#modal_rj_nomor_kesepakatan').val());
                $('#hidden_rj_tanggal_kesepakatan').val($('#modal_rj_tanggal_kesepakatan').val());
                $('#hidden_rj_pihak_korban').val($('#modal_rj_pihak_korban').val());
                $('#hidden_rj_pihak_pelaku').val($('#modal_rj_pihak_pelaku').val());
                $('#hidden_rj_bentuk_ganti_rugi').val($('#modal_rj_bentuk_ganti_rugi').val());
                $('#hidden_rj_nilai_ganti_rugi').val($('#modal_rj_nilai_ganti_rugi').val());
                $('#hidden_rj_keterangan_tambahan').val($('#modal_rj_keterangan_tambahan').val());
                
                $('#rjDataSummary').show();
                $('#rjModal').modal('hide');
            });

            // Update file input label
            $('#modal_rj_dokumen_pendukung').on('change', function() {
                var fileCount = this.files.length;
                var label = $(this).next('.custom-file-label');
                if (fileCount > 0) {
                    if (fileCount === 1) {
                        label.html(this.files[0].name);
                    } else {
                        label.html(fileCount + ' file dipilih');
                    }
                }
            });

            // Initialize Select2 for Barang Bukti
            $('#barang_bukti').select2({
                theme: 'bootstrap4',
                width: '100%',
                multiple: true,
                tags: true,
                tokenSeparators: [',']
            });

            // Update jumlah_bb based on selection
            $('#barang_bukti').on('change', function() {
                var count = $(this).val() ? $(this).val().length : 0;
                $('#jumlah_bb').val(count);
            });
            
            // Trigger initial count
            $('#barang_bukti').trigger('change');

            // Datatable for Barang Bukti Modal
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id_barang_bukti").val();
            
            var table_bb = $('.barang-bukti-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('get_barang_bukti') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_barang', name: 'nama_barang' },
                    { data: 'jumlah_barang', name: 'jumlah_barang' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[1, 'asc']]
            });

            // Handle Save Barang Bukti via AJAX
            $(".btn-barang-bukti").click(function(e) {
                e.preventDefault();
                var barang_bukti_id = $("#barang_bukti_id").val();
                var nama_barang = $("#nama_barang").val();
                var jumlah_barang = $("#jumlah_barang").val();

                if (!nama_barang || !jumlah_barang) {
                    alert('Mohon isi nama dan jumlah barang bukti.');
                    return;
                }

                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>');

                $.ajax({
                    url: "{{ route('add_barang_bukti') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        barang_bukti_id: barang_bukti_id,
                        accident_id_barang_bukti: accident_id,
                        nama_barang: nama_barang,
                        jumlah_barang: jumlah_barang
                    },
                    success: function(data) {
                        $('#barang-bukti-form')[0].reset();
                        $('#barang_bukti_id').val('');
                        $(".btn-barang-bukti").prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                        
                        // Refresh pool table
                        table_bb.draw();

                        // Add to Select2 if success
                        var newOptionText = nama_barang + ' (' + jumlah_barang + ')';
                        if ($('#barang_bukti').find("option[value='" + newOptionText + "']").length == 0) {
                            var newOption = new Option(newOptionText, newOptionText, true, true);
                            $('#barang_bukti').append(newOption).trigger('change');
                        }

                        toastr.success('Barang bukti berhasil disimpan.');
                    },
                    error: function(xhr) {
                        $(".btn-barang-bukti").prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                        alert('Terjadi kesalahan saat menyimpan data.');
                    }
                });
            });

            // Edit BB in Modal
            $(document).on('click', '.editBarangBukti', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var qty = $(this).data('qty');
                
                $('#barang_bukti_id').val(id);
                $('#nama_barang').val(name);
                $('#jumlah_barang').val(qty);
                $('#nama_barang').focus();
            });

            // Delete BB in Modal
            $(document).on('click', '.deleteBarang', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var qty = $(this).data('qty');
                var optionText = name + ' (' + qty + ')';
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus barang bukti "' + name + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete_barang_bukti') }}",
                            type: 'POST',
                            data: {
                                _token: _token,
                                id: id
                            },
                            success: function(response) {
                                if(response.success) {
                                    table_bb.draw();
                                    
                                    // Remove from Select2
                                    $('#barang_bukti option').each(function() {
                                        if ($(this).val() == optionText) {
                                            $(this).remove();
                                        }
                                    });
                                    $('#barang_bukti').trigger('change');
                                    
                                    toastr.success(response.message);
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Terjadi kesalahan saat menghapus data.');
                            }
                        });
                    }
                });
            });

            // Form submission (Existing)
            $('#suratKetetapanPenghentianPenyidikanForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = response.redirect;
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan'
                        });
                    }
                });
            });
        });
    </script>
@endpush
