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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex justify-content-center" style="background-color: #eeeeee">
        <div class="radius-card mt-4 w-60">
            <form target="_blank" method="POST">
                <div>
                    <input type="text" name="" id="" value={{$accident->id}} hidden>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span class="">KEPOLISIAN NEGARA REPUBLIK INDONESIA</span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span class="">DAERAH {{$accident->polres->polda->full_name ?? ''}}</span>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span class="">RESOR {{$accident->polres->full_name ?? ''}}</span>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span class="border-bot">{{ucwords($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode)}}</span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                    <h3 style=""><span><img class="w-15" src="{{ asset('images/logo.png') }}"></span>
                    </h3>
                </div>

                <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 text-center mb-4">
                    <h3><span class="pb-1 border-bot  font-weight-bolder">LAPORAN HASIL GELAR PERKARA</span></h3>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Dasar</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                1. Laporan Polisi Nomor : {{$accident->no_lp}} <br/>
                                2. Nomor Surat Perintah Penyidikan : {{$laporanHasilGelarPerkaraDocument->suratPerintahPenyidikanDocument->document_number}} <br/>
                                3. Surat {{$laporanHasilGelarPerkaraDocument->case_degree_invite_reference}} Tentang Undangan gelar perkara. <br/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Pelaksanaan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                a.	Permasalahan :	<br/>
	                            b.	Hari dan tanggal : {{Carbon\Carbon::parse($laporanHasilGelarPerkaraDocument->date)->locale('id')->translatedFormat('l') . ', ' . Carbon\Carbon::parse($laporanHasilGelarPerkaraDocument->date)->locale('id')->translatedFormat('d F Y')}} <br/>
	                            c.	Jam	: {{Carbon\Carbon::parse($laporanHasilGelarPerkaraDocument->time)->translatedFormat('H:i')}} {{$laporanHasilGelarPerkaraDocument->timezone->name}} <br/>
	                            d.	Tempat : {{$laporanHasilGelarPerkaraDocument->place}} <br/>
	                            e.	Peserta gelar :	<ul>
                                        <li>a.	Pimpinan Gelar : {{$laporanHasilGelarPerkaraDocument->case_degree_leader}}</li>
                                        <li>b.	Jumlah Peserta : {{$laporanHasilGelarPerkaraDocument->attendees}} </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
             
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Pembahasan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                {{$laporanHasilGelarPerkaraDocument->discussion}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Kesimpulan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                {{$laporanHasilGelarPerkaraDocument->conclusion}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Penutup</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                {{$laporanHasilGelarPerkaraDocument->closing}}
                            </div>
                        </div>
                    </div>
                </div>
            

                <button type="button" class="btn btn-primary btn-lg float-right mr-4">
                    <a href="{{route('doc.laporan-hasil-gelar-perkara-document.download', ['id'=> $laporanHasilGelarPerkaraDocumentId,'accident_id' => $accident->id])}}" style="color: #ffffff"><i class="fa fa-download"></i> Download</a>
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
