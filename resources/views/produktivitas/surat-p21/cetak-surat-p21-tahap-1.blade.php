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
                <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                    <div class="text-center">
                        <img class="w-40" src="{{ asset('images/logo.png') }}">
                        <h6 class="font-weight-bold m-0">KEPOLISIAN NEGARA REPUBLIK INDONESIA</h6>
                        <h6 class="font-weight-bold m-0">{{$province_name}}</h6>
                        <h6 class="font-weight-bold m-0">RESOR {{$polres_name}}</h6>
                    </div>
                </div>
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-4 ">
                        <h6 class="text-center"><span class="border-bot">{{$polres_address}}</span></h6>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-8 ">
                        <h6 class="m-0 float-right">{{$p21_location}}, {{$p21_date}}</h6>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                    <div class="row">
                        <h6 class="col-lg-5 col-md-5 col-sm-5 col-5">Nomor</h6>
                        <h6><span class="col-lg-1 col-md-1 col-sm-1 p-0">: {{$no_p21}}</span></h6>
                    </div>
                    <div class="row">
                        <h6 class="col-lg-5 col-md-5 col-sm-5 col-5">Klasifikasi</h6>
                        <h6><span class="col-lg-1 col-md-1 col-sm-1 p-0">: {{$classification}}</span></h6>
                    </div>
                    <div class="row">
                        <h6 class="col-lg-5 col-md-5 col-sm-5 col-5">Lampiran</h6>
                        <h6><span class="col-lg-1 col-md-1 col-sm-1 p-0">: {{$attachment}}</span></h6>
                    </div>
                </div>
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-5">
                        <div class="row">
                            <h6 class="col-lg-4 col-md-4 col-sm-4 col-4 mr-1">Perihal</h6>
                            <h6><span class="col-lg-1 col-md-1 col-sm-1 p-0">: {{$subject}}</span></h6>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7 col-7 floar-right text-center">
                        <h6>Kepada</h6>
                        <h6>Yth. KEPALA {{$letter_recipient}}</h6>
                        <h6>di</h6>
                        <h6><span class="border-bot">{{$recipient_location}}</span></h6>
                    </div>
                </div>

                @csrf
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <h6 class="col-lg-2 col-md-2 col-sm-2 col-2 font-weight-bold"><span class="font-weight-bold">1.
                        </span>Rujukan
                        <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span></h6>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 pb-4">
                    <textarea class="form-control inline-block txtarea" style="white-space: pre-line;" disabled>
                        a. a.	Pasal 8 ayat (2) dan Pasal 10 ayat (1) KUHAP;
                        b. Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;
                        c. Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;
                        d. Laporan Polisi nomor : {{$no_lp}}, tanggal {{$accident_date}}.
                        e. Surat Pemberitahuan Dimulainya Penyidikan Nomor : {{$no_spdp}}, tanggal {{$spdp_date}}.
                    </textarea>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <h6 class="pl-3"><span class="font-weight-bold">2.</span></h6>
                    <div class="col-lg-11 col-md-11 col-sm-11 col-11">
                        <textarea class="form-control inline-block txtarea" style="white-space: pre-line;" disabled>
                        Sehubungan dengan rujukan tersebut di atas, Bersama ini diberitahukan kepada Ka, pada Hari {{Carbon\Carbon::parse($accident->accident_date)->dayName}}. Tanggal {{$accident_date}} sekira pukul {{$accident_time}} di {{$road_name}} ; {{$description}} Sebagaimana dimaksud dalam pasal 310 ayat (4), (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan.
                        </textarea>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3 pl-3">
                    <h6 class="pl-3"><span class="font-weight-bold pr-3">3.</span>Demikian untuk menjadi maklum.</h6>
                </div>

                <button class="btn btn-primary float-right mr-4">
                    <a href="{{url('createword-surat-p21-tahap-1/'.$accident->id)}}" style="color: #ffffff">Generate</a>
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

        // $('.txtarea').each(function () {
        //     this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow:hidden;');
        // }).on('input', function () {
        //     this.style.height = 'auto';
        //     this.style.height = (this.scrollHeight) + 'px';
        // });

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
