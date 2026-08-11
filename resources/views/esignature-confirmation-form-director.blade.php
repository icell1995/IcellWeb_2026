<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Konfirmasi Akun TTE</title>  
  </head>
  <body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh">
        <div class="card">
            <div class="card-header text-center">
                Konfirmasi Akun TTE
            </div>
            <div class="card-body">
                <h4 class="card-title text-center">Konfirmasi Akun TTE Yang Sudah Dibuat Di BSrE</h4>
                <p class="card-text mt-4">
                    
                </p>
               
               <div class="row">
                    <div class="col-12 text-center d-grid">
                        <button class="btn btn-primary btn-lg" id="passphraseConfirm" type="button"><i
                                class="bi bi-search"></i> Klik Untuk Konfirmasi TTE </button>
                    </div>
               </div>
                        
                <hr/>
                
                <h4 class="fw-bold text-blue-dark mb-4 mt-4">Belum Punya Passphrase Atau Akun BSrE? Silahkan Ikuti Alur Membuat Akun BSrE Untuk TTE Dibawah Ini</h4>

                <a href="{{ asset('images/AlurPendaftaranTTE.png') }}">
                    <img src="{{ asset('images/AlurPendaftaranTTE.png') }}" class="img-fluid" alt="Responsive image" >
                </a>

		<hr/>

                <h4 class="fw-bold text-blue-dark mb-4 mt-4">Prosedur Aktivasi Akun BSrE Untuk TTE (Jika Sudah Mendaftarakan Ke Robinops Bareskrim Di Alur No 2)</h4>

                <div class="embed-responsive embed-responsive-16by1">
                    <iframe style="top:0;left:0;width:100%;height:1024px;" class="embed-responsive-item" src="{{ asset('file/PPT_PENERBITAN_TTE_BSrE.pdf') }}" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
    <script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        $('#passphraseConfirm').on('click', function (e) {
            e.preventDefault();

            //sweetalert input passphrase
            Swal.fire({
                title: 'Masukkan Passphrase (Belum Punya? Silahkan Ikuti Alur Pendaftaran TTE)',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Konfirmasi Sekarang',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: (passphrase) => {
                    $('#passphrase').val(passphrase);

                    Swal.fire({
                        icon: 'info',
                        title: 'Mohon Menunggu...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    //ajax
                    $.ajax({
                        url: "{{route('esignature-confirmation.post')}}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            passphrase: passphrase,
                        },
                        success: function (response) {
                            console.log(response.data);
                            var data = response.data;

                            Swal.close();

                            if(data.message == 'SUCCESS') {
                                return Swal.fire({
                                    icon: 'success',
                                    title: 'Konfirmasi Berhasil',
                                    text: data.message,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Lanjut',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // redirect to document signature index
                                        window.location.href = "{{route('home')}}";
                                    }
                                });
                            } else {
                                return false;
                            }
                        },
                        error: function (xhr) {
                            Swal.close();

                            var response = JSON.parse(xhr.responseText);
                            
                            if(response.code == 400){
                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.data.message,
                                });
                            }else if(response.code == 500){
                                return Swal.fire({
                                    icon: 'error',
                                    title: 'Maaf, Terjadi Kesalahan',
                                    text: response.message,
                                });
                            }

                            return false;
                        }
                    });
                }
            });
        });
    </script>
  </body>
</html>
