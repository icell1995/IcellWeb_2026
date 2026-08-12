<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseTugas" aria-expanded="true" aria-controls="collapseTugas">
            1. Surat Tugas
        </button>
    </h2>
    <div id="collapseTugas" class="accordion-collapse collapse show" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list" style="display: none !important">
                <span>Surat Perintah Penyelidikan</span>
                @if ($sprinlidik == null)
                    <a href="{{ route('investigation-warrant.create', ['accident_id' => $id]) }}">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @else
                    <div class="action">
                        <a href="{{ route('investigation-warrant.edit', ['accident_id' => $id]) }}"
                            class="">Edit</a>
                        <a target="_blank" href="{{ route('investigation-warrant.show', ['accident_id' => $id]) }}"
                            id="lihat_surat_penyelidikan">Lihat</a></span>
                        <form action="{{ route('investigation-warrant.delete', ['accident_id' => $id]) }}"
                            method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list" style="display: none !important">
                <span>Surat Perintah Penyidikan</span>
                @if ($sprindik == null)
                    <a href="{{ route('investigation-order-letter.create', ['accident_id' => $id]) }}" class="">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @else
                    <div class="action">
                        <a href="{{ route('investigation-order-letter.edit', ['accident_id' => $id]) }}"
                            class="">Edit</a>
                        <a target="_blank" href="{{ route('investigation-order-letter.show', ['accident_id' => $id]) }}"
                            id="lihat_surat_penyidikan">Lihat</a></span>
                        @if (empty($spdp) == true && empty($Sddl) == true && empty($lhgp) == true && empty($springas) == true)
                            <form action="{{ route('investigation-order-letter.delete', ['accident_id' => $id]) }}"
                                method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit">DELETE</button>
                            </form>
                        @endif
                    </div>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>
            <div class="item-list" style="display: none !important">
                <span>Surat Perintah Tugas</span>
                @if ($sprindik == null)
                    <i class="bi bi-pencil-square" id="notification_modal_sprindik"
                        name="notification_modal_sprindik"></i>
                @elseif($springas == null)
                    <i class="bi bi-pencil-square" id="perintah_tugas" name="perintah_tugas"></i>
                @else
                    {{-- <i class="" id="edit_tugas" name="edit_tugas">Edit</i> --}}
                    <div class="action">
                        <a href=# class="" id="edit_tugas" name="edit_tugas">Edit</a>
                        <a target="_blank"
                            href="{{ url('produktivitas/view-surat-tugas') }}?accident_id={{ $id }}"
                            id="lihat_surat_tugas">Lihat</a></span>
                        {{-- <img class="max-height-80 cetak01" data-bs-toggle="modal" data-backdrop="static" data-keyboard="false" title="Cetak" src="{{ asset('images/05.png') }}">
                        --}}
                        <form action="/springas/{{ $id }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit">DELETE</button>
                        </form>
                    </div>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>
            <div class="item-list" style="display: none !important">
                <span>Laporan Hasil Gelar Perkara</span>
                @if ($sprindik == null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSprindik">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @elseif ($lhgp == null)
                    <a href="{{ route('lhgp.create', ['accident_id' => $id]) }}" class="">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @else
                    <div class="action">
                        <a href="{{ route('lhgp.edit', ['accident_id' => $id]) }}" class="">Edit</a>
                        <a target="_blank" href="{{ route('lhgp.view', ['accident_id' => $id]) }}"
                            id="">Lihat</a>
                        @if (empty($Sddl) == true)
                            <form action="{{ route('lhgp.delete', ['accident_id' => $id]) }}" method="post">
                                @method('DELETE')
                                @csrf
                                <button type="submit">DELETE</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
            <div class="item-list" style="display: none !important">
                <span>Surat Ketetapan Tentang Penetapan Tersangka</span>
                @if ($suspectsName == null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSPDP">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @elseif ($Sddl == null)
                    <a href="{{ route('sddl.create', ['accident_id' => $id]) }}" class="">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @else
                    <div class="action">
                        <a href="{{ route('sddl.edit', ['accident_id' => $id]) }}" class="">Edit</a>
                        <a target="_blank" href="{{ route('sddl.view', ['accident_id' => $id]) }}"
                            id="">Lihat</a></span>
                        <form action="{{ route('sddl.delete', ['accident_id' => $id]) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list" style="display: none !important">
                <span>Surat Pemberitahuan Dimulainya Penyidikan</span>
                @if ($suspectsName == null)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#notificationModalSPDP">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @elseif ($spdp == null)
                    <a href="{{ route('spdp.create', ['accident_id' => $id]) }}" class="">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @else
                    <div class="action">
                        <a href="{{ route('spdp.edit', ['accident_id' => $id]) }}" class="">Edit</a>
                        <a target="_blank" href="{{ route('spdp.view', ['accident_id' => $id]) }}"
                            id="">Lihat</a></span>
                        <form action="{{ route('spdp.delete', ['accident_id' => $id]) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list">
                <span>SP2HP</span>
                <i class="bi bi-pencil-square" id="sp2hp" name="sp2hp"></i>
            </div>
            <div class="item-list">
                <span>Berita Acara Penangkapan TKP</span>
                @if ($BAPenangkapanTKP == null)
                    <i class="bi bi-pencil-square" id="BA_Penangkapan" name="BA_Penangkapan"></i>
                @else
                    <div class="action">
                        <a target="_blank" href="/BA_Penangkapan/{{ $id }}"
                            id="">Lihat</a></span>
                        <form action="/BA_Penangkapan/{{ $id }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="submit">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list">
                <span>Berita Acara Pemotretan</span>
                @if ($BAPemotretan == null)
                    <i class="bi bi-pencil-square" id="BA_Pemotretan" name="BA_Pemotretan"></i>
                @else
                    <div class="action">
                        <a target="_blank" href="/BA_Pemotretan/{{ $id }}" id=""
                            }>Lihat</a></span>
                        <form action="/BA_Pemotretan/{{ $id }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="submit">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list">
                <span>Berita Acara Pengambilan Darah</span>
                @if ($BAPengambilanDarah == null)
                    <i class="bi bi-pencil-square" id="BA_PengambilanDarah" name="BA_PengambilanDarah"></i>
                @else
                    <div class="action">
                        <a target="_blank" href="/BA-pengambilan-darah/{{ $id }}"
                            id="">Lihat</a></span>
                        <form action="/BA-pengambilan-darah/{{ $id }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="submit"
                                style="color: #007bff; border: none; background: none;
                                font-weight: bold; padding: 10px;">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list">
                <span>Laporan Hasil Penyidikan</span>
                @if ($laporan_hasil_penyelidikan == null)
                    <i class="bi bi-pencil-square" id="laporan_hasil_penyelidikan"
                        name="laporan_hasil_penyelidikan"></i>
                @else
                    <div class="action">
                        <a target="_blank" href="/laporan-hasil-penyelidikan/{{ $id }}"
                            id="">Lihat</a></span>
                        <form action="/laporan-hasil-penyelidikan/{{ $id }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="submit"
                                style="color: #007bff; border: none; background: none;
                                font-weight: bold; padding: 10px;">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="item-list">
                <span>Berita Acara Introgasi</span>
                @if ($BAIntrogasi == null)
                    <i class="bi bi-pencil-square" id="Berita_acara_introgasi" name="Berita_acara_introgasi"></i>
                @else
                    <div class="action">
                        <a target="_blank" href="/BA-introgasi/{{ $id }}" id="">Lihat</a></span>
                        <form action="/BA-introgasi/{{ $id }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="submit"
                                style="color: #007bff; border: none; background: none;
                                    font-weight: bold; padding: 10px;">DELETE</button>
                        </form>
                    </div>
                @endif
            </div>
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

{{-- start modal surat spdp  --}}
{{--@include('produktivitas.surat-tugas.modal.modal-spdp')--}}
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
                                                    <td><input type="text" name="officer_id_3[]"
                                                            class="form-control" required="">
                                                    </td>
                                                    <td><input type="text" name="officer_3[]"
                                                            class="form-control">
                                                    </td>
                                                    {{-- <div id="test_officer_list"></div></td><td> --}}
                                                    <td><a href="#" class="btn btn-danger test_remove"
                                                            id="test">delete</a></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori1").css("width", "{{ $TotalKategori1 }}%")
            $(".progress1").hide();
            if ({{ $TotalKategori1 }} >= 40 && {{ $TotalKategori1 }} <= 75) {
                $(".progress1").show();
                document.getElementById("kategori1").classList.add("bg-warning")
            } else if ({{ $TotalKategori1 }} > 0 && {{ $TotalKategori1 }} < 40) {
                $(".progress1").show();
                document.getElementById("kategori1").classList.add("bg-danger")
            } else if ({{ $TotalKategori1 }} >= 80 && {{ $TotalKategori1 }} <= 90) {
                $(".progress1").show();
                document.getElementById("kategori1").classList.add("bg-info")
            } else if ({{ $TotalKategori1 }} > 90) {
                $(".progress1").show();
                document.getElementById("kategori1").classList.add("bg-success")
            }
        })
    </script>
@endpush
