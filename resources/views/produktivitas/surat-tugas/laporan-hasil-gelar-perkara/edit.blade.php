@extends('layouts.app')

@section('content')
    <div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
        <div class="back-button">
            <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i
                    class="bi bi-arrow-left"></i> Kembali ke Produktivitas</a>
        </div>

        <div class="box">
            <div class="box-header">
                <h5 class="fw-bold text-blue-dark">Tambah Laporan Hasil Gelar Perkara (Penetapan Tersangka)</h5>
            </div>

            <form action="{{ route('lhgp.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="accident_id_lhgp" id="accident_id_lhgp" value="{{ $accidentId }}" hidden>
                @foreach ($lhgp as $lhgp)
                    <input type="text" value="{{ $lhgp->id }}" name="lhgp_id" hidden>

                    <fieldset class="border p-2 mt-4">
                        <legend class="w-auto text-legend font-weight-bold m-0" style="font-size: 1.2rem">Laporan Hasil
                            Gelar
                            Perkara</legend>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Nomor LP :</span>
                                <input type="text" class="form-control" id="no_lp" name="no_lp"
                                    value="{{ $lhgp->no_lp }}" readonly>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 com-12 form-group">
                                <span class="font-weight-bold">Nomor SPRINDIK :</span>
                                <input type="text" class="form-control" id="nomor_sprindik" name="nomor_sprindik"
                                    value="{{ $sprindik->letter_number }}" readonly>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 com-12 form-group">
                                <span class="font-weight-bold">Jenis LHGP :</span>
                                <select type="text" class="form-control custom-select" id="jenis_lhgp" name="jenis_lhgp"
                                    @if ($sprindik == null) disabled @endif>
                                    <option value="">Pilih Jenis LHGP</option>
                                    <option value="Biasa" {{ $lhgp->jenis_lhgp == 'Biasa' ? 'selected' : '' }}>Biasa
                                    </option>
                                    <option value="Khusus" {{ $lhgp->jenis_lhgp == 'Khusus' ? 'selected' : '' }}>Khusus
                                    </option>
                                    @error('jenis_lhgp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 com-12 form-group">
                                <span class="font-weight-bold">Jenis Gelar Perkara :</span>
                                <select type="text" class="form-control custom-select" id="jenis_gelar_perkara"
                                    name="jenis_gelar_perkara">
                                    <option value="">Pilih Permasalahan</option>
                                    @foreach ($jenis_gp as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            @if ($jenis->id == $lhgp->jenis_gelar_perkara) selected @endif>
                                            {{ $jenis->nama_permasalahan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border p-2 mt-2">
                        <legend class="w-auto text-legend font-weight-bold m-0" style="font-size: 1.2rem">Pelaksanaan
                        </legend>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Ref Surat Undangan :</span>
                                <input id="surat_undangan" class="form-control" type="text" name="surat_undangan"
                                    value="{{ $lhgp->surat_undangan }}" required autocomplete="surat_undangan" autofocus
                                    placeholder="Maks. 100 Karakter">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Tanggal Pelaksanaan :</span>
                                <input id="tanggal_pelaksanaan" type="text"
                                    class="form-control datepicker @error('tanggal_pelaksanaan') is-invalid @enderror font-weight-bold"
                                    name="tanggal_pelaksanaan" required placeholder="Tanggal Pelaksanaan"
                                    value="{{ Carbon\Carbon::parse($lhgp->tanggal_pelaksanaan)->format('d-m-Y') }}"
                                    data-provide="datepicker">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Waktu Pelaksanaan :</span>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="time" class="flatpickr-input form-control text-center"
                                            id="waktu_pelaksanaan" name="waktu_pelaksanaan"
                                            value="{{ Carbon\Carbon::parse($lhgp->waktu_pelaksanaan)->format('H:i:s') }}"
                                            required>
                                    </div>
                                    <div class="col-6">
                                        <select type="text" class="form-control custom-select" id="zona_waktu"
                                            name="zona_waktu">
                                            <option value="">Zona Waktu</option>
                                            <option value="WIB" {{ $lhgp->zona_waktu == 'WIB' ? 'selected' : '' }}>WIB
                                            </option>
                                            <option value="WITA" {{ $lhgp->zona_waktu == 'WITA' ? 'selected' : '' }}>WITA
                                            </option>
                                            <option value="WIT" {{ $lhgp->zona_waktu == 'WIT' ? 'selected' : '' }}>WIT
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Tempat Pelaksanaan :</span>
                                <input id="tempat_pelaksanaan" class="form-control" type="text" name="tempat_pelaksanaan"
                                    value="{{ $lhgp->tempat_pelaksanaan }}" required autocomplete="tempat_pelaksanaan"
                                    autofocus placeholder="Maks. 100 Karakter">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Pimpinan Gelar Perkara :</span>
                                <input id="pimpinan_gelar" class="form-control" type="text" name="pimpinan_gelar"
                                    value="{{ $lhgp->pimpinan_gelar_perkara }}" required autocomplete="pimpinan_gelar"
                                    autofocus placeholder="*contoh: AKP nama_test (Kasat Lantas Polres....)">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Pemapar :</span>
                                <input id="pemapar" class="form-control" type="text" name="pemapar"
                                    value="{{ $lhgp->pemapar }}" required autocomplete="pemapar" autofocus
                                    placeholder="*contoh: AKP nama_test">
                            </div>

                        </div>
                    </fieldset>

                    <fieldset class="border p-2 mt-2" id="rekomendasi_tersangka">
                        <legend class="w-auto text-legend font-weight-bold m-0" style="font-size: 1.2rem">Rekomendasi Data
                            Tersangka</legend>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <span class="font-weight-bold">Sumber Data Tersangka :</span>
                                <select type="text" class="form-control custom-select" id="sumber_tersangka"
                                    name="sumber_tersangka">
                                    <option value="">Pilih Terlibat</option>
                                    <option value="">Tersangka adalah terlapor dalam LP</option>
                                    <option value="">Tersangka adalah saksi dalam LP</option>
                                    <option value="">Tersangka Baru</option>
                                </select>
                            </div>

                            {{-- <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                            <span class="font-weight-bold">Daftar Tersangka :</span>
                            <select class="custom-select" name="nama_tersangka" id="nama_tersangka">
                                <option value=""></option>
                            </select>
                        </div> --}}

                            <div class="col-12">
                                <button type="button" class="btn btn-primary" id="tambah-tersangka" data-bs-toggle="modal"
                                    data-bs-target="#modalTersangka">Tambah Tersangka</button>
                            </div>

                            <div class="table-responsive col-12 mt-3">
                                <label for="">Tersangka yang Direkomendasikan untuk Ditetapkan Status
                                    Tersangkanya</label>
                                <table class="table table-bordered table-tersangka">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Jenis / Nomor Identitas</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Tempat / Tanggal Lahir</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </fieldset>

                    <fieldset class="border p-2 mt-2">
                        <legend class="w-auto text-legend font-weight-bold m-0" style="font-size: 1.2rem">Pembahasan
                        </legend>
                        <div class="col-12 form-group">
                            <span class="font-weight-bold">Pembahasan :</span>
                            <textarea class="form-control" name="pembahasan" id="pembahasan" style="height: auto;">{{ $lhgp->Pembahasan }}</textarea>
                        </div>
                        <div class="col-12 form-group">
                            <span class="font-weight-bold">Kesimpulan :</span>
                            <textarea class="form-control" name="kesimpulan" id="kesimpulan" style="height: auto;">{{ $lhgp->Kesimpulan }}</textarea>
                        </div>
                        <div class="col-12 form-group">
                            <span class="font-weight-bold">Penutup :</span>
                            <textarea class="form-control" name="penutup" id="penutup" style="height: auto;">{{ $lhgp->Penutup }}</textarea>
                        </div>
                    </fieldset>

                    <fieldset class="border p-2 mt-2">
                        <legend class="w-auto text-legend font-weight-bold m-0" style="font-size: 1.2rem">Penandatangan
                            Surat
                        </legend>
                        <div class="col-12">
                            <span class="font-weight-bold">Penandatangan Surat</span>
                            <select id="pejabat_penandatangan" class="form-control" type="text"
                                name="pejabat_penandatangan" value="{{ old('pejabat_penandatangan') }}" required
                                autocomplete="pejabat_penandatangan" autofocus placeholder="Pejabat Penandatangan">
                                <option value="">Pilih Pejabat Penandatangan</option>
                                @foreach ($penandatangan_surat as $officers)
                                    <option value="{{ $officers->id }}" @if ($lhgp->pejabat_penandatangan == $officers->id) selected @endif>
                                        {{ $officers->register_number }} || {{ $officers->full_name }} ||
                                        {{ $officers->position_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                @endforeach

                <div class="col-12 mt-3 text-center">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"
                        class="btn btn-danger">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
    @include('produktivitas.surat-tugas.laporan-hasil-gelar-perkara.modal.modal-tambah-tersangka')

    <div id="modalTersangka" name="modalTersangka" class="modal fade" role="dialog"tabindex="-1" aria-hidden="true"
        data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Tersangka</h5>

                </div>
                <div class="modal-body">
                    <form id="formTambahTersangka">
                        @csrf
                        <div class="row">
                            <div class="col-12 form-group">
                                <label for="status_identitas" class="form-label">Status Identitas</label>
                                <div class="d-flex">
                                    <div class="col-6 m-2 form-check">
                                        <input class="form-check-input identification_1" type="radio"
                                            name="identification" id="identification_1" value="identification_1">
                                        <label class="form-check-label" for="identification_1">
                                            Hanya Diketahui Nama
                                        </label>
                                    </div>
                                    <div class="col-6 m-2 form-check">
                                        <input class="form-check-input identification_2" type="radio"
                                            name="identification" id="identification_2" value="identification_2">
                                        <label class="form-check-label" for="identification_2">
                                            Diketahui Nama dan Identitas
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="identity_type" class="col-form-label">Jenis
                                    Identitas</label>
                                <select class="form-control custom-select disabled-inputs" name="identity_type"
                                    id="identity_type">
                                    <option selected>Jenis Identitas</option>
                                    @foreach ($identity_type as $identitas)
                                        <option value="{{ $identitas->id }}">
                                            {{ $identitas->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="text" name="accident_id" id="accident_id" value="{{ $accidentId }}"
                                hidden>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="identity_number" class="col-form-label">Nomor Identitas</label>
                                <input type="text" class="form-control disabled-inputs" id="identity_number"
                                    name="identity_number">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="name" class="col-form-label">Nama</label>
                                <input class="form-control disabled-inputs" id="name" name="name">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="jenis_kelamin" class="col-form-label ">Jenis Kelamin</label>
                                <div class="d-flex">
                                    @foreach ($genders as $gender)
                                        <div class="col-6 m-2 form-check">
                                            <input class="form-check-input disabled-inputs" type="radio" name="gender"
                                                value="{{ $gender->id }}" id="gender_{{ $gender->id }}">
                                            <label class="form-check-label" for="gender_{{ $gender->id }}">
                                                {{ $gender->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="birth_place" class="col-form-label">Tempat Lahir</label>
                                <input class="form-control disabled-inputs" id="birth_place" name="birth_place">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="birth_date" class="col-form-label">Tanggal Lahir :</label>
                                <input id="birth_date" type="text"
                                    class="form-control disabled-inputs datepicker @error('birth_date') is-invalid @enderror font-weight-bold"
                                    name="birth_date" required placeholder="Tanggal Lahir"
                                    value="{{ old('birth_date') }}" data-provide="datepicker">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="mother_name" class="col-form-label ">Ibu Kandung</label>
                                <input class="form-control disabled-inputs" id="mother_name" name="mother_name">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="father_name" class="col-form-label ">Ayah Kandung</label>
                                <input class="form-control disabled-inputs" id="father_name" name="father_name">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="ethnicity" class="col-form-label">Suku</label>
                                <select class="form-control custom-select disabled-inputs" name="ethnicity"
                                    id="ethnicity">
                                    <option>Suku</option>
                                    @foreach ($ethnicity as $ethnicitys)
                                        <option value="{{ $ethnicitys->id }}">
                                            {{ $ethnicitys->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="occupation" class="col-form-label">Pekerjaan</label>
                                <select class="form-control custom-select disabled-inputs" name="occupation"
                                    id="occupation">
                                    <option selected>Pekerjaan</option>
                                    @foreach ($job as $jobs)
                                        <option value="{{ $jobs->id }}">
                                            {{ $jobs->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="religion" class="col-form-label">Agama</label>
                                <select class="form-control custom-select disabled-inputs" name="religion"
                                    id="religion">
                                    <option selected>Agama</option>
                                    @foreach ($religion as $religions)
                                        <option value="{{ $religions->id }}">
                                            {{ $religions->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="education" class="col-form-label">Pendidikan</label>
                                <select class="form-control custom-select disabled-inputs" name="education"
                                    id="education">
                                    <option value="">Pilih Pendidikan</option>
                                    @foreach ($edu as $education)
                                        <option value="{{ $education->id }}">
                                            {{ $education->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="country" class="col-form-label">Negara</label>
                                <select class="form-control custom-select disabled-inputs" name="country" id="country">
                                    <option selected>Negara</option>
                                    @foreach ($country as $countrys)
                                        <option value="{{ $countrys->id }}">
                                            {{ $countrys->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="status-kawin" class="col-form-label">Status Kawin</label>
                                <div class="d-flex">
                                    @foreach ($status_kawin as $status_kawins)
                                        <div class="col-6 m-2 form-check">
                                            <input class="form-check-input disabled-inputs" type="radio"
                                                name="marital_status" value="{{ $status_kawins->id }}"
                                                id="marital_status_{{ $status_kawins->id }}">
                                            <label class="form-check-label"
                                                for="marital_status_{{ $status_kawins->id }}">
                                                {{ $status_kawins->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="phone_number" class="col-form-label">Nomor Telepon</label>
                                <input class="form-control disabled-inputs" id="phone_number" name="phone_number">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="email_address" class="col-form-label">Alamat Email</label>
                                <input class="form-control disabled-inputs" id="email_address" name="email_address">
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="province" class="col-form-label">Provinsi</label>
                                <select class="form-control custom-select disabled-inputs" name="province"
                                    id="province">
                                    <option selected>Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="regency" class="col-form-label">Kota</label>
                                <select class="form-control custom-select disabled-inputs" name="regency" id="regency">
                                    <option selected>Kota</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="district" class="col-form-label">Kecamatan</label>
                                <select class="form-control custom-select disabled-inputs" name="district"
                                    id="district">
                                    <option selected>Kecamatan</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                <label for="village" class="col-form-label">Kelurahan</label>
                                <select class="form-control custom-select disabled-inputs" name="village" id="village">
                                    <option selected>Kelurahan</option>
                                </select>
                            </div>

                            <div class="col-12 form-group">
                                <label for="address" class="col-form-label">Alamat</label>
                                <textarea class="form-control disabled-inputs" name="address" id="address" cols="30" rows="10"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary simpan-tersangka"
                                id="simpan-tersangka">Simpan</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
    <script type="text/javascript">
        $('#tanggal_pelaksanaan').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom',
        });

        // Menginisialisasi flatpickr untuk input dengan ID "waktu_pelaksanaan"
        flatpickr("#waktu_pelaksanaan", {
            enableTime: true, // Mengaktifkan kontrol waktu
            noCalendar: true, // Tidak menampilkan kalender
            dateFormat: "H:i:S", // Format waktu
            time_24hr: true // Menggunakan format 24 jam
        });



        $(document).ready(function() {
            $('#tambah-tersangka').click(function() {
                $('#modalTersangka').modal('show');
            });

            // Mengambil referensi ke elemen "jenis_lhgp" dan "jenis_gelar_perkara" menggunakan document.getElementById()
            const jenisLhgp = document.getElementById("jenis_lhgp");
            const jenisGelarPerkara = document.getElementById("jenis_gelar_perkara");

            // Menonaktifkan elemen "jenis_gelar_perkara" saat halaman dimuat
            // jenisGelarPerkara.disabled = true;

            // Menambahkan event listener ke elemen "jenis_lhgp" menggunakan addEventListener()

            var jenis_gelar_perkara = document.getElementById('jenis_gelar_perkara');
            var rekomendasi_tersangka = document.getElementById('rekomendasi_tersangka');
            // alert(jenisLhgp.value);
            if (jenisLhgp.value == 'Biasa' || jenisLhgp.value == 'Khusus') {
                jenisGelarPerkara.disabled = false;
            } else {
                jenisGelarPerkara.disabled = true;
            }

            jenisLhgp.addEventListener("change", function() {
                // Jika opsi baru dipilih dari elemen "jenis_lhgp", aktifkan elemen "jenis_gelar_perkara"
                if (jenisLhgp.value == 'Biasa' || jenisLhgp.value == 'Khusus') {
                    jenisGelarPerkara.disabled = false;
                } else {
                    jenisGelarPerkara.disabled = true;
                    jenisGelarPerkara.value = '';
                }
            });

            jenis_gelar_perkara.addEventListener('change', function() {
                if (jenis_gelar_perkara.value == '1') {
                    rekomendasi_tersangka.style.display = 'block';
                } else {
                    rekomendasi_tersangka.style.display = 'none';
                }
            });

        });

        $(function() {
            $('#tersangka_id').val('');
            var _token = $("input[name='_token']").val();
            var accident_id = $("#accident_id").val();
            // alert('success');
            var tersangka = $('.table-tersangka').DataTable({
                processing: true,
                serverSide: true,
                // ajax: {"{{ route('get_saksi') }}",
                ajax: {
                    url: "{{ route('read_tersangka') }}",
                    type: 'GET',
                    data: {
                        _token: _token,
                        accident_id: accident_id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: null,
                        name: 'identity',
                        render: function(data, type, row) {
                            return row.identity_type + ' / ' + row.identity_number;
                        },
                        width: '35%'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        width: '25%'
                    },
                    {
                        data: null,
                        name: 'dob/pob',
                        render: function(data, type, row) {
                            return row.birth_place + ' / ' + row.birth_date;
                        },
                        width: '25%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        width: '15%',
                        className: 'text-center'
                    },
                ]
            });

            $(".simpan-tersangka").click(function(e) {
                // alert('success')
                e.preventDefault();
                var _token = $("input[name='_token']").val();
                var tersangka_id = $("#tersangka_id").val();
                var accident_id = $("#accident_id").val();
                var identification = $('input[name="identification"]:checked').val();
                var identity_type = $("#identity_type").val();
                var identity_number = $("#identity_number").val();
                var name = $("#name").val();
                var gender = $('input[name="gender"]:checked').val();
                var birth_place = $("#birth_place").val();
                var birth_date = $("#birth_date").val();
                var mother_name = $("#mother_name").val();
                var father_name = $("#father_name").val();
                var ethnicity = $("#ethnicity").val();
                var occupation = $("#occupation").val();
                var education = $('#education').val();
                var religion = $("#religion").val();
                var country = $("#country").val();
                var marital_status = $("input[name='marital_status']:checked").val();
                var phone_number = $("#phone_number").val();
                var email_address = $("#email_address").val();
                var province = $("#province").val();
                var regency = $("#regency").val();
                var district = $("#district").val();
                var village = $("#village").val();
                var address = $("#address").val();

                $.ajax({
                    url: "{{ route('store_tersangka') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        tersangka_id: tersangka_id,
                        accident_id: accident_id,
                        identification: identification,
                        identity_type: identity_type,
                        identity_number: identity_number,
                        name: name,
                        gender: gender,
                        birth_place: birth_place,
                        birth_date: birth_date,
                        mother_name: mother_name,
                        father_name: father_name,
                        ethnicity: ethnicity,
                        occupation: occupation,
                        education: education,
                        religion: religion,
                        country: country,
                        marital_status: marital_status,
                        phone_number: phone_number,
                        email_address: email_address,
                        province: province,
                        regency: regency,
                        district: district,
                        village: village,
                        address: address
                    },
                    success: function(data) {
                        $('#modalTersangka').modal('hide');
                        alert('Tersangka Berhasil Ditambahkan');
                        $('#formTambahTersangka')[0].reset();
                        $('#tersangka_id').val('');
                        tersangka.draw();
                    }
                });
            });

            $('body').on('click', '.deleteTersangka', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('destroy_tersangka') }}",
                    type: 'POST',
                    data: {
                        _token: _token,
                        id: id
                    },
                    success: function(data) {
                        $('#tersangka_id').val(data.id);
                        tersangka.draw();
                    }
                })
            });
        });
    </script>
@endpush
