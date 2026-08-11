<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Cetak SP2HP</title>

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
                    <h3><span class="pb-1 border-bot  font-weight-bolder">SURAT PEMBERITAHUAN PERKEMBANGAN HASIL PENYIDIKAN</span></h3>
                    <h4 class="pt-1 pb-2 font-weight-bolder">NOMOR : {{$sp2hpDocument->nomor_surat ?? '-'}}</h4>
                </div>

                <!-- Document content akan ditambahkan sesuai dengan struktur SP2HP -->

            </form>
        </div>
    </div>
</body>

</html>
