<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePenangkapan" aria-expanded="false" aria-controls="collapsePenangkapan">
            12. Penangkapan
        </button>
        {{-- <div class="progress12 progress-bar-none">
            <div id="kategori12" class="progress-bar bg-success kategori12" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori12 }}%</div>
        </div> --}}
    </h2>

    <div id="collapsePenangkapan" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            {{-- <div class="item-list">
                <span>Surat Ketetapan Tentang Penetapan Tersangka</span>
                @if ($surat_penetapan_tersangka == null)
                    <i class="bi bi-pencil-square" id="surat_penangkapan_1" name="surat_penangkapan_1"></i>
                @else
                    <a target="_blank" href="/surat-penetapan-tersangka/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-penetapan-tersangka/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div> --}}

            <div class="item-list">
                <span>Surat Perintah Penangkapan</span>
                @if ($surat_perintah_penangkapan == null)
                    <i class="bi bi-pencil-square" id="surat_penangkapan_2" name="surat_penangkapan_2"></i>
                @else
                    <a target="_blank" href="/surat-perintah-penangkapan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-perintah-penangkapan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Perintah Membawa Dan Menghadapkan</span>
                {{-- <i class="bi bi-pencil-square" id="surat_penangkapan_3" name="surat_penangkapan_3"></i> --}}
                @if ($surat_membawa_menghadapkan == null)
                    <i class="bi bi-pencil-square" id="surat_penangkapan_3" name="surat_penangkapan_3"></i>
                @else
                    <a target="_blank" href="/surat-membawa-menghadapkan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-membawa-menghadapkan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Surat Perintah Pelepasan Tersangka</span>
                @if ($surat_pelepasan_tersangka == null)
                    <i class="bi bi-pencil-square" id="surat_penangkapan_4" name="surat_penangkapan_4"></i>
                @else
                    <a target="_blank" href="/surat-pelepasan-tersangka/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pelepasan-tersangka/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita Acara Penangkapan</span>
                @if ($berita_acara_penangkapan == null)
                    <i class="bi bi-pencil-square" id="surat_penangkapan_5" name="surat_penangkapan_5"></i>
                @else
                    <a target="_blank" href="/berita-acara-penangkapan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-penangkapan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>Berita Acara Pelepasan Tersangka</span>
                @if ($berita_pelepasan_tersangka == null)
                    <div class="d-flex">
                        <i class="bi bi-pencil-square" id="surat_penangkapan_6" name="surat_penangkapan_6"></i>
                        <div class="form-check p-0 check" id="check_berita_acara_pelepasan_tersangka">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @else
                    <a target="_blank" href="/pelepasan-tersangka/{{ $id }}" id="">Lihat</a></span>
                    <form action="/pelepasan-tersangka/{{ $id }}" method="post">
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

@include('produktivitas.surat-penangkapan.modal.modal')

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori12").css("width", "{{ $TotalKategori12 }}%")
            $(".progress5").hide();
            if ({{ $TotalKategori12 }} >= 20 && {{ $TotalKategori12 }} <= 60) {
                $(".progress12").show();
                document.getElementById("kategori12").classList.add("bg-warning")
            } else if ({{ $TotalKategori12 }} > 0 && {{ $TotalKategori12 }} < 20) {
                $(".progress12").show();
                document.getElementById("kategori12").classList.add("bg-danger")
            } else if ({{ $TotalKategori12 }} >= 60 && {{ $TotalKategori12 }} <= 85) {
                $(".progress12").show();
                document.getElementById("kategori12").classList.add("bg-info")
            } else if ({{ $TotalKategori12 }} > 85) {
                $(".progress12").show();
                document.getElementById("kategori12").classList.add("bg-success")
            }
        })
    </script>
@endpush
