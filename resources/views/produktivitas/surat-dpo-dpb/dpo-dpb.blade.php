<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseDPO/B" aria-expanded="false" aria-controls="collapseDPO/B">
            10. DPO atau DPB
        </button>
        <div class="progress10 progress-bar-none">
            <div id="kategori10" class="progress-bar bg-success kategori10" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori10 }}%</div>
        </div>
    </h2>
    <div id="collapseDPO/B" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat permintaan penangkapan tersangka yang masuk Daftar Pencarian Orang
                    (DPO);
                    (jika ada)</span>
                <i class="bi bi-pencil-square" id="dpo_1" name="dpo_1"></i>
            </div>

            <div class="item-list">
                <span>Surat pencabutan permintaan penangkapan tersangka yang masuk Daftar Pencarian
                    Orang (DPO); (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="test" name="test"></i> --}}
                @if ($surat_pencabutan_tersangka == null)
                    <i class="bi bi-pencil-square" id="dpo_2" name="dpo_2"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-pencabutan-tersangka/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pencabutan-tersangka/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat permintaan pencarian barang sesuai Daftar Pencarian Barang (DPB); (jika
                    ada)</span>
                <i class="bi bi-pencil-square" id="dpb_1" name="dpb_1"></i>
            </div>

            <div class="item-list">
                <span>Surat pencabutan permintaan pencarian barang sesuai Daftar Pencarian Barang
                    (DPB); (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="test" name="test"></i> --}}
                @if ($surat_pencabutan_barang == null)
                    <i class="bi bi-pencil-square" id="dpb_2" name="dpb_2"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-pencabutan-barang/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pencabutan-barang/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            {{-- <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat permintaan cegah dan tangkal (cekal); (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat pencabutan cekal; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat penitipan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat perintah penyisihan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Berita acara penyisihan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat perintah pelelangan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Berita acara pelelangan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat perintah pemusnahan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Berita acara pemusnahan barang bukti; (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Surat perintah penitipan barang bukti; dan (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-10">
                        <a class="item-action">Berita acara penitipan barang bukti. (jika ada)</a>
                    </div>
                    <div class="col-lg-2">
                        <i class="fa fa-pencil-square-o" id="test" name="test"></i>
                    </div>
                </div> --}}

        </div>
    </div>
</div>

@include('produktivitas.surat-dpo-dpb.modal.modal')
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori10").css("width", "{{ $TotalKategori10 }}%")
            $(".progress10").hide();
            if ({{ $TotalKategori10 }} >= 40 && {{ $TotalKategori10 }} < 75) {
                $(".progress10").show();
                document.getElementById("kategori10").classList.add("bg-warning")
            } else if ({{ $TotalKategori10 }} > 0 && {{ $TotalKategori10 }} < 40) {
                $(".progress10").show();
                document.getElementById("kategori10").classList.add("bg-danger")
                $(".kategori10").show();
            } else if ({{ $TotalKategori10 }} >= 75 && {{ $TotalKategori10 }} <= 90) {
                $(".progress10").show();
                document.getElementById("kategori10").classList.add("bg-info")
            } else if ({{ $TotalKategori10 }} > 90) {
                $(".progress10").show();
                document.getElementById("kategori10").classList.add("bg-success")
            }
        })
    </script>
@endpush
