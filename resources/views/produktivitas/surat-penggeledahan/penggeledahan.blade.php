<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePenggeledahan" aria-expanded="false" aria-controls="collapsePenggeledahan">
            5. Penggeledahan
        </button>
        <div class="progress5 progress-bar-none">
            <div id="kategori5" class="progress-bar bg-success kategori5" role="progressbar" style="width: %"
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori5 }}%</div>
        </div>
    </h2>
    <div id="collapsePenggeledahan" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat permintaan izin/izin khusus penggeledahan kepada ketua pengadilan; (jika
                    ada)</span>
                @if ($surat_izin_penggeledahan == null)
                    <div class="d-flex">
                        <i class="bi bi-pencil-square" id="surat_penggeledahan_1" name="surat_penggeledahan_1"></i>
                        <div class="form-check p-0 check" id="check_permintaan_perpanjangan_penahanan">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @else
                    {{-- <a href=# class="" id="surat_penggeledahan_1" name="surat_penggeledahan_1">Edit</a> --}}
                    <a target="_blank" href="/permintaan-izin-penggeledahan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/permintaan-izin-penggeledahan/{{ $id }}" method="post">
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
                <span>Surat perintah penggeledahan; (jika ada)</span>
                @if ($surat_perintah_penggeledahan == null)
                    <div class="d-flex">
                        <i class="bi bi-pencil-square" id="surat_penggeledahan_2" name="surat_penggeledahan_2"></i>
                        <div class="form-check p-0 check" id="check_permintaan_perpanjangan_penahanan">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @else
                    {{-- <a href=# class="" id="surat_penggeledahan_2" name="surat_penggeledahan_2">Edit</a> --}}
                    <a target="_blank" href="/perintah-penggeledahan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/perintah-penggeledahan/{{ $id }}" method="post">
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
                <span>Surat Perintah Penggeledahan Rumah dan Tempat Tertutup Lainnya; (jika ada)</span>
                @php
                    $docSpDahRumah = $surat_sp_dah_rumah_doc ?? \App\Models\Doc\SuratPerintahPenggeledahanRumahDocument\SuratPerintahPenggeledahanRumahDocument::where('accident_id', $id)->first();
                @endphp
                @if (!$docSpDahRumah && ($surat_persetujuan_penggeledahan == null || (is_countable($surat_persetujuan_penggeledahan) && count($surat_persetujuan_penggeledahan) == 0)))
                    <div class="d-flex align-items-center">
                        <a href="{{ route('doc.surat-perintah-penggeledahan-rumah-document.create', ['accident_id' => $id]) }}" style="text-decoration: none; color: inherit;">
                            <i class="bi bi-pencil-square" title="Buat Dokumen" style="cursor: pointer;"></i>
                        </a>
                        <div class="form-check p-0 check" id="check_surat_persetujuan_penggeledahan">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @elseif ($docSpDahRumah)
                    <div class="d-flex flex-column align-items-end justify-content-center" style="gap: 2px;">
                        <a href="{{ route('doc.surat-perintah-penggeledahan-rumah-document.download', ['id' => $docSpDahRumah->id, 'accident_id' => $id]) }}"
                            style="color: #000080; text-decoration: none; font-weight: bold; line-height: 1.2;">UNDUH</a>
                        <a href="{{ route('doc.surat-perintah-penggeledahan-rumah-document.edit', ['id' => $docSpDahRumah->id, 'accident_id' => $id]) }}"
                            style="color: #000080; text-decoration: none; font-weight: bold; line-height: 1.2;">UBAH</a>
                        <form action="{{ route('doc.surat-perintah-penggeledahan-rumah-document.delete', ['id' => $docSpDahRumah->id, 'accident_id' => $id]) }}"
                            method="post" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                            @method('DELETE')
                            @csrf
                            <button type="submit"
                                style="color: #000080; border: none; background: none; font-weight: bold; padding: 0; line-height: 1.2; cursor: pointer;">HAPUS</button>
                        </form>
                    </div>
                @else
                    {{-- <a href=# class="" id="surat_penggeledahan_3" name="surat_penggeledahan_3">Edit</a> --}}
                    <a target="_blank" href="/persetujuan-penggeledahan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/persetujuan-penggeledahan/{{ $id }}" method="post">
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
                <span>Berita Acara Penggeledahan Rumah / Badan / Pakaian / Angkutan; (jika ada)</span>
                @if ($berita_acara_penggeledahan == null)
                    <div class="d-flex">
                        <i class="bi bi-pencil-square" id="surat_penggeledahan_4" name="surat_penggeledahan_4"></i>
                        <div class="form-check p-0 check" id="check_berita_acara_penggeledahan">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @else
                    {{-- <a href=# class="" id="surat_penggeledahan_4" name="surat_penggeledahan_4">Edit</a> --}}
                    <a target="_blank" href="/berita-penggeledahan/{{ $id }}" id="">Lihat</a></span>
                    <form action="/berita-penggeledahan/{{ $id }}" method="post">
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

{{-- @include('produktivitas.surat-penggeledahan.modal') --}}
@include('produktivitas.surat-penggeledahan.modal.modal')
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".kategori5").css("width", "{{ $TotalKategori5 }}%")
            $(".progress5").hide();
            if ({{ $TotalKategori5 }} >= 40 && {{ $TotalKategori5 }} <= 70) {
                $(".progress5").show();
                document.getElementById("kategori5").classList.add("bg-warning")
            } else if ({{ $TotalKategori5 }} > 0 && {{ $TotalKategori5 }} < 40) {
                $(".progress5").show();
                document.getElementById("kategori5").classList.add("bg-danger")
            } else if ({{ $TotalKategori5 }} >= 71 && {{ $TotalKategori5 }} <= 90) {
                $(".progress5").show();
                document.getElementById("kategori5").classList.add("bg-info")
            } else if ({{ $TotalKategori5 }} > 90) {
                $(".progress5").show();
                document.getElementById("kategori5").classList.add("bg-success")
            }
        })
    </script>
@endpush
