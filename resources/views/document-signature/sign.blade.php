@php
    $_title = 'SIGN DOC TTE'
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">TANDA TANGAN ELEKTRONIK</h5>
        </div>

        <hr/>
        <br/>

         <!-- error alert -->
        @if ($errors->any())
        <div class="card-body">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="card-body">
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        </div>
        @endif

        <form action="{{route('document-signature.sign.process', ['accident_id' => $accidentId, 'document_id' => $documentId, 'document_category_id' => $documentCategoryId])}}" method="POST" enctype="multipart/form-data" id="signForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="accidentId" id="accidentId" value="{{$accidentId}}">
            <input type="hidden" name="passphrase" id="passphrase" value="">

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="accidentNumber">Nomor LP :</label>
                <div class="col-sm-10">
                    <input id="accidentNumber" type="text"
                        class="form-control @error('accidentNumber') is-invalid @enderror font-weight-bold" name="accidentNumber"
                        value="{{$document->accident->no_lp ?? ''}}" required placeholder="" readonly>
                    @error('accidentNumber')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="documentNumber">Nomor Dokumen : </label>
                <div class="col-sm-10">
                    <input id="documentNumber" type="text"
                        class="form-control @error('documentNumber') is-invalid @enderror font-weight-bold" name="documentNumber"
                        value="{{$document->document_number ?? ''}}" required placeholder="No Dokumen" readonly>

                    @error('documentNumber')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Tanggal Dokumen : </label>
                <div class="col-sm-10">
                    <input class="form-control" id="documentDate" name="documentDate"
                        autocomplete="off" value="{{$document->document_date ?? ''}}" readonly>

                    @error('documentDate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label" for="documentType">Jenis Dokumen :</label>
                <div class="col-sm-10">
                    <input id="documentType" type="text"
                        class="form-control @error('documentType') is-invalid @enderror font-weight-bold" name="documentType"
                        value="{{$document->documentCategory->name ?? ''}}" required placeholder="" readonly>
                    @error('documentType')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <br/>
            <hr/>

            <div class="card">
                @php
                    $attachmentFIleName = $document->attachment()->first()->name ?? NULL;
                @endphp

                <a target="_blank" href="{{ asset('documents/attachments/' . $attachmentFIleName) }}" class="btn btn-primary btn-lg" target="_blank">
                    <i class="bi bi-download"></i> Lihat Dokumen Yang Akan Ditanda Tangani
                </a>

                {{-- <a target="_blank" href="{{route($document->documentCategory->base_route . '.download', ['id' => $document->id,'accident_id' => $accidentId, 'document_category_id'=> $document->documentCategory->id])}}" class="btn btn-primary btn-lg">
                    <i class="bi bi-printer"></i>
                </a> --}}
            </div>

            <br/>
            <hr/>

            <div class="card">
                <div class="card-body">
                    <div class="box-header mb-4">
                        <h5 class="fw-bold text-blue-dark">IDENTITAS PENANDATANGAN</h5>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label">Nama :</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="signatoryName" name="signatoryName"
                                placeholder="Nama" value="{{$officer->full_name}}" readonly>

                            @error('signatoryName')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label">NRP :</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="signatoryRegisterNumber" name="signatoryRegisterNumber"
                                placeholder="NRP" value="{{$officer->register_number}}" readonly>

                            @error('signatoryRegisterNumber')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label">Pangkat :</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="signatoryRank" name="signatoryRank"
                                placeholder="Pangkat" value="{{$officer->rank->full_name ?? ''}}" readonly>

                            @error('signatoryRank')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group row mb-3 ms-0">
                        <label class="fw-bold col-sm-2 col-form-label">Jabatan :</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="signatoryPosition" name="signatoryPosition"
                                placeholder="Jabatan" value="{{$officer->position->name ?? ''}}" readonly>

                            @error('signatoryPosition')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-4 text-center">
                         <button type="button" class="btn btn-primary btn-lg text-center" id="signButton">
                            <i class="fa fa-edit text-white"></i> Tanda Tangan Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-6">
                    <div class="float-left">
                        <a href="{{route('document-signature.index')}}" class="btn btn-danger btn-lg">
                            <i class="bi bi-x-circle"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <img src="https://robinops.bareskrim.polri.go.id/Content/img/logo_bsre.png" alt="">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
<script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
<script src="{{ asset('libs/sweetalert/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">
    $('#signButton').on('click', function (e) {
        e.preventDefault();

        //sweetalert input passphrase
        Swal.fire({
            title: 'Masukkan Passphrase',
            input: 'text',
            showCancelButton: true,
            confirmButtonText: 'Tanda Tangan Sekarang',
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
                    url: "{{route('document-signature.sign.process', ['accident_id' => $document->accident->id])}}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        passphrase: passphrase,
                        document_id: "{{$document->id}}",
                        document_category_id: "{{$document->document_category_id}}",
                        
                    },
                    success: function (response) {
                        console.log(response.data);
                        var data = response.data;

                        Swal.close();

                        if(data.message == 'SUCCESS') {
                            return Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // redirect to document signature index
                                    window.location.href = "{{route('document-signature.index')}}";
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
@endpush
