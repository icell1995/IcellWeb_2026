
<!Doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Success Form Data Collect</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h3>Kirim Data Sukses</h3>
                <p>Data Polres {{$polresName}}, Polda {{$poldaName}} 
                    Sudah Berhasil Dikirim Ke Master ICELL, Terima Kasih 
                </p>

                <p>
                    Ada Perubahan ? <a class="btn btn-primary" href="{{route('forms.collect', ['mode'=>'edit'])}}">Kembali Ke Edit</a>
                </p>
                
                <!-- Table Result -->
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><b>Polda</b></td>
                            <td>{{$poldaName}}</td>
                        </tr>
                        <tr>
                            <td><b>Polres</b></td>
                            <td>{{$polresName}}</td>
                        </tr>
                        <tr>
                            <td><b>Alamat Polres</b></td>
                            <td>{{$polresAddress}}</td>
                        </tr>
                        <tr>
                            <td><b>Provinsi</b></td>
                            <td>{{$polresProvince}}</td>
                        </tr>
                        <tr>
                            <td><b>Kabupaten/Kota</b></td>
                            <td>{{$polresRegency}}</td>
                        </tr>
                        <tr>
                            <td><b>Kecamatan</b></td>
                            <td>{{$polresDistrict}}</td>
                        </tr>
                        <tr>
                            <td><b>Desa/Kelurahan</b></td>
                            <td>{{$polresVillage}}</td>
                        </tr>
                        <tr>
                            <td><b>Kode Pos</b></td>
                            <td>{{$polresZipcode}}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td><b>Kejaksaan Negeri/Tinggi</b></td>
                            <td>{{$kejaksaan}}</td>
                        </tr>
                        <tr>
                            <td><b>Alamat Kejaksaan</b></td>
                            <td>{{$kejaksaanAddress}}</td>
                        </tr>
                        <tr>
                            <td><b>Provinsi</b></td>
                            <td>{{$kejaksaanProvince}}</td>
                        </tr>
                        <tr>
                            <td><b>Kabupaten/Kota</b></td>
                            <td>{{$kejaksaanRegency}}</td>
                        </tr>
                        <tr>
                            <td><b>Kecamatan</b></td>
                            <td>{{$kejaksaanDistrict}}</td>
                        </tr>
                        <tr>
                            <td><b>Desa/Kelurahan</b></td>
                            <td>{{$kejaksaanVillage}}</td>
                        </tr>

                        @if($kejaksaan2)
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td><b>Kejaksaan 2 Negeri/Tinggi</b></td>
                            <td>{{$kejaksaan2}}</td>
                        </tr>
                        <tr>
                            <td><b>Alamat Kejaksaan 2</b></td>
                            <td>{{$kejaksaan2Address}}</td>
                        </tr>
                        <tr>
                            <td><b>Provinsi</b></td>
                            <td>{{$kejaksaan2Province}}</td>
                        </tr>
                        <tr>
                            <td><b>Kabupaten/Kota</b></td>
                            <td>{{$kejaksaan2Regency}}</td>
                        </tr>
                        <tr>
                            <td><b>Kecamatan</b></td>
                            <td>{{$kejaksaan2District}}</td>
                        </tr>
                        <tr>
                            <td><b>Desa/Kelurahan</b></td>
                            <td>{{$kejaksaan2Village}}</td>
                        </tr>
                        @endif

                        @if($kejaksaan3)
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td><b>Kejaksaan 3 Negeri/Tinggi</b></td>
                            <td>{{$kejaksaan3}}</td>
                        </tr>
                        <tr>
                            <td><b>Alamat Kejaksaan 3</b></td>
                            <td>{{$kejaksaan3Address}}</td>
                        </tr>
                        <tr>
                            <td><b>Provinsi</b></td>
                            <td>{{$kejaksaan3Province}}</td>
                        </tr>
                        <tr>
                            <td><b>Kabupaten/Kota</b></td>
                            <td>{{$kejaksaan3Regency}}</td>
                        </tr>
                        <tr>
                            <td><b>Kecamatan</b></td>
                            <td>{{$kejaksaan3District}}</td>
                        </tr>
                        <tr>
                            <td><b>Desa/Kelurahan</b></td>
                            <td>{{$kejaksaan3Village}}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Button To Dashboard -->
                <a href="{{url('/home')}}" class="btn btn-primary">Kembali Ke Dashboard ICELL</a>
                {{-- <a href="{{url('/forms/collect')}}" class="btn btn-primary">Kembali Ke Forms</a> --}}
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.16.6/dist/umd/popper.min.js" integrity="sha384-GOGV7fZkR8uN7eKGj9Z4Dq3l2mzKwA1/h4yO7kM/LZWtbD0tK9XaTzT8A/pa/wY" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>