@extends('cms.layouts.app')

@section('_title', 'Create Ticket')

@section('content')
<div class="box mx-2 py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 text-primary">Create Ticket</h3>
            <p class="mb-0 text-muted small">
                Form pengajuan ticket helpdesk terkait Networking, Data, atau kebutuhan lainnya.
            </p>
        </div>
        <div>
            <a href="{{ route('ticketing.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Error message --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Card Form --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('ticketing.store') }}">
                @csrf
                <div class="row g-3">

                    {{-- Kategori --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kategori Ticket</label>
                        <select name="kategori" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="A1" {{ old('kategori') == 'A1' ? 'selected' : '' }}>Networking (A1)</option>
                            <option value="A2" {{ old('kategori') == 'A2' ? 'selected' : '' }}>Data (A2)</option>
                            <option value="A3" {{ old('kategori') == 'A3' ? 'selected' : '' }}>Lainnya (A3)</option>
                        </select>
                        <small class="text-muted">
                            Pilih kategori utama permasalahan yang diajukan.
                        </small>
                    </div>

                    {{-- Polda --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Polda</label>
                        <select id="polda_id" name="polda_id" class="form-select">
                            <option value="">-- Pilih Polda --</option>
                            @foreach($poldas as $p)
                                <option value="{{ $p->id }}" {{ old('polda_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name ?? $p->nama ?? $p->id }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Polda asal permintaan ticket.
                        </small>
                    </div>

                    {{-- Polres --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Polres</label>
                        <select id="polres_id" name="polres_id" class="form-select">
                            <option value="-">- Pilih Polres -</option>
                        </select>
                        <small class="text-muted">
                            Polres terkait (jika ada).
                        </small>
                    </div>

                    {{-- Deskripsi Permasalahan --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi Permasalahan</label>
                        <textarea
                            name="deskripsi_permasalahan"
                            class="form-control"
                            rows="4"
                            placeholder="Jelaskan secara singkat & jelas permasalahan yang terjadi, termasuk context: modul apa, waktu kejadian, dan pesan error jika ada."
                        >{{ old('deskripsi_permasalahan') }}</textarea>
                        <small class="text-muted">
                            Contoh: "Tidak bisa login ICELL untuk user Polres X, muncul error 500 saat klik tombol Login."
                        </small>
                    </div>

                    {{-- Assigned To (otomatis user login) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Assigned To</label>
                        <input
                            type="hidden"
                            id="assigned_select"
                            name="assigned_to"
                            value="{{ old('assigned_to', auth()->id()) }}"
                        >
                        @php
                            $__u = auth()->user();
                            $__displayName = $__u->full_name
                                ?? trim((($__u->first_name ?? '') . ' ' . ($__u->last_name ?? '')));
                            if (empty($__displayName)) {
                                $__displayName = $__u->name ?? '';
                            }
                        @endphp
                        <div class="form-control-plaintext">
                            <span class="badge bg-primary me-1">Default</span>
                            {{ $__displayName }}
                        </div>
                        <small class="text-muted">
                            Ticket ini secara default akan di-assign ke akun Anda.
                        </small>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="col-12 mt-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Create Ticket
                        </button>
                        <a href="{{ route('ticketing.index') }}" class="btn btn-light border">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ====== SCRIPT LANGSUNG DI DALAM CONTENT (SUPAYA PASTI KE-RENDER) ====== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var poldaSelect  = document.getElementById('polda_id');
            var polresSelect = document.getElementById('polres_id');

            if (!poldaSelect || !polresSelect) {
                console.warn('[Ticketing] polda_id / polres_id tidak ditemukan di DOM');
                return;
            }

            function resetPolres() {
                polresSelect.innerHTML = '<option value="-">- Pilih Polres -</option>';
            }

            function loadPolres(poldaId, selectedPolres) {
                console.log('[Ticketing] Load Polres untuk Polda:', poldaId);
                resetPolres();

                if (!poldaId) {
                    return;
                }

                var url = '{{ url('pengguna/polres_list') }}/' + encodeURIComponent(poldaId);
                console.log('[Ticketing] Fetch URL:', url);

                polresSelect.innerHTML = '<option value="">Memuat...</option>';

                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(function (res) {
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        console.log('[Ticketing] Response polres_list:', data);

                        resetPolres();

                        (data || []).forEach(function (row) {
                            var opt = document.createElement('option');
                            opt.value = row.id;
                            opt.textContent = row.name || row.nama || row.id;
                            polresSelect.appendChild(opt);
                        });

                        if (selectedPolres) {
                            polresSelect.value = String(selectedPolres);
                        }
                    })
                    .catch(function (err) {
                        console.error('[Ticketing] Gagal load Polres:', err);
                        resetPolres();
                        alert('Gagal memuat Polres. Coba pilih Polda lagi.');
                    });
            }

            // Event: saat Polda berubah
            poldaSelect.addEventListener('change', function () {
                loadPolres(this.value, null);
            });

            // Restore old polda/polres kalau ada (habis validation error)
            var oldPolda  = @json(old('polda_id'));
            var oldPolres = @json(old('polres_id'));

            if (oldPolda) {
                poldaSelect.value = String(oldPolda);
                loadPolres(oldPolda, oldPolres);
            }
        });
    </script>
</div>
@endsection
