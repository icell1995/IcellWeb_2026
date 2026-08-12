@extends('cms.layouts.app')

@section('_title', 'FAQ & Tutorial')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .cms-faq-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    /* Header Card styling */
    .cms-faq-header-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Table styling */
    .faq-table-card {
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .table-faq {
        margin-bottom: 0;
    }

    .table-faq th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 16px 20px;
        background-color: #1a252f !important;
        border: none;
    }

    .table-faq td {
        padding: 16px 20px;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #333333;
    }

    .badge-category-cms {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }



    /* Custom Input / Modal Design */
    .modal-content-custom {
        border-radius: 20px !important;
        border: none !important;
        overflow: hidden;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #ced4da;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
</style>
@endpush

@section('content')
<div class="box mx-2 py-4 cms-faq-container">
    <div class="container-fluid">
        
        {{-- Header Card --}}
        <div class="card cms-faq-header-card border-0 p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-7 mb-3 mb-md-0">
                    <h3 class="fw-bold mb-1 text-blue-dark">Manajemen FAQ & Tutorial</h3>
                    <p class="text-muted mb-0">Kelola daftar Tanya-Jawab yang tayang di sisi petugas lapangan atau buat otomatis dengan AI.</p>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <button class="btn btn-secondary px-3 py-2 fw-semibold rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAdd">
                            <i class="bi bi-plus-circle me-1"></i> Tambah FAQ Manual
                        </button>
                        <form action="{{ route('cms.faq.generate') }}" method="POST" id="form-ai-generate">
                            @csrf
                            <button type="submit" class="btn btn-primary px-3 py-2 fw-semibold rounded-pill" id="btn-generate-ai">
                                <i class="bi bi-cpu me-1"></i> Hasilkan FAQ dengan AI
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FAQ Table Card --}}
        <div class="card faq-table-card border-0 bg-white">
            <div class="card-header text-white fw-bold py-3" style="background-color:#1a252f;">
                <i class="bi bi-list-task me-1"></i> Daftar Tanya-Jawab
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-faq mb-0">
                        <thead>
                            <tr class="text-white">
                                <th style="width: 5%">No</th>
                                <th style="width: 15%">Kategori</th>
                                <th style="width: 30%">Pertanyaan</th>
                                <th style="width: 40%">Jawaban</th>
                                <th style="width: 5%">Status</th>
                                <th style="width: 5%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $index => $faq)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $faqs->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge badge-category-cms bg-light text-primary border border-primary-subtle">
                                            {{ $faq->kategori }}
                                        </span>
                                    </td>
                                    <td><strong class="text-dark">{{ $faq->pertanyaan }}</strong></td>
                                    <td class="text-muted">{{ Str::limit($faq->jawaban, 150) }}</td>
                                    <td>
                                        @if($faq->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-outline-warning rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $faq->id }}" style="width:32px; height:32px; padding:0;">
                                                <i class="bi bi-pencil-fill fs-6"></i>
                                            </button>
                                            <form action="{{ route('cms.faq.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width:32px; height:32px; padding:0;">
                                                    <i class="bi bi-trash-fill fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="modalEdit{{ $faq->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content modal-content-custom shadow-lg">
                                            <form action="{{ route('cms.faq.update', $faq->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header bg-warning border-0 py-3">
                                                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-1"></i> Edit FAQ</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Kategori</label>
                                                        <input type="text" name="kategori" class="form-control form-control-custom" value="{{ $faq->kategori }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Pertanyaan</label>
                                                        <input type="text" name="pertanyaan" class="form-control form-control-custom" value="{{ $faq->pertanyaan }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Jawaban</label>
                                                        <textarea name="jawaban" rows="6" class="form-control form-control-custom" required>{{ $faq->jawaban }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Status Keaktifan</label>
                                                        <select name="is_active" class="form-select form-select-custom">
                                                            <option value="1" {{ $faq->is_active ? 'selected' : '' }}>Aktif (Tampil ke Petugas)</option>
                                                            <option value="0" {{ !$faq->is_active ? 'selected' : '' }}>Nonaktif (Sembunyikan)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 bg-light py-3">
                                                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary px-4 rounded-pill">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-database-exclamation text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 fw-bold">Belum Ada Data FAQ</h5>
                                        <p class="text-muted">Klik tombol "Hasilkan FAQ dengan AI" atau "Tambah FAQ Manual" untuk memulai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($faqs->hasPages())
                    <div class="d-flex justify-content-end p-3 border-top">
                        {{ $faqs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-custom shadow-lg">
            <form action="{{ route('cms.faq.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-1"></i> Tambah FAQ Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <input type="text" name="kategori" class="form-control form-control-custom" placeholder="Contoh: Login, TTE, Sinkronisasi Data" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pertanyaan</label>
                        <input type="text" name="pertanyaan" class="form-control form-control-custom" placeholder="Tulis pertanyaan..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jawaban</label>
                        <textarea name="jawaban" rows="6" class="form-control form-control-custom" placeholder="Tulis jawaban solusi detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 rounded-pill">Tambah FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    document.getElementById('form-ai-generate').addEventListener('submit', function() {
        const btn = document.getElementById('btn-generate-ai');
        
        // Ubah tombol menjadi spinner loading inline
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses AI...';
        
        // Disable tombol biar gak double click
        btn.disabled = true;
    });
</script>
@endpush
@endsection
