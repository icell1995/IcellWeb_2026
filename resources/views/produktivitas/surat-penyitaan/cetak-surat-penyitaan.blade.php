<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak-Laporan</title>
    <link rel="shortcut icon" href="{{ asset('images/logo1.png') }}" />

    {{-- Bootsrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href={{ asset('css/app.css') }}>
    <link rel="stylesheet" href={{ asset('css/bootstrap.css') }}>
    <link rel="stylesheet" href={{ asset('css/style.css') }}>
</head>

<body>
    <div class="d-flex justify-content-center my-3">
        <div class="card p-3 w-60">
            <form target="_blank" method="POST">
                <div class="d-flex flex-column text-start border-bottom border-2 col-5">
                    <input type="text" name="" id="" value={{ $accident->id }} hidden>
                    <span class="">KEPOLISIAN NEGARA REPUBLIK INDONESIA</span>
                    <span class="">DAERAH {{ $accident->polres->polda->full_name ?? '' }}</span>
                    <span class="">RESOR {{ $accident->polres->full_name ?? '' }}</span>
                    <span
                        class="border-bot">{{ ucwords($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode) }}</span>
                </div>
                <span>"PRO JUSTITIA"</span>
                <div class="d-flex text-center align-items-center flex-column my-4">
                    <img class="w-10 m-2" src="{{ asset('images/logo.png') }}" alt="">
                    <h5 class="fw-bold mb-2">SURAT PERINTAH PENYITAAN</h5>
                    <h6 class="fw-bold">NOMOR : </h6>
                </div>

                @csrf
                <div class="d-flex col-12 mb-3">
                    <div class="d-flex col-3">
                        <h6 class="col-11 fw-bold">Pertimbangan</h6>
                        <span class="col-1 fw-bold">:</span>
                    </div>
                    <div class="card col-9 p-0">
                        <div class="card-body">
                            Bahwa untuk kepentingan penyidikan tindak pidana, penuntutan dan peradilan berupa penyitaan
                            terhadap benda-benda yang diduga ada kaitannya langsung dengan tindak pidana yang telah
                            terjadi, maka perlu dikeluarkan Surat Perintah ini.
                        </div>
                    </div>
                </div>
                <div class="d-flex col-12 mb-3">
                    <div class="d-flex col-3">
                        <h6 class="col-11 fw-bold">Dasar</h6>
                        <span class="col-1 fw-bold">:</span>
                    </div>
                    <div class="card col-9 p-0">
                        <div class="card-body">
                            1. Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d, Pasal 11, Pasal 12, Pasal 11,
                            Pasal 38, Pasal 39, Pasal 40, Pasal 44, Pasal 128, Pasal 129, Pasal 130, Pasal 131 KUHAP.
                            <br />
                            2. Undang-Undang Republik Indonesia Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik
                            Indonesia <br />
                            3. Udang-Undang Republik Indonesia Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan
                            Jalan <br />
                            4. Laporan Polisi Nomor : {{ $accident->no_lp }}, tanggal
                            {{ Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <h5 class="fw-bold">DIPERINTAHKAN</h5>
                </div>

                <div class="d-flex col-12 mb-3">
                    <div class="d-flex col-3">
                        <h6 class="col-11 fw-bold">Kepada</h6>
                        <span class="col-1 fw-bold">:</span>
                    </div>
                    <div class="card col-9 p-0">
                        <div class="card-body">
                            @php $xNo = 1; @endphp
                            @foreach ($surat_penyitaan as $officer)
                                {{ $xNo }}. Nama : {{ $officer->first_name }} {{ $officer->last_name }} <br>
                                Pangkat/NRP : {{ $officer->rank_short_name }} / {{ $officer->id }}
                                Jabatan :
                                @php $xNo++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="d-flex col-12 mb-3">
                    <div class="d-flex col-3">
                        <h6 class="col-11 fw-bold">Untuk</h6>
                        <span class="col-1 fw-bold">:</span>
                    </div>
                    <div class="card col-9 p-0">
                        <div class="card-body">
                            1. <br>
                            2. Melakukan pembungkusan dan atau penyegelan dan dilabel terhadap benda atau surat tulisan
                            lain yang disita.<br>
                            3. Setelah melaksanakan perintah ini pada kesempatan pertama harus membuat Berita Acara
                            Penyitaan dan atau Berita Acara Penyegelan dan atau Berita Acara Pembungkusan.<br>
                        </div>
                    </div>
                </div>
                <div class="d-flex col-12 mb-3">
                    <div class="d-flex col-3">
                        <h6 class="col-11 fw-bold">Selesai</h6>
                        <span class="col-1 fw-bold">:</span>
                    </div>
                    <div class="card col-9 p-0">
                        <div class="card-body">
                            -
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <a class="btn btn-primary" href="{{url('createword-surat-penyitaan/'.$accident->id)}}"><i class="bi bi-download"></i> Cetak Laporan</a>
                </div>
            </form>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.form-control').each(function(i, el) {
            $(el).height(el.scrollHeight + 5)
        });
        $(window).resize(function() {
            $('.form-control').css('overflow', 'auto');
            if (Math.round(window.devicePixelRatio * 100) == 100) $('.form-control').css('overflow',
                'hidden')
        })

        $('.txtarea').each(function() {
            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow:hidden;');
        }).on('input', function() {
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
