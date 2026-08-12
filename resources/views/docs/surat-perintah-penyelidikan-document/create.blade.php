@php
    $_title = 'Surat Perintah Penyidikan';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
            class="bi bi-arrow-left"></i>
        Kembali ke Progres Perkara</a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penyelidikan (SPRINLIDIK)</h5>

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

        <form action="{{ route('doc.surat-perintah-penyelidikan-document.store', ['accident_id' => $accidentId]) }}"
            method="POST" enctype="multipart/form-data" id="suratPerintahPenyelidikanForm">
            @csrf
            <input type="hidden" name="accidentId" id="accidentId" value="{{ $accidentId }}">

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <input id="accidentNumber" type="text"
                        class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold"
                        name="accidentNumber" value="{{ $accident->no_lp }}" required placeholder="" readonly>
                    @error('accidentNumber')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen Sprinlidik <span class="text-danger fs-5">*</span></label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <input id="documentNumber" type="text"
                        class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold"
                        name="documentNumber" value="{{ old('documentNumber') }}" required
                        placeholder="Masukkan Nomor Dokumen Sprinlidik">

                    @error('documentNumber')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2" for="isRenewalDocument">Kategori Surat</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isRenewalDocument" name="isRenewalDocument"
                            value="true" aria-label="...">
                        <label for="isRenewalDocument">
                            Perpanjangan dari Surat Perintah Penyelidikan Sebelumnya Karena Masa Berlaku Habis
                        </label>
                    </div>

                    @error('isRenewalDocument')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0" id="referenceOfRenewalDocument" style="display: none;">
                <label class="fw-bold col-sm-2 col-form-label" for="referenceDocument">SP Penyelidikan Referensi</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <select class="form-control select2" name="referenceDocument" id="referenceDocument">
                        <option value="">--Pilih Surat Perintah Penyelidikan Sumber--</option>
                        @foreach ($suratPerintahPenyelidikanDocuments as $suratPerintahPenyelidikanDocument)
                            <option value="{{ $suratPerintahPenyelidikanDocument->id }}">
                                {{ $suratPerintahPenyelidikanDocument->document_number }}</option>
                        @endforeach
                    </select>

                    @error('referenceDocument')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Kata Kunci</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <select class="form-control select2-multiple" name="keywords[]" id="keywords" multiple="multiple"
                        data-placeholder="Pilih Kata Kunci (Bisa Lebih Dari Satu)">
                        <option value="">--Pilih Kata Kunci (Bisa Lebih Dari Satu)--</option>
                        @foreach ($caseKeywords as $caseKeyword)
                            <option value="{{ $caseKeyword->id }}">{{ $caseKeyword->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">(*Pilih kata kunci apabila terdapat kata kunci yang sesuai, bisa dipilih lebih
                        dari satu atau kosongkan bila tidak ada kata kunci yang sesuai)</small>

                    @error('keywords')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="caseClassification">Klasifikasi Kasus<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <select class="form-control select2" name="caseClassification" id="caseClassification">
                        <option value="">--Pilih Klasifikasi Kasus--</option>
                        <option value="SANGAT SULIT">SANGAT SULIT</option>
                        <option value="SULIT">SULIT</option>
                        <option value="SEDANG">SEDANG</option>
                        <option value="MUDAH">MUDAH</option>
                    </select>

                    @error('caseClassification')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Tanggal Mulai Lidik<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input class="form-control" id="startDate" name="startDate" placeholder="YYYY-MM-DD"
                        autocomplete="off" value="{{ old('startDate') }}" data-provide="datepicker">

                    @error('startDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Tanggal Akhir Lidik<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input class="form-control" id="endDate" name="endDate" placeholder="YYYY-MM-DD"
                        autocomplete="off" value="@if (old('isFinished') != 1) {{ old('endDate') }} @endif"
                        data-provide="datepicker" @if (old('isFinished') == 1) {{ 'readonly' }} @endif>
                    @error('endDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-sm-2 d-flex align-self-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isFinished" name="isFinished"
                            value="true" aria-label="..." @if (old('isFinished') == 1) {{ 'checked' }} @endif>
                        <label for="isFinished">
                            <b>Sampai dengan selesai</b>
                        </label>
                    </div>
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Tanggal Ditandatangani Dokumen<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-12 d-flex align-self-center">
                    <input class="form-control" id="documentDate" name="documentDate" placeholder="YYYY-MM-DD"
                        autocomplete="off" value="{{ old('documentDate') }}" data-provide="datepicker">

                    @error('documentDate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Yang Menandatangani<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <select class="form-control select2" name="signatory" id="signatory">
                        <option value="">--Pilih Yang Menandatangani--</option>
                        @foreach ($authorizedSignatories as $data)
                            @php
                                $positionName = $data->position->name ?? '';
                            @endphp
                            <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}">
                                {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">(*Apabila daftar yang menandatangani kosong silahkan hubungi Helpdesk untuk
                        mendapat bantuan)</small>

                    @error('signatory')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <hr>

            <h5 class="fw-bold text-blue-dark">Tim Penyelidik</h5>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Ketua Tim Penyelidik<span class="text-danger fs-5">*</span></label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex-align-self-center">
                    <select class="form-control select2" name="officerLeader" id="officerLeader">
                        <option value="">--Pilih Ketua Penyelidik--</option>
                        @foreach ($leaderOfficers as $data)
                            @php
                                $positionName = $data->position->name ?? '';
                            @endphp
                            <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}">
                                {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">(*Apabila daftar ketua penyelidik kosong silahkan hubungi Helpdesk untuk
                        mendapat bantuan)</small>

                    @error('officerLeader')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="row col-12 my-3 ms-0">
                <label class="fw-bold">Penyidik<small class="fw-normal text-muted"> (*Pilihan Penyidik akan tampil setelah
                        Ketua Tim
                        Penyelidik dipilih)</small></label>

                <div id="internalOfficer">
                    <div class="alert alert-primary my-3" role="alert">
                        Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai penyidik.
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="custom-select select2-input-group" id="officerInternalMemberOption"
                                    aria-describedby="officerInternalMemberOptionAddButtton">
                                    <option value="">--Pilih Penyelidik--</option>
                                </select>
                                <button class="btn btn-primary" type="button"
                                    id="officerInternalMemberOptionAddButtton"><i class="bi bi-plus-circle"></i>
                                    Tambah</button>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="internalOfficerMemberTable">
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
                            <tbody></tbody>
                        </table>

                        @error('personnel')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label class="fw-bold d-flex align-self-center">
                    <span>Penyidik Yang Telah Pindah</span>
                    <div class="form-check ms-2">
                        <input class="form-check-input" type="checkbox" name="isMovedOfficers" id="isMovedOfficers"
                            value="true" aria-label="...">
                        <label for="isMovedOfficers">
                            <small class="form-check-label fw-normal text-muted">
                                (*Tambah penyidik yang sudah tidak ada di satker jika ada)
                            </small>
                        </label>
                    </div>
                </label>

                <div id="movedOfficers" style="display: none;">
                    <div class="alert alert-primary my-3" role="alert">
                        Jika personel sudah pindah dan tidak ada di Satker, personel dapat ditambahkan sebagai
                        penyidik dengan cara sebagai berikut: <br />
                        - Cari personel berdasarkan nama atau NRP, pilih personel lalu klik tombol 'Tambah' untuk
                        mengubah detail personel agar sesuai dengan data penyidik ketika melakukan penyidikan.
                        <br />
                        - Jika personel tidak ditemukan, klik tombol 'Tambah Manual' lalu masukkan data personel
                        secara manual. <br />
                        - Setelah disimpan, penyidik yang telah pindah akan otomatis ditambahkan sebagai penyidik.
                        <br />
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-5">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="searchMovedOfficerField"
                                    placeholder="Cari NRP" aria-label="Cari NRP"
                                    aria-describedby="searchMovedOfficerButton">
                                <button class="btn btn-primary" id="searchMovedOfficerButton" type="button"><i
                                        class="bi bi-search"></i> Cari</button>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="input-group">
                                <select class="custom-select select2-input-group" id="officerMovedMemberOption"
                                    aria-describedby="officerMovedMemberOptionAddButtton">
                                    <option value="">--Pilih Penyidik Yang Telah Pindah--</option>
                                </select>
                                <button class="btn btn-primary" id="officerMovedMemberOptionAddButtton" type="button"><i
                                        class="bi bi-plus-circle"></i> Tambah</button>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-success" id="addManualMovedOfficerButton" type="button"
                                data-bs-toggle="modal" data-bs-target="#addManualMovedOfficerModal"><i
                                    class="bi bi-plus-circle"></i> Tambah Manual</button>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="movedOfficerMemberTable">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label class="fw-bold d-flex align-self-center">
                    <span>Penyidik Luar</span>
                    <div class="form-check ms-2">
                        <input class="form-check-input" type="checkbox" name="isExternalOfficers"
                            id="isExternalOfficers" value="true" aria-label="...">
                        <label for="isExternalOfficers">
                            <small class="form-check-label fw-normal text-muted">
                                (*Tambah penyidik dari luar satker jika ada)
                            </small>
                        </label>
                    </div>
                </label>

                <div id="externalOfficers" style="display: none;">
                    <div class="alert alert-primary my-3" role="alert">
                        Cari personel berdasarkan nama atau NRP, pilih personel lalu klik tombol 'Tambah' untuk
                        menambahkan personel sebagai penyidik.
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="searchExternalOfficerField"
                                    placeholder="Cari NRP" aria-label="Cari NRP" aria-describedby="basic-addon2"
                                    aria-describedby="searchExternalOfficerButton">
                                <button class="btn btn-primary" id="searchExternalOfficerButton" type="button"><i
                                        class="bi bi-search"></i> Cari</button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="custom-select select2-input-group" id="officerExternalMemberOption"
                                    aria-describedby="officerExternalMemberOptionAddButtton">
                                    <option value="">--Pilih Penyidik Luar--</option>
                                </select>
                                <button class="btn btn-primary" id="officerExternalMemberOptionAddButtton"
                                    type="button"><i class="bi bi-plus-circle"></i> Tambah</button>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <table class="table table-bordered table-responsive-md" id="externalOfficerMemberTable">
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
                            <tbody></tbody>
                        </table>

                        @error('personnel')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            @if(strtotime($accident->report_date) < strtotime('2024-01-01') || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
		        @include('docs.components.form.checkbox.is-legacy')
            @endif

            <hr>

            <div class="text-center">
                <button type="submit" class="btn btn-dark-blue" id="suratPerintahPenyelidikanFormSubmit">
                    <i class="bi bi-save"></i> {{ __('Simpan') }}
                </button>
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                    class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> {{ __('Batal') }}
                </a>
            </div>
        </form>
    </div>


    <!-- Modal Add Manual Moved Officer-->
    <div class="modal fade" id="addManualMovedOfficerModal" tabindex="-1" role="dialog"
        aria-labelledby="addManualMovedOfficerModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modalContent">
                <!-- Header Modal -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-blue-dark" id="addManualMovedOfficerModalLabel">DATA PERSONIL KETIKA MENANGANI
                        KASUS</h5>
                </div>

                <!-- Body Modal -->
                <div class="modal-body">
                    <form id="addManualMovedOfficerForm">
                        <input type="hidden" class="form-control" id="addManualMovedOfficerFormMode" value="">
                        <input type="hidden" class="form-control" id="oldRegisterNumber" value="">
                        <div class="mb-3">
                            <label class="fw-bold" for="registerNumber">NRP</label>
                            <input type="text" class="form-control" id="registerNumber" placeholder="Masukkan NRP">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold" for="name">Nama Penyidik</label>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" id="firstName" placeholder="Nama Depan">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="lastName"
                                        placeholder="Nama Belakang (Opsional)">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold" for="phone">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone"
                                placeholder="Masukkan Nomor Telepon">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold" for="rank">Pangkat</label>
                            <div>
                                <select class="form-control" id="rank">
                                    <option value="">--Pilih Pangkat--</option>
                                    @foreach ($ranks as $rank)
                                        <option value="{{ $rank->id }}" data-rank-name="{{ $rank->full_name }}">
                                            {{ $rank->full_name . ' (' . $rank->name . ')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold" for="position">Jabatan</label>
                            <div>
                                <select class="form-control" id="position">
                                    <option value="">--Pilih Jabatan--</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" data-position-name="{{ $position->name }}">
                                            {{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <label class="fw-bold" for="kesatuan" class="mb-3">Kesatuan</label>
                                <div class="mb-3">
                                    <select class="form-control" id="regionalPolice">
                                        <option value="">--Polda/Korlantas--</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <select class="form-control" id="resortPolice">
                                        <option value="">--Polres/Subdit--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
                            class="bi bi-x-circle"></i> Batal</button>
                    <button type="button" class="btn btn-dark-blue" id="saveAddManualMovedOfficerForm"><i
                            class="bi bi-save"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    @if(strtotime($accident->report_date) < strtotime('2024-01-01') || $accident->police->is_whitelisted_document_legacy == true && strtotime($accident->police->start_date_whitelisted_document_legacy) <= strtotime($accident->report_date) && strtotime($accident->report_date) <= strtotime($accident->police->end_date_whitelisted_document_legacy))
        @include('docs.components.form.checkbox.is-legacy-js')
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $('#attentionBox').toggleClass('alert-danger alert-warning');
            }, 1000);

            $("#isMovedOfficers").change(function() {
                if ($(this).is(":checked")) {
                    $("#movedOfficers").show();
                } else {
                    $("#movedOfficers").hide();
                }
            });

            $("#isExternalOfficers").change(function() {
                if ($(this).is(":checked")) {
                    $("#externalOfficers").show();
                } else {
                    $("#externalOfficers").hide();
                }
            });

            $("#isRenewalDocument").change(function() {
                if ($(this).is(":checked")) {
                    $("#referenceOfRenewalDocument").show();
                } else {
                    $("#referenceOfRenewalDocument").hide();
                }
            });

            $('#documentDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
                endDate: new Date()
            });
            $('#documentDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#startDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var startDate = new Date(selected.date.valueOf());
                $('#endDate').datepicker('setStartDate', startDate);
            });
            $('#startDate').keydown(function(e) {
                e.preventDefault();
                return false;
            })

            $('#endDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: "true",
                orientation: 'auto bottom',
            }).on('changeDate', function(selected) {
                var endDate = new Date(selected.date.valueOf());
                $('#startDate').datepicker('setEndDate', endDate);
            });
            $('#endDate').keydown(function(e) {
                e.preventDefault();
                return false;
            });

            $('#isFinished').on('change', function() {
                if (this.checked) {
                    $('#endDate').prop('disabled', true);
                    $('#endDate').val('');
                } else {
                    $('#endDate').prop('disabled', false);
                }
            });
        });

        // Select2 with Bootstrap4 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            $('.select2-multiple').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });

            $('#addManualMovedOfficerModal #rank').select2({
                dropdownParent: $('#rank').parent(),
                theme: 'bootstrap4',
                width: '100%'
            });
            $('#addManualMovedOfficerModal #position').select2({
                dropdownParent: $('#position').parent(),
                theme: 'bootstrap4',
                width: '100%'
            });
        });

        // Internal Officer
        $(document).ready(function() {
            $('#officerInternalMemberOption').prop('disabled', true);
            $('#officerLeader').on('change', function() {
                var selectedLeader = $(this).find('option:selected').data('register-number');
                var registerNumber = $(this).find('option:selected').data('register-number');

                // Cek apakah opsi sudah terappend dalam tabel
                var tablesToCheck = [{
                        tableSelector: '#movedOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil yang telah pindah, hapus terlebih dahulu untuk memilih sebagai ketua'
                    },
                    {
                        tableSelector: '#internalOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil, hapus terlebih dahulu untuk memilih sebagai ketua'
                    },
                    {
                        tableSelector: '#externalOfficerMemberTable',
                        errorMessage: 'Sudah ada dalam daftar personil luar, hapus terlebih dahulu untuk memilih sebagai ketua'
                    }
                ];

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var appendedRegisterNumber = $(this).find('.registerNumber').text();

                        if (appendedRegisterNumber == registerNumber) {
                            isAppended = true;
                            Swal.fire({
                                title: 'Gagal',
                                text: table.errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return false; // Keluar dari perulangan
                        }
                    });
                });

                if (!isAppended) {
                    if (selectedLeader !== '') {

                        // Panggil fungsi Ajax di sini
                        $.ajax({
                            url: "{{ route('doc.surat-perintah-penyelidikan-document.api.internal-officers', ['accident_id' => $accidentId]) }}", // Ganti dengan URL yang sesuai
                            type: "GET",
                            dataType: "json",
                            data: {
                                selectedLeaderOfficerRegisterNumber: selectedLeader
                            },
                            success: function(response) {
                                // Clear existing options
                                $('#officerInternalMemberOption').empty();

                                // Populate options based on response data
                                response.data.forEach(function(member) {
                                    var rankName = (member.rank) ? member.rank.name :
                                        '-';
                                    var positionName = (member.position) ? member
                                        .position.name : '-';
                                    var policeName = (member.police) ? member.police
                                        .full_name : '-';

                                    $('#officerInternalMemberOption').append($(
                                        '<option>', {
                                            value: member.id,
                                            text: member.register_number +
                                                ' - ' + member.full_name +
                                                ' - ' + rankName,
                                            'data-register-number': member
                                                .register_number,
                                            'data-rank-name': rankName,
                                            'data-name': member.full_name,
                                            'data-position-name': positionName,
                                            'data-police-name': policeName,
                                        }));
                                });

                                // Aktifkan opsi officerInternalMember
                                $('#officerInternalMemberOption').prop('disabled', false);

                                $('#officerInternalMemberOption').select2({
                                    theme: 'bootstrap4'
                                });
                            },
                            error: function(error) {
                                return Swal.fire({
                                    title: 'Gagal',
                                    text: 'Maaf, terjadi kesalahan teknis.',
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                });
                            }
                        });
                    } else {
                        // Nonaktifkan opsi officerInternalMember dan hapus opsi yang ada
                        $('#officerInternalMemberOption').prop('disabled', true);
                        $('#officerInternalMemberOption').empty();
                    }
                }
            });

            $('#officerInternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerInternalMemberOption').find('option:selected');

                if (selectedOption.val() == '') {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Ketua Tim Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                // Ambil data yang diperlukan dari selectedOption
                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                $('#internalOfficerMemberTable tbody tr').each(function() {
                    var appendedRegisterNumber = $(this).find('.registerNumber').text();

                    if (appendedRegisterNumber == registerNumber) {
                        isAppended = true;
                        return Swal.fire({
                            title: 'Gagal',
                            text: 'Personil sudah ada dalam daftar',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });

                if (!isAppended) {
                    // Buat baris baru untuk ditambahkan ke dalam tabel
                    var newRow = $('<tr class="text-center"></tr>');

                    // Tambahkan kolom-kolom dengan nilai yang diambil dari selectedOption
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + policeName + '</td>');
                    newRow.append('<td><input type="hidden" name="internalOfficers[]" value="' +
                        registerNumber +
                        '"><button class="btn btn-danger btn-sm deleteInternalOfficer" type="button"><i class="bi bi-trash"></i></button></td>'
                    );

                    // Tambahkan baris ke dalam tabel
                    $('#internalOfficerMemberTable tbody').append(newRow);

                    // Hapus event listener deleteInternalOfficer sebelumnya
                    $(document).off('click', '.deleteInternalOfficer');

                    // Tambahkan event listener deleteInternalOfficer yang baru
                    $(document).on('click', '.deleteInternalOfficer', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });
        });

        // Moved Officer
        $(document).ready(function() {
            // Search moved officer
            $('#officerMovedMemberOption').prop('disabled', true);
            $('#searchMovedOfficerButton').on('click', function() {
                var searchedOfficer = $('#searchMovedOfficerField').val();
                var accidentId = $('#accidentId').val();

                if (searchedOfficer !== '') {
                    $.ajax({
                        url: "{{ route('doc.surat-perintah-penyelidikan-document.api.moved-officers', ['accident_id' => $accidentId]) }}",
                        type: "GET",
                        dataType: "json",
                        data: {
                            searchedOfficerRegisterNumber: searchedOfficer
                        },
                        success: function(response) {
                            // Clear existing options
                            $('#officerMovedMemberOption').empty();

                            // Populate options based on response data
                            var member = response.data;
                            var rankName = (member.rank) ? member.rank.name : '-';
                            var positionName = (member.position) ? member.position.name : '-';
                            var policeName = (member.police) ? member.police.full_name : '-';

                            $('#officerMovedMemberOption').append($('<option>', {
                                value: member.id,
                                text: member.register_number + ' - ' + member
                                    .full_name + ' - ' + rankName,
                                'data-register-number': member.register_number,
                                'data-rank-name': rankName,
                                'data-name': member.full_name,
                                'data-position-name': positionName,
                                'data-police-name': policeName,
                            }));

                            $('#officerMovedMemberOption').prop('disabled', false);

                            $('#officerMovedMemberOption').select2({
                                theme: 'bootstrap4'
                            });
                        },
                        error: function(error, xhr, status) {
                            if (status == 'Not Found') {
                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Data Tidak Ditemukan',
                                    text: 'Data penyidik dengan NRP ' +
                                        searchedOfficer + ' tidak ditemukan',
                                });
                            }
                        }
                    });
                } else {
                    // Nonaktifkan opsi officerMovedMember dan hapus opsi yang ada
                    $('#officerMovedMemberOption').prop('disabled', true);
                    $('#officerMovedMemberOption').empty();
                }
            });

            $('#officerMovedMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerMovedMemberOption').find('option:selected');

                if (selectedOption.val() == '') {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Penyidik Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                // Ambil data yang diperlukan dari selectedOption
                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');

                var tablesToCheck = [{
                        tableSelector: '#movedOfficerMemberTable',
                        errorMessage: 'Personil sudah ada dalam daftar'
                    },
                    {
                        tableSelector: '#internalOfficerMemberTable',
                        errorMessage: 'Personil sudah ada dalam daftar penyidik'
                    },
                    {
                        tableSelector: '#externalOfficerMemberTable',
                        errorMessage: 'Personil sudah ada dalam daftar penyidik luar'
                    }
                ];

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var appendedRegisterNumber = $(this).find('.registerNumber').text();

                        if (appendedRegisterNumber == registerNumber) {
                            isAppended = true;
                            Swal.fire({
                                title: 'Gagal',
                                text: table.errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return false; // Keluar dari perulangan
                        }
                    });
                });

                if (!isAppended) {
                    // Buat baris baru untuk ditambahkan ke dalam tabel
                    var newRow = $('<tr class="text-center"></tr>');

                    // Tambahkan kolom-kolom dengan nilai yang diambil dari selectedOption
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + policeName + '</td>');
                    newRow.append('<td><input type="hidden" name="movedOfficers[]" value="' +
                        registerNumber +
                        '"><button class="btn btn-danger btn-sm deleteMovedOfficer" type="button"><i class="bi bi-trash"></i></button></td>'
                    );

                    // Tambahkan baris ke dalam tabel
                    $('#movedOfficerMemberTable tbody').append(newRow);

                    // Hapus event listener deleteMovedOfficer sebelumnya
                    $(document).off('click', '.deleteMovedOfficer');

                    // Tambahkan event listener deleteMovedOfficer yang baru
                    $(document).on('click', '.deleteMovedOfficer', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });

            // Add manual moved officer
            $('#addManualMovedOfficerButton').on('click', function() {
                $('#addManualMovedOfficerFormMode').val('create');

                // Kosongkan nilai input
                $('#addManualMovedOfficerForm #registerNumber').val('');
                $('#addManualMovedOfficerForm #name').val('');
                $('#addManualMovedOfficerForm #firstName').val('');
                $('#addManualMovedOfficerForm #lastName').val('');
                $('#addManualMovedOfficerForm #phone').val('');
                $('#addManualMovedOfficerForm #rank').val('');
                $('#addManualMovedOfficerForm #position').val('');
                $('#addManualMovedOfficerForm #regionalPolice').val('');
                $('#addManualMovedOfficerForm #resortPolice').val('');

                // call ajax get police
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penyelidikan-document.api.polices', ['accident_id' => $accidentId]) }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        policeClass: 'DAERAH'
                    },
                    success: function(response) {
                        // Clear existing options
                        $('#regionalPolice').empty();

                        // Populate options based on response data
                        var polices = response.data;
                        $('#regionalPolice').append($('<option>', {
                            value: '',
                            text: '--Pilih Polda--',
                        }));
                        $.each(polices, function(index, police) {
                            $('#regionalPolice').append($('<option>', {
                                value: police.id,
                                text: police.full_name,
                                'data-regional-police-name': police
                                    .full_name
                            }));
                        });
                        $('#addManualMovedOfficerModal #regionalPolice').select2({
                            dropdownParent: $('#regionalPolice').parent(),
                            theme: 'bootstrap4',
                            width: '100%'
                        });
                    },
                    error: function(error) {
                        return Swal.fire({
                            title: 'Gagal',
                            text: 'Maaf, terjadi kesalahan teknis.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });
            });
            $('#addManualMovedOfficerForm #regionalPolice').on('change', function() {
                // call ajax get police
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penyelidikan-document.api.polices', ['accident_id' => $accidentId]) }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        policeClass: 'RESOR',
                        policeId: $(this).val()
                    },
                    success: function(response) {
                        // Clear existing options
                        $('#resortPolice').empty();

                        // Populate options based on response data
                        var polices = response.data;
                        $('#resortPolice').append($('<option>', {
                            value: '',
                            text: '--Pilih Polres--',
                        }));
                        $.each(polices, function(index, police) {
                            $('#resortPolice').append($('<option>', {
                                value: police.id,
                                text: police.full_name,
                                'data-resort-police-name': police.full_name
                            }));
                        });
                        $('#addManualMovedOfficerModal #resortPolice').select2({
                            dropdownParent: $('#resortPolice').parent(),
                            theme: 'bootstrap4',
                            width: '100%'
                        });
                    },
                    error: function(error) {
                        return Swal.fire({
                            title: 'Gagal',
                            text: 'Maaf, terjadi kesalahan teknis.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                });
            });

            $('#saveAddManualMovedOfficerForm').on('click', function() {
                // Ambil nilai dari input
                var registerNumber = $('#addManualMovedOfficerForm #registerNumber').val();
                var firstName = $('#addManualMovedOfficerForm #firstName').val();
                var lastName = $('#addManualMovedOfficerForm #lastName').val();
                var name = firstName + ' ' + lastName;
                var phone = $('#addManualMovedOfficerForm #phone').val();
                var rankId = $('#addManualMovedOfficerForm #rank').find('option:selected').val();
                var rankName = $('#addManualMovedOfficerForm #rank').find('option:selected').data(
                    'rank-name');
                var positionId = $('#addManualMovedOfficerForm #position').find('option:selected').val();
                var positionName = $('#addManualMovedOfficerForm #position').find('option:selected').data(
                    'position-name');
                var regionalPoliceId = $('#addManualMovedOfficerForm #regionalPolice').find(
                    'option:selected').val();
                var regionalPoliceName = $('#addManualMovedOfficerForm #regionalPolice').find(
                    'option:selected').data('regional-police-name');
                var resortPoliceId = $('#addManualMovedOfficerForm #resortPolice').find('option:selected')
                    .val();
                var resortPoliceName = $('#addManualMovedOfficerForm #resortPolice').find('option:selected')
                    .data('resort-police-name');

                resortPoliceName = (resortPoliceId == '' || resortPoliceId == null) ? '' : resortPoliceName;
                var police = resortPoliceName + ' - ' + regionalPoliceName;

                // cek semua inputan sudah terisi
                if (registerNumber == '' || firstName == '' || phone == '' || rankId == '' || positionId ==
                    '' || regionalPoliceId == '') {
                    // append small text error di bawah inputan
                    $('#addManualMovedOfficerForm .mb-3').each(function() {
                        var small = $(this).find('small');
                        if (small.length == 0) {
                            $(this).append(
                                '<small class="text-danger">Inputan ini wajib diisi</small>');
                        }
                    });

                    return false;
                }

                var formMode = $('#addManualMovedOfficerFormMode').val();

                var tablesToCheck = [{
                        tableSelector: '#movedOfficerMemberTable',
                        errorMessage: 'Anggota dengan NRP tersebut sudah ada di dalam daftar'
                    },
                    {
                        tableSelector: '#internalOfficerMemberTable',
                        errorMessage: 'Anggota dengan NRP tersebut sudah ada di dalam daftar penyidik'
                    },
                    {
                        tableSelector: '#externalOfficerMemberTable',
                        errorMessage: 'Anggota dengan NRP tersebut sudah ada di dalam daftar penyidik eksternal'
                    }
                ];

                // cek apakah register number sudah ada di dalam table
                var isRegisterNumberExist = false;
                var isRegisterNumberExistErrorMessage = '';
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var registerNumberInTable = $(this).find('.registerNumber').text();
                        if (formMode == 'create') {
                            if (registerNumberInTable == registerNumber) {
                                isRegisterNumberExist = true;
                                return isRegisterNumberExistErrorMessage = table
                                    .errorMessage; // Keluar dari perulangan
                            }
                        } else if (formMode == 'edit') {
                            if (registerNumberInTable == registerNumber &&
                                registerNumberInTable != $('#oldRegisterNumber').val()) {
                                isRegisterNumberExist = true;
                                return isRegisterNumberExistErrorMessage = table
                                    .errorMessage; // Keluar dari perulangan
                            }
                        }
                    });
                });

                if (isRegisterNumberExist) {
                    if ($('#addManualMovedOfficerForm .alert').length == 0) {
                        $('#addManualMovedOfficerForm').prepend('<div class="alert alert-danger">' +
                            isRegisterNumberExistErrorMessage + '</div>');
                    }
                    return false;
                }
                $('#addManualMovedOfficerForm .alert').remove();


                if (formMode == "create") {
                    // Buat row baru di dalam table #movedOfficerMemberTable
                    var newRow = $('<tr class="text-center"></tr>');
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + police + '</td>');
                    newRow.append('<td>' +
                        '<input type="hidden" name="manualMovedOfficerRegisterNumbers[]" value="' +
                        registerNumber + '">' +
                        '<input type="hidden" name="manualMovedOfficerNames[]" value="' + name + '">' +
                        '<input type="hidden" name="manualMovedOfficerFirstNames[]" value="' +
                        firstName + '">' +
                        '<input type="hidden" name="manualMovedOfficerLastNames[]" value="' + lastName +
                        '">' +
                        '<input type="hidden" name="manualMovedOfficerPhones[]" value="' + phone +
                        '">' +
                        '<input type="hidden" name="manualMovedOfficerRankIds[]" value="' + rankId +
                        '">' +
                        '<input type="hidden" name="manualMovedOfficerPositionIds[]" value="' +
                        positionId + '">' +
                        '<input type="hidden" name="manualMovedOfficerPositionNames[]" value="' +
                        positionName + '">' +
                        '<input type="hidden" name="manualMovedOfficerRegionalPoliceIds[]" value="' +
                        regionalPoliceId + '">' +
                        '<input type="hidden" name="manualMovedOfficerResortPoliceIds[]" value="' +
                        resortPoliceId + '">' +
                        '<button class="btn btn-secondary btn-sm editManualMovedOfficer" type="button">' +
                        '<i class="bi bi-pencil-square"></i>' +
                        '</button>' +
                        '<button class="btn btn-danger btn-sm deleteManualMovedOfficer ml-1" type="button">' +
                        '<i class="bi bi-trash"></i>' +
                        '</button>' +
                        '</td>'
                    );

                    // Masukkan row baru ke dalam tbody table #movedOfficerMemberTable
                    $('#movedOfficerMemberTable tbody').append(newRow);
                } else if (formMode == "edit") {
                    var oldRegisterNumber = $('#addManualMovedOfficerForm #oldRegisterNumber').val()
                    // remove row yang lama
                    $('#movedOfficerMemberTable tbody tr').each(function() {
                        var rowRegisterNumber = $(this).find('.registerNumber').text();
                        if (rowRegisterNumber == oldRegisterNumber) {
                            $(this).remove();
                        }
                    });

                    // Buat row baru di dalam table #movedOfficerMemberTable
                    var newRow = $('<tr class="text-center"></tr>');
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + police + '</td>');
                    newRow.append('<td>' +
                        '<input type="hidden" name="manualMovedOfficerRegisterNumbers[]" value="' +
                        registerNumber + '">' +
                        '<input type="hidden" name="manualMovedOfficerNames[]" value="' + name + '">' +
                        '<input type="hidden" name="manualMovedOfficerFirstNames[]" value="' +
                        firstName + '">' +
                        '<input type="hidden" name="manualMovedOfficerLastNames[]" value="' + lastName +
                        '">' +
                        '<input type="hidden" name="manualMovedOfficerPhones[]" value="' + phone +
                        '">' +
                        '<input type="hidden" name="manualMovedOfficerRankIds[]" value="' + rankId +
                        '">' +
                        '<input type="hidden" name="manualMovedOfficerPositionIds[]" value="' +
                        positionId + '">' +
                        '<input type="hidden" name="manualMovedOfficerPositionNames[]" value="' +
                        positionName + '">' +
                        '<input type="hidden" name="manualMovedOfficerRegionalPoliceIds[]" value="' +
                        regionalPoliceId + '">' +
                        '<input type="hidden" name="manualMovedOfficerResortPoliceIds[]" value="' +
                        resortPoliceId + '">' +
                        '<button class="btn btn-secondary btn-sm editManualMovedOfficer" type="button">' +
                        '<i class="bi bi-pencil-square"></i>' +
                        '</button>' +
                        '<button class="btn btn-danger btn-sm deleteManualMovedOfficer ml-1" type="button">' +
                        '<i class="bi bi-trash"></i>' +
                        '</button>' +
                        '</td>'
                    );

                    // Masukkan row baru ke dalam tbody table #movedOfficerMemberTable
                    $('#movedOfficerMemberTable tbody').append(newRow);
                }

                // Kosongkan nilai input
                $('#addManualMovedOfficerForm #registerNumber').val('');
                $('#addManualMovedOfficerForm #name').val('');
                $('#addManualMovedOfficerForm #firstName').val('');
                $('#addManualMovedOfficerForm #lastName').val('');
                $('#addManualMovedOfficerForm #phone').val('');
                $('#addManualMovedOfficerForm #rank').val('');
                $('#addManualMovedOfficerForm #position').val('');
                $('#addManualMovedOfficerForm #regionalPolice').val('');
                $('#addManualMovedOfficerForm #resortPolice').val('');

                // Tutup modal
                $('#addManualMovedOfficerModal').modal('hide');
                $('.modal-backdrop').hide();

                // Hapus event listener deleteInternalOfficer sebelumnya
                $(document).off('click', '.deleteManualMovedOfficer');

                // Tambahkan event listener deleteInternalOfficer yang baru
                $(document).on('click', '.deleteManualMovedOfficer', function() {
                    $(this).closest('tr').remove();
                });

                // Hapus event listener editInternalOfficer sebelumnya
                $(document).off('click', '.editManualMovedOfficer');

                // Tambahkan event listener editInternalOfficer yang baru
                $(document).on('click', '.editManualMovedOfficer', function() {
                    $('#addManualMovedOfficerFormMode').val('edit');

                    // Ambil data dari row yang akan diedit
                    var registerNumber = $(this).closest('tr').find('.registerNumber').text();
                    var name = $(this).closest('tr').find('input[name="manualMovedOfficerNames[]"]')
                        .val();
                    var firstName = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerFirstNames[]"]').val();
                    var lastName = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerLastNames[]"]').val();
                    var phone = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerPhones[]"]').val();
                    var rankId = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerRankIds[]"]').val();
                    var positionId = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerPositionIds[]"]').val();
                    var regionalPoliceId = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerRegionalPoliceIds[]"]').val();
                    var resortPoliceId = $(this).closest('tr').find(
                        'input[name="manualMovedOfficerResortPoliceIds[]"]').val();

                    // Masukkan data ke dalam form
                    $('#addManualMovedOfficerForm #oldRegisterNumber').val(registerNumber);
                    $('#addManualMovedOfficerForm #registerNumber').val(registerNumber);
                    $('#addManualMovedOfficerForm #name').val(name);
                    $('#addManualMovedOfficerForm #firstName').val(firstName);
                    $('#addManualMovedOfficerForm #lastName').val(lastName);
                    $('#addManualMovedOfficerForm #phone').val(phone);
                    $('#addManualMovedOfficerForm #rank').val(rankId);
                    $('#addManualMovedOfficerForm #position').val(positionId);
                    $('#addManualMovedOfficerForm #regionalPolice').val(regionalPoliceId);
                    $('#addManualMovedOfficerForm #resortPolice').val(resortPoliceId);

                    // Tampilkan modal
                    $('#addManualMovedOfficerModal').modal('show');
                });
            });
        });

        // External Officer
        $(document).ready(function() {
            $('#officerExternalMemberOption').prop('disabled', true);
            $('#searchExternalOfficerButton').on('click', function() {
                var searchedOfficer = $('#searchExternalOfficerField').val();
                var accidentId = $('#accidentId').val();

                if (searchedOfficer !== '') {
                    $.ajax({
                        url: "{{ route('doc.surat-perintah-penyelidikan-document.api.external-officers', ['accident_id' => $accidentId]) }}",
                        type: "GET",
                        dataType: "json",
                        data: {
                            searchedOfficerRegisterNumber: searchedOfficer
                        },
                        success: function(response) {
                            // Clear existing options
                            $('#officerExternalMemberOption').empty();

                            // Populate options based on response data
                            var member = response.data;
                            var rankName = (member.rank) ? member.rank.name : '-';
                            var positionName = (member.position) ? member.position.name : '-';
                            var policeName = (member.police) ? member.police.full_name : '-';

                            $('#officerExternalMemberOption').append($('<option>', {
                                value: member.id,
                                text: member.register_number + ' - ' + member
                                    .full_name + ' - ' + rankName,
                                'data-register-number': member.register_number,
                                'data-rank-name': rankName,
                                'data-name': member.full_name,
                                'data-position-name': positionName,
                                'data-police-name': policeName,
                            }));

                            $('#officerExternalMemberOption').prop('disabled', false);

                            $('#officerExternalMemberOption').select2({
                                theme: 'bootstrap4',
                            });
                        },
                        error: function(error, xhr, status) {
                            if (status == 'Not Found') {
                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Data Tidak Ditemukan',
                                    text: 'Data penyidik dengan NRP ' +
                                        searchedOfficer + ' tidak ditemukan',
                                });
                            }
                        }
                    });
                } else {
                    // Nonaktifkan opsi officerExternalMember dan hapus opsi yang ada
                    $('#officerExternalMemberOption').prop('disabled', true);
                    $('#officerExternalMemberOption').empty();
                }
            });

            $('#officerExternalMemberOptionAddButtton').on('click', function() {
                var selectedOption = $('#officerExternalMemberOption').find('option:selected');

                if (selectedOption.val() == '') {
                    return Swal.fire({
                        title: 'Gagal',
                        text: 'Pilih Penyidik Terlebih Dahulu',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                // Ambil data yang diperlukan dari selectedOption
                var registerNumber = selectedOption.data('register-number');
                var rankName = selectedOption.data('rank-name');
                var name = selectedOption.data('name');
                var positionName = selectedOption.data('position-name');
                var policeName = selectedOption.data('police-name');

                var tablesToCheck = [{
                        tableSelector: '#externalOfficerMemberTable',
                        errorMessage: 'Personil sudah ada dalam daftar penyidik luar'
                    },
                    {
                        tableSelector: '#internalOfficerMemberTable',
                        errorMessage: 'Personil sudah ada dalam daftar penyidik'
                    },
                    {
                        tableSelector: '#movedOfficerMemberTable',
                        errorMessage: 'Personil sudah ada dalam daftar penyidik yang telah pindah'
                    }
                ];

                // Cek apakah opsi sudah terappend dalam tabel
                var isAppended = false;
                tablesToCheck.forEach(function(table) {
                    $(table.tableSelector).find('tbody tr').each(function() {
                        var appendedRegisterNumber = $(this).find('.registerNumber').text();

                        if (appendedRegisterNumber == registerNumber) {
                            isAppended = true;
                            Swal.fire({
                                title: 'Gagal',
                                text: table.errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return false; // Keluar dari perulangan
                        }
                    });
                });

                if (!isAppended) {
                    // Buat baris baru untuk ditambahkan ke dalam tabel
                    var newRow = $('<tr class="text-center"></tr>');

                    // Tambahkan kolom-kolom dengan nilai yang diambil dari selectedOption
                    newRow.append('<td>' + name + '</td>');
                    newRow.append('<td>' + rankName + '</td>');
                    newRow.append('<td class="registerNumber">' + registerNumber + '</td>');
                    newRow.append('<td>' + positionName + '</td>');
                    newRow.append('<td>' + policeName + '</td>');
                    newRow.append('<td><input type="hidden" name="externalOfficers[]" value="' +
                        registerNumber +
                        '"><button class="btn btn-danger btn-sm deleteExternalOfficer" type="button"><i class="bi bi-trash"></i></button></td>'
                    );

                    // Tambahkan baris ke dalam tabel
                    $('#externalOfficerMemberTable tbody').append(newRow);

                    // Hapus event listener deleteExternalOfficer sebelumnya
                    $(document).off('click', '.deleteExternalOfficer');

                    // Tambahkan event listener deleteExternalOfficer yang baru
                    $(document).on('click', '.deleteExternalOfficer', function() {
                        $(this).closest('tr').remove();
                    });
                }
            });
        });

        // Validasi Submit Form
        $(document).ready(function() {
            $('#suratPerintahPenyelidikanFormSubmit').on('click', function(e) {
                e.preventDefault();

                // Lakukan validasi di sisi server menggunakan Ajax
                $.ajax({
                    url: "{{ route('doc.surat-perintah-penyelidikan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $('#suratPerintahPenyelidikanForm').serialize(),
                    success: function(response) {
                        // Cek jika validasi berhasil di sisi server
                        if (response.success) {
                            // sweetalert2 berhasil sebelum submit form
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                // Submit form
                                $('#suratPerintahPenyelidikanForm').submit();
                            });
                        }
                    },
                    error: function(xhr) {
                        // Tangani error jika terjadi kesalahan saat melakukan validasi
                        response = JSON.parse(xhr.responseText);

                        if (response.code == '422') {
                            var errorMessages = '';

                            $.each(response.errors, function(key, value) {
                                errorMessages += '- ' + value + '<br>';
                            });

                            return Swal.fire({
                                icon: 'error',
                                title: 'Mohon Periksa Kembali Isian Anda',
                                html: errorMessages,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
