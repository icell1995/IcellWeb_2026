@php
    $_title = 'Surat Pemberitahuan Perkembangan Hasil Penyidikan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* FORCE HIDE all dynamic sections by default */
        .dynamic-section {
            display: none !important;
        }
        
        /* ONLY show when has active class */
        .dynamic-section.active {
            display: block !important;
        }
        
        /* Hide sections until SP2HP type is selected */
        .hidden-until-type-selected {
            display: none !important;
        }
        
        .hidden-until-type-selected.show-section {
            display: block !important;
        }
        
        /* Ensure animation works */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .dynamic-section.active {
            animation: fadeIn 0.3s ease-in;
        }
        
        /* Purple color for A6 */
        .bg-purple {
            background-color: #6f42c1 !important;
            color: white !important;
        }
        
        /* Sticky alert */
        .alert-sp2hp-reg {
            position: sticky;
            top: 70px;
            z-index: 999;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i>
        Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Pemberitahuan Perkembangan Hasil Penyidikan (SP2HP)</h5>

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

            <!-- error alert -->
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
            <form
                action="{{ isset($isEdit) && $isEdit ? route('doc.sp2hp-document.update', ['id' => $sp2hp->id]) : route('doc.sp2hp-document.store', ['accident_id' => $accident->id]) }}"
                method="POST" enctype="multipart/form-data" id="sp2hpForm">
                @csrf
                @if(isset($isEdit) && $isEdit)
                    @method('PUT')
                    <input type="hidden" name="sp2hp_id" id="sp2hp_id" value="{{ $sp2hp->id }}">
                @endif
                <input type="hidden" name="accident_id" id="accident_id" value="{{ $accident->id }}">
                
                {{-- Hidden fields untuk data pelapor (akan diisi dari data penerima) --}}
                <input type="hidden" name="pelapor_nama" id="pelapor_nama" value="{{ old('pelapor_nama', $sp2hp->pelapor_nama ?? '') }}">
                <input type="hidden" name="pelapor_alamat" id="pelapor_alamat" value="{{ old('pelapor_alamat', $sp2hp->pelapor_alamat ?? '') }}">
                
                {{-- Container untuk multiple penyidik (akan diisi dari data A1 personnel) --}}
                <div id="penyidikHiddenContainer"></div>

                <!-- ====== FORM SP2HP (diadaptasi dari card yang Anda berikan) ====== -->

                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-file-alt"></i> Informasi Surat SP2HP</h6>
                    </div>
                    <div class="card-body">
                        <div class="row"> 
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor LP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nomor_lp" name="nomor_lp"
                                        value="{{ old('nomor_lp', $accident->no_lp ?? '') }}" readonly>
                                    <span class="text-danger error-text nomor_lp_err">@error('nomor_lp'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal LP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tanggal_lp" name="tanggal_lp"
                                        value="{{ old('tanggal_lp', isset($accident->accident_date) ? \Carbon\Carbon::parse($accident->accident_date)->format('d-m-Y') : '') }}" readonly>
                                    <span class="text-danger error-text tanggal_lp_err">@error('tanggal_lp'){{ $message }}@enderror</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pilih Jenis SP2HP <span class="text-danger">*</span></label>
                                    <select class="form-control form-select" id="tipe_sp2hp" name="tipe_sp2hp">
                                        <option value="">-- Pilih Tipe SP2HP --</option>
                                        @php $selectedTipe = old('tipe_sp2hp', $sp2hp->tipe_sp2hp ?? ''); @endphp
                                        <option value="A1" {{ $selectedTipe == 'A1' ? 'selected' : '' }} data-required="">📋 A-1: Pemberitahuan Hasil Penelitian Laporan (Awal)</option>
                                        <option value="A2" {{ $selectedTipe == 'A2' ? 'selected' : '' }} data-required="A1">🔍 A-2: Hasil Penyelidikan (Belum Dapat Ditingkatkan)</option>
                                        <option value="A3" {{ $selectedTipe == 'A3' ? 'selected' : '' }} data-required="A2">⚖️ A-3: Pemberitahuan Dimulainya Penyidikan (SPDP Pelapor)</option>
                                        <option value="A4" {{ $selectedTipe == 'A4' ? 'selected' : '' }} data-required="A3">📊 A-4: Perkembangan Hasil Penyidikan (Berjalan)</option>
                                        <option value="A5" {{ $selectedTipe == 'A5' ? 'selected' : '' }} data-required="A4">🛑 A-5: Pemberitahuan Penghentian Penyidikan (SP3)</option>
                                        <option value="A6" {{ $selectedTipe == 'A6' ? 'selected' : '' }} data-required="A4" data-alt-path="true">📤 A-6: Pemberitahuan Pengiriman Berkas (Tahap 1)</option>
                                        <option value="A7" {{ $selectedTipe == 'A7' ? 'selected' : '' }} data-required="A6">✅ A-7: Pemberitahuan Penyelesaian Perkara (Tahap 2)</option>
                                    </select>
                                    <small class="form-text text-muted" id="deskripsi_tipe">
                                        <i class="fas fa-info-circle"></i> Pilih jenis SP2HP sesuai tahapan penanganan perkara
                                    </small>
                                    <span class="text-danger error-text tipe_sp2hp_err">@error('tipe_sp2hp'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tingkat Kasus <span class="text-danger">*</span></label>
                                    <select class="form-control form-select" id="tingkat_kasus" name="tingkat_kasus">
                                        <option value="">-- Pilih Tingkat --</option>
                                        @php $selectedTingkat = old('tingkat_kasus', $sp2hp->tingkat_kasus ?? ''); @endphp
                                        <option value="RINGAN" {{ $selectedTingkat=='RINGAN' ? 'selected' : '' }}>RINGAN</option>
                                        <option value="SEDANG" {{ $selectedTingkat=='SEDANG' ? 'selected' : '' }}>SEDANG</option>
                                        <option value="BERAT" {{ $selectedTingkat=='BERAT' ? 'selected' : '' }}>BERAT</option>
                                    </select>
                                    <span class="text-danger error-text tingkat_kasus_err">@error('tingkat_kasus'){{ $message }}@enderror</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box untuk Tipe SP2HP -->
                        <div class="alert alert-info border-0 shadow-sm" id="info_tipe_sp2hp" style="display: none;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle me-2 mt-1" style="font-size: 1.2em;"></i>
                                <div id="info_display_content" class="flex-grow-1">
                                    <!-- Konten akan diupdate oleh Javascript -->
                                </div>
                            </div>
                        </div>

                        <!-- Regulasi Warning Box -->
                        <div class="alert alert-warning border-warning" id="regulasi_box" style="display: none;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle me-2 mt-1" style="font-size: 1.2em;"></i>
                                <div>
                                    <strong>⚠️ PERHATIAN - Basis Hukum:</strong><br>
                                    <small>
                                        Pastikan nomor rujukan (Nomor LP, Nomor Sprin Sidik, Nomor Surat sebelumnya) diisi dengan tepat 
                                        karena akan menjadi dasar hukum surat ini sesuai KUHAP dan Perkap No. 14 Tahun 2012 tentang Manajemen Penyidikan Tindak Pidana.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Section Nomor Surat, Tanggal, Tempat & Lokasi (Only for A1 & A4) -->
                        <div id="surat-location-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control editable-a1-a4" id="nomor_surat" name="nomor_surat"
                                            placeholder="SP2HP/001/I/UNIT/2025" value="{{ old('nomor_surat', $sp2hp->nomor_surat ?? '') }}">
                                    </div>
                                    <span class="text-danger error-text nomor_surat_err">@error('nomor_surat'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Surat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control editable-a1-a4" id="tanggal_surat" name="tanggal_surat"
                                        placeholder="dd-mm-yyyy" value="{{ old('tanggal_surat', isset($sp2hp->tanggal_surat) ? \Carbon\Carbon::parse($sp2hp->tanggal_surat)->format('d-m-Y') : '') }}" autocomplete="off">
                                    <span class="text-danger error-text tanggal_surat_err">@error('tanggal_surat'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tempat Surat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control editable-a1-a4 bg-light" id="tempat_surat" name="tempat_surat"
                                        placeholder="Akan terisi otomatis" value="{{ old('tempat_surat', $sp2hp->tempat_surat ?? '') }}" readonly>
                                    <small class="form-text text-muted">Field ini akan terisi otomatis berdasarkan pilihan lokasi</small>
                                    <span class="text-danger error-text tempat_surat_err">@error('tempat_surat'){{ $message }}@enderror</span>
                                </div>
                            </div>
                        </div>

                        <!-- Location Selectors Row -->
                        <div class="row mb-3">
                            <!-- Hidden field: Default Indonesia -->
                            <input type="hidden" id="negara_tempat_surat" name="negara_tempat_surat" value="{{ $countries->where('name', 'INDONESIA')->first()->id ?? '' }}">
                            
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <select class="form-control form-select select2-location" id="provinsi_tempat_surat" name="provinsi_tempat_surat">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <select class="form-control form-select select2-location" id="kota_tempat_surat" name="kota_tempat_surat">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label">Kecamatan</label>
                                    <select class="form-control form-select select2-location" id="kecamatan_tempat_surat" name="kecamatan_tempat_surat">
                                        <option value="">-- Pilih Kecamatan (Opsional) --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        </div>{{-- End surat-location-section --}}
                    </div>
                </div>

                <div class="accordion mb-3" id="accordionPenerimaSp2hp">

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPenerima">
                            <button class="accordion-button bg-secondary text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapsePenerima"
                                aria-expanded="true" aria-controls="collapsePenerima">
                                <i class="fas fa-user-tie me-2"></i> Data Penerima SP2HP (KEPADA)
                            </button>
                        </h2>

                        <div id="collapsePenerima" class="accordion-collapse collapse show"
                            aria-labelledby="headingPenerima" data-bs-parent="#accordionPenerimaSp2hp">

                            <div class="accordion-body">

                                @if(isset($existingPenerima) && count($existingPenerima) > 0)
                                    {{-- EXISTING PENERIMA - READ ONLY --}}
                                    <div class="alert alert-success border-0 shadow-sm mb-3">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Data Penerima Sudah Ada!</strong><br>
                                        <small>Data penerima SP2HP sudah diinput sebelumnya. Tidak perlu input ulang untuk tipe SP2HP selanjutnya.</small>
                                    </div>
                                    
                                    <h6 class="fw-bold"><i class="fas fa-list"></i> Daftar Penerima SP2HP yang Sudah Terdaftar</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover" id="penerimaTable">
                                            <thead class="table-success">
                                                <tr class="text-center">
                                                    <th width="5%">No</th>
                                                    <th width="15%">Nama</th>
                                                    <th width="12%">Jenis Identitas</th>
                                                    <th width="15%">No. Identitas</th>
                                                    <th width="10%">No. Telepon</th>
                                                    <th width="20%">Alamat</th>
                                                    <th width="8%">Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($existingPenerima as $index => $penerima)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $penerima->name }}</td>
                                                    <td class="text-center">{{ $penerima->identityType->name ?? '-' }}</td>
                                                    <td>{{ $penerima->identity_number ?? '-' }}</td>
                                                    <td>{{ $penerima->phone_number ?? '-' }}</td>
                                                    <td><small>{{ \Illuminate\Support\Str::limit($penerima->address ?? '-', 70) }}</small></td>
                                                    <td class="text-center">
                                                        {{-- Hidden inputs untuk data existing penerima --}}
                                                        @php
                                                            $penerimaFields = [
                                                                'jenis_identitas' => $penerima->identity_type_id,
                                                                'jenis_identitas_text' => $penerima->identityType->name ?? '',
                                                                'nomor_identitas' => $penerima->identity_number,
                                                                'nama' => $penerima->name,
                                                                'nama_alias' => $penerima->alias_name,
                                                                'tempat_lahir' => $penerima->place_of_birth,
                                                                'tanggal_lahir' => $penerima->date_of_birth,
                                                                'jenis_kelamin' => $penerima->gender_id,
                                                                'jenis_kelamin_text' => $penerima->gender->name ?? '',
                                                                'nama_ayah' => $penerima->father_name,
                                                                'nama_ibu' => $penerima->mother_name,
                                                                'kewarganegaraan' => $penerima->nationality_id,
                                                                'kewarganegaraan_text' => $penerima->nationality->name ?? '',
                                                                'suku' => $penerima->ethnic_id,
                                                                'agama' => $penerima->religion_id,
                                                                'agama_text' => $penerima->religion->name ?? '',
                                                                'pendidikan' => $penerima->education_id,
                                                                'pendidikan_text' => $penerima->education->name ?? '',
                                                                'pekerjaan' => $penerima->job_id,
                                                                'pekerjaan_text' => $penerima->job->name ?? '',
                                                                'status_perkawinan' => $penerima->marital_status_id,
                                                                'nomor_telepon' => $penerima->phone_number,
                                                                'email' => $penerima->email,
                                                                'negara' => $penerima->country_id,
                                                                'provinsi' => $penerima->province_id,
                                                                'kota' => $penerima->regency_id,
                                                                'kecamatan' => $penerima->district_id,
                                                                'kelurahan' => $penerima->village_id,
                                                                'alamat' => $penerima->address,
                                                            ];
                                                        @endphp
                                                        @foreach($penerimaFields as $fieldName => $fieldValue)
                                                            <input type="hidden" name="penerima[{{ $index }}][{{ $fieldName }}]" value="{{ $fieldValue }}">
                                                        @endforeach
                                                        <span class="badge bg-success">Existing</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    {{-- Tombol untuk menambah penerima tambahan --}}
                                    <div class="text-end mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnTambahPenerimaLain">
                                            <i class="fas fa-plus"></i> Tambah Penerima Lain
                                        </button>
                                    </div>
                                    
                                    {{-- Form untuk menambah penerima tambahan (initially hidden) --}}
                                    <div id="formTambahPenerimaLain" style="display: none;">
                                        <hr class="my-3">
                                        <div class="alert alert-warning border-0 shadow-sm mb-3">
                                            <i class="fas fa-user-plus"></i>
                                            <strong>Tambah Penerima Baru</strong><br>
                                            <small>Form di bawah ini untuk menambahkan penerima SP2HP tambahan.</small>
                                        </div>
                                        
                                        {{-- Semua form input penerima akan muncul di sini ketika tombol diklik --}}
                                        {{-- Form akan di-clone dari template di bawah --}}
                                    </div>
                                @else
                                    {{-- NEW PENERIMA - INPUT FORM --}}
                                    <div id="penerimaFormContainer">
                                    <div class="alert alert-info border-0 shadow-sm mb-3">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Informasi:</strong> Penerima SP2HP biasanya adalah atasan langsung atau pejabat berwenang (Kapolres, Kapolda, dll) atau pelapor/korban sesuai regulasi.
                                        </small>
                                    </div>

                                <!-- Dropdown Pilih dari Data DORS Victims -->
                                @if(isset($dorsVictims) && count($dorsVictims) > 0)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="fw-bold form-label">Pilih dari Data Korban/Pelapor (DORS)</label>
                                        <select class="form-control select2-penerima" id="penerima_dors_select">
                                            <option value="">-- Pilih Korban/Pelapor dari DORS atau Isi Manual --</option>
                                            @foreach($dorsVictims as $victim)
                                                <option value="{{ $victim->id }}"
                                                    data-jenis-identitas="{{ $victim->jenis_identitas ?? '' }}"
                                                    data-nik="{{ $victim->nik ?? '' }}"
                                                    data-nama="{{ $victim->nama ?? '' }}"
                                                    data-tempat-lahir="{{ $victim->tempat_lahir ?? '' }}"
                                                    data-tgl-lahir="{{ $victim->tgl_lahir ? \Carbon\Carbon::parse($victim->tgl_lahir)->format('Y-m-d') : '' }}"
                                                    data-gender="{{ $victim->gender ?? '' }}"
                                                    data-kewarganegaraan="{{ $victim->kewarganegaraan ?? '' }}"
                                                    data-suku="{{ $victim->suku ?? '' }}"
                                                    data-pekerjaan="{{ $victim->pekerjaan ?? '' }}"
                                                    data-agama="{{ $victim->agama ?? '' }}"
                                                    data-pendidikan="{{ $victim->pendidikan_terakhir ?? '' }}"
                                                    data-no-hp="{{ $victim->no_hp ?? '' }}"
                                                    data-alamat="{{ $victim->alamat ?? '' }}"
                                                    data-alamat-non-nkri="{{ $victim->alamat_non_nkri ?? '' }}">
                                                    {{ $victim->nama ?? '-' }} - {{ $victim->nik ?? '-' }} ({{ $victim->status_korban ?? 'Korban' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Pilih korban/pelapor dari data DORS untuk mengisi otomatis, atau isi manual di bawah</small>
                                    </div>
                                </div>
                                @endif

                                <!-- Jenis Identitas & Nomor Identitas -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Jenis Identitas<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_jenis_identitas" name="penerima_jenis_identitas">
                                                <option value="">-- Pilih Jenis Identitas --</option>
                                                <option value="10" {{ old('penerima_jenis_identitas') == '10' ? 'selected' : '' }}>KTP</option>
                                                <option value="12" {{ old('penerima_jenis_identitas') == '12' ? 'selected' : '' }}>PASSPORT</option>
                                                <option value="13" {{ old('penerima_jenis_identitas') == '13' ? 'selected' : '' }}>SIM</option>
                                                <option value="15" {{ old('penerima_jenis_identitas') == '15' ? 'selected' : '' }}>TIDAK DIKETAHUI</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Nomor Identitas<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="penerima_nomor_identitas" name="penerima_nomor_identitas"
                                                placeholder="Nomor Identitas" value="{{ old('penerima_nomor_identitas') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama & Alias -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Nama<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="penerima_nama" name="penerima_nama" placeholder="Nama Lengkap" value="{{ old('penerima_nama') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Nama Alias</label>
                                            <input type="text" class="form-control" id="penerima_nama_alias" name="penerima_nama_alias" placeholder="Nama Alias (Opsional)" value="{{ old('penerima_nama_alias') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tempat & Tanggal Lahir -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Tempat Lahir<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="penerima_tempat_lahir" name="penerima_tempat_lahir"
                                                placeholder="Tempat Lahir" value="{{ old('penerima_tempat_lahir') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Tanggal Lahir<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="penerima_tanggal_lahir" name="penerima_tanggal_lahir"
                                                placeholder="YYYY-MM-DD" value="{{ old('penerima_tanggal_lahir') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_jenis_kelamin" name="penerima_jenis_kelamin">
                                                <option value="">-- Pilih Jenis Kelamin --</option>
                                                <option value="1" {{ old('penerima_jenis_kelamin') == '1' ? 'selected' : '' }}>Laki-Laki</option>
                                                <option value="2" {{ old('penerima_jenis_kelamin') == '2' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ayah & Ibu -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Ayah Kandung</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" class="form-control w-75" id="nama_ayah" name="nama_ayah"
                                                    placeholder="Nama Ayah Kandung" value="{{ old('nama_ayah') }}">
                                                <div class="form-check ms-2">
                                                    <input class="form-check-input" type="checkbox" id="unknown_father" name="unknown_father"
                                                        {{ old('unknown_father') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="unknown_father">Tidak diketahui</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Ibu Kandung</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" class="form-control w-75" id="nama_ibu" name="nama_ibu"
                                                    placeholder="Nama Ibu Kandung" value="{{ old('nama_ibu') }}">
                                                <div class="form-check ms-2">
                                                    <input class="form-check-input" type="checkbox" id="unknown_mother" name="unknown_mother"
                                                        {{ old('unknown_mother') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="unknown_mother">Tidak diketahui</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kewarganegaraan & Suku -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Kewarganegaraan<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_kewarganegaraan" name="penerima_kewarganegaraan">
                                                <option value="">-- Pilih Kewarganegaraan --</option>
                                                @foreach($nationalities as $nationality)
                                                <option value="{{ $nationality->id }}" {{ old('penerima_kewarganegaraan') == $nationality->id ? 'selected' : '' }}>
                                                    {{ $nationality->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Suku<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_suku" name="penerima_suku">
                                                <option value="">-- Pilih Suku --</option>
                                                @foreach($ethnics as $ethnic)
                                                <option value="{{ $ethnic->id }}" {{ old('penerima_suku') == $ethnic->id ? 'selected' : '' }}>
                                                    {{ $ethnic->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pendidikan & Pekerjaan -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Pendidikan<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_pendidikan" name="penerima_pendidikan">
                                                <option value="">-- Pilih Pendidikan --</option>
                                                @foreach($educations as $education)
                                                <option value="{{ $education->id }}" {{ old('penerima_pendidikan') == $education->id ? 'selected' : '' }}>
                                                    {{ $education->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Pekerjaan<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_pekerjaan" name="penerima_pekerjaan">
                                                <option value="">-- Pilih Pekerjaan --</option>
                                                @foreach($jobs as $job)
                                                <option value="{{ $job->id }}" {{ old('penerima_pekerjaan') == $job->id ? 'selected' : '' }}>
                                                    {{ $job->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agama & Status Kawin -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Agama<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_agama" name="penerima_agama">
                                                <option value="">-- Pilih Agama --</option>
                                                @foreach($religions as $religion)
                                                <option value="{{ $religion->id }}" {{ old('penerima_agama') == $religion->id ? 'selected' : '' }}>
                                                    {{ $religion->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Status Kawin<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_status_kawin" name="penerima_status_kawin">
                                                <option value="">-- Pilih Status Kawin --</option>
                                                @foreach($maritalStatuses as $maritalStatus)
                                                <option value="{{ $maritalStatus->id }}" {{ old('penerima_status_kawin') == $maritalStatus->id ? 'selected' : '' }}>
                                                    {{ $maritalStatus->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Telepon & Email -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Nomor Telepon<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="penerima_nomor_telepon" name="penerima_nomor_telepon"
                                                placeholder="Nomor Telepon" value="{{ old('penerima_nomor_telepon') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Email<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="penerima_email" name="penerima_email"
                                                placeholder="Email" value="{{ old('penerima_email') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Negara -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Negara<span class="text-danger">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_negara" name="penerima_negara">
                                                <option value="">-- Pilih Negara --</option>
                                                @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('penerima_negara', $countries->where('name','INDONESIA')->first()->id ?? '') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <small class="text-info"><i class="fas fa-info-circle"></i> Jika memilih negara <strong>selain Indonesia</strong>, field Provinsi/Kota/Kecamatan/Kelurahan tidak perlu diisi (otomatis disembunyikan)</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 indonesia-location-field">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Provinsi<span class="text-danger penerima-required-mark">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_provinsi" name="penerima_provinsi">
                                                <option value="">-- Pilih Provinsi --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kota & Kecamatan -->
                                <div class="row indonesia-location-field">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Kabupaten/Kota<span class="text-danger penerima-required-mark">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_kota" name="penerima_kota">
                                                <option value="">-- Pilih Kabupaten/Kota --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Kecamatan<span class="text-danger penerima-required-mark">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_kecamatan" name="penerima_kecamatan">
                                                <option value="">-- Pilih Kecamatan --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kelurahan & Alamat -->
                                <div class="row">
                                    <div class="col-md-6 indonesia-location-field">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Kelurahan/Desa<span class="text-danger penerima-required-mark">*</span></label>
                                            <select class="form-control form-select select2-penerima" id="penerima_kelurahan" name="penerima_kelurahan">
                                                <option value="">-- Pilih Kelurahan/Desa --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="penerima_alamat_container">
                                        <div class="mb-3">
                                            <label class="fw-bold form-label">Alamat<span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="penerima_alamat" name="penerima_alamat" rows="3"
                                                placeholder="Alamat">{{ old('penerima_alamat') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Tambah Penerima -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-success w-100" type="button" id="addPenerimaButton">
                                            <i class="bi bi-plus-circle"></i> Tambah Penerima ke Daftar
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabel Daftar Penerima -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h6 class="fw-bold"><i class="fas fa-list"></i> Daftar Penerima SP2HP</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover" id="penerimaTable">
                                                <thead class="table-primary">
                                                    <tr class="text-center">
                                                        <th width="5%">No</th>
                                                        <th width="15%">Nama</th>
                                                        <th width="12%">Jenis Identitas</th>
                                                        <th width="15%">No. Identitas</th>
                                                        <th width="10%">No. Telepon</th>
                                                        <th width="20%">Alamat</th>
                                                        <th width="8%">Opsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Data will be added here via JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> Minimal 1 penerima harus ditambahkan. Isi form di atas lalu klik "Tambah Penerima ke Daftar".
                                        </small>
                                    </div>
                                </div>
                                
                                    </div>{{-- End penerimaFormContainer --}}
                                    </div>{{-- End formTambahPenerimaLain --}}
                                @endif {{-- End if existing penerima --}}
                                
                                {{-- TEMPLATE FORM PENERIMA (Hidden, untuk clone) - LENGKAP SAMA DENGAN ORIGINAL --}}
                                <div id="penerimaFormTemplate" style="display: none;">
                                    <div class="card border-success mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-user-plus"></i> Form Tambah Penerima Lain</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info border-0 shadow-sm mb-3">
                                                <small>
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>Informasi:</strong> Penerima SP2HP biasanya adalah atasan langsung atau pejabat berwenang (Kapolres, Kapolda, dll) atau pelapor/korban sesuai regulasi.
                                                </small>
                                            </div>

                                            @if(isset($dorsVictims) && count($dorsVictims) > 0)
                                            <!-- Dropdown Pilih dari Data DORS -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label class="fw-bold form-label">Pilih dari Data Korban/Pelapor (DORS)</label>
                                                    <select class="form-control select2-penerima-clone" id="penerima_dors_select_clone">
                                                        <option value="">-- Pilih Korban/Pelapor dari DORS atau Isi Manual --</option>
                                                        @foreach($dorsVictims as $victim)
                                                            <option value="{{ $victim->id }}"
                                                                data-jenis-identitas="{{ $victim->jenis_identitas ?? '' }}"
                                                                data-nik="{{ $victim->nik ?? '' }}"
                                                                data-nama="{{ $victim->nama ?? '' }}"
                                                                data-tempat-lahir="{{ $victim->tempat_lahir ?? '' }}"
                                                                data-tgl-lahir="{{ $victim->tgl_lahir ? \Carbon\Carbon::parse($victim->tgl_lahir)->format('Y-m-d') : '' }}"
                                                                data-gender="{{ $victim->gender ?? '' }}"
                                                                data-kewarganegaraan="{{ $victim->kewarganegaraan ?? '' }}"
                                                                data-suku="{{ $victim->suku ?? '' }}"
                                                                data-pekerjaan="{{ $victim->pekerjaan ?? '' }}"
                                                                data-agama="{{ $victim->agama ?? '' }}"
                                                                data-pendidikan="{{ $victim->pendidikan_terakhir ?? '' }}"
                                                                data-no-hp="{{ $victim->no_hp ?? '' }}"
                                                                data-alamat="{{ $victim->alamat ?? '' }}"
                                                                data-alamat-non-nkri="{{ $victim->alamat_non_nkri ?? '' }}">
                                                                {{ $victim->nama ?? '-' }} - {{ $victim->nik ?? '-' }} ({{ $victim->status_korban ?? 'Korban' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Pilih korban/pelapor dari data DORS untuk mengisi otomatis, atau isi manual di bawah</small>
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Jenis Identitas & Nomor -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Jenis Identitas<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_jenis_identitas_clone" name="penerima_jenis_identitas_clone">
                                                            <option value="">-- Pilih Jenis Identitas --</option>
                                                            <option value="10">KTP</option>
                                                            <option value="12">PASSPORT</option>
                                                            <option value="13">SIM</option>
                                                            <option value="15">TIDAK DIKETAHUI</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Nomor Identitas<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="penerima_nomor_identitas_clone" name="penerima_nomor_identitas_clone" placeholder="Nomor Identitas">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Nama & Alias -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Nama<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="penerima_nama_clone" name="penerima_nama_clone" placeholder="Nama Lengkap">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Nama Alias</label>
                                                        <input type="text" class="form-control" id="penerima_nama_alias_clone" name="penerima_nama_alias_clone" placeholder="Nama Alias (Opsional)">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tempat & Tanggal Lahir -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Tempat Lahir<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="penerima_tempat_lahir_clone" name="penerima_tempat_lahir_clone" placeholder="Tempat Lahir">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Tanggal Lahir<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control datepicker" id="penerima_tanggal_lahir_clone" name="penerima_tanggal_lahir_clone" placeholder="dd-mm-yyyy">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Jenis Kelamin -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_jenis_kelamin_clone" name="penerima_jenis_kelamin_clone">
                                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                                            <option value="1">Laki-Laki</option>
                                                            <option value="2">Perempuan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ayah & Ibu -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Ayah Kandung</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="text" class="form-control w-75" id="nama_ayah_clone" name="nama_ayah_clone" placeholder="Nama Ayah Kandung">
                                                            <div class="form-check ms-2">
                                                                <input class="form-check-input" type="checkbox" id="unknown_father_clone" name="unknown_father_clone">
                                                                <label class="form-check-label" for="unknown_father_clone">Tidak diketahui</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Ibu Kandung</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="text" class="form-control w-75" id="nama_ibu_clone" name="nama_ibu_clone" placeholder="Nama Ibu Kandung">
                                                            <div class="form-check ms-2">
                                                                <input class="form-check-input" type="checkbox" id="unknown_mother_clone" name="unknown_mother_clone">
                                                                <label class="form-check-label" for="unknown_mother_clone">Tidak diketahui</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kewarganegaraan & Suku -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Kewarganegaraan<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_kewarganegaraan_clone" name="penerima_kewarganegaraan_clone">
                                                            <option value="">-- Pilih Kewarganegaraan --</option>
                                                            @foreach($nationalities as $nationality)
                                                            <option value="{{ $nationality->id }}">
                                                                {{ $nationality->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Suku<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_suku_clone" name="penerima_suku_clone">
                                                            <option value="">-- Pilih Suku --</option>
                                                            @foreach($ethnics as $ethnic)
                                                            <option value="{{ $ethnic->id }}">
                                                                {{ $ethnic->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pendidikan & Pekerjaan -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Pendidikan<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_pendidikan_clone" name="penerima_pendidikan_clone">
                                                            <option value="">-- Pilih Pendidikan --</option>
                                                            @foreach($educations as $education)
                                                            <option value="{{ $education->id }}">
                                                                {{ $education->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Pekerjaan<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_pekerjaan_clone" name="penerima_pekerjaan_clone">
                                                            <option value="">-- Pilih Pekerjaan --</option>
                                                            @foreach($jobs as $job)
                                                            <option value="{{ $job->id }}">
                                                                {{ $job->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Agama & Status Kawin -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Agama<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_agama_clone" name="penerima_agama_clone">
                                                            <option value="">-- Pilih Agama --</option>
                                                            @foreach($religions as $religion)
                                                            <option value="{{ $religion->id }}">
                                                                {{ $religion->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Status Kawin<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_status_kawin_clone" name="penerima_status_kawin_clone">
                                                            <option value="">-- Pilih Status Kawin --</option>
                                                            @foreach($maritalStatuses as $maritalStatus)
                                                            <option value="{{ $maritalStatus->id }}">
                                                                {{ $maritalStatus->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Telepon & Email -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Nomor Telepon<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="penerima_nomor_telepon_clone" name="penerima_nomor_telepon_clone" placeholder="08xxxxxxxxxx">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Email<span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" id="penerima_email_clone" name="penerima_email_clone" placeholder="email@example.com">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Negara -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Negara<span class="text-danger">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_negara_clone" name="penerima_negara_clone">
                                                            <option value="">-- Pilih Negara --</option>
                                                            @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" {{ $country->name == 'INDONESIA' ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-info"><i class="fas fa-info-circle"></i> Jika memilih negara <strong>selain Indonesia</strong>, field Provinsi/Kota/Kecamatan/Kelurahan tidak perlu diisi</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 indonesia-location-field-clone">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Provinsi<span class="text-danger penerima-required-mark-clone">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_provinsi_clone" name="penerima_provinsi_clone">
                                                            <option value="">-- Pilih Provinsi --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kota & Kecamatan -->
                                            <div class="row indonesia-location-field-clone">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Kabupaten/Kota<span class="text-danger penerima-required-mark-clone">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_kota_clone" name="penerima_kota_clone">
                                                            <option value="">-- Pilih Kabupaten/Kota --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Kecamatan<span class="text-danger penerima-required-mark-clone">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_kecamatan_clone" name="penerima_kecamatan_clone">
                                                            <option value="">-- Pilih Kecamatan --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kelurahan & Alamat -->
                                            <div class="row">
                                                <div class="col-md-6 indonesia-location-field-clone">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Kelurahan/Desa<span class="text-danger penerima-required-mark-clone">*</span></label>
                                                        <select class="form-control form-select select2-penerima-clone" id="penerima_kelurahan_clone" name="penerima_kelurahan_clone">
                                                            <option value="">-- Pilih Kelurahan/Desa --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Alamat<span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="penerima_alamat_clone" name="penerima_alamat_clone" rows="3" placeholder="Alamat Lengkap"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tombol Tambah -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-success w-100" type="button" id="addPenerimaButtonClone">
                                                        <i class="bi bi-plus-circle"></i> Tambah Penerima Lain ke Daftar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- End penerimaFormTemplate --}}

                            </div><!-- END BODY -->

                        </div>
                    </div>
                </div>

                <!-- ====== SECTION DINAMIS BERDASARKAN TIPE SP2HP ====== -->
                
                <!-- FORM A1: Pemberitahuan Awal -->
                <div class="card mb-3 dynamic-section" id="section_a1">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-file-alt"></i> Data Spesifik A-1: Pemberitahuan Awal</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 shadow-sm">
                            <strong><i class="fas fa-info-circle"></i> Informasi Tipe A-1:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Awal Penyelidikan</li>
                                <li><strong>Waktu:</strong> Segera setelah Laporan Polisi diterima</li>
                                <li><strong>Fokus:</strong> Memberitahu pelapor bahwa laporan telah diterima dan penyidik telah ditunjuk</li>
                                <li><strong>Data Kunci:</strong> Nama & HP Penyidik yang telah ditunjuk</li>
                            </ul>
                        </div>

                        <h6 class="fw-bold text-primary mt-3">Anggota Personel Penyidik</h6>
                        <div class="alert alert-primary my-3" role="alert">
                            <i class="fas fa-users"></i> <strong>Pilih personel penyidik yang ditugaskan untuk kasus ini.</strong><br>
                            <small>Anda dapat menambahkan lebih dari 1 penyidik. Klik tombol 'Tambah' untuk menambahkan personel ke daftar. <strong>Semua penyidik yang ditambahkan akan tersimpan.</strong></small>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="a1_personnelOption">
                                        <option value="">--Pilih Penyidik--</option>
                                        @if(isset($officers) && count($officers) > 0)
                                            @foreach($officers as $officer)
                                                @php
                                                    $rankName = $officer->rank->name ?? '-';
                                                    $positionName = $officer->position->name ?? '-';
                                                    $policeName = $officer->police->full_name ?? '-';
                                                @endphp
                                                <option value="{{ $officer->id }}"
                                                    data-register-number="{{ $officer->register_number }}"
                                                    data-rank-name="{{ $rankName }}"
                                                    data-name="{{ $officer->full_name }}"
                                                    data-position-name="{{ $positionName }}"
                                                    data-police-name="{{ $policeName }}"
                                                    data-phone="{{ $officer->phone_number ?? '-' }}">
                                                    {{ $officer->register_number }} - {{ $officer->full_name }} | {{ $rankName }} - {{ $positionName }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>-- Data personel tidak tersedia --</option>
                                        @endif
                                    </select>
                                    <button class="btn btn-primary" type="button" id="a1_addPersonnelButton">
                                        <i class="bi bi-plus-circle"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mt-3">
                            <table class="table table-bordered table-responsive-md" id="a1_personnelTable">
                                <thead class="table-primary">
                                    <tr class="text-center">
                                        <th scope="col">NRP</th>
                                        <th scope="col">Pangkat</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">No. HP</th>
                                        <th scope="col">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> <strong>Semua penyidik yang ada di tabel akan disimpan.</strong> Penyidik pertama akan menjadi penyidik utama.
                        </small>
                    </div>
                </div>

                <!-- FORM A2: Hasil Penyelidikan -->
                <div class="card mb-3 dynamic-section" id="section_a2">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-search"></i> Data Spesifik A-2: Hasil Penyelidikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning border-0 shadow-sm">
                            <strong><i class="fas fa-exclamation-triangle"></i> Informasi Tipe A-2:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Hasil Penyelidikan (Hambatan/Henti Lidik)</li>
                                <li><strong>Kondisi:</strong> Belum ditemukan unsur pidana atau bukti belum cukup untuk naik Sidik</li>
                                <li><strong>Data Kunci:</strong> Rujukan surat A1, Fakta Lidik, dan Alasan belum bisa sidik</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Surat Penelitian Awal (A1) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="a2_rujukan_a1" value="{{ old('a2_rujukan_a1', $nomorRujukanA1 ?? '') }}" {{ isset($nomorRujukanA1) && $nomorRujukanA1 ? 'readonly' : '' }}
                                placeholder="Contoh: B/531/VI/2025/Reskrim">
                            <small class="text-muted">{{ isset($nomorRujukanA1) && $nomorRujukanA1 ? 'Nomor surat A1 diambil otomatis dari data sebelumnya' : 'Nomor surat A1 yang telah diterbitkan sebelumnya' }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Surat A1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="a2_tanggal_a1" id="a2_tanggal_a1" value="{{ old('a2_tanggal_a1', $tanggalRujukanA1 ?? '') }}" {{ isset($tanggalRujukanA1) && $tanggalRujukanA1 ? 'readonly' : '' }}
                                placeholder="dd-mm-yyyy" autocomplete="off">
                            <small class="text-muted">{{ isset($tanggalRujukanA1) && $tanggalRujukanA1 ? 'Tanggal diambil otomatis dari surat A1' : '' }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fakta-fakta Hasil Penyelidikan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="a2_fakta_lidik" rows="4" 
                                placeholder="Jelaskan fakta-fakta yang ditemukan selama penyelidikan...&#10;Contoh:&#10;a. Telah dilakukan pengecekan TKP pada tanggal...&#10;b. Telah diperiksa saksi: ...&#10;c. Hasil pemeriksaan menunjukkan...">{{ old('a2_fakta_lidik') }}</textarea>
                            <small class="text-muted">Uraikan secara detail temuan selama penyelidikan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kesimpulan / Alasan Belum Naik Sidik <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="a2_alasan" rows="3"
                                placeholder="Contoh: Belum memenuhi unsur pasal, kurang bukti, saksi tidak ditemukan, dll...">{{ old('a2_alasan') }}</textarea>
                            <small class="text-muted">Jelaskan alasan mengapa perkara belum dapat ditingkatkan ke penyidikan</small>
                        </div>
                    </div>
                </div>

                <!-- FORM A3: SPDP (Naik Sidik) -->
                <div class="card mb-3 dynamic-section" id="section_a3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-gavel"></i> Data Spesifik A-3: Dimulainya Penyidikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success border-0 shadow-sm">
                            <strong><i class="fas fa-check-circle"></i> Informasi Tipe A-3:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Naik Sidik (Penyidikan)</li>
                                <li><strong>Waktu:</strong> Setelah gelar perkara menetapkan adanya unsur pidana</li>
                                <li><strong>Data Kunci:</strong> Nomor & Tanggal Sprin Sidik (Surat Perintah Penyidikan)</li>
                            </ul>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">No. Surat Penelitian Awal (A1) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a3_rujukan_a1" value="{{ old('a3_rujukan_a1', $nomorRujukanA1 ?? '') }}" {{ isset($nomorRujukanA1) && $nomorRujukanA1 ? 'readonly' : '' }}
                                        placeholder="Contoh: B/531/VI/2025/Reskrim">
                                    <small class="text-muted">{{ isset($nomorRujukanA1) && $nomorRujukanA1 ? 'Otomatis dari surat A1' : 'Rujukan dari surat A1 yang telah diterbitkan' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Surat A1 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control " name="a3_tanggal_a1" id="a3_tanggal_a1" value="{{ old('a3_tanggal_a1', $tanggalRujukanA1 ?? '') }}" {{ isset($tanggalRujukanA1) && $tanggalRujukanA1 ? 'readonly' : '' }}
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Sprin Sidik <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a3_sprin_sidik" value="{{ old('a3_sprin_sidik', $nomorSprinSidik ?? '') }}" {{ isset($nomorSprinSidik) && $nomorSprinSidik ? 'readonly' : '' }}
                                        placeholder="Contoh: SP.Sidik/01/I/2025/Reskrim">
                                    <small class="text-muted">{{ isset($nomorSprinSidik) && $nomorSprinSidik ? 'Otomatis dari Surat Perintah Penyidikan' : 'Surat Perintah Penyidikan yang diterbitkan setelah gelar perkara' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Sprin Sidik <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a3_tanggal_sprin" id="a3_tanggal_sprin" value="{{ old('a3_tanggal_sprin', $tanggalSprinSidik ?? '') }}" {{ isset($tanggalSprinSidik) && $tanggalSprinSidik ? 'readonly' : '' }}
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                    <small class="text-muted">{{ isset($tanggalSprinSidik) && $tanggalSprinSidik ? 'Otomatis dari Sprin Sidik' : '' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor SPDP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a3_nomor_spdp" value="{{ old('a3_nomor_spdp', $nomorSpdp ?? '') }}" {{ isset($nomorSpdp) && $nomorSpdp ? 'readonly' : '' }}
                                        placeholder="Contoh: SPDP/01/I/2025/Reskrim">
                                    <small class="text-muted">{{ isset($nomorSpdp) && $nomorSpdp ? 'Otomatis dari SPDP' : 'Surat Pemberitahuan Dimulainya Penyidikan' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal SPDP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a3_tanggal_spdp" id="a3_tanggal_spdp" value="{{ old('a3_tanggal_spdp', $tanggalSpdp ?? '') }}" {{ isset($tanggalSpdp) && $tanggalSpdp ? 'readonly' : '' }}
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                    <small class="text-muted">{{ isset($tanggalSpdp) && $tanggalSpdp ? 'Otomatis dari SPDP' : '' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pasal yang Diduga <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="a3_pasal_diduga" rows="2" {{ isset($pasalDiduga) && $pasalDiduga ? 'readonly' : '' }}
                                        placeholder="Contoh: Pasal 310 KUHP, Pasal 311 KUHP">{{ old('a3_pasal_diduga', $pasalDiduga ?? '') }}</textarea>
                                    <small class="text-muted">{{ isset($pasalDiduga) && $pasalDiduga ? 'Otomatis dari Surat Perintah Penyidikan' : 'Pasal pidana yang diduga dilanggar sesuai Sprin Sidik' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM A4: Perkembangan -->
                <div class="card mb-3 dynamic-section" id="section_a4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-line"></i> Data Spesifik A-4: Perkembangan Penyidikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 shadow-sm">
                            <strong><i class="fas fa-info-circle"></i> Informasi Tipe A-4:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Berjalan (Update Bulanan)</li>
                                <li><strong>Fungsi:</strong> Memberitahu progress apa yang sudah dilakukan (periksa saksi, sita BB, dll) dan kendala</li>
                                <li><strong>Data Kunci:</strong> Rujukan A1, Poin Tindakan, Hambatan, Rencana</li>
                            </ul>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">No. Surat Penelitian Awal (A1)</label>
                                    {{-- <input type="text" class="form-control" name="a4_rujukan_a1" value="{{ old('a4_rujukan_a1') }}" placeholder="Nomor surat A1"> --}}
                                    <input type="text" class="form-control" name="a4_rujukan_a1" value="{{ old('a4_rujukan_a1', $nomorRujukanA1 ?? '') }}" {{ isset($nomorRujukanA1) && $nomorRujukanA1 ? 'readonly' : '' }}
                                        placeholder="Contoh: B/531/VI/2025/Reskrim">
                                    <small class="text-muted">{{ isset($nomorRujukanA1) && $nomorRujukanA1 ? 'Otomatis dari surat A1' : 'Rujukan dari surat A1 yang telah diterbitkan' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Surat A1</label>
                                    <input type="text" class="form-control " name="a4_tanggal_a1" id="a4_tanggal_a1" value="{{ old('a3_tanggal_a1', $tanggalRujukanA1 ?? '') }}" {{ isset($tanggalRujukanA1) && $tanggalRujukanA1 ? 'readonly' : '' }}
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <label class="form-label fw-bold d-block mb-3">
                                    <i class="fas fa-tasks"></i> Tindakan Yang Telah Dilakukan <span class="text-danger">*</span>
                                </label>
                                <div class="alert alert-info border-0 mb-3">
                                    <small><i class="fas fa-info-circle"></i> <strong>Petunjuk:</strong> Centang tindakan sesuai urutan yang benar. Checkbox akan otomatis enabled setelah checkbox sebelumnya dicentang.</small>
                                </div>

                            <div class="row">
                                <!-- KIRI -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2">
                                            <i class="fas fa-clipboard"></i> Administrasi
                                        </small>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="lp" id="b_lp" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('lp', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_lp"> Laporan Polisi </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_lp" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[lp]" 
                                                    placeholder="Keterangan untuk Laporan Polisi" value="{{ old('berkas_keterangan.lp') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="sprin_tugas" id="b_sprintugas" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('sprin_tugas', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_sprintugas"> Surat Perintah Tugas </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_sprintugas" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[sprin_tugas]" 
                                                    placeholder="Contoh: Telah diterbitkan Surat perintah tugas pada tanggal 14 desember 2025" value="{{ old('berkas_keterangan.sprin_tugas') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="sprindik" id="b_sprindik" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('sprindik', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_sprindik"> Surat Perintah Penyidikan </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_sprindik" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[sprindik]" 
                                                    placeholder="Keterangan untuk Surat Perintah Penyidikan" value="{{ old('berkas_keterangan.sprindik') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="spdp" id="b_spdp" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('spdp', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_spdp"> SPDP </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_spdp" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[spdp]" 
                                                    placeholder="Keterangan untuk SPDP" value="{{ old('berkas_keterangan.spdp') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2">
                                            <i class="fas fa-map-marker-alt"></i> TKP
                                        </small>
                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="ba_tkp" id="b_ba_tkp" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('ba_tkp', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_ba_tkp"> BA Pemeriksaan TKP </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_ba_tkp" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[ba_tkp]" 
                                                    placeholder="Keterangan untuk BA Pemeriksaan TKP" value="{{ old('berkas_keterangan.ba_tkp') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2">
                                            <i class="fas fa-users"></i> Saksi
                                        </small>
                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="panggilan_saksi" id="b_panggilan_saksi" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('panggilan_saksi', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_panggilan_saksi"> Surat Panggilan Saksi </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_panggilan_saksi" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[panggilan_saksi]" 
                                                    placeholder="Keterangan untuk Surat Panggilan Saksi" value="{{ old('berkas_keterangan.panggilan_saksi') }}">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="ba_periksa_saksi" id="b_ba_saksi" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('ba_periksa_saksi', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_ba_saksi"> BA Pemeriksaan Saksi/Korban </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_ba_saksi" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[ba_periksa_saksi]" 
                                                    placeholder="Keterangan untuk BA Pemeriksaan Saksi/Korban" value="{{ old('berkas_keterangan.ba_periksa_saksi') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KANAN -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2">
                                            <i class="fas fa-user-secret"></i> Tersangka
                                        </small>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="panggilan_tersangka" id="b_panggilan_tersangka" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('panggilan_tersangka', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_panggilan_tersangka"> Surat Panggilan Tersangka </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_panggilan_tersangka" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[panggilan_tersangka]" 
                                                    placeholder="Keterangan untuk Surat Panggilan Tersangka" value="{{ old('berkas_keterangan.panggilan_tersangka') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="ba_periksa_tersangka" id="b_ba_tersangka" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('ba_periksa_tersangka', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_ba_tersangka"> BA Pemeriksaan Tersangka </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_ba_tersangka" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[ba_periksa_tersangka]" 
                                                    placeholder="Keterangan untuk BA Pemeriksaan Tersangka" value="{{ old('berkas_keterangan.ba_periksa_tersangka') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2">
                                            <i class="fas fa-box"></i> Barang Bukti
                                        </small>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="sprin_sita" id="b_sprin_sita" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('sprin_sita', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_sprin_sita"> Surat Perintah Penyitaan </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_sprin_sita" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[sprin_sita]" 
                                                    placeholder="Keterangan untuk Surat Perintah Penyitaan" value="{{ old('berkas_keterangan.sprin_sita') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="ba_sita" id="b_ba_sita" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('ba_sita', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_ba_sita"> BA Penyitaan </label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_ba_sita" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[ba_sita]" 
                                                    placeholder="Keterangan untuk BA Penyitaan" value="{{ old('berkas_keterangan.ba_sita') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2">
                                            <i class="fas fa-paper-plane"></i> Pelimpahan
                                        </small>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="kirim_berkas" id="b_kirim_berkas" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('kirim_berkas', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_kirim_berkas">Surat Pengiriman Berkas ke JPU</label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_kirim_berkas" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[kirim_berkas]" 
                                                    placeholder="Keterangan untuk Surat Pengiriman Berkas ke JPU" value="{{ old('berkas_keterangan.kirim_berkas') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input tindakan-sequence tindakan-checkbox" type="checkbox" value="serah_tersangka_bb" id="b_serah_bb" name="berkas[]"
                                                    {{ is_array(old('berkas')) && in_array('serah_tersangka_bb', old('berkas')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="b_serah_bb"> BA Serah Terima Tersangka & BB</label>
                                            </div>
                                            <div class="tindakan-keterangan-container" id="ket_b_serah_bb" style="display: none;">
                                                <input type="text" class="form-control" name="berkas_keterangan[serah_tersangka_bb]" 
                                                    placeholder="Keterangan untuk BA Serah Terima Tersangka & BB" value="{{ old('berkas_keterangan.serah_tersangka_bb') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label fw-bold">Hambatan (Jika Ada)</label>
                            <textarea class="form-control" name="a4_hambatan" rows="2"
                                placeholder="Jelaskan hambatan di lapangan jika ada (saksi tidak kooperatif, tersangka melarikan diri, dll)...">{{ old('a4_hambatan') }}</textarea>
                            <small class="text-muted">Kosongkan jika tidak ada hambatan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rencana Tindak Lanjut</label>
                            <textarea class="form-control" name="a4_rencana" rows="2"
                                placeholder="Rencana penyidikan selanjutnya (akan memanggil saksi tambahan, melakukan rekonstruksi, dll)...">{{ old('a4_rencana') }}</textarea>
                            <small class="text-muted">Jelaskan langkah-langkah yang akan dilakukan ke depan</small>
                        </div>
                    </div>
                </div>

                <!-- FORM A5: SP3 -->
                <div class="card mb-3 dynamic-section" id="section_a5">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-ban"></i> Data Spesifik A-5: Penghentian Penyidikan (SP3)</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger border-0 shadow-sm">
                            <strong><i class="fas fa-stop-circle"></i> Informasi Tipe A-5:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Penghentian Penyidikan (SP3)</li>
                                <li><strong>Kondisi:</strong> Kasus dihentikan karena alasan hukum (Bukan pidana, Daluarsa, dll)</li>
                                <li><strong>Data Kunci:</strong> Sprin Sidik, SP2HP sebelumnya, dan Alasan SP3</li>
                                <li><strong>Dasar Hukum:</strong> Pasal 109 ayat (2) KUHAP</li>
                            </ul>
                        </div>

                        @if(isset($tersangkaFromA4) && is_array($tersangkaFromA4) && (array_key_exists('tersangka_nama', $tersangkaFromA4) || isset($tersangkaFromA4['tersangka_nama'])))
                        <div class="alert alert-info border-0 shadow-sm mt-4">
                            <strong><i class="fas fa-info-circle"></i> Data Tersangka dari SP2HP A4:</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-danger">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis Identitas</th>
                                        <th>Nomor Identitas</th>
                                        <th>Tempat, Tgl Lahir</th>
=                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                @php $no = 1; @endphp
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $tersangkaFromA4['tersangka_nama'] ?? '-' }}</td>
                                        <td>KTP</td>
                                        <td>{{ $tersangkaFromA4['tersangka_nik'] ?? '-' }}</td>
                                        <td>
                                            {{ ($tersangkaFromA4['tersangka_tempat_lahir'] ?? '-') }},
                                            {{ ($tersangkaFromA4['tersangka_tanggal_lahir'] ?? '-') }}
                                        </td>
                                        <td>{{ $tersangkaFromA4['tersangka_alamat'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @elseif(isset($tersangkaFromA4))
                        <div class="alert alert-warning border-0 shadow-sm mt-4">
                            <strong><i class="fas fa-exclamation-triangle"></i> Data Tersangka belum diisi di SP2HP A4</strong><br>
                            <small>Silakan buat atau edit SP2HP A4 terlebih dahulu dan isi data tersangka.</small>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">No. Sprin Sidik <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a5_sprin_sidik" value="{{ old('a5_sprin_sidik', $nomorSprinSidik ?? '') }}" {{ isset($nomorSprinSidik) && $nomorSprinSidik ? 'readonly' : '' }}
                                        placeholder="Contoh: SP.Sidik/01/I/2025/Reskrim">
                                    <small class="text-muted">{{ isset($nomorSprinSidik) && $nomorSprinSidik ? 'Otomatis dari Surat Perintah Penyidikan' : 'Surat Perintah Penyidikan yang diterbitkan setelah gelar perkara' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">No. SP2HP Terakhir</label>
                                    <input type="text" class="form-control" name="a5_sp2hp_terakhir" value="{{ old('a5_sp2hp_terakhir', $nomorRujukanA4 ?? '') }}" {{ isset($nomorRujukanA4) && $nomorRujukanA4 ? 'readonly' : '' }}
                                        placeholder="Nomor SP2HP A4 perkembangan terakhir">
                                    <small class="text-muted">{{ isset($nomorRujukanA4) && $nomorRujukanA4 ? 'Otomatis dari SP2HP A4 terakhir' : 'Jika ada SP2HP A4 perkembangan sebelumnya' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penghentian (SP3) <span class="text-danger">*</span></label>
                            <select class="form-control form-select" name="a5_alasan_sp3">
                                <option value="">-- Pilih Alasan Penghentian --</option>
                                <option value="Bukan merupakan Tindak Pidana" {{ old('a5_alasan_sp3') == 'Bukan merupakan Tindak Pidana' ? 'selected' : '' }}>Bukan merupakan Tindak Pidana</option>
                                <option value="Tidak Cukup Bukti" {{ old('a5_alasan_sp3') == 'Tidak Cukup Bukti' ? 'selected' : '' }}>Tidak Cukup Bukti</option>
                                <option value="Peristiwa Telah Daluarsa" {{ old('a5_alasan_sp3') == 'Peristiwa Telah Daluarsa' ? 'selected' : '' }}>Peristiwa Telah Daluarsa</option>
                                <option value="Tersangka Meninggal Dunia" {{ old('a5_alasan_sp3') == 'Tersangka Meninggal Dunia' ? 'selected' : '' }}>Tersangka Meninggal Dunia</option>
                                <option value="Perkara Telah Diselesaikan Secara Kekeluargaan" {{ old('a5_alasan_sp3') == 'Perkara Telah Diselesaikan Secara Kekeluargaan' ? 'selected' : '' }}>Perkara Telah Diselesaikan Secara Kekeluargaan</option>
                                <option value="Tersangka Gangguan Jiwa (Disertai Surat Dokter)" {{ old('a5_alasan_sp3') == 'Tersangka Gangguan Jiwa (Disertai Surat Dokter)' ? 'selected' : '' }}>Tersangka Gangguan Jiwa (Disertai Surat Dokter)</option>
                                <option value="Lainnya" {{ old('a5_alasan_sp3') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            <small class="text-muted">Pilih sesuai kondisi yang menyebabkan perkara dihentikan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan Tambahan</label>
                            <textarea class="form-control" name="a5_keterangan_sp3" rows="3"
                                placeholder="Jelaskan detail alasan penghentian secara lengkap...&#10;Contoh: Setelah dilakukan penyidikan secara mendalam, tidak ditemukan cukup bukti yang mengarah kepada tersangka...">{{ old('a5_keterangan_sp3') }}</textarea>
                            <small class="text-muted">Uraikan detail pertimbangan penghentian penyidikan</small>
                        </div>
                    </div>
                </div>

                <!-- FORM A6: Tahap 1 (Kirim Berkas) -->
                <div class="card mb-3 dynamic-section" id="section_a6">
                    <div class="card-header text-white" style="background-color: #6f42c1;">
                        <h6 class="mb-0"><i class="fas fa-file-export"></i> Data Spesifik A-6: Pengiriman Berkas (Tahap 1)</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert border-0 shadow-sm" style="background-color: #e7d6ff; border-color: #6f42c1;">
                            <strong><i class="fas fa-paper-plane"></i> Informasi Tipe A-6:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Tahap 1 (Kirim Berkas)</li>
                                <li><strong>Fungsi:</strong> Memberitahu pelapor bahwa berkas perkara sudah dikirim ke Jaksa (JPU)</li>
                                <li><strong>Data Kunci:</strong> Nomor Surat Pengiriman Berkas, Nama Tersangka</li>
                            </ul>
                        </div>

                        @if(isset($tersangkaFromA4) && is_array($tersangkaFromA4) && (array_key_exists('tersangka_nama', $tersangkaFromA4) || isset($tersangkaFromA4['tersangka_nama'])))
                        <div class="alert alert-info border-0 shadow-sm mt-4">
                            <strong><i class="fas fa-info-circle"></i> Data Tersangka dari SP2HP A4:</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-danger">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis Identitas</th>
                                        <th>Nomor Identitas</th>
                                        <th>Tempat, Tgl Lahir</th>
=                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                @php $no = 1; @endphp
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $tersangkaFromA4['tersangka_nama'] ?? '-' }}</td>
                                        <td>KTP</td>
                                        <td>{{ $tersangkaFromA4['tersangka_nik'] ?? '-' }}</td>
                                        <td>
                                            {{ ($tersangkaFromA4['tersangka_tempat_lahir'] ?? '-') }},
                                            {{ ($tersangkaFromA4['tersangka_tanggal_lahir'] ?? '-') }}
                                        </td>
                                        <td>{{ $tersangkaFromA4['tersangka_alamat'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @elseif(isset($tersangkaFromA4))
                        <div class="alert alert-warning border-0 shadow-sm mt-4">
                            <strong><i class="fas fa-exclamation-triangle"></i> Data Tersangka belum diisi di SP2HP A4</strong><br>
                            <small>Silakan buat atau edit SP2HP A4 terlebih dahulu dan isi data tersangka.</small>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">No. SP2HP Perkembangan Terakhir</label>
                                    <input type="text" class="form-control" name="a6_sp2hp_terakhir" value="{{ old('a6_sp2hp_terakhir', $nomorRujukanA4 ?? '') }}" {{ isset($nomorRujukanA4) && $nomorRujukanA4 ? 'readonly' : '' }}
                                        placeholder="Contoh: B/532/XII/2024/Reskrim">
                                    <small class="text-muted">{{ isset($nomorRujukanA4) && $nomorRujukanA4 ? 'Otomatis dari SP2HP A4 terakhir' : 'Rujukan ke SP2HP A4 terakhir yang diterbitkan' }}</small>
                                </div>
                            </div>
                            @if(isset($tersangkaFromA4) && is_array($tersangkaFromA4) && (array_key_exists('tersangka_nama', $tersangkaFromA4) || isset($tersangkaFromA4['tersangka_nama'])))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Tersangka <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a6_nama_tersangka" value="{{ $tersangkaFromA4['tersangka_nama'] ?? '-' }}"
                                        placeholder="Nama lengkap tersangka" readonly>
                                    <small class="text-muted">Nama tersangka yang berkasnya dikirim</small>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Surat Pengiriman Berkas (Ke Jaksa) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a6_nomor_kirim_berkas" value="{{ old('a6_nomor_kirim_berkas') }}"
                                        placeholder="Contoh: B/533/XII/2024/Reskrim">
                                    <small class="text-muted">Nomor surat resmi pengiriman berkas perkara ke Kejaksaan</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Pengiriman Berkas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a6_tanggal_kirim" id="a6_tanggal_kirim" value="{{ old('a6_tanggal_kirim') }}"
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                    <small class="text-muted">Tanggal berkas dikirim ke Jaksa</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tujuan (Kejaksaan) <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="a6_tujuan_kejaksaan" id="a6_tujuan_kejaksaan">
                                        <option value="">-- Pilih Kejaksaan --</option>
                                        @foreach ($prosecutors as $prosecutor)
                                            <option value="{{ $prosecutor->id }}" {{ old('a6_tujuan_kejaksaan') == $prosecutor->id ? 'selected' : '' }}>
                                                {{ $prosecutor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Nama Kejaksaan Negeri yang menerima berkas
                                        @if(isset($spdpProsecutorName) && $spdpProsecutorName)
                                            <br><span class="text-info"><i class="fas fa-info-circle"></i> Otomatis diisi dari SPDP: <strong>{{ $spdpProsecutorName }}</strong></span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM A7: Tahap 2 (Selesai) -->
                <div class="card mb-3 dynamic-section" id="section_a7">
                    <div class="card-header text-white" style="background-color: #28a745;">
                        <h6 class="mb-0"><i class="fas fa-check-circle"></i> Data Spesifik A-7: Penyelesaian Perkara (Tahap 2)</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success border-0 shadow-sm">
                            <strong><i class="fas fa-flag-checkered"></i> Informasi Tipe A-7:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li><strong>Fase:</strong> Tahap 2 (Selesai)</li>
                                <li><strong>Fungsi:</strong> Memberitahu pelapor bahwa Tsk & BB sudah diserahkan ke Jaksa (P21)</li>
                                <li><strong>Data Kunci:</strong> Nomor P21, Surat Pengiriman Tsk & BB</li>
                            </ul>
                        </div>

                        @if(isset($tersangkaFromA4) && is_array($tersangkaFromA4) && (array_key_exists('tersangka_nama', $tersangkaFromA4) || !empty($tersangkaFromA4['tersangka_nama'] ?? '')))
                        <div class="alert alert-info border-0 shadow-sm mt-4">
                            <strong><i class="fas fa-info-circle"></i> Data Tersangka dari SP2HP A4:</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-danger">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis Identitas</th>
                                        <th>Nomor Identitas</th>
                                        <th>Tempat, Tgl Lahir</th>
=                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                @php $no = 1; @endphp
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $tersangkaFromA4['tersangka_nama'] ?? '-' }}</td>
                                        <td>KTP</td>
                                        <td>{{ $tersangkaFromA4['tersangka_nik'] ?? '-' }}</td>
                                        <td>
                                            {{ ($tersangkaFromA4['tersangka_tempat_lahir'] ?? '-') }},
                                            {{ ($tersangkaFromA4['tersangka_tanggal_lahir'] ?? '-') }}
                                        </td>
                                        <td>{{ $tersangkaFromA4['tersangka_alamat'] ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @elseif(isset($tersangkaFromA4))
                        <div class="alert alert-warning border-0 shadow-sm mt-4">
                            <strong><i class="fas fa-exclamation-triangle"></i> Data Tersangka belum diisi di SP2HP A4</strong><br>
                            <small>Silakan buat atau edit SP2HP A4 terlebih dahulu dan isi data tersangka.</small>
                        </div>
                        @endif
                        
                        <div class="row">
                            @if(isset($tersangkaFromA4) && is_array($tersangkaFromA4) && (array_key_exists('tersangka_nama', $tersangkaFromA4) || !empty($tersangkaFromA4['tersangka_nama'] ?? '')))
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Tersangka <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a7_nama_tersangka" value="{{ $tersangkaFromA4['tersangka_nama'] ?? '-' }}"
                                        placeholder="Nama lengkap tersangka yang diserahkan" readonly>
                                    <small class="text-muted">Nama tersangka yang akan diadili</small>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rujukan Surat Pengiriman Berkas (Tahap 1)</label>
                                    <input type="text" class="form-control" name="a7_rujukan_tahap1" value="{{ old('a7_rujukan_tahap1', $nomorKirimBerkasA6 ?? '') }}" {{ isset($nomorKirimBerkasA6) && $nomorKirimBerkasA6 ? 'readonly' : '' }}
                                        placeholder="Contoh: B/533/XII/2024/Reskrim">
                                    <small class="text-muted">{{ isset($nomorKirimBerkasA6) && $nomorKirimBerkasA6 ? 'Otomatis dari Nomor Surat Pengiriman Berkas A6' : 'Nomor surat A6 pengiriman berkas tahap 1' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Surat P-21 Jaksa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a7_nomor_p21" value="{{ old('a7_nomor_p21') }}"
                                        placeholder="Contoh: B-450/O.2.19/Ep.1/12/2024">
                                    <small class="text-muted">Surat pemberitahuan dari Jaksa bahwa berkas lengkap</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Surat P-21 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a7_tanggal_p21" id="a7_tanggal_p21" value="{{ old('a7_tanggal_p21') }}"
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Surat Pengiriman Tsk & BB (Tahap 2) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a7_nomor_kirim_tahap2" value="{{ old('a7_nomor_kirim_tahap2') }}"
                                        placeholder="Contoh: B/534/XII/2024/Reskrim">
                                    <small class="text-muted">Nomor surat penyerahan tersangka dan barang bukti</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Penyerahan Tahap 2 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="a7_tanggal_serah_tahap2" id="a7_tanggal_serah_tahap2" value="{{ old('a7_tanggal_serah_tahap2') }}"
                                        placeholder="dd-mm-yyyy" autocomplete="off">
                                    <small class="text-muted">Tanggal tersangka dan BB diserahkan</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tujuan (Kejaksaan)</label>
                                    <select class="form-control select2" name="a7_tujuan_kejaksaan" id="a7_tujuan_kejaksaan">
                                        <option value="">-- Pilih Kejaksaan --</option>
                                        @foreach ($prosecutors as $prosecutor)
                                            <option value="{{ $prosecutor->id }}" {{ old('a7_tujuan_kejaksaan') == $prosecutor->id ? 'selected' : '' }}>
                                                {{ $prosecutor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Nama Kejaksaan Negeri tujuan penyerahan
                                        @if(isset($spdpProsecutorName) && $spdpProsecutorName)
                                            <br><span class="text-info"><i class="fas fa-info-circle"></i> Otomatis diisi dari SPDP: <strong>{{ $spdpProsecutorName }}</strong></span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Tersangka -->
                <div class="card mb-3 hidden-until-type-selected" id="card-tersangka">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-exclamation-circle"></i> Data Tersangka/Terdakwa</h6>
                    </div>
                    <div class="card-body">
                        <!-- Dropdown Pilih Tersangka -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Pilih Tersangka <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="tersangka_select" name="tersangka_select">
                                    <option value="">-- Pilih Tersangka --</option>
                                    @if(isset($accident->suspects) && count($accident->suspects) > 0)
                                        @foreach($accident->suspects as $suspect)
                                            <option value="{{ $suspect->id }}"
                                                data-nama="{{ $suspect->name ?? '' }}"
                                                data-nik="{{ $suspect->identity_number ?? '' }}"
                                                data-tempat-lahir="{{ $suspect->birth_place ?? '' }}"
                                                data-tanggal-lahir="{{ $suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->format('d-m-Y') : '' }}"
                                                data-umur="{{ $suspect->birth_date ? \Carbon\Carbon::parse($suspect->birth_date)->age : '' }}"
                                                data-kebangsaan="{{ $suspect->nationality->name ?? 'Indonesia' }}"
                                                data-pekerjaan="{{ $suspect->job->name ?? '' }}"
                                                data-alamat="{{ $suspect->address ?? '' }}">
                                                {{ $suspect->name ?? '-' }} - {{ $suspect->identity_number ?? '-' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>-- Belum ada data tersangka --</option>
                                    @endif
                                </select>
                                <small class="text-muted">Pilih tersangka dari daftar, atau isi manual di bawah jika tidak ada</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tersangka_nama" name="tersangka_nama"
                                        placeholder="Nama lengkap tersangka" value="{{ old('tersangka_nama') }}">
                                    <span class="text-danger error-text tersangka_nama_err">@error('tersangka_nama'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tersangka_nik" name="tersangka_nik"
                                        placeholder="Contoh: 3174123456789012" value="{{ old('tersangka_nik') }}">
                                    <span class="text-danger error-text tersangka_nik_err">@error('tersangka_nik'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tersangka_tempat_lahir" name="tersangka_tempat_lahir"
                                        placeholder="Contoh: Jakarta" value="{{ old('tersangka_tempat_lahir') }}">
                                    <span class="text-danger error-text tersangka_tempat_lahir_err">@error('tersangka_tempat_lahir'){{ $message }}@enderror</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tersangka_tanggal_lahir" 
                                        name="tersangka_tanggal_lahir" placeholder="dd-mm-yyyy" value="{{ old('tersangka_tanggal_lahir') }}" autocomplete="off">
                                    <span class="text-danger error-text tersangka_tanggal_lahir_err">@error('tersangka_tanggal_lahir'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Umur <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="tersangka_umur" name="tersangka_umur"
                                        placeholder="Contoh: 25" value="{{ old('tersangka_umur') }}">
                                    <span class="text-danger error-text tersangka_umur_err">@error('tersangka_umur'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kebangsaan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tersangka_kebangsaan" name="tersangka_kebangsaan"
                                        placeholder="Contoh: Indonesia" value="{{ old('tersangka_kebangsaan') }}">
                                    <span class="text-danger error-text tersangka_kebangsaan_err">@error('tersangka_kebangsaan'){{ $message }}@enderror</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pekerjaan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tersangka_pekerjaan" name="tersangka_pekerjaan"
                                        placeholder="Contoh: Pengemudi" value="{{ old('tersangka_pekerjaan') }}">
                                    <span class="text-danger error-text tersangka_pekerjaan_err">@error('tersangka_pekerjaan'){{ $message }}@enderror</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="tersangka_alamat" name="tersangka_alamat" rows="2"
                                        placeholder="Alamat lengkap tersangka">{{ old('tersangka_alamat') }}</textarea>
                                    <span class="text-danger error-text tersangka_alamat_err">@error('tersangka_alamat'){{ $message }}@enderror</span>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
                

                <!-- Uraian Peristiwa -->
                <div class="card mb-3 hidden-until-type-selected" id="uraian-singkat">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-file-text"></i> Uraian Singkat Peristiwa</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Uraian Peristiwa <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="uraian_peristiwa" name="uraian_peristiwa" rows="4" readonly
                                placeholder="Uraian peristiwa dari data kecelakaan">{{ old('uraian_peristiwa', $accident->damage_lose_desc ?? '') }}</textarea>
                            <span class="text-danger error-text uraian_peristiwa_err">@error('uraian_peristiwa'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>

                <!-- Lokasi Kejadian -->
                <div class="card mb-3 hidden-until-type-selected" id="lokasi-kejadian">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-file-text"></i> Lokasi Kejadian</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi Kejadian <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="lokasi_kejadian" name="lokasi_kejadian" rows="4" readonly
                                placeholder="Lokasi kejadian dari data kecelakaan">{{ old('lokasi_kejadian', $accident->road_name ?? '') }}</textarea>
                            <span class="text-danger error-text lokasi_kejadian_err">@error('lokasi_kejadian'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>

                <!-- Pasal -->
                <div class="card mb-3 hidden-until-type-selected" id="card-pasal">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-scale-balanced"></i> Pasal-Pasal yang Diduga Dilanggar</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pasal Diduga <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="pasal_diduga" rows="2" {{ isset($pasalDiduga) && $pasalDiduga ? 'readonly' : '' }}
                                placeholder="Contoh: Pasal 310 KUHP, Pasal 311 KUHP">{{ old('pasal_diduga', $pasalDiduga ?? '') }}</textarea>
                            <small class="text-muted">{{ isset($pasalDiduga) && $pasalDiduga ? 'Otomatis dari Surat Perintah Penyidikan' : 'Pasal pidana yang diduga dilanggar sesuai Sprin Sidik' }}</small>
                        </div>
                    </div>
                </div>

                <!-- Barang Bukti (checkbox list) -->
                <div class="card mb-3 hidden-until-type-selected" id="card-bb">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-cube"></i> Barang Bukti</h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-bold d-block mb-2">Pilih Barang Bukti</label>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="bb_kendaraan" name="barang_bukti[]"
                                {{ is_array(old('barang_bukti')) && in_array('1', old('barang_bukti')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bb_kendaraan">
                                1. Kendaraan
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="2" id="bb_ktp" name="barang_bukti[]"
                                {{ is_array(old('barang_bukti')) && in_array('2', old('barang_bukti')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bb_ktp">
                                2. KTP
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="3" id="bb_sim" name="barang_bukti[]"
                                {{ is_array(old('barang_bukti')) && in_array('3', old('barang_bukti')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bb_sim">
                                3. SIM
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="4" id="bb_stnk" name="barang_bukti[]"
                                {{ is_array(old('barang_bukti')) && in_array('4', old('barang_bukti')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bb_stnk">
                                4. STNK
                            </label>
                        </div>

                        <span class="text-danger error-text barang_bukti_err">@error('barang_bukti'){{ $message }}@enderror</span>

                        <!-- (Optional) jika ingin menampilkan pilihan 'Lainnya' -->
                        <div class="mt-2">
                            <label class="form-label">Lainnya (sebutkan)</label>
                            <input type="text" class="form-control" name="barang_bukti_lainnya" value="{{ old('barang_bukti_lainnya') }}" placeholder="Contoh: Helm, Surat Kendali, dll">
                        </div>
                       
                    </div>
                </div>

                <!-- Data Kendaraan (opsional) -->
                @if($accident->case_flag != 'JATANLIN')
                <div class="card mb-3 hidden-until-type-selected" id="card_kendaraan" style="display:none;">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-car"></i> Data Kendaraan Terkait</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Kendaraan</label>
                            <select class="form-control select2" id="kendaraan_select" name="kendaraan_select">
                                <option value="">--Pilih Kendaraan--</option>
                                @if(isset($vehicleList) && count($vehicleList) > 0)
                                    @foreach($vehicleList as $vehicle)
                                        <option value="{{ $vehicle['nopol'] ?? '' }}"
                                            data-identity-number="{{ $vehicle['nomor_identitas'] ?? '' }}"
                                            data-identity-type="{{ $vehicle['tipe_identitas'] ?? '' }}"
                                            data-driving-license-type="{{ $vehicle['jenis_sim'] ?? '' }}"
                                            data-driver-name="{{ $vehicle['nama_pengemudi'] ?? '' }}"
                                            data-accident-number="{{ $vehicle['no_lp'] ?? '' }}"
                                            data-vehicle-type="{{ $vehicle['jenis_ranmor'] ?? '' }}"
                                            data-vehicle-plate="{{ $vehicle['nopol'] ?? '' }}"
                                            data-accident-location="{{ $vehicle['lokasi_kejadian'] ?? '' }}"
                                            data-accident-date="{{ $vehicle['tgl_kejadian'] ?? '' }}"
                                            data-accident-type="{{ $vehicle['jenis_kecelakaan'] ?? '' }}"
                                            data-reason="{{ $vehicle['penyebab'] ?? '' }}"
                                            data-victim="{{ $vehicle['total_korban'] ?? '' }}"
                                            data-material-loss="{{ $vehicle['total_kermat'] ?? '' }}"
                                            data-latitude="{{ $vehicle['latitude'] ?? '' }}"
                                            data-longitude="{{ $vehicle['longtitude'] ?? '' }}"
                                            data-accident-type-id="{{ $vehicle['accident_type_id'] ?? '' }}"
                                            data-accident-type-name="{{ $vehicle['accident_type_name'] ?? '' }}"
                                            data-vehicle-type-id="{{ $vehicle['vehicle_type_id'] ?? '' }}"
                                            data-vehicle-type-name="{{ $vehicle['vehicle_type_name'] ?? '' }}"
                                            data-accident-cause-id="{{ $vehicle['accident_cause_id'] ?? '' }}"
                                            data-accident-cause-name="{{ $vehicle['accident_cause_name'] ?? '' }}"
                                            data-identity-type-id="{{ $vehicle['identity_type_id'] ?? '' }}"
                                            data-identity-type-name="{{ $vehicle['identity_type_name'] ?? '' }}"
                                            data-driving-license-type-id="{{ $vehicle['driving_license_type_id'] ?? '' }}"
                                            data-driving-license-type-name="{{ $vehicle['driving_license_type_name'] ?? '' }}">
                                            Nopol: {{ $vehicle['nopol'] ?? '-' }} || {{ $vehicle['vehicle_type_name'] ?? '-' }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>-- Data kendaraan tidak tersedia --</option>
                                @endif
                            </select>
                            <span class="text-danger error-text kendaraan_select_err"></span>
                        </div>
                        
                        <div id="kendaraan_detail" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Plat Nomor</label>
                                        <input type="text" class="form-control" id="kendaraan_plat_nomor" name="kendaraan_plat_nomor" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Jenis Kendaraan</label>
                                        <input type="text" class="form-control" id="kendaraan_jenis" name="kendaraan_jenis" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Catatan Tambahan -->
                <div class="card mb-3 hidden-until-type-selected" id="card-tambahan">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Catatan Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                placeholder="Catatan tambahan jika diperlukan">{{ old('catatan') }}</textarea>
                            <span class="text-danger error-text catatan_err">@error('catatan'){{ $message }}@enderror</span>
                        </div>
                    </div>
                </div>

                <!-- Pejabat Penandatangan & Tembusan -->
                <div class="card mb-3 hidden-until-type-selected" id="card-signatory-cc">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-pen-fancy"></i> Penandatangan & Tembusan</h6>
                    </div>
                    <div class="card-body">
                        <!-- Pejabat Penandatangan -->
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <select class="form-control select2" name="signatory" id="signatory">
                                    <option value="">--Pilih Yang Menandatangani--</option>
                                    @foreach ($authorizedSignatories as $data)
                                        @php
                                            $positionName = ($data->position) ? $data->position->name : '-';
                                        @endphp
                                        <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}">
                                            {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk untuk mendapat bantuan)</small>
                                
                                @error('signatory')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tembusan -->
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label" for="carbonCopies">Tembusan</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                <div id="carbonCopiesContainer" class="mb-2">
                                </div>

                                <button class="btn btn-primary btn-sm addCarbonCopiesButton" type="button">
                                    <i class="bi bi-plus-circle"></i> Tambah Tembusan
                                </button>

                                @error('carbonCopies')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                
                <div class="text-center hidden-until-type-selected">
                    <button type="submit" class="btn btn-dark-blue" id="suratPemberitahuanPerkembangnHasilPenyidikanFormSubmit">
                        <i class="bi bi-save"></i> {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                    </a>
                </div>

                <!-- end form -->
            </form>

            <!-- area alert untuk JS -->
            <div class="alert-sp2hp-reg mt-3"></div>
        </div>
    </div>
@endsection

@push('script')
<script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
// Wrap everything to ensure jQuery is loaded
(function($) {
    'use strict';
    
    // Info untuk setiap tipe
    const sp2hpInfo = {
        'A1': `
            <strong>📋 A-1: Pemberitahuan Awal</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Awal Penyelidikan</li>
                <li><strong>Waktu:</strong> Segera setelah Laporan Polisi diterima</li>
                <li><strong>Fokus:</strong> Memberitahu pelapor bahwa laporan diterima dan penyidik ditunjuk</li>
                <li><strong>Data Kunci:</strong> Nama & HP Penyidik 1 dan 2</li>
            </ul>
        `,
        'A2': `
            <strong>🔍 A-2: Hasil Penyelidikan</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Hasil Penyelidikan (Hambatan/Henti Lidik)</li>
                <li><strong>Kondisi:</strong> Belum ditemukan unsur pidana atau bukti belum cukup untuk naik Sidik</li>
                <li><strong>Data Kunci:</strong> Rujukan surat A1, Fakta Lidik, dan Alasan belum bisa sidik</li>
            </ul>
        `,
        'A3': `
            <strong>⚖️ A-3: Dimulainya Penyidikan (SPDP)</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Naik Sidik (Penyidikan)</li>
                <li><strong>Waktu:</strong> Setelah gelar perkara menetapkan adanya unsur pidana</li>
                <li><strong>Data Kunci:</strong> Nomor & Tanggal Sprin Sidik (Surat Perintah Penyidikan)</li>
            </ul>
        `,
        'A4': `
            <strong>📊 A-4: Perkembangan Penyidikan</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Berjalan (Update Bulanan)</li>
                <li><strong>Fungsi:</strong> Memberitahu progress apa yang sudah dilakukan (periksa saksi, sita BB, dll) dan kendala</li>
                <li><strong>Data Kunci:</strong> Rujukan A1, Poin Tindakan, Hambatan, Rencana</li>
            </ul>
        `,
        'A5': `
            <strong>🛑 A-5: Penghentian (SP3)</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Penghentian Penyidikan (SP3)</li>
                <li><strong>Kondisi:</strong> Kasus dihentikan karena alasan hukum (Bukan pidana, Daluarsa, dll)</li>
                <li><strong>Data Kunci:</strong> Sprin Sidik, SP2HP sebelumnya, dan Alasan SP3</li>
                <li><strong>Dasar Hukum:</strong> Pasal 109 ayat (2) KUHAP</li>
            </ul>
        `,
        'A6': `
            <strong>📤 A-6: Pengiriman Berkas (Tahap 1)</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Tahap 1 (Kirim Berkas)</li>
                <li><strong>Fungsi:</strong> Memberitahu pelapor bahwa berkas perkara sudah dikirim ke Jaksa (JPU)</li>
                <li><strong>Data Kunci:</strong> Nomor Surat Pengiriman Berkas, Nama Tersangka</li>
            </ul>
        `,
        'A7': `
            <strong>✅ A-7: Penyelesaian Perkara (Tahap 2)</strong><br>
            <ul class="mb-0 mt-2 small">
                <li><strong>Fase:</strong> Tahap 2 (Selesai)</li>
                <li><strong>Fungsi:</strong> Memberitahu pelapor bahwa Tsk & BB sudah diserahkan ke Jaksa (P21)</li>
                <li><strong>Data Kunci:</strong> Nomor P21, Surat Pengiriman Tsk & BB</li>
            </ul>
        `
    };
    
    // MAIN FUNCTION: Toggle sections
    function toggleSections() {
        const tipe = $('#tipe_sp2hp').val();

        // 1) Reset active flags
        $('.dynamic-section').removeClass('active');

        // 2) If no selection, hide info and all type-dependent sections (reset)
        if (!tipe || tipe === '') {
            $('#info_tipe_sp2hp').hide();
            $('#regulasi_box').hide();

            // Reset all hidden-until-type-selected to clean state
            $('.hidden-until-type-selected').removeClass('show-section').hide();

            // Ensure kendaraan follows checkbox state even when no tipe selected
            if (typeof toggleCardKendaraan === 'function') toggleCardKendaraan();

            return;
        }

        // 3) Reset all type-dependent areas to clean state (remove possible inline/kelas)
        $('.hidden-until-type-selected').removeClass('show-section').hide();

        // 4) Show generic hidden sections (exclude special cards handled explicitly)
        $('.hidden-until-type-selected')
            .not('#card_kendaraan, #card-tersangka, #card-pasal, #card-bb, #card-tambahan, #uraian-singkat, #lokasi-kejadian')
            .addClass('show-section')
            .show();

        // 5) Handle special cards based on tipe
        // For tersangka: A1, A2, A3 => hidden; else shown
        if (tipe === 'A1' || tipe === 'A2' || tipe === 'A3' || tipe === 'A5' || tipe === 'A6' || tipe === 'A7') {
            $('#card-tersangka').removeClass('show-section').hide();
            $('#card-tersangka input, #card-tersangka select, #card-tersangka textarea').removeAttr('required');
        } else {
            $('#card-tersangka').addClass('show-section').show();
        }

        if (tipe === 'A1' || tipe === 'A2' || tipe === 'A3' || tipe === 'A5' || tipe === 'A6' || tipe === 'A7') {
            $('#card-pasal, #card-bb, #card-tambahan')
                .removeClass('show-section')
                .hide();

            $('#card-pasal textarea, #card-bb input, #card-bb select, #card-bb textarea, #card-tambahan input, #card-tambahan textarea')
                .removeAttr('required');
        } else {
            $('#card-pasal, #card-bb, #card-tambahan')
                .addClass('show-section')
                .show();
        }

        // For uraian singkat & lokasi kejadian: only A1 shows them
        if (tipe === 'A1' || tipe === 'A2' || tipe === 'A3' || tipe === 'A4') {
            $('#uraian-singkat, #lokasi-kejadian')
                .addClass('show-section')
                .show();
        } else {
            $('#uraian-singkat, #lokasi-kejadian')
                .removeClass('show-section')
                .hide();
        }


        // 6) Ensure kendaraan card matches checkbox state AFTER other toggles
        if (typeof toggleCardKendaraan === 'function') toggleCardKendaraan();

        // 6.5) Handle editable-a1-a4 fields (nomor surat, tanggal surat)
        // Only editable for A1 and A4, readonly for others
        // Note: tempat_surat is always readonly (auto-filled from location)
        if (tipe === 'A1' || tipe === 'A4') {
            $('.editable-a1-a4').not('#tempat_surat').removeClass('bg-light').prop('readonly', false);
            $('#surat-location-section').show();
        } else {
            $('.editable-a1-a4').not('#tempat_surat').addClass('bg-light').prop('readonly', true);
            $('#surat-location-section').hide();
        }
        // Ensure tempat_surat is always readonly
        $('#tempat_surat').addClass('bg-light').prop('readonly', true);

        // 7) Show info box / regulasi for selected tipe
        if (sp2hpInfo[tipe]) {
            $('#info_display_content').html(sp2hpInfo[tipe]);
            $('#info_tipe_sp2hp').show();
            $('#regulasi_box').show();
        }

        // 8) Activate the specific dynamic section for the tipe
        const sectionId = 'section_' + tipe.toLowerCase();
        $('#' + sectionId).addClass('active');

        // 9) Auto-populate kejaksaan from SPDP for A6 and A7
        @if(isset($spdpProsecutorId) && $spdpProsecutorId)
        if (tipe === 'A6' || tipe === 'A7') {
            const prosecutorId = '{{ $spdpProsecutorId }}';
            const prosecutorName = '{{ $spdpProsecutorName }}';
            
            // Only set if the field is empty or edit mode with matching value
            if (tipe === 'A6') {
                const currentVal = $('#a6_tujuan_kejaksaan').val();
                if (!currentVal || currentVal === '') {
                    $('#a6_tujuan_kejaksaan').val(prosecutorId).trigger('change');
                    console.log('Auto-populated A6 prosecutor from SPDP:', prosecutorName);
                }
            } else if (tipe === 'A7') {
                const currentVal = $('#a7_tujuan_kejaksaan').val();
                if (!currentVal || currentVal === '') {
                    $('#a7_tujuan_kejaksaan').val(prosecutorId).trigger('change');
                    console.log('Auto-populated A7 prosecutor from SPDP:', prosecutorName);
                }
            }
        }
        @endif

        // 10) Re-init datepickers for any newly shown fields
        setTimeout(function() {
            $('.datepicker').each(function() {
                if (!$(this).data('datepicker')) {
                    $(this).datepicker({
                        format: 'dd-mm-yyyy',
                        todayHighlight: true,
                        autoclose: true
                    });
                }
            });
        }, 150);
    }
    
    // Document ready
    $(document).ready(function() {        
        // ========================================
        // VALIDASI BERTAHAP SP2HP TYPE
        // ========================================
        const existingTypes = @json($existingSp2hpTypes ?? []);
        
        function validateSp2hpSequence() {
            $('#tipe_sp2hp option').each(function() {
                const $option = $(this);
                const value = $option.val();
                const requiredType = $option.data('required');
                const isAltPath = $option.data('alt-path') === true; // A6 has alternative path
                
                // Skip empty option
                if (!value) return;
                
                // A1 is always available
                if (value === 'A1') {
                    $option.prop('disabled', false);
                    return;
                }
                
                // Check if required type exists
                const hasRequired = requiredType ? existingTypes.includes(requiredType) : false;
                
                // Special logic for A6 (dapat dibuat jika ada A4, meskipun tidak ada A5)
                if (value === 'A6' && isAltPath) {
                    const hasA4 = existingTypes.includes('A4');
                    if (hasA4) {
                        $option.prop('disabled', false);
                        return;
                    }
                }
                
                // General validation: harus ada dokumen sebelumnya
                if (!hasRequired) {
                    $option.prop('disabled', true);
                    // Add tooltip text
                    const originalText = $option.text();
                    if (!originalText.includes('(Harus buat')) {
                        let requiredText = requiredType;
                        // Special message for A6
                        if (value === 'A6') {
                            requiredText = 'A4 terlebih dahulu';
                        } else {
                            requiredText = requiredType + ' terlebih dahulu';
                        }
                        $option.text(originalText + ' (Harus buat ' + requiredText + ')');
                        $option.css('color', '#999');
                    }
                } else {
                    $option.prop('disabled', false);
                    // Clean up tooltip if exists
                    const originalText = $option.text().replace(/ \(Harus buat.*?\)$/, '');
                    $option.text(originalText);
                    $option.css('color', '');
                }
            });
        }
        
        // Run validation on page load
        validateSp2hpSequence();
        
        // Re-validate if types change (in case of dynamic updates)
        window.revalidateSp2hpTypes = function(newTypes) {
            existingTypes.length = 0;
            existingTypes.push(...newTypes);
            validateSp2hpSequence();
        };
        
        // Initialize select2
        if ($.fn.select2) {
            $('.select2').select2({ 
                theme: 'bootstrap4', 
                width: '100%'
            });
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });
        }
        
        // Initialize all datepickers
        $('.datepicker, #tanggal_surat, #penerima_tanggal_lahir, #a3_tanggal_sprin, #a6_tanggal_kirim, #a7_tanggal_p21, #a7_tanggal_serah_tahap2, #tersangka_tanggal_lahir, #korban_tanggal_lahir, #a4_tanggal_tindakan').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            autoclose: true,
            orientation: 'bottom auto',
            endDate: new Date()
        })
            .each(function () {
                if (!$(this).val()) $(this).datepicker('setDate', new Date());
        });
        
        // FORCE hide all sections on initial load
        $('.dynamic-section').removeClass('active');
        $('#info_tipe_sp2hp').hide();
        $('#regulasi_box').hide();
        
        // Check if there's a pre-selected value
        const initialValue = $('#tipe_sp2hp').val();
        
        if (initialValue && initialValue !== '') {
            setTimeout(function() {
                toggleSections();
                
                // Populate data for edit mode
                @if(isset($isEdit) && $isEdit && isset($sp2hp))
                    populateEditData();
                @endif
            }, 300);
        }
        
        // Function to populate data for edit mode
        @if(isset($isEdit) && $isEdit && isset($sp2hp))
        function populateEditData() {
            console.log('Populating edit data for SP2HP type: {{ $sp2hp->tipe_sp2hp }}');
            
            // Populate tempat_surat
            @if(isset($sp2hp->tempat_surat))
                $('#tempat_surat').val('{{ addslashes($sp2hp->tempat_surat) }}');
            @endif
            
            // Parse type_specific_data
            @if(isset($sp2hp->type_specific_data))
                @php
                    $typeData = is_string($sp2hp->type_specific_data) 
                        ? json_decode($sp2hp->type_specific_data, true) 
                        : $sp2hp->type_specific_data;
                @endphp
                
                @if(is_array($typeData))
                    const typeSpecificData = @json($typeData);
                    console.log('Type specific data:', typeSpecificData);
                    
                    // Populate all fields from type_specific_data
                    Object.keys(typeSpecificData).forEach(function(key) {
                        const value = typeSpecificData[key];
                        if (value !== null && value !== undefined && key !== 'carbon_copies') {
                            const $field = $('#' + key);
                            if ($field.length) {
                                if ($field.is('select')) {
                                    $field.val(value).trigger('change');
                                } else if ($field.is('textarea')) {
                                    $field.val(value);
                                } else {
                                    $field.val(value);
                                }
                                console.log('Populated field:', key, '=', value);
                            }
                        }
                    });
                    
                    // Populate tembusan (carbon_copies)
                    if (typeSpecificData.carbon_copies && Array.isArray(typeSpecificData.carbon_copies)) {
                        console.log('Populating carbon copies:', typeSpecificData.carbon_copies);
                        typeSpecificData.carbon_copies.forEach(function(tembusan) {
                            if (tembusan && tembusan.trim()) {
                                const inputGroup = '<div class="input-group mb-2">' +
                                    '<input type="text" class="form-control" name="carbonCopies[]" value="' + tembusan + '" placeholder="Masukkan Tembusan">' +
                                    '<div class="input-group-append">' +
                                    '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button"><i class="bi bi-trash"></i> Hapus</button>' +
                                    '</div>' +
                                    '</div>';
                                $('#carbonCopiesContainer').append(inputGroup);
                            }
                        });
                    }
                @endif
            @endif
            
            // Populate signatory
            @if(isset($existingSignatoryId))
                $('#signatory').val('{{ $existingSignatoryId }}').trigger('change');
                console.log('Populated signatory:', '{{ $existingSignatoryId }}');
            @endif
            
            // Populate penyidik (add to table)
            @if(isset($existingPenyidik) && count($existingPenyidik) > 0)
                const existingPenyidik = @json($existingPenyidik);
                console.log('Existing penyidik:', existingPenyidik);
                
                // Clear existing table rows first
                $('#a1_personnelTable tbody').empty();
                
                existingPenyidik.forEach(function(penyidik) {
                    const newRow = '<tr data-unit="' + (penyidik.unit || '') + '">' +
                        '<td>' + (penyidik.nrp || '') + '</td>' +
                        '<td>' + (penyidik.pangkat || '') + '</td>' +
                        '<td>' + (penyidik.nama || '') + '</td>' +
                        '<td>' + (penyidik.telp || '') + '</td>' +
                        '<td>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger removeOfficerBtn" title="Hapus">' +
                        '<i class="bi bi-trash"></i>' +
                        '</button>' +
                        '</td>' +
                        '</tr>';
                    $('#a1_personnelTable tbody').append(newRow);
                });
                
                console.log('Populated ' + existingPenyidik.length + ' penyidik to table');
            @endif
            
            // Populate A4 tindakan list (checkboxes)
            @if($sp2hp->tipe_sp2hp == 'A4' && isset($sp2hp->a4_tindakan_list))
                @php
                    $tindakanList = is_string($sp2hp->a4_tindakan_list) 
                        ? json_decode($sp2hp->a4_tindakan_list, true) 
                        : $sp2hp->a4_tindakan_list;
                @endphp
                
                @if(is_array($tindakanList))
                    const tindakanData = @json($tindakanList);
                    console.log('Tindakan list:', tindakanData);
                    
                    Object.keys(tindakanData).forEach(function(key) {
                        const data = tindakanData[key];
                        if (data && data.checked) {
                            const checkboxId = 'b_' + key;
                            $('#' + checkboxId).prop('checked', true);
                            
                            // Show keterangan container and populate if exists
                            if (data.keterangan) {
                                const $ketContainer = $('#ket_' + checkboxId);
                                $ketContainer.show();
                                $ketContainer.find('input').val(data.keterangan);
                            }
                        }
                    });
                @endif
            @endif
            
            // Populate common fields (pasal_diduga, barang_bukti, catatan)
            @if(isset($sp2hp->pasal_diduga))
                $('#pasal_diduga, #a3_pasal_diduga, #a4_pasal_diduga').val('{{ addslashes($sp2hp->pasal_diduga) }}');
            @endif
            
            @if(isset($sp2hp->barang_bukti))
                $('#barang_bukti').val('{{ addslashes($sp2hp->barang_bukti) }}');
            @endif
            
            @if(isset($sp2hp->catatan))
                $('#catatan').val('{{ addslashes($sp2hp->catatan) }}');
            @endif
            
            // Trigger select2 update for all select2 fields
            $('.select2').trigger('change.select2');
            
            console.log('Edit data populated successfully');
        }
        @endif
        
        // Event handler for dropdown change
        $('#tipe_sp2hp').on('change', function() {
            const selectedValue = $(this).val();
            toggleSections();
        });
        
        // Test: Manual trigger
        window.testToggle = function(tipe) {
            $('#tipe_sp2hp').val(tipe).trigger('change');
        };
        
        // ===== TOGGLE FORM TAMBAH PENERIMA LAIN =====
        let formCloned = false;
        $('#btnTambahPenerimaLain').on('click', function() {
            // Clone form pertama kali ketika tombol diklik
            if (!formCloned) {
                // Clone dari template yang selalu ada
                if ($('#penerimaFormTemplate').length > 0) {
                    const clonedForm = $('#penerimaFormTemplate').clone();
                    clonedForm.attr('id', 'penerimaFormCloned'); // Change ID
                    clonedForm.show(); // Make it visible
                    
                    // Clear all values
                    clonedForm.find('input, select, textarea').each(function() {
                        $(this).val('');
                    });
                    
                    // Append to formTambahPenerimaLain
                    $('#formTambahPenerimaLain').append(clonedForm);
                    
                    // Re-initialize select2 for cloned elements
                    clonedForm.find('.select2-penerima-clone').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: '-- Pilih --'
                    });
                    
                    // Re-initialize datepicker
                    clonedForm.find('.datepicker').datepicker({
                        format: 'dd-mm-yyyy',
                        todayHighlight: true,
                        autoclose: true,
                        orientation: 'bottom auto'
                    });
                    
                    // Load provinces for clone
                    $.ajax({
                        url: '/api/location/provinces',
                        type: 'GET',
                        success: function(response) {
                            const $provinsiClone = $('#penerima_provinsi_clone');
                            $provinsiClone.empty().append('<option value="">-- Pilih Provinsi --</option>');
                            if (response.data && Array.isArray(response.data)) {
                                response.data.forEach(function(province) {
                                    $provinsiClone.append('<option value="' + province.id + '">' + province.name + '</option>');
                                });
                            }
                        }
                    });
                    
                    // Set initial state: Show Indonesia location fields
                    setTimeout(function() {
                        // Get Indonesia ID from clone dropdown
                        const indonesiaIdClone = $('#penerima_negara_clone option:contains("INDONESIA")').filter(function() {
                            return $(this).text().trim() === 'INDONESIA';
                        }).val();
                        
                        
                        $('.indonesia-location-field-clone').show();
                        $('.penerima-required-mark-clone').show();
                        
                        // Trigger change on negara to load provinces
                        const selectedCountry = $('#penerima_negara_clone').val();
                        
                        if (selectedCountry && selectedCountry === indonesiaIdClone) {
                            getPenerimaProvinsiClone(selectedCountry);
                        }
                    }, 300);
                    
                    // Handler untuk checkbox "Tidak diketahui" ayah dan ibu di form clone
                    $(document).on('change', '#unknown_father_clone', function() {
                        if ($(this).is(':checked')) {
                            $('#nama_ayah_clone').val('TIDAK DIKETAHUI').prop('readonly', true).addClass('bg-light');
                        } else {
                            $('#nama_ayah_clone').val('').prop('readonly', false).removeClass('bg-light');
                        }
                    });
                    
                    $(document).on('change', '#unknown_mother_clone', function() {
                        if ($(this).is(':checked')) {
                            $('#nama_ibu_clone').val('TIDAK DIKETAHUI').prop('readonly', true).addClass('bg-light');
                        } else {
                            $('#nama_ibu_clone').val('').prop('readonly', false).removeClass('bg-light');
                        }
                    });
                    
                    formCloned = true;
                }
                // Fallback: clone from existing form container if available
                else if ($('#penerimaFormContainer').length > 0) {
                    const clonedForm = $('#penerimaFormContainer').clone();
                    clonedForm.attr('id', 'penerimaFormCloned');
                    clonedForm.find('input, select, textarea').each(function() {
                        $(this).val('');
                    });
                    $('#formTambahPenerimaLain').append(clonedForm);
                    
                    clonedForm.find('.select2-penerima').select2({
                        theme: 'bootstrap4',
                        width: '100%'
                    });
                    
                    clonedForm.find('.datepicker').datepicker({
                        format: 'dd-mm-yyyy',
                        todayHighlight: true,
                        autoclose: true,
                        orientation: 'bottom auto'
                    });
                    
                    formCloned = true;
                }
            }
            
            $('#formTambahPenerimaLain').slideToggle();
            const icon = $(this).find('i');
            if (icon.hasClass('fa-plus')) {
                icon.removeClass('fa-plus').addClass('fa-minus');
                $(this).html('<i class="fas fa-minus"></i> Sembunyikan Form');
            } else {
                icon.removeClass('fa-minus').addClass('fa-plus');
                $(this).html('<i class="fas fa-plus"></i> Tambah Penerima Lain');
            }
        });
        
        // ===== A1 PERSONNEL HANDLER =====
        $('#a1_addPersonnelButton').on('click', function() {
            const selectedOption = $('#a1_personnelOption').find(':selected');
            const personnelId = selectedOption.val();
            
            if (!personnelId) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan pilih personel terlebih dahulu'
                });
            }
            
            // Get data from selected option
            const registerNumber = selectedOption.data('register-number');
            const name = selectedOption.data('name');
            const rankName = selectedOption.data('rank-name');
            const positionName = selectedOption.data('position-name');
            const policeName = selectedOption.data('police-name');
            const phone = selectedOption.data('phone');
            
            // Check if already added
            let isDuplicate = false;
            $('#a1_personnelTable tbody tr').each(function() {
                if ($(this).find('.registerNumber').text() === registerNumber) {
                    isDuplicate = true;
                    return false;
                }
            });
            
            if (isDuplicate) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Personel sudah ditambahkan'
                });
            }
            
            // Add to table
            const rowIndex = $('#a1_personnelTable tbody tr').length;
            const newRow = $('<tr>');
            newRow.append('<td>' + registerNumber + '</td>');
            newRow.append('<td>' + rankName + '</td>');
            newRow.append('<td>' + name + '</td>');
            newRow.append('<td>' + phone + '</td>');
            newRow.append('<td>' +
                '<input type="hidden" name="penyidik[' + rowIndex + '][nrp]" value="' + registerNumber + '">' +
                '<input type="hidden" name="penyidik[' + rowIndex + '][nama]" value="' + name + '">' +
                '<input type="hidden" name="penyidik[' + rowIndex + '][pangkat]" value="' + rankName + '">' +
                '<input type="hidden" name="penyidik[' + rowIndex + '][telp]" value="' + phone + '">' +
                '<input type="hidden" name="penyidik[' + rowIndex + '][unit]" value="' + policeName + '">' +
                '<button class="btn btn-danger btn-sm deleteA1Personnel" type="button">' +
                '<i class="bi bi-trash"></i></button>' +
                '</td>');
            
            $('#a1_personnelTable tbody').append(newRow);
            
            // Reset selection
            $('#a1_personnelOption').val('').trigger('change');
            
        });
        
        // Delete personnel from A1
        $(document).on('click', '.deleteA1Personnel', function() {
            $(this).closest('tr').remove();
        });

        // ========================================
        // MULTIPLE PENERIMA HANDLER
        // ========================================
        let penerimaCounter = 0;
        
        // Get Indonesia country ID from dropdown
        const indonesiaCountryId = $('#penerima_negara option:contains("INDONESIA")').filter(function() {
            return $(this).text().trim() === 'INDONESIA';
        }).val();
                
        // Handler untuk Negara - Toggle field lokasi Indonesia untuk negara selain Indonesia
        $('#penerima_negara').on('change', function() {
            const selectedCountry = $(this).val();
            const isIndonesia = (selectedCountry === indonesiaCountryId);
            
            if (!isIndonesia && selectedCountry) {
                // Hide Indonesia location fields for non-Indonesia countries
                $('.indonesia-location-field').hide();
                $('.penerima-required-mark').hide();
                
                // Clear values
                $('#penerima_provinsi').val('').trigger('change');
                $('#penerima_kota').val('').trigger('change');
                $('#penerima_kecamatan').val('').trigger('change');
                $('#penerima_kelurahan').val('').trigger('change');
                
                // Make alamat full width
                $('#penerima_alamat_container').removeClass('col-md-6').addClass('col-md-12');                
            } else if (isIndonesia) {
                // Show Indonesia location fields
                $('.indonesia-location-field').show();
                $('.penerima-required-mark').show();
                
                // Restore alamat to half width
                $('#penerima_alamat_container').removeClass('col-md-12').addClass('col-md-6');                
            }
        });
        
        // Trigger on page load if value exists
        $('#penerima_negara').trigger('change');

        $('#addPenerimaButton').on('click', function() {
            const selectedCountry = $('#penerima_negara').val();
            const isIndonesia = (selectedCountry === indonesiaCountryId);
            
            // Validasi field required - adjust based on country
            const requiredFields = {
                'penerima_jenis_identitas': 'Jenis Identitas',
                'penerima_nomor_identitas': 'Nomor Identitas',
                'penerima_nama': 'Nama Lengkap',
                'penerima_tempat_lahir': 'Tempat Lahir',
                'penerima_tanggal_lahir': 'Tanggal Lahir',
                'penerima_jenis_kelamin': 'Jenis Kelamin',
                'penerima_kewarganegaraan': 'Kewarganegaraan',
                'penerima_agama': 'Agama',
                'penerima_pendidikan': 'Pendidikan',
                'penerima_pekerjaan': 'Pekerjaan',
                'penerima_nomor_telepon': 'Nomor Telepon',
                'penerima_negara': 'Negara',
                'penerima_alamat': 'Alamat'
            };
            
            // Only require location fields if Indonesia
            if (isIndonesia) {
                requiredFields['penerima_provinsi'] = 'Provinsi';
                requiredFields['penerima_kota'] = 'Kabupaten/Kota';
                requiredFields['penerima_kecamatan'] = 'Kecamatan';
                requiredFields['penerima_kelurahan'] = 'Kelurahan/Desa';
            }

            let missingFields = [];
            for (let [fieldId, fieldName] of Object.entries(requiredFields)) {
                const value = $('#' + fieldId).val();
                if (!value || value.trim() === '') {
                    missingFields.push(fieldName);
                }
            }

            if (missingFields.length > 0) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    html: 'Field berikut harus diisi:<br><strong>' + missingFields.join(', ') + '</strong>',
                    confirmButtonColor: '#3085d6'
                });
            }

            penerimaCounter++;

            // Get all form values
            const penerimaData = {
                jenis_identitas: $('#penerima_jenis_identitas').val(),
                jenis_identitas_text: $('#penerima_jenis_identitas option:selected').text(),
                nomor_identitas: $('#penerima_nomor_identitas').val(),
                nama: $('#penerima_nama').val(),
                nama_alias: $('#penerima_nama_alias').val(),
                tempat_lahir: $('#penerima_tempat_lahir').val(),
                tanggal_lahir: $('#penerima_tanggal_lahir').val(),
                jenis_kelamin: $('#penerima_jenis_kelamin').val(),
                jenis_kelamin_text: $('#penerima_jenis_kelamin option:selected').text(),
                nama_ayah: $('#nama_ayah').val(),
                nama_ibu: $('#nama_ibu').val(),
                kewarganegaraan: $('#penerima_kewarganegaraan').val(),
                kewarganegaraan_text: $('#penerima_kewarganegaraan option:selected').text(),
                suku: $('#penerima_suku').val(),
                agama: $('#penerima_agama').val(),
                agama_text: $('#penerima_agama option:selected').text(),
                pendidikan: $('#penerima_pendidikan').val(),
                pendidikan_text: $('#penerima_pendidikan option:selected').text(),
                pekerjaan: $('#penerima_pekerjaan').val(),
                pekerjaan_text: $('#penerima_pekerjaan option:selected').text(),
                status_perkawinan: $('#penerima_status_kawin').val(),
                nomor_telepon: $('#penerima_nomor_telepon').val(),
                email: $('#penerima_email').val(),
                negara: $('#penerima_negara').val(),
                provinsi: $('#penerima_provinsi').val(),
                kota: $('#penerima_kota').val(),
                kecamatan: $('#penerima_kecamatan').val(),
                kelurahan: $('#penerima_kelurahan').val(),
                alamat: $('#penerima_alamat').val()
            };

            // Create table row
            const rowNum = $('#penerimaTable tbody tr').length + 1;
            const newRow = $('<tr>');
            
            newRow.append('<td class="text-center">' + rowNum + '</td>');
            newRow.append('<td>' + penerimaData.nama + '</td>');
            newRow.append('<td class="text-center">' + penerimaData.jenis_identitas_text + '</td>');
            newRow.append('<td>' + penerimaData.nomor_identitas + '</td>');
            newRow.append('<td>' + penerimaData.nomor_telepon + '</td>');
            newRow.append('<td><small>' + penerimaData.alamat.substring(0, 50) + (penerimaData.alamat.length > 50 ? '...' : '') + '</small></td>');
            
            // Hidden inputs for all data
            let hiddenInputs = '';
            for (let [key, value] of Object.entries(penerimaData)) {
                hiddenInputs += '<input type="hidden" name="penerima[' + penerimaCounter + '][' + key + ']" value="' + (value || '').replace(/"/g, '&quot;') + '">';
            }
            
            newRow.append('<td class="text-center">' + hiddenInputs +
                '<button class="btn btn-danger btn-sm deletePenerima" type="button">' +
                '<i class="bi bi-trash"></i></button></td>');
            
            $('#penerimaTable tbody').append(newRow);

            // Clear form after adding
            clearPenerimaForm();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Penerima berhasil ditambahkan ke daftar',
                timer: 1500,
                showConfirmButton: false
            });
        });

        // Delete penerima from table
        $(document).on('click', '.deletePenerima', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const row = $(this).closest('tr');
            
            if (typeof Swal === 'undefined') {
                if (confirm('Hapus penerima dari daftar?')) {
                    row.remove();
                    $('#penerimaTable tbody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                }
                return;
            }
            
            Swal.fire({
                title: 'Hapus Penerima?',
                text: 'Data penerima akan dihapus dari daftar',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                    // Renumber rows
                    $('#penerimaTable tbody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus',
                        text: 'Penerima telah dihapus dari daftar',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Handler untuk button "Tambah Penerima ke Daftar" dari form clone (delegated event)
        $(document).on('click', '#addPenerimaButtonClone', function() {
            
            // Validasi field required MINIMAL untuk form clone
            const requiredFieldsClone = {
                'penerima_jenis_identitas_clone': 'Jenis Identitas',
                'penerima_nomor_identitas_clone': 'Nomor Identitas',
                'penerima_nama_clone': 'Nama Lengkap',
                'penerima_nomor_telepon_clone': 'Nomor Telepon',
                'penerima_alamat_clone': 'Alamat'
            };

            let missingFields = [];
            for (let [fieldId, fieldName] of Object.entries(requiredFieldsClone)) {
                const value = $('#' + fieldId).val();
                if (!value || value.trim() === '') {
                    missingFields.push(fieldName);
                }
            }

            if (missingFields.length > 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        html: 'Field berikut harus diisi:<br><strong>' + missingFields.join(', ') + '</strong>',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    alert('Data Belum Lengkap!\n\nField berikut harus diisi:\n- ' + missingFields.join('\n- '));
                }
                return false;
            }

            penerimaCounter++;
            // Get all form values from clone - LENGKAP
            const penerimaData = {
                jenis_identitas: $('#penerima_jenis_identitas_clone').val(),
                jenis_identitas_text: $('#penerima_jenis_identitas_clone option:selected').text(),
                nomor_identitas: $('#penerima_nomor_identitas_clone').val(),
                nama: $('#penerima_nama_clone').val(),
                nama_alias: $('#penerima_nama_alias_clone').val() || '',
                tempat_lahir: $('#penerima_tempat_lahir_clone').val(),
                tanggal_lahir: $('#penerima_tanggal_lahir_clone').val(),
                jenis_kelamin: $('#penerima_jenis_kelamin_clone').val(),
                jenis_kelamin_text: $('#penerima_jenis_kelamin_clone option:selected').text(),
                nama_ayah: $('#nama_ayah_clone').val() || '',
                nama_ibu: $('#nama_ibu_clone').val() || '',
                kewarganegaraan: $('#penerima_kewarganegaraan_clone').val() || '',
                kewarganegaraan_text: $('#penerima_kewarganegaraan_clone option:selected').text() || '',
                suku: $('#penerima_suku_clone').val() || '',
                agama: $('#penerima_agama_clone').val() || '',
                agama_text: $('#penerima_agama_clone option:selected').text() || '',
                pendidikan: $('#penerima_pendidikan_clone').val() || '',
                pendidikan_text: $('#penerima_pendidikan_clone option:selected').text() || '',
                pekerjaan: $('#penerima_pekerjaan_clone').val() || '',
                pekerjaan_text: $('#penerima_pekerjaan_clone option:selected').text() || '',
                status_perkawinan: $('#penerima_status_kawin_clone').val() || '',
                nomor_telepon: $('#penerima_nomor_telepon_clone').val(),
                email: $('#penerima_email_clone').val(),
                negara: $('#penerima_negara_clone').val() || '',
                provinsi: $('#penerima_provinsi_clone').val() || '',
                kota: $('#penerima_kota_clone').val() || '',
                kecamatan: $('#penerima_kecamatan_clone').val() || '',
                kelurahan: $('#penerima_kelurahan_clone').val() || '',
                alamat: $('#penerima_alamat_clone').val()
            };

            // Create table row
            const rowNum = $('#penerimaTable tbody tr').length + 1;
            const newRow = $('<tr>');
            
            newRow.append('<td class="text-center">' + rowNum + '</td>');
            newRow.append('<td>' + penerimaData.nama + '</td>');
            newRow.append('<td class="text-center">' + penerimaData.jenis_identitas_text + '</td>');
            newRow.append('<td>' + penerimaData.nomor_identitas + '</td>');
            newRow.append('<td>' + penerimaData.nomor_telepon + '</td>');
            newRow.append('<td><small>' + penerimaData.alamat.substring(0, 50) + (penerimaData.alamat.length > 50 ? '...' : '') + '</small></td>');
            
            // Hidden inputs for all data
            let hiddenInputs = '';
            for (let [key, value] of Object.entries(penerimaData)) {
                hiddenInputs += '<input type="hidden" name="penerima[' + penerimaCounter + '][' + key + ']" value="' + (value || '').replace(/"/g, '&quot;') + '">';
            }
            
            newRow.append('<td class="text-center">' + hiddenInputs +
                '<button class="btn btn-danger btn-sm deletePenerima" type="button">' +
                '<i class="bi bi-trash"></i></button></td>');
            
            console.log('About to append row to table...');
            console.log('Table selector exists:', $('#penerimaTable tbody').length);
            console.log('Row HTML:', newRow.html().substring(0, 200));
            
            $('#penerimaTable tbody').append(newRow);
            
            console.log('Row appended! Total rows now:', $('#penerimaTable tbody tr').length);

            // Clear ALL form clone fields after adding
            $('#penerima_dors_select_clone').val('').trigger('change');
            $('#penerima_jenis_identitas_clone').val('').trigger('change');
            $('#penerima_nomor_identitas_clone').val('');
            $('#penerima_nama_clone').val('');
            $('#penerima_nama_alias_clone').val('');
            $('#penerima_tempat_lahir_clone').val('');
            $('#penerima_tanggal_lahir_clone').val('');
            $('#penerima_jenis_kelamin_clone').val('').trigger('change');
            $('#nama_ayah_clone').val('');
            $('#nama_ibu_clone').val('');
            $('#penerima_kewarganegaraan_clone').val('').trigger('change');
            $('#penerima_suku_clone').val('').trigger('change');
            $('#penerima_pendidikan_clone').val('').trigger('change');
            $('#penerima_pekerjaan_clone').val('').trigger('change');
            $('#penerima_agama_clone').val('').trigger('change');
            $('#penerima_status_kawin_clone').val('').trigger('change');
            $('#penerima_nomor_telepon_clone').val('');
            $('#penerima_email_clone').val('');
            $('#penerima_negara_clone').val('').trigger('change');
            $('#penerima_provinsi_clone').val('').trigger('change');
            $('#penerima_kota_clone').val('').trigger('change');
            $('#penerima_kecamatan_clone').val('').trigger('change');
            $('#penerima_kelurahan_clone').val('').trigger('change');
            $('#penerima_alamat_clone').val('');
            $('#unknown_father_clone').prop('checked', false);
            $('#unknown_mother_clone').prop('checked', false);
            
            console.log('Form cleared successfully');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Penerima berhasil ditambahkan ke daftar',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
            
            // Hide form setelah berhasil tambah
            $('#formTambahPenerimaLain').slideUp();
            $('#btnTambahPenerimaLain').html('<i class="fas fa-plus"></i> Tambah Penerima Lain');
        });

        // ===== HANDLERS FOR CLONE FORM =====
        
        // DORS dropdown handler untuk form clone
        $(document).on('change', '#penerima_dors_select_clone', function() {
            const selected = $(this).find(':selected');
            const dorsId = $(this).val();
            
            if (dorsId) {
                // Fill form from DORS data - sama seperti original
                const jenisIdentitas = selected.data('jenis-identitas');
                const nik = selected.data('nik');
                const nama = selected.data('nama');
                const tempatLahir = selected.data('tempat-lahir');
                const tglLahir = selected.data('tgl-lahir');
                const gender = selected.data('gender');
                const kewarganegaraan = selected.data('kewarganegaraan');
                const suku = selected.data('suku');
                const pekerjaan = selected.data('pekerjaan');
                const agama = selected.data('agama');
                const pendidikan = selected.data('pendidikan');
                const noHp = selected.data('no-hp');
                const alamat = selected.data('alamat');
                
                // Identity mapping
                const identityTypeMapping = {
                    'KTP': '10',
                    'PASSPORT': '12',
                    'SIM': '13',
                    'TIDAK DIKETAHUI': '15'
                };
                
                // Gender mapping
                const genderMapping = {
                    'LAKI-LAKI': '1',
                    'LAKI LAKI': '1',
                    'Laki-laki': '1',
                    'L': '1',
                    'PEREMPUAN': '2',
                    'P': '2',
                    'Perempuan': '2'
                };
                
                const identityCode = identityTypeMapping[jenisIdentitas] || '';
                const genderCode = genderMapping[(gender || '').toUpperCase()] || '';
                
                $('#penerima_nomor_identitas_clone').val(nik || '');
                $('#penerima_nama_clone').val(nama || '');
                $('#penerima_tempat_lahir_clone').val(tempatLahir || '');
                
                if (tglLahir) {
                    const dateParts = tglLahir.split('-');
                    if (dateParts.length === 3) {
                        $('#penerima_tanggal_lahir_clone').val(dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]);
                    }
                }
                
                $('#penerima_nomor_telepon_clone').val(noHp || '');
                $('#penerima_alamat_clone').val(alamat || '');
                
                if (identityCode) {
                    $('#penerima_jenis_identitas_clone').val(identityCode).trigger('change');
                }
                
                if (genderCode) {
                    $('#penerima_jenis_kelamin_clone').val(genderCode).trigger('change');
                }
                
                $('#penerima_dors_select_clone').select2('close');
            }
        });

        // Negara handler untuk form clone - toggle Indonesia location fields
        $(document).on('change', '#penerima_negara_clone', function() {
            const selectedCountry = $(this).val();
            
            // Get Indonesia ID from the clone dropdown itself
            const indonesiaIdClone = $('#penerima_negara_clone option:contains("INDONESIA")').filter(function() {
                return $(this).text().trim() === 'INDONESIA';
            }).val();
            
            const isIndonesia = (selectedCountry === indonesiaIdClone);
            
            if (isIndonesia) {
                $('.indonesia-location-field-clone').show();
                $('.penerima-required-mark-clone').show();
                // Load provinces
                getPenerimaProvinsiClone(selectedCountry);
            } else {
                $('.indonesia-location-field-clone').hide();
                $('.penerima-required-mark-clone').hide();
                // Clear location dropdowns
                $('#penerima_provinsi_clone, #penerima_kota_clone, #penerima_kecamatan_clone, #penerima_kelurahan_clone').val('').trigger('change');
            }
        });

        // Province change handler untuk clone
        $(document).on('change', '#penerima_provinsi_clone', function() {
            const parentId = $(this).val();
            if (parentId) {
                getPenerimaKotaClone(parentId);
            } else {
                $('#penerima_kota_clone, #penerima_kecamatan_clone, #penerima_kelurahan_clone').empty().append('<option value=\"\">-- Pilih --</option>');
            }
        });

        // City change handler untuk clone
        $(document).on('change', '#penerima_kota_clone', function() {
            const parentId = $(this).val();
            if (parentId) {
                getPenerimaKecamatanClone(parentId);
            } else {
                $('#penerima_kecamatan_clone, #penerima_kelurahan_clone').empty().append('<option value=\"\">-- Pilih --</option>');
            }
        });

        // District change handler untuk clone
        $(document).on('change', '#penerima_kecamatan_clone', function() {
            const parentId = $(this).val();
            if (parentId) {
                getPenerimaKelurahanClone(parentId);
            } else {
                $('#penerima_kelurahan_clone').empty().append('<option value=\"\">-- Pilih --</option>');
            }
        });

        // Functions untuk load location data clone
        function getPenerimaProvinsiClone(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'PROVINCE',
                },
                success: function(response) {
                    const $select = $('#penerima_provinsi_clone');
                    $select.empty().append('<option value=\"\">-- Pilih Provinsi --</option>');
                    $.each(response.data, function(index, item) {
                        $select.append('<option value=\"' + item.id + '\">' + item.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('Error loading provinces clone:', xhr.responseText);
                }
            });
        }

        function getPenerimaKotaClone(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'REGENCY',
                },
                success: function(response) {
                    const $select = $('#penerima_kota_clone');
                    $select.empty().append('<option value=\"\">-- Pilih Kabupaten/Kota --</option>');
                    $.each(response.data, function(index, item) {
                        $select.append('<option value=\"' + item.id + '\">' + item.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('Error loading cities clone:', xhr.responseText);
                }
            });
        }

        function getPenerimaKecamatanClone(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'DISTRICT',
                },
                success: function(response) {
                    const $select = $('#penerima_kecamatan_clone');
                    $select.empty().append('<option value=\"\">-- Pilih Kecamatan --</option>');
                    $.each(response.data, function(index, item) {
                        $select.append('<option value=\"' + item.id + '\">' + item.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('Error loading districts clone:', xhr.responseText);
                }
            });
        }

        function getPenerimaKelurahanClone(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'VILLAGE',
                },
                success: function(response) {
                    const $select = $('#penerima_kelurahan_clone');
                    $select.empty().append('<option value=\"\">-- Pilih Kelurahan/Desa --</option>');
                    $.each(response.data, function(index, item) {
                        $select.append('<option value=\"' + item.id + '\">' + item.name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('Error loading villages clone:', xhr.responseText);
                }
            });
        }
        
        // Function to clear penerima form
        function clearPenerimaForm() {
            $('#penerima_dors_select').val('').trigger('change');
            $('#penerima_jenis_identitas').val('').trigger('change');
            $('#penerima_nomor_identitas').val('');
            $('#penerima_nama').val('');
            $('#penerima_nama_alias').val('');
            $('#penerima_tempat_lahir').val('');
            $('#penerima_tanggal_lahir').val('');
            $('#penerima_jenis_kelamin').val('').trigger('change');
            $('#nama_ayah').val('');
            $('#nama_ibu').val('');
            $('#penerima_kewarganegaraan').val('').trigger('change');
            $('#penerima_suku').val('').trigger('change');
            $('#penerima_agama').val('').trigger('change');
            $('#penerima_pendidikan').val('').trigger('change');
            $('#penerima_pekerjaan').val('').trigger('change');
            $('#penerima_status_kawin').val('').trigger('change');
            $('#penerima_nomor_telepon').val('');
            $('#penerima_email').val('');
            $('#penerima_negara').val('').trigger('change');
            $('#penerima_provinsi').val('').trigger('change');
            $('#penerima_kota').val('').trigger('change');
            $('#penerima_kecamatan').val('').trigger('change');
            $('#penerima_kelurahan').val('').trigger('change');
            $('#penerima_alamat').val('');
            $('#unknown_father').prop('checked', false);
            $('#unknown_mother').prop('checked', false);
        }
        
        // Vehicle dropdown handler (IRSMS API data)
        $('#kendaraan_select').on('change', function() {
            const selected = $(this).find(':selected');
            const vehiclePlate = $(this).val();
            
            if (vehiclePlate) {
                // Data dari IRSMS API
                $('#kendaraan_plat_nomor').val(selected.data('vehicle-plate') || '');
                $('#kendaraan_jenis').val(selected.data('vehicle-type-name') || '');
                $('#kendaraan_merk').val(''); // Tidak ada di IRSMS
                $('#kendaraan_warna').val(''); // Tidak ada di IRSMS
                $('#kendaraan_nomor_rangka').val(''); // Tidak ada di IRSMS
                $('#kendaraan_nomor_mesin').val(''); // Tidak ada di IRSMS
                
                // Additional IRSMS data (can be used if needed)
                const driverName = selected.data('driver-name') || '';
                const accidentDate = selected.data('accident-date') || '';
                const accidentLocation = selected.data('accident-location') || '';
                                
                $('#kendaraan_detail').show();
            } else {
                $('#kendaraan_detail').hide();
            }
        });

        // ===== TERSANGKA DROPDOWN HANDLER =====
        $('#tersangka_select').on('change', function() {
            const selected = $(this).find(':selected');
            const tersangkaId = $(this).val();
            
            if (tersangkaId) {
                $('#tersangka_nama').val(selected.data('nama') || '');
                $('#tersangka_nik').val(selected.data('nik') || '');
                $('#tersangka_tempat_lahir').val(selected.data('tempat-lahir') || '');
                $('#tersangka_tanggal_lahir').val(selected.data('tanggal-lahir') || '');
                $('#tersangka_umur').val(selected.data('umur') || '');
                $('#tersangka_jenis_kelamin').val(selected.data('jenis-kelamin') || '').trigger('change');
                $('#tersangka_kebangsaan').val(selected.data('kebangsaan') || '');
                $('#tersangka_pekerjaan').val(selected.data('pekerjaan') || '');
                $('#tersangka_alamat').val(selected.data('alamat') || '');                
            }
        });

        // ===== PENERIMA DORS DROPDOWN HANDLER =====
        $('#penerima_dors_select').on('change', function() {
            const selected = $(this).find(':selected');
            const dorsId = $(this).val();
            
            if (dorsId) {
                // Fill form from DORS data
                const jenisIdentitas = selected.data('jenis-identitas');
                const nik = selected.data('nik');
                const nama = selected.data('nama');
                const tempatLahir = selected.data('tempat-lahir');
                const tglLahir = selected.data('tgl-lahir');
                const gender = selected.data('gender');
                const kewarganegaraan = selected.data('kewarganegaraan');
                const suku = selected.data('suku');
                const pekerjaan = selected.data('pekerjaan');
                const agama = selected.data('agama');
                const pendidikan = selected.data('pendidikan');
                const noHp = selected.data('no-hp');
                const alamat = selected.data('alamat');
                
                // Mapping jenis identitas dari text ke kode
                const identityTypeMapping = {
                    'KTP': '10',
                    'PASSPORT': '12',
                    'SIM': '13',
                    'TIDAK DIKETAHUI': '15'
                };
                
                // Mapping jenis kelamin dari text ke kode
                const genderMapping = {
                    'LAKI-LAKI': '1',
                    'LAKI LAKI': '1',
                    'Laki-laki': '1',
                    'L': '1',
                    'PEREMPUAN': '2',
                    'P': '2',
                    'Perempuan': '2'
                };
                
                // Mapping kewarganegaraan
                // 1 = WNI, 2 = WNA, 3 = TIDAK DIKETAHUI
                let nationalityCode = '';
                const kewarganegaraanUpper = (kewarganegaraan || '').toUpperCase();
                if (kewarganegaraanUpper.includes('INDONESIA') || kewarganegaraanUpper === 'WNI') {
                    nationalityCode = '1'; // WNI
                } else if (kewarganegaraanUpper.includes('TIDAK DIKETAHUI') || kewarganegaraanUpper.includes('UNKNOWN')) {
                    nationalityCode = '3'; // TIDAK DIKETAHUI
                } else if (kewarganegaraan && kewarganegaraan.trim() !== '') {
                    nationalityCode = '2'; // WNA (selain Indonesia)
                }
                
                // Mapping pendidikan
                const educationMapping = {
                    'TIDAK DIKETAHUI': '0',
                    'SD': '1',
                    'SD / SEDERAJAT': '1',
                    'SMP': '2',
                    'SMP / SEDERAJAT': '2',
                    'SMA': '3',
                    'SMA / SEDERAJAT': '3',
                    'D1': '4',
                    'D2': '5',
                    'D3': '6',
                    'D4': '7',
                    'S1': '8',
                    'S2': '9',
                    'S3': '10',
                    'SLTA': '3',
                    'SLTP': '2'
                };
                
                // Mapping agama
                const religionMapping = {
                    'TIDAK DIKETAHUI': '0',
                    'ISLAM': '1',
                    'KRISTEN PROTESTAN': '2',
                    'PROTESTAN': '2',
                    'KRISTEN': '2',
                    'KRISTEN KATOLIK': '3',
                    'KATOLIK': '3',
                    'HINDU': '4',
                    'BUDDHA': '5',
                    'BUDHA': '5',
                    'KONGHUCU': '6',
                    'KHONGHUCU': '6'
                };
                
                const identityCode = identityTypeMapping[jenisIdentitas] || '';
                const genderCode = genderMapping[(gender || '').toUpperCase()] || '';
                const educationCode = educationMapping[(pendidikan || '').toUpperCase()] || '';
                const religionCode = religionMapping[(agama || '').toUpperCase()] || '';
                
                $('#penerima_nomor_identitas').val(nik || '');
                $('#penerima_nama').val(nama || '');
                $('#penerima_tempat_lahir').val(tempatLahir || '');
                if (tglLahir) {
                    // Convert from Y-m-d to d-m-Y for datepicker
                    const dateParts = tglLahir.split('-');
                    if (dateParts.length === 3) {
                        $('#penerima_tanggal_lahir').val(dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]);
                    }
                }
                $('#penerima_nomor_telepon').val(noHp || '');
                $('#penerima_alamat').val(alamat || '');
                
                // Set jenis identitas dropdown dengan kode
                if (identityCode) {
                    $('#penerima_jenis_identitas').val(identityCode).trigger('change');
                }
                
                // Set jenis kelamin dropdown dengan kode
                if (genderCode) {
                    $('#penerima_jenis_kelamin').val(genderCode).trigger('change');
                }
                
                // Set kewarganegaraan dropdown dengan kode
                if (nationalityCode) {
                    $('#penerima_kewarganegaraan').val(nationalityCode).trigger('change');
                }
                
                // Set pendidikan dropdown dengan kode
                if (educationCode) {
                    $('#penerima_pendidikan').val(educationCode).trigger('change');
                }
                
                // Set pekerjaan dropdown dengan kode
                if (jobCode) {
                    $('#penerima_pekerjaan').val(jobCode).trigger('change');
                }
                
                // Set agama dropdown dengan kode
                if (religionCode) {
                    $('#penerima_agama').val(religionCode).trigger('change');
                }

                // Close dropdown after selection
                $('#penerima_dors_select').select2('close');
            } else {
                // Clear form if no selection
                $('#penerima_nomor_identitas').val('');
                $('#penerima_nama').val('');
                $('#penerima_tempat_lahir').val('');
                $('#penerima_tanggal_lahir').val('');
                $('#penerima_nomor_telepon').val('');
                $('#penerima_alamat').val('');
            }
        });
        
        //Form validation and submission
        $('#sp2hpForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
                        
            const tipe = $('#tipe_sp2hp').val();
            let errors = [];
                        
            // Basic validation
            if (!tipe) errors.push('Pilih jenis SP2HP terlebih dahulu');
            
            // Nomor surat, tanggal surat, tempat surat hanya required untuk A1 dan A4
            if (tipe === 'A1' || tipe === 'A4') {
                if (!($('#nomor_surat').val() || '').trim()) errors.push('Nomor surat harus diisi');
                if (!($('#tanggal_surat').val() || '').trim()) errors.push('Tanggal surat harus diisi');
                if (!($('#tempat_surat').val() || '').trim()) errors.push('Tempat surat harus diisi');
            }
            
            // Type-specific validation
            if (tipe === 'A2') {
                if (!($('input[name="a2_rujukan_a1"]').val() || '').trim()) 
                    errors.push('A2: No. Surat A1 harus diisi');
                if (!($('textarea[name="a2_fakta_lidik"]').val() || '').trim()) 
                    errors.push('A2: Fakta penyelidikan harus diisi');
                if (!($('textarea[name="a2_alasan"]').val() || '').trim()) 
                    errors.push('A2: Alasan belum naik sidik harus diisi');
            } else if (tipe === 'A3') {
                if (!($('input[name="a3_rujukan_a1"]').val() || '').trim()) 
                    errors.push('A3: No. Surat A1 harus diisi');
                if (!($('input[name="a3_sprin_sidik"]').val() || '').trim()) 
                    errors.push('A3: No. Sprin Sidik harus diisi');
            } else if (tipe === 'A4') {
                // A4 tidak ada validasi field wajib karena semua optional
            } else if (tipe === 'A5') {
                if (!($('input[name="a5_sprin_sidik"]').val() || '').trim()) 
                    errors.push('A5: No. Sprin Sidik harus diisi');
                if (!$('select[name="a5_alasan_sp3"]').val()) 
                    errors.push('A5: Alasan SP3 harus dipilih');
            } else if (tipe === 'A6') {
                if (!($('input[name="a6_nama_tersangka"]').val() || '').trim()) 
                    errors.push('A6: Nama tersangka harus diisi');
                if (!($('input[name="a6_nomor_kirim_berkas"]').val() || '').trim()) 
                    errors.push('A6: No. Surat Kirim Berkas harus diisi');
                if (!($('input[name="a6_tanggal_kirim"]').val() || '').trim()) 
                    errors.push('A6: Tanggal pengiriman berkas harus diisi');
            } else if (tipe === 'A7') {
                if (!($('input[name="a7_nama_tersangka"]').val() || '').trim()) 
                    errors.push('A7: Nama tersangka harus diisi');
                if (!($('input[name="a7_nomor_p21"]').val() || '').trim()) 
                    errors.push('A7: No. P-21 harus diisi');
                if (!($('input[name="a7_nomor_kirim_tahap2"]').val() || '').trim()) 
                    errors.push('A7: No. Surat Pengiriman Tahap 2 harus diisi');
            }
            
            // Show errors if any
            if (errors.length > 0) {
                let errorHtml = '<div class="alert alert-danger alert-dismissible fade show">';
                errorHtml += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                errorHtml += '<strong><i class="fas fa-exclamation-triangle"></i> Validasi Error!</strong><ul class="mt-2 mb-0">';
                errors.forEach(err => errorHtml += '<li>' + err + '</li>');
                errorHtml += '</ul></div>';
                
                $('.alert-sp2hp-reg').html(errorHtml);
                $('html, body').animate({ scrollTop: 0 }, 500);
                
                return false;
            }
                        
            // ========================================
            // VALIDASI: MINIMAL 1 PENERIMA HARUS DITAMBAHKAN (HANYA UNTUK A1 DAN A4)
            // ========================================
            if (tipe === 'A1' || tipe === 'A4') {
                const penerimaCount = $('#penerimaTable tbody tr').length;
                if (penerimaCount === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Penerima Kosong',
                            text: 'Anda harus menambahkan minimal 1 penerima SP2HP. Silakan isi form Data Penerima dan klik tombol "Tambah Penerima ke Daftar".',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Data Penerima Kosong!\n\nAnda harus menambahkan minimal 1 penerima SP2HP. Silakan isi form Data Penerima dan klik tombol "Tambah Penerima ke Daftar".');
                    }
                    return false;
                }                
            }
            
            // ========================================
            // AMBIL DATA PELAPOR DARI PENERIMA PERTAMA
            // ========================================
            // Menggunakan data penerima pertama sebagai pelapor
            const firstPenerimaRow = $('#penerimaTable tbody tr:first');
            let pelapor_nama = '';
            let pelapor_alamat = '';
            
            if (firstPenerimaRow.length > 0) {
                // Ambil nama dari kolom kedua (index 1)
                const namaElement = firstPenerimaRow.find('td:eq(1)');
                pelapor_nama = (namaElement.text() || '').trim();
                
                // Ambil alamat dari hidden input di row
                const alamatInput = firstPenerimaRow.find('input[name*="[alamat]"]');
                pelapor_alamat = alamatInput.val() || '';
            }
            
            // Set ke hidden fields
            if (pelapor_nama) {
                $('#pelapor_nama').val(pelapor_nama);
            } else {
                console.warn('⚠ pelapor_nama is empty!');
            }
            
            if (pelapor_alamat) {
                $('#pelapor_alamat').val(pelapor_alamat);
            } else {
                console.warn('⚠ pelapor_alamat is empty!');
            }
            
            // ========================================
            // AMBIL SEMUA DATA PENYIDIK DARI TABEL A1
            // ========================================
            // Ambil SEMUA baris dari tabel personnel A1
            const personnelRows = $('#a1_personnelTable tbody tr');
            
            // Clear container dulu
            $('#penyidikHiddenContainer').empty();
            
            if (personnelRows.length > 0) {
                const penyidikArray = [];
                
                // Loop semua penyidik di tabel
                personnelRows.each(function(index) {
                    const row = $(this);
                    const penyidikData = {
                        nrp: (row.find('td:eq(0)').text() || '').trim(),
                        pangkat: (row.find('td:eq(1)').text() || '').trim(),
                        nama: (row.find('td:eq(2)').text() || '').trim(),
                        telp: (row.find('td:eq(3)').text() || '').trim(),
                        unit: row.data('unit') || ''
                    };
                    
                    penyidikArray.push(penyidikData);
                    
                    // Buat hidden inputs untuk setiap penyidik
                    $('#penyidikHiddenContainer').append(
                        '<input type="hidden" name="penyidik[' + index + '][nrp]" value="' + penyidikData.nrp + '">' +
                        '<input type="hidden" name="penyidik[' + index + '][pangkat]" value="' + penyidikData.pangkat + '">' +
                        '<input type="hidden" name="penyidik[' + index + '][nama]" value="' + penyidikData.nama + '">' +
                        '<input type="hidden" name="penyidik[' + index + '][telp]" value="' + penyidikData.telp + '">' +
                        '<input type="hidden" name="penyidik[' + index + '][unit]" value="' + penyidikData.unit + '">'
                    );
                    
                });
            } else {
                console.warn('⚠ Tidak ada penyidik yang dipilih di A1');
            }
            
            // Disable submit button
            const $submitBtn = $('#suratPemberitahuanPerkembangnHasilPenyidikanFormSubmit');
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            
            // Submit via AJAX
            const formData = new FormData(this);
            
            // Remove pasal fields if empty (only submit if filled)
            const pasalDiduga = formData.get('pasal_diduga');
            const a3PasalDiduga = formData.get('a3_pasal_diduga');
            
            if (!pasalDiduga || (typeof pasalDiduga === 'string' && pasalDiduga.trim() === '')) {
                formData.delete('pasal_diduga');
            }
            if (!a3PasalDiduga || (typeof a3PasalDiduga === 'string' && a3PasalDiduga.trim() === '')) {
                formData.delete('a3_pasal_diduga');
            }
            
            // Debug: Log semua data yang akan dikirim
            console.log('=== FORM DATA YANG AKAN DIKIRIM ===');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            console.log('===================================');
            
            console.log('Form action URL:', $(this).attr('action'));
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('AJAX success:', response);
                    if (response.success) {
                        // Check if Swal is available
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Redirect to produktivitas accident page
                                window.location.href = response.redirect;
                            });
                        } else {
                            // Fallback to alert if Swal not available
                            alert(response.message);
                            window.location.href = response.redirect;
                        }
                    }
                },
                error: function(xhr) {
                    console.log('AJAX error:', xhr.status, xhr.responseJSON);
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan SP2HP');
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        let errorHtml = '<div class="alert alert-danger alert-dismissible fade show">';
                        errorHtml += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                        errorHtml += '<strong><i class="fas fa-exclamation-triangle"></i> Validasi Error!</strong><ul class="mt-2 mb-0">';
                        
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        
                        errorHtml += '</ul></div>';
                        $('.alert-sp2hp-reg').html(errorHtml);
                    } else {
                        // Other errors
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                        
                        // Check if Swal is available
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: message
                            });
                        } else {
                            // Fallback to alert if Swal not available
                            alert('Error: ' + message);
                        }
                    }
                    
                    $('html, body').animate({ scrollTop: 0 }, 500);
                }
            });
            
        });

        // ========================================
        // LOCATION CASCADE: Provinsi → Kota → Kecamatan (Indonesia Default)
        // ========================================
        
        // Initialize Select2 for location dropdowns
        $('.select2-location').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: function() {
                return $(this).data('placeholder') || $(this).find('option:first').text();
            },
            allowClear: true
        });
        
        // Auto-load provinces on page load (Indonesia is default)
        const indonesiaId = $('#negara_tempat_surat').val();
        if (indonesiaId) {
            getProvinsi(indonesiaId);
        }

        // Event: When province is selected, load cities
        $('#provinsi_tempat_surat').on('change', function() {
            const parentId = $(this).val();
            
            if (parentId) {
                getKota(parentId);
            } else {
                resetLocationDropdowns(['kota', 'kecamatan']);
            }
            updateTempatSurat();
        });

        // Event: When city is selected, load districts
        $('#kota_tempat_surat').on('change', function() {
            const parentId = $(this).val();
            
            if (parentId) {
                getKecamatan(parentId);
            } else {
                resetLocationDropdowns(['kecamatan']);
            }
            updateTempatSurat();
        });

        // Event: When district is selected, update tempat_surat
        $('#kecamatan_tempat_surat').on('change', function() {
            updateTempatSurat();
        });

        // Function: Get provinces based on country
        function getProvinsi(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'PROVINCE',
                },
                success: function(response) {
                    const data = response.data;
                    const $provinsiSelect = $('#provinsi_tempat_surat');
                    
                    $provinsiSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Provinsi--'
                    }));
                    
                    $.each(data, function(index, item) {
                        $provinsiSelect.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $provinsiSelect.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading provinces:', xhr.responseText);
                }
            });
        }

        // Function: Get cities based on province
        function getKota(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'REGENCY',
                },
                success: function(response) {
                    const data = response.data;
                    const $kotaSelect = $('#kota_tempat_surat');
                    
                    $kotaSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kota/Kabupaten--'
                    }));
                    
                    $.each(data, function(index, item) {
                        $kotaSelect.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $kotaSelect.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading cities:', xhr.responseText);
                }
            });
        }

        // Function: Get districts based on city
        function getKecamatan(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'DISTRICT',
                },
                success: function(response) {
                    const data = response.data;
                    const $kecamatanSelect = $('#kecamatan_tempat_surat');
                    
                    $kecamatanSelect.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kecamatan--'
                    }));
                    
                    $.each(data, function(index, item) {
                        $kecamatanSelect.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $kecamatanSelect.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading districts:', xhr.responseText);
                }
            });
        }

        // Function: Reset location dropdowns
        function resetLocationDropdowns(levels) {
            levels.forEach(function(level) {
                let dropdownId = level + '_tempat_surat';
                let placeholder = level === 'provinsi' ? 'Provinsi' : 
                                level === 'kota' ? 'Kota/Kabupaten' : 'Kecamatan (Opsional)';
                
                $('#' + dropdownId).empty().append($('<option>', {
                    value: '',
                    text: '--Pilih ' + placeholder + '--'
                })).trigger('change');
            });
        }

        // Function: Update tempat_surat field based on selections
        function updateTempatSurat() {
            const kecamatan = $('#kecamatan_tempat_surat option:selected').text();
            const kota = $('#kota_tempat_surat option:selected').text();
            
            // Only fill with Kecamatan if selected, otherwise use Kota
            let tempatSurat = '';
            
            if (kecamatan && kecamatan !== '--Pilih Kecamatan (Opsional)--') {
                tempatSurat = kecamatan;
            } else if (kota && kota !== '--Pilih Kota/Kabupaten--') {
                tempatSurat = kota;
            }
            
            $('#tempat_surat').val(tempatSurat);
            
        }

        // ========================================
        // DATA PENERIMA LOCATION CASCADE
        // ========================================
        
        // Initialize Select2 for penerima dropdowns
        $('.select2-penerima').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true
        });

        // Auto-load penerima provinces when country changes
        $('#penerima_negara').on('change', function() {
            const parentId = $(this).val();
            
            if (parentId) {
                getPenerimaProvinsi(parentId);
            } else {
                resetPenerimaLocationDropdowns(['provinsi', 'kota', 'kecamatan', 'kelurahan']);
            }
        });

        // Province change event
        $('#penerima_provinsi').on('change', function() {
            const parentId = $(this).val();
            if (parentId) {
                getPenerimaKota(parentId);
            } else {
                resetPenerimaLocationDropdowns(['kota', 'kecamatan', 'kelurahan']);
            }
        });

        // City change event
        $('#penerima_kota').on('change', function() {
            const parentId = $(this).val();
            if (parentId) {
                getPenerimaKecamatan(parentId);
            } else {
                resetPenerimaLocationDropdowns(['kecamatan', 'kelurahan']);
            }
        });

        // District change event
        $('#penerima_kecamatan').on('change', function() {
            const parentId = $(this).val();
            if (parentId) {
                getPenerimaKelurahan(parentId);
            } else {
                resetPenerimaLocationDropdowns(['kelurahan']);
            }
        });

        // Auto-load penerima provinces on page load for default Indonesia
        const penerimaDefaultCountry = $('#penerima_negara').val();
        if (penerimaDefaultCountry) {
            getPenerimaProvinsi(penerimaDefaultCountry);
        }

        // Function: Get penerima provinces
        function getPenerimaProvinsi(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'PROVINCE',
                },
                success: function(response) {
                    const $select = $('#penerima_provinsi');
                    $select.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Provinsi--'
                    }));
                    
                    $.each(response.data, function(index, item) {
                        $select.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $select.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading penerima provinces:', xhr.responseText);
                }
            });
        }

        // Function: Get penerima cities
        function getPenerimaKota(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'REGENCY',
                },
                success: function(response) {
                    const $select = $('#penerima_kota');
                    $select.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kabupaten/Kota--'
                    }));
                    
                    $.each(response.data, function(index, item) {
                        $select.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $select.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading penerima cities:', xhr.responseText);
                }
            });
        }

        // Function: Get penerima districts
        function getPenerimaKecamatan(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'DISTRICT',
                },
                success: function(response) {
                    const $select = $('#penerima_kecamatan');
                    $select.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kecamatan--'
                    }));
                    
                    $.each(response.data, function(index, item) {
                        $select.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $select.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading penerima districts:', xhr.responseText);
                }
            });
        }

        // Function: Get penerima villages
        function getPenerimaKelurahan(parentId) {
            $.ajax({
                url: "{{ route('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations', ['accident_id' => $accidentId]) }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    'parent_id': parentId,
                    'class': 'VILLAGE',
                },
                success: function(response) {
                    const $select = $('#penerima_kelurahan');
                    $select.empty().append($('<option>', {
                        value: '',
                        text: '--Pilih Kelurahan/Desa--'
                    }));
                    
                    $.each(response.data, function(index, item) {
                        $select.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    
                    $select.trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading penerima villages:', xhr.responseText);
                }
            });
        }

        // Function: Reset penerima location dropdowns
        function resetPenerimaLocationDropdowns(levels) {
            levels.forEach(function(level) {
                let dropdownId = 'penerima_' + level;
                let placeholder = level === 'provinsi' ? 'Provinsi' : 
                                level === 'kota' ? 'Kabupaten/Kota' : 
                                level === 'kecamatan' ? 'Kecamatan' : 'Kelurahan/Desa';
                
                $('#' + dropdownId).empty().append($('<option>', {
                    value: '',
                    text: '--Pilih ' + placeholder + '--'
                })).trigger('change');
            });
        }

        // ===== Toggle Card Kendaraan berdasarkan checkbox Barang Bukti (nilai = "1") =====
        function toggleCardKendaraan() {
            try {
                const $card = $('#card_kendaraan');
                // gunakan id eksplisit agar pasti
                const kendaraanChecked = $('#bb_kendaraan').is(':checked');

                if (kendaraanChecked) {
                    // remove the "hidden" class and add show-section to match other sections
                    $card.removeClass('hidden-until-type-selected').addClass('show-section');
                    // ensure parent chain is visible (remove show-section-hiding class if any)
                    $card.parents('.hidden-until-type-selected').removeClass('hidden-until-type-selected').addClass('show-section');
                    // slide down for smoothness
                    $card.stop(true,true).slideDown();
                } else {
                    // hide and restore classes
                    $card.stop(true,true).slideUp(function(){
                        // optionally re-add hidden class if you rely on it for initial state
                        $card.removeClass('show-section').addClass('hidden-until-type-selected');
                    });
                    $('#kendaraan_select').val('').trigger('change');
                    $('#kendaraan_detail').hide();
                    $('#kendaraan_plat_nomor, #kendaraan_jenis, #kendaraan_merk, #kendaraan_warna, #kendaraan_nomor_rangka, #kendaraan_nomor_mesin').val('');
                }
            } catch (err) {
                console.error('toggleCardKendaraan error:', err);
            }
        }


        // bind sekali (tangani dynamic/delegated changes juga)
        $(document).on('change', '#bb_kendaraan', toggleCardKendaraan);

        // jalankan saat load (setelah select2 / form ter-render)
        toggleCardKendaraan();

        // Jika Anda ingin card kendaraan otomatis muncul juga bila ada old('kendaraan_select')
        // (mis. ketika validasi gagal dan user sebelumnya sudah memilih kendaraan)
        if ($('#kendaraan_select').val()) {
            $('#kendaraan_detail').show();
        }

        // ========================================
        // AYAH & IBU TIDAK DIKETAHUI HANDLER
        // ========================================
        function toggleUnknownParent(checkboxSelector, inputSelector) {
            const $checkbox = $(checkboxSelector);
            const $input = $(inputSelector);

            if ($checkbox.is(':checked')) {
                $input
                    .val('TIDAK DIKETAHUI')
                    .prop('readonly', true);
            } else {
                $input
                    .val('')
                    .prop('readonly', false);
            }
        }

        // Event bindings
        $('#unknown_father').on('change', function () {
            toggleUnknownParent('#unknown_father', '#nama_ayah');
        });

        $('#unknown_mother').on('change', function () {
            toggleUnknownParent('#unknown_mother', '#nama_ibu');
        });

        // Jalankan saat halaman pertama kali load (old input Laravel)
        toggleUnknownParent('#unknown_father', '#nama_ayah');
        toggleUnknownParent('#unknown_mother', '#nama_ibu');

        // ========================================
        // TINDAKAN PENYIDIKAN SEQUENTIAL VALIDATION
        // ========================================
        const tindakanSequence = [
            'a4_lp',
            'a4_sprintugas',
            'a4_sprindik',
            'a4_spdp',
            'a4_olah_tkp',
            'a4_saksi',
            'a4_penyitaan',
            'a4_tersangka'
        ];

        // Function to check and update checkbox states
        function updateTindakanCheckboxStates() {
            let lastCheckedIndex = -1;
            
            // Find the last checked checkbox
            tindakanSequence.forEach((id, index) => {
                if ($('#' + id).is(':checked')) {
                    lastCheckedIndex = index;
                }
            });

            // Enable/disable checkboxes based on sequence
            tindakanSequence.forEach((id, index) => {
                const checkbox = $('#' + id);
                
                if (index === 0) {
                    // First checkbox always enabled
                    checkbox.prop('disabled', false);
                } else if (index <= lastCheckedIndex + 1) {
                    // Enable up to next after last checked
                    checkbox.prop('disabled', false);
                } else {
                    // Disable rest
                    checkbox.prop('disabled', true);
                    checkbox.prop('checked', false);
                }
            });
        }

        // Bind change event to all tindakan checkboxes
        tindakanSequence.forEach((id) => {
            $('#' + id).on('change', function() {
                const currentIndex = tindakanSequence.indexOf(id);
                const isChecked = $(this).is(':checked');
                
                if (!isChecked) {
                    // If unchecking, also uncheck all after this
                    for (let i = currentIndex + 1; i < tindakanSequence.length; i++) {
                        $('#' + tindakanSequence[i]).prop('checked', false);
                    }
                }
                
                updateTindakanCheckboxStates();
            });
        });

        // Initialize states on page load
        updateTindakanCheckboxStates();

        // ========================================
        // BERKAS (TINDAKAN YANG TELAH DILAKUKAN) SEQUENTIAL VALIDATION
        // ========================================
        const berkasSequence = [
            'b_lp',
            'b_sprintugas',
            'b_sprindik',
            'b_spdp',
            'b_ba_tkp',
            'b_panggilan_saksi',
            'b_ba_saksi',
            'b_panggilan_tersangka',
            'b_ba_tersangka',
            'b_sprin_sita',
            'b_ba_sita',
            'b_kirim_berkas',
            'b_serah_bb'
        ];

        // Function to check and update berkas checkbox states
        function updateBerkasCheckboxStates() {
            let lastCheckedIndex = -1;
            
            // Find the last checked checkbox
            berkasSequence.forEach((id, index) => {
                if ($('#' + id).is(':checked')) {
                    lastCheckedIndex = index;
                }
            });

            // Enable/disable checkboxes based on sequence
            berkasSequence.forEach((id, index) => {
                const checkbox = $('#' + id);
                const label = checkbox.closest('.form-check');
                
                if (index === 0) {
                    // First checkbox always enabled
                    checkbox.prop('disabled', false);
                    label.css('opacity', '1');
                } else if (index <= lastCheckedIndex + 1) {
                    // Enable up to next after last checked
                    checkbox.prop('disabled', false);
                    label.css('opacity', '1');
                } else {
                    // Disable rest with visual feedback
                    checkbox.prop('disabled', true);
                    checkbox.prop('checked', false);
                    label.css('opacity', '0.5');
                }
            });
        }

        // Bind change event to all berkas checkboxes
        berkasSequence.forEach((id) => {
            $('#' + id).on('change', function() {
                const currentIndex = berkasSequence.indexOf(id);
                const isChecked = $(this).is(':checked');
                
                // Toggle keterangan input visibility
                const keteranganContainer = $('#ket_' + id);
                if (isChecked) {
                    keteranganContainer.slideDown(200);
                } else {
                    keteranganContainer.slideUp(200);
                    // Clear keterangan value when unchecked
                    keteranganContainer.find('input').val('');
                }
                
                if (!isChecked) {
                    // If unchecking, also uncheck all after this
                    for (let i = currentIndex + 1; i < berkasSequence.length; i++) {
                        const nextId = berkasSequence[i];
                        $('#' + nextId).prop('checked', false);
                        // Hide and clear keterangan for unchecked items
                        $('#ket_' + nextId).slideUp(200).find('input').val('');
                    }
                }
                
                updateBerkasCheckboxStates();
            });
        });

        // Initialize berkas states on page load
        updateBerkasCheckboxStates();
        
        // Show keterangan for checked checkboxes on page load
        $('.tindakan-checkbox:checked').each(function() {
            const checkboxId = $(this).attr('id');
            $('#ket_' + checkboxId).show();
        });

        // ========================================
        // CARBON COPIES (TEMBUSAN) HANDLER
        // ========================================
        $(".addCarbonCopiesButton").on('click', function() {
            var inputGroup = '<div class="input-group mb-2">' +
                '<input type="text" class="form-control" name="carbonCopies[]" value="" placeholder="Masukkan Tembusan">' +
                '<div class="input-group-append">' +
                '<button class="btn btn-outline-danger removeCarbonCopiesButton" type="button"><i class="bi bi-trash"></i> Hapus</button>' +
                '</div>' +
                '</div>';

            $("#carbonCopiesContainer").append(inputGroup);
        });

        // Remove carbon copy button
        $(document).on('click', '.removeCarbonCopiesButton', function() {
            $(this).closest('.input-group').remove();
        });
    });
    
})(jQuery);
</script>
@endpush