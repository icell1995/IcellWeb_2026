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

                <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3><span class="pb-1 border-bot  font-weight-bolder">SURAT PEMBERITAHUAN DIMULAINYA PENYIDIKAN</span></h3>
                    <h4 class="pt-1 pb-2 font-weight-bolder">NOMOR : {{$suratPemberitahuanDimulainyaPenyidikanDocument->document_number}}</h4>
                </div>

                @csrf
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Menimbang</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                 bahwa berdasarkan hasil penyidikan telah diperoleh dua alat bukti atau lebih dan laporan hasil gelar perkara, Penyidik menetapkan status seseorang sebagai Tersangka, maka perlu dikeluarkan surat ketetapan.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Mengingat</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                1. Pasal 1 butir 14, Pasal 26 dan Pasal 184 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana;<br/>
                                2. Pasal 16 Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;<br/>
                                3. <br/>
                                4. Laporan Polisi Nomor : {{$accident->no_lp}}, tanggal {{Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y')}} <br/>
                                5. Surat Perintah Penyidikan Nomor : {{$suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument->document_number}}, tanggal {{Carbon\Carbon::parse($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y')}}<br/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Memperhatikan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                Laporan Hasil Gelar Perkara, tanggal {{Carbon\Carbon::parse($suratPemberitahuanDimulainyaPenyidikanDocument->laporanHasilGelarPerkaraDocument->document_date ?? null)->locale('id')->translatedFormat('d F Y')}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xs-12 col-sm-12 text-center mb-3" style="padding:0;">
                    <h5 class="font-weight-bolder">M  E  M  U  T  U  S  K  A  N</h5>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Menetapkan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                1.	Status seseorang dengan identitas sebagai berikut : <br/>
                                    a.	Nama	:	<br/>
                                    b.	Nomor Identitas	:	<br/>
                                    c.	Kewarganegaraan	:	<br/>
                                    d.	Jenis Kelamin	:	<br/>
                                    e.	Tempat/Tanggal Lahir	:	<br/>
                                    f.	Pekerjaan	:	<br/>
                                    g.	Agama	:	<br/>
                                    h.	Alamat	:	<br/>
                                <br/>
                                2.	menjadi TERSANGKA sehubungan dengan perkara dugaan tindak pidana DATA BELUM DILENGKAPI sebagaimana dimaksud dalam Pasal DATA BELUM DILENGKAPI yang terjadi di DATA BELUM DILENGKAPI, pada hari tanggal bulan tahun . <br/>
                                <br/>
                                3.	Memberitahukan penetapan tersangka kepada Kepala Kejaksaan Negeri ..... <br/>
                                <br/>
                                4.	Surat Ketetapan ini berlaku sejak tanggal ditetapkan. <br/>

                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-lg float-right mr-4">
                    <a href="{{route('doc.surat-pemberitahuan-dimulainya-penyidikan-document.download', ['id'=> $suratPemberitahuanDimulainyaPenyidikanDocumentId,'accident_id' => $accident->id])}}" style="color: #ffffff"><i class="fa fa-download"></i> Download</a>
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
