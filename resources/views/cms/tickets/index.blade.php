@extends('cms.layouts.app')

@section('_title', 'Ticketing')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="box mx-2 my-3">

    {{-- Header + Summary --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 text-primary">Helpdesk Ticketing</h3>
            <p class="mb-0 text-muted small">
                Kelola pengaduan dan permintaan bantuan terkait ICELL / layanan lainnya.
            </p>
        </div>
        <div class="text-end">
            <div class="mb-2">
                <span class="badge bg-primary">
                    Total Ticket: {{ $tickets->total() }}
                </span>
                <span class="badge bg-secondary">
                    Halaman: {{ $tickets->currentPage() }} / {{ $tickets->lastPage() }}
                </span>
            </div>
            <a href="{{ route('ticketing.create') }}" class="btn btn-success">
                + Create Ticket
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Legend Status & Kategori --}}
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-2">
                    <h6 class="card-title mb-2 text-muted text-uppercase small">Legend Status</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-secondary">Open</span>
                        <span class="badge bg-warning text-dark">Pending</span>
                        <span class="badge bg-success">Solved</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-2">
                    <h6 class="card-title mb-2 text-muted text-uppercase small">Kategori</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-info">A1 - Networking</span>
                        <span class="badge bg-primary">A2 - Data</span>
                        <span class="badge bg-dark">A3 - Lainnya</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Ticket --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 60px;" class="text-center align-items-center">No</th>
                            <th class="align-items-center">Ticket</th>
                            <th >Polda / Polres</th>
                            <th >Kategori</th>
                            <th >Deskripsi Permasalahan</th>
                            <th >Status</th>
                            <th >Dibuat Oleh</th>
                            <th style="width: 140px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                            @php
                                // relasi assigned user
                                $assigned = $t->assigned;

                                $status = $t->status ?? 'open';
                                $statusClass = match ($status) {
                                    'solved'  => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'open'    => 'bg-secondary',
                                    default   => 'bg-light text-muted',
                                };

                                $kategori = $t->kategori ?? '-';
                                $kategoriClass = match ($kategori) {
                                    'A1' => 'bg-info',
                                    'A2' => 'bg-primary',
                                    'A3' => 'bg-dark',
                                    default => 'bg-secondary',
                                };

                                $ticketCreated = $t->created_at?->format('d-m-Y H:i');
                                $ticketUpdated = $t->updated_at?->format('d-m-Y H:i');

                                // kode & nama polda/polres
                                $poldaCode  = $t->polda_id  ? str_pad((string) $t->polda_id,  2, '0', STR_PAD_LEFT) : null;
                                $polresCode = $t->polres_id ? str_pad((string) $t->polres_id, 4, '0', STR_PAD_LEFT) : null;

                                // nama dari relasi (kalau ada)
                                $poldaNameRel  = optional($t->polda)->name ?? optional($t->polda)->nama;
                                $polresNameRel = optional($t->polres)->name ?? optional($t->polres)->nama;

                                // fallback: kalau index ini dikirim poldas/polress dari controller, bisa dipakai juga
                                $poldaFromList  = isset($poldas)
                                    ? optional($poldas->firstWhere('id', $t->polda_id))->name
                                    : null;

                                $polresFromList = isset($polress)
                                    ? optional($polress->firstWhere('id', $t->polres_id))->name
                                    : null;

                                $poldaName  = $poldaNameRel  ?? $poldaFromList  ?? null;
                                $polresName = $polresNameRel ?? $polresFromList ?? null;
                            @endphp
                            <tr>
                                <td class="text-center text-muted">
                                    {{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $t->ticket_number ?? '-' }}</div>
                                    <div class="small text-muted">
                                        Dibuat: {{ $ticketCreated ?? '-' }} <br>
                                        Update: {{ $ticketUpdated ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="small">
                                        <span class="text-muted">Polda:</span>
                                        {{ $poldaCode ? $poldaCode.' - ' : '' }}{{ $poldaName ?? '-' }}
                                    </div>

                                    <div class="small">
                                        <span class="text-muted">Polres:</span>
                                        {{ $polresCode ? $polresCode.' - ' : '' }}{{ $polresName ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge {{ $kategoriClass }}">
                                        {{ $kategori }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $short = Str::limit((string) $t->deskripsi_permasalahan, 80);
                                    @endphp
                                    <span title="{{ $t->deskripsi_permasalahan }}">
                                        {{ $short ?: '-' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    @if($t->deskripsi_solusi)
                                        <div class="small text-muted mt-1">
                                            <span title="{{ $t->deskripsi_solusi }}">
                                                Solusi: {{ Str::limit($t->deskripsi_solusi, 60) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if($assigned)
                                        <div class="fw-semibold">
                                            {{ $assigned->full_name
                                                ?? trim(($assigned->first_name ?? '') . ' ' . ($assigned->last_name ?? ''))
                                                ?: $t->assigned_to
                                                ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            User ID: {{ $assigned->id }}
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            ID: {{ $t->assigned_to ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-md btn-secondary"
                                        onclick="openStatusModal(
                                            {{ $t->id }},
                                            {{ json_encode($t->status) }},
                                            {{ json_encode($t->assigned_to) }},
                                            {!! json_encode($t->deskripsi_solusi) !!},
                                            {{ json_encode($t->ticket_number) }}  {{-- ticket_number dikirim ke modal --}}
                                        )"
                                    >
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-md btn-danger btn-delete-ticket" data-id="{{ $t->id }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada ticket yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Menampilkan {{ $tickets->firstItem() ?? 0 }}–{{ $tickets->lastItem() ?? 0 }}
                        dari {{ $tickets->total() }} ticket
                    </div>
                    <div>
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === MODAL UBAH STATUS (langsung di file ini) === --}}
    <div class="modal fade" id="ticketStatusModal" tabindex="-1" aria-labelledby="ticketStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="ticketStatusModalLabel">
                        Ubah Status Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="status_form">
                    @csrf
                    <div class="modal-body">
                        {{-- Hidden: ID ticket --}}
                        <input type="hidden" id="modal_ticket_id" name="ticket_id">

                        {{-- Info singkat ticket --}}
                        <div class="alert alert-light border small mb-3">
                            <div class="fs-6"><strong>No Ticket: <span id="modal_ticket_number_label"></span></strong></div>
                        </div>

                        {{-- Deskripsi Permasalahan (read-only) --}}
                        {{-- <div class="mb-3">
                            <label class="form-label fw-semibold">Permasalahan</label>
                            <textarea class="form-control" id="modal_deskripsi_permasalahan" rows="3" readonly></textarea>
                        </div> --}}


                        <div class="row g-3">
                            {{-- Status --}}
                            <div class="col-md-6">
                                <label for="modal_status" class="form-label fw-semibold">Status</label>
                                <select id="modal_status" name="status" class="form-select" required>
                                    <option value="open">Open</option>
                                    <option value="pending">Pending</option>
                                    <option value="solved">Solved</option>
                                </select>
                                <small class="text-muted">
                                    Pilih status terkini untuk ticket ini.
                                </small>
                            </div>

                            {{-- Assigned To (boleh dikosongkan) --}}
                            <div class="col-md-6">
                                <label for="modal_assigned_to" class="form-label fw-semibold">Dikerjakan Oleh</label>
                                <select id="modal_assigned_to" name="assigned_to" class="form-select">
                                    <option value="">-- Tidak diubah / tetap seperti sekarang --</option>
                                    @foreach(($assignedUsers ?? []) as $user)
                                        @php
                                            $name = $user->full_name
                                                ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                                            if (!$name) {
                                                $name = 'User ID '.$user->id;
                                            }
                                        @endphp
                                        <option value="{{ $user->id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Pilih petugas yang meng-handle ticket. Biarkan kosong jika tidak ingin mengubah.
                                </small>
                            </div>

                            {{-- Deskripsi Solusi (muncul hanya jika status = solved) --}}
                            <div class="col-12" id="modal_deskripsi_solusi_wrap" style="display:none;">
                                <label for="modal_deskripsi_solusi" class="form-label fw-semibold">
                                    Deskripsi Solusi <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    class="form-control"
                                    id="modal_deskripsi_solusi"
                                    name="deskripsi_solusi"
                                    rows="3"
                                    placeholder="Tuliskan solusi yang telah dilakukan untuk menyelesaikan ticket ini."
                                ></textarea>
                                <small class="text-muted">
                                    Wajib diisi jika status diubah menjadi <strong>Solved</strong>.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    {{-- === END MODAL === --}}

</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // **WAJIB**: tempel fungsi ke window, biar bisa dipanggil dari onclick=
    window.openStatusModal = function(id, status, assignedTo, deskripsiSolusi, ticketNumber) {
        const modal       = document.getElementById('ticketStatusModal');
        if (!modal) return;

        const idField      = document.getElementById('modal_ticket_id');
        const statusField  = document.getElementById('modal_status');
        const assignField  = document.getElementById('modal_assigned_to');
        const solWrap      = document.getElementById('modal_deskripsi_solusi_wrap');
        const solField     = document.getElementById('modal_deskripsi_solusi');
        const ticketLabel  = document.getElementById('modal_ticket_number_label');

        if (idField)      idField.value      = id;
        if (ticketLabel)  ticketLabel.textContent = ticketNumber || '-';
        if (statusField)  statusField.value  = status || 'open';
        if (assignField)  assignField.value  = assignedTo ?? '';

        if (solField) {
            solField.value = deskripsiSolusi || '';
        }
        if (solWrap) {
            solWrap.style.display = (status === 'solved') ? 'block' : 'none';
        }

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const m = new bootstrap.Modal(modal);
            m.show();
        } else {
            modal.style.display = 'block';
        }
    };

    // Template URL dari named route untuk update status
    const updateUrlTemplate = @json(
        route('ticketing.updateStatus', ['ticket' => '__ID__'])
    );

    // Submit form status via AJAX
    document.getElementById('status_form')?.addEventListener('submit', function(e){
        e.preventDefault();

        const idField     = document.getElementById('modal_ticket_id');
        const statusField = document.getElementById('modal_status');
        const assignField = document.getElementById('modal_assigned_to');
        const solField    = document.getElementById('modal_deskripsi_solusi');

        const id               = idField ? idField.value : null;
        const status           = statusField ? statusField.value : null;
        const assigned_to      = assignField ? assignField.value : null;
        const deskripsi_solusi = solField ? solField.value : null;

        if (!id) {
            alert('Ticket ID tidak ditemukan.');
            return;
        }

        const url = updateUrlTemplate.replace('__ID__', id);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status, assigned_to, deskripsi_solusi })
        })
        .then(r => r.json())
        .then(resp => {
            if (resp && resp.success) {
                location.reload();
            } else {
                alert('Gagal mengubah status ticket.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat mengubah status ticket.');
        });
    });

    // toggle textarea deskripsi solusi saat status diganti di modal
    document.getElementById('modal_status')?.addEventListener('change', function(e){
        const wrap = document.getElementById('modal_deskripsi_solusi_wrap');
        if (!wrap) return;
        wrap.style.display = (e.target.value === 'solved') ? 'block' : 'none';
    });

    // Handle tombol DELETE pakai SweetAlert
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-delete-ticket').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                if (!id) return;

                Swal.fire({
                    title: 'Hapus Tiket?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    const url = @json(route('ticketing.destroy', ['ticket' => '__ID__']))
                        .replace('__ID__', id);

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(resp => {
                        if (resp && resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: resp.message || 'Ticket berhasil dihapus.'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: resp.message || 'Gagal menghapus ticket.'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghapus ticket.'
                        });
                    });
                });
            });
        });
    });
</script>
@endpush
