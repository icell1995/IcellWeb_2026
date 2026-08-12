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
                    <h3><span class="pb-1 border-bot  font-weight-bolder">LAPORAN HASIL GELAR PERKARA</span></h3>
                </div>

                @csrf

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="font-weight-bold">I. Dasar :</h5>
                        </div>
                    </div>
                    <div class="col-12 p-0">
                        <textarea class="form-control" name="" style="white-space: pre-line;" disabled>
                            1. Laporan Polisi Nomor : {{$no_lp}}
                            2. Nomor Surat perintah Penyidikan : {{$no_sprindik}}
                            3. Surat {{$surat_undangan}} Tentang Undangan Gelar Perkara
                        </textarea>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="font-weight-bold">II. PELAKSANAAN GELAR PERKARA :</h5>
                        </div>
                    </div>
                    <div class="col-12 p-0">
                        <textarea class="form-control" name="" style="white-space: pre-line;" disabled>
                                1. Permasalahan     :
                                2. Hari dan Tanggal : {{$tanggal_pelaksanaan}}
                                3. Jam              : {{$waktu_pelaksanaan}}
                                4. Tempat           : {{$tempat_pelaksanaan}}
                                5. Pemapar          : {{$pemapar}}
                                6. Pimpinan Gelar   : {{$pimpinan_gelar}}
                        </textarea>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="font-weight-bold">III. PEMBAHASAN :</h5>
                        </div>
                    </div>
                    <div class="col-12 p-0">
                        <textarea class="form-control" name="" style="white-space: pre-line;" disabled>
                            {{$pembahasan}}
                        </textarea>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="font-weight-bold">IV. KESIMPULAN :</h5>
                        </div>
                    </div>
                    <div class="col-12 p-0">
                        <textarea class="form-control" name="" style="white-space: pre-line;" disabled>
                            {{$kesimpulan}}
                        </textarea>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="font-weight-bold">V. PENUTUP :</h5>
                        </div>
                    </div>
                    <div class="col-12 p-0">
                        <textarea class="form-control" name="" style="white-space: pre-line;" disabled>
                            {{$penutup}}
                        </textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-primary float-right mr-4">
                    <a href="{{url('createword-lhgp/'.$accident->id)}}" style="color: #ffffff">Generate</a>
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
