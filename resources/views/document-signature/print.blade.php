@extends('layouts.app')

@section('content')
<div class="loaderbg" style="display:none"></div>

<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="box">
        <form action="{{ route('signature.print') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h3 class="font-weight-bold">TANDA TANGAN ELEKTRONIK</h3>
                <div class="row">
                    <div class="col-2">
                        <label>No Laporan Polisi</label>
                    </div>
                    <div class="input-group col-6" style="padding: 0px">
                        <input id="no_lp" type="text" class="form-control font-weight-bold mb-3" name="no_lp"
                        value="{{$esign->no_lp}}" readonly autofocus>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <label>No Dokumen</label>
                    </div>
                    <div class="input-group col-6" style="padding: 0px">
                        <input id="no_dokumen" type="text" class="form-control font-weight-bold mb-3" name="no_dokumen"
                        value="{{$esign->no_spdp}}" readonly autofocus>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <label>Tanggal Dokumen</label>
                    </div>
                    <div class="input-group col-6" style="padding: 0px">
                        <input id="tgl_dokumen" type="text" class="form-control font-weight-bold mb-3" name="tgl_dokumen"
                        value="{{ \Carbon\Carbon::parse($esign->spdp_date)->translatedFormat('d F Y')}} " readonly autofocus>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <label>Jenis Dokumen</label>
                    </div>
                    <div class="input-group col-6" style="padding: 0px">
                        <input id="jns_dokumen" type="text" class="form-control font-weight-bold mb-3" name="jns_dokumen"
                        value="{{$esign->category_spdp}}" readonly autofocus>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="box">
        <form action="{{ route('signature.print') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="col-4">
                    <label>IDENTITAS PENANDATANGAN</label>
                </div>
                <div class="row">
                    <div class="col-4">
                        <label>Nama</label>
                        <input id="name_officer" type="text" class="form-control font-weight-bold mb-3" name="name_officer"
                            value="{{$officer->first_title . ' '. $officer->first_name . ' '. $officer->last_name . ' '. $officer->last_title }}" readonly autofocus>
                    </div>
                    <div class="col-4">
                        <label>Pangkat</label>
                        <input id="jns_dokumen" type="text" class="form-control font-weight-bold mb-3" name="jns_dokumen"
                            value="{{ $officer->rank_id }}" readonly autofocus>
                    </div>
                    <div class="col-4">
                        <label>Jabatan</label>
                        <input id="jns_dokumen" type="text" class="form-control font-weight-bold mb-3" name="jns_dokumen"
                            value="{{ $officer->position_id }}" readonly autofocus>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <div class="mb-3">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#confirmationModal" type="button">Tanda Tangani Dokumen</button>
                    </div>
                </div>
                <div class="text-center overflow-auto">
                    <iframe src="{{ asset('file/tugas/SPDP-Upload/'. $fileName) }}" width="1480px" height="1180px">
                    </iframe>
                </div>
            </div>
        </form>
    </div>
    <!-- Modal -->
<div class="modal" id="confirmationModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Konfirmasi Tanda Tangan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menandatangani dokumen ini?</p>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#signatureModal">Ya</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal untuk mengisikan kode tanda tangan -->
  <div class="modal" id="signatureModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Isikan Kode Tanda Tangan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-3" id="identity_number" placeholder="Nomor NIK" value="{{$officer->identity_number}}" readonly>
          <input type="text" class="form-control" id="signatureCode" placeholder="Kode Tanda Tangan">
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="validateSignature()">Tanda Tangani</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </div>
  </div>
@endsection

<!-- JavaScript -->
<script>
    function validateSignature() {
      // Mengambil nilai inputan nomor NIK dan kode tanda tangan
      let identityType = document.getElementById("identity_type").value;
      let identityNumber = document.getElementById("identity_number").value;
      let signatureCode = document.getElementById("signatureCode").value;

      // Lakukan validasi nomor NIK dengan kode tanda tangan
      if (identityType === "NIK" && identityNumber === signatureCode) {
        // Nomor NIK sudah sesuai dengan kode tanda tangan, arahkan pengguna ke halaman penandatanganan dokumen
        window.location.href = "halaman-penandatanganan-dokumen.html?signatureCode=" + signatureCode;
      } else {
        // Nomor NIK tidak sesuai dengan kode tanda tangan, tampilkan pesan error
        alert("Nomor NIK tidak sesuai dengan kode tanda tangan.");
      }
    }
  </script>
