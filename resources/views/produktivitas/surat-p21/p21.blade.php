<div class="accordion-item p21-section d-none">
    <div class="item">
        <a class="header-item bg-warning">
            13. Pengiriman Berkas Perkara (P21)
            <i class="fa fa-angle-right dropdown-side"></i>
            {{--<div class="progress13 progress-bar-none">
                <div id="kategori13" class="progress-bar bg-success kategori13" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{$TotalKategori13}}%</div>
            </div>--}}
        </a>

        <div class="item-content">
            <div class="row row-p21-tahap-1">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">P21 Tahap 1</a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-2">
                    @if ($surat_p21_tahap_1==null)
                    <i class="fa fa-pencil-square-o" id="surat_p21_1" name="surat_p21_1"></i>
                    @else
                    {{-- <a target="_blank" href="/surat-p21-tahap-1/{{$id}}" id="">Lihat</a></span> --}}
                    <a href=# class="" id="edit_p21_tahap_1" name="edit_p21_tahap_1">Edit</a>
                    <a target="_blank" href="{{ url('produktivitas/view-surat-p21-tahap-1')}}?accident_id={{ $id }}" id="lihat_surat_tugas">Lihat</a></span>
                    <form action="/surat-p21-tahap-1/{{$id}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="row row-p21-tahap-2 d-none">
                <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                    <a class="item-action">P21 Tahap 2</a>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-2">
                    @if ($surat_p21_tahap_2==null)
                    <i class="fa fa-pencil-square-o" id="surat_p21_2" name="surat_p21_2"></i>
                    @else
                    {{-- <a target="_blank" href="/surat-p21-tahap-2/{{$id}}" id="">Lihat</a></span> --}}
                    <a href=# class="" id="edit_p21_tahap_2" name="edit_p21_tahap_2">Edit</a>
                    <a target="_blank" href="{{ url('produktivitas/view-surat-p21-tahap-2')}}?accident_id={{ $id }}" id="lihat_surat_tugas">Lihat</a></span>
                    <form action="/surat-p21-tahap-2/{{$id}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" style="color: #007bff; border: none; background: none;
                        font-weight: bold; padding: 10px;">DELETE</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@include('produktivitas.surat-p21.modal.modal')

@push('script')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>

<script type="text/javascript">
$(document).ready(function(){
    // Check is p21_tahap2_1 checked
    if ($('#p21_tahap2_1').is(':checked')) {
        $('.row-p21-tahap-2').removeClass('d-none');
    }

    $("#surat_p21_1").click(function(){
        $("#myModalP21Tahap1").modal('show');
    });
    $('#edit_p21_tahap_1').click(function(){
        // GET DATA FROM DB TO MODAL WITH AJAX
        $.ajax({
            url: "{{ url('produktivitas/json-surat-p21-tahap-1')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "accident_id": "{{$id}}"
            },
            dataType: "json",
            success: function(data) {
                var ccResponse = JSON.parse(data.cc);

                $('#edit_p21t1_accident_id_p21_tahap_1').val(data.accident_id);
                $('#edit_p21t1_province').val(data.province_name);
                $('#edit_p21t1_polres').val(data.polres_name);
                $('#edit_p21t1_polres_address').val(data.polres_address);
                $('#edit_p21t1_no_p21').val(data.no_p21);
                $('#edit_p21t1_p21_date').val(data.p21_date);
                $('#edit_p21t1_p21_location').val(data.p21_location);
                $('#edit_p21t1_classification').val(data.classification);
                $('#edit_p21t1_attachment').val(data.attachment);
                $('#edit_p21t1_subject').val(data.subject);
                $('#edit_p21t1_letter_recipient').val(data.letter_recipient);
                $('#edit_p21t1_recipient_location').val(data.recipient_location);
                $('#edit_p21t1_no_spdp').val(data.no_spdp);
                $('#edit_p21t1_spdp_date').val(data.spdp_date);
                $('#edit_p21t1_no_lp').val(data.no_lp);
                $('#edit_p21t1_accident_date').val(data.accident_date);
                $('#edit_p21t1_suspects').val(data.suspects);
                $('#edit_p21t1_incident_description').val(data.description);
                $('#edit_p21t1_penyidik_name').val(data.penyidik_name);
                $('#edit_p21t1_penyidik_nrp').val(data.penyidik_nrp);
                $('#edit_p21t1_penyidik_position').val(data.penyidik_position);

                var container = $('#edit-cc-container-p21-tahap-1');
                container.empty();

                for (var i = 0; i < ccResponse.length; i++) {
                    var tembusan = ccResponse[i];
                    var newDiv = $('<div class="input-group mb-2">');
                    var input = $('<input>').attr({
                        type: 'text',
                        class: 'form-control',
                        name: 'cc[]',
                        value: tembusan
                    });

                    if(i == 0){
                        $('#edit-cc-container-p21-tahap-1').append('<label for="cc">Tembusan:</label>');
                        var button = $('<button>').attr({
                            type: 'button',
                            class: 'btn btn-secondary edit-add-cc-p21-tahap-1',
                            id: 'edit-add-cc-p21-tahap-1'
                        }).text('+');
                    }else{
                        var button = $('<button>').attr({
                            type: 'button',
                            class: 'btn btn-secondary remove-cc-p21-tahap-1'
                        }).text('-');
                    }

                    newDiv.append(input).append(button);
                    container.append(newDiv);
                }
            }
        });

        $("#myEditModalP21Tahap1").modal('show');
    });

    $("#surat_p21_2").click(function(){
        $("#myModalP21Tahap2").modal('show');
    });
    $('#edit_p21_tahap_2').click(function(){
        // GET DATA FROM DB TO MODAL WITH AJAX
        $.ajax({
            url: "{{ url('produktivitas/json-surat-p21-tahap-2')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "accident_id": "{{$id}}"
            },
            dataType: "json",
            success: function(data) {
                var ccResponse = JSON.parse(data.cc);

                $('#edit_p21t2_accident_id_p21_tahap_2').val(data.accident_id);
                $('#edit_p21t2_province').val(data.province_name);
                $('#edit_p21t2_polres').val(data.polres_name);
                $('#edit_p21t2_polres_address').val(data.polres_address);
                $('#edit_p21t2_no_p21').val(data.no_p21);
                $('#edit_p21t2_p21_date').val(data.p21_date);
                $('#edit_p21t2_p21_start_date').val(data.p21_start_date);
                $('#edit_p21t2_p21_location').val(data.p21_location);
                $('#edit_p21t2_classification').val(data.classification);
                $('#edit_p21t2_attachment').val(data.attachment);
                $('#edit_p21t2_subject').val(data.subject);
                $('#edit_p21t2_letter_recipient').val(data.letter_recipient);
                $('#edit_p21t2_recipient_location').val(data.recipient_location);
                $('#edit_p21t2_evidences').val(data.evidences);
                $('#edit_p21t2_no_lp').val(data.no_lp);
                $('#edit_p21t2_accident_date').val(data.accident_date);
                $('#edit_p21t2_suspects').val(data.suspects);
                $('#edit_p21t2_incident_description').val(data.description);
                $('#edit_p21t2_penyidik_name').val(data.penyidik_name);
                $('#edit_p21t2_penyidik_nrp').val(data.penyidik_nrp);
                $('#edit_p21t2_penyidik_position').val(data.penyidik_position);

                var container = $('#edit-cc-container-p21-tahap-2');
                container.empty();

                for (var i = 0; i < ccResponse.length; i++) {
                    var tembusan = ccResponse[i];
                    var newDiv = $('<div class="input-group mb-2">');
                    var input = $('<input>').attr({
                        type: 'text',
                        class: 'form-control',
                        name: 'cc[]',
                        value: tembusan
                    });

                    if(i == 0){
                        $('#edit-cc-container-p21-tahap-2').append('<label for="cc">Tembusan:</label>');
                        var button = $('<button>').attr({
                            type: 'button',
                            class: 'btn btn-secondary edit-add-cc-p21-tahap-2',
                            id: 'edit-add-cc-p21-tahap-2'
                        }).text('+');
                    }else{
                        var button = $('<button>').attr({
                            type: 'button',
                            class: 'btn btn-secondary remove-cc-p21-tahap-2'
                        }).text('-');
                    }

                    newDiv.append(input).append(button);
                    container.append(newDiv);
                }

                // ccResponse.forEach(function(tembusan) {
                //     var newDiv = $('<div>');
                //     var input = $('<input>').attr({
                //         type: 'text',
                //         class: 'form-control',
                //         name: 'cc[]',
                //         value: tembusan
                //     });
                //     var button = $('<button>').attr({
                //         type: 'button',
                //         class: 'btn btn-secondary remove-cc-p21-tahap-2'
                //     }).text('-');

                //     newDiv.append(input).append(button);
                //     container.append(newDiv);
                // });
            }
        });

        $("#myEditModalP21Tahap2").modal('show');
    });

    $(".kategori13").css("width","{{$TotalKategori13}}%")
    $(".progress5").hide();
//    if({{$TotalKategori13}}>=20 && {{$TotalKategori13}}<=60){
//     $(".progress13").show();
//     document.getElementById("kategori13").classList.add("bg-warning")
//    }else if( {{$TotalKategori13}}>0 && {{$TotalKategori13}}<20){
//     $(".progress13").show();
//     document.getElementById("kategori13").classList.add("bg-danger")
//    }else if({{$TotalKategori13}}>=60 && {{$TotalKategori13}}<=85){
//     $(".progress13").show();
//     document.getElementById("kategori13").classList.add("bg-info")
//    }else if({{$TotalKategori13}}>85){
//     $(".progress13").show();
//     document.getElementById("kategori13").classList.add("bg-success")
//    }
    
    // $('.select2').select2(function(){
    //     console.log('select2')
    // });
});

$('#selra_flag').on('change', function() {
    if ( this.value == 'S0101')
    {
        $('.p21-section').removeClass('d-none');
    }else{
        $('.p21-section').addClass('d-none');
    }
});

$('#p21_tahap2_1').on('click', function() {
    if ( this.checked == true)
    {
        $('.row-p21-tahap-2').removeClass('d-none');
    }else{
        $('.row-p21-tahap-2').addClass('d-none');
    }
});


$('.add-cc-p21-tahap-1').on('click', function() {
    $('#cc-container-p21-tahap-1').append('<div class="input-group mb-2"><input type="text" class="form-control" name="cc[]" placeholder="Enter Tembusan"><button type="button" class="btn btn-secondary remove-cc-p21-tahap-1">-</button></div>');
});

$('#cc-container-p21-tahap-1').on('click', '.remove-cc-p21-tahap-1', function() {
    $(this).parent().remove();
});

$('.add-cc-p21-tahap-2').on('click', function() {
    $('#cc-container-p21-tahap-2').append('<div class="input-group mb-2"><input type="text" class="form-control" name="cc[]" placeholder="Enter Tembusan"><button type="button" class="btn btn-secondary remove-cc-p21-tahap-2">-</button></div>');
});

$('#cc-container-p21-tahap-2').on('click', '.remove-cc-p21-tahap-2', function() {
    $(this).parent().remove();
});

$('#edit-cc-container-p21-tahap-1').on('click', '#edit-add-cc-p21-tahap-1', function() {
    $('#edit-cc-container-p21-tahap-1').append('<div class="input-group mb-2"><input type="text" class="form-control" name="cc[]" placeholder="Enter Tembusan"><button type="button" class="btn btn-secondary remove-cc-p21-tahap-1">-</button></div>');
});

$('#edit-cc-container-p21-tahap-1').on('click', '.remove-cc-p21-tahap-1', function() {
    $(this).parent().remove();
});

$('#edit-cc-container-p21-tahap-2').on('click', '#edit-add-cc-p21-tahap-2', function() {
    $('#edit-cc-container-p21-tahap-2').append('<div class="input-group mb-2"><input type="text" class="form-control" name="cc[]" placeholder="Enter Tembusan"><button type="button" class="btn btn-secondary remove-cc-p21-tahap-2">-</button></div>');
});

$('#edit-cc-container-p21-tahap-2').on('click', '.remove-cc-p21-tahap-2', function() {
    $(this).parent().remove();
});

// Add Tersangka Button
$('#add-tersangka-p21-tahap-1').on('click', function() {
    // show myModalTersangka modal
    $('#myModalTersangka').modal({
        show: true,
    });
    $('#myModalTersangka').on('shown.bs.modal', function (e) {
        // $('#myModalTersangka').css('z-index', '9999');
        $('#myModalP21Tahap1').modal('hide');
    });
    $('#myModalTersangka').on('hidden.bs.modal', function (e) {
        $('#myModalP21Tahap1').modal('show');
    });
});
// edit add tersangka
$('#edit-add-tersangka-p21-tahap-1').on('click', function() {
    // show myModalTersangka modal
    $('#myModalTersangka').modal({
        show: true,
    });
    $('#myModalTersangka').on('shown.bs.modal', function (e) {
        // $('#myModalTersangka').css('z-index', '9999');
        $('#myEditModalP21Tahap1').modal('hide');
    });
    $('#myModalTersangka').on('hidden.bs.modal', function (e) {
        $('#myEditModalP21Tahap1').modal('show');
    });
});

// Add Tersangka Button
$('#add-tersangka-p21-tahap-2').on('click', function() {
    // show myModalTersangka modal
     // show myModalTersangka modal
     $('#myModalTersangka').modal({
        show: true,
    });
    $('#myModalTersangka').on('shown.bs.modal', function (e) {
        // $('#myModalTersangka').css('z-index', '9999');
        $('#myModalP21Tahap2').modal('hide');
    });
    $('#myModalTersangka').on('hidden.bs.modal', function (e) {
        $('#myModalP21Tahap2').modal('show');
    });
});
// edit add tersangka
$('#edit-add-tersangka-p21-tahap-2').on('click', function() {
    console.log('test');
     // show myModalTersangka modal
     $('#myModalTersangka').modal({
        show: true,
    });
    $('#myModalTersangka').on('shown.bs.modal', function (e) {
        // $('#myModalTersangka').css('z-index', '9999');
        $('#myEditModalP21Tahap2').modal('hide');
    });
    $('#myModalTersangka').on('hidden.bs.modal', function (e) {
        $('#myEditModalP21Tahap2').modal('show');
    });
});

// Add Barang Bukti Button
$('#add-barang-bukti-p21-tahap-2').on('click', function() {
     // show myModalTersangka modal
     $('#myModalPenyitaan3').modal({
        show: true,
    });
    $('#myModalPenyitaan3').on('shown.bs.modal', function (e) {
        // $('#myModalTersangka').css('z-index', '9999');
        $('#myModalP21Tahap2').modal('hide');
    });
    $('#myModalPenyitaan3').on('hidden.bs.modal', function (e) {
        $('#myModalP21Tahap2').modal('show');
    });
});
// edit add barang bukti
$('#edit-add-barang-bukti-p21-tahap-2').on('click', function() {
     // show myModalTersangka modal
     $('#myModalPenyitaan3').modal({
        show: true,
    });
    $('#myModalPenyitaan3').on('shown.bs.modal', function (e) {
        // $('#myModalTersangka').css('z-index', '9999');
        $('#myEditModalP21Tahap2').modal('hide');
    });
    $('#myModalPenyitaan3').on('hidden.bs.modal', function (e) {
        $('#myEditModalP21Tahap2').modal('show');
    });
});
</script>
@endpush
