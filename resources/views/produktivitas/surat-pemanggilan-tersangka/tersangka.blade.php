<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed item-header" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseTersangka" aria-expanded="false" aria-controls="collapseTersangka">
            3. Pemanggilan Tersangka
        </button>
        <div class="progress3 progress-bar-none">
            <div id="kategori3" class="progress-bar bg-success kategori3" role="progressbar" style="width:"
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{$TotalKategori3}}%</div>
        </div>
    </h2>
    <div id="collapseTersangka" class="accordion-collapse collapse" data-bs-parent="#accordionProduktivitas">
        <div class="accordion-body item-body">
            <div class="item-list" style="display: none !important">
                <span>Daftar Tersangka</span>
                <i class="bi bi-pencil-square" id="daftar_tersangka" name="daftar_tersangka"></i>
            </div>

            <div class="item-list">
                <span>Surat panggilan tersangka</span>
                @if ($surat_panggilan_tersangka==null)
                <i class="bi bi-pencil-square" id="surat_pemanggilan_tersangka_1"
                    name="surat_pemanggilan_tersangka_1"></i>
                @else
                {{-- <a href=# class="" id="surat_pemanggilan_tersangka_3"
                    name="surat_pemanggilan_tersangka_3">Edit</a> --}}
                <a target="_blank" href="/surat-panggilan-tersangka/{{$id}}" id="">Lihat</a></span>
                <form action="/surat-panggilan-tersangka/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            {{-- <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Surat perintah penangkapan</a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-2">
                    @if ($surat_perintah_penangkapan==null)
                    <i class="fa fa-pencil-square-o" id="surat_pemanggilan_tersangka_2"
                        name="surat_pemanggilan_tersangka_2"></i>
                    @else
                    <a target="_blank" href="/surat-perintah-penangkapan/{{$id}}" id="">Lihat</a></span>
                    <form action="/surat-perintah-penangkapan/{{$id}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div> --}}

            <div class="item-list">
                <span class="item-action">Berita acara pemeriksaan tersangka</span>
                @if ($berita_acara_pemeriksaan_tersangka==null)
                <i class="bi bi-pencil-square" id="surat_pemanggilan_tersangka_3"
                    name="surat_pemanggilan_tersangka_3"></i>
                @else
                {{-- <a href=# class="" id="surat_pemanggilan_tersangka_3"
                    name="surat_pemanggilan_tersangka_3">Edit</a> --}}
                <a target="_blank" href="/berita-pemeriksaan-tersangka/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-pemeriksaan-tersangka/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span class="item-action">Berita acara konfrontasi (jika ada)</span>
                @if ($berita_acara_konfrontasi==null)
                    <div class="d-flex">
                        <i class="bi bi-pencil-square" id="surat_pemanggilan_tersangka_4"
                            name="surat_pemanggilan_tersangka_4"></i>
                        <div class="form-check p-0 check" id="check_berita_acara_konfrontasi">
                            <input class="form-check-input ms-2" type="checkbox" value="1">
                        </div>
                    </div>
                @else
                {{-- <a href=# class="" id="surat_pemanggilan_tersangka_4"
                    name="surat_pemanggilan_tersangka_4">Edit</a> --}}
                <a target="_blank" href="/berita-acara-konfrontasi/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-acara-konfrontasi/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span class="item-action">Berita acara Rekonstruksi (jika ada)</span>
                @if ($berita_acara_rekonstruksi==null)
                <div class="d-flex">
                    <i class="bi bi-pencil-square" id="surat_pemanggilan_tersangka_5"
                        name="surat_pemanggilan_tersangka_5"></i>
                    <div class="form-check p-0 check" id="check_berita_acara_rekontruksi">
                        <input class="form-check-input ms-2" type="checkbox" value="1">
                    </div>
                </div>
                @else
                {{-- <a href=# class="" id="surat_pemanggilan_tersangka_5"
                    name="surat_pemanggilan_tersangka_5">Edit</a> --}}
                <a target="_blank" href="/berita-acara-rekonstruksi/{{$id}}" id="">Lihat</a></span>
                <form action="/berita-acara-rekonstruksi/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span class="item-action">Surat permintaan bantuan penangkapan (jika ada)</span>
                @if ($surat_bantuan_penangkapan==null)
                <div class="d-flex">
                    <i class="bi bi-pencil-square" id="surat_pemanggilan_tersangka_7"
                        name="surat_pemanggilan_tersangka_7"></i>
                    <div class="form-check p-0 check" id="check_surat_permintaan_bantuan_penangkapan">
                        <input class="form-check-input ms-2" type="checkbox" value="1">
                    </div>
                </div>
                @else
                {{-- <a href=# class="" id="surat_pemanggilan_tersangka_7"
                    name="surat_pemanggilan_tersangka_7">Edit</a> --}}
                <a target="_blank" href="/surat-bantuan-penangkapan/{{$id}}" id="">Lihat</a></span>
                <form action="/surat-bantuan-penangkapan/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            <div class="item-list">
                <span class="item-action">Berita acara penyerahan tersangka (jika ada)</span>
                @if ($berita_penyerahan_tersangka==null)
                <div class="d-flex">
                    <i class="bi bi-pencil-square" id="surat_pemanggilan_tersangka_8"
                        name="surat_pemanggilan_tersangka_8"></i>
                    <div class="form-check p-0 check" id="check_berita_acara_penyerahan_tersangka">
                        <input class="form-check-input ms-2" type="checkbox" value="1">
                    </div>
                </div>
                @else
                {{-- <a href=# class="" id="surat_pemanggilan_tersangka_8"
                    name="surat_pemanggilan_tersangka_8">Edit</a> --}}
                <a target="_blank" href="/penyerahan-tersangka/{{$id}}" id="">Lihat</a></span>
                <form action="/penyerahan-tersangka/{{$id}}" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" style="color: #007bff; border: none; background: none;
                    font-weight: bold; padding: 10px;">DELETE</button>
                </form>
                {{-- <a href=# class="" id="delete" name="delete">Delete</a> --}}
                @endif
            </div>

            {{-- <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">Berita acara pelepasan tersangka (jika ada)</a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-2 row border-0">
                    @if ($berita_pelepasan_tersangka==null)
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                        <i class="fa fa-pencil-square-o" id="surat_pemanggilan_tersangka_9"
                            name="surat_pemanggilan_tersangka_9"></i>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 check" id="check_berita_acara_pelepasan_tersangka">
                        <input type="checkbox" value="1">
                    </div>
                    @else
                    <a target="_blank" href="/pelepasan-tersangka/{{$id}}" id="">Lihat</a></span>
                    <form action="/pelepasan-tersangka/{{$id}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div> --}}
        </div>
    </div>
</div>

{{-- daftar tersangka --}}
<div data-backdrop="false" id="myModalTersangka" name="myModalTersangka" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Tersangka</h5>
            </div>
            <div class="modal-body ">
                @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissble fade show" role="alert">
                                {{session('error')}}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                             @endif
                <div class="modal-tersangka">
                    <form id="tersangka-form">
                        @csrf
                        <div class=col-12>
                            <div>
                                <input type="hidden" name="tersangka_id" id="tersangka_id">
                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}" hidden>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label for="name_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Nama') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="name_tersangka" type="text"
                                                class="form-control @error('name_tersangka') is-invalid @enderror"
                                                name="name_tersangka" value="{{ old('name_tersangka')}}"
                                                autocomplete="name_tersangka">
                                            <span class="text-danger error-text name_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="gender_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Jenis Kelamin') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <select name="gender_tersangka" id="gender_tersangka" class="form-control">
                                                <option value="G0101">Pilih Jenis Kelamin</option>
                                                @foreach ($gender as $genders)
                                                <option value="{{ $genders->id }}">
                                                    {{ old('gender') == $genders->id ? 'selected' : '' }}
                                                    {{ $genders->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            {{-- <input id="gender_tersangka" type="text"
                                                class="form-control @error('gender_tersangka') is-invalid @enderror"
                                                name="gender_tersangka" value="{{ old('gender_tersangka')}}"
                                                autocomplete="gender_tersangka"> --}}
                                            <span class="text-danger error-text gender_err"></span>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="jenis_identitas_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Jenis Identitas') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            {{-- <input id="jenis_identitas_tersangka" type="text"
                                                class="form-control @error('jenis_identitas_tersangka') is-invalid @enderror"
                                                name="jenis_identitas_tersangka" value="{{ old('jenis_identitas_tersangka')}}"
                                                autocomplete="jenis_identitas_tersangka"> --}}
                                            <select name="jenis_identitas_tersangka" id="jenis_identitas_tersangka"
                                                class="form-control">
                                                <option value="G0201">Jenis Identitas</option>
                                                @foreach ($identity_type as $type_ids)
                                                <option value="{{ $type_ids->id }}">
                                                    {{ old('identity_type') == $type_ids->id ? 'selected' : '' }}
                                                    {{ $type_ids->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text jenis_identitas_tersangka_err"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-13">
                                        <div class="mb-3 row">
                                            <label for="identity_no" class="col-sm-3 fw-bold col-form-label">{{
                                                __('Nomor Identitas') }}</label>

                                            <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                                <input id="identity_no" type="text"
                                                    class="form-control @error('identity_no') is-invalid @enderror"
                                                    name="identity_no" value="{{ old('identity_no')}}"
                                                    autocomplete="identity_no">
                                                <span class="text-danger error-text phone_err"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="city_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Tempat tanggal lahir') }}</label>

                                        <div class="col-md-4">
                                            <input id="city_tersangka" type="text"
                                                class="form-control @error('city_tersangka') is-invalid @enderror"
                                                name="city_tersangka" value="{{ old('city_tersangka')}}"
                                                autocomplete="city_tersangka">
                                            <span class="text-danger error-text city_err"></span>
                                        </div>

                                        <div class="col-md-5">
                                            {{-- <input id="birth_date_tersangka" type="text"
                                                class="form-control @error('birth_date_tersangka') is-invalid @enderror"
                                                name="birth_date_tersangka" value="{{ old('birth_date_tersangka')}}"
                                                autocomplete="birth_date_tersangka"> --}}
                                            <input class="form-control" type="text" id="birth_date_tersangka"
                                                name="birth_date_tersangka" placeholder="dd/mm/yyyy" autocomplete="off">
                                            <span class="text-danger error-text birth_date_err"></span>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label for="religion_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Agama') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            {{-- <input id="religion_tersangka" type="text"
                                                class="form-control @error('religion_tersangka') is-invalid @enderror"
                                                name="religion_tersangka" value="{{ old('religion_tersangka')}}"
                                                autocomplete="religion_tersangka"> --}}
                                            <select name="religion_tersangka" id="religion_tersangka"
                                                class="form-control">
                                                <option value="R0101">Agama</option>
                                                @foreach ($religion as $religions)
                                                <option value="{{ $religions->id }}">
                                                    {{ old('religion') == $religions->id ? 'selected' : '' }}
                                                    {{ $religions->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text religion_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="job_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Pekerjaan') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="job_tersangka" type="text"
                                                class="form-control @error('job_tersangka') is-invalid @enderror"
                                                name="job_tersangka" value="{{ old('job_tersangka')}}"
                                                autocomplete="job_tersangka">
                                            <span class="text-danger error-text job_err"></span>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="education_tersangka"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Pendidikan') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            {{-- <input id="education_tersangka" type="text"
                                                class="form-control @error('education_tersangka') is-invalid @enderror"
                                                name="education_tersangka" value="{{ old('education_tersangka')}}"
                                                autocomplete="education_tersangka"> --}}
                                            <select name="education_tersangka" id="education_tersangka"
                                                class="form-control">
                                                <option value="E0101">Pendidikan</option>
                                                @foreach ($education as $educations)
                                                <option value="{{ $educations->id }}">
                                                    {{ old('education') == $educations->id ? 'selected' : '' }}
                                                    {{ $educations->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text education_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="phone_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Nomor HP') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="phone_tersangka" type="text"
                                                class="form-control @error('phone_tersangka') is-invalid @enderror"
                                                name="phone_tersangka" value="{{ old('phone_tersangka')}}"
                                                autocomplete="phone_tersangka">
                                            <span class="text-danger error-text phone_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="citizen_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Warga Negara') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            <input id="citizen_tersangka" type="text"
                                                class="form-control @error('citizen_tersangka') is-invalid @enderror"
                                                name="citizen_tersangka" value="{{ old('citizen_tersangka')}}"
                                                autocomplete="citizen_tersangka">
                                            <span class="text-danger error-text citizen_err"></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="address_tersangka" class="col-sm-3 fw-bold col-form-label">{{
                                            __('Alamat') }}</label>

                                        <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                                            {{-- <input id="address" type="text"
                                                class="form-control @error('address') is-invalid @enderror"
                                                name="address" value="{{ old('address')}}" required
                                                autocomplete="address"> --}}
                                            <textarea class="form-control" name="address_tersangka"
                                                id="address_tersangka" placeholder="Address"></textarea>
                                            <span class="text-danger error-text address_err"></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="text-start">
                            <button type="submit" class="btn btn-dark-blue btn-tersangka">Tambah Tersangka</button>
                        </div>
                    </form>
                    <form action="{{route('daftarTersangka', $id)}}" method="GET" class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" value="{{$id}}"> Save to Word
                        </button>
                    </form>
                    <table class="table table-bordered tersangka-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Tersangka</th>
                                <th>Jenis Kelamin</th>
                                <th>Tempat Lahir</th>
                                <th>Tanggal Lahir</th>
                                <th>Warga Negara</th>
                                <th>Jenis Identitas</th>
                                <th>Nomor Identitas</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                {{-- <div class="alert alert-success alert-block" style="display: none;">
                    <button type="button" class="close" data-dismiss="test">×</button>
                    <strong class="success-msg"></strong>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- end daftar tersangka --}}

{{-- @include('produktivitas.surat-pemanggilan-tersangka.modal-bak') --}}
@include('produktivitas.surat-pemanggilan-tersangka.modal.modal')
@push('script')
<script type="text/javascript">
    $(document).ready(function(){
    $(".kategori3").css("width","{{$TotalKategori3}}%")
    $(".progress3").hide();
   if({{$TotalKategori3}}>=40 && {{$TotalKategori3}}<75){
    $(".progress3").show();
    document.getElementById("kategori3").classList.add("bg-warning");
   }else if({{$TotalKategori3}}>0 && {{$TotalKategori3}}<40){
    $(".progress3").show();
    document.getElementById("kategori3").classList.add("bg-danger")
   }else if({{$TotalKategori3}}>=75 && {{$TotalKategori3}}<=90){
    $(".progress3").show();
    document.getElementById("kategori3").classList.add("bg-info")
   }else if({{$TotalKategori3}}>90){
    $(".progress3").show();
    document.getElementById("kategori3").classList.add("bg-success")
   }
})

</script>
@endpush
