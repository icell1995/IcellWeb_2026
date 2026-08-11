@extends('layouts.app')

@section('content')
<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="back-button">
        <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}"><i class="bi bi-arrow-left"></i> Kembali ke Produktivitas</a>
    </div>
    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">Tambah Surat Perintah Penyidikan (SPRINDIK)</h5>
        </div>
        <br>

        <form action="{{ route('investigation-order-letter.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="accident_id" value="{{$accidentId}}">

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-3">
                        <label class="fw-bold">Nomor LP</label>
                        <div class="input-group" style="padding: 0px">
                            <input id="accident_number" type="text"
                                class="form-control @error('accident_number') is-invalid @enderror font-weight-bold" name="accident_number"
                                value="{{ $accident->no_lp }}" required placeholder="" readonly>

                            @error('accident_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-3">
                        <label class="fw-bold">Nomor Surat Sprindik</label>
                        <div class="input-group" style="padding: 0px">
                            <input id="letter_number" type="text"
                                class="form-control @error('letter_number') is-invalid @enderror font-weight-bold" name="letter_number"
                                value="{{ old('letter_number') }}" required placeholder="Masukkan Nomor Surat Sprindik">

                            @error('letter_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold">Tanggal Dimulai</label>
                        <div class="input-group" style="padding: 0px">
                            <input class="form-control datepicker" id="start_date" name="start_date"
                                placeholder="DD/MM/YYYY" autocomplete="off" value="{{old('start_date')}}" data-provide="datepicker">

                            @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold">Tanggal Berakhir</label>
                        <div class="input-group" style="padding: 0px">
                            <input class="form-control datepicker" id="end_date" name="end_date"
                                placeholder="DD/MM/YYYY" autocomplete="off" value="{{old('end_date')}}" data-provide="datepicker">

                            @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-3">
                        <label class="fw-bold">Yang Menandatangani</label>
                        <div class="input-group" style="padding: 0px">
                            <select class="form-control" name="authorized_signatory" id="authorized_signatory">
                                @foreach($authorizedSignatories as $data)
                                <option value="{{$data->id}}">{{$data->register_number . ' - ' . $data->full_name . ' | ' . $data->position_id}}</option>
                                @endforeach
                            </select>

                            @error('authorized_signatory')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-header">
                <h5 class="fw-bold text-blue-dark">Undang-Undang yang Dikenakan</h5>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-5">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                <th scope="col">Pilih</th>
                                <th scope="col">Undang-Undang</th>
                                <th scope="col">Pasal</th>
                                <th scope="col">Ayat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laws as $data)
                                <tr class="text-center">
                                    <th>
                                        <input class="form-check-input" type="checkbox" value="{{$data->id}}" id="law{{$data->id}}" name="laws[]">
                                    </th>
                                    <td>{{$data->law}}</td>
                                    <td>{{$data->chapter}}</td>
                                    <td>{{$data->verse}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @error('laws')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="box-header">
                <h5 class="fw-bold text-blue-dark">Tim Penyidik</h5>
            </div>
            <br>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-3">
                        <label class="fw-bold">Ketua Tim Penyidik</label>
                        <div class="input-group" style="padding: 0px">
                            <select class="form-control" name="personnel_leader" id="personnel_leader">
                                @foreach($personnelLeaders as $data)
                                <option value="{{$data->id}}">{{$data->id . ' - ' . $data->full_name . ' | ' . $data->sebagai_kepala}}</option>
                                @endforeach
                            </select>

                            @error('personnel_leader')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="mb-3">
                        <label class="fw-bold">Anggota Tim Penyidik</label>

                        <div class="input-group" style="padding: 0px">
                            {{--<button class="btn btn-primary btn-add-personnel" type="button" name="add_personnel" id="add_personnel">
                                Tambah Petugas
                            </button>

                            <div class="col-sm-12 col-md-12" id="add_personel_penyidikan">

                            </div>--}}

                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                    <th scope="col">Pilih</th>
                                    <th scope="col">NRP</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Jabatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($officers as $data)
                                    <tr class="text-center">
                                        <th>
                                            <input class="form-check-input" type="checkbox" value="{{$data->id}}" id="personnel{{$data->id}}" name="personnel[]">
                                        </th>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->full_name}}</td>
                                        <td>{{$data->position_short_name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @error('personnel')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    {{ __('Simpan') . ' ' . 'Sprindik'}}
                </button>
                <a href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}" class="btn btn-danger">
                    {{ __('Batal') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" defer></script>
<script type="text/javascript">
    var xRowPersonnel = 1;

    $('#add_personnel').click(function(){
        xRowPersonnel++;
        var officers = {!! json_encode($officers) !!};
        var options = '';

        for (var i = 0; i < officers.length; i++) {
            options += '<option value="' + officers[i].id + '">' + officers[i].id + ' - ' + officers[i].first_name + ' ' + officers[i].last_name + '</option>';
        }

        var selectBox = '<div class="input-group col-lg-11 input-personnel mt-4" id="inputAddPersonnel' + xRowPersonnel + '"> <select class="form-control" name="personnel[]">' + options + '</select><div class="remove-personnel input-group-append col-lg-1"><button type="button" name="remove_personnel" class="btn btn-danger btn_removeX btn_remove_personnel" id="' + xRowPersonnel + '">X</button></div></div>';

        $('#add_personel_penyidikan').append(selectBox);

    });

    $(document).on('click', '.btn_remove_personnel', function(){
        var button_id = $(this).attr("id");
        $('#inputAddPersonnel' + button_id).remove();
    });

    $('#start_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom',
        });
    $('#end_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom',
        });
</script>
@endpush
