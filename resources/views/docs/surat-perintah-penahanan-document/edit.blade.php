@php
    $_title = 'Surat Perintah Penahanan';
    $d = $defaults ?? [];
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Surat Perintah Penahanan</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-perintah-penahanan-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}"
                method="POST">
                @csrf

                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <!-- Nomor LP -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Nomor LP</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp ?? '' }}"
                            readonly>
                    </div>
                </div>

                <!-- Nomor Dokumen -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="document_number">Nomor Dokumen <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input type="text" name="document_number" id="document_number" class="form-control"
                            value="{{ old('document_number', $d['document_number'] ?? '') }}" required>
                    </div>
                </div>

                <!-- Surat Perintah Penyidikan -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_perintah_penyidikan_document_id">No Sprindik
                        <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <select name="surat_perintah_penyidikan_document_id" id="surat_perintah_penyidikan_document_id"
                            class="form-control select2" required>
                            <option value="">--Pilih No Surat Perintah Penyidikan--</option>
                            @foreach ($suratPerintahPenyidikanDocuments as $doc)
                                @php
                                    $sel = old('surat_perintah_penyidikan_document_id', $d['surat_perintah_penyidikan_document_id'] ?? '');
                                @endphp
                                <option value="{{ $doc->id }}" {{ (string) $sel === (string) $doc->id ? 'selected' : '' }}>
                                    {{ $doc->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Surat Ketetapan Tersangka -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="surat_ketetapan_penetapan_tersangka_id">No Surat
                        Ketetapan Tersangka <span class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <select name="surat_ketetapan_penetapan_tersangka_id" id="surat_ketetapan_penetapan_tersangka_id"
                            class="form-control select2" required>
                            <option value="">--Pilih No Surat Ketetapan Penetapan Tersangka--</option>
                            @foreach ($suratPenetapanTersangkaDocuments as $doc)
                                @php
                                    $sel = old('surat_ketetapan_penetapan_tersangka_id', $d['surat_ketetapan_penetapan_tersangka_id'] ?? '');
                                @endphp
                                <option value="{{ $doc->id }}" {{ (string) $sel === (string) $doc->id ? 'selected' : '' }}>
                                    {{ $doc->document_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Yang Menandatangani -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="signatory_id">
                        Yang Menandatangani <span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-9">
                        @php
                            $sel = old('signatory_id', $d['signatory_id'] ?? '');
                        @endphp
                        <select name="signatory_id" id="signatory_id" class="form-control select2" required>
                            <option value="">--Pilih Yang Menandatangani--</option>
                            @foreach ($authorizedSignatories as $data)
                                @php $positionName = $data->position->name ?? ''; @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}"
                                    {{ (string) $sel === (string) $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tanggal Dokumen -->
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label">Tanggal Ditandatangani <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        <input type="text" name="document_date" id="document_date" class="form-control"
                            placeholder="YYYY-MM-DD" value="{{ old('document_date', $d['document_date'] ?? '') }}" required>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">Tim Penyidik</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="officer_id">
                        Ketua Tim Penyidik <span class="text-danger fs-5">*</span>
                    </label>
                    <div class="col-lg-9">
                        @php
                            $sel = old('officer_id', $d['officer_id'] ?? '');
                        @endphp
                        <select name="officer_id" id="officer_id" class="form-control select2" required>
                            <option value="">--Pilih Petugas Yang Diperintahkan--</option>
                            @foreach ($officers as $data)
                                @php $positionName = $data->position->name ?? ''; @endphp
                                <option value="{{ $data->id }}" data-register-number="{{ $data->register_number }}"
                                    {{ (string) $sel === (string) $data->id ? 'selected' : '' }}>
                                    {{ $data->register_number . ' - ' . $data->full_name . ' | ' . $positionName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row col-12 my-2 ms-0">
                    <label class="fw-bold">Penyidik<small> (*Pilihan Penyidik akan tampil setelah Ketua Tim
                            Penyidik dipilih)</small></label>

                    <div id="internalOfficer">
                        <div class="alert alert-primary my-2" role="alert">
                            1. Pilihan Penyidik akan tampil setelah Ketua Tim Penyidik dipilih. <br />
                            2. Pilih personel lalu klik tombol 'Tambah' untuk menambahkan personel sebagai penyidik.
                        </div>

                        <div class="row my2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="custom-select select2-input-group" id="officerInternalMemberOption"
                                        aria-describedby="officerInternalMemberOptionAddButtton">
                                        <option value="">--Pilih Penyidik--</option>
                                    </select>
                                    <button class="btn btn-primary" type="button"
                                        id="officerInternalMemberOptionAddButtton"><i class="bi bi-plus-circle"></i>
                                        Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive my-2">
                            <table class="table table-bordered" id="internalOfficerMemberTable">
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

                <hr>

                <h5 class="fw-bold text-blue-dark">PILIH TERSANGKA</h5>
                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="tersangka_id">Tersangka yang ditetapkan <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        @php
                            $sel = old('tersangka_id', $d['tersangka_id'] ?? '');
                        @endphp
                        <select name="tersangka_id" id="tersangka_id" class="form-control select2" required>
                            <option value="">--Pilih Tersangka--</option>
                            @foreach ($suspects as $suspect)
                                <option value="{{ $suspect->id }}" {{ (string) $sel === (string) $suspect->id ? 'selected' : '' }}>
                                    {{ $suspect->full_name ?? $suspect->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold text-blue-dark">KETERANGAN PENAHANAN</h5>

                <div class="input-group row mb-3 ms-0">
                    <label class="fw-bold col-sm-3 col-form-label" for="jenis_penahanan">Jenis Penahanan <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-9">
                        @php
                            $sel = old('jenis_penahanan', $d['jenis_penahanan'] ?? '');
                        @endphp
                        <select name="jenis_penahanan" id="jenis_penahanan" class="form-control select2" required>
                            <option value="">--Pilih Jenis Penahanan--</option>
                            @foreach ($detentionTypes as $detention)
                                <option value="{{ $detention->type_name }}"
                                    {{ (string) $sel === (string) $detention->type_name ? 'selected' : '' }}>
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
                            @php
                                $sel = old('lokasi_penahanan', $d['lokasi_penahanan'] ?? '');
                            @endphp
                            <select name="lokasi_penahanan" id="lokasi_penahanan" class="form-control select2">
                                <option value="">--Pilih Lokasi Penahanan--</option>
                                @foreach ($prisons as $prison)
                                    <option value="{{ $prison->name }}" data-branch="{{ $prison->branch ?? '' }}"
                                        {{ (string) $sel === (string) $prison->name ? 'selected' : '' }}>
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
                            @php
                                $sel = old('cabang_penahanan', $d['cabang_penahanan'] ?? '');
                            @endphp
                            <select name="cabang_penahanan" id="cabang_penahanan" class="form-control select2">
                                <option value="">--Pilih Cabang--</option>
                                @if (! empty($sel))
                                    <option value="{{ $sel }}" selected>{{ $sel }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div id="rumahFields" class="penahanan-fields" style="display: none;">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Alamat Penahanan</label>
                        <div class="col-lg-9">
                            <textarea name="alamat_penahanan" id="alamat_penahanan" class="form-control" rows="3" readonly></textarea>
                        </div>
                    </div>
                </div>

                <div id="kotaFields" class="penahanan-fields" style="display: none;">
                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-3 col-form-label">Kota/Kabupaten Penahanan</label>
                        <div class="col-lg-9">
                            <input type="text" name="kota_penahanan" id="kota_penahanan" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark-blue">
                        <i class="bi bi-save"></i> Simpan
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
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                allowClear: true
            });
            $('.select2-input-group').select2({
                theme: 'bootstrap4'
            });

            $('#document_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                endDate: new Date()
            });
            $('#document_date').keydown(function(e) {
                e.preventDefault();
                return false;
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const suspectAddresses = @json($suspectAddresses ?? []);
            const suspectRegencies = @json($suspectRegencies ?? []);

            const jenisPenahananSelect = $('#jenis_penahanan');
            const tersangkaSelect = $('#tersangka_id');

            const rutanFields = $('#rutanFields');
            const rumahFields = $('#rumahFields');
            const kotaFields = $('#kotaFields');

            const lokasiPenahanan = $('#lokasi_penahanan');
            const cabangPenahanan = $('#cabang_penahanan');

            function showFieldsByJenis(jenis) {
                $('.penahanan-fields').hide();
                if (jenis === 'Penahanan Rumah Tahanan Negara') {
                    rutanFields.show();
                    lokasiPenahanan.prop('required', true);
                    cabangPenahanan.prop('required', true);
                } else if (jenis === 'Penahanan Rumah') {
                    rumahFields.show();
                    lokasiPenahanan.prop('required', false);
                    cabangPenahanan.prop('required', false);
                    loadAlamatTersangka();
                } else if (jenis === 'Penahanan Kota') {
                    kotaFields.show();
                    lokasiPenahanan.prop('required', false);
                    cabangPenahanan.prop('required', false);
                    loadKotaTersangka();
                }
            }

            function loadAlamatTersangka() {
                const suspectId = tersangkaSelect.val();
                $('#alamat_penahanan').val((suspectId && suspectAddresses[suspectId]) ? suspectAddresses[suspectId] : '');
            }

            function loadKotaTersangka() {
                const suspectId = tersangkaSelect.val();
                $('#kota_penahanan').val((suspectId && suspectRegencies[suspectId]) ? suspectRegencies[suspectId] : '');
            }

            jenisPenahananSelect.on('change', function() {
                showFieldsByJenis($(this).val());
            });

            tersangkaSelect.on('change', function() {
                const jenis = jenisPenahananSelect.val();
                if (jenis === 'Penahanan Rumah') loadAlamatTersangka();
                if (jenis === 'Penahanan Kota') loadKotaTersangka();
            });

            lokasiPenahanan.on('change', function() {
                const branch = $(this).find('option:selected').data('branch');
                if (branch) {
                    cabangPenahanan.prop('disabled', false)
                        .empty()
                        .append(`<option value="${branch}">${branch}</option>`)
                        .val(branch)
                        .trigger('change');
                } else {
                    cabangPenahanan.prop('disabled', true)
                        .empty()
                        .append('<option value="">--Pilih Cabang--</option>');
                }
            });

            // initial show based on current value
            showFieldsByJenis(jenisPenahananSelect.val());
        });
    </script>

    <script>
        $(document).ready(function() {
            const allOfficers = @json($allOfficers ?? []);
            let selectedChairmanId = $('#officer_id').val() || null;
            let selectedMembers = @json($selectedMembers ?? []);

            const chairmanSelect = $('#officer_id');
            const memberSelect = $('#officerInternalMemberOption');
            const addButton = $('#officerInternalMemberOptionAddButtton');
            const tableBody = $('#internalOfficerMemberTable tbody');
            const form = $('form');

            memberSelect.prop('disabled', !selectedChairmanId).trigger('change');

            function populateMemberDropdown() {
                memberSelect.empty();
                memberSelect.append(new Option('--Pilih Penyidik--', ''));
                allOfficers.forEach(function(officer) {
                    if (selectedChairmanId && officer.id == selectedChairmanId) return;
                    if (selectedMembers.some(m => m.id == officer.id)) return;
                    const text = `${officer.register_number} - ${officer.full_name} | ${officer.position_name}`;
                    memberSelect.append(new Option(text, officer.id));
                });
                memberSelect.val('').trigger('change');
            }

            function renderMemberTable() {
                tableBody.empty();
                selectedMembers.forEach(function(officer, index) {
                    const row = `
                    <tr>
                        <td>${officer.full_name}</td>
                        <td>${officer.rank_name}</td>
                        <td>${officer.register_number}</td>
                        <td>${officer.position_name}</td>
                        <td>${officer.police_name || '-'}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-member" data-index="${index}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>`;
                    tableBody.append(row);
                });

                $('.remove-member').off('click').on('click', function() {
                    const index = parseInt($(this).data('index'));
                    selectedMembers.splice(index, 1);
                    renderMemberTable();
                    populateMemberDropdown();
                });
            }

            chairmanSelect.on('change', function() {
                selectedChairmanId = $(this).val();
                if (selectedChairmanId) {
                    memberSelect.prop('disabled', false);
                    populateMemberDropdown();
                } else {
                    memberSelect.prop('disabled', true).val(null).trigger('change');
                }
            });

            addButton.on('click', function() {
                const officerId = memberSelect.val();
                if (!officerId) return;
                const officer = allOfficers.find(o => o.id == officerId);
                if (!officer) return;
                if (selectedMembers.some(m => m.id == officer.id)) return;
                selectedMembers.push(officer);
                renderMemberTable();
                populateMemberDropdown();
            });

            // init
            renderMemberTable();
            populateMemberDropdown();

            form.on('submit', function() {
                $('.personnel-hidden').remove();
                selectedMembers.forEach(function(member) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'personnel[]',
                        value: member.id,
                        class: 'personnel-hidden'
                    }));
                });
            });
        });
    </script>
@endpush

