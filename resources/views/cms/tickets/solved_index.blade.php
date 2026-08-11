@extends('cms.layouts.app')

@section('_title', $_title ?? 'Ticketing - Solved')

@section('content')
<div class="box mx-2 my-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 text-primary">Helpdesk Ticketing - Solved</h3>
            <p class="mb-0 text-muted small">
                Daftar ticket yang sudah <strong>Solved</strong>. Bisa difilter berdasarkan periode dan di-export ke Excel.
            </p>
        </div>
    </div>

    {{-- Filter tanggal + tombol export --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Periode Mulai</label>
                    <input type="text" id="filter_from" class="form-control form-control-sm" placeholder="dd-mm-yyyy">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Periode Selesai</label>
                    <input type="text" id="filter_to" class="form-control form-control-sm" placeholder="dd-mm-yyyy">
                </div>
                <div class="col-md-3">
                    <button id="btnFilter" class="btn btn-primary btn-sm w-100">
                        Terapkan Filter
                    </button>
                </div>
                <div class="col-md-3">
                    <button id="btnExport" class="btn btn-success btn-sm w-100">
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel DataTables --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tickets-table" class="table table-striped table-bordered table-sm w-100">
                    <thead>
                        <tr class="text-center align-middle">
                            <th>No</th>
                            <th>Ticket</th>
                            <th>Kategori</th>
                            <th>Polda</th>
                            <th>Polres</th>
                            <th>Permasalahan</th>
                            <th>Solusi</th>
                            <th>Tanggal Dibuat</th>
                            <th>Tanggal Diselesaikan</th>
                            <th>Dikerjakan Oleh</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('script')
<script>
    // Inisialisasi datepicker kalau sudah pakai bootstrap-datepicker di project lain
    function initDatepickersSimple() {
        const opts = {
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto',
            container: 'body'
        };
        $('#filter_from, #filter_to').datepicker(opts);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDatepickersSimple();

        const table = $('#tickets-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('ticketing.solved') }}',
                data: function (d) {
                    d.from = $('#filter_from').val();
                    d.to   = $('#filter_to').val();
                }
            },
            order: [[8, 'asc']], // sort by updated_at kolom ke-9 (index 8)
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'kategori_label', name: 'kategori' },
                { data: 'polda_name', name: 'polda.name' },
                { data: 'polres_name', name: 'polres.name' },
                { data: 'deskripsi_permasalahan', name: 'deskripsi_permasalahan' },
                { data: 'deskripsi_solusi', name: 'deskripsi_solusi' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'assigned_name', name: 'assigned_name', orderable: false, searchable: false },
            ]
        });

        // tombol filter: redraw DataTable dengan parameter baru
        $('#btnFilter').on('click', function () {
            table.ajax.reload();
        });

        // tombol export: kirim ke route export dengan query string from/to
        $('#btnExport').on('click', function (e) {
            e.preventDefault();
            const from = $('#filter_from').val() || '';
            const to   = $('#filter_to').val() || '';

            const url = '{{ route('ticketing.solved.export') }}'
                + '?from=' + encodeURIComponent(from)
                + '&to='   + encodeURIComponent(to);

            window.location.href = url;
        });
    });
</script>
@endpush
