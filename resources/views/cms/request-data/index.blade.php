@extends('cms.layouts.app')

@section('_title', 'Request Data')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker3.min.css"/>
<style>
    /* Datepicker harus muncul di atas segalanya */
    .datepicker { z-index: 9999 !important; }

    #modalRequest,
    #modalRequest .modal-dialog,
    #modalRequest .modal-content,
    #modalRequest .modal-body { overflow: visible !important; }
</style>
@endpush

@section('content')
<div class="box mx-2 py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Request Data</h3>
        @if(Auth::user()->hasPermission('cms.C'))
        <button class="btn btn-danger" id="btnTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Permintaan Data
        </button>
        @endif
    </div>

    {{-- Alert flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== FILTER PANEL ===== --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header text-white fw-semibold" style="background-color:#0097a7;">
            Daftar Permintaan Data
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Jenis Institusi --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Korlantas / Polda / Instansi Lain</label>
                    <select id="filter_jenis" class="form-select">
                        <option value="">Pilih Instansi Pemohon</option>
                        <option value="korlantas">KORLANTAS</option>
                        <option value="polda">POLDA</option>
                        <option value="polres">POLRES</option>
                        <option value="lainnya">Instansi Lain</option>
                    </select>
                </div>

                {{-- Sub-filter: Nama Polda (muncul saat pilih polda atau polres) --}}
                <div class="col-md-6" id="filter_wrap_polda" style="display:none;">
                    <label class="form-label fw-semibold">Nama Polda</label>
                    <select id="filter_polda_id" class="form-select">
                        <option value="">-- Pilih Polda --</option>
                        @foreach($poldas as $p)
                            <option value="{{ $p->id }}">{{ $p->name ?? $p->id }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub-filter: Nama Polres (muncul saat pilih polres) --}}
                <div class="col-md-6" id="filter_wrap_polres" style="display:none;">
                    <label class="form-label fw-semibold">Nama Polres</label>
                    <select id="filter_polres_id" class="form-select">
                        <option value="">-- Pilih Polres --</option>
                    </select>
                </div>

                {{-- Sub-filter: Instansi Lain (muncul saat pilih lainnya) --}}
                <div class="col-md-6" id="filter_wrap_instansi_lain" style="display:none;">
                    <label class="form-label fw-semibold">Nama Instansi</label>
                    <input type="text" id="filter_instansi_lain" class="form-control"
                           placeholder="Cari nama instansi...">
                </div>

                {{-- Tanggal --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <div class="input-group">
                        <input type="text" id="filter_dari" class="form-control datepicker-input"
                               placeholder="dd/mm/yyyy" autocomplete="off" readonly>
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hingga Tanggal</label>
                    <div class="input-group">
                        <input type="text" id="filter_hingga" class="form-control datepicker-input"
                               placeholder="dd/mm/yyyy" autocomplete="off" readonly>
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary" id="btnCari">
                        <i class="bi bi-search me-1"></i> Cari Data
                    </button>
                    <button class="btn btn-outline-secondary" id="btnReset">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ===== TABLE ===== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-2">
                <a id="btnExport" href="{{ route('request-data.exportExcel') }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                </a>
            </div>
            <div class="table-responsive">
                <table id="tblRequestData" class="table table-bordered table-hover w-100 small">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pemohon</th>
                            <th>Instansi / Perguruan Tinggi / Lainnya</th>
                            <th>Tanggal Permintaan Data</th>
                            <th>Tanggal Data Disediakan</th>
                            <th>Penyedia Data</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===================================================
     MODAL FORM (Create & Edit)
     =================================================== --}}
<div class="modal fade" id="modalRequest" tabindex="-1" aria-labelledby="modalRequestLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRequestLabel">Permintaan Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRequest" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="record_id" name="record_id" value="">

                    {{-- ─── CATATAN PERMINTAAN DATA ─── --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Catatan Permintaan Data
                            <small class="text-muted fw-normal">(data apa saja yang diminta)</small>
                        </label>
                        <input type="text" name="catatan_permintaan" id="catatan_permintaan" class="form-control" placeholder="Contoh: Data Selra Tahun 2025, dst.">
                    </div>

                    <hr class="my-3">

                    {{-- ─── DETAIL PEMOHON ─── --}}
                    <h6 class="fw-bold text-uppercase text-secondary mb-3 mt-1">Detail Pemohon</h6>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap_pemohon" id="nama_lengkap_pemohon" class="form-control" required>
                        </div>

                        {{-- No Telp – disembunyikan saat korlantas --}}
                        <div class="col-md-6" id="wrap_telp">
                            <label class="form-label">Nomor Telepon Pemohon</label>
                            <input type="text" name="no_telp_pemohon" id="no_telp_pemohon" class="form-control"
                                   placeholder="82123456789">
                        </div>

                        {{-- Institusi Pemohon --}}
                        <div class="col-12">
                            <label class="form-label">Institusi Pemohon <span class="text-danger">*</span></label>
                            <select name="jenis_institusi" id="jenis_institusi" class="form-select" required>
                                <option value="korlantas">Korlantas</option>
                                <option value="polda">Polda</option>
                                <option value="polres">Polres</option>
                                <option value="lainnya">Instansi Lain</option>
                            </select>
                        </div>

                        {{-- Sub-field: Polda (tampil jika polda atau polres) --}}
                        <div class="col-md-6" id="wrap_polda" style="display:none;">
                            <label class="form-label">Nama Polda</label>
                            <select name="polda_id" id="form_polda_id" class="form-select">
                                <option value="">-- Pilih Polda --</option>
                                @foreach($poldas as $p)
                                    <option value="{{ $p->id }}">{{ $p->name ?? $p->id }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sub-field: Polres (tampil hanya jika polres) --}}
                        <div class="col-md-6" id="wrap_polres" style="display:none;">
                            <label class="form-label">Nama Polres</label>
                            <select name="polres_id" id="form_polres_id" class="form-select">
                                <option value="">-- Pilih Polres --</option>
                            </select>
                        </div>

                        {{-- Sub-field: Instansi Lain (tampil hanya jika lainnya) --}}
                        <div class="col-12" id="wrap_instansi_lain" style="display:none;">
                            <label class="form-label">Nama Instansi</label>
                            <input type="text" name="instansi_lain" id="instansi_lain" class="form-control"
                                   placeholder="Masukkan nama instansi / perguruan tinggi">
                        </div>

                        {{-- Evidence (Bukti Permintaan) --}}
                        <div class="col-12">
                            <label class="form-label">Evidence</label>
                            <input type="file" name="evidence" id="evidence" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">
                                Dukungan untuk file PDF (.pdf), Gambar (.jpg, .jpeg, .png), Word (.doc, .docx) dengan maksimal size 20 MB.
                            </small>
                            <div id="wrap_evidence_existing" class="mt-1" style="display:none;">
                                <small>File saat ini: <a id="link_evidence_existing" href="#" target="_blank" class="text-primary"></a></small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ─── DETAIL PENYEDIA ─── --}}
                    <h6 class="fw-bold text-uppercase text-secondary mb-3">Detail Penyedia</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Permintaan Data <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="tanggal_permintaan" id="tanggal_permintaan"
                                       class="form-control datepicker-input"
                                       placeholder="dd/mm/yyyy" autocomplete="off" readonly required>
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Penyajian Data</label>
                            <div class="input-group">
                                <input type="text" name="tanggal_penyajian" id="tanggal_penyajian"
                                       class="form-control datepicker-input"
                                       placeholder="dd/mm/yyyy" autocomplete="off" readonly>
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            </div>
                        </div>

                        {{-- Nama Penyedia Data (auto-fill dari user login, read-only) --}}
                        <div class="col-12">
                            <label class="form-label">Nama Penyedia Data</label>
                            @php
                                $u = auth()->user();
                                $displayName = $u->full_name
                                    ?? trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''))
                                    ?: ($u->name ?? $u->username ?? '');
                            @endphp
                            <input type="text" class="form-control bg-light" value="{{ strtoupper($displayName) }}" readonly>
                        </div>

                        {{-- File Data --}}
                        <div class="col-12">
                            <label class="form-label">Data</label>
                            <input type="file" name="file_data" id="file_data" class="form-control"
                                   accept=".xlsx,.xls,.ppt,.pptx,.doc,.docx,.pdf">
                            <small class="text-muted">
                                Dukungan untuk file Excel (.xlsx, .xls), PowerPoint (.ppt, .pptx), Word (.doc, .docx), dan lainnya dengan maksimal size 20 MB.
                            </small>
                            <div id="wrap_file_existing" class="mt-1" style="display:none;">
                                <small>File saat ini: <a id="link_file_existing" href="#" target="_blank" class="text-primary"></a></small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSimpan">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script>
(function () {
    'use strict';

    /* ─── Route helpers ─── */
    const ROUTE_INDEX     = '{{ route('request-data.index') }}';
    const ROUTE_STORE     = '{{ route('request-data.store') }}';
    const ROUTE_EXPORT    = '{{ route('request-data.exportExcel') }}';
    const POLRES_API_BASE = '{{ url('cms/request-data/api/polres') }}';
    const CSRF            = '{{ csrf_token() }}';

    /* ─── DataTable ─── */
    let table;

    function initTable() {
        table = $('#tblRequestData').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ROUTE_INDEX,
                data: function (d) {
                    d.jenis_institusi  = $('#filter_jenis').val();
                    d.polda_id         = $('#filter_polda_id').val();
                    d.polres_id        = $('#filter_polres_id').val();
                    d.instansi_lain    = $('#filter_instansi_lain').val();
                    d.dari_tanggal     = $('#filter_dari').val();
                    d.hingga_tanggal   = $('#filter_hingga').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex',      orderable: false, searchable: false, width: '40px' },
                { data: 'nama_pemohon',     orderable: false },
                { data: 'instansi_label',   orderable: false },
                { data: 'tanggal_permintaan' },
                { data: 'tanggal_penyajian', orderable: false },
                { data: 'penyedia_label',   orderable: false },
                { data: 'file_link',        orderable: false },
                { data: 'aksi',             orderable: false, searchable: false },
            ],
            language: {
                processing: 'Memuat data...',
                emptyTable: 'Tidak ada data.',
                info: 'Menampilkan _START_ s/d _END_ dari _TOTAL_ entri',
                infoEmpty: 'Menampilkan 0 entri',
                search: 'Cari Berdasarkan Nama Judul:',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            },
            order   : [[3, 'desc']],
            pageLength: 10,
        });
    }

    /* ─── Filter Panel Dynamics ─── */
    $('#filter_jenis').on('change', function () {
        const val = $(this).val();
        // Sembunyikan semua sub-filter dulu
        $('#filter_wrap_polda, #filter_wrap_polres, #filter_wrap_instansi_lain').hide();
        $('#filter_polda_id').val('');
        $('#filter_polres_id').html('<option value="">-- Pilih Polres --</option>');
        $('#filter_instansi_lain').val('');

        if (val === 'polda') {
            $('#filter_wrap_polda').show();
        } else if (val === 'polres') {
            $('#filter_wrap_polda').show();
            $('#filter_wrap_polres').show();
        } else if (val === 'lainnya') {
            $('#filter_wrap_instansi_lain').show();
        }
        // korlantas & '' = tidak ada sub-filter
    });

    // Ketika Polda di filter berubah, load Polres-nya
    $('#filter_polda_id').on('change', function () {
        if ($('#filter_jenis').val() === 'polres') {
            const poldaId = $(this).val();
            const sel = $('#filter_polres_id');
            sel.html('<option value="">Memuat...</option>');
            if (!poldaId) { sel.html('<option value="">-- Pilih Polres --</option>'); return; }

            fetch(`${POLRES_API_BASE}/${encodeURIComponent(poldaId)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                sel.html('<option value="">-- Pilih Polres --</option>');
                data.forEach(row => sel.append(new Option(row.name || row.id, row.id)));
            })
            .catch(() => sel.html('<option value="">-- Pilih Polres --</option>'));
        }
    });

    /* ─── Filter submit ─── */
    $('#btnCari').on('click', function () {
        buildExportUrl();
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('#filter_jenis').val('').trigger('change');
        $('#filter_dari, #filter_hingga').val('');
        buildExportUrl();
        table.ajax.reload();
    });

    function buildExportUrl() {
        const jenis = $('#filter_jenis').val();
        let params = new URLSearchParams({
            jenis_institusi  : jenis,
            polda_id         : $('#filter_polda_id').val(),
            polres_id        : $('#filter_polres_id').val(),
            instansi_lain    : $('#filter_instansi_lain').val(),
            dari_tanggal     : $('#filter_dari').val(),
            hingga_tanggal   : $('#filter_hingga').val(),
        });
        $('#btnExport').attr('href', ROUTE_EXPORT + '?' + params.toString());
    }

    function handleJenisChange(val, poldaVal, polresVal) {
        $('#wrap_polda, #wrap_polres, #wrap_instansi_lain').hide();
        $('#form_polda_id, #form_polres_id, #instansi_lain').val('');

        // Sembunyikan no_telp hanya saat Korlantas
        if (val === 'korlantas') {
            $('#wrap_telp').hide();
            $('#no_telp_pemohon').val('');
        } else {
            $('#wrap_telp').show();
        }

        if (val === 'polda') {
            $('#wrap_polda').show();
            if (poldaVal) $('#form_polda_id').val(poldaVal);
        } else if (val === 'polres') {
            $('#wrap_polda').show();
            $('#wrap_polres').show();
            if (poldaVal) {
                $('#form_polda_id').val(poldaVal);
                loadPolres(poldaVal, polresVal);
            }
        } else if (val === 'lainnya') {
            $('#wrap_instansi_lain').show();
            if (polresVal) $('#instansi_lain').val(polresVal);
        }
    }

    $('#jenis_institusi').on('change', function () {
        handleJenisChange($(this).val(), '', '');
    });

    $('#form_polda_id').on('change', function () {
        const jenis = $('#jenis_institusi').val();
        if (jenis === 'polres') {
            loadPolres($(this).val(), '');
        }
    });

    function loadPolres(poldaId, selectedId) {
        const sel = $('#form_polres_id');
        sel.html('<option value="">Memuat...</option>');
        if (!poldaId) { sel.html('<option value="">-- Pilih Polres --</option>'); return; }

        fetch(`${POLRES_API_BASE}/${encodeURIComponent(poldaId)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            sel.html('<option value="">-- Pilih Polres --</option>');
            data.forEach(row => {
                const opt = new Option(row.name || row.id, row.id);
                sel.append(opt);
            });
            if (selectedId) sel.val(String(selectedId));
        })
        .catch(() => { sel.html('<option value="">-- Pilih Polres --</option>'); });
    }

    /* ─── Open modal: ADD ─── */
    $('#btnTambah').on('click', function () {
        resetForm();
        $('#modalRequestLabel').text('Permintaan Data');
        $('#record_id').val('');
        new bootstrap.Modal(document.getElementById('modalRequest')).show();
    });

    /* ─── Open modal: EDIT (via DataTable row) ─── */
    $(document).on('click', '.btn-edit-request', function () {
        const id = $(this).data('id');
        fetch(`${ROUTE_INDEX}/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            if (!res.success) { alert('Gagal memuat data.'); return; }
            const d = res.data;
            resetForm();
            $('#record_id').val(d.id);
            $('#modalRequestLabel').text('Ubah Request Data');

            $('#catatan_permintaan').val(d.catatan_permintaan || '');

            $('#nama_lengkap_pemohon').val(d.nama_lengkap_pemohon || '');
            $('#no_telp_pemohon').val(d.no_telp_pemohon || '');

            $('#jenis_institusi').val(d.jenis_institusi);

            // Handle dynamic fields
            const instansiLainVal = d.jenis_institusi === 'lainnya' ? d.instansi_lain : '';
            handleJenisChange(d.jenis_institusi, d.polda_id, d.jenis_institusi === 'polres' ? d.polres_id : instansiLainVal);
            if (d.jenis_institusi === 'lainnya') $('#instansi_lain').val(d.instansi_lain || '');

            // Simpan tanggal sementara sebagai data attribute,
            // akan di-set ke datepicker setelah modal shown (datepicker belum exist di sini)
            $('#tanggal_permintaan').data('pending-date', d.tanggal_permintaan || '');
            $('#tanggal_penyajian').data('pending-date', d.tanggal_penyajian || '');

            // Existing files
            if (d.evidence_path) {
                $('#wrap_evidence_existing').show();
                $('#link_evidence_existing').attr('href', '/storage/' + d.evidence_path)
                    .text(d.evidence_name || 'Lihat File');
            }
            if (d.file_data_path) {
                $('#wrap_file_existing').show();
                $('#link_file_existing').attr('href', '/storage/' + d.file_data_path)
                    .text(d.file_data_name || 'Lihat File');
            }

            new bootstrap.Modal(document.getElementById('modalRequest')).show();
        })
        .catch(() => alert('Terjadi kesalahan. Coba lagi.'));
    });

    /* ─── Delete ─── */
    $(document).on('click', '.btn-delete-request', function () {
        const id  = $(this).data('id');
        const btn = $(this);

        if (!confirm('Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) return;

        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

        fetch(`${ROUTE_INDEX}/${id}`, {
            method : 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : new URLSearchParams({ _method: 'DELETE' }),
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                table.ajax.reload(null, false);
                showToast(res.message || 'Data berhasil dihapus.', 'success');
            } else {
                showToast(res.message || 'Gagal menghapus data.', 'error');
                btn.prop('disabled', false).html('<i class="bi bi-trash3"></i> Hapus');
            }
        })
        .catch(() => {
            showToast('Terjadi kesalahan jaringan.', 'error');
            btn.prop('disabled', false).html('<i class="bi bi-trash3"></i> Hapus');
        });
    });

    /* ─── Simpan (Create / Update) ─── */
    $('#btnSimpan').on('click', function () {
        const id = $('#record_id').val();
        const formData = new FormData(document.getElementById('formRequest'));

        let url, method;
        if (id) {
            url    = `${ROUTE_INDEX}/${id}`;
            method = 'POST'; // Laravel trick untuk PUT via POST
            formData.append('_method', 'POST');
        } else {
            url    = ROUTE_STORE;
            method = 'POST';
        }
        formData.append('_token', CSRF);

        fetch(url, { method, body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalRequest')).hide();
                table.ajax.reload(null, false);
                showToast(res.message, 'success');
            } else {
                const msgs = res.errors
                    ? Object.values(res.errors).flat().join('\n')
                    : (res.message || 'Terjadi kesalahan.');
                alert(msgs);
            }
        })
        .catch(() => alert('Terjadi kesalahan jaringan.'));
    });

    /* ─── Reset form ─── */
    function resetForm() {
        document.getElementById('formRequest').reset();
        // Cukup kosongkan value — jangan panggil datepicker('clearDates')
        // karena jika belum diinit, Bootstrap Datepicker akan auto-init dengan opsi default (salah)
        $('#tanggal_permintaan, #tanggal_penyajian').val('');
        $('#wrap_polda, #wrap_polres, #wrap_instansi_lain').hide();
        // Reset institusi ke default (korlantas) → sembunyikan telp
        $('#jenis_institusi').val('korlantas');
        $('#wrap_telp').hide();
        $('#no_telp_pemohon').val('');
        $('#wrap_evidence_existing, #wrap_file_existing').hide();
        $('#link_evidence_existing, #link_file_existing').attr('href', '#').text('');
        $('#form_polres_id').html('<option value="">-- Pilih Polres --</option>');
    }

    /* ─── Simple toast ─── */
    function showToast(msg, type = 'success') {
        const color = type === 'success' ? '#198754' : '#dc3545';
        const el = document.createElement('div');
        el.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;
            background:${color};color:#fff;padding:12px 20px;border-radius:6px;
            box-shadow:0 3px 10px rgba(0,0,0,.2);font-size:.9rem;`;
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    /* ─── Init ─── */
    $(document).ready(function () {
        initTable();
        buildExportUrl();

        // Init datepicker – filter panel
        $('#filter_dari, #filter_hingga').datepicker({
            format        : 'dd-mm-yyyy',
            autoclose     : true,
            todayHighlight: true,
            endDate       : new Date(),   // maksimal hari ini
            orientation   : 'bottom auto',
        });

        // Init datepicker – modal form
        // SELALU force destroy + init setiap modal terbuka
        // agar opsi (format, endDate, container) selalu ter-update
        $('#modalRequest').on('shown.bs.modal', function () {
            const opts = {
                format        : 'dd-mm-yyyy',
                autoclose     : true,
                todayHighlight: true,
                endDate       : new Date(),
                orientation   : 'bottom auto',
                container     : '#modalRequest .modal-content',
            };

            // Force destroy dulu (apapun state-nya), lalu init ulang
            try { $('#tanggal_permintaan').datepicker('destroy'); } catch(e) {}
            try { $('#tanggal_penyajian').datepicker('destroy');  } catch(e) {}
            $('#tanggal_permintaan').datepicker(opts);
            $('#tanggal_penyajian').datepicker(opts);

            // Terapkan tanggal yang disimpan saat edit (pending-date)
            const tReq = $('#tanggal_permintaan').data('pending-date');
            const tSaj = $('#tanggal_penyajian').data('pending-date');
            if (tReq) { $('#tanggal_permintaan').datepicker('update', tReq); $('#tanggal_permintaan').data('pending-date', ''); }
            if (tSaj) { $('#tanggal_penyajian').datepicker('update', tSaj);  $('#tanggal_penyajian').data('pending-date', '');  }
        });
    });

})();
</script>
@endpush
