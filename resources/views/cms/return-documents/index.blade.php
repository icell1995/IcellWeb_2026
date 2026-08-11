@extends('cms.layouts.app')

@section('_title', 'Pengembalian Dokumen')

{{-- @section('content')
    <div class="container-fluid card rounded-4 p-3">
        <div class="row">
            <div class="col-12">
                <h3 class="text-primary fw-bold ">Pengembalian Dokumen</h3>
                <form id="search-form" class="mb-4">
                    <div class="input-group input-group-outline">
                        <input type="text" id="no_lp" class="form-control" placeholder="Masukkan No LP..." autofocus>
                        <button class="btn btn-primary bg-gradient-primary" type="submit"
                            id="search-accident">Cari</button>
                    </div>
                </form>

                <div id="results-container" class="row mt-4"></div>
            </div>
        </div>
    </div>
@endsection --}}

@section('content')
    <div class="container-fluid p-0">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 p-3">
                <div>
                    <h3 class="text-primary fw-bold mb-0">Pengembalian Dokumen</h3>
                    <p class="text-muted small mb-0">Kelola proses pengembalian dokumen administrasi penyidikan.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <form id="search-form" class="mb-2">
                            <div class="input-group input-group shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-4">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="no_lp" class="form-control border-0 px-3 fs-6"
                                    placeholder="Cari berdasarkan No. Laporan Polisi (LP)..." autofocus
                                    style="box-shadow: none;">
                                <button class="btn btn-primary bg-gradient px-4 fw-bold" type="submit" id="search-accident">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <div id="results-container" class="row g-4">
                    <div class="col-12 text-center py-5">
                        <div class="opacity-50 mb-2">
                            <i class="bi bi-file-earmark-text shadow-sm p-3 rounded-circle"
                                style="font-size: 2rem; background: #f8f9fa;"></i>
                        </div>
                        <h6 class="text-muted fw-light">Hasil pencarian akan muncul di sini</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.all.min.js" defer></script>
    <script src="{{ asset('js/document-return.js') }}"></script>
@endpush
