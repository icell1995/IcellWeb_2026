@php
    $_title = 'Edit Undangan Gelar Perkara';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group > .select2-container--bootstrap4 {
            flex: 1 1 auto;
            width: 1% !important;
        }
        .input-group > .select2-container--bootstrap4 .select2-selection--single {
            height: calc(1.5em + .75rem + 2px) !important;
            display: flex;
            align-items: center;
        }
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-custom .card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Edit Undangan Gelar Perkara</h5>
            <small class="text-muted d-block mb-3">Kode Dokumen: <b>DCT-0701</b> │ Kategori: <b>07 (Dokumen Umum / Gelar Perkara)</b></small>

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
            <form action="{{ route('doc.undangan-gelar-perkara-document.update', ['id' => $document->id, 'accident_id' => $accidentId]) }}" method="POST">
                @csrf
                <input type="hidden" name="accident_id" value="{{ $accidentId }}">

                <!-- CARD 1: INFORMASI UMUM SURAT -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-envelope-paper me-1"></i> I. INFORMASI & NOMOR SURAT UNDANGAN
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Nomor Surat Undangan</label>
                                <input type="text" name="nomor" class="form-control" value="{{ old('nomor', $document->nomor) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Klasifikasi / Sifat Surat <span class="text-danger">*</span></label>
                                <select name="sifat" class="form-control custom-select" required>
                                    <option value="Biasa" {{ old('sifat', $document->sifat) == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                                    <option value="Segera" {{ old('sifat', $document->sifat) == 'Segera' ? 'selected' : '' }}>Segera</option>
                                    <option value="Rahasia" {{ old('sifat', $document->sifat) == 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Lampiran</label>
                                <input type="text" name="lampiran" class="form-control" value="{{ old('lampiran', $document->lampiran) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Perihal Surat <span class="text-danger">*</span></label>
                                <input type="text" name="perihal" class="form-control" value="{{ old('perihal', $document->perihal) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $document->tanggal ? \Carbon\Carbon::parse($document->tanggal)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Tempat Dikeluarkan Surat</label>
                                <input type="text" name="tempat_surat" class="form-control" value="{{ old('tempat_surat', $document->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: DASAR & RUJUKAN GELAR PERKARA -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-journal-text me-1"></i> II. DASAR & RUJUKAN HUKUM
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Nomor Laporan Polisi (LP)</label>
                                <input type="text" class="form-control bg-light" value="{{ $document->nomor_lp ?: ($accident->no_lp ?: ($accident->nomor_laporan_polisi ?: ($accident->accident_number ?: '-'))) }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Nomor Surat Perintah Penyidikan (Sprindik)</label>
                                <input type="text" class="form-control bg-light" value="{{ $document->nomor_sprindik ?: '-' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Rujukan / Dasar Hukum Pelaksanaan Gelar</label>
                            <textarea name="dasar_hukum" class="form-control" rows="5">{{ old('dasar_hukum', $document->dasar_hukum) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: WAKTU, TEMPAT & AGENDA GELAR PERKARA -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-calendar-event me-1"></i> III. WAKTU, TEMPAT & AGENDA GELAR PERKARA
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Hari Pelaksanaan <span class="text-danger">*</span></label>
                                <input type="text" name="hari_pelaksanaan" class="form-control" value="{{ old('hari_pelaksanaan', $document->hari_pelaksanaan) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label font-weight-bold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pelaksanaan" class="form-control" value="{{ old('tanggal_pelaksanaan', $document->tanggal_pelaksanaan ? \Carbon\Carbon::parse($document->tanggal_pelaksanaan)->format('Y-m-d') : date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-weight-bold">Jam / Pukul <span class="text-danger">*</span></label>
                                <input type="text" name="jam_pelaksanaan" class="form-control" value="{{ old('jam_pelaksanaan', $document->jam_pelaksanaan) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Tempat Pelaksanaan <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_pelaksanaan" class="form-control" value="{{ old('tempat_pelaksanaan', $document->tempat_pelaksanaan) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Agenda / Masalah yang Digelarkan <span class="text-danger">*</span></label>
                            <textarea name="agenda" class="form-control" rows="3" required>{{ old('agenda', $document->agenda) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: PIHAK YANG DIUNDANG -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-people me-1"></i> IV. TUJUAN UNDANGAN (KEPADA YTH)
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Daftar Pihak / Pejabat yang Diundang (Teks Kop/Tujuan) <span class="text-danger">*</span></label>
                            <textarea name="tujuan_undangan" class="form-control" rows="5" required>{{ old('tujuan_undangan', $document->tujuan_undangan) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Pilih Personel / Petugas Internal yang Diundang (Opsional)</label>
                            <select name="officer_invitees[]" id="officer_invitees" class="form-control select2bs4" multiple="multiple" data-placeholder="Pilih petugas...">
                                @foreach ($officers as $officer)
                                    <option value="{{ $officer->id }}" {{ in_array($officer->id, old('officer_invitees', $selectedInviteeIds ?? [])) ? 'selected' : '' }}>
                                        {{ $officer->full_name }} — {{ $officer->position->name ?? '-' }} ({{ $officer->rank->name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CARD 5: PEJABAT PENANDATANGAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-pen me-1"></i> V. PEJABAT PENANDATANGAN (ATASAN / PENYIDIK)
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Head Text / An. Jabatan (Opsional)</label>
                                <input type="text" name="signatory_head_text" class="form-control" value="{{ old('signatory_head_text', $document->signatory_head_text) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Pilih Pejabat Penandatangan <span class="text-danger">*</span></label>
                                <select name="signatory" id="signatory_select" class="form-control select2bs4" required>
                                    <option value="">-- Pilih Pejabat Penandatangan --</option>
                                    @foreach ($authorizedSignatories as $sig)
                                        <option value="{{ $sig->id }}"
                                            data-name="{{ $sig->full_name }}"
                                            data-rank="{{ $sig->rank->name ?? '-' }}"
                                            data-nrp="{{ $sig->register_number ?? '-' }}"
                                            data-position="{{ $sig->position->name ?? 'KASAT LANTAS' }}"
                                            {{ old('signatory', $document->signatory_id) == $sig->id ? 'selected' : '' }}>
                                            {{ $sig->full_name }} ({{ $sig->position->name ?? '-' }} / {{ $sig->rank->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="signatory_preview" class="p-3 bg-light rounded border mt-2">
                            <span class="font-weight-bold d-block text-secondary mb-1">Pratinjau Pejabat:</span>
                            <div class="row">
                                <div class="col-md-4"><strong>Jabatan:</strong> <span id="prev_position">{{ $document->signatory_position ?? '-' }}</span></div>
                                <div class="col-md-4"><strong>Nama:</strong> <span id="prev_name">{{ $document->signatory_name ?? '-' }}</span></div>
                                <div class="col-md-4"><strong>Pangkat/NRP:</strong> <span id="prev_rank_nrp">{{ ($document->signatory_rank ?? '-') . ' / NRP. ' . ($document->signatory_nrp ?? '-') }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 6: TEMBUSAN & CATATAN -->
                <div class="card card-custom">
                    <div class="card-header text-primary">
                        <i class="bi bi-list-check me-1"></i> VI. TEMBUSAN & CATATAN
                    </div>
                    <div class="card-body">
                        <label class="form-label font-weight-bold">Daftar Tembusan Surat</label>
                        <div id="tembusan_wrapper">
                            @php
                                $tembusanList = is_array($document->tembusan) ? $document->tembusan : (json_decode($document->tembusan ?? '[]', true) ?? []);
                            @endphp
                            @if (!empty($tembusanList))
                                @foreach ($tembusanList as $tembusanItem)
                                    <div class="input-group mb-2 tembusan-row">
                                        <input type="text" name="tembusan[]" class="form-control" value="{{ $tembusanItem }}">
                                        <button type="button" class="btn btn-outline-danger btn-remove-tembusan"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 tembusan-row">
                                    <input type="text" name="tembusan[]" class="form-control" value="1. Kapolres {{ $accident->polres->name ?? '' }} (sebagai laporan)">
                                    <button type="button" class="btn btn-outline-danger btn-remove-tembusan"><i class="bi bi-trash"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="btn_add_tembusan" class="btn btn-sm btn-outline-primary mb-3">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Tembusan
                        </button>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $document->catatan) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT BUTTONS -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-secondary px-4">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan Undangan Gelar Perkara
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            function updateSignatoryPreview() {
                var selected = $('#signatory_select').find(':selected');
                if (selected.val()) {
                    $('#prev_position').text(selected.data('position') || '-');
                    $('#prev_name').text(selected.data('name') || '-');
                    $('#prev_rank_nrp').text((selected.data('rank') || '-') + ' / NRP. ' + (selected.data('nrp') || '-'));
                }
            }

            $('#signatory_select').on('change', updateSignatoryPreview);

            $('#btn_add_tembusan').click(function() {
                var count = $('#tembusan_wrapper .tembusan-row').length + 1;
                var html = '<div class="input-group mb-2 tembusan-row">' +
                    '<input type="text" name="tembusan[]" class="form-control" placeholder="' + count + '. ...">' +
                    '<button type="button" class="btn btn-outline-danger btn-remove-tembusan"><i class="bi bi-trash"></i></button>' +
                    '</div>';
                $('#tembusan_wrapper').append(html);
            });

            $(document).on('click', '.btn-remove-tembusan', function() {
                if ($('#tembusan_wrapper .tembusan-row').length > 1) {
                    $(this).closest('.tembusan-row').remove();
                } else {
                    $(this).closest('.tembusan-row').find('input').val('');
                }
            });
        });
    </script>
@endpush
