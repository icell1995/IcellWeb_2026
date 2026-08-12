<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Masukan Evaluasi</title>  
  </head>
  <body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh">
        <div class="card text-center">
            <div class="card-header">
                Kuesioner Masukan
            </div>
            <div class="card-body">
                <h5 class="card-title">Masukan Tentang Kegiatan Penyelidikan/Penyidikan Dan ICELL</h5>
                <p class="card-text mt-4">
                    Ijin Mohon maaf, 
			Sejalan dengan upaya kami dalam meningkatkan kualitas dan pelayanan dari kegiatan penyidikan dan aplikasi ICELL, kami mengharapkan kesediaan Bapak/Ibu untuk dapat mengisi kuesioner masukan terkait aplikasi ICELL maupun kendala yang dialami saat proses Lidik/Sidik. 
		    <br>	
			Silakan klik tombol di bawah ini untuk memulai proses pengisian. 
                    <br>
                    Terima kasih atas perhatian dan partisipasi Bapak/Ibu.
                </p>
                <a href="{{route('evaluation-form-fill.redirect')}}" class="btn btn-primary">Mengisi Form Masukan</a>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
