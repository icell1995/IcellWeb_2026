@extends('layouts.app')

@section('content')
<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="row">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">ANEV</h3>
            </div>

            <div class="radius-card">
                <form class="form_anev" action="{{ route('export_report_anev') }}" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <fieldset class="border p-2">
                                <legend class="w-auto font-weight-bold">Periode Lalu</legend>
                                <div class="form-group">
                                    <span class="font-weight-600">Dari Tanggal <span class="font-red">*</span></span>
                                    <input class="form-control datepicker" type="text" id="date_from" name="date_from"
                                        placeholder="DD - MM - YYYY" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <span class="font-weight-600">Hingga Tanggal <span class="font-red">*</span></span>
                                    <input class="form-control datepicker" type="text" id="date_to" name="date_to"
                                        placeholder="DD - MM - YYYY" autocomplete="off">
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <fieldset class="border p-2">
                                <legend class="w-auto font-weight-bold">Periode Ini</legend>
                                <div class="form-group">
                                    <span class="font-weight-600">Dari Tanggal <span class="font-red">*</span></span>
                                    <input class="form-control datepicker" type="text" id="date_from_now"
                                        name="date_from_now" placeholder="DD - MM - YYYY" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <span class="font-weight-600">Hingga Tanggal <span class="font-red">*</span></span>
                                    <input class="form-control datepicker" type="text" id="date_to_now"
                                        name="date_to_now" placeholder="DD - MM - YYYY" autocomplete="off">
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                            <div class="form-group">
                                <span class="font-weight-600">Polda</span>
                                <select id="polda_id" name="polda_id"
                                    class="form-control @error('polda_id') is-invalid @enderror">
                                    @if( Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                    <option value="-" }}>Semua Polda</option>
                                    @endif
                                    @foreach ($polda as $poldas)
                                    <option value="{{ $poldas->id }}" {{ old('polda_id')==$poldas->id ? 'selected' : ''
                                        }}>
                                        {{ $poldas->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                            <div class="form-group">
                                <span class="font-weight-600">Polres</span>
                                <select id="polres_id" name="polres_id"
                                    class="form-control @error('polres_id') is-invalid @enderror">
                                    @if(Auth::user()->role_id <> 3)
                                        <option value="-" }}>Pilih Polres</option>
                                        @endif
                                        @foreach ($polres as $polress)
                                        <option value="{{ $polress->id }}" {{ old('polres_id')==$polress->id ?
                                            'selected' : '' }}>
                                            {{ $polress->name }}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="col-lg-12 text-center align-items-center">
                            <button type="submit" id="button_anev" class="btn btn-primary">Cari</button>
                            <div class="text-right">
                                {{-- <a href="{{route('export_report_anev')}}"></a> --}}
                                <button id="btn-export" class="btn btn-short rounded-pill btn-export" type="submit"
                                    style="display: none;"><span><i class="fas fa-download"></i></span> Export
                                    data</button>
                                {{-- <a class="btn btn-warning" href="{{ route('export_report_anev') }}">Export User
                                    Data</a> --}}
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box-body">
                <div class="radius-card">
                    <div class="anev table-responsive tabel-anev">
                        <table class="display" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="10" id="thn_lalu"></th>
                                    <th class="text-center" colspan="8" id="thn_ini"></th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="2">No</th>
                                    <th class="text-center" rowspan="2">Polda / Polres</th>
                                    <th class="text-center" rowspan="2">Total</th>
                                    <th class="text-center" colspan="7">Selra</th>
                                    <th class="text-center" rowspan="2">Total</th>
                                    <th class="text-center" colspan="7">Selra</th>
                                </tr>
                                <tr>
                                    <th class="text-center">P21</th>
                                    <th class="text-center">SP3</th>
                                    <th class="text-center">Diversi</th>
                                    <th class="text-center">POM/TNI</th>
                                    <th class="text-center">RJ</th>
                                    <th class="text-center">DLM PROSES</th>
                                    <th class="text-center">SP2LID</th>
                                    <th class="text-center">P21</th>
                                    <th class="text-center">SP3</th>
                                    <th class="text-center">Diversi</th>
                                    <th class="text-center">POM/TNI</th>
                                    <th class="text-center">RJ</th>
                                    <th class="text-center">DLM PROSES</th>
                                    <th class="text-center">SP2LID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="desc" id="no"></td>
                                    <td class="desc" id="polda"></td>
                                    <td class="text-center number" id="total_sidik_lalu"></td>
                                    <td class="text-center number" id="p21_lalu"></td>
                                    <td class="text-center number" id="sp3_lalu"></td>
                                    <td class="text-center number" id="diversi_lalu"></td>
                                    <td class="text-center number" id="pom_tni_lalu"></td>
                                    <td class="text-center number" id="rj_lalu"></td>
                                    <td class="text-center number" id="dlm_proses_lalu"></td>
                                    <td class="text-center number" id="sp2lid_lalu"></td>
                                    <td class="text-center number2" id="total_sidik_ini"></td>
                                    <td class="text-center number2" id="p21_ini"></td>
                                    <td class="text-center number2" id="sp3_ini"></td>
                                    <td class="text-center number2" id="diversi_ini"></td>
                                    <td class="text-center number2" id="pom_tni_ini"></td>
                                    <td class="text-center number2" id="rj_ini"></td>
                                    <td class="text-center number2" id="dlm_proses_ini"></td>
                                    <td class="text-center number2" id="sp2lid_ini"></td>
                                </tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc" style="font-weight: bold">TOTAL</td>
                                    <td class="text-center number" id="tot_sidik_lalu"></td>
                                    <td class="text-center number" id="tot_p21_lalu"></td>
                                    <td class="text-center number" id="tot_sp3_lalu"></td>
                                    <td class="text-center number" id="tot_diversi_lalu"></td>
                                    <td class="text-center number" id="tot_pom_tni_lalu"></td>
                                    <td class="text-center number" id="tot_rj_lalu"></td>
                                    <td class="text-center number" id="tot_dlm_proses_lalu"></td>
                                    <td class="text-center number" id="tot_sp2lid_lalu"></td>
                                    <td class="text-center number2" id="tot_sidik_ini"></td>
                                    <td class="text-center number2" id="tot_p21_ini"></td>
                                    <td class="text-center number2" id="tot_sp3_ini"></td>
                                    <td class="text-center number2" id="tot_diversi_ini"></td>
                                    <td class="text-center number2" id="tot_pom_tni_ini"></td>
                                    <td class="text-center number2" id="tot_rj_ini"></td>
                                    <td class="text-center number2" id="tot_dlm_proses_ini"></td>
                                    <td class="text-center number2" id="tot_sp2lid_ini"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $('#date_from').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom'
    });
    $('#date_to').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom'
    });
    $('#date_from_now').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom'
    });
    $('#date_to_now').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom'
    });


function clearAnev(){

	$('#no').html("");
	$('#polda').html("");

    //periode lalu
	$('#total_sidik_lalu').html("");
	$('#p21_lalu').html("");
	$('#sp3_lalu').html("");
	$('#diversi_lalu').html("");
	$('#pom_tni_lalu').html("");
    $('#rj_lalu').html("");
    $('#dlm_proses_lalu').html("");
    $('#sp2lid_lalu').html("");

    //total periode lalu
	$('#tot_sidik_lalu').html("");
	$('#tot_p21_lalu').html("");
	$('#tot_sp3_lalu').html("");
	$('#tot_diversi_lalu').html("");
	$('#tot_pom_tni_lalu').html("");
    $('#tot_rj_lalu').html("");
    $('#tot_dlm_proses_lalu').html("");
    $('#tot_sp2lid_lalu').html("");

    //periode ini
	$('#total_sidik_ini').html("");
	$('#p21_ini').html("");
	$('#sp3_ini').html("");
	$('#diversi_ini').html("");
	$('#pom_tni_ini').html("");
    $('#rj_ini').html("");
    $('#dlm_proses_ini').html("");
    $('#sp2lid_ini').html("");

    //total periode ini
	$('#tot_sidik_ini').html("");
	$('#tot_p21_ini').html("");
	$('#tot_sp3_ini').html("");
	$('#tot_diversi_ini').html("");
	$('#tot_pom_tni_ini').html("");
	$('#tot_rj_ini').html("");
	$('#tot_dlm_proses_ini').html("");
    $('#tot_sp2lid_ini').html("");
}

$(document).ready(function(){
    var get_pol = $('#polda_id').val();
        if( get_pol == null || get_pol == '-'){
            $('#polres_id').prop('disabled', true);
        }else{
            $('#polres_id').prop('disabled', false);
        }
        $("#hasil_days").hide()
        $("#btn-export").hide()
});

    $("#button_anev").click(function(e){
        e.preventDefault();
		// $("#loader").fadeIn();
		// 	$(".loaderbg").fadeIn();
        var _token = $("input[name='_token']").val();
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();
		var date_from_now = $('#date_from_now').val();
		var date_to_now = $('#date_to_now').val();
        // alert(date_to_now);
        var polda = $('#polda_id').val();
        var polres = $('#polres_id').val();


		// if(date_from == '' || date_to == '' || date_from_now == '' || date_to_now == ''){
		// 	$('#error_alert').modal()
		// 	$('#alert_modal').html('Silahkan Lengkapi Form Input')
		// 	return;
		// }
		clearAnev();
		// var user1 = $('#get_user').html();
		//  if(user1 == 1 || user1 == 2){
		// 	$('#polda').html("POLDA");
		// }else{
		// 	$('#polda').html("POLRES");
		// }
		// console.log($('#poldas').val())
       $.ajax({
        url: "{{ route('get_report_anev') }}",
        type: "GET",
        data : {
                _token:_token,
                date_from:date_from,
                date_to:date_to,
                date_from_now:date_from_now,
                date_to_now:date_to_now,
                polda:polda,
                polres:polres,

				// 'level'			: $('#get_user').html(),
				// 'type' 			: $('#laporan_berdasarkan1').val(),
			  },
		success: function(response) {
		// if(response.data.total_all_ini == 0 && response.data.total_all_lalu == 0){
		// 		$('#error_alert').modal()
		// 		$('#alert_modal').html('Data Tidak Ditemukan')
		// 		return;
		// }
		var data = response.data
		// var vChart = {
            // setDataText(data) {
				// var tahun_lalu = data.poldaName - 1;
                $("#btn-export").show();
				$('#thn_lalu').html("Periode Lalu")
				$('#thn_ini').html("Periode Ini")
				// var tot = 0;
			    // var rum = 0;
			    // var asa = 0;
			    // var asb = 0;
				// var asc = 0;
				// var tot1 = 0;
				// var rum1 = 0;
			    // var asa1 = 0;
			    // var asb1 = 0;
				// var asc1 = 0;
				// console.log(data);

				// var rankin = [];
				// if($('#get_user').html() == 1 || $('#get_user').html() == 2 || $('#get_user').html() == 3 || $('#get_user').html() == 4 || $('#get_user').html() == 5 || $('#get_user').html() == 6){
					data = data.summary;
                    // var length = data.tahun_lalu.length;

					// var rank = 1;
					// data.tahun_lalu.sort(function(a, b){
					// 	return b.total_tahun_ini - a.total_tahun_ini;
					// });
					for(i=0;i<data.tahun_lalu.length;i++){
						// if (i > 0 && data.tahun_lalu[i].total_tahun_ini < data.tahun_lalu[i - 1].total_tahun_ini) {
						// 	rank++;
						// }
						// data.tahun_lalu[i].rank = rank;

						$('#no').append('<tr><td class="desc table-desc">' + [i+1] + '</td></tr>')
                        $('#polda').append('<tr><td class="desc table-desc">'+ [data.tahun_lalu[i].polda] +'</td></tr>')

                        // periode lalu
						$('#total_sidik_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].total_laka_lalu] +'</td></tr>')
						$('#p21_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].p21_lalu] +'</td></tr>')
						$('#sp3_lalu').append('<tr><td class="text-center periode-lalu number;">'+ [data.tahun_lalu[i].sp3_lalu] +'</td></tr>')
						$('#diversi_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].diversi_lalu] +'</td></tr>')
                        $('#pom_tni_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].pom_tni_lalu] +'</td></tr>')
                        $('#rj_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].rj_lalu] +'</td></tr>')
                        $('#dlm_proses_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].dalam_proses_lalu] +'</td></tr>')
                        $('#sp2lid_lalu').append('<tr><td class="text-center periode-lalu number">'+ [data.tahun_lalu[i].sp2lid_lalu] +'</td></tr>')

                        //periode ini
                        $('#total_sidik_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].total_laka_ini] +'</td></tr>')
						$('#p21_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].p21_tahun_ini] +'</td></tr>')
						$('#sp3_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].sp3_tahun_ini] +'</td></tr>')
						$('#diversi_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].diversi_tahun_ini] +'</td></tr>')
                        $('#pom_tni_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].pom_tni_tahun_ini] +'</td></tr>')
                        $('#rj_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].rj_tahun_ini] +'</td></tr>')
                        $('#dlm_proses_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].dalam_proses_tahun_ini] +'</td></tr>')
                        $('#sp2lid_ini').append('<tr><td class="text-center periode-ini number2">'+ [data.tahun_lalu[i].sp2lid_tahun_ini] +'</td></tr>')

                        //total
                        $('#tot_sidik_lalu').html(response.data.total_sidik_lalu);
						$('#tot_p21_lalu').html(response.data.total_p21_lalu);
						$('#tot_sp3_lalu').html(response.data.total_sp3_lalu);
						$('#tot_diversi_lalu').html(response.data.total_diversi_lalu);
						$('#tot_pom_tni_lalu').html(response.data.total_pom_tni_lalu);
                        $('#tot_rj_lalu').html(response.data.total_rj_lalu);
                        $('#tot_dlm_proses_lalu').html(response.data.total_dalam_proses_lalu);
                        $('#tot_sp2lid_lalu').html(response.data.total_sp2lid_lalu);

						$('#tot_sidik_ini').html(response.data.total_sidik_ini);
						$('#tot_p21_ini').html(response.data.total_p21_ini);
						$('#tot_sp3_ini').html(response.data.total_sp3_ini);
						$('#tot_diversi_ini').html(response.data.total_diversi_ini);
						$('#tot_pom_tni_ini').html(response.data.total_pom_tni_ini);
                        $('#tot_rj_ini').html(response.data.total_rj_ini);
                        $('#tot_dlm_proses_ini').html(response.data.total_dalam_proses_ini);
                        $('#tot_sp2lid_ini').html(response.data.total_sp2lid_ini);
						// if (data.tahun_lalu[i].total_laka < data.tahun_lalu[i].total_tahun_ini){
						// 	$('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">Naik <span> <i class="fa fa-long-arrow-up" aria-hidden="true" style="color:red;"></i></span></td></tr>');
						// }else if(data.tahun_lalu[i].total_laka == data.tahun_lalu[i].total_tahun_ini){
						// 	$('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">Sama <span> <i class="fa fa-exclamation" aria-hidden="true" style="color:black;"></i></span></td></tr>');
						// }else{
						// 	$('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">Turun <span> <i class="fa fa-long-arrow-down" aria-hidden="true" style="color:green;"></i></span></td></tr>');
						// }
						// $('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">'+ [data.tahun_lalu[i].rank] +' </td></tr>');
					}
					// if (response.data.total_all_lalu < response.data.total_all_ini){
					// 		$('#total').append('<tr><td class="text-center" style="background: #feefb3">Naik <span> <i class="fa fa-long-arrow-up" aria-hidden="true" style="color:red;float: right;"></i></span></td></tr>');
					// 	}else if(response.data.total_all_lalu == response.data.total_all_ini){
					// 		$('#total').append('<tr><td class="text-center" style="background: #feefb3">Sama <span> <i class="fa fa-exclamation" aria-hidden="true" style="color:black;float: right;"></i></span></td></tr>');
					// 	}else{
					// 		$('#total').append('<tr><td class="text-center" style="background: #feefb3">Turun <span> <i class="fa fa-long-arrow-down" aria-hidden="true" style="color:green;float: right;"></i></span></td></tr>');
					// 	}

				// }
                // else{
				// 		for(i=0;i<data.tahun_lalu.length;i++){
				// 		tot1 = tot1 + data.tahun_lalu[i].total_laka;
				// 		asa = asa + data.tahun_lalu[i].md;
				// 		asb = asb + data.tahun_lalu[i].lb;
				// 		asc = asc + data.tahun_lalu[i].lr;
				// 		rum = rum + data.tahun_lalu[i].rumat;
				// 		total_lalu = data.tahun_lalu[i].total_laka;
				// 		$('.no').append('<tr><td class="text-center" style="background: #feefb3">' + [i+1] + '</td></tr>')
				// 		$('.polda').append('<tr><td class="text-center"style="background: #feefb3; text-align: left;">'+ [data.tahun_lalu[i].polda] +'</td></tr>')
				// 		$('.kejadian').append('<tr><td class="text-center"style="background: #fbc347; text-align: left;">'+ [data.tahun_lalu[i].total_laka] +'</td></tr>')
				// 		$('.md_lalu').append('<tr><td class="text-center"style="background: #fbc347; text-align: left;">'+ [data.tahun_lalu[i].md] +'</td></tr>')
				// 		$('.lb_lalu').append('<tr><td class="text-center"style="background: #fbc347; text-align: left;">'+ [data.tahun_lalu[i].lb] +'</td></tr>')
				// 		$('.lr_lalu').append('<tr><td class="text-center"style="background: #fbc347; text-align: left;">'+ [data.tahun_lalu[i].lr] +'</td></tr>')
				// 		$('.rumat_lalu').append('<tr><td class="text-center"style="background: #fbc347; text-align: left;">'+ [data.tahun_lalu[i].rumat] +'</td></tr>')
				// 		$('#tot').html(tot);
				// 		$('#mdt').html(asa);
				// 		$('#lbt').html(asb);
				// 		$('#lrt').html(asc);
				// 		$('#rum').html(rum);
				// 	}
				// 	for(j=0;j<data.tahun_ini.length;j++){
				// 		tot = tot + data.tahun_ini[j].total_laka;
				// 		asa1 = asa1 + data.tahun_ini[j].md;
				// 		asb1 = asb1 + data.tahun_ini[j].lb;
				// 		asc1 = asc1 + data.tahun_ini[j].lr;
				// 		rum1 = rum1 + data.tahun_ini[j].rumat;
				// 		total = tot1 + tot;
				// 		$('.hitung1').append('<tr><td class="text-center"style="background: #afa88b; text-align: left;">'+ [data.tahun_ini[j].total_laka] +'</td></tr>')
				// 		$('.md_ini').append('<tr><td class="text-center"style="background: #afa88b; text-align: left;">'+ [data.tahun_ini[j].md] +'</td></tr>')
				// 		$('.lb_ini').append('<tr><td class="text-center"style="background: #afa88b; text-align: left;">'+ [data.tahun_ini[j].lb] +'</td></tr>')
				// 		$('.lr_ini').append('<tr><td class="text-center"style="background: #afa88b; text-align: left;">'+ [data.tahun_ini[j].lr] +'</td></tr>')
				// 		$('.rumat_ini').append('<tr><td class="text-center"style="background: #afa88b; text-align: left;">'+ [data.tahun_ini[j].rumat] +'</td></tr>')
				// 		$('#tot1').html(tot);
				// 		$('#mdt1').html(asa1);
				// 		$('#lbt1').html(asb1);
				// 		$('#lrt1').html(asc1);
				// 		$('#rum1').html(rum1);
				// 		$('#total').html(total);

				// 		if (total_lalu < data.tahun_ini[j].total_laka){
				// 			$('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">Naik <span> <i class="fa fa-long-arrow-up" aria-hidden="true" style="color:red;float: right;"></i></span></td></tr>');
				// 		}else if(total_lalu == data.tahun_ini[j].total_laka){
				// 			$('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">Sama <span> <i class="fa fa-exclamation" aria-hidden="true" style="color:black;float: right;"></i></span></td></tr>');
				// 		}else{
				// 			$('.keterangan').append('<tr><td class="text-center" style="background: #feefb3">Turun <span> <i class="fa fa-long-arrow-down" aria-hidden="true" style="color:green;float: right;"></i></span></td></tr>');
				// 		}

				// 	}
				// }


            // },
        //      init: function(data) {
        //      this.setDataText(data)
        //     }
        // }
		// vChart.init(data);
		// $("#loader").fadeOut();
		// $(".loaderbg").fadeOut();
    //    }
      }
    });
   });

   $('#polda_id').on('change', function(event) {
        event.preventDefault();
        var poldaId = $(this).val();
        $('#polres_id').prop('disabled', true);
        $('#polres_id').empty();
        $('#polres_id').append('<option value="">Pilih Polres</option>');
        if (!poldaId) {
            return;
        }

        $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

            $('#polres_id').empty()
            var option = '<option value="">Pilih Polres</option>';
            $('#polres_id').append(option);

            $.each(data, function(key, polres) {
                var id = polres.id;
                var name = polres.name;
                var option = '<option value="' + id + '">' + name + '</option>';

                $('#polres_id').append(option);
            });

            $('#polres_id').prop('disabled', false);
        });
    });

    $("#btn-export").click(function(e){
        e.preventDefault();
        var polda= $("#polda_id").val();
        var polres = $('#polres_id').val();
        var start_date_then = $('#date_from').val();
        var end_date_then = $('#date_to').val();
        var start_date_now = $('#date_from_now').val();
        var end_date_now = $('#date_to_now').val();
        window.location = "{{ route('export_report_anev')}}"+ '?polda=' +polda+ '&polres=' +polres+ '&start_date_then=' +start_date_then+ '&end_date_then=' +end_date_then+ '&start_date_now=' +start_date_now+ '&end_date_now=' +end_date_now;

    });
</script>
@endpush
