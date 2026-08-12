@php
    $_title = 'Tindak Lanjut Perkara Jatanlin';
@endphp

@extends('layouts.app')

@section('title',$_title)

@section('content') 
    <div class="box">
        <div class="box-body">
            @php 
                $case = $case['result'][0];
            @endphp


            <form method="POST" action="{{ route('case.save', ['id' => $case['id']]) }}">
                @csrf
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nama
                                    Petugas Pelapor</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="officer_name" name="officer_name" type="text" disabled="disabled"
                                        value="{{ $case['created_by'] }}"
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
                                        value="{{ $case['officer_id'] }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Polres</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="polres_id_name" name="polres_id_name" type="text" disabled="disabled"
                                        value="{{ $case['polres_name'] }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Tanggal
                                    Kejadian</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input type="text" disabled="disabled"
                                        value="{{ Carbon\Carbon::parse($case['accident_date'])->locale('id')->format('d F Y') }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Waktu
                                    Kejadian</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input type="text" disabled="disabled"
                                        value="{{ Carbon\Carbon::parse($case['accident_time'])->format('H:i') . ' WIB' }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nomor
                                    LP</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="no_lp" name="no_lp" type="text" disabled="disabled"
                                        value="{{ $case['no_lp'] }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Latitude</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                    <input id="latitude" name="latitude" type="text" disabled="disabled"
                                        value="{{ $case['latitude'] }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Longtitude</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">

                                    <input id="longtitude" name="longtitude" type="text" disabled="disabled"
                                        value="{{ $case['longitude'] }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Nama
                                    Jalan</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <textarea id="road_name" name="road_name" type="text" disabled="disabled" class="form-control">{{ $case['road_name'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Tipe
                                    Kendaraan</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="vehicle_type_id" name="vehicle_type_id" type="text"
                                        disabled="disabled" value="{{ $case['vehicle_type'] }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Desain 
                                    Kendaraan</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="vehicle_design_id" name="vehicle_design_id" type="text"
                                        disabled="disabled" value="{{ $case['vehicle_design'] }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Warna
                                    Kendaraan</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="vehicle_color_id" name="vehicle_color_id" type="text"
                                        disabled="disabled" value="{{ $case['vehicle_color'] }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">No
                                    Plat</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="plate_no" name="plate_no" type="text"
                                        disabled="disabled" value="{{ $case['plate_no'] }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">No
                                    STNK</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="stnk_no" name="stnk_no" type="text"
                                        disabled="disabled" value="{{ $case['stnk_no'] }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Tanggal
                                    Dilaporkan</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="report_date" name="report_date" type="text"
                                        disabled="disabled" value="{{ Carbon\Carbon::parse($case['report_date'])->locale('id')->format('d F Y') }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 col-md-4 col-sm-12 col-12 fw-semibold d-flex align-items-center">Waktu
                                    Dilaporkan</label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <input id="report_time" name="report_time" type="text"
                                        disabled="disabled" value="{{ Carbon\Carbon::parse($case['report_time'])->format('H:i') . ' WIB' }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="fw-semibold">Deskripsi</label>
                        <div class="col-12">
                            <textarea id="accident_description" name="accident_description" type="text" disabled="disabled" class="form-control" rows="3">{{ $case['accident_description'] }}</textarea>
                        </div>
                        <div class="col-12">
                            <textarea id="damage_lose_desc" name="damage_lose_desc" type="text" disabled="disabled" class="form-control mt-2" rows="3">{{ $case['damage_lose_desc'] }}</textarea>
                        </div>
                        <div class="col-12">
                            <textarea id="temporary_deductive" name="temporary_deductive" type="text" disabled="disabled" class="form-control mt-2" rows="3">{{ $case['temporary_deductive'] }}</textarea>
                        </div>
                    </div>
                </div>

                @if(Auth::getUser()->role_id == 3 || Auth::getUser()->role_id == 1)
                    <div class="d-flex justify-content-center mt-3">
                        <a class="btn btn-danger me-2" href="{{ route('case.index') }}">
                            Kembali
                        </a>
                        <button id="btn-save-accident" type="submit" class="btn btn-dark-blue">
                            {{ __('Tindak Lanjut') }}
                        </button>
                    </div>
                @endif
            </form>
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
