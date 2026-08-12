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

                <div class="col-lg-12 col-md-12 col-sm-12 col-12 pb-5">
                    <span class="border-bot">KEPOLISIAN NEGARA REPUBLIK INDONESIA</span>
                    <br>
                    <span class="mt-2">"PRO JUSTITIA"</span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                    <h3 style=""><span><img class="w-15" src="{{ asset('images/logo.png') }}"></span>
                    </h3>
                </div>

                <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3><span class="pb-1 border-bot font-weight-bolder">SURAT PERINTAH PENGHENTIAN PENYIDIKAN</span>
                    </h3>
                    <h4 class="pt-1 pb-2 font-weight-bolder">Nomor: {{ $no_sp3 }}</h4>
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
                        <textarea class="form-control inline-block txtarea" name="" disabled>Bahwa untuk kepentingan Penyidikan dan memberikan kepastian hukum terhadap Laporan Polisi yang telah dilakukan penyidikan, maka dipandangg perlu mengeluarkan Surat Perintah;</textarea>
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
                        <textarea id="myTextArea" class="form-control txtarea myTextArea" name="" disabled>
                            1. Pasal 7 ayat (1) huruf I dan Pasal 109 ayat(2), Undang-Undang Nomor 8 Tahun 1981 tentang Kitab Undang-Undang Hukum Acara Pidana;
                            2. Undang-undang Nomor 2 Tahun 2002, tentang Kepolisian Negara Republik Indonesia;
                            3. Undang-undang Nomor 22 Tahun 2009, tentang Lalu Lintas dan Angkutan Jalan;
                            4. Peraturan Kepolisian Negara Republik Indonesia Nomor 8 Tahun 2021 tentang Penanganan Tindak Pidana Berdasarkan Keadilan Restoratif;
                            5. Laporan Polisi Nomor: {{ $no_lp }}, tanggal {{ $accident_date }};
                            6. Surat Perintah Penyidikan Nomor : {{ $no_surat_perintah_penyidikan }}, tanggal {{ $tanggal_sp_dik }}
                            7. Surat Ketetapan Penghentian Penyidikan Nomor :
                        </textarea>
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
                        <textarea class="form-control inline-block txtarea myTextArea" name="" id="" disabled>
                            @foreach ($officer_springas as $springas)
                            Nama            : {{ $springas->first_name }} {{ $springas->last_name }}
                            Pangkat / NRP   : {{ $springas->rank_id }} / {{ $springas->id }}
                            Jabatan         : {{ $springas->position_short_name }}

                            @endforeach
                        </textarea>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-9 p-0">
                        {{-- <textarea class="form-control inline-block txtarea" name="" id=""
                            disabled>@foreach ($surat_perintah_tugas as $tugas){{$tugas->rank_id}} {{$tugas->first_name}} {{$tugas->last_name}} NRP {{$tugas->id}}&#13;&#10;{{$tugas->position_short_name}}&#13;&#10;@endforeach</textarea> --}}
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
                        <textarea id="" class="form-control inline-block txtarea myTextArea" name="" disabled>
                            1. Menghentikan Penyidikan terhadap Laporan Polisi Nomor: {{ $no_lp }}, tanggal {{ $accident_date }}, dengan alasan {{ $alasan }}, dan demi hukum karena keadilan restoratif;
                            2. Apabila dikemudian hari diperintahkan untuk melakukan penyidikan lanjutan berdasarkan putusan praperadilan yang berkekuatan hukum tetap dan Laporan Hasil Gelar perkara Khusus, maka penyidikan kasus ini dapat dilanjutkan kembali;
                            3. Melaksanakan perintah ini dengan sebaik-baiknya dan penuh rasa tanggung jawab;
                            4. Surat Perintah ini berlaku sejak tanggal dikeluarkan.
                        </textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-primary float-right mr-4">
                    <a href="{{url('createword-sp3/'.$accident->id)}}" style="color: #ffffff">Generate</a>
                </button>

            </form>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    const textAreas = document.querySelectorAll('.myTextArea');
    textAreas.forEach((textArea) => {
        textArea.value = textArea.value.replace(/^\s+|\s+$/g, '').replace(/ +(?= )/g, '');
    });
</script>

</html>
