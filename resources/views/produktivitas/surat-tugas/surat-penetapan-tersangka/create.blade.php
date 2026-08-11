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
            <h5 class="fw-bold text-blue-dark">Tambah Surat Ketetapan Tentang Penetapan Tersangka</h5>
        </div>
        <form action="{{ route('sddl.store') }}" method="GET" enctype="multipart/form-data">
            @csrf
            @method('GET')
            <input type="text" name="accident_id_sddl" id="accident_id_sddl" value="{{ $accidentId }}" hidden>
            <input type="text" name="accident_id_lhgp" id="accident_id_lhgp" value="{{ $lhgpId }}" hidden>
            <input type="text" name="sprindik_id_sddl" id="sprindik_id_sddl" value="{{ $sprindikId }}" hidden>
            {{-- <input type="text" name="accident_id" value="{{ $accidentId }}" hidden> --}}
                <div class="card-body">
                    <h3>Surat Ketetapan Tentang Penetapan Tersangka</h3>
                    <fieldset class="border p-2 mt-4">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Nomor Surat Ketetapan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="letter_number" type="text"
                                            class="form-control @error('letter_number') is-invalid @enderror font-weight-bold" name="letter_number"
                                            value="" required autocomplete="letter_number" autofocus placeholder="Masukkan Nomor Surat Ketatapan Tentang Penetapan Tersangka">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Tanggal Surat Ketetapan</label>
                                    <div class="input-group" style="padding: 0px">
                                        {{-- <input class="form-control datepicker" type="text" id="tgl_penetapan" name="tgl_penetapan"
                                        placeholder="Tanggal Surat Ketetapan" autocomplete="off" required data-provide="datepicker"> --}}
                                        <input id="letter_date_ketetapan" type="text" class="form-control datepicker @error('letter_date_ketetapan') is-invalid @enderror font-weight-bold"
                                        name="letter_date_ketetapan" required placeholder="Tanggal Pelaksanaan"
                                        value="{{ old('letter_date_ketetapan') }}" data-provide="datepicker">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Nomor LP</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_lp" type="text"
                                            class="form-control @error('no_lp') is-invalid @enderror font-weight-bold" name="no_lp"
                                            value="{{$no_lp}}" required autocomplete="no_lp" autofocus placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Nomor Sprindik</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_sprindik" type="text"
                                            class="form-control @error('no_sprindik') is-invalid @enderror font-weight-bold" name="no_sprindik"
                                            value="{{$sprindik->letter_number}}" required autocomplete="no_sprindik" autofocus placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Kejaksaan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="kejaksaan" id="kejaksaan" class="form-control @error('kejaksaan') is-invalid @enderror" type="">
                                            <option value="">--Pilih Nama Kejaksaan</option>
                                            @foreach ($name as $kj)
                                                <option value="{{ $kj->id }}">{{ $kj->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Yang Menandatangani</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="letter_signature" id="letter_signature" class="form-control @error('letter_signature') is-invalid @enderror">
                                            <option value="">--Pilih Yang Menandatangani--</option>
                                            @foreach($authorizedSignatories as $data)
                                                <option value="{{$data->id}}">{{$data->register_number . ' - ' . $data->full_name . ' | ' . $data->position_id}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Sumber Yang Menyebutkan Tersangka</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="sumber" id="sumber" class="form-control">
                                            <option value="">--Sumber Tersangka--</option>
                                            {{-- <option value="1" hidden>Tersangka disebutkan di dalam laporan polisi</option> --}}
                                            <option value="1">Tersangka ditetapkan melalui Gelar Perkara Penetapan Tersangka</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                {{-- <div class="mb-3" id="resume_penetapan_tersangka" style="display: none;">
                                    <label class="">Tanggal Resume Penetapan Tersangka</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="tgl_resume" type="text" class="form-control datepicker @error('tgl_resume') is-invalid @enderror font-weight-bold"
                                        name="tgl_resume" required placeholder="Tanggal Resume Penetapan Tersangka" value="{{ old('tgl_resume') }}" data-provide="datepicker" hidden>
                                    </div>
                                </div>     --}}
                                <div class="mb-3" id="gelar_perkara" style="display: none;">
                                    <label class="">Tanggal Gelar Perkara Penetapan Tersangka</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="tgl_gelar" type="text"
                                            class="form-control @error('tgl_gelar') is-invalid @enderror font-weight-bold" name="tgl_gelar"
                                            value="{{carbon\Carbon::parse()->translatedFormat('d-m-Y') }}" required autocomplete="tgl_gelar" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="mb-3">
                                <label class="">Tersangka yang Ditetapkan</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="tersangka" type="text"
                                        class="form-control @error('tersangka') is-invalid @enderror font-weight-bold" name="tersangka"
                                        value="{{$suspectName}}" required autocomplete="tersangka" autofocus placeholder="" readonly>
                                </div>
                            </div>
                        </div>
                    <fieldset class="border p-2 mt-4">
                </div>



                <div class="card-body">
                    <h3> LENGKAPI DATA TERSANGKA</h3>
                    <fieldset class="border p-2 mt-4">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Jenis Identitas</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="identity_type" id="identity_type" class="form-control">
                                            <option value="{{$id_types}}">--Jenis Identitas--</option>
                                            @foreach ($id as $idtype)
                                                <option value="{{ $idtype->id }}" @if($idtype->id == $id_types) {{'selected'}}@endif >{{ $idtype->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Status Kawin</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="marriage" id="marriage" class="form-control">
                                            <option value="">--Status Pernikahan--</option>
                                            @foreach ($merried as $merrieds)
                                                <option value="{{ $merrieds->id }}" @if($merrieds->id == $mart_status) {{'selected'}}@endif >{{ $merrieds->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Nomor Identitas</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_identitas" type="text"
                                            class="form-control @error('no_identitas') is-invalid @enderror font-weight-bold" name="no_identitas"
                                            value="{{$id_number}}" required autocomplete="no_identitas" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Nomor Telepon</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="no_tlp" type="text"
                                            class="form-control @error('no_tlp') is-invalid @enderror font-weight-bold" name="no_tlp"
                                            value="{{$phone_no}}" required autocomplete="no_tlp" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Nama</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="name_tsk" type="text"
                                            class="form-control @error('name_tsk') is-invalid @enderror font-weight-bold" name="name_tsk"
                                            value="{{$suspectName}}" required autocomplete="name_tsk" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Alamat Email</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="email" type="text"
                                            class="form-control @error('email') is-invalid @enderror font-weight-bold" name="email"
                                            value="{{$email_address}}" required autocomplete="email" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Jenis Kelamin</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                            <option value="">--Pilih Jenis Kelamin--</option>
                                            @foreach ($gender as $genders)
                                                <option value="{{ $genders->id }}" @if($genders->id == $sex) {{'selected'}}@endif >{{ $genders->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Negara</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="country" id="country" class="form-control">
                                            <option value="">--Pilih Negara--</option>
                                            @foreach ($country as $cnt)
                                                <option value="{{ $cnt->id }}" @if($cnt->id == $countrys) {{'selected'}}@endif >{{ $cnt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Tempat Lahir</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="tempat_lahir" type="text"
                                            class="form-control @error('tempat_lahir') is-invalid @enderror font-weight-bold" name="tempat_lahir"
                                            value="{{$birth_place}}" required autocomplete="tempat_lahir" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Provinsi</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="province" id="province" class="form-control">
                                            <option value="">--Pilih Provinsi--</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}" @if($province->id == $prov) {{'selected'}}@endif >{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Tanggal Lahir</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="tgl_lahir" type="text"
                                            class="form-control @error('tgl_lahir') is-invalid @enderror font-weight-bold datepicker" name="tgl_lahir"
                                            value="{{carbon\carbon::parse($birth_date)->translatedFormat('d F Y')}}" required autocomplete="tgl_lahir" date-provide="datepicker" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Kota</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="regency" id="regency" class="form-control">
                                            <option value="0">-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Ibu Kandung</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="ibu_kandung" type="text"
                                            class="form-control @error('ibu_kandung') is-invalid @enderror font-weight-bold" name="ibu_kandung"
                                            value="{{$mother_name}}" required autocomplete="ibu_kandung" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Kecamatan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="district" id="district" class="form-control">
                                            <option value="0">-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Bapak Kandung</label>
                                    <div class="input-group" style="padding: 0px">
                                        <input id="bapak_kandung" type="text"
                                            class="form-control @error('bapak_kandung') is-invalid @enderror font-weight-bold" name="bapak_kandung"
                                            value="{{$father_name}}" required autocomplete="bapak_kandung" autofocus placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Kelurahan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="village" id="village" class="form-control">
                                            <option value="0">-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Suku</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="suku" id="suku" class="form-control">
                                            <option value="">--Pilih Suku--</option>
                                            @foreach ($suku as $sk)
                                                <option value="{{ $sk->id }}" @if($sk->id == $ethnicity) {{'selected'}}@endif >{{ $sk->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Alamat</label>
                                    <div class="input-group" style="padding: 0px">
                                        <textarea class="form-control" name="alamat" id="alamat" cols="30" rows="10">{{$address}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Pekerjaan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="job" id="job" class="form-control">
                                            <option value="">--Pilih Pekerjaan--</option>
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->id }}" @if($job->id == $occupation) {{'selected'}}@endif >{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="">Agama</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="religion" id="religion" class="form-control">
                                            <option value="">--Pilih Pekerjaan--</option>
                                            @foreach ($religion as $religions)
                                                <option value="{{ $religions->id }}" @if($religions->id == $relig) {{'selected'}}@endif >{{ $religions->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="">Pendidikan</label>
                                    <div class="input-group" style="padding: 0px">
                                        <select name="education" id="education" class="form-control">
                                            <option value="">--Pilih Pendidikan--</option>
                                            @foreach ($educate as $edu)
                                                <option value="{{ $edu->id }}" @if($edu->id == $education) {{'selected'}}@endif >{{ $edu->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary simpan_tersangka" id="simpan-tersangka">
                            {{ __('Simpan') }}
                        </button>
                        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </fieldset>
        </form>
    </div>


</div>
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
<script type="text/javascript">
    $('#letter_date_ketetapan').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom',
        });
    $('#tgl_resume').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom',
        });
    $(document).ready(function() {
        // Initialize Ajax CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get Polres Regency Data
        $('#province').on('change', function() {
            var provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: '/api/forms/regency',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'provinceId': provinceId
                    },
                    success: function(data) {
                        $('#regency').empty();
                        $('#regency').append('<option value="">Pilih Kabupaten/Kota</option>');
                        $.each(data, function(key, value) {
                            $('#regency').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });

                        // Reset District, and Village Dropdown
                        $('#district').empty();
                        $('#district').append('<option value="">Pilih Kecamatan (Silahkan Pilih Kabupaten/Kota Terlebih Dahulu)</option>');
                        $('#village').empty();
                        $('#village').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                        $('#regency').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#regency').empty();
            }
        });

        // Get Polres District Data
        $('#regency').on('change', function() {
            var regencyId = $(this).val();
            if (regencyId) {
                $.ajax({
                    url: '/api/forms/district',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'regencyId': regencyId
                    },
                    success: function(data) {
                        $('#district').empty();
                        $('#district').append('<option value="">Pilih Kecamatan</option>');
                        $.each(data, function(key, value) {
                            $('#district').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#district').select2({
                            theme: 'classic',
                        });

                        // Reset Village Dropdown
                        $('#village').empty();
                        $('#village').append('<option value="">Pilih Kelurahan/Desa (Silahkan Pilih Kecamatan Terlebih Dahulu)</option>');
                    }
                });
            } else {
                $('#district').empty();
            }
        });

        // Get Polres Village Data
        $('#district').on('change', function() {
            var districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    url: '/api/forms/village',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'districtId': districtId
                    },
                    success: function(data) {
                        $('#village').empty();
                        $('#village').append('<option value="">Pilih Kelurahan/Desa</option>');
                        $.each(data, function(key, value) {
                            $('#village').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#village').select2({
                            theme: 'classic',
                        });
                    }
                });
            } else {
                $('#village').empty();
            }
        });
    });

    $(document).ready(function () {
        $('#sumber').on('change', function() {
            if (this.value == '1') {
                $('#resume_penetapan_tersangka').hide();
                $('#gelar_perkara').show();
            } else {
                $('#resume_penetapan_tersangka').hide();
                $('#gelar_perkara').hide();
            }
        });

    });

    $(function() {
        $('#tersangka_id').val('');
        var _token = $("input[name='_token']").val();
        var accident_id = $("#accident_id").val();

        $(".simpan-tersangka").click(function(e) {
            // alert('success')
            e.preventDefault();
            var _token = $("input[name='_token']").val();
            var tersangka_id = $("#tersangka_id").val();
            var accident_id_sddl = $("#accident_id_sddl").val();
            var identity_type = $("#identity_type").val();
            var no_identitas = $("#no_identitas").val();
            var name_tsk = $("#name_tsk").val();
            var jenis_kelamin = $("#jenis_kelamin").val();
            var tempat_lahir = $("#tempat_lahir").val();
            var tgl_lahir = $("#tgl_lahir").val();
            var ibu_kandung = $("#ibu_kandung").val();
            var bapak_kandung = $("#bapak_kandung").val();
            var suku = $("#suku").val();
            var job = $("#job").val();
            var religion = $("#religion").val();
            var education = $("#education").val();
            var country = $("#country").val();
            var marriage = $("#marriage").val();
            var no_tlp = $("#no_tlp").val();
            var email = $("#email").val();
            var province = $("#province").val();
            var regency = $("#regency").val();
            var district = $("#district").val();
            var village = $("#village").val();
            var alamat = $("#alamat").val();

            $.ajax({
                url: "{{ route('store_tersangka') }}",
                type: 'POST',
                data: {
                    _token: _token,
                    tersangka_id: tersangka_id,
                    identity_type: identity_type,
                    no_identitas: no_identitas,
                    name_tsk: name_tsk,
                    jenis_kelamin: jenis_kelamin,
                    tempat_lahir: tempat_lahir,
                    tgl_lahir: tgl_lahir,
                    ibu_kandung: ibu_kandung,
                    bapak_kandung: bapak_kandung,
                    suku: suku,
                    job: job,
                    religion: religion,
                    country: country,
                    marriage: marriage,
                    no_tlp: no_tlp,
                    email: email,
                    province: province,
                    regency: regency,
                    district: district,
                    village: village,
                    alamat: alamat
                },
                success: function(data) {
                    alert('Tersangka Berhasil Ditambahkan');
                    $('#formTambahTersangka')[0].reset();
                    $('#tersangka_id').val('');
                    tersangka.draw();
                }
            });
        });
    });


</script>
@endpush

