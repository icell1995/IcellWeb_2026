<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePenghentian" aria-expanded="false" aria-controls="collapsePenghentian">
            11. Penghentian
        </button>
    </h2>
    <div id="collapsePenghentian" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat Perintah Penghentian Penyelidikan</span>
                @if ($surat_penghentian_penyelidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_1" name="surat_penghentian_1"></i>
                @else
                    <a target="_blank" href="/surat-perintah-penyelidikan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-perintah-penyelidikan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{--  <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat Pencabutan Penghentian Penyelidikan</span>
                @if ($surat_pencabutan_penyelidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_3" name="surat_penghentian_3"></i>
                @else
                    <a target="_blank" href="/surat-pencabutan-penyelidikan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pencabutan-penyelidikan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{--  <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <!-- <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Ketetapan Penghentian Penyelidikan</a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-2">
                    @if ($surat_ketetapan_penyelidikan == null)
                    <i class="fa fa-pencil-square-o" id="surat_penghentian_2" name="surat_penghentian_2"></i>
                    @else
                    <a target="_blank" href="/surat-ketetapan-penyelidikan/{{ $id }}" id="">Lihat</a></span>
                    <form action="/surat-ketetapan-penyelidikan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{--  <a href=# class="" id="delete" name="delete">Delete</a> --}}
                    @endif
                </div>
            </div> -->

            <div class="item-list">
                <span>Surat Perintah Penyelidikan Lanjutan</span>
                @if ($surat_penyelidikan_lanjutan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_4" name="surat_penghentian_4"></i>
                @else
                    <a target="_blank" href="/surat-penyelidikan-lanjutan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-penyelidikan-lanjutan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{--  <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>


            <div class="item-list">
                <span>Berita Acara Penghentian Penyelidikan</span>
                @if ($berita_penghentian_penyelidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_5" name="surat_penghentian_5"></i>
                @else
                    <a target="_blank" href="/berita-penghentian-penyelidikan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-penghentian-penyelidikan/{{ $id }}" method="post">
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
                <span>Persetujuan/Disposisi/Arahan Pejabat Yang Berwenang</span>
                @if ($persetujuan_pejabat_berwenang == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_6" name="surat_penghentian_6"></i>
                @else
                    <a target="_blank" href="/persetujuan-pejabat-berwenang/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/persetujuan-pejabat-berwenang/{{ $id }}" method="post">
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
                <span>Surat Perintah Penghentian Penyidikan</span>
                @if ($surat_penghentian_penyidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_7" name="surat_penghentian_7"></i>
                @else
                    <a href=# class="" id="edit_sp3" name="edit_sp3">Edit</a>
                    {{-- <a target="_blank" href="/surat-perintah-penyidikan/{{$id}}" id="">Lihat</a></span>
                        <form action="/surat-perintah-penyidikan/{{$id}}" method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                        </form> --}}
                    <a target="_blank" href="{{ url('produktivitas/view-sp3') }}?accident_id={{ $id }}"
                        id="lihat_sp3">Lihat</a></span>
                    <form action="/surat-perintah-penyidikan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            style="color: #007bff; border: none; background: none;
                            font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <!-- <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat Ketetapan Penghentian Penyidikan</a>
                </div>
                <div class="col-lg-2  col-md-2 col-sm-2 col-2">
                    @if ($surat_ketetapan_penyidikan == null)
                    <i class="fa fa-pencil-square-o" id="surat_penghentian_8" name="surat_penghentian_8"></i>
                    @else
                    <a target="_blank" href="/surat-ketetapan-penyidikan/{{ $id }}" id="">Lihat</a></span>
                    <form action="/surat-ketetapan-penyidikan/{{ $id }}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                    @endif
                </div>
            </div> -->

            <div class="item-list">
                <span>Putusan Pra Peradilan</span>
                @if ($putusan_pra_peradilan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_9" name="surat_penghentian_9"></i>
                @else
                    <a target="_blank" href="/putusan-pra-peradilan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/putusan-pra-peradilan/{{ $id }}" method="post">
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
                <span>Surat Ketetapan Pencabutan Penghentian Penyidikan</span>
                @if ($surat_pencabutan_penyidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_10" name="surat_penghentian_10"></i>
                @else
                    <a target="_blank" href="/surat-pencabutan-penyidikan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-pencabutan-penyidikan/{{ $id }}" method="post">
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
                <span>Surat Perintah Penyidikan Lanjutan</span>
                @if ($surat_penyidikan_lanjutan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_11" name="surat_penghentian_11"></i>
                @else
                    <a target="_blank" href="/surat-penyidikan-lanjutan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-penyidikan-lanjutan/{{ $id }}" method="post">
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
                <span>Berita Acara Penghentian Penyidikan</span>
                @if ($berita_penghentian_penyidikan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_12" name="surat_penghentian_12"></i>
                @else
                    <a target="_blank" href="/berita-penghentian-penyidikan/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/berita-penghentian-penyidikan/{{ $id }}" method="post">
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
                <span>Surat Pernyataan</span>
                @if ($surat_pernyataan == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_13" name="surat_penghentian_13"></i>
                @else
                    <a target="_blank" href="/surat-pernyataan/{{ $id }}" id="">Lihat</a></span>
                    <form action="/surat-pernyataan/{{ $id }}" method="post">
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
                <span>Surat Kesepakatan Perdamaian</span>
                @if ($surat_kesepakatan_perdamaian == null)
                    <i class="bi bi-pencil-square" id="surat_penghentian_14" name="surat_penghentian_14"></i>
                @else
                    <a target="_blank" href="/surat-kesepakatan-perdamaian/{{ $id }}"
                        id="">Lihat</a></span>
                    <form action="/surat-kesepakatan-perdamaian/{{ $id }}" method="post">
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

@include('produktivitas.surat-penghentian.modal.modal')
