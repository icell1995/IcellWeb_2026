@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="loaderbg" style="display:none"></div>

<div id="loader" class="loader" style="display:none"></div>

<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="back-button">
        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i class="bi bi-arrow-left"></i> Kembali ke Produktivitas</a>
    </div>
    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Pemberitahuan Dimulainya Penyidikan</h5>
        </div>
        <form action="{{ route('spdp.store') }}" method="GET" enctype="multipart/form-data">
            @csrf
            @method('GET')
            <input type="text" name="accident_id_spdp" id="accident_id_spdp" value="{{ $accidentId }}" hidden>
            <input type="text" name="id_spdik" id="id_spdik" value="{{ $sprindik->id }}" hidden>
            <input type="text" name="id_springas" id="id_springas" value="{{ $springas->id }}" hidden>
            <input type="text" name="Created_By" id="Created_By" value="{{ $Created_By }}" hidden>
            {{-- <input type="text" name="accident_id" value="{{ $accidentId }}" hidden> --}}
                <div class="card-body">
                    <fieldset class="border p-2 mt-4">
                        <div class="alert alert-danger text-center" role="alert">
                            <b>PERHATIAN!!!</b> <br>
                            <b>DATA INI WAJIB DIISI DENGAN DETAIL DAN LENGKAP KARENA AKAN DIPERTUKARKAN DENGAN APARAT PENEGAK HUKUM LAINNYA DALAM KERANGKA SISTEM PENANGANAN PERKARA TERPADU BERBASIS TEKNOLOGI INFORMASI (SPPT-TI).</b>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Nomor SPDP</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_spdp" type="text" class="form-control @error('no_spdp') is-invalid @enderror font-weight-bold" name="no_spdp" value="{{ old('no_spdp') }}" required autocomplete="no_spdp" autofocus placeholder="Masukkan Nomor SPDP">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Nomor LP</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_lp" type="text"
                                            class="form-control @error('no_lp') is-invalid @enderror font-weight-bold" name="no_lp"
                                            value="{{$no_lp}}" required autocomplete="no_lp" autofocus placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Klasifikasi</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select class="form-control" name="klasifikasi" id="klasifikasi">
                                            <option value="Mudah">Mudah</option>
                                            <option value="Biasa" selected>Biasa</option>
                                            <option value="Sulit">Sulit</option>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Nomor SP Penyidikan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_sprindik" type="text"
                                            class="form-control @error('no_sprindik') is-invalid @enderror font-weight-bold" name="no_sprindik"
                                            value="{{$sprindik->letter_number}}" required autocomplete="no_sprindik" autofocus placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Tanggal SP Penyidikan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input class="form-control" type="text" id="sprindik_date" name="sprindik_date" value="{{Carbon\Carbon::parse($sprindik->issued_date)->format('d-m-Y')}}" placeholder="DD/MM/YYYY" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Nomor SP Tugas Penyidikan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_sprindik" type="text"
                                            class="form-control @error('no_sprindik') is-invalid @enderror font-weight-bold" name="no_sprindik"
                                            value="{{$springas->no_surat}}" required autocomplete="no_sprindik" autofocus placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="fw-bold">Tanggal Ditandatangani Dokumen SPDP</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input class="form-control datepicker" id="spdp_date" name="spdp_date"
                                            placeholder="DD/MM/YYYY" autocomplete="off" value="{{old('spdp_date')}}" data-provide="datepicker">

                                        @error('spdp_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="fw-bold">Apakah Ada Tersangka?</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input type="radio" name="tersangka" value="ada" onclick="toggleInput(true)" style="margin-left: 2em"> Ada Tersangka<br>
                                        <input type="radio" name="tersangka" value="tidak_ada" onclick="toggleInput(false)" style="margin-left: 2em"> Tidak Ada Tersangka<br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <div id="suspects" class="mb-3" style="display: none">
                                        <label class="">Nama Tersangka</label>
                                        <div class="input-group" style="padding: 0px">
                                            <input id="suspect_name" type="text" class="form-control @error('suspect_name') is-invalid @enderror" name="suspect_name" value="{{ $suspects->name }}" required autocomplete="suspect_name" autofocus placeholder="Masukkan Nama Tersangka" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div id="nonsuspects" class="mb-3" style="display: none">
                                    <label class="">Pelapor</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_sprindik" type="text"
                                            class="form-control @error('no_sprindik') is-invalid @enderror font-weight-bold" name="no_sprindik"
                                            value="" required autocomplete="no_sprindik" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div id="nonsuspects1" class="mb-3" style="display: none">
                                    <label class="">Terlapor</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_sprindik" type="text"
                                            class="form-control @error('no_sprindik') is-invalid @enderror font-weight-bold" name="no_sprindik"
                                            value="" required autocomplete="no_sprindik" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Kategori SPDP</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select class="form-control" name="category_spdp" id="category_spdp">
                                            <option value="" selected>Pilih Kategori SPDP</option>
                                            <option value="SPDP BARU" selected>SPDP BARU</option>
                                            <option value="Direferensikan Dari SPDP Sebelumnya">Direferensikan Dari SPDP Sebelumnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Nama Penerima</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="endorsee_name" id="endorsee_name" class="form-control @error('endorsee_name') is-invalid @enderror" type="">
                                        <option value="" selected>Pilih Nama Penerima Kejaksaan</option>
                                        @foreach ($kejaksaan as $kj)
                                        <option value="<?php echo htmlspecialchars($kj->id); ?>"> <?php echo htmlspecialchars($kj->name); ?> </option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Pengadilan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="pengadilan" id="pengadilan" class="form-control @error('pengadilan') is-invalid @enderror" type="">
                                            <option value="" selected>Pilih Nama Pengadilan Sebagai Tembusan</option>
                                            @foreach ($pengadilan as $pg)
                                            <option value="<?php echo htmlspecialchars($pg->id); ?>"> <?php echo htmlspecialchars($pg->name); ?> </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Lampiran</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="lampiran" type="text" class="form-control @error('lampiran') is-invalid @enderror" name="lampiran" value="{{ old('lampiran') }}" required autocomplete="lampiran" autofocus placeholder="Lampiran">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Penandatangan Surat</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="latter_signature" id="latter_signature" class="form-control @error('latter_signature') is-invalid @enderror">
                                            <option value="" selected>Pilih Nama Penandatangan Surat</option>
                                            @foreach ($pejabat as $officers)
                                                <option value="{{ $officers->id }}">{{$officers->register_number . ' - ' . $officers->first_title . ' ' . $officers->first_name . ' ' . $officers->last_name . ', ' . $officers->last_title . ' | ' . $officers->position_id}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Tembusan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="tembusan" type="text" class="form-control @error('tembusan') is-invalid @enderror" name="tembusan" value="{{ old('tembusan') }}" required autocomplete="tembusan" autofocus placeholder="Masukkan Tembusan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary simpan_tersangka" id="simpan-tersangka">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                        {{ __('Batal') }}
                    </a>
                </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script type="text/javascript">

        $(document).ready(function() {
            setInterval(function() {
                $('.alert').toggleClass('alert-danger alert-warning');
            }, 1000);
        });

        $('#spdp_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom',
        });

        function toggleInput(show) {
            var suspects = document.getElementById("suspects");
            var nonsuspects = document.getElementById("nonsuspects");
            var nonsuspects1 = document.getElementById("nonsuspects1");

            if (show) {
                suspects.style.display = "block";
                nonsuspects.style.display = "none";
                nonsuspects1.style.display = "none";
            } else {
                suspects.style.display = "none";
                nonsuspects.style.display = "block";
                nonsuspects1.style.display = "block";
            }
        }
    </script>
@endpush
