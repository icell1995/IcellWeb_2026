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
                    <input type="text" name="" id="" value={{ $accident->id }} hidden>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-5">
                    <div class="text-center">
                        <h6 class="font-weight-bold m-0">KEPOLISIAN NEGARA REPUBLIK INDONESIA</h6>
                        <h6 class="m-0">DAERAH {{$polda_name ?? ''}}</h6>
                        <h6 class="m-0">RESOR {{$polres_name ?? ''}}</h6>
                        <h6 class="m-0">{{$polres_alamat . ', ' . $polres_district . ', ' . $polres_zipcode}}</h6>
                    </div>
                    <span class="font-weight-bold text-underline">"PRO JUSTITIA"</span>
                </div>
                
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 float-right text-center">
                        <img class="w-10" src="{{ asset('images/logo.png') }}">
                        <h6 class="text-center"><span class="font-weight-bold">SURAT KETETAPAN</span></h6>
                        <h6 class="text-center"><span>Nomor : {{$letter_number}}</span></h6>
                    </div>
                </div>

                @csrf
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <h6 class="col-lg-2 col-md-2 col-sm-2 col-2 font-weight-bold"><span class="font-weight-bold">1.
                        </span>Menimbang
                        <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span></h6>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 pb-4">
                    <textarea class="form-control inline-block txtarea" style="white-space: pre-line;" disabled>
                        Bahwa berdasarkan hasil penyidikan telah diperoleh dua alat bukti atau lebih dan laporan hasil gelar perkara, penyidik menetapkan status seseorang sebagai tersangka, maka perlu dikeluarkan surat ketetapan. 
                    </textarea>
                </div>
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <h6 class="col-lg-2 col-md-2 col-sm-2 col-2 font-weight-bold"><span class="font-weight-bold">2.
                        </span>Rujukan
                        <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span></h6>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 pb-4">
                    <textarea class="form-control inline-block txtarea" style="white-space: pre-line;" disabled>
                        a. Pasal 119 ayat(2) KUHAP;
                        b. Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;
                        c. Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;
                        d. Laporan Polisi Nomor : {{$no_lp}}, tanggal {{Carbon\Carbon::parse($accident->accident_date)->translatedFormat('d F Y')}}
                        e. Surat Perintah Penyidikan Nomor : {{$no_sprindik}}, tanggal {{Carbon\Carbon::parse($issued_date)->translatedFormat('d F Y')}}
                    </textarea>
                </div>
                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <h6 class="col-lg-4 col-md-4 col-sm-4 col-4 font-weight-bold">
                        <span class="font-weight-bold">3. Memperhatikan</span>
                        <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span></h6>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 pb-4">
                    <textarea class="form-control inline-block txtarea" style="white-space: pre-line;" disabled>
                        Laporan Hasil Gelar Perkara tanggal {{ $tanggal_pelaksanaan }}
                    </textarea>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <h6 class="pl-3"><span class="font-weight-bold">4. Menetapkan</span></h6>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                        <textarea class="form-control inline-block txtarea" style="white-space: pre-line;" disabled>
                            1. Status seseorang dengan identitas sebagai berikut :
                        
                            Nama                        : {{$name}}
                            Nomor Identitas             : {{$identity_number}}
                            Kewarganegaraan             : {{$country}}
                            Jenis Kelamin               : {{$gender}}
                            Tempat Tanggal Lahir / Umur : {{$birth_place}}, {{Carbon\Carbon::parse($birth_date)->translatedFormat('d F Y')}} / {{Carbon\Carbon::parse($birth_date)->age.' Tahun'}}
                            Pekerjaan                   : {{$occupation}}
                            Agama                       : {{$religion}}
                            Alamat                      : {{$address}}

                            2. Menjadi TERSANGKA sehubungan dengan perkara dugaan tindak pidana.

                            3. Memberitahukan penetapan tersangka kepada Kepala {{$kejaksaan}}.

                            4. Surat Ketetapan ini berlaku sejak tanggal ditetapkan.
                        </textarea>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3 pl-3">
                    <h6 class="pl-3"><span class="font-weight-bold pr-3">5.</span>Demikian untuk menjadi maklum.</h6>
                </div>

                <button type="button" class="btn btn-primary float-right mr-4">
                    <a href="{{url('createword-sddl/'.$accident->id)}}" style="color: #ffffff">Generate</a>
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

    const textAreas = document.querySelectorAll('.myTextArea');
    textAreas.forEach((textArea) => {
        textArea.value = textArea.value.replace(/^\s+|\s+$/g, '').replace(/ +(?= )/g, '');
    });
</script>


</html>
