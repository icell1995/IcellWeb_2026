{{-- <div id="myModalSuratTugas" name="myModalSuratTugas" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Masukan Petugas Untuk Perintah Tugas</h3>
            </div>

            <form action="{{ route('add_surat_tugas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_surat_tugas" name="accident_id_surat_tugas" type="text" value="{{$id}}"
                        hidden>
                    <button class="btn btn-add" type="button" name="add_officer" id="add_officer">Tambah Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="add_surat_tugas" style="display: table">

                    </div>
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
</div> --}}

<div id="myModalSuratTugas" name="myModalSuratTugas" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Surat Perintah Tugas</h3>
            </div>

            <form action="{{ route('add_surat_tugas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_springas" name="accident_id_springas" type="text"
                        value="{{ $id }}" hidden>

                    <div class="row">
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">No LP</span>
                                <input type="text" class="form-control" id="no_lp_springas" name="no_lp_springas"
                                    value="{{ $no_lp }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white test font-weight-bold">No SPRINDIK</span>
                                <input id="no_sprindik" class="form-control" type="text" name="no_sprindik"
                                    value="{{ $spdik_letter_number }}" placeholder="No SPRINGAS" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">No SPRINGAS</span>
                                <input id="no_surat" class="form-control" type="text" name="no_surat"
                                    value="{{ old('no_surat') }}" required autocomplete="no_surat" autofocus
                                    placeholder="No SPRINGAS">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Tanggal SPRINGAS</span>
                                <input class="form-control datepicker" type="text" id="tanggal_springas"
                                    name="tanggal_springas" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker">
                            </div>
                        </div>
                        {{--<div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Lokasi</span>
                                <input id="lokasi" class="form-control" type="text" name="lokasi"
                                    value="{{ old('lokasi') }}" required autocomplete="lokasi" autofocus
                                    placeholder="Lokasi">
                            </div>
                        </div>--}}
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Tanggal Dimulai</span>
                                <input class="form-control datepicker" type="text" id="tanggal_dimulai"
                                    name="tanggal_dimulai" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Tanggal Berakhir</span>
                                <input class="form-control datepicker" type="text" id="tanggal_berakhir"
                                    name="tanggal_berakhir" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Pejabat Pedantandangan</span>
                                <select id="pejabat_penandatangan" class="form-control" type="text"
                                    name="pejabat_penandatangan" value="{{ old('pejabat_penandatangan') }}" required
                                    autocomplete="pejabat_penandatangan" autofocus placeholder="Pejabat Penandatangan">
                                    <option value="">Pilih Pejabat Penandatangan</option>
                                    @foreach($penandatangan as $officers)
                                        <option value="{{ $officers->id }}">
                                            {{ $officers->register_number }} || {{ $officers->full_name}} || {{$officers->position_id}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-blod">Ketua Tim</span>
                                <select id="ketua_tim" class="form-control" type="text"
                                    name="ketua_tim" value="{{ old('ketua_tim') }}" required
                                    autocomplete="ketua_tim" autofocus placeholder="Pejabat Penandatangan">
                                    <option value="">Pilih Ketua Tim</option>
                                    @foreach($officer as $ketua_tim)
                                        @if($ketua_tim->sebagai_kepala == 'KANIT LAKA' || $ketua_tim->sebagai_kepala == 'BANIT LAKA' || $ketua_tim->sebagai_kepala == 'PENYIDIK')
                                        <option value="{{ $ketua_tim->id }}" {{ old('ketua_tim') == $ketua_tim->id ? 'selected' : '' }}>
                                            {{ $ketua_tim->id }} || {{ $ketua_tim->first_name}} {{ $ketua_tim->last_name}} || {{$ketua_tim->sebagai_kepala}}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Personel Springas</span>
                                <div class="mt-1">
                                    <button class="btn btn-add" type="button" name="add_personnel" id="add_personnel">Tambah Petugas</button>

                                    <div class="sub-field col-sm-12 col-md-12" id="add_personel_springas" style="display: table">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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


<div class="modal fade" id="myEditModalSuratTugas" name="myEditModalSuratTugas" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Surat Perintah Tugas</h3>
            </div>

            <form action="{{ route('edit_surat_tugas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_springas" name="accident_id_edit_springas" type="text"
                        value="{{ $id }}" hidden>
                    @foreach ($springas as  $springass)
                    <input type="text" value="{{$springass->id}}" name="springas_id" hidden>

                    <div class="row">
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">No LP</span>
                                <input type="text" class="form-control" id="no_lp_springas" name="no_lp_springas"
                                    value="{{ $no_lp }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white test font-weight-bold">No SPRINDIK</span>
                                <input id="no_sprindik" class="form-control" type="text" name="no_sprindik"
                                    value="{{ $spdik_letter_number }}" placeholder="No SPRINGAS" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">No SPRINGAS</span>
                                <input id="no_surat" class="form-control" type="text" name="no_surat"
                                    value="{{$springass->no_surat}}" required autocomplete="no_surat" autofocus
                                    placeholder="No SPRINGAS">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Tanggal SPRINGAS</span>
                                <input class="form-control datepicker" type="text" id="edit_tanggal_springas"
                                    name="tanggal_springas" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker" value="{{Carbon\Carbon::parse($springass->tanggal_springas)->format('d-m-Y')}}">
                            </div>
                        </div>
                        {{-- <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Lokasi</span>
                                <input id="lokasi" class="form-control" type="text" name="lokasi"
                                    value="{{ $springass->lokasi }}" required autocomplete="lokasi" autofocus
                                    placeholder="Lokasi">
                            </div>
                        </div> --}}
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Tanggal Dimulai</span>
                                <input class="form-control datepicker" type="text" id="edit_tanggal_dimulai"
                                    name="tanggal_dimulai" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker" value="{{Carbon\Carbon::parse($springass->tanggal_dimulai)->format('d-m-Y')}}">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Tanggal Berakhir</span>
                                <input class="form-control datepicker" type="text" id="edit_tanggal_berakhir"
                                    name="tanggal_berakhir" placeholder="dd/mm/yyyy" autocomplete="off"
                                    data-provide="datepicker" value="{{Carbon\Carbon::parse($springass->tanggal_berakhir)->format('d-m-Y')}}"">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Pejabat Pedantandangan</span>
                                <select id="pejabat_penandatangan" class="form-control" type="text"
                                    name="pejabat_penandatangan" value="{{ old('pejabat_penandatangan') }}" required
                                    autocomplete="pejabat_penandatangan" autofocus placeholder="Pejabat Penandatangan">
                                    <option value="">Pilih Pejabat Penandatangan</option>
                                    @foreach($penandatangan as $officers)
                                        <option value="{{ $officers->id }}">
                                            {{ $officers->register_number }} || {{ $officers->full_name}} || {{$officers->position_id}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-blod">Ketua Tim</span>
                                <select id="ketua_tim" class="form-control" type="text"
                                    name="ketua_tim" value="{{ old('ketua_tim') }}" required
                                    autocomplete="ketua_tim" autofocus placeholder="Pejabat Penandatangan">
                                    <option value="">Pilih Ketua Tim</option>
                                    @foreach($officer as $ketua_tim)
                                        @if($ketua_tim->sebagai_kepala == 'KANIT LAKA' || $ketua_tim->sebagai_kepala == 'BANIT LAKA' || $ketua_tim->sebagai_kepala == 'PENYIDIK')
                                        <option value="{{ $ketua_tim->id }}" {{ old('ketua_tim') == $ketua_tim->id ? 'selected' : '' }}>
                                            {{ $ketua_tim->id }} || {{ $ketua_tim->first_name}} {{ $ketua_tim->last_name}} || {{$ketua_tim->sebagai_kepala}}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <div class="form-group">
                                <span class="text-white font-weight-bold">Personel Springas</span>
                                <div class="mt-1">
                                    <button class="btn btn-add" type="button" name="add_personnel_edit" id="add_personnel_edit">Tambah Petugas</button>

                                    <div class="sub-field col-sm-12 col-md-12" id="edit_personel_springas" style="display: table">
                                        @foreach($springas_officer as $tugas)
                                        <div class="add-row row">
                                            <div class="input-group col-lg-11">
                                                <select id="edit_officer_surat_tugas[]" name="edit_officer_surat_tugas[]"
                                                    class="form-control">
                                                    <option value="">Pilih Petugas</option>
                                                    @foreach ($officer as $officers )

                                                    <option value="{{$officers->id}}"
                                                        {{ $officers->id == $tugas->id ?'selected' : '' }}>
                                                        {{$officers->id}} - {{$officers->first_name}} {{$officers->last_name}}
                                                    </option>

                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="remove col-lg-1">
                                                <button type="button" name="remove" class="btn btn_removeX btn_remove_edit_officer">X</button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


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

{{-- <div class="modal fade" id="myEditModalSuratTugas" name="myEditModalSuratTugas" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    @csrf
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title">Edit Perintah Surat Tugas</h3>
            </div>

            <form action="{{ route('edit_surat_tugas') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input id="accident_id_edit_surat_tugas" name="accident_id_edit_surat_tugas" type="text"
                        value="{{ $id }}" hidden>
                    <button class="btn btn-add" type="button" name="add_officer_edit" id="add_officer_edit">Tambah
                        Petugas</button>

                    <div class="sub-field col-sm-12 col-md-12" id="edit_surat_tugas" style="display: table">
                        @foreach ($springas as $tugas)
                            <div class="add-row row">
                                <div class="input-group col-lg-11">
                                    <select id="edit_officer_surat_tugas[]" name="edit_officer_surat_tugas[]"
                                        class="form-control">
                                        <option value="">Pilih Petugas</option>
                                        @foreach ($officer as $officers)
                                            <option value="{{ $officers->id }}"
                                                {{ $officers->id == $tugas->id ? 'selected' : '' }}>
                                                {{ $officers->id }} - {{ $officers->first_name }}
                                                {{ $officers->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="remove col-lg-1">
                                    <button type="button" name="remove"
                                        class="btn btn_removeX btn_remove_edit_officer">X</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
</div> --}}

<script type="text/javascript">
    $('#tanggal_springas').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myModalSuratTugas'
    });
    $('#tanggal_dimulai').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myModalSuratTugas'
    });
    $('#tanggal_berakhir').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myModalSuratTugas'
    });

    $('#edit_tanggal_springas').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myEditModalSuratTugas'
    });
    $('#edit_tanggal_dimulai').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myEditModalSuratTugas'
    });
    $('#edit_tanggal_berakhir').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: "true",
        orientation: 'auto bottom',
        container: '#myEditModalSuratTugas'
    });

    $('#add_personnel').click(function() {
        addRowPersonnel();
    });

    $('#add_personnel_edit').click(function() {
        addRowEditPersonnel();
    });

    function addRowPersonnel(){
        var div ='<div class="row add-row">'+
                        '<div class="input-group col-lg-11">'+
                            '<select id="officer_id[]" name="officer_id[]" class="form-control" required>'+
                            '<option value="" }} class="option">Pilih Petugas</option>'+
                            '@foreach($officer as $officers)'+
                            '<option value="{{$officers->id}}"'+
                            '{{ old('officer') == $officers->id ?'selected' : '' }}>'+
                            '{{$officers->id}} || {{$officers->first_name}} {{$officers->last_name}}</option>'+
                            '@endforeach'+
                            '</select>'+
                        '</div>'+
                        '<div class="remove col-lg-1">'+
                            '<button type="button" name="remove" class="btn btn_removeX btn_remove_officer">X</button>'+
                        '</div>'+
                    '</div>'

        $('#add_personel_springas').append(div);
    };

    function addRowEditPersonnel(){
        var div ='<div class="row add-row">'+
                        '<div class="input-group col-lg-11">'+
                            '<select id="edit_officer_surat_tugas[]" name="edit_officer_surat_tugas[]" class="form-control" required>'+
                            '<option value="" }} class="option">Pilih Petugas</option>'+
                            '@foreach($officer as $officers)'+
                            '<option value="{{$officers->id}}"'+
                            '{{ old('officer') == $officers->id ?'selected' : '' }}>'+
                            '{{$officers->id}} || {{$officers->first_name}} {{$officers->last_name}}</option>'+
                            '@endforeach'+
                            '</select>'+
                        '</div>'+
                        '<div class="remove col-lg-1">'+
                            '<button type="button" name="remove" class="btn btn_removeX btn_remove_officer">X</button>'+
                        '</div>'+
                    '</div>'

        $('#edit_personel_springas').append(div);
    };

    $(document).on('click', '.btn_remove_officer', function() {
        var test = $('#add_personel_springas div.add-row').length;
        if (test == 1) {
            alert("You Can not Remove Last Row");
        } else {
            $(this).parent().parent().remove();
        }
    });

    $(document).on('click', '.btn_remove_edit_officer', function() {
        var test = $('#edit_personel_springas div.add-row').length;
        if (test == 1) {
            alert("You Can not Remove Last Row");
        } else {
            $(this).parent().parent().remove();
        }
    });

</script>
