@php
    $_title = 'Daftar Penyidik';
@endphp

@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <!-- 1. Konten dalam Card Utama -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">

                <!-- 2. Header: Start dari pojok kiri -->
                <div class="text-start mb-5">
                    <h2 class="fw-bold text-blue-dark mb-1">Panel Data Penyidik</h2>
                    <p class="text-muted mb-0">Sistem Pemantauan Distribusi Personel Kepolisian Republik Indonesia</p>
                    <div class="mt-2" style="width: 60px; height: 4px; background: #0d6efd; border-radius: 2px;"></div>
                </div>

                <!-- 3. Card Total Anggota: Berada di tengah -->
                <div class="row justify-content-center mb-5">
                    <div class="col-12 col-md-8 col-lg-4">
                        <div class="card bg-white border-0 rounded-4 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap">
                                    <!-- Ikon dengan Rasio 1:1 -->
                                    <div class="bg-opacity-10 d-flex align-items-center justify-content-center text-blue-dark rounded-circle me-3 shadow-sm"
                                        style="width: 70px; height: 70px; min-width: 70px;">
                                        <i class="bi bi-people-fill fs-2"></i>
                                    </div>
                                    <div class="text-center text-sm-start">
                                        <h6 class="text-uppercase small fw-bold mb-1 text-muted opacity-75 tracking-wider"
                                            style="letter-spacing: 1px;">Total Personel</h6>
                                        <div
                                            class="d-flex align-items-baseline justify-content-center justify-content-sm-start">
                                            <h3 class="display-6 fw-bold text-blue-dark mb-0">{{ number_format($totalAnggota) }}
                                            </h3>
                                            <span class="ms-2 small text-muted">Anggota</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Search Input: Berada di tengah -->
                <div class="row justify-content-center mb-5">
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-4">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                            <input type="text" id="searchPolda" class="form-control border-0 shadow-none fs-6 py-3"
                                placeholder="Cari unit Polda atau wilayah..." autocomplete="off">
                        </div>
                    </div>
                </div>

                <!-- 5 & 6. List Card Polda: Satu baris satu card -->
                <div class="row g-4" id="polda-cards">
                    @foreach ($poldas as $polda)
                        <div class="col-12 polda-item" data-polda-name="{{ strtolower($polda->name) }}">
                            <div class="card border bg-light-subtle border-0 shadow-sm polda-card h-100 rounded-4"
                                data-polda-id="{{ $polda->id }}"
                                style="cursor: pointer; transition: transform 0.2s;">

                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <!-- Nama Polda -->
                                        <div class="col">
                                            <h5 class="fw-bold text-dark mb-1">POLDA {{ $polda->name }}</h5>
                                            <p class="text-muted small mb-0">Satuan Kerja Tingkat Daerah (Polda & Jajaran
                                                Polres)</p>
                                        </div>

                                        <!-- Jumlah Anggota -->
                                        <div class="col-12 col-md-auto mt-3 mt-md-0 text-md-end">
                                            <div class="d-inline-flex flex-column align-items-center">
                                                <span class="text-muted small mb-1">Jumlah Personel</span>
                                                <div class="badge rounded-pill bg-dark px-4 py-2 fs-6 fw-bold">
                                                    {{ number_format($polda->users_count) }}
                                                    <span class="fw-normal opacity-75 ms-1">Pers</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Indikator Panah -->
                                        <div class="col-auto d-none d-md-block">
                                            <i class="bi bi-chevron-right text-muted opacity-50"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown List Area (Hidden by default) -->
                                <div class="polres-dropdown collapse">
                                    <div class="card-footer bg-light border-0 p-4">
                                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Daftar Polres Jajaran</h6>
                                        <div class="bg-white rounded-3 shadow-sm overflow-hidden">
                                            <ul class="list-group list-group-flush polres-list small">
                                                <!-- Data via JS -->

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- End Template -->
                </div>

                <!-- State: Tidak Ada Hasil -->
                <div id="no-results" class="text-center py-5 d-none">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle display-4 text-muted"></i>
                    </div>
                    <h5 class="text-dark fw-bold">Data Tidak Ditemukan</h5>
                    <p class="text-muted small">Coba gunakan kata kunci pencarian yang berbeda.</p>
                </div>

            </div>

            <!-- Footer -->
            <div class="card-footer bg-light py-3 text-center border-0">
                <small class="text-muted">Terakhir diperbarui: {{ date('d M Y H:i') }}</small>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/anggota.js') }}"></script>
@endpush
