<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseSaksi" aria-expanded="false" aria-controls="collapseSaksi">
            2. Surat Saksi
        </button>
        <div class="progress2 progress-bar-none">
            <div id="kategori2" class="progress-bar bg-success kategori2" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori2 }}%</div>
        </div>
    </h2>
    <div id="collapseSaksi" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat Panggilan Saksi</span>
                <i class="bi bi-pencil-square" id="surat_saksi_1" name="surat_saksi_1"></i>
            </div>

            <div class="item-list">
                <span>Surat Perintah Membawa Saksi</span>
                @if ($surat_perintah_membawa_saksi == null)
                    <i class="bi bi-pencil-square" id="surat_perintah_membawa_saksi"
                        name="surat_perintah_membawa_saksi"></i>
                @else
                    {{-- <a href=# class="" id="edit_surat_perintah_membawa_saksi"
                    name="edit_surat_perintah_membawa_saksi">Edit</a> --}}
                    <a target="_blank" href="/surat-perintah-membawa-saksi/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-perintah-membawa-saksi/{{ $id }}" method="post">
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
                <span>Berita acara membawa dan menghadapkan saksi (jika ada)</span>
                @if ($berita_acara_membawa_saksi == null)
                    <div class="d-flex">
                        <i class="bi bi-pencil-square" id="berita_acara_membawa_saksi"
                            name="berita_acara_membawa_saksi"></i>
                        <div class="form-check p-0 check" id="check_berita_acara_membawa_saksi">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @else
                    {{-- <a href=# class="" id="edit_berita_acara_membawa_saksi"
                    name="edit_berita_acara_membawa_saksi">Edit</a> --}}
                    <a target="_blank" href="/berita-acara-membawa-saksi/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-membawa-saksi/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href="/membawa-saksi/{{$id}}/destroy" class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara penyumpahan saksi/ahli</span>
                @if ($berita_acara_penyumpahan_saksi == null)
                    <i class="bi bi-pencil-square" id="berita_acara_penyumpahan_saksi"
                        name="berita_acara_penyumpahan_saksi"></i>
                @else
                    <a target="_blank" href="/berita-acara-penyumpahan-saksi/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-penyumpahan-saksi/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span class="item-action">Berita Acara Pemeriksaan Saksi</span>
                @if ($berita_pemeriksaan_saksi == null)
                    <i class="bi bi-pencil-square" id="surat_saksi_5" name="surat_saksi_5"></i>
                @else
                    <a target="_blank" href="/berita-pemeriksaan-saksi/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-pemeriksaan-saksi/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span class="item-action">Berita Acara Pemeriksaan Ahli</span>
                @php
                    $bapAhliDoc = $berita_acara_pemeriksaan_ahli_doc ?? \App\Models\Doc\BeritaAcaraPemeriksaanAhliDocument\BeritaAcaraPemeriksaanAhliDocument::where('accident_id', $id)->first();
                @endphp
                @if (empty($bapAhliDoc))
                    <div class="d-inline-flex align-items-center">
                        <a href="{{ route('doc.berita-acara-pemeriksaan-ahli-document.create', ['accident_id' => $id]) }}" title="Tambah Berita Acara Pemeriksaan Ahli">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        @if ($berita_pemeriksaan_ahli != null)
                            <a target="_blank" href="/berita-pemeriksaan-ahli/{{ $id }}" class="btn btn-sm btn-link text-primary fw-bold text-decoration-none ms-2">Lihat File Upload</a>
                            <form action="/berita-pemeriksaan-ahli/{{ $id }}" method="post" class="d-inline m-0">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-danger fw-bold text-decoration-none border-0 bg-transparent p-0 ms-1">HAPUS</button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="d-flex flex-column align-items-end" style="line-height: 1.15;">
                        <a href="{{ route('doc.berita-acara-pemeriksaan-ahli.download-word', ['accident_id' => $id]) }}" style="color: #000080; font-weight: bold; text-decoration: none; line-height: 1.15;" target="_blank">UNDUH</a>
                        <a href="{{ route('doc.berita-acara-pemeriksaan-ahli-document.edit', ['accident_id' => $id]) }}" style="color: #000080; font-weight: bold; text-decoration: none; line-height: 1.15;">UBAH</a>
                        <form action="{{ route('doc.berita-acara-pemeriksaan-ahli-document.destroy', ['accident_id' => $id]) }}" method="post" class="m-0 p-0" style="line-height: 1.15;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                            @method('DELETE')
                            @csrf
                            <button type="submit" style="color: #000080; border: none; background: none; font-weight: bold; text-decoration: none; padding: 0; margin: 0; line-height: 1.15;">HAPUS</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Start Modal --}}
@include('produktivitas.surat-saksi.modal.modal')

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori2").css("width", "{{ $TotalKategori2 }}%")
            $(".progress2").hide();
            if ({{ $TotalKategori2 }} >= 20 && {{ $TotalKategori2 }} <= 60) {
                $(".progress2").show();
                document.getElementById("kategori2").classList.add("bg-warning")
            } else if ({{ $TotalKategori2 }} > 0 && {{ $TotalKategori2 }} < 20) {
                $(".progress2").show();
                document.getElementById("kategori2").classList.add("bg-danger")
            } else if ({{ $TotalKategori2 }} >= 60 && {{ $TotalKategori2 }} <= 85) {
                $(".progress2").show();
                document.getElementById("kategori2").classList.add("bg-info")
            } else if ({{ $TotalKategori2 }} > 85) {
                $(".progress2").show();
                document.getElementById("kategori2").classList.add("bg-success")
            }
            //     $(".kategori2").css("width","{{ $TotalKategori2 }}%")
            //     $(".progress2").hide();
            //    if({{ $TotalKategori2 }}>0 && {{ $TotalKategori2 }}<20){
            //     $(".progress2").show();
            //     document.getElementById("kategori2").classList.add("bg-danger")
            //    }else if({{ $TotalKategori2 }}>=20 && {{ $TotalKategori2 }}<=60){
            //     $(".progress2").show();
            //     document.getElementById("kategori2").classList.add("bg-warning")
            //    }else if({{ $TotalKategori2 }}>=60 && {{ $TotalKategori2 }}<=85){
            //     $(".progress2").show();
            //     document.getElementById("kategori2").classList.add("bg-info")
            //    }else if({{ $TotalKategori2 }}>85){
            //     $(".progress2").show();
            //     document.getElementById("kategori2").classList.add("bg-success")
            //    }
        })
    </script>
@endpush
