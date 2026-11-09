@php
    $_title = 'Ubah Dokumen Resume Berkas Perkara';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .card-header-resume {
            background-color: #000080;
            color: #ffffff;
            font-weight: bold;
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
        .section-subhead {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
            margin-top: 1.5rem;
            margin-bottom: 0.85rem;
        }
        .repeater-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 16px;
            background-color: #fbfcfd;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .form-label, label.small {
            font-size: 0.88rem;
            margin-bottom: 0.4rem;
            color: #334155;
            font-weight: 600;
        }
        .form-control {
            line-height: 1.6;
            font-size: 0.925rem;
        }
        textarea.form-control {
            line-height: 1.6;
            font-size: 0.925rem;
        }
    </style>
@endpush

@section('content')

    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i> Kembali ke Progress Perkara</a>

    <div class="box">
        <div class="box-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-blue-dark m-0">Ubah Dokumen Resume Berkas Perkara</h5>
            </div>

            <div class="alert alert-danger mt-3 py-3 px-4" id="attentionBox" style="background-color: #fef2f2; border-color: #fecaca; color: #991b1b; border-radius: 8px;">
                <div class="text-center" style="line-height: 1.7; font-size: 0.95rem;">
                    <span class="fw-bold d-block mb-1 fs-6">PERHATIAN</span>
                    Data resume berkas perkara merupakan intisari berkas administrasi penyidikan (MINDIK) resmi POLRI.<br />
                    Seluruh formulir berikut wajib diisi secara lengkap dan benar sesuai fakta penyidikan perkara.
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
            <form action="{{ route('doc.resume-document.update', ['id' => $document->id]) }}" method="POST" id="formResumeDocument">
                @csrf
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accidentId }}">

                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">I. Identitas Surat & Dasar Dokumen<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold form-label" for="nomor_surat">Nomor Dokumen Resume<span class="text-danger">*</span></label>
                                    <input id="nomor_surat" type="text" class="form-control" name="nomor_surat"
                                        value="{{ old('nomor_surat', $document->nomor_surat) }}" required placeholder="Contoh: R/01/X/2026/Lantas">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="fw-bold form-label" for="tanggal_surat">Tanggal Surat Resume<span class="text-danger">*</span></label>
                                    <input id="tanggal_surat" type="date" class="form-control" name="tanggal_surat"
                                        value="{{ old('tanggal_surat', $document->tanggal_surat ? \Carbon\Carbon::parse($document->tanggal_surat)->format('Y-m-d') : date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="fw-bold form-label" for="dikeluarkan_di">Dikeluarkan di (Kota)<span class="text-danger">*</span></label>
                                    <input id="dikeluarkan_di" type="text" class="form-control" name="dikeluarkan_di"
                                        value="{{ old('dikeluarkan_di', $document->dikeluarkan_di) }}" required placeholder="Contoh: JAMBI">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold form-label" for="satker_penyidik">Kesatuan / Satker Penyidik<span class="text-danger">*</span></label>
                            <input id="satker_penyidik" type="text" class="form-control" name="satker_penyidik"
                                value="{{ old('satker_penyidik', $document->satker_penyidik) }}" required placeholder="Satuan Lalu Lintas...">
                        </div>

                        <div class="section-subhead">Rujukan Dokumen Dasar Penyidikan :</div>

                        {{-- Dasar A: LP --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold form-label">A. Nomor Laporan Polisi (LP)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nomor_lp" id="nomor_lp"
                                    value="{{ old('nomor_lp', $document->nomor_lp ?? $document->dasar['lp_nomor'] ?? $accident->no_lp) }}" required readonly>
                                <input type="hidden" name="dasar[lp_nomor]" value="{{ old('nomor_lp', $document->nomor_lp ?? $document->dasar['lp_nomor'] ?? $accident->no_lp) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Tanggal LP<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_lp" id="tanggal_lp"
                                    value="{{ old('tanggal_lp', $document->tanggal_lp ? \Carbon\Carbon::parse($document->tanggal_lp)->format('Y-m-d') : ($document->dasar['lp_tanggal'] ?? date('Y-m-d'))) }}" required>
                                <input type="hidden" name="dasar[lp_tanggal]" value="{{ old('tanggal_lp', $document->tanggal_lp ? \Carbon\Carbon::parse($document->tanggal_lp)->format('Y-m-d') : ($document->dasar['lp_tanggal'] ?? date('Y-m-d'))) }}">
                            </div>
                        </div>

                        {{-- Dasar B: Sprindik --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold form-label">B. Nomor Surat Perintah Penyidikan (SP.Sidik)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="dasar[sprindik_nomor]"
                                    value="{{ old('dasar.sprindik_nomor', $document->dasar['sprindik_nomor'] ?? ($sprindikDoc->document_number ?? '')) }}" required placeholder="SP.Sidik/...">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Tanggal SP.Sidik<span class="text-danger">*</span></label>
                                @php
                                    $sprindikRaw = old('dasar.sprindik_tanggal', $document->dasar['sprindik_tanggal'] ?? ($sprindikTanggal ?? null));
                                    $sprindikVal = $sprindikRaw ? \Carbon\Carbon::parse($sprindikRaw)->format('Y-m-d') : date('Y-m-d');
                                @endphp
                                <input type="date" class="form-control" name="dasar[sprindik_tanggal]"
                                    value="{{ $sprindikVal }}" required>
                            </div>
                        </div>

                        {{-- Dasar C: Springas --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold form-label">C. Nomor Surat Perintah Tugas (SP.Gas)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="dasar[springas_nomor]"
                                    value="{{ old('dasar.springas_nomor', $document->dasar['springas_nomor'] ?? ($springasDoc->document_number ?? '')) }}" required placeholder="SP.Gas/...">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Tanggal SP.Gas<span class="text-danger">*</span></label>
                                @php
                                    $springasRaw = old('dasar.springas_tanggal', $document->dasar['springas_tanggal'] ?? ($springasTanggal ?? null));
                                    $springasVal = $springasRaw ? \Carbon\Carbon::parse($springasRaw)->format('Y-m-d') : date('Y-m-d');
                                @endphp
                                <input type="date" class="form-control" name="dasar[springas_tanggal]"
                                    value="{{ $springasVal }}" required>
                            </div>
                        </div>

                        {{-- Dasar D: SPDP --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold form-label">D. Nomor Surat Pemberitahuan Dimulainya Penyidikan (SPDP)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="dasar[spdp_nomor]"
                                    value="{{ old('dasar.spdp_nomor', $document->dasar['spdp_nomor'] ?? ($spdpDoc->document_number ?? ($spdpDoc->nomor_spdp ?? ''))) }}" required placeholder="SPDP/...">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Tanggal SPDP<span class="text-danger">*</span></label>
                                @php
                                    $spdpRaw = old('dasar.spdp_tanggal', $document->dasar['spdp_tanggal'] ?? ($spdpTanggal ?? null));
                                    $spdpVal = $spdpRaw ? \Carbon\Carbon::parse($spdpRaw)->format('Y-m-d') : date('Y-m-d');
                                @endphp
                                <input type="date" class="form-control" name="dasar[spdp_tanggal]"
                                    value="{{ $spdpVal }}" required>
                            </div>
                        </div>

                        {{-- Dasar E: S.Tap Tersangka --}}
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="fw-bold form-label">E. Nomor Surat Ketetapan Tersangka (S.Tap)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="dasar[sp_tap_nomor]"
                                    value="{{ old('dasar.sp_tap_nomor', $document->dasar['sp_tap_nomor'] ?? ($spTapDoc->document_number ?? '')) }}" required placeholder="S.Tap/...">
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold form-label">Tanggal S.Tap<span class="text-danger">*</span></label>
                                @php
                                    $spTapRaw = old('dasar.sp_tap_tanggal', $document->dasar['sp_tap_tanggal'] ?? ($spTapTanggal ?? null));
                                    $spTapVal = $spTapRaw ? \Carbon\Carbon::parse($spTapRaw)->format('Y-m-d') : date('Y-m-d');
                                @endphp
                                <input type="date" class="form-control" name="dasar[sp_tap_tanggal]"
                                    value="{{ $spTapVal }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Atas Nama Tersangka<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="dasar[sp_tap_tersangka]"
                                    value="{{ old('dasar.sp_tap_tersangka', $document->dasar['sp_tap_tersangka'] ?? ($spTapDoc->suspect_name ?? '')) }}" required placeholder="Nama Tersangka">
                            </div>
                        </div>

                        {{-- Dasar F: Surat ke Jaksa --}}
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="fw-bold form-label">F. Nomor Surat Pemberitahuan Penetapan Tsk ke Kejaksaan<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="dasar[surat_jaksa_nomor]"
                                    value="{{ old('dasar.surat_jaksa_nomor', $document->dasar['surat_jaksa_nomor'] ?? '') }}" required placeholder="B/...">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Tanggal Surat ke Kejaksaan<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="dasar[surat_jaksa_tanggal]"
                                    value="{{ old('dasar.surat_jaksa_tanggal', $document->dasar['surat_jaksa_tanggal'] ?? date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">II. Uraian Perkara & Taksir Kerugian<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="fw-bold form-label">Dugaan Tindak Pidana<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[tindak_pidana]"
                                    value="{{ old('perkara.tindak_pidana', $document->perkara['tindak_pidana'] ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Pasal<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[pasal]"
                                    value="{{ old('perkara.pasal', $document->perkara['pasal'] ?? 'Pasal 310 ayat (4)') }}" required placeholder="Pasal 310 ayat (4)">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold form-label">UU Nomor<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[uu_nomor]"
                                    value="{{ old('perkara.uu_nomor', $document->perkara['uu_nomor'] ?? '22') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Tahun UU<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[uu_tahun]"
                                    value="{{ old('perkara.uu_tahun', $document->perkara['uu_tahun'] ?? '2009') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Tentang UU<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[uu_tentang]"
                                    value="{{ old('perkara.uu_tentang', $document->perkara['uu_tentang'] ?? 'Lalu Lintas dan Angkutan Jalan') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold form-label">Tempat Kejadian Perkara (TKP)<span class="text-danger">*</span></label>
                            <textarea class="form-control" name="perkara[tkp]" rows="3" required placeholder="Alamat lengkap lokasi TKP">{{ old('perkara.tkp', $document->perkara['tkp'] ?? '') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Hari Kejadian<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[hari]"
                                    value="{{ old('perkara.hari', $document->perkara['hari'] ?? '') }}" required placeholder="Contoh: Senin">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Tanggal Kejadian<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="perkara[tanggal]"
                                    value="{{ old('perkara.tanggal', $document->perkara['tanggal'] ?? date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold form-label">Kurun Waktu / Pukul<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perkara[kurun_waktu]"
                                    value="{{ old('perkara.kurun_waktu', $document->perkara['kurun_waktu'] ?? '') }}" required placeholder="sekitar pukul 14.30 WIB">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold form-label">Pihak / Tersangka<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="perkara[pihak]"
                                value="{{ old('perkara.pihak', $document->perkara['pihak'] ?? '') }}" required>
                        </div>

                        <div class="section-subhead">Daftar Kerugian Materil & Nilai Taksir:</div>
                        <div class="mb-4">
                            <label class="fw-bold form-label">Rincian Kerugian Kerusakan Kendaraan / Benda:<span class="text-danger">*</span></label>
                            @php
                                $daftarKerugian = $document->perkara['daftar_kerugian'] ?? ['1 (satu) unit Kendaraan mengalami kerusakan bodi;', '1 (satu) unit Kendaraan lawan rusak depan.'];
                                if (!is_array($daftarKerugian)) {
                                    $daftarKerugian = [$daftarKerugian];
                                }
                                $alpha = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                            @endphp
                            <div id="kerugianListContainer">
                                @foreach ($daftarKerugian as $kIdx => $kVal)
                                    <div class="input-group mb-2 row-kerugian">
                                        <span class="input-group-text fw-bold">{{ $alpha[$kIdx] ?? ($kIdx + 1) }}</span>
                                        <input type="text" class="form-control" name="perkara[daftar_kerugian][]"
                                            value="{{ old('perkara.daftar_kerugian.' . $kIdx, $kVal) }}" required>
                                        <button type="button" class="btn btn-outline-danger btn-remove-kerugian">Hapus</button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btnAddKerugian">
                                + Tambah Kerugian
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold form-label">Nilai Taksir Kerugian (Rp.)<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="nilai_taksir" name="perkara[nilai_taksir]"
                                        value="{{ old('perkara.nilai_taksir', $document->perkara['nilai_taksir'] ?? 0) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold form-label">Nilai Taksir Terbilang<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light" id="nilai_terbilang" name="perkara[terbilang]"
                                        value="{{ old('perkara.terbilang', $document->perkara['terbilang'] ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">III. Fakta-Fakta Penyidikan (TKP, Korban, Penangkapan, Penahanan, Geledah, Sita)<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        {{-- A. TKP --}}
                        <div class="section-subhead">A. Penanganan Tempat Kejadian Perkara (TKP)</div>
                        <div class="mb-3">
                            <label class="fw-bold form-label">1. Tindakan Pertama di TKP (Poin a, b, c):</label>
                            @php
                                $tkp1 = $document->fakta_tkp['tindakan_pertama'] ?? "Mendatangi Tempat Kejadian Perkara (TKP) setelah menerima laporan kecelakaan;\nMenolong korban kecelakaan dan membawa ke Rumah Sakit terdekat;\nMengamankan status quo TKP dan barang bukti kecelakaan.";
                                $tkp1Text = is_array($tkp1) ? implode("\n", $tkp1) : $tkp1;
                            @endphp
                            <textarea class="form-control" name="fakta_tkp[tindakan_pertama][]" rows="4" required>{{ old('fakta_tkp.tindakan_pertama.0', $tkp1Text) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="fw-bold form-label">2. Pengolahan TKP (Berita Acara, Sketsa, dan Pemotretan):</label>
                            @php
                                $tkp2Fallback = "1. Berita Acara Pemeriksaan di TKP;\n2. Berita Acara Memasuki Rumah/Tempat Tertutup Di TKP;\n3. Berita Acara Penemuan Dan Penyitaan Barang Bukti Di TKP;\n4. Berita Acara Penemuan Dan Pengambilan Darah Di TKP;\n5. Berita Acara Penemuan Dan Pengambilan Sperma Di TKP;\n6. Berita Acara Penemuan Dan Pengambilan Sidik Jari Latent/Jejak Kaki/Jejak Ban Di TKP;\n7. Kartu Pendapatan Jejak/Sidik Jari Di TKP;\n8. Berita Acara Penemuan Dan Pengambilan Limbah Dan Lain-Lain Di TKP;\n9. Berita Acara Pemotretan Di TKP (Termasuk Data Pemotretan Korban Dan Barang Bukti);\n10. Gambar Sketsa Kasar TKP;\n11. Sketsa TKP Umum/Khusus;\n12. Data Pentahapan Penanganan TKP (Kegiatan Selama Di TKP).";
                                $tkp2 = $document->fakta_tkp['pengolahan_tkp'] ?? $tkp2Fallback;
                                $tkp2Text = is_array($tkp2) ? implode("\n", $tkp2) : $tkp2;
                            @endphp
                            <textarea class="form-control" name="fakta_tkp[pengolahan_tkp][]" rows="6" required>{{ old('fakta_tkp.pengolahan_tkp.0', $tkp2Text) }}</textarea>
                        </div>

                        {{-- B. Korban --}}
                        <div class="section-subhead">B. Pemeriksaan Keadaan Korban</div>
                        <div class="mb-4">
                            @php
                                $korbanFallback = "1. Surat Permintaan Pemeriksaan Luka Kepada Kepala Rumah Sakit;\n2. Surat Permintaan Pemeriksaan Mayat Kepada Kepala Rumah Sakit;\n3. Surat Permintaan Pemeriksaan Korban Perkosaan/Perzinahan Kepada Kepala Rumah Sakit Atau Puskesmas;\n4. Surat Permintaan Pemeriksaan Oleh Ahli;\n5. Berita Acara Pemeriksaan Oleh Ahli/Visum et Repertum.";
                                $korbanVal = $document->fakta_korban ?? $korbanFallback;
                                $korbanText = is_array($korbanVal) ? implode("\n", $korbanVal) : $korbanVal;
                            @endphp
                            <textarea class="form-control" name="fakta_korban[]" rows="5" required>{{ old('fakta_korban.0', $korbanText) }}</textarea>
                        </div>

                        {{-- C. Pemanggilan & D. Membawa --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="section-subhead">C. Pemanggilan Saksi / Tersangka</div>
                                <div class="mb-3">
                                    <label class="fw-bold form-label small">Surat Panggilan Ke-1 (Nomor & Tanggal):</label>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control form-control-sm" name="fakta_pemanggilan[p1_nomor]"
                                                value="{{ old('fakta_pemanggilan.p1_nomor', $document->fakta_pemanggilan['p1_nomor'] ?? 'SP.Pgl/01/X/2026/Lantas') }}" required placeholder="Nomor Surat">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="date" class="form-control form-control-sm" name="fakta_pemanggilan[p1_tanggal]"
                                                value="{{ old('fakta_pemanggilan.p1_tanggal', $document->fakta_pemanggilan['p1_tanggal'] ?? date('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control form-control-sm" name="fakta_pemanggilan[p1_nama]"
                                                value="{{ old('fakta_pemanggilan.p1_nama', $document->fakta_pemanggilan['p1_nama'] ?? 'Tersangka') }}" required placeholder="Nama yang dipanggil">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="date" class="form-control form-control-sm" name="fakta_pemanggilan[p1_hadir]"
                                                value="{{ old('fakta_pemanggilan.p1_hadir', $document->fakta_pemanggilan['p1_hadir'] ?? date('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold form-label small">Keterangan Panggilan Ke-2:</label>
                                    <input type="text" class="form-control form-control-sm" name="fakta_pemanggilan[p2_keterangan]"
                                        value="{{ old('fakta_pemanggilan.p2_keterangan', $document->fakta_pemanggilan['p2_keterangan'] ?? 'Nihil / Telah hadir pada panggilan pertama.') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="section-subhead">D. Perintah Membawa Saksi / Tersangka</div>
                                <div class="mb-3">
                                    <label class="fw-bold form-label small">Surat Perintah Membawa (SP Membawa):</label>
                                    <input type="text" class="form-control form-control-sm" name="fakta_perintah_membawa[sp_membawa]"
                                        value="{{ old('fakta_perintah_membawa.sp_membawa', $document->fakta_perintah_membawa['sp_membawa'] ?? 'Dengan Surat Perintah Membawa Tersangka/Saksi Nomor: ....... tanggal......, telah dibawa untuk dihadapkan kepada penyidik, seorang Tersangka/saksi atas nama .... pada tanggal ....') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold form-label small">Berita Acara Membawa:</label>
                                    <input type="text" class="form-control form-control-sm" name="fakta_perintah_membawa[ba_membawa]"
                                        value="{{ old('fakta_perintah_membawa.ba_membawa', $document->fakta_perintah_membawa['ba_membawa'] ?? 'Telah dibuatkan Berita acara Membawa Tersangka/Saksi atas nama .... tanggal ....') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold form-label small">Pemberitahuan kepada Keluarga:</label>
                                    <input type="text" class="form-control form-control-sm" name="fakta_perintah_membawa[surat_keluarga]"
                                        value="{{ old('fakta_perintah_membawa.surat_keluarga', $document->fakta_perintah_membawa['surat_keluarga'] ?? 'Telah dibuatkan dan dikirimkan Surat pemberitahuan kepada keluarga Nomor: B/...... tanggal ...... .') }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- E. Penangkapan & F. Penahanan --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="section-subhead">E. Administrasi Penangkapan</div>
                                @php
                                    $penangkapanFallback = "1. Surat Perintah Tugas untuk melakukan penangkapan terhadap Tersangka;\n2. Surat Perintah Penangkapan telah dilakukan penangkapan terhadap Tersangka;\n3. Berita Acara Penangkapan;\n4. Berita Acara Penolakan Tanda Tangan Surat Perintah Penangkapan dan Berita Acara Penangkapan;\n5. Berita Acara Penolakan Tanda Tangan Atas Berita Acara Penolakan Tandatangan Surat Perintah Penangkapan Dan Berita Acara Penangkapan (oleh Penyidik);\n6. Berita Acara Penggeledahan Badan/Pakaian;\n7. Surat Pemberitahuan Penangkapan kepada keluarga Tersangka;\n8. Surat Perintah Membawa Dan Menghadapkan Tersangka kepada Penyidik;\n9. Berita Acara Membawa Dan Menghadapkan Tersangka kepada Penyidik;\n10. Surat Pemberitahuan Membawa Dan Menghadapkan kepada Keluarga Tersangka;\n11. Surat Perintah Pelepasan Tersangka;\n12. Berita Acara Pelepasan Tersangka;\n13. Surat Pemberitahuan Pelepasan Tersangka kepada keluarga Tersangka;\n14. Surat Perintah Penangkapan Atas Permintaan Satuan Lain/Kepolisian Anggota ICPO-Interpol;\n15. Berita Acara Penangkapan Atas Permintaan Satuan Lain/Kepolisian Anggota ICPO-Interpol;\n16. Surat Pemberitahuan Penangkapan Atas Permintaan Satuan Lain/Kepolisian Anggota ICPO-Interpol kepada keluarga Tersangka.";
                                    $penangkapanVal = $document->fakta_penangkapan ?? $penangkapanFallback;
                                    $penangkapanText = is_array($penangkapanVal) ? implode("\n", $penangkapanVal) : $penangkapanVal;
                                @endphp
                                <textarea class="form-control" name="fakta_penangkapan[]" rows="6" required>{{ old('fakta_penangkapan.0', $penangkapanText) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="section-subhead">F. Administrasi Penahanan</div>
                                @php
                                    $penahananFallback = "1. Surat Perintah Penahanan;\n2. Berita Acara Penahanan;\n3. Berita Acara Penolakan Tandatangan Surat Perintah Penahanan Dan Berita Acara Penahanan;\n4. Berita Acara Penolakan Tandatangan Atas Berita Acara Penolakan Tandatangan Surat Perintah Penahanan Dan Berita Acara Penahanan (oleh Penyidik);\n5. Surat Pemberitahuan Penahanan kepada keluarga Tersangka;\n6. Surat Pemberitahuan Penangkapan dan Penahanan kepada keluarga Tersangka;\n7. Surat Permintaan Perpanjangan Penahanan kepada Jampidum/Jampidsus/Kepala Kejaksaan Tinggi/Negeri;\n8. Surat Ketetapan Perpanjangan Penahanan dari Jampidum/Jampidsus/Kepala Kejaksaan Tinggi/Negeri untuk memperpanjang penahanan terhadap Tersangka;\n9. Surat Perintah Perpanjangan Penahanan dari Jaksa;\n10. Berita Acara Perpanjangan Penahanan dari Jaksa;\n11. Surat Pemberitahuan Perpanjangan Penahanan dari JPU kepada keluarga Tersangka;\n12. Surat Permintaan Perpanjangan Penahanan kepada Ketua Pengadilan Negeri (1);\n13. Surat Ketetapan Perpanjangan Penahanan dari Ketua Pengadilan Negeri (1);\n14. Surat Perintah Perpanjangan Penahanan dari PN 1;\n15. Berita Acara Perpanjangan Penahanan Dari PN 1;\n16. Surat Pemberitahuan Perpanjangan Penahanan Dari PN1 Kepada Keluarga Tersangka;\n17. Surat Permintaan Perpanjangan Penahanan Kepada Ketua Pengadilan Negeri (2);\n18. Surat Ketetapan Perpanjangan Penahanan dari Ketua Pengadilan Negeri (2);\n19. Surat Perintah Perpanjangan Penahanan Dari PN 2;\n20. Berita Acara Perpanjangan Penahanan Dari PN 2;\n21. Surat Pemberitahuan Perpanjangan Penahanan Dari PN 2 Kepada Keluarga Tersangka;\n22. Surat Permohonan Penangguhan Penahanan Dari Tersangka/Keluarganya;\n23. Surat Perintah Penangguhan Penahanan;\n24. Berita Acara Penangguhan Penahanan;\n25. Surat Perintah Pencabutan Penangguhan Penahanan;\n26. Berita Acara Pencabutan Penangguhan Penahanan;\n27. Surat Permohonan Pengalihan Jenis Penahanan Dari Tersangka/Keluarganya;\n28. Surat Perintah Pengalihan Jenis Penahanan;\n29. Berita Acara Pengalihan Jenis Penahanan;\n30. Surat Pemberitahuan Pengalihan Jenis Penahanan Kepada Keluarga Tersangka;\n31. Surat Permohonan Pemindahan Tempat Penahanan Dari Tersangka/Keluarganya;\n32. Surat Perintah Pemindahan Tempat Penahanan;\n33. Berita Acara Pemindahan Tempat Penahanan;\n34. Surat Pemberitahuan Pemindahan Tempat Penahanan Kepada Keluarga Tersangka;\n35. Surat Permohonan Rawat Inap Tersangka Kepada Kepala Rumah Sakit;\n36. Surat Perintah Pembantaran Penahanan;\n37. Berita Acara Pembantaran Penahanan;\n38. Surat Pemberitahuan Pembantaran Penahanan kepada Keluarga Tersangka;\n39. Surat Pengambilan Tahanan Yang Telah Selesai Dirawat Inap Kepada Kepala Rumah Sakit;\n40. Surat Perintah Pencabutan Pembantaran Penahanan;\n41. Berita Acara Pencabutan Pembantaran Penahanan;\n42. Surat Perintah Penahanan Lanjutan;\n43. Berita Acara Penahanan Lanjutan;\n44. Surat Pemberitahuan Pencabutan Pembantaran dan Penahanan Lanjutan kepada Keluarga Tersangka;\n45. Surat Perintah Pengeluaran Tahanan;\n46. Berita Acara Pengeluaran Tahanan.";
                                    $penahananVal = $document->fakta_penahanan ?? $penahananFallback;
                                    $penahananText = is_array($penahananVal) ? implode("\n", $penahananVal) : $penahananVal;
                                @endphp
                                <textarea class="form-control" name="fakta_penahanan[]" rows="6" required>{{ old('fakta_penahanan.0', $penahananText) }}</textarea>
                            </div>
                        </div>

                        {{-- G. Penggeledahan & H. Penyitaan --}}
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="section-subhead">G. Administrasi Penggeledahan</div>
                                @php
                                    $penggeledahanFallback = "1. Surat Perintah Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Umum);\n2. Berita Acara Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Umum);\n3. Surat/Laporan Guna Memperoleh Persetujuan Penggeledahan Kepada Ketua PN;\n4. Surat/Laporan Guna Memperoleh Persetujuan Penggeledahan Dan Penyitaan Kepada Ketua PN;\n5. Surat Permintaan Izin/Izin Khusus Penggeledahan Kepada Ketua PN;\n6. Surat Permintaan Izin/Izin Khusus Penggeledahan Dan Penyitaan Kepada Ketua PN;\n7. Surat Perintah Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Setelah Mendapat Izin/Penetapan Dari Ketua PN);\n8. Berita Acara Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Setelah Mendapat Izin/Penetapan Dari PN);\n9. Surat Perintah Penggeledahan Alat Angkutan Darat/Air/Udara;\n10. Berita Acara Penggeledahan Alat Angkutan Darat/Air/Udara.";
                                    $penggeledahanVal = $document->fakta_penggeledahan ?? $penggeledahanFallback;
                                    $penggeledahanText = is_array($penggeledahanVal) ? implode("\n", $penggeledahanVal) : $penggeledahanVal;
                                @endphp
                                <textarea class="form-control" name="fakta_penggeledahan[]" rows="6" required>{{ old('fakta_penggeledahan.0', $penggeledahanText) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="section-subhead">H. Administrasi Penyitaan</div>
                                @php
                                    $penyitaanFallback = "1. Surat Perintah Penyitaan (Umum);\n2. Surat Tanda Penerimaan Barang Bukti;\n3. Berita Acara Penyitaan Barang Bukti (Umum);\n4. Surat/Laporan Guna Memperoleh Persetujuan Penyitaan Kepada Ketua PN;\n5. Surat Permintaan Izin/Izin Khusus Penyitaan;\n6. Surat Perintah Penyitaan (Setelah Mendapat Izin/Penetapan Dari PN);\n7. Surat Tanda Penerimaan Barang Bukti;\n8. Berita Acara Penyitaan (Setelah Mendapat Izin/Penetapan PN);\n9. Surat Perintah Pembungkusan Barang Bukti;\n10. Berita Acara Pembungkusan Barang Bukti;\n11. Surat Perintah Penyegelan Barang Bukti;\n12. Berita Acara Penyegelan Barang Bukti;\n13. Surat Perintah Penyegelan Dan Pembungkusan Barang Bukti;\n14. Berita Acara Penyegelan Dan Atau Pembungkusan Barang Bukti;\n15. Surat Perintah Pembukaan Dan Pembungkusan/ Penyegelan Kembali Barang Bukti;\n16. Berita Acara Pembukaan Dan Pembungkusan/ Penyegelan Kembali Barang Bukti;\n17. Surat Perintah Penitipan Barang Bukti;\n18. Berita Acara Penitipan Barang Bukti;\n19. Surat Perintah Titip/Titip Rawat Barang Bukti;\n20. Berita Acara Titip/Titip Rawat Barang Bukti;\n21. Surat Perintah Pengembalian Benda Sitaan/Barang Bukti;\n22. Berita Acara Pengembalian Barang Bukti;\n23. Surat Permintaan Penelitian/Pemeriksaan Benda Sitaan/Barang bukti Kepada Instansi Berwenang;\n24. Surat Pemberitahuan Dan Permintaan Persetujuan Lelang Barang Bukti Kepada Tersangka/Kuasanya;\n25. Surat Pernyataan Persetujuan Pelelangan Barang Bukti Dari Tersangka;\n26. Surat Permohonan Izin Pelelangan Barang Bukti Kepada Ketua PN;\n27. Surat Ketetapan Pelelangan Benda Sitaan/ Barang Bukti;\n28. Surat Perintah Pelelangan Benda Sitaan/Barang Bukti;\n29. Berita Acara Pelelangan Benda Sitaan/Barang Bukti;\n30. Surat Perintah Penyisihan Barang Bukti;\n31. Berita Acara Penyisihan Benda Sitaan/ Barang Bukti;\n32. Surat Permintaan Bantuan Pelelangan Benda Sitaan/Barang Bukti Kepada Kepala Kantor Lelang Negara;\n33. Berita Acara Penerimaan Hasil Lelang;\n34. Surat Permintaan Bantuan Pemeriksaan Benda Sitaan/Barang Bukti Kepada Instansi Berwenang Untuk Dimusnahkan;\n35. Surat Pemberitahuan Dan Permintaan Persetujuan Perampasan/Pemusnahan Barang Bukti Kepada Tersangka/Kuasanya;\n36. Surat Pernyataan Persetujuan Perampasan/Pemusnahan Barang Bukti Dari Tersangka;\n37. Surat Permohonan Izin Pemusnahan/Perampasan Benda Sitaan/Barang Bukti Yang Berbahaya Dan Terlarang/Dilarang Untuk Diedarkan Kepada Ketua PN;\n38. Surat Ketetapan Perampasan/Pemusnahan Benda Sitaan/Barang Bukti;\n39. Surat Perintah Perampasan/Pemusnahan Benda Sitaan/ Barang Bukti;\n40. Berita Acara Pemusnahan/Perampasan Benda Sitaan/Barang Bukti Yang Berbahaya Dan Terlarang/Dilarang Untuk Diedarkan;\n41. Surat Undangan Dalam Rangka Perampasan/Pemusnahan Benda Sitaan/Barang Bukti;\n42. Berita Acara Penyerahan Barang Bukti Ke Rubasan;\n43. Berita Acara Pengambilan Sampel Barang Bukti;\n44. Surat Kepada Ketua Pengadilan Negeri;\n45. Surat Perintah Pemeriksaan Surat;\n46. Berita Acara Pemeriksaan Surat;\n47. Surat Perintah Penyitaan Surat.";
                                    $penyitaanVal = $document->fakta_penyitaan ?? $penyitaanFallback;
                                    $penyitaanText = is_array($penyitaanVal) ? implode("\n", $penyitaanVal) : $penyitaanVal;
                                @endphp
                                <textarea class="form-control" name="fakta_penyitaan[]" rows="6" required>{{ old('fakta_penyitaan.0', $penyitaanText) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">III. Keterangan Saksi, Ahli, Tersangka, & Barang Bukti<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">

                        {{-- I. SAKSI --}}
                        <div class="section-subhead d-flex justify-content-between align-items-center">
                            <span>I. Keterangan Saksi-Saksi<span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-sm btn-dark-blue" id="btnAddSaksi">
                                + Tambah Saksi
                            </button>
                        </div>
                        <div id="saksiContainer">
                            @php
                                $saksiList = $document->keterangan_saksi ?? [];
                                if (empty($saksiList)) {
                                    $saksiList = [['nama' => 'Saksi 1', 'keterangan' => 'Menerangkan kronologi kejadian.']];
                                }
                            @endphp
                            @foreach ($saksiList as $idx => $s)
                                <div class="repeater-card row-saksi" data-index="{{ $idx }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="fw-bold text-primary m-0">Saksi {{ $loop->iteration }}:</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-saksi">Hapus</button>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-sm fw-bold" name="keterangan_saksi[{{ $idx }}][nama]"
                                            value="{{ old('keterangan_saksi.' . $idx . '.nama', $s['nama'] ?? '') }}" required placeholder="Nama Saksi beserta gelar">
                                    </div>
                                    <label class="small fw-bold">Poin-poin Keterangan (a, b, c...):</label>
                                    <textarea class="form-control form-control-sm" name="keterangan_saksi[{{ $idx }}][keterangan]" rows="4" required placeholder="Tuliskan keterangan per baris">{{ old('keterangan_saksi.' . $idx . '.keterangan', is_array($s['keterangan'] ?? '') ? implode("\n", $s['keterangan']) : ($s['keterangan'] ?? '')) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        {{-- J. AHLI --}}
                        <div class="section-subhead d-flex justify-content-between align-items-center mt-4">
                            <span>J. Keterangan Ahli</span>
                            <button type="button" class="btn btn-sm btn-dark-blue" id="btnAddAhli">
                                + Tambah Ahli
                            </button>
                        </div>
                        <div id="ahliContainer">
                            @php
                                $ahliList = $document->keterangan_ahli ?? [];
                            @endphp
                            @foreach ($ahliList as $idx => $a)
                                <div class="repeater-card row-ahli" data-index="{{ $idx }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="fw-bold text-success m-0">Ahli {{ $loop->iteration }}:</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ahli">Hapus</button>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-sm fw-bold" name="keterangan_ahli[{{ $idx }}][nama]"
                                            value="{{ old('keterangan_ahli.' . $idx . '.nama', $a['nama'] ?? '') }}" required placeholder="Nama Ahli dan Bidang Keahlian">
                                    </div>
                                    <label class="small fw-bold">Poin-poin Keterangan (a, b, c...):</label>
                                    <textarea class="form-control form-control-sm" name="keterangan_ahli[{{ $idx }}][keterangan]" rows="4" placeholder="Tuliskan keterangan per baris">{{ old('keterangan_ahli.' . $idx . '.keterangan', is_array($a['keterangan'] ?? '') ? implode("\n", $a['keterangan']) : ($a['keterangan'] ?? '')) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        {{-- K. TERSANGKA --}}
                        <div class="section-subhead d-flex justify-content-between align-items-center mt-4">
                            <span>K. Keterangan Tersangka<span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-sm btn-dark-blue" id="btnAddTersangka">
                                + Tambah Tersangka
                            </button>
                        </div>
                        <div id="tersangkaContainer">
                            @php
                                $tersangkaList = $document->keterangan_tersangka ?? [];
                                if (empty($tersangkaList)) {
                                    $tersangkaList = [['nama' => 'Tersangka 1', 'keterangan' => 'Menerangkan mengakui kelalaian dalam berkendara.']];
                                }
                            @endphp
                            @foreach ($tersangkaList as $idx => $t)
                                <div class="repeater-card row-tersangka" data-index="{{ $idx }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="fw-bold text-danger m-0">Tersangka {{ $loop->iteration }}:</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-tersangka">Hapus</button>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-sm fw-bold" name="keterangan_tersangka[{{ $idx }}][nama]"
                                            value="{{ old('keterangan_tersangka.' . $idx . '.nama', $t['nama'] ?? '') }}" required placeholder="Nama lengkap Tersangka">
                                    </div>
                                    <label class="small fw-bold">Poin-poin Keterangan (a, b, c...):</label>
                                    <textarea class="form-control form-control-sm" name="keterangan_tersangka[{{ $idx }}][keterangan]" rows="4" required placeholder="Tuliskan keterangan per baris">{{ old('keterangan_tersangka.' . $idx . '.keterangan', is_array($t['keterangan'] ?? '') ? implode("\n", $t['keterangan']) : ($t['keterangan'] ?? '')) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        {{-- S. BARANG BUKTI --}}
                        <div class="section-subhead d-flex justify-content-between align-items-center mt-4">
                            <span>S. Barang Bukti</span>
                            <button type="button" class="btn btn-sm btn-dark-blue" id="btnAddBb">
                                + Tambah Barang Bukti
                            </button>
                        </div>
                        <div id="bbContainer">
                            @php
                                $bbList = $document->barang_bukti ?? [];
                            @endphp
                            @foreach ($bbList as $idx => $bb)
                                <div class="repeater-card row-bb" data-index="{{ $idx }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="fw-bold text-dark m-0">Barang Bukti {{ $loop->iteration }}:</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-bb">Hapus</button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold">Disita Dari:</label>
                                        <input type="text" class="form-control form-control-sm" name="barang_bukti[{{ $idx }}][disita_dari]"
                                            value="{{ old('barang_bukti.' . $idx . '.disita_dari', $bb['disita_dari'] ?? '') }}" required placeholder="Nama pemilik / pihak yang disita">
                                    </div>
                                    <label class="small fw-bold">Rincian Barang Bukti (a, b, c...):</label>
                                    <textarea class="form-control form-control-sm" name="barang_bukti[{{ $idx }}][keterangan]" rows="4" required placeholder="Tuliskan rincian per baris">{{ old('barang_bukti.' . $idx . '.keterangan', is_array($bb['keterangan'] ?? '') ? implode("\n", $bb['keterangan']) : ($bb['keterangan'] ?? '')) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        {{-- Fakta Tambahan L s/d R --}}
                        <div class="section-subhead mt-4">Fakta-Fakta Tambahan (L s/d R, T, U):</div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">L. Konfrontasi:</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_konfrontasi"
                                    value="{{ old('fakta_konfrontasi', $document->fakta_konfrontasi ?? 'Tidak dilaksanakan konfrontasi;') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">M. Rekonstruksi:</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_rekonstruksi"
                                    value="{{ old('fakta_rekonstruksi', $document->fakta_rekonstruksi ?? 'Telah dilaksanakan rekonstruksi perkara pada TKP;') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">N. Daftar Pencarian Orang (DPO):</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_dpo"
                                    value="{{ old('fakta_dpo', $document->fakta_dpo ?? 'Nihil;') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">O. Daftar Pencarian Barang (DPB):</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_dpb"
                                    value="{{ old('fakta_dpb', $document->fakta_dpb ?? 'Nihil;') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">P. Red Notice:</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_red_notice"
                                    value="{{ old('fakta_red_notice', $document->fakta_red_notice ?? 'Nihil;') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">Q. Pencegahan ke Luar Negeri:</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_pencegahan"
                                    value="{{ old('fakta_pencegahan', $document->fakta_pencegahan ?? 'Nihil;') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold small">R. Penangkalan dari Luar Negeri:</label>
                                <input type="text" class="form-control form-control-sm" name="fakta_penangkalan"
                                    value="{{ old('fakta_penangkalan', $document->fakta_penangkalan ?? 'Nihil;') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold form-label small">T. Penghentian Penyidikan:</label>
                            @php
                                $penghentianVal = $document->fakta_penghentian_penyidikan ?? "1. Surat Perintah Penghentian Penyidikan;\n2. Surat Ketetapan Penghentian Penyidikan;\n3. Surat Pemberitahuan Penghentian Penyidikan Kepada Kejaksaan Dengan Tembusan Pelapor/Korban, Terlapor/Tersangka;\n4. Surat Pemberitahuan Penghentian Penyidikan Kepada Pelapor (SP2HP).";
                                $penghentianText = is_array($penghentianVal) ? implode("\n", $penghentianVal) : $penghentianVal;
                            @endphp
                            <textarea class="form-control" name="fakta_penghentian_penyidikan[]" rows="4">{{ old('fakta_penghentian_penyidikan.0', $penghentianText) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">IV. Pembahasan & V. Kesimpulan<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        {{-- Analisa Kasus --}}
                        <div class="section-subhead">IV. A. Analisa Kasus<span class="text-danger">*</span></div>
                        <div class="mb-4">
                            <label class="fw-bold form-label small">Uraian Sintesis Analisa Kasus:</label>
                            @php
                                $analisaKasusVal = $document->analisa_kasus ?? '';
                                $analisaKasusText = is_array($analisaKasusVal) ? implode("\n\n", $analisaKasusVal) : $analisaKasusVal;
                            @endphp
                            <textarea class="form-control" name="analisa_kasus[]" rows="5" required>{{ old('analisa_kasus.0', $analisaKasusText) }}</textarea>
                        </div>

                        {{-- Analisa Yuridis --}}
                        <div class="section-subhead mt-4">IV. B. Analisa Yuridis (Pembuktian Unsur-Unsur Delik)<span class="text-danger">*</span></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold form-label small">Tersangka:</label>
                                <input type="text" class="form-control form-control-sm" name="analisa_yuridis[tersangka_nama]"
                                    value="{{ old('analisa_yuridis.tersangka_nama', $document->analisa_yuridis['tersangka_nama'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label small">Pasal yang Dibuktikan:</label>
                                <input type="text" class="form-control form-control-sm" name="analisa_yuridis[pasal]"
                                    value="{{ old('analisa_yuridis.pasal', $document->analisa_yuridis['pasal'] ?? 'Pasal 310 ayat (4) UU No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan') }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="fw-bold form-label small">Uraian Pembuktian Unsur-Unsur Delik (a, b, c...):<span class="text-danger">*</span></label>
                            @php
                                $unsurVal = $document->analisa_yuridis['unsur_unsur'] ?? '';
                                $unsurText = is_array($unsurVal) ? implode("\n\n", $unsurVal) : $unsurVal;
                            @endphp
                            <textarea class="form-control" name="analisa_yuridis[unsur_unsur][]" rows="6" required>{{ old('analisa_yuridis.unsur_unsur.0', $unsurText) }}</textarea>
                        </div>

                        {{-- Kesimpulan --}}
                        <div class="section-subhead mt-4">V. Kesimpulan<span class="text-danger">*</span></div>
                        <div class="mb-3">
                            @php
                                $kesimpulanVal = $document->kesimpulan ?? '';
                                $kesimpulanText = is_array($kesimpulanVal) ? implode("\n\n", $kesimpulanVal) : $kesimpulanVal;
                            @endphp
                            <textarea class="form-control" name="kesimpulan[]" rows="5" required>{{ old('kesimpulan.0', $kesimpulanText) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title fw-bold m-0">Pejabat Penandatangan<span class="text-danger">*</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- PENANDATANGAN 1: ATASAN PENYIDIK --}}
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold text-dark-blue mb-3">1. Mengetahui: Atasan Penyidik Selaku Penyidik<span class="text-danger">*</span></h6>

                                <div class="mb-3">
                                    <label class="fw-bold form-label" for="select_atasan">Pilih Pejabat Atasan<span class="text-danger">*</span></label>
                                    <select id="select_atasan" class="form-control select2bs4" style="width: 100%;">
                                        <option value="">-- Pilih Atasan Penyidik --</option>
                                        @if ($authorizedSignatories->isNotEmpty())
                                            <optgroup label="Pejabat Berwenang (Kasat / Kanit)">
                                                @foreach ($authorizedSignatories as $sig)
                                                    @php
                                                        $sName = trim(($sig->full_name ?? '') ?: ($sig->first_name . ' ' . ($sig->last_name ?? '')));
                                                        $sRank = is_object($sig->rank) ? ($sig->rank->name ?? '') : (is_string($sig->rank) ? $sig->rank : ($sig->rank_name ?? ''));
                                                        $sPos = is_object($sig->position) ? ($sig->position->name ?? '') : (is_string($sig->position) ? $sig->position : ($sig->position_name ?? 'KASAT LANTAS'));
                                                        $rawNrp = $sig->register_number ?? ($sig->nrp ?? ($sig->officer->register_number ?? ($sig->officer->nrp ?? ($sig->id ?? ''))));
                                                        $sNrp = preg_replace('/[^0-9]/', '', (string)$rawNrp) ?: $rawNrp;
                                                    @endphp
                                                    <option value="{{ $sig->id }}"
                                                        data-name="{{ $sName }}"
                                                        data-rank="{{ $sRank }}"
                                                        data-nrp="{{ $sNrp }}"
                                                        data-position="{{ $sPos }}">
                                                        {{ $sName }} ({{ $sRank ?: '-' }} / NRP: {{ $sNrp ?: '-' }}) - {{ $sPos }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="fw-bold form-label small">Nama Atasan Penyidik<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="atasan_penyidik_nama" name="atasan_penyidik_nama"
                                        value="{{ old('atasan_penyidik_nama', $document->atasan_penyidik_nama) }}" required readonly placeholder="Otomatis dari Pilih Pejabat">
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold form-label small">Pangkat Atasan<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="atasan_penyidik_pangkat" name="atasan_penyidik_pangkat"
                                        value="{{ old('atasan_penyidik_pangkat', $document->atasan_penyidik_pangkat) }}" required readonly placeholder="Contoh: AKP">
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold form-label small">NRP Atasan<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="atasan_penyidik_nrp" name="atasan_penyidik_nrp"
                                        value="{{ old('atasan_penyidik_nrp', $document->atasan_penyidik_nrp) }}" required readonly placeholder="Contoh: 84070641">
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold form-label small">Jabatan Atasan<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="atasan_penyidik_jabatan" name="atasan_penyidik_jabatan"
                                        value="{{ old('atasan_penyidik_jabatan', $document->atasan_penyidik_jabatan ?? 'ATASAN PENYIDIK Selaku Penyidik') }}" required readonly>
                                </div>
                            </div>

                            {{-- PENANDATANGAN 2: YANG MEMBUAT --}}
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark-blue mb-3">2. Yang Membuat: Penyidik / Penyidik Pembantu<span class="text-danger">*</span></h6>

                                <div class="mb-3">
                                    <label class="fw-bold form-label" for="select_pembuat">Pilih Pejabat Pembuat<span class="text-danger">*</span></label>
                                    <select id="select_pembuat" class="form-control select2bs4" style="width: 100%;">
                                        <option value="">-- Pilih Penyidik Pembuat --</option>
                                        @if ($officers->isNotEmpty())
                                            <optgroup label="Daftar Penyidik / Penyidik Pembantu">
                                                @foreach ($officers as $off)
                                                    @php
                                                        $oName = trim(($off->full_name ?? '') ?: ($off->first_name . ' ' . ($off->last_name ?? '')));
                                                        $oRank = is_object($off->rank) ? ($off->rank->name ?? '') : (is_string($off->rank) ? $off->rank : ($off->rank_name ?? ''));
                                                        $oPos = is_object($off->position) ? ($off->position->name ?? '') : (is_string($off->position) ? $off->position : ($off->position_name ?? 'Penyidik / Penyidik Pembantu'));
                                                        $rawNrp = $off->register_number ?? ($off->nrp ?? ($off->officer->register_number ?? ($off->officer->nrp ?? ($off->id ?? ''))));
                                                        $oNrp = preg_replace('/[^0-9]/', '', (string)$rawNrp) ?: $rawNrp;
                                                    @endphp
                                                    <option value="{{ $off->id }}"
                                                        data-name="{{ $oName }}"
                                                        data-rank="{{ $oRank }}"
                                                        data-nrp="{{ $oNrp }}"
                                                        data-position="{{ $oPos }}">
                                                        {{ $oName }} ({{ $oRank ?: '-' }} / NRP: {{ $oNrp ?: '-' }}) - {{ $oPos }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="fw-bold form-label small">Nama Pembuat<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="pembuat_nama" name="pembuat_nama"
                                        value="{{ old('pembuat_nama', $document->pembuat_nama) }}" required readonly placeholder="Otomatis dari Pilih Pejabat">
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold form-label small">Pangkat Pembuat<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="pembuat_pangkat" name="pembuat_pangkat"
                                        value="{{ old('pembuat_pangkat', $document->pembuat_pangkat) }}" required readonly placeholder="Contoh: AIPDA / BRIPKA">
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold form-label small">NRP Pembuat<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="pembuat_nrp" name="pembuat_nrp"
                                        value="{{ old('pembuat_nrp', $document->pembuat_nrp) }}" required readonly placeholder="Contoh: 88120179">
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold form-label small">Jabatan Pembuat<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="pembuat_jabatan" name="pembuat_jabatan"
                                        value="{{ old('pembuat_jabatan', $document->pembuat_jabatan ?? 'Penyidik Pembantu') }}" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="text-end mb-4">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-dark-blue px-4 py-2 fw-bold" id="btnSubmitForm">
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
            // Inisialisasi select2
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Pejabat --',
                allowClear: true
            });

            // Auto-fill Atasan Penyidik
            $('#select_atasan').on('change select2:select', function() {
                var sel = $(this).find('option:selected');
                if (!sel || !sel.val()) {
                    $('#atasan_penyidik_nama, #atasan_penyidik_pangkat, #atasan_penyidik_nrp, #atasan_penyidik_jabatan')
                        .prop('readonly', false).removeClass('bg-light');
                    return;
                }
                var name = sel.attr('data-name') || sel.data('name') || '';
                var rank = sel.attr('data-rank') || sel.data('rank') || '';
                var rawNrp = (sel.attr('data-nrp') || sel.data('nrp') || '').toString();
                var nrp = rawNrp.replace(/[^0-9]/g, '') || rawNrp;
                var pos = sel.attr('data-position') || sel.data('position') || 'ATASAN PENYIDIK Selaku Penyidik';

                $('#atasan_penyidik_nama').val(name);
                $('#atasan_penyidik_pangkat').val(rank);
                $('#atasan_penyidik_nrp').val(nrp);
                $('#atasan_penyidik_jabatan').val(pos);
                $('#atasan_penyidik_nama, #atasan_penyidik_pangkat, #atasan_penyidik_nrp, #atasan_penyidik_jabatan')
                    .prop('readonly', true).addClass('bg-light');
            });

            // Auto-fill Pembuat Resume
            $('#select_pembuat').on('change select2:select', function() {
                var sel = $(this).find('option:selected');
                if (!sel || !sel.val()) {
                    $('#pembuat_nama, #pembuat_pangkat, #pembuat_nrp, #pembuat_jabatan')
                        .prop('readonly', false).removeClass('bg-light');
                    return;
                }
                var name = sel.attr('data-name') || sel.data('name') || '';
                var rank = sel.attr('data-rank') || sel.data('rank') || '';
                var rawNrp = (sel.attr('data-nrp') || sel.data('nrp') || '').toString();
                var nrp = rawNrp.replace(/[^0-9]/g, '') || rawNrp;
                var pos = sel.attr('data-position') || sel.data('position') || 'Penyidik / Penyidik Pembantu';

                $('#pembuat_nama').val(name);
                $('#pembuat_pangkat').val(rank);
                $('#pembuat_nrp').val(nrp);
                $('#pembuat_jabatan').val(pos);
                $('#pembuat_nama, #pembuat_pangkat, #pembuat_nrp, #pembuat_jabatan')
                    .prop('readonly', true).addClass('bg-light');
            });

            // Dynamic Kerugian Repeater
            var kerugianAlphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            $('#btnAddKerugian').on('click', function() {
                var count = $('#kerugianListContainer .row-kerugian').length;
                var ch = kerugianAlphabet[count] || (count + 1);
                var html = `
                    <div class="input-group mb-2 row-kerugian">
                        <span class="input-group-text fw-bold">${ch}</span>
                        <input type="text" class="form-control" name="perkara[daftar_kerugian][]" placeholder="Uraian kerugian..." required>
                        <button type="button" class="btn btn-outline-danger btn-remove-kerugian">Hapus</button>
                    </div>`;
                $('#kerugianListContainer').append(html);
            });

            $(document).on('click', '.btn-remove-kerugian', function() {
                if ($('#kerugianListContainer .row-kerugian').length <= 1) {
                    alert('Minimal 1 rincian kerugian.');
                    return;
                }
                $(this).closest('.row-kerugian').remove();
                $('#kerugianListContainer .row-kerugian').each(function(idx) {
                    var ch = kerugianAlphabet[idx] || (idx + 1);
                    $(this).find('.input-group-text').text(ch);
                });
            });

            // Dynamic Saksi Repeater
            $('#btnAddSaksi').on('click', function() {
                var idx = $('#saksiContainer .row-saksi').length;
                var num = idx + 1;
                var html = `
                    <div class="repeater-card row-saksi" data-index="${idx}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-primary m-0">Saksi ${num}:</label>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-saksi">Hapus</button>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm fw-bold" name="keterangan_saksi[${idx}][nama]" required placeholder="Nama Saksi beserta gelar">
                        </div>
                        <label class="small fw-bold">Poin-poin Keterangan (a, b, c...):</label>
                        <textarea class="form-control form-control-sm" name="keterangan_saksi[${idx}][keterangan]" rows="4" required placeholder="Tuliskan keterangan per baris"></textarea>
                    </div>`;
                $('#saksiContainer').append(html);
            });

            $(document).on('click', '.btn-remove-saksi', function() {
                if ($('#saksiContainer .row-saksi').length <= 1) {
                    alert('Minimal harus ada 1 data saksi.');
                    return;
                }
                $(this).closest('.row-saksi').remove();
                $('#saksiContainer .row-saksi').each(function(index) {
                    $(this).find('label.text-primary').text('Saksi ' + (index + 1) + ':');
                });
            });

            // Dynamic Ahli Repeater
            $('#btnAddAhli').on('click', function() {
                var idx = $('#ahliContainer .row-ahli').length;
                var num = idx + 1;
                var html = `
                    <div class="repeater-card row-ahli" data-index="${idx}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-success m-0">Ahli ${num}:</label>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ahli">Hapus</button>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm fw-bold" name="keterangan_ahli[${idx}][nama]" required placeholder="Nama Ahli dan Bidang Keahlian">
                        </div>
                        <label class="small fw-bold">Poin-poin Keterangan (a, b, c...):</label>
                        <textarea class="form-control form-control-sm" name="keterangan_ahli[${idx}][keterangan]" rows="4" placeholder="Tuliskan keterangan per baris"></textarea>
                    </div>`;
                $('#ahliContainer').append(html);
            });

            $(document).on('click', '.btn-remove-ahli', function() {
                $(this).closest('.row-ahli').remove();
                $('#ahliContainer .row-ahli').each(function(index) {
                    $(this).find('label.text-success').text('Ahli ' + (index + 1) + ':');
                });
            });

            // Dynamic Tersangka Repeater
            $('#btnAddTersangka').on('click', function() {
                var idx = $('#tersangkaContainer .row-tersangka').length;
                var num = idx + 1;
                var html = `
                    <div class="repeater-card row-tersangka" data-index="${idx}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-danger m-0">Tersangka ${num}:</label>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-tersangka">Hapus</button>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm fw-bold" name="keterangan_tersangka[${idx}][nama]" required placeholder="Nama lengkap Tersangka">
                        </div>
                        <label class="small fw-bold">Poin-poin Keterangan (a, b, c...):</label>
                        <textarea class="form-control form-control-sm" name="keterangan_tersangka[${idx}][keterangan]" rows="4" required placeholder="Tuliskan keterangan per baris"></textarea>
                    </div>`;
                $('#tersangkaContainer').append(html);
            });

            $(document).on('click', '.btn-remove-tersangka', function() {
                if ($('#tersangkaContainer .row-tersangka').length <= 1) {
                    alert('Minimal harus ada 1 data tersangka.');
                    return;
                }
                $(this).closest('.row-tersangka').remove();
                $('#tersangkaContainer .row-tersangka').each(function(index) {
                    $(this).find('label.text-danger').text('Tersangka ' + (index + 1) + ':');
                });
            });

            // Dynamic Barang Bukti Repeater
            $('#btnAddBb').on('click', function() {
                var idx = $('#bbContainer .row-bb').length;
                var num = idx + 1;
                var html = `
                    <div class="repeater-card row-bb" data-index="${idx}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-dark m-0">Barang Bukti ${num}:</label>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-bb">Hapus</button>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Disita Dari:</label>
                            <input type="text" class="form-control form-control-sm" name="barang_bukti[${idx}][disita_dari]" required placeholder="Nama pemilik / pihak yang disita">
                        </div>
                        <label class="small fw-bold">Rincian Barang Bukti (a, b, c...):</label>
                        <textarea class="form-control form-control-sm" name="barang_bukti[${idx}][keterangan]" rows="4" required placeholder="Tuliskan rincian per baris"></textarea>
                    </div>`;
                $('#bbContainer').append(html);
            });

            $(document).on('click', '.btn-remove-bb', function() {
                $(this).closest('.row-bb').remove();
                $('#bbContainer .row-bb').each(function(index) {
                    $(this).find('label.text-dark').text('Barang Bukti ' + (index + 1) + ':');
                });
            });
        });
    </script>
@endpush
