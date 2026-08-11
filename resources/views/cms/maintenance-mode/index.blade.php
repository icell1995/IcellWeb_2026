@extends('cms.layouts.app')

@section('_title', 'Maintenance Mode - CMS ICELL')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark">Maintenance Mode</h3>
        </div>
        <div class="boxy-body mt-4">
            {{-- Alert Messages --}}
            <div id="alert-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="fw-bold text-blue-dark mt-1">Status Sistem</h4>
                </div>
                <div class="card-body">
                    @if ($isMaintenanceActive)
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i> Sistem sedang OFFLINE (Maintenance Aktif)
                        </div>
                        
                        <h5 class="fw-bold text-blue-dark mb-4 mt-4">DETAIL MAINTENANCE</h5>
                        
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Dimulai Pada</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" value="{{ isset($maintenanceData['started_at']) ? \Carbon\Carbon::createFromTimestamp($maintenanceData['started_at'])->format('d M Y, H:i:s') : '-' }}" disabled>
                            </div>
                        </div>
                        
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Berakhir Pada</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" value="{{ isset($maintenanceData['end_time']) ? \Carbon\Carbon::createFromTimestamp($maintenanceData['end_time'])->format('d M Y, H:i:s') : '-' }}" disabled>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Durasi</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" value="{{ $maintenanceData['duration_minutes'] ?? '-' }} menit" disabled>
                            </div>
                        </div>
                        
                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Diaktifkan Oleh</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-self-center">
                                <input type="text" class="form-control" value="{{ $maintenanceData['activated_by'] ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label">Bypass URL (Secret)</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 align-self-center">
                                <div class="input-group mb-1">
                                    <input type="text" class="form-control" id="bypass-url-input" value="{{ isset($maintenanceData['secret']) ? url($maintenanceData['secret']) : '-' }}" disabled>
                                    <button class="btn btn-primary" id="btn-copy-url" type="button"><i class="bi bi-clipboard"></i> Salin URL</button>
                                </div>
                                <small class="text-muted">Gunakan URL ini untuk mengakses aplikasi saat maintenance.</small>
                            </div>
                        </div>

                        <div class="input-group row mb-3 ms-0">
                            <label class="fw-bold col-sm-3 col-form-label text-danger">Sisa Waktu</label>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex align-self-center">
                                <div class="d-flex text-danger fw-bold fs-5" id="countdown-display">
                                    <span id="cd-hours">00</span><span class="mx-2">:</span><span id="cd-minutes">00</span><span class="mx-2">:</span><span id="cd-seconds">00</span>
                                </div>
                            </div>
                        </div>

                        <form id="form-deactivate" action="{{ route('cms.maintenance-mode.deactivate') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-success" id="btn-deactivate">
                                <i class="bi bi-power"></i> Akhiri Maintenance & Kembali Online
                            </button>
                        </form>

                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i> Sistem sedang ONLINE (Berjalan Normal)
                        </div>
                        
                        <h5 class="fw-bold text-blue-dark mb-4 mt-4">AKTIFKAN MAINTENANCE MODE</h5>

                        <form id="form-activate" action="{{ route('cms.maintenance-mode.activate') }}" method="POST">
                            @csrf
                            
                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="secret">Kunci Bypass (Secret)</label>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex flex-column justify-content-center">
                                    <input type="text" class="form-control @error('secret') is-invalid @enderror" id="secret" name="secret" placeholder="Contoh: icell-maintenance-2026" value="{{ old('secret') }}" required>
                                    @error('secret')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-1">Kunci ini digunakan sebagai URL bypass untuk mengakses aplikasi saat maintenance. Minimal 6 karakter.</small>
                                </div>
                            </div>

                            <div class="input-group row mb-3 ms-0">
                                <label class="fw-bold col-sm-3 col-form-label" for="duration">Durasi Maintenance (menit)</label>
                                <div class="col-lg-9 col-md-9 col-sm-12 col-12 d-flex flex-column justify-content-center">
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" placeholder="Contoh: 30" value="{{ old('duration', 30) }}" min="1" max="1440" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-quick" data-minutes="15">15 menit</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-quick" data-minutes="30">30 menit</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-quick" data-minutes="60">1 jam</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-quick" data-minutes="120">2 jam</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-quick" data-minutes="360">6 jam</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-quick" data-minutes="720">12 jam</button>
                                    </div>
                                    <small class="text-muted mt-1">Sistem akan otomatis kembali online setelah durasi habis (Maks 24 Jam).</small>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-4">
                                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Perhatian!</strong> Mengaktifkan maintenance mode akan membuat seluruh pengguna (kecuali admin yang memiliki bypass URL) tidak dapat mengakses aplikasi ICELL.
                            </div>

                            <button type="submit" class="btn btn-danger mt-2" id="btn-activate">
                                <i class="bi bi-toggle-on"></i> Aktifkan Maintenance Mode
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- History Log Card --}}
            <div class="card mt-4 mb-4">
                <div class="card-header">
                    <h4 class="fw-bold text-blue-dark mt-1"><i class="bi bi-clock-history me-2"></i> Riwayat Log Maintenance</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Start Maintenance</th>
                                    <th>Aksi</th>
                                    <th>Durasi Set</th>
                                    <th>Bypass URL</th>
                                    <th>IP Address</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td class="align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}</td>
                                        <td class="align-middle">
                                            @if ($log->action == 'activated')
                                                <span class="badge bg-danger rounded-pill px-3">Maintenance ON</span>
                                            @else
                                                <span class="badge bg-success rounded-pill px-3">Maintenance OFF</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ $log->duration_minutes ? $log->duration_minutes . ' menit' : '-' }}</td>
                                        <td class="align-middle">
                                            @if($log->secret)
                                                <small class="text-muted">{{ url($log->secret) }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ $log->ip_address ?? '-' }}</td>
                                        <td class="align-middle fw-bold text-primary">
                                            {{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Otomatis Sistem' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-muted py-4">Belum ada riwayat maintenance apapun.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Quick duration buttons
    $('.btn-quick').on('click', function() {
        $('#duration').val($(this).data('minutes'));
    });

    // Copy bypass URL
    $('#btn-copy-url').on('click', function() {
        var urlInput = document.getElementById('bypass-url-input');
        urlInput.select();
        urlInput.setSelectionRange(0, 99999); /* For mobile devices */
        navigator.clipboard.writeText(urlInput.value).then(function() {
            alert('Bypass URL berhasil disalin!');
        });
    });

    // Activate form submission via AJAX
    $('#form-activate').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#btn-activate');
        var originalHtml = btn.html();

        btn.html('<i class="bi bi-hourglass-split"></i> Mengaktifkan...').prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                window.location.href = '{{ route("cms.maintenance-mode.index") }}';
            },
            error: function(xhr) {
                btn.html(originalHtml).prop('disabled', false);
                var msg = 'Gagal mengaktifkan maintenance mode.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.status === 403) {
                    msg = 'Anda tidak memiliki akses untuk melakukan aksi ini (403).';
                } else if (xhr.status === 419) {
                    msg = 'Sesi telah berakhir (CSRF token expired). Silakan refresh halaman.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert('Error ' + xhr.status + ': ' + msg);
            }
        });
    });

    // Deactivate form submission via AJAX
    $('#form-deactivate').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#btn-deactivate');
        var originalHtml = btn.html();

        btn.html('<i class="bi bi-hourglass-split"></i> Menonaktifkan...').prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                window.location.href = '{{ route("cms.maintenance-mode.index") }}';
            },
            error: function(xhr) {
                btn.html(originalHtml).prop('disabled', false);
                alert('Gagal menonaktifkan maintenance mode. Error: ' + xhr.status);
            }
        });
    });

    @if ($isMaintenanceActive && isset($maintenanceData['end_time']))
    // Countdown timer
    var endTime = {{ $maintenanceData['end_time'] }} * 1000;

    function updateCountdown() {
        var now = Date.now();
        var distance = endTime - now;

        if (distance <= 0) {
            $('#cd-hours').text('00');
            $('#cd-minutes').text('00');
            $('#cd-seconds').text('00');
            setTimeout(function() { location.reload(); }, 2000);
            return;
        }

        var hours   = Math.floor(distance / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $('#cd-hours').text(String(hours).padStart(2, '0'));
        $('#cd-minutes').text(String(minutes).padStart(2, '0'));
        $('#cd-seconds').text(String(seconds).padStart(2, '0'));
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
    @endif
});
</script>
@endpush
