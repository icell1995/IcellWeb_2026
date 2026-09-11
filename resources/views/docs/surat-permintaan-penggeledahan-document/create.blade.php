@php
    $_title = 'Surat Permintaan Izin Penggeledahan — Pusiknas Bareskrim (SPPT-TI)';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">
                Tambah Surat Permintaan Izin Penggeledahan
                <span class="badge badge-warning ms-2 text-dark">SPPT-TI / Pusiknas Bareskrim</span>
            </h5>

            <div class="alert alert-danger" id="attentionBox">
                <div class="text-center">
                    <b>
                        PERHATIAN !<br /><br />
                        DATA INI AKAN DIPERTUKARKAN DENGAN PUSIKNAS BARESKRIM POLRI
                        DALAM KERANGKA SPPT-TI. KODE PROSES: <strong>GLDH-10</strong>
                    </b>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>

        <div class="box-body">
            <form action="{{ route('doc.surat-permintaan-penggeledahan-document.store', ['accident_id' => $accidentId]) }}"
                  method="POST" enctype="multipart/form-data" id="suratPermintaanPenggeledahanForm">
                @csrf
                {{-- Input Hidden --}}
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">
                <input type="hidden" name="noLp" value="{{ $accident->no_lp }}">
                <input type="hidden" name="documentDate" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="tanggal_terbit_lp" value="{{ $accident->created_at?->format('Y-m-d') }}">
                <input type="hidden" name="tanggal_sprindik" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="tanggal_spdp" value="{{ date('Y-m-d') }}">
                

                <div class="row">
                    <div class="col-12">
                        {{-- Identitas Document --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="documentNumber">Nomor Surat <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" id="documentNumber" name="documentNumber" class="form-control font-weight-bold" value="S10/{{ date('d/m/Y') }}" readonly>
                            </div>
                        </div>

                        <hr>

                        {{-- Konten Document --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Nomor LP (dari perkara)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control font-weight-bold" value="{{ $accident->no_lp }}" readonly>
                            </div>
                        </div>

                        <hr>

                           {{-- Nomor Surat Perintah --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="nomorSuratPerintah">Nomor Surat Perintah Penggeledahan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="nomorSuratPerintah" type="text" class="form-control" name="nomorSuratPerintah" value="{{ old('nomorSuratPerintah') }}" placeholder="Contoh: B/45/XII/2024/Reskrim" required>
                            </div>
                        </div>

                        {{-- Tanggal Surat Perintah --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="tglSuratPerintah">Tanggal Surat Perintah Penggeledahan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="tglSuratPerintah" type="text" class="form-control datepicker" name="tglSuratPerintah" value="{{ old('tglSuratPerintah') }}" placeholder="YYYY-MM-DD" autocomplete="off" data-provide="datepicker" required>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="no_sprindik">Nomor SPRINDIK <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="no_sprindik" id="no_sprindik" required>
                                    <option value="">-- Pilih Nomor SPRINDIK --</option>
                                    @foreach ($sprindikDocuments as $sprindik)
                                        <option value="{{ $sprindik->id }}" 
                                                data-pasal="{{ $sprindik->pasal_formatted }}" 
                                                data-tindak-pidana="{{ $sprindik->tindak_pidana_formatted }}" 
                                                {{ old('no_sprindik') == $sprindik->id ? 'selected' : '' }}>
                                            {{ $sprindik->document_number }} ({{ $sprindik->document_date ? date('d/m/Y', strtotime($sprindik->document_date)) : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Nomor SPDP Terkait --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="no_spdp">Nomor SPDP Terkait <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="no_spdp" id="no_spdp" required>
                                    <option value="">-- Pilih Nomor SPDP --</option>
                                    @foreach ($spdpDocuments as $spdp)
                                        <option value="{{ $spdp->id }}" {{ old('no_spdp') == $spdp->id ? 'selected' : '' }}>
                                            {{ $spdp->document_number }} ({{ $spdp->document_date ? date('d/m/Y', strtotime($spdp->document_date)) : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                     

                        {{-- Undang-Undang Pasal --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="undangUndangPasal">Daftar UU Pasal <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" id="undangUndangPasal" name="undangUndangPasal" class="form-control" value="{{ old('undangUndangPasal') }}" placeholder="Contoh: Pasal 362 KUHP" required readonly>
                            </div>
                        </div>

                        {{-- Jenis Tindak Pidana --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="jenisTindakPidana">Jenis Tindak Pidana <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" id="jenisTindakPidana" name="jenisTindakPidana" class="form-control" value="{{ old('jenisTindakPidana') }}" placeholder="Contoh: Pencurian dengan Pemberatan" required readonly>
                            </div>
                        </div>

                        <hr>

                        {{-- Tersangka Terkait --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="suspects_id">Tersangka Terkait <small class="text-muted">(opsional)</small></label>
                            <div class="col-sm-9">
                                <select class="form-control select2-multiple" name="suspects_id[]" id="suspects_id" multiple>
                                    @foreach ($suspects as $suspect)
                                        <option value="{{ $suspect->id }}">{{ $suspect->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Jenis Target Penggeledahan --}}
                         <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="daftar_penggeledahan">Daftar / Jenis Lokasi Penggeledahan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2-multiple" name="daftar_penggeledahan[]" id="daftar_penggeledahan" multiple="multiple" required>
                                    <option value="Rumah / Tempat Kediaman" {{ (is_array(old('daftar_penggeledahan')) && in_array('Rumah / Tempat Kediaman', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Rumah / Tempat Kediaman</option>
                                    <option value="Tempat Tertutup Lainnya" {{ (is_array(old('daftar_penggeledahan')) && in_array('Tempat Tertutup Lainnya', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Tempat Tertutup Lainnya</option>
                                    <option value="Alat Angkut / Kendaraan" {{ (is_array(old('daftar_penggeledahan')) && in_array('Alat Angkut / Kendaraan', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Alat Angkut / Kendaraan</option>
                                    <option value="Badan / Pakaian" {{ (is_array(old('daftar_penggeledahan')) && in_array('Badan / Pakaian', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Badan / Pakaian</option>
                                    <option value="Pekarangan / Area Terbuka" {{ (is_array(old('daftar_penggeledahan')) && in_array('Pekarangan / Area Terbuka', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Pekarangan / Area Terbuka</option>
                                    <option value="Kantor / Tempat Usaha / Bangunan" {{ (is_array(old('daftar_penggeledahan')) && in_array('Kantor / Tempat Usaha / Bangunan', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Kantor / Tempat Usaha / Bangunan</option>
                                    <option value="Lainnya" {{ (is_array(old('daftar_penggeledahan')) && in_array('Lainnya', old('daftar_penggeledahan'))) ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        {{-- Pemilik / Penguasai Tempat yang Digeledah (Dihapus/Diberi Komentar Blade jika Tidak Digunakan)
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="pemilikTempat">Pemilik/Penguasai Sasaran <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input id="pemilikTempat" type="text" class="form-control" name="pemilikTempat" value="{{ old('pemilikTempat') }}" placeholder="Nama pemilik rumah / badan / pakaian yang digeledah">
                            </div>
                        </div>
                        --}}

                        {{-- Lokasi / Alamat Penggeledahan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="alamatPenggeledahan">Lokasi/Alamat/Uraian Sasaran <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea id="alamatPenggeledahan" class="form-control" name="alamatPenggeledahan" rows="3" placeholder="Masukkan alamat lengkap lokasi penggeledahan atau uraian target" required>{{ old('alamatPenggeledahan') }}</textarea>
                            </div>
                        </div>

                        <hr>

                        {{-- Penandatangan --}}
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="signatory_id">Penandatangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="signatory_id" id="signatory_id" required>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $officer)
                                        <option value="{{ $officer->id }}" {{ old('signatory_id') == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name) }} — {{ $officer->position->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Lampiran Digital (PDF) (Dihapus/Diberi Komentar Blade jika Tidak Digunakan)
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="dokumen_digital">File Surat Permintaan Izin (PDF) <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="file" id="dokumen_digital" name="dokumen_digital" class="form-control" accept="application/pdf">
                                <small class="text-muted">Upload dokumen surat permintaan izin penggeledahan hasil scan/digital dalam format PDF.</small>
                            </div>
                        </div>
                        --}}

                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2 mt-3">
                    <button type="button" id="suratPermintaanPenggeledahanFormSubmit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Surat Permintaan
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Attention blink
            setInterval(function () { $('#attentionBox').toggleClass('alert-danger alert-warning'); }, 1000);

            // Datepicker
            $('#tglSuratPerintah').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'auto bottom',
                endDate: new Date()
            });
            $('#tglSuratPerintah').keydown(function (e) { e.preventDefault(); return false; });

            // Select2
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
            $('.select2-multiple').select2({ theme: 'bootstrap4', width: '100%' });

            // Auto-fill undangUndangPasal dan jenisTindakPidana berdasarkan SPRINDIK yang dipilih
            $('#no_sprindik').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var pasal = selectedOption.data('pasal') || '';
                var tindakPidana = selectedOption.data('tindak-pidana') || '';

                $('#undangUndangPasal').val(pasal);
                $('#jenisTindakPidana').val(tindakPidana);
            });

            // Trigger change jika sudah terisi (misalnya saat reload old input)
            if ($('#no_sprindik').val()) {
                $('#no_sprindik').trigger('change');
            }

            // AJAX Validate & Submit
            $('#suratPermintaanPenggeledahanFormSubmit').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('doc.surat-permintaan-penggeledahan-document.api.validate-request-form', ['accident_id' => $accidentId]) }}",
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: new FormData($('#suratPermintaanPenggeledahanForm')[0]),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil Validasi',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Simpan'
                            }).then(function (result) {
                                if (result.isConfirmed) { $('#suratPermintaanPenggeledahanForm').submit(); }
                            });
                        }
                    },
                    error: function (xhr) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.code == '422') {
                            var errorMessages = '';
                            $.each(response.errors, function (key, value) { errorMessages += '- ' + value + '<br>'; });
                            Swal.fire({ icon: 'error', title: 'Periksa Isian', html: errorMessages });
                        }
                    }
                });
            });
        });
    </script>
@endpush
