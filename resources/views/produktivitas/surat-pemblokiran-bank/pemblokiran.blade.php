<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePemblokiran" aria-expanded="false" aria-controls="collapsePemblokiran">
            9. Pemblokiran Bank
        </button>
        <div class="progress9 progress-bar-none">
            <div id="kategori9" class="progress-bar bg-success kategori9" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori9 }}%</div>
        </div>
    </h2>
    <div id="collapsePemblokiran" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat permintaan blokir rekening bank; (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_1" name="surat_pemblokiran_bank_1"></i> --}}
                @if ($surat_blokir_rekening_bank == null)
                    <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_1"
                        name="surat_pemblokiran_bank_1"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-blokir-rekening-bank/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-blokir-rekening-bank/{{ $id }}" method="post">
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
                <span>Berita acara blokir rekening bank; (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_2" name="surat_pemblokiran_bank_2"></i> --}}
                @if ($berita_acara_blokir_rekening_bank == null)
                    <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_2"
                        name="surat_pemblokiran_bank_2"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/berita-acara-blokir/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-blokir/{{ $id }}" method="post">
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
                <span>Surat permintaan pembukaan blokir rekening bank; (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_3" name="surat_pemblokiran_bank_3"></i> --}}
                @if ($surat_pembukaan_blokir_rekening_bank == null)
                    <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_3"
                        name="surat_pemblokiran_bank_3"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/surat-pembukaan-blokir/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pembukaan-blokir/{{ $id }}" method="post">
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
                <span>Berita acara pembukaan blokir rekening bank; (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_4" name="surat_pemblokiran_bank_4"></i> --}}
                @if ($berita_acara_pembukaan_blokir_rekening_bank == null)
                    <i class="bi bi-pencil-square" id="surat_pemblokiran_bank_4"
                        name="surat_pemblokiran_bank_4"></i>
                @else
                    {{-- <a href=# class="" id="surat_penyitaan_1" name="surat_penyitaan_1">Edit</a> --}}
                    <a target="_blank" href="/berita-acara-pembukaan-blokir/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-pembukaan-blokir/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

        </div>
    </div>
</div>

@include('produktivitas.surat-pemblokiran-bank.modal.modal')
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori9").css("width", "{{ $TotalKategori9 }}%")
            $(".progress9").hide();
            if ({{ $TotalKategori9 }} >= 40 && {{ $TotalKategori9 }} < 75) {
                $(".progress9").show();
                document.getElementById("kategori9").classList.add("bg-warning");
            } else if ({{ $TotalKategori9 }} > 0 && {{ $TotalKategori9 }} < 40) {
                $(".progress9").show();
                document.getElementById("kategori9").classList.add("bg-danger")
            } else if ({{ $TotalKategori9 }} >= 75 && {{ $TotalKategori9 }} <= 90) {
                $(".progress9").show();
                document.getElementById("kategori9").classList.add("bg-info")
            } else if ({{ $TotalKategori9 }} > 90) {
                $(".progress9").show();
                document.getElementById("kategori9").classList.add("bg-success")
            }
        })
    </script>
@endpush
