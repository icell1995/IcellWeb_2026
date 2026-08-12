<div id="myModalPenyitaan1" name="myModalPenyitaan1" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Surat Permintaan Izin / Izin Khusus Penyitaan
                    Kepada Ketua
                    Pengadilan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_izin_penyitaan" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan2" name="myModalPenyitaan2" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Surat Permintaan Persetujuan Penyitaan Kepada
                    Ketua
                    Pengadilan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_persetujuan_penyitaan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalPenyitaan3" name="myModalPenyitaan3" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-gradient-primary text-white p-4" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-box-seam me-2"></i> Tambah & Kelola Barang Bukti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-plus-circle me-1"></i> Data Barang Bukti Baru
                        </h6>
                        <form id="barang-bukti-form">
                            @csrf
                            <input type="hidden" name="barang_bukti_id" id="barang_bukti_id">
                            <input type="hidden" name="accident_id_barang_bukti" id="accident_id_barang_bukti" value="{{ $id ?? '' }}">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input id="nama_barang" type="text" class="form-control" name="nama_barang" placeholder="Nama Barang Bukti" required>
                                        <label for="nama_barang">Nama Barang Bukti</label>
                                        <span class="text-danger error-text name_err small"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input id="jumlah_barang" type="text" class="form-control" name="jumlah_barang" placeholder="Jumlah (contoh: 1 unit)" required>
                                        <label for="jumlah_barang">Jumlah / Satuan</label>
                                        <span class="text-danger error-text gender_err small"></span>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-center mb-3">
                                    <button type="submit" class="btn btn-primary h-100 w-100 py-2 btn-barang-bukti shadow-sm" style="border-radius: 10px; transition: all 0.3s ease;">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Barang Bukti</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 barang-bukti-datatable w-100">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">No</th>
                                        <th class="py-3 text-secondary text-uppercase small fw-bold">Nama Barang</th>
                                        <th class="py-3 text-secondary text-uppercase small fw-bold">Jumlah</th>
                                        <th class="pe-4 py-3 text-secondary text-uppercase small fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 p-3">
                <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    #myModalPenyitaan3 .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    #myModalPenyitaan3 .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }
    #myModalPenyitaan3 .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }
</style>


<div id="myModalPenyitaan4" name="myModalPenyitaan4" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan Masukan Petugas Untuk Perintah Penyitaan</h5>
            </div>

            <form action="{{ route('add_surat_penyitaan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_surat_penyitaan" name="accident_id_surat_penyitaan" type="text"
                        value="{{ $id }}" hidden>
                    <button class="btn btn-add btn-success mb-2" type="button" name="add_officer_penyitaan"
                        id="add_officer_penyitaan"><i class="bi bi-plus-circle"></i> Tambah
                        Petugas</button>

                    <div id="add_surat_penyitaan">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark-blue">
                        {{ __('Simpan') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myEditModalPenyitaan4" name="myEditModalPenyitaan4" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Perintah Surat Penyitaan</h5>
            </div>

            <form action="{{ route('edit_surat_penyitaan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_surat_penyitaan" name="accident_id_edit_surat_penyitaan"
                        type="text" value="{{ $id }}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_edit_penyitaan"
                        id="add_officer_edit_penyitaan">Tambah Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="edit_surat_penyitaan" style="display: table">
                        @isset($surat_penyitaan)
                            @foreach ($surat_penyitaan as $penyitaan)
                                <div class="add-row mb-3">
                                    <div class="input-group">
                                        <select id="edit_officer_surat_penyitaan[]" name="edit_officer_surat_penyitaan[]"
                                            class="form-select" aria-describedby="#btn-remove-edit-penyitaan">
                                            <option value="">Pilih Petugas</option>
                                            @isset($officer)
                                                @foreach ($officer as $officers)
                                                    <option value="{{ $officers->id }}"
                                                        {{ $officers->id == $penyitaan->id ? 'selected' : '' }}>
                                                        {{ $officers->id }} - {{ $officers->first_name }}
                                                        {{ $officers->last_name }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        <button type="button" name="remove"
                                            class="btn btn-danger remove btn_remove_edit_penyitaan"><i class="bi bi-x-square"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-simpan">
                        {{ __('Simpan') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan5" name="myModalPenyitaan5" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Silahkan masukan file Berita Acara Penyitaan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="berita_acara_penyitaan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan6" name="myModalPenyitaan6" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Pengiriman Berkas Perkara</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="surat_pengiriman_berkas_perkara" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan7" name="myModalPenyitaan7" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tanda Terima Berkas Pekara</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="tanda_terima_berkas_perkara"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="myModalPenyitaan8" name="myModalPenyitaan8" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Pengiriman Tersangka dan Barang Bukti</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="surat_pengiriman_tersangka_barang_bukti" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan9" name="myModalPenyitaan9" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Berita Acara Serah Terima Tersangka dan Barang Bukti</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="berita_acara_serah_terima_tersangka" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan10" name="myModalPenyitaan10" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Bantuan Penyelidikan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_bantuan_penyelidikan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan11" name="myModalPenyitaan11" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Penitipan / Titip Rawat Barang Bukti</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pentitipan_barang"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan12" name="myModalPenyitaan12" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Perintah Pengembalian Benda Sitaan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pengembalian_sitaan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan13" name="myModalPenyitaan13" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Berita Acara Penitipan / Titip Rawat Barang Bukti</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="berita_penitipan_barang"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan14" name="myModalPenyitaan14" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Berita Acara Pengembalian Benda Sitaan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="berita_pengembalian_sitaan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan15" name="myModalPenyitaan15" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ketetapan Ijin Penyitaan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="ketetapan_ijin_penyitaan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan16" name="myModalPenyitaan16" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ketetapan Persetujuan Penyitaan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text"
                                value="ketetapan_persetujuan_penyitaan" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan17" name="myModalPenyitaan17" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Tanda Penerimaan</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_tanda_penerimaan"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan18" name="myModalPenyitaan18" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Surat Pengantar</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="surat_pengantar" hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan19" name="myModalPenyitaan19" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Berita Acara Penyerahan Berkas Perkara</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="berita_penyerahan_berkas"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan20" name="myModalPenyitaan20" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Laporan Hasil Gelar Perkara</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="laporan_gelar_perkara"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalPenyitaan21" name="myModalPenyitaan21" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Laporan Hasil Gelar Perkara Khusus</h5>
            </div>

            <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <input id="form_id" name="form_id" type="text" value="laporan_perkara_khusus"
                                hidden>
                        </div>
                        <div>
                            <input id="accident_id" name="accident_id" type="text" value="{{ $id }}"
                                hidden>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="upload col-10 me-1">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="col-2 ms-1">
                            <button type="submit" class="btn btn-dark-blue">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">
                        {{ __('Delete') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        {{ __('Batal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
