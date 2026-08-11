<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Cetak-Laporan</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href={{ asset('css/app.css') }}>
    <link rel="stylesheet" href={{ asset('css/bootstrap.css') }}>
</head>

<body>
    <div class="d-flex justify-content-center" style="background-color: #eeeeee">
        <div class="radius-card mt-4 w-60">
            <form target="_blank" method="POST">
                <div>
                    <input type="text" name="" id="" value={{$accident->id}} hidden>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 pb-5">
                    <span class="border-bot">DIREKTORAT LALU LINTAS</span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span>PRO JUSTICIA</span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                    <h3 style=""><span><img class="w-15" src="{{ asset('images/logo.png') }}"></span>
                    </h3>
                </div>

                <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3><span class="pb-1 border-bot  font-weight-bolder">SURAT PERINTAH TUGAS</span></h3>
                    <h4 class="pt-1 pb-2 font-weight-bolder">NOMOR : </h4>
                </div>

                @csrf
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Pertimbangan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <textarea class="form-control inline-block txtarea"
                            name="" disabled>Bahwa untuk kepentingan penyidikan tindak pidana, maka perlu mengeluarkan surat perintah ini.</textarea>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Dasar</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <textarea class="form-control inline-block txtarea" name=""
                            disabled>1. Pasal 7, Pasal 8, Pasal 9, Passal 11, Pasal 12, Pasal 159, Pasal 109 ayat (1), Pasal 10 ayat (1) KUHAP&#13;&#10;2. Undang-Undang Republik Indonesia Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia&#13;&#10;3. Udang-Undang Republik Indonesia Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan&#13;&#10;4. Laporan Polisi Nomor : , tanggal 25 Desember 2020</textarea>
                    </div>
                </div>

                <div class="col-md-12 col-xs-12 col-sm-12 text-center mb-3" style="padding:0;">
                    <h5 class="font-weight-bolder">DIPERINTAHKAN</h5>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Kepada</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <textarea class="form-control inline-block txtarea" name="" id="" disabled>@foreach ($surat_perintah_penyidikan as $sidik ){{$sidik->rank_id}} {{$sidik->first_name}} {{$sidik->last_name}} NRP {{$sidik->id}}&#13;&#10;{{$sidik->position_short_name}}&#13;&#10;@endforeach</textarea>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Untuk</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <textarea class="form-control inline-block txtarea"
                            name="" disabled>1. Melakukan penyidikan terhadap kecelakaan lalu lintas yang terjadi pada Hari ............ tanggal {{$accident_date}} sekira pukul {{$accident->accident_time}} di {{$accident->road_name}}&#13;&#10;2. Membuat rencana penyidikan&#13;&#10;3. Melaporkan setiap perkembangan pelaksanaan penyidik tindak pidana tersebut kepada Kepala Seksi Yanmas Laka selaku Penyidik&#13;&#10;4. Surat Perintah ini berlaku sejak tanggal dikeluarkan</textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-primary float-right mr-4">
                    <a href="{{url('createword-surat-penyidikan/'.$accident->id)}}" style="color: #ffffff">Generate</a>
                </button>

            </form>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

		$('.form-control').each(function(i,el){$(el).height(el.scrollHeight+5)});$(window).resize(function(){$('.form-control').css('overflow','auto');if(Math.round(window.devicePixelRatio*100)==100)$('.form-control').css('overflow','hidden')})

		$('.txtarea').each(function () {
			this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow:hidden;');
		}).on('input', function () {
			this.style.height = 'auto';
			this.style.height = (this.scrollHeight) + 'px';
		});

		$('#print-cetak').on('click', function printAnyMaps() {
			const $body = $('body');
			const $mapContainer = $('.modal-lg');
			const $mapContainerParent = $mapContainer.parent();
			const $printContainer = $('<div style="position:relative;">');

			$printContainer
				.height($mapContainer.height())
				.append($mapContainer)
				.prependTo($body);

			const $content = $body
				.children()
				.not($printContainer)
				.not('script')
				.detach();

			/**
			* Needed for those who use Bootstrap 3.x, because some of
			* its `@media print` styles ain't play nicely when printing.
			*/
			const $patchedStyle = $('<style media="print">')
				.text(`
				img { max-width: none !important; }
				a[href]:after { content: ""; }
				`)
				.appendTo('head');

			window.print();

			$body.prepend($content);
			$mapContainerParent.prepend($mapContainer);

			$printContainer.remove();
			$patchedStyle.remove();
		});
	});
</script>

</html>
