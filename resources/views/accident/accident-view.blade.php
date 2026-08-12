@php
    $_title = 'Detail Perkara';
@endphp

@extends('layouts.app')

@section('title',$_title)

@section('content')
    <div class="box">
        <div class="box-body">
            @foreach ($data['result'] as $datas)
                <form method="POST" action="{{ route('save_accident') }}">
                    @csrf
                    <input type="hidden" name="dors_id" value="{{ $datas->dors_id }}" id="dors_id">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="col-12">
                                <div class="form-group">
                                    <input id="officer_rank_id" name="officer_rank_id" type="text" disabled="disabled"
                                        value="{{ $datas->rank_id }}" hidden>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <input id="officer_first_name" name="officer_first_name" type="text"
                                        disabled="disabled" value="{{ $datas->first_name }}" hidden>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <input id="officer_last_name" name="officer_last_name" type="text"
                                        disabled="disabled" value="{{ $datas->last_name }}" hidden>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nama
                                        Petugas Pelapor</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="officer_name" name="officer_name" type="text" disabled="disabled"
                                            value="{{ $datas->rank_id }} {{ $datas->first_name }} {{ $datas->last_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nomor
                                        Petugas</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                        <input id="officer_id" name="officer_id" type="text" disabled="disabled"
                                            value="{{ $datas->officer_id }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Polres</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                        <input id="polres_id" name="polres_id" type="text" disabled="disabled"
                                            value="{{ $datas->polres_id }}" class="form-control" hidden>
                                        <input id="polres_id_name" name="polres_id_name" type="text" disabled="disabled"
                                            value="{{ $datas->polres_name }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <input id="accident_date" name="accident_date" type="text" disabled="disabled"
                                        value="{{ $datas->accident_date }}" hidden>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <input id="accident_time" name="accident_time" type="text" disabled="disabled"
                                        value="{{ $datas->accident_time }}" hidden>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Tanggal
                                        Kejadian</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input type="text" disabled="disabled"
                                            value="{{ $datas->accident_date }} {{ $datas->accident_time }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <input id="report_date" name="report_date" type="text" disabled="disabled"
                                        value="{{ $datas->report_date }}" hidden>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <input id="report_time" name="report_time" type="text" disabled="disabled"
                                        value="{{ $datas->report_time }}" hidden>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nomor
                                        LP</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="no_lp" name="no_lp" type="text" disabled="disabled"
                                            value="{{ $datas->no_lp }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Latitude</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                        <input id="latitude" name="latitude" type="text" disabled="disabled"
                                            value="{{ $datas->latitude }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Longtitude</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                        <input id="longtitude" name="longtitude" type="text" disabled="disabled"
                                            value="{{ $datas->longtitude }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Total
                                        Ranmor</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="total_ranmor" name="total_ranmor" type="text" disabled="disabled"
                                            value="{{ $datas->total_ranmor }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Tipe
                                        Kecelakaan</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="accident_type_id" name="accident_type_id" type="text"
                                            disabled="disabled" value="{{ $datas->accident_type_id }}"
                                            class="form-control" hidden>
                                        <input id="accident_type_id" name="accident_type_id" type="text"
                                            disabled="disabled" value="{{ $datas->accident_type_id_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <input id="md" name="md" type="text" disabled="disabled"
                                        value="{{ $datas->md }}" hidden>
                                    <input id="lb" name="lb" type="text" disabled="disabled"
                                        value="{{ $datas->lb }}" hidden>
                                    <input id="lr" name="lr" type="text" disabled="disabled"
                                        value="{{ $datas->lr }}" hidden>
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Tingkat
                                        Luka</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        @if ($datas->md > 0)
                                            <input type="text" disabled="disabled" value="Berat"
                                                class="form-control">
                                        @elseif ($datas->lb > 0)
                                            <input type="text" disabled="disabled" value="Sedang"
                                                class="form-control">
                                        @else
                                            <input type="text" disabled="disabled" value="Ringan"
                                                class="form-control">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nama
                                        Jalan</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <textarea id="road_name" name="road_name" type="text" disabled="disabled" class="form-control">{{ $datas->road_name }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Kondisi
                                        Cuaca</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="weather_cond_id" name="weather_cond_id" type="text"
                                            disabled="disabled" value="{{ $datas->weather_cond_id }}"
                                            class="form-control" hidden>
                                        <input id="weather_cond_id" name="weather_cond_id" type="text"
                                            disabled="disabled" value="{{ $datas->weather_cond_id_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Kondisi
                                        Cahaya</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="light_cond_id" name="light_cond_id" type="text"
                                            disabled="disabled" value="{{ $datas->light_cond_id }}" class="form-control"
                                            hidden>
                                        <input id="light_cond_id" name="light_cond_id" type="text"
                                            disabled="disabled" value="{{ $datas->light_cond_id_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Fungsi
                                        Jalan</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="road_function_id" name="road_function_id" type="text"
                                            disabled="disabled" value="{{ $datas->road_function_id }}"
                                            class="form-control" hidden>
                                        <input id="road_function_id" name="road_function_id" type="text"
                                            disabled="disabled" value="{{ $datas->road_function_id_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Status
                                        Jalan</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="road_state_id" name="road_state_id" type="text"
                                            disabled="disabled" value="{{ $datas->road_state_id }}" class="form-control"
                                            hidden>
                                        <input id="road_state_id" name="road_state_id" type="text"
                                            disabled="disabled" value="{{ $datas->road_state_id_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group row">
                                    <label
                                        class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Laka
                                        Jol</label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                        <input id="urgent_accident_id" name="urgent_accident_id" type="text"
                                            disabled="disabled" value="{{ $datas->urgent_accident_id }}"
                                            class="form-control" hidden>
                                        <input id="urgent_accident_id_name" name="road_state_id_name" type="text"
                                            disabled="disabled" value="{{ $datas->urgent_accident_id_name }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="fw-semibold">Deskripsi</label>
                            <div class="col-12">
                                <textarea id="damage_lose_desc" name="damage_lose_desc" type="text" disabled="disabled" class="form-control">{{ $datas->damage_lose_desc }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row" style="visibility: hidden">
                            <div>
                                <input id="id" name="id" type="text" disabled="disabled"
                                    value="{{ $datas->id }}">
                            </div>
                        </div>
                    </div>

                    @if(Auth::getUser()->role_id == 3 || Auth::getUser()->role_id == 1)
                        @if (Auth::getUser()->polres_id != 0)
                            <div class="d-flex justify-content-center">
                                <button id="btn-save-accident" type="submit" class="btn btn-dark-blue">
                                    {{ __('Tindak Lanjut') }}
                                </button>
                            </div>
                        @endif
                    @endif
                </form>
            @endforeach
        </div>
    </div>

    @push('script')
        <script type="text/javascript">
            $(document).ready(function() {
                //---FOR MENU ARROWS--//
                $('#btn-save-accident').click(function() {
                    $('#id').prop('disabled', false);
                    $('#officer_id').prop('disabled', false);
                    $('#officer_rank_id').prop('disabled', false);
                    $('#officer_first_name').prop('disabled', false);
                    $('#officer_last_name').prop('disabled', false);
                    $('#polres_id').prop('disabled', false);
                    $('#accident_date').prop('disabled', false);
                    $('#accident_time').prop('disabled', false);
                    $('#report_date').prop('disabled', false);
                    $('#report_time').prop('disabled', false);
                    $('#no_lp').prop('disabled', false);
                    $('#latitude').prop('disabled', false);
                    $('#longtitude').prop('disabled', false);
                    $('#accident_type_id').prop('disabled', false);
                    $('#md').prop('disabled', false);
                    $('#lb').prop('disabled', false);
                    $('#lr').prop('disabled', false);
                    $('#road_name').prop('disabled', false);
                    $('#weather_cond_id').prop('disabled', false);
                    $('#light_cond_id').prop('disabled', false);
                    $('#road_function_id').prop('disabled', false);
                    $('#road_state_id').prop('disabled', false);
                    $('#damage_lose_desc').prop('disabled', false);
                    $('#total_ranmor').prop('disabled', false);

                    $.ajax({
                        type: "GET",
                        url: "https://irsmsdev.xyz/irsmsapidev/api/get_data_korban",
                        success: function(data) {
                            console.log(data);
                            // Use the data here to update the content of the webpage
                        }
                    });
                });

                function textAreaAdjust(element) {
                    element.style.height = "1px";
                    element.style.height = (25 + element.scrollHeight) + "px";
                }
            });
        </script>
    @endpush
@endsection
