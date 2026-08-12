<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePenyegelan" aria-expanded="false" aria-controls="collapsePenyegelan">
            7. Penyegelan
        </button>
        <div class="progress7 progress-bar-none">
            <div id="kategori7" class="progress-bar bg-success kategori7" role="progressbar" style="width: "
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ $TotalKategori7 }}%</div>
        </div>
    </h2>
    <div id="collapsePenyegelan" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>
                    Surat permintaan persetujuan Presiden, Mendagri, Jaksa Agung,
                    Gubernur, Majelis Pengawas Daerah (Notaris) untuk melakukan pemanggilan/pemeriksaan
                    terhadap pejabat tertentu; (jika ada, sesuai perkaranya)
                </span>
                @if ($surat_persetujuan_penyegelan == null)
                    <i class="bi bi-pencil-square" id="surat_penyegelan_1" name="surat_penyegelan_1"></i>
                @else
                    <a target="_blank" href="/surat-persetujuan-penyegelan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-persetujuan-penyegelan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>
                    Surat perintah pembungkusan, penyegelan dan pelabelan barang bukti; (jika
                    ada)
                </span>
                @if ($surat_penyegelan == null)
                    <i class="bi bi-pencil-square" id="surat_penyegelan_2" name="surat_penyegelan_2"></i>
                @else
                    <a href=# class="" id="edit_surat_penyegelan_2" name="edit_surat_penyegelan_2">Edit</a>
                    <a target="_blank"
                        href="{{ url('produktivitas/view-surat-penyegelan') }}?accident_id={{ $id }}"
                        id="lihat_surat_penyegelan">Lihat</a></span>
                    <form action="/surat-penyegelan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                @endif
            </div>

            <div class="item-list">
                <span>
                    Berita acara pembungkusan, penyegelan dan pelabelan barang bukti
                </span>
                @if ($berita_acara_penyegelan == null)
                    <i class="bi bi-pencil-square" id="surat_penyegelan_3" name="surat_penyegelan_3"></i>
                @else
                    <a target="_blank" href="/berita-acara-penyegelan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-acara-penyegelan/{{ $id }}" method="post">
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

{{-- @include('produktivitas.surat-penyegelan.modal-bak') --}}
@include('produktivitas.surat-penyegelan.modal.modal')
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".progress7").hide();
            $(".kategori7").css("width", "{{ $TotalKategori7 }}%");

            if ({{ $TotalKategori7 }} >= 40 && {{ $TotalKategori7 }} < 75) {
                $(".progress7").show();
                document.getElementById("kategori7").classList.add("bg-warning")
            } else if ({{ $TotalKategori7 }} > 0 && {{ $TotalKategori7 }} < 40) {
                $(".progress7").show();
                document.getElementById("kategori7").classList.add("bg-danger")
            } else if ({{ $TotalKategori7 }} >= 75 && {{ $TotalKategori7 }} <= 90) {
                $(".progress7").show();
                document.getElementById("kategori7").classList.add("bg-info")
            } else if ({{ $TotalKategori7 }} > 90) {
                $(".progress7").show();
                document.getElementById("kategori7").classList.add("bg-success")
            }
        })
    </script>
@endpush
