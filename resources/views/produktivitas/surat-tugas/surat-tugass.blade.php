<div class="accordion-item">
    <div class="item">
        <a class="header-item">
            1. Surat Tugas
            <i class="fa fa-angle-right dropdown-side"></i>

            <div class="progress1 progress-bar-none" >
                <div id="kategori1" class="progress-bar kategori1" role="progressbar" style="width:" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{$TotalKategori1}}%</div>
            </div>
        </a>
        <div class="item-content">

            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Perintah Penyelidikan</a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-2">
                    @if ($sprinlidik==null)
                    <a href="{{route('investigation-warrant.create', ['accident_id' => $id])}}" class="">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @else
                    <a href="{{route('investigation-warrant.edit', ['accident_id' => $id])}}" class="">Edit</a>
                    <a target="_blank" href="{{route('investigation-warrant.show', ['accident_id' => $id])}}" id="lihat_surat_penyelidikan">Lihat</a></span>
                        <form action="{{route('investigation-warrant.delete', ['accident_id' => $id])}}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                        </form>
                        {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Perintah Penyidikan</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($sprindik==null)
                    <a href="{{route('investigation-order-letter.create', ['accident_id' => $id])}}" class="">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @else
                    <a href="{{route('investigation-order-letter.edit', ['accident_id' => $id])}}" class="">Edit</a>
                    <a target="_blank" href="{{route('investigation-order-letter.show', ['accident_id' => $id])}}" id="lihat_surat_penyidikan">Lihat</a></span>
                        @if(empty($spdp) == true && empty($Sddl) == true && empty($lhgp) == true && empty($springas) == true)
                            <form action="{{route('investigation-order-letter.delete', ['accident_id' => $id])}}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit" style="color: #007bff; border: none; background: none;
                                font-weight: bold; padding: 10px;">DELETE</button>
                            </form>
                        @endif
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action"> Surat Perintah Tugas</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($sprindik==null)
                    <i class="fa fa-pencil-square-o" id="notification_modal_sprindik" name="notification_modal_sprindik"></i>
                    @elseif($springas==null)
                    <i class="fa fa-pencil-square-o" id="perintah_tugas" name="perintah_tugas"></i>
                    @else
                    {{-- <i class="" id="edit_tugas" name="edit_tugas">Edit</i> --}}
                    <a href=# class="" id="edit_tugas" name="edit_tugas">Edit</a>
                    <a target="_blank" href="{{ url('produktivitas/view-surat-tugas')}}?accident_id={{ $id }}"
                        id="lihat_surat_tugas">Lihat</a></span>
                    {{-- <img class="max-height-80 cetak01" data-bs-toggle="modal" data-backdrop="static" data-keyboard="false" title="Cetak" src="{{ asset('images/05.png') }}">
                    --}}
                    <form action="/springas/{{$id}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                    @endif
                </div>
            </div>



            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Laporan Hasil Gelar Perkara ( Penetapan Tersangka )</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($sprindik==null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSprindik">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @elseif ($lhgp==null)
                    <a href="{{route('lhgp.create', ['accident_id' => $id])}}" class="">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @else
                    <a href="{{route('lhgp.edit', ['accident_id' => $id])}}" class="">Edit</a>
                    <a target="_blank" href="{{route('lhgp.view', ['accident_id' => $id])}}" id="">Lihat</a>
                        @if(empty($Sddl) == true)
                        <form action="{{route('lhgp.delete', ['accident_id' => $id])}}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Ketetapan Tentang Penetapan Tersangka</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($suspectsName==null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSPDP">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @elseif ($Sddl==null)
                    <a href="{{route('sddl.create', ['accident_id' => $id])}}" class="">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @else
                    <a href="{{route('sddl.edit', ['accident_id' => $id])}}" class="">Edit</a>
                    <a target="_blank" href="{{route('sddl.view', ['accident_id' => $id])}}" id="">Lihat</a></span>
                    <form action="{{route('sddl.delete', ['accident_id' => $id])}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Pemberitahuan Dimulainya Penyidikan</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($suspectsName==null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSPDP">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @elseif ($spdp==null)
                    <a href="{{route('spdp.create', ['accident_id' => $id])}}" class="">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @else
                    <a href="{{route('spdp.edit', ['accident_id' => $id])}}" class="">Edit</a>
                    <a target="_blank" href="{{route('spdp.view', ['accident_id' => $id])}}" id="">Lihat</a></span>
                    <form action="{{route('spdp.delete', ['accident_id' => $id])}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Pemberitahuan Dimulainya Penyidikan</a> --}}
                    {{-- @If($spdp==null)
                    <i class="fa fa-pencil-square-o" id="perintah_spdp" name="perintah_spdp" hidden></i>
                    @else
                    <button class="btn btn-primary btn-sm" >UPLOAD</button>
                    @endif --}}
                {{-- </div> --}}
                {{-- <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($suspectsName==null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSPDP">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    @elseif ($spdp==null)
                        <i class="fa fa-pencil-square-o" id="perintah_spdp" name="perintah_spdp"></i>
                    @else
                        <a href=# class="" id="edit_spdp" name="edit_spdp">Edit</a>
                        <a target="_blank" href="{{ url('produktivitas/view-surat-spdp')}}?accident_id={{ $id }}" id="lihat_spdp">Lihat</a></span>
                        <form action="/spdp/{{$id}}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                        </form> --}}
                        {{-- @if ($spdp_upload==null)
                            <a href="/SPDP-Upload" id="SPDP-Upload" name="SPDP-Upload" data-bs-toggle="modal" data-bs-target="#myModalSPDPUpload">UPLOAD</a>
                        @else
                            <a href="/SPDP-Upload" id="SPDP-Upload" name="SPDP-Upload" data-bs-toggle="modal" data-bs-target="#myModalSPDPUpload" hidden>UPLOAD</a>
                        @endif --}}

                    {{-- @endif
                </div>
            </div> --}}


            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">SP2HP</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    <i class="fa fa-pencil-square-o" id="sp2hp" name="sp2hp"></i>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Laporan Polisi</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if($LaporanPolisi==null)
                    <i class="fa fa-pencil-square-o" id="laporan_polisi" name="laporan_polisi"></i>
                    @else
                    <a target="_blank" href="/laporan_polisi/{{ $id }}" id="" }>Lihat</a></span>
                    <form action="/laporan_polisi/{{ $id }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Berita Acara Penangkapan TKP</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if($BAPenangkapanTKP==null)
                    <i class="fa fa-pencil-square-o" id="BA_Penangkapan" name="BA_Penangkapan"></i>
                    @else
                    <a target="_blank" href="/BA_Penangkapan/{{ $id }}" id="" }>Lihat</a></span>
                    <form action="/BA_Penangkapan/{{ $id }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Berita Acara Pemotretan</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if($BAPemotretan==null)
                    <i class="fa fa-pencil-square-o" id="BA_Pemotretan" name="BA_Pemotretan"></i>
                    @else
                    <a target="_blank" href="/BA_Pemotretan/{{ $id }}" id="" }>Lihat</a></span>
                    <form action="/BA_Pemotretan/{{ $id }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Berita Acara Pengambilan Darah</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    {{-- <i class="fa fa-pencil-square-o" id="BA_PengambilanDarah" name="BA_PengambilanDarah"></i> --}}
                    @if($BAPengambilanDarah==null)
                    <i class="fa fa-pencil-square-o" id="BA_PengambilanDarah" name="BA_PengambilanDarah"></i>
                    @else
                    <a target="_blank" href="/BA-pengambilan-darah/{{ $id }}" id="">Lihat</a></span>
                    <form action="/BA-pengambilan-darah/{{ $id }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Laporan Hasil Penyelidikan</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    {{-- <i class="fa fa-pencil-square-o" id="laporan_hasil_penyelidikan" name="laporan_hasil_penyelidikan"></i> --}}
                    @if($laporan_hasil_penyelidikan==null)
                    <i class="fa fa-pencil-square-o" id="laporan_hasil_penyelidikan" name="laporan_hasil_penyelidikan"></i>
                    @else
                    <a target="_blank" href="/laporan-hasil-penyelidikan/{{ $id }}" id="">Lihat</a></span>
                    <form action="/laporan-hasil-penyelidikan/{{ $id }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Berita Acara Introgasi</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    {{-- <i class="fa fa-pencil-square-o" id="Berita_acara_introgasi" name="Berita_acara_introgasi"></i> --}}
                    @if($BAIntrogasi==null)
                    <i class="fa fa-pencil-square-o" id="Berita_acara_introgasi" name="Berita_acara_introgasi"></i>
                    @else
                    <a target="_blank" href="/BA-introgasi/{{ $id }}" id="">Lihat</a></span>
                    <form action="/BA-introgasi/{{ $id }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- <a class="item-action"> Berkas
                @if ($surat_berkas==null)
                <i class="fa fa-pencil-square-o" id="perintah_tugas_4" name="perintah_tugas_4"></i>
                @else
                <a href=# class="" id="edit_spdp" name="edit_penyidikan">Edit</a>
                <a target="_blank" href="#" id="lihat_spdp">Lihat</a></span>
                <form action="/surat-spdp/{{$id}}" method="post">
            @method('DELETE')
            @csrf
            <button type="submit">Delete</button>
            </form>
            <i class="" id="edit" name="edit">Edit</i>
            <i class="" id="lihat" name="lihat">Lihat</i>
            <i class="" id="delete" name="delete">Delete</i>
            @endif
            </a> --}}
        </div>

    </div>
</div>

{{-- modal perintah tugas --}}
@include('produktivitas.surat-tugas.modal.modal-perintah-tugas')
{{-- end modal perintah tugas --}}

{{-- start modal perintah penyelidikan --}}
@include('produktivitas.surat-tugas.modal.modal-perintah-penyelidikan')
{{-- end modal perintah penyelidikan --}}


{{-- start modal surat penyidikan --}}
@include('produktivitas.surat-tugas.modal.modal-perintah-penyidikan')

{{-- <div data-backdrop="false" id="" name="myModal2" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
           <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="card">
                    <div class="card-body"> <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Silahkan masukan file surat perintah penyidikan berupa PDF</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
enctype="multipart/form-data">
@csrf
<div class="row">
    <div>
        <input id="accident_id" name="accident_id" type="text" value="{{$id}}" hidden>
    </div>
    <div class="col-md-10">
        <input type="file" name="file" class="form-control" required>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-success">Upload</button>
    </div>

    @foreach ($surat_perintah_tugas as $tugas )
    {{$tugas->officer_id}}
    @endforeach
</div>
</form>
</div>
</div>
</div>
</div>
<div class="modal-footer">
    <button type="button" data-backdrop="false" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-primary">delete</button>
</div>
</div>
</div>
</div>
</div>
</div> --}}
{{-- end modal surat penyidikan --}}

{{-- start modal surat spdp  --}}
@include('produktivitas.surat-tugas.modal.modal-spdp')
{{-- end modal surat spdp --}}

{{-- start modal surat sp2hp  --}}
@include('produktivitas.surat-tugas.modal.modal-sp2hp')
{{-- end modal surat sp2hp --}}

{{-- Start modal laporan polisi --}}
@include('produktivitas.surat-tugas.modal.modal-laporan-polisi')
{{-- end modal laporan polisi --}}

{{-- Start modal BA penangkapan tkp --}}
@include('produktivitas.surat-tugas.modal.modal-BA-penangkapan-tkp')
{{-- end modal BA penangkapan tkp --}}

{{-- Start modal BA Pemotretan --}}
@include('produktivitas.surat-tugas.modal.modal-BA-pemotretan')
{{-- end modal BA Pemotretan --}}

{{-- Start modal BA Pengambilan Darah --}}
@include('produktivitas.surat-tugas.modal.modal-BA-pengambilan-darah')
{{-- end modal  BA Pengambilan Darah --}}

{{-- Start modal laporan hasil penyelidikan --}}
@include('produktivitas.surat-tugas.modal.modal-hasil-penyelidikan')
{{-- end modal  BA Pengambilan Darah --}}

{{-- Start modal berita acara introgasi --}}
@include('produktivitas.surat-tugas.modal.modal-berita-acara-introgasi')
{{-- end modal  berita acara introgasi --}}

{{-- Start modal upload surat spdp --}}
@include('produktivitas.surat-tugas.modal.modal-spdp-upload')
{{-- end modal  upload surat spdp --}}

@include('produktivitas.surat-tugas.modal.modal-notification')

{{-- start modal surat berkas --}}
<div data-backdrop="false" id="myModal4" name="myModal4" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{--    <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Silahkan masukan file berkas berupa PDF</h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload.post') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        {{-- <div class="row">
                                                <div>
                                                    <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                        hidden>
                                </div>
                                <div class="col-md-10">
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Upload</button>
                                </div>
                            </div> --}}

                            {{-- <div class="panel panel-footer" >
                                                    <table class="table table-bordered" id="test_table">
                                                        <thead>
                                                            <tr>
                                                                <th>Officer Id</th>
                                                                <th>Officer</th>
                                                                <th><a href="#" class="addRow">add</a></th>
                                                            </tr>
                                                        </thead>


                                                                <tr>
                                                                <td><input type="text" name="officer_id_3[]" class="form-control" required=""></td>
                                                                <td><input type="text" name="officer_3[]" class="form-control"></td>
                                                                <div id="test_officer_list"></div></td><td>
                                                                <td><a href="#" class="btn btn-danger test_remove" id="test">delete</a></td>
                                                                </tr>
                                                            </tr>

                                                    </table>
                                                </div> --}}

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Officer Id</th>
                                        <th>Officer</th>
                                        <th><a href="#" class="addRow">add</a></th>
                                    </tr>
                                </table>
                                <table class="table table-bordered" id="test1">
                                    <tr>
                                        <td><input type="text" name="officer_id_3[]" class="form-control" required="">
                                        </td>
                                        <td><input type="text" name="officer_3[]" class="form-control"></td>
                                        {{-- <div id="test_officer_list"></div></td><td> --}}
                                        <td><a href="#" class="btn btn-danger test_remove" id="test">delete</a></td>
                                    </tr>
                                </table>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-backdrop="false" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary">delete</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@push('script')
<script type="text/javascript">

$(document).ready(function(){
    $(".kategori1").css("width","{{$TotalKategori1}}%")
    $(".progress1").hide();
   if({{$TotalKategori1}}>=40 && {{$TotalKategori1}}<=75){
    $(".progress1").show();
    document.getElementById("kategori1").classList.add("bg-warning")
   }else if({{$TotalKategori1}}>0 && {{$TotalKategori1}}<40){
    $(".progress1").show();
    document.getElementById("kategori1").classList.add("bg-danger")
   }else if({{$TotalKategori1}}>=80 && {{$TotalKategori1}}<=90){
    $(".progress1").show();
    document.getElementById("kategori1").classList.add("bg-info")
   }else if({{$TotalKategori1}}>90){
    $(".progress1").show();
    document.getElementById("kategori1").classList.add("bg-success")
   }
})
</script>
@endpush
{{-- end modal surat berkas --}}

{{-- <div class="modal fade bd-example-modal-lg" id="myModal" name="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
               <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row">
    <div>
        <input id="accident_id" name="accident_id" type="text" value="{{$id}}">
    </div>
    <div class="col-md-10">
        <input type="file" name="file" class="form-control" required>
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-success">Upload</button>
    </div>
</div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
</div>
</div>
</div>
</div> --}}

