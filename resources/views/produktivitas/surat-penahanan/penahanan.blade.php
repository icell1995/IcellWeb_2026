<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapsePenahanan" aria-expanded="false" aria-controls="collapsePenahanan">
            4. Penahanan
        </button>
        <div class="progress4 progress-bar-none">
            <div id="kategori4" class="progress-bar bg-success kategori4" role="progressbar" style="width: " aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{$TotalKategori4}}%</div>
        </div>
    </h2>
    <div id="collapsePenahanan" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list">
                <span>Surat perintah penahanan</span>
                @if ($surat_perintah_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_1" name="surat_penahanan_1"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_1" name="surat_penahanan_1">Edit</a> --}}
                <a target="_blank" href="/surat-perintah-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/surat-perintah-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara penahanan</span>
                @if ($berita_acara_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_2" name="surat_penahanan_2"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_2" name="surat_penahanan_2">Edit</a> --}}
                <a target="_blank" href="/berita-acara-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-acara-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat permintaan perpanjangan penahanan kepada jaksa penuntut umum JPU dan hakim (jika ada)</span>
                {{-- <i class="bi bi-pencil-square" id="surat_penahanan_3" name="surat_penahanan_3"></i> --}}
                @if ($permintaan_perpanjangan_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_3" name="surat_penahanan_3"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_2" name="surat_penahanan_2">Edit</a> --}}
                <a target="_blank" href="/perpanjangan-penahanan-hakim/{{$id}}" id="">Lihat</a></span>
                <form action="/perpanjangan-penahanan-hakim/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat perintah perpanjangan penahanan (jika ada)</span>
                @if ($surat_perpanjangan_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_4" name="surat_penahanan_4"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_4" name="surat_penahanan_4">Edit</a> --}}
                <a target="_blank" href="/surat-perpanjangan-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/surat-perpanjangan-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara pengeluaran penahanan (jika ada)</span>
                @if ($berita_pengeluaran_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_5" name="surat_penahanan_5"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_4" name="surat_penahanan_4">Edit</a> --}}
                <a target="_blank" href="/berita-pengeluaran-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-pengeluaran-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat pembatalan penahanan (jika ada)</span>
                @if ($surat_pembatalan_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_6" name="surat_penahanan_6"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_6" name="surat_penahanan_6">Edit</a> --}}
                <a target="_blank" href="/surat-pembatalan-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/surat-pembatalan-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat perintah pencabutan pembantalan penahanan (jika ada)</span>
                @if ($surat_pencabutan_pembatalan_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_7" name="surat_penahanan_7"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_7" name="surat_penahanan_7">Edit</a> --}}
                <a target="_blank" href="/pencabutan-pembatalan-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/pencabutan-pembatalan-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara pencabutan pembantalan penahanan (jika ada)</span>
                @if ($berita_pencabutan_pembatalan_penahanan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_8" name="surat_penahanan_8"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_8" name="surat_penahanan_8">Edit</a> --}}
                <a target="_blank" href="/berita-pembatalan-penahanan/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-pembatalan-penahanan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Surat perintah penahanan lanjutan (jika ada)</span>
                @if ($surat_penahanan_lanjutan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_9" name="surat_penahanan_9"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_9" name="surat_penahanan_9">Edit</a> --}}
                <a target="_blank" href="/penahanan-lanjutan/{{$id}}" id="">Lihat</a></span>
                <form action="/penahanan-lanjutan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span>Berita acara penahanan lanjutan (jika ada)</span>
                @if ($berita_penahanan_lanjutan==null)
                <i class="bi bi-pencil-square" id="surat_penahanan_10" name="surat_penahanan_10"></i>
                @else
                {{-- <a href=# class="" id="surat_penahanan_10" name="surat_penahanan_10">Edit</a> --}}
                <a target="_blank" href="/berita-penahanan-lanjutan/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-penahanan-lanjutan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>
        </div>
    </div>
</div>

{{-- @include('produktivitas.surat-penahanan.modal-bak') --}}
@include('produktivitas.surat-penahanan.modal.modal')
@push('script')
<script type="text/javascript">

$(document).ready(function(){
    $(".kategori4").css("width","{{$TotalKategori4}}%")
    $(".progress4").hide();
   if({{$TotalKategori4}}>=40 && {{$TotalKategori4}}<=70){
    $(".progress4").show();
    document.getElementById("kategori4").classList.add("bg-warning")
   }else if({{$TotalKategori4}}>0 && {{$TotalKategori4}}<40){
    $(".progress4").show();
    document.getElementById("kategori4").classList.add("bg-danger")
   }else if({{$TotalKategori4}}>=80 && {{$TotalKategori4}}<=90){
    $(".progress4").show();
    document.getElementById("kategori4").classList.add("bg-info")
   }else if({{$TotalKategori4}}>90){
    $(".progress4").show();
    document.getElementById("kategori4").classList.add("bg-success")
   }
})

</script>
@endpush
