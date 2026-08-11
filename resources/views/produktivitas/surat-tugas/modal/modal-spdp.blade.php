<div class="modal fade" id="myModalSuratSpdp" name="myModalSuratSpdp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog" style="min-width: 1140px;">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Lengkapi Data untuk SPDP</h3>
            </div>

            <form action="{{ route('add_surat_spdp') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_spdp" name="accident_id_spdp" type="text" value="{{$id}}" hidden>
                    <input id="id_spdik" name="id_spdik" type="text" value="{{$id_spdik}}" hidden>
                    <input id="id_spgas" name="id_spgas" type="text" value="{{$id_spgas}}" hidden>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="mb-3">
                            <label class="text-white">Nomor SPDP</label>
                            <div class="input-group" style="padding: 0px">
                                <input id="no_spdp" type="text" class="form-control @error('no_spdp') is-invalid @enderror font-weight-bold" name="no_spdp" value="{{ old('no_spdp') }}" required autocomplete="no_spdp" autofocus placeholder="Masukkan Nomor SPDP">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nomor LP</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="no_lp" type="text" class="form-control @error('no_lp') is-invalid @enderror font-weight-bold" name="no_lp" value="{{ $no_lp }}" required autocomplete="no_lp" autofocus placeholder="{{ $no_lp }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nomor Sprindik</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="no_sprindik" type="text" class="form-control @error('no_sprindik') is-invalid @enderror font-weight-bold" name="no_sprindik" value="{{ $spdik_letter_number }}" required autocomplete="no_sprindik" autofocus readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Tanggal Sprindik</label>
                                <div class="input-group" style="padding: 0px">
                                    <input class="form-control" type="text" id="sprindik_date" name="sprindik_date" value="{{ $spdik_issued_date }}" placeholder="DD/MM/YYYY" autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Tanggal Ditandatangani Dokumen SPDP</label>
                                <div class="input-group" style="padding: 0px">
                                    <input class="form-control datepicker" type="text" id="spdp_date" name="spdp_date" placeholder="DD/MM/YYYY" autocomplete="off" data-provide="datepicker">
                                    <span class="text-danger error-text birth_date_err"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Kategori SPDP</label>
                                <div class="input-group" style="padding: 0px">
                                    <select class="form-control" name="category_spdp" id="category_spdp">
                                        <option value="SPDP BARU">SPDP BARU</option>
                                        <option value="Direferensikan Dari SPDP Sebelumnya">Direferensikan Dari SPDP Sebelumnya</option>
                                    </select>
                                    {{-- <input id="category_spdp" type="text"
                                        class="form-control @error('category_spdp') is-invalid @enderror" name="category_spdp"
                                        value="{{ old('category_spdp') }}" required autocomplete="category_spdp" autofocus placeholder="SPDP Baru"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nama Penerima</label>
                                <div class="input-group" style="padding: 0px">
                                    <select name="endorsee_name" id="endorsee_name" class="form-control @error('endorsee_name') is-invalid @enderror" type="">
                                        @foreach ($kejaksaan_id as $kj)
                                        <option value="<?php echo htmlspecialchars($kj->id); ?>"> <?php echo htmlspecialchars($kj->name); ?> </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Pengadilan</label>
                                <div class="input-group" style="padding: 0px">
                                    <select name="pengadilan" id="pengadilan" class="form-control @error('pengadilan') is-invalid @enderror" type="">
                                        @foreach ($pengadilan_id as $pg)
                                        <option value="<?php echo htmlspecialchars($pg->id); ?>"> <?php echo htmlspecialchars($pg->name); ?> </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Klasifikasi</label>
                                <div class="input-group" style="padding: 0px">
                                    <select class="form-control" name="klasifikasi" id="klasifikasi">
                                        <option value="Mudah">Mudah</option>
                                        <option value="Biasa" selected>Biasa</option>
                                        <option value="Sulit">Sulit</option>
                                    </select>
                                    {{-- <input id="klasifikasi" type="text"
                                        class="form-control @error('klasifikasi') is-invalid @enderror" name="klasifikasi"
                                        value="{{ old('klasifikasi') }}" required autocomplete="klasifikasi" autofocus placeholder="Klasifikasi"> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nama Tersangka</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="suspect_name" type="text" class="form-control @error('suspect_name') is-invalid @enderror" name="suspect_name" value="{{ $suspectsName }}" required autocomplete="suspect_name" autofocus placeholder="Masukkan Nama Tersangka" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Lampiran</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="lampiran" type="text" class="form-control @error('lampiran') is-invalid @enderror" name="lampiran" value="{{ old('lampiran') }}" required autocomplete="lampiran" autofocus placeholder="Lampiran">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Penandatanganan Surat</label>
                                <div class="input-group" style="padding: 0px">
                                    <select name="latter_signature" id="latter_signature" class="form-control @error('latter_signature') is-invalid @enderror">
                                        @foreach ($pejabat as $officers)
                                        <option value="{{ $officers->id }}">{{$officers->register_number . ' - ' . $officers->first_title . ' ' . $officers->first_name . ' ' . $officers->last_name . ', ' . $officers->last_title . ' | ' . $officers->position_id}}
                                        </option>
                                        @endforeach
                                    </select>

                                    {{-- <input id="latter_signature" type="text"
                                        class="form-control @error('latter_signature') is-invalid @enderror" name="latter_signature"
                                        value="{{ old('latter_signature') }}" required autocomplete="latter_signature" autofocus placeholder="Masukkan Yang Menandatangani Surat"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            {{-- <div class="mb-3">
                                <label class="text-white">Untuk Perhatian</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="for_attention" type="text"
                                        class="form-control @error('for_attention') is-invalid @enderror" name="for_attention"
                                        value="{{ old('for_attention') }}" required autocomplete="for_attention" autofocus placeholder="">
                        </div>
                    </div> --}}
                    <div class="mb-3">
                        <label class="text-white">Tembusan</label>
                        <div class="input-group" style="padding: 0px">
                            <input id="tembusan" type="text" class="form-control @error('tembusan') is-invalid @enderror" name="tembusan" value="{{ old('tembusan') }}" required autocomplete="tembusan" autofocus placeholder="Masukkan tembusan">
                        </div>
                    </div>
                </div>
        </div>
        {{-- <div class="col-xs-12 col-sm-12 col-md-12">

                    </div> --}}
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-simpan">
            {{ __('Simpan') }}
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            {{ __('Batal') }}
        </button>
    </div>
    </form>
</div>
</div>
</div>

<div class="modal fade" id="myEditModalSuratSPDP" name="myEditModalSuratSPDP" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog" style="min-width: 1140px;">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Data SPDP</h3>
            </div>

            <form action="{{ route('edit_surat_spdp') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_spdp" name="accident_id_spdp" type="text" value="{{$id}}" hidden>
                    @foreach ($spdp as $spdp )

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="mb-3">
                            <label class="text-white">Nomor SPDP</label>
                            <div class="input-group" style="padding: 0px">
                                <input id="no_spdp" type="text" class="form-control @error('no_spdp') is-invalid @enderror font-weight-bold" name="no_spdp" value="{{$spdp->no_spdp}}" required autocomplete="no_spdp" autofocus placeholder="Masukkan Nomor SPDP">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nomor LP</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="no_lp" type="text" class="form-control @error('no_lp') is-invalid @enderror font-weight-bold" name="no_lp" value="{{$spdp->no_lp}}" required autocomplete="no_lp" autofocus placeholder="{{ $no_lp }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nomor Sprindik</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="no_sprindik" type="text" class="form-control @error('no_sprindik') is-invalid @enderror" name="no_sprindik" value="{{$sprindik->letter_number}}" required autocomplete="no_sprindik" autofocus placeholder="Nomor Sprindik">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Tanggal Sprindik</label>
                                <div class="input-group" style="padding: 0px">
                                    <input class="form-control datepicker" type="text" id="sprindik_date_edit" name="sprindik_date" placeholder="DD/MM/YYYY" autocomplete="off" data-provide="datepicker" value="{{Carbon\Carbon::parse($spdp->sprindik_date)->format('d-m-Y')}}">
                                    <span class="text-danger error-text birth_date_err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Tanggal Ditandatangani Dokumen SPDP</label>
                                <div class="input-group" style="padding: 0px">
                                    <input class="form-control datepicker" type="text" id="spdp_date_edit" name="spdp_date" placeholder="DD/MM/YYYY" autocomplete="off" data-provide="datepicker" value="{{Carbon\Carbon::parse($spdp->spdp_date)->format('d-m-Y')}}">
                                    <span class="text-danger error-text birth_date_err"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Kategori SPDP</label>
                                <div class="input-group" style="padding: 0px">
                                    <select class="form-control" name="category_spdp" id="category_spdp" required autocomplete="category_spdp" autofocus>
                                        <option value="SPDP BARU" {{ $spdp->category_spdp == 'SPDP BARU' ? 'selected' : '' }}>SPDP BARU</option>
                                        <option value="Direferensikan Dari SPDP Sebelumnya" {{ $spdp->category_spdp == 'Direferensikan Dari SPDP Sebelumnya' ? 'selected' : '' }}>Direferensikan Dari SPDP Sebelumnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nama Penerima</label>
                                <div class="input-group" style="padding: 0px">
                                    <select name="endorsee_name" id="endorsee_name" class="form-control @error('endorsee_name') is-invalid @enderror" type="">
                                        @foreach ($kejaksaan_id as $kj)
                                        <option value="{{ $kj->id }}" @if($kj->id == $spdp->kejaksaan_id) selected="selected" @endif> {{ $kj->name }} </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Pengadilan</label>
                                <div class="input-group" style="padding: 0px">
                                    <select name="pengadilan" id="pengadilan" class="form-control @error('pengadilan') is-invalid @enderror" type="">
                                        @foreach ($pengadilan_id as $pg)
                                        <option value="{{ $pg->id }}" @if($pg->id == $spdp->pengadilan_id) selected="selected" @endif> {{ $pg->name }} </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Klasifikasi</label>
                                <div class="input-group" style="padding: 0px">
                                    <select class="form-control" name="klasifikasi" id="klasifikasi" required autocomplete="klasifikasi" autofocus>
                                        <option value="Ringan" {{ $spdp->klasifikasi == 'Ringan' ? 'selected' : '' }}>Ringan</option>
                                        <option value="Sedang" {{ $spdp->klasifikasi == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="Berat" {{ $spdp->klasifikasi == 'Berat' ? 'selected' : '' }}>Berat</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Nama Tersangka</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="suspect_name" type="text" class="form-control @error('suspect_name') is-invalid @enderror" name="suspect_name" value="{{$spdp->suspect_name}}" required autocomplete="suspect_name" autofocus placeholder="Masukkan Nama Tersangka">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Lampiran</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="lampiran" type="text" class="form-control @error('lampiran') is-invalid @enderror" name="lampiran" value="{{$spdp->lampiran}}" required autocomplete="lampiran" autofocus placeholder="Lampiran">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="mb-3">
                                <label class="text-white">Penandatanganan Surat</label>
                                <div class="input-group" style="padding: 0px">
                                    <select name="latter_signature" id="latter_signature" class="form-control @error('latter_signature') is-invalid @enderror">
                                        @foreach ($pejabat as $officers)
                                        <option value="{{ $officers->id }}">{{$officers->register_number . ' - ' . $officers->first_title . ' ' . $officers->first_name . ' ' . $officers->last_name . ', ' . $officers->last_title . ' | ' . $officers->position_id}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            {{-- <div class="mb-3">
                                <label class="text-white">Untuk Perhatian</label>
                                <div class="input-group" style="padding: 0px">
                                    <input id="for_attention" type="text"
                                        class="form-control @error('for_attention') is-invalid @enderror" name="for_attention"
                                        value="{{$spdp->for_attention}}" required autocomplete="for_attention" autofocus placeholder="">
                        </div>
                    </div> --}}
                    <div class="mb-3">
                        <label class="text-white">Tembusan</label>
                        <div class="input-group" style="padding: 0px">
                            <input id="tembusan" type="text" class="form-control @error('tembusan') is-invalid @enderror" name="tembusan" value="{{$spdp->tembusan}}" required autocomplete="tembusan" autofocus placeholder="Masukkan tembusan">
                        </div>
                    </div>
                </div>
        </div>
        {{-- <div class="col-xs-12 col-sm-12 col-md-12">

                    </div> --}}
        @endforeach

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-simpan">
            {{ __('Simpan') }}
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            {{ __('Batal') }}
        </button>
    </div>
    </form>
</div>
</div>
</div>

<script type="text/javascript">
    $('#sprindik_date_edit').datepicker({
        format: 'dd-mm-yyyy'
        , autoclose: "true"
        , orientation: 'auto bottom'
        , todayHighlight: true
        , container: '#myEditModalSuratSPDP'
    });

    $('#spdp_date').datepicker({
        format: 'dd-mm-yyyy'
        , autoclose: "true"
        , orientation: 'auto bottom'
        , todayHighlight: true
        , container: '#myModalSuratSpdp'
    });

    $('#spdp_date_edit').datepicker({
        format: 'dd-mm-yyyy'
        , autoclose: "true"
        , orientation: 'auto bottom'
        , todayHighlight: true
        , container: '#myEditModalSuratSPDP'
    });

</script>
