<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cetak-Laporan</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex justify-content-center" style="background-color: #eeeeee">
        <div class="radius-card mt-4 w-60">
            <form target="_blank" method="POST">
                <div>
                    <input type="text" name="" id="" value="{{ $accident->id }}" hidden>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span>KEPOLISIAN NEGARA REPUBLIK INDONESIA</span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span>DAERAH {{ strtoupper($accident->polres->polda->full_name ?? '') }}</span>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span>RESOR {{ strtoupper($accident->polres->full_name ?? '') }}</span>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <span class="border-bot">{{ ucwords(strtolower($accident->polres->address ?? '')) }}</span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center mt-3">
                    <h3><span><img class="w-15" src="{{ asset('images/logo.png') }}"></span></h3>
                </div>

                <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3><span class="pb-1 border-bot font-weight-bolder">BERITA ACARA PENAHANAN</span></h3>
                    <h5 class="pt-1 pb-2 font-weight-bolder">(BA-HAN)</h5>
                </div>

                @csrf

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 p-0">
                        <div class="card">
                            <div class="card-body">
                                Pada hari ini <b>{{ $dayName ?? '-' }}</b> tanggal <b>{{ $documentDateText ?? '-' }}</b>, sekira pukul <b>{{ $time ?? '--:--' }} {{ $timezone ?? 'WIB' }}</b>, bertempat di <b>{{ $place ?? '-' }}</b>.
                                <br/><br/>
                                Saya:
                                <br/>
                                Nama : <b>{{ $officerLeaderName ?? '-' }}</b><br/>
                                Pangkat / NRP : <b>{{ $officerLeaderRank ?? '-' }} / {{ $officerLeaderNrp ?? '-' }}</b><br/>
                                Jabatan Selaku : <b>{{ $investigatorRole ?? 'Penyidik' }}</b><br/>
                                Dari kantor kepolisian tersebut di atas, bersama-sama dengan:
                                <br/><br/>
                                @if(!empty($companionOfficers) && count($companionOfficers) > 0)
                                    @foreach($companionOfficers as $idx => $officer)
                                        {{ $idx + 1 }}. Nama : {{ $officer['name'] }}<br/>
                                        &nbsp;&nbsp;&nbsp; Pangkat / NRP : {{ $officer['rank_nrp'] }}<br/>
                                        &nbsp;&nbsp;&nbsp; Jabatan : {{ $officer['position'] }}<br/>
                                    @endforeach
                                @else
                                    <i>(Tidak ada penyidik pendamping)</i>
                                @endif
                                <br/>
                                Masing-masing dari kantor polisi yang sama, berdasarkan <b>Surat Perintah Penahanan Nomor: {{ $spHanNumber ?? '-' }}</b> tanggal <b>{{ $spHanDate ?? '-' }}</b> atas nama <b>{{ $suspectName ?? '-' }}</b>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Tersangka</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                Nama Lengkap : <b>{{ $suspectName ?? '-' }}</b><br/>
                                NIK / No Identitas : <b>{{ $suspectIdentityNumber ?? '-' }}</b><br/>
                                Tempat / Tgl Lahir : <b>{{ $suspectBirthPlace ?? '-' }} / {{ $suspectBirthDate ?? '-' }}</b><br/>
                                Jenis Kelamin : <b>{{ $suspectGender ?? '-' }}</b><br/>
                                Pekerjaan : <b>{{ $suspectJob ?? '-' }}</b><br/>
                                Kewarganegaraan : <b>{{ $suspectNationality ?? 'INDONESIA' }}</b><br/>
                                Agama : <b>{{ $suspectReligion ?? '-' }}</b><br/>
                                Alamat : <b>{{ $suspectAddress ?? '-' }}</b><br/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Tindak Pidana & Pasal</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                Diduga telah melakukan tindak pidana <b>{{ $crimeDescription ?? '-' }}</b>, sebagaimana dimaksud dalam Pasal <b>{{ $crimeArticle ?? '-' }}</b>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Penempatan & Masa Penahanan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                Menempatkan tersangka di <b>{{ $detentionPlace ?? '-' }}</b> cabang <b>{{ $detentionBranch ?? '-' }}</b> selama 20 (dua puluh) hari terhitung mulai tanggal <b>{{ $startDate ?? '-' }}</b> sampai dengan tanggal <b>{{ $endDate ?? '-' }}</b>.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-3">
                        <div class="row">
                            <h5 class="col-lg-11 col-md-11 col-sm-11 col-11 font-weight-bold">Pelaksanaan & Kesehatan</h5>
                            <span class="col-lg-1 col-md-1 col-sm-1 col-1 p-0 font-weight-bold">:</span>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        <div class="card">
                            <div class="card-body">
                                Pelaksanaan : <b>{{ $task ?? '-' }}</b><br/>
                                Keadaan Kesehatan : <b>{{ $healthCondition ?? 'dalam keadaan sehat' }}</b>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                    <div class="col-6 text-center">
                        <br/>
                        <b>Tersangka</b>
                        <br/><br/><br/><br/>
                        <b>( {{ $suspectName ?? '-' }} )</b>
                    </div>
                    <div class="col-6 text-center">
                        {{ $accident->polres->polres_district ?? 'Tempat' }}, {{ $documentDateText ?? date('d-m-Y') }}<br/>
                        <b>{{ $investigatorRole ?? 'Penyidik' }}</b>
                        <br/><br/><br/><br/>
                        <u><b>{{ $officerLeaderName ?? '-' }}</b></u><br/>
                        <b>{{ $officerLeaderRank ?? '-' }} NRP. {{ $officerLeaderNrp ?? '-' }}</b>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4 me-3">
                    <button type="button" class="btn btn-secondary btn-lg me-2" id="print-cetak">
                        <i class="fa fa-print"></i> Cetak
                    </button>
                    <a href="{{ route('doc.berita-acara-penahanan-document.download', ['id' => $beritaAcaraPenahananDocumentId ?? $accident->id, 'accident_id' => $accident->id]) }}"
                        class="btn btn-primary btn-lg" style="color: #ffffff">
                        <i class="fa fa-download"></i> Download Word
                    </a>
                </div>

            </form>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.form-control').each(function(i,el){$(el).height(el.scrollHeight+5)});
        $(window).resize(function(){$('.form-control').css('overflow','auto');if(Math.round(window.devicePixelRatio*100)==100)$('.form-control').css('overflow','hidden')});

        $('.txtarea').each(function () {
            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow:hidden;');
        }).on('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        $('#print-cetak').on('click', function printAnyMaps() {
            window.print();
        });
    });
</script>
</html>
