<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePenyitaan" aria-expanded="false" aria-controls="collapsePenyitaan">
            6. Penyitaan
        </button>
        <div class="progress6 progress-bar-none">
            <div id="kategori6" class="progress-bar bg-success kategori6" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori6 }}%</div>
        </div>
    </h2>
    <div id="collapsePenyitaan" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat permintaan izin/izin khusus penyitaan kepada ketua pengadilan</span>
                @if ($surat_izin_penyitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_1" name="surat_penyitaan_1"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a>
                    <a target="_blank" href="/surat-izin-penyitaan/{{ $id }}" id="">Lihat</a></span>
                    <form action="/surat-izin-penyitaan/{{ $id }}" method="post">
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
                <span>Surat permintaan persetujuan penyitaan kepada ketua pengadilan</span>
                @if ($surat_persetujuan_penyitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_2" name="surat_penyitaan_2"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_2" name="surat_penyitaan_2">Edit</a>
                    <a target="_blank" href="/surat-persetujuan-penyitaan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-persetujuan-penyitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Daftar Barang Bukti </span>
                <i class="bi bi-pencil-square" id="surat_penyitaan_3" name="surat_penyitaan_3"></i>
            </div>

            <div class="item-list">
                <span>Surat perintah penyitaan</span>
                @if ($surat_penyitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_4" name="surat_penyitaan_4"></i>
                @else
                    <a href=# class="" id="edit_surat_penyitaan_4" name="edit_surat_penyitaan_4">Edit</a>
                    <a target="_blank"
                        href="{{ url('produktivitas/view-surat-penyitaan') }}?accident_id={{ $id }}"
                        id="lihat_surat_penyitaan">Lihat</a></span>
                    <form action="/surat-penyitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara penyitaan</span>
                @if ($berita_acara_penyitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_5" name="surat_penyitaan_5"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_5" name="surat_penyitaan_5">Edit</a>
                    <a target="_blank" href="/berita-acara-penyitaan/{{ $id }}"
                        id="lihat_surat_penyitaan">Lihat</a></span>
                    <form action="/berita-acara-penyitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat pengiriman berkas perkara</span>
                @if ($surat_pengiriman_berkas_perkara == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_6" name="surat_penyitaan_6"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_6" name="surat_penyitaan_6">Edit</a>
                    <a target="_blank" href="/surat-pengiriman-berkas-perkara/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pengiriman-berkas-perkara/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Tanda terima berkas perkara</span>
                @if ($tanda_terima_berkas_perkara == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_7" name="surat_penyitaan_7"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_7" name="surat_penyitaan_7">Edit</a>
                    <a target="_blank" href="/tanda-terima-berkas-perkara/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/tanda-terima-berkas-perkara/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat pengiriman tersangka dan barang bukti</span>
                @if ($surat_pengiriman_tersangka_barang_bukti == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_8" name="surat_penyitaan_8"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_8" name="surat_penyitaan_8">Edit</a>
                    <a target="_blank" href="/pengiriman-barang-bukti/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/pengiriman-barang-bukti/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara serah terima tersangka dan barang bukti</span>
                @if ($berita_acara_serah_terima_tersangka == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_9" name="surat_penyitaan_9"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_9" name="surat_penyitaan_9">Edit</a>
                    <a target="_blank" href="/berita-acara-terima-tersangka/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-terima-tersangka/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat bantuan penyelidikan</span>
                @if ($surat_bantuan_penyelidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_10" name="surat_penyitaan_10"></i>
                @else
                    <a href=# class="" id="surat_penyitaan_10" name="surat_penyitaan_10">Edit</a>
                    <a target="_blank" href="/surat-bantuan-penyelidikan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-bantuan-penyelidikan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Perintah Penitipan / Titip Rawat Barang Bukti</span>
                @if ($surat_pentitipan_barang == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_11" name="surat_penyitaan_11"></i>
                @else
                    <a target="_blank" href="/surat-pentitipan-barang/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pentitipan-barang/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Perintah Pengembalian Benda Sitaan</span>
                @if ($surat_pengembalian_sitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_12" name="surat_penyitaan_12"></i>
                @else
                    <a target="_blank" href="/surat-pengembalian-sitaan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pengembalian-sitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita Acara Penitipan / Titip Rawat Barang Bukti</span>
                @if ($berita_penitipan_barang == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_13" name="surat_penyitaan_13"></i>
                @else
                    <a target="_blank" href="/berita-penitipan-barang/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-penitipan-barang/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita Acara Pengembalian Benda Sitaan</span>
                @if ($berita_pengembalian_sitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_14" name="surat_penyitaan_14"></i>
                @else
                    <a target="_blank" href="/berita-pengembalian-sitaan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-pengembalian-sitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Ketetapan Ijin Penyitaan</span>
                @if ($ketetapan_ijin_penyitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_15" name="surat_penyitaan_15"></i>
                @else
                    <a target="_blank" href="/ketetapan-ijin-penyitaan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/ketetapan-ijin-penyitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Ketetapan Persetujuan Penyitaan</span>
                @if ($ketetapan_persetujuan_penyitaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_16" name="surat_penyitaan_16"></i>
                @else
                    <a target="_blank" href="/ketetapan-persetujuan-penyitaan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/ketetapan-persetujuan-penyitaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Tanda Penerimaan</span>
                @if ($surat_tanda_penerimaan == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_17" name="surat_penyitaan_17"></i>
                @else
                    <a target="_blank" href="/surat-tanda-penerimaan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-tanda-penerimaan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Pengantar</span>
                @if ($surat_pengantar == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_18" name="surat_penyitaan_18"></i>
                @else
                    <a target="_blank" href="/surat-pengantar/{{ $id }}" id="">Lihat</a></span>
                    <form action="/surat-pengantar/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita Acara Penyerahan Berkas Perkara</span>
                @if ($berita_penyerahan_berkas == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_19" name="surat_penyitaan_19"></i>
                @else
                    <a target="_blank" href="/berita-penyerahan-berkas/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-penyerahan-berkas/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            {{-- <div class="item-list">
                <span>Laporan Hasil Gelar Perkara</span>
                @if ($laporan_gelar_perkara == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_20" name="surat_penyitaan_20"></i>
                @else
                    <a target="_blank" href="/laporan-gelar-perkara/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/laporan-gelar-perkara/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div> --}}

            <div class="item-list">
                <span>Laporan Hasil Gelar Perkara Khusus</span>
                @if ($laporan_perkara_khusus == null)
                    <i class="bi bi-pencil-square" id="surat_penyitaan_21" name="surat_penyitaan_21"></i>
                @else
                    <a target="_blank" href="/laporan-perkara-khusus/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/laporan-perkara-khusus/{{ $id }}" method="post">
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

{{-- @include('produktivitas.surat-penyitaan.modal-bak') --}}
@include('produktivitas.surat-penyitaan.modal.modal')
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori6").css("width", "{{ $TotalKategori6 }}%");
            $(".progress6").hide();
            if ({{ $TotalKategori6 }} >= 40 && {{ $TotalKategori6 }} <= 70) {
                $(".progress6").show();
                document.getElementById("kategori6").classList.add("bg-warning")
            } else if ({{ $TotalKategori6 }} > 0 && {{ $TotalKategori6 }} < 40) {
                $(".progress6").show();
                document.getElementById("kategori6").classList.add("bg-danger")
            } else if ({{ $TotalKategori6 }} >= 80 && {{ $TotalKategori6 }} <= 90) {
                $(".progress6").show();
                document.getElementById("kategori6").classList.add("bg-info")
            } else if ({{ $TotalKategori6 }} > 90) {
                $(".progress6").show();
                document.getElementById("kategori6").classList.add("bg-success")
            }
        })
    </script>
@endpush
