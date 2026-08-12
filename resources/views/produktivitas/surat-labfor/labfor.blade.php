<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseLabfor" aria-expanded="false" aria-controls="collapseLabfor">
            8. Pemeriksaan Surat dan Laboratorium
        </button>
        <div class="progress8 progress-bar-none">
            <div id="kategori8" class="progress-bar bg-success kategori8" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori8 }}%</div>
        </div>
    </h2>
    <div id="collapseLabfor" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat permintaan bantuan pemeriksaan laboratorium forensik (Labfor)</span>
                {{-- <i class="bi bi-pencil-square" id="surat_labfor_1" name="surat_labfor_1"></i> --}}
                @if ($surat_permintaan_bantuan_labfor == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_1" name="surat_labfor_1"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-permintaan-bantuan-labfor/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-permintaan-bantuan-labfor/{{ $id }}" method="post">
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
                <span>Surat hasil pemeriksaan Labfor</span>
                {{-- <i class="bi bi-pencil-square" id="surat_labfor_2" name="surat_labfor_2"></i> --}}
                @if ($surat_hasil_pemeriksaan_labfor == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_2" name="surat_labfor_2"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-hasil-pemeriksaan-labfor/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-hasil-pemeriksaan-labfor/{{ $id }}" method="post">
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
                <span>Surat permintaan bantuan pemeriksaan identifikasi</span>
                {{-- <i class="bi bi-pencil-square" id="surat_labfor_3" name="surat_labfor_3"></i> --}}
                @if ($surat_permintaan_bantuan_identifikasi == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_3" name="surat_labfor_3"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-bantuan-identifikasi/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-bantuan-identifikasi/{{ $id }}" method="post">
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
                <span>Surat hasil pemeriksaan identifikasi</span>
                {{-- <i class="bi bi-pencil-square" id="surat_labfor_4" name="surat_labfor_4"></i> --}}
                @if ($surat_hasil_pemeriksaan_identifikasi == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_4" name="surat_labfor_4"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-pemeriksaan-identifikasi/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pemeriksaan-identifikasi/{{ $id }}" method="post">
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
                <span>Ketetapan Ijin Khusus Pemeriksaan Surat</span>
                @if ($ketetapan_khusus_surat == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_5" name="surat_labfor_5"></i>
                @else
                    <a target="_blank" href="/ketetapan-khusus-surat/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/ketetapan-khusus-surat/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Perintah Pemeriksaan Surat</span>
                @if ($perintah_pemeriksaan_surat == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_6" name="surat_labfor_6"></i>
                @else
                    <a target="_blank" href="/perintah-pemeriksaan-surat/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/perintah-pemeriksaan-surat/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita Acara Pemeriksaan Surat</span>
                @if ($berita_pemeriksaan_surat == null)
                    <i class="bi bi-pencil-square" id="surat_labfor_7" name="surat_labfor_7"></i>
                @else
                    <a target="_blank" href="/berita-pemeriksaan-surat/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-pemeriksaan-surat/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</div>

@include('produktivitas.surat-labfor.modal.modal')
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori8").css("width", "{{ $TotalKategori8 }}%")
            $(".progress8").hide();

            if ({{ $TotalKategori8 }} >= 40 && {{ $TotalKategori8 }} < 75) {
                $(".progress8").show();
                document.getElementById("kategori8").classList.add("bg-warning")
            } else if ({{ $TotalKategori8 }} > 0 && {{ $TotalKategori8 }} < 40) {
                $(".progress8").show();
                document.getElementById("kategori8").classList.add("bg-danger")
            } else if ({{ $TotalKategori8 }} >= 75 && {{ $TotalKategori8 }} <= 90) {
                $(".progress8").show();
                document.getElementById("kategori8").classList.add("bg-info")
            } else if ({{ $TotalKategori8 }} > 90) {
                $(".progress8").show();
                document.getElementById("kategori8").classList.add("bg-success")
            }
        })
    </script>
@endpush
