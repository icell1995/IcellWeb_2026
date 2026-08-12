<!-- Extra large modal -->
<div data-backdrop="false" id="myModalSaksi2" name="myModalSaksi2" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{-- <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h5>Silahkan masukan file Surat Perintah Membawa Saksi berupa PDF</h5>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="form_perintah_membawa_saksi" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalSaksi3" name="myModalSaksi3" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{-- <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Silahkan masukan file Berita acara membawa dan menghadapkan saksi berupa
                                        PDF
                                    </h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_membawa_saksi" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModalSaksi1" name="myModalSaksi1" class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title fw-bold">Tambah Saksi</h5>

            </div>
            <div class="modal-body ">
                <div class="modal-saksi">
                    <form id="saksi-form">
                        @csrf
                        <div class=col-md-12>
                            <div>
                                <input type="hidden" name="saksi_id" id="saksi_id">
                                <input id="accident_id_saksi" name="accident_id_saksi" type="text"
                                    value="{{ $id }}" hidden>
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Nama') }}</label>

                                        <div class="col-md-9">
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" autocomplete="name">
                                            <span class="text-danger error-text name_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="gender"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Jenis Kelamin') }}</label>

                                        <div class="col-md-9">
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="G0101">Pilih Jenis Kelamin</option>
                                                @foreach ($gender as $genders)
                                                    <option value="{{ $genders->id }}">
                                                        {{ old('gender') == $genders->id ? 'selected' : '' }}
                                                        {{ $genders->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- <select id="gender" class="form-control">
                                                <option selected value="G0103">Pilih Jenis Kelamin</option>
                                                <option value="G0101">Pria</option>
                                                <option value="G0102">Wanita</option>
                                            </select> --}}
                                            {{-- <input id="gender" type="text"
                                                class="form-control @error('gender') is-invalid @enderror" name="gender"
                                                value="{{ old('gender')}}" autocomplete="gender"> --}}
                                            <span class="text-danger error-text gender_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="city"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Tempat') }}</label>

                                        <div class="col-md-4">
                                            <input id="city" type="text"
                                                class="form-control @error('city') is-invalid @enderror" name="city"
                                                value="{{ old('city') }}" autocomplete="city">
                                            <span class="text-danger error-text city_err"></span>
                                        </div>

                                        <div class="col-md-5">
                                            {{-- <input id="birth_date" type="text"
                                                class="form-control datepicker @error('birth_date') is-invalid @enderror"
                                                name="birth_date" value="{{ old('birth_date')}}"
                                                placeholder="dd/mm/yyyy"
                                                autocomplete="off"> --}}
                                            <input class="form-control datepicker" type="text" id="birth_date"
                                                name="birth_date" placeholder="dd/mm/yyyy" autocomplete="off">
                                            <span class="text-danger error-text birth_date_err"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="religion"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Agama') }}</label>

                                        <div class="col-md-9">
                                            {{-- <input id="religion" type="text"
                                                class="form-control @error('religion') is-invalid @enderror"
                                                name="religion" value="{{ old('religion') }}" autocomplete="religion"> --}}
                                            <select name="religion" id="religion" class="form-control">
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
                                    <div class="form-group row">
                                        <label for="job"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Pekerjaan') }}</label>

                                        <div class="col-md-9">
                                            <input id="job" type="text"
                                                class="form-control @error('job') is-invalid @enderror" name="job"
                                                value="{{ old('job') }}" autocomplete="job">
                                            <span class="text-danger error-text job_err"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="education"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Pendidikan') }}</label>

                                        <div class="col-md-9">
                                            {{-- <input id="education" type="text"
                                                class="form-control @error('education') is-invalid @enderror"
                                                name="education" value="{{ old('education') }}"
                                                autocomplete="education"> --}}
                                            <select name="education" id="education" class="form-control">
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
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="phone"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Nomor HP') }}</label>

                                        <div class="col-md-9">
                                            <input id="phone" type="text"
                                                class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                value="{{ old('phone') }}" autocomplete="phone">
                                            <span class="text-danger error-text phone_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="citizen"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Warga Negara') }}</label>

                                        <div class="col-md-9">
                                            <input id="citizen" type="text"
                                                class="form-control @error('citizen') is-invalid @enderror"
                                                name="citizen" value="{{ old('citizen') }}" autocomplete="citizen">
                                            <span class="text-danger error-text citizen_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="address"
                                            class="col-sm-3 fw-bold col-form-label">{{ __('Alamat') }}</label>

                                        <div class="col-md-9">
                                            {{-- <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address')}}"
                                            required autocomplete="address"> --}}
                                            <textarea class="form-control" name="address" id="address"
                                                placeholder="Address"></textarea>
                                            <span class="text-danger error-text address_err"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="centered">
                            <button type="submit" class="btn btn-primary btn-saksi">Tambah Saksi</button>
                        </div>
                    </form>
                    <table class="table table-bordered saksi-datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>gender</th>
                                <th>city</th>
                                <th>birth_date</th>
                                <th>citizen</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                {{-- <div class="alert alert-success alert-block" style="display: none;">
                  <button type="button" class="close" data-dismiss="test">×</button>
                  <strong class="success-msg"></strong>
          </div> --}}

            </div>
            <div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div data-backdrop="false" id="myModalSaksi4" name="myModalSaksi4" class="modal fade bd-example-modal-lg" tabindex="-1"
    role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    {{-- <div id="myModal" name="myModalDelete" class="modal hide fade in" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true"> --}}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="col-md-8 section offset-md-2">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h2>Silahkan masukan file Berita acara penyumpahan saksi/ahli berupa PDF
                                    </h2>
                                </div>
                                <div class="panel-body">
                                    <form action="{{ route('file.upload') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div>
                                                <input id="form_id" name="form_id" type="text"
                                                    value="berita_acara_penyumpahan_saksi" hidden>
                                            </div>
                                            <div>
                                                <input id="accident_id" name="accident_id" type="text" value="{{$id}}"
                                                    hidden>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-backdrop="false" class="btn btn-danger"
                            data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
