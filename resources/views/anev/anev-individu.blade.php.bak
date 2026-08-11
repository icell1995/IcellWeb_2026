@php
    $_title = 'Anev Individu';
@endphp

@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark">Laporan Individu</h3>

            <div class="card">
                <div class="card-body">
                    <form class="form_anev" method="GET" action="{{ route('get_report_individu') }}">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <fieldset class="border p-2">
                                    <legend class="fw-bold text-blue-dark"> Periode Lalu </legend>
                                    <div class="mb-3">
                                        <span class="fw-bold">Dari Tanggal <span class="font-red">*</span></span>
                                        <input class="form-control" type="text" id="date_from" name="date_from"
                                            placeholder="DD - MM - YYYY" autocomplete="off">
                                    </div>
                                    <div class="mb-3">
                                        <span class="fw-bold">Hingga Tanggal <span class="font-red">*</span></span>
                                        <input class="form-control" type="text" id="date_to" name="date_to"
                                            placeholder="DD - MM - YYYY" autocomplete="off">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <fieldset class="border p-2">
                                    <legend class="fw-bold text-blue-dark"> Periode Ini </legend>
                                    <div class="mb-3">
                                        <span class="fw-bold">Dari Tanggal <span class="font-red">*</span></span>
                                        <input class="form-control" type="text" id="date_from_now" name="date_from_now"
                                            placeholder="DD - MM - YYYY" autocomplete="off">
                                    </div>
                                    <div class="mb-3">
                                        <span class="fw-bold">Hingga Tanggal <span class="font-red">*</span></span>
                                        <input class="form-control" type="text" id="date_to_now" name="date_to_now"
                                            placeholder="DD - MM - YYYY" autocomplete="off">
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                                <div class="mb-3">
                                    <span class="fw-bold">Polda</span>
                                    <select id="polda_id" name="polda_id"
                                        class="form-select @error('polda_id') is-invalid @enderror">
                                        @if (Auth::user()->role_id == 1)
                                            <option value="-">Semua Polda</option>
                                        @endif
                                        @foreach ($polda as $poldas)
                                            <option value="{{ $poldas->id }}"
                                                {{ old('polda_id') == $poldas->id ? 'selected' : '' }}>
                                                {{ $poldas->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                                <div class="mb-3">
                                    <span class="fw-bold">Polres</span>
                                    <select id="polres_id" name="polres_id"
                                        class="form-select @error('polres_id') is-invalid @enderror">
                                        @if (Auth::user()->role_id != 3)
                                            <option value="-">Pilih Polres</option>
                                        @endif
                                        @foreach ($polres as $polress)
                                            <option value="{{ $polress->id }}"
                                                {{ old('polres_id') == $polress->id ? 'selected' : '' }}>
                                                {{ $polress->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" id="button_individu" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <button id="btn-export" class="btn btn-secondary rounded-pill btn-export fw-bold"
                                type="submit"><span><i class="bi bi-download"></i></span> Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="box-body mt-3">
            <div class="card">
                <div class="card-body">
                    @foreach ($accident as $index => $accidents)
                        <?php $no = 0; ?>
                        <?php $no++; ?>
                        <div class="individu-column">
                            <span class="me-2 fw-bold">{{ $accident->perPage() - $accident->perPage() + ($index + 1) }}.</span>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                                    @if ($accidents->avatars == null)
                                        <img src="/image-profile/profile640/user.png"
                                            style="width:70%; border-radius:50%; padding: 0;">
                                    @else
                                        <img src="/image-profile/profile640/{{ $accidents->avatars }}"
                                            style="width:70%; border-radius:50%; padding: 0;">
                                    @endif
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                    <div class="d-flex justify-content-between mb-1">
                                        <label class="fw-bold mx-2 col-3">Nama</label>
                                        <span>:</span>
                                        <label class="ms-2 col-8 text-start fw-bold" for="">{{ $accidents->first_name }}
                                            {{ $accidents->last_name }}</label>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <label class="fw-bold mx-2 col-3">Pangkat</label>
                                        <span>:</span>
                                        <label class="ms-2 col-8 text-start fw-bold"
                                            for="">{{ $accidents->rank_short_name }}</label>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <label class="fw-bold mx-2 col-3">NRP</label>
                                        <span>:</span>
                                        <label class="ms-2 col-8 text-start fw-bold" for="">{{ $accidents->id }}</label>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <label class="fw-bold mx-2 col-3">POLRES</label>
                                        <span>:</span>
                                        <label class="ms-2 col-8 text-start fw-bold"
                                            for="">{{ $accidents->polres_name }}</label>
                                    </div>
                                </div>

                                @if ($format_start_date_then == null)
                                    <div class="col-lg-7 col-md-7 col-sm-12 col-12">
                                        <div class="col-12">
                                            <div class="text-center">
                                                <div class="label-table">
                                                    <label for="">Jumlah Laka Sidik</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_p21_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">P21</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_sp3_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">SP3</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_diversi_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">Diversi</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_pom_tni_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">POM/TNI</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_rj_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">RJ</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_dalam_proses_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">DLM PROSES</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_sp2lid_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">SP2LID</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-lg-7 col-md-7 col-sm-12 col-12">
                                        <div class="col-12">
                                            <div class="text-center">
                                                <div class="label-table">
                                                    <label for="">Jumlah Laka Sidik Periode
                                                        {{ $format_start_date_then }} sampai
                                                        {{ $format_end_date_then }}</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_p21_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">P21</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_sp3_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">SP3</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_diversi_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">Diversi</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_pom_tni_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">POM/TNI</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_rj_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">RJ</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_dalam_proses_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">DLM PROSES</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_sp2lid_lalu }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">SP2LID</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="text-center pt-1">
                                                <div class="label-table2">
                                                    <label for="">Jumlah Laka Sidik Periode
                                                        {{ $format_start_date_now }} sampai
                                                        {{ $format_end_date_now }}</label>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_p21_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">P21</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_sp3_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">SP3</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_diversi_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">Diversi</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_pom_tni_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">POM/TNI</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_rj_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">RJ</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_dalam_proses_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">DLM PROSES</label>
                                                        </div>
                                                    </div>
                                                    <div class="label">
                                                        <div class="box-item">
                                                            <label>{{ $accidents->total_sp2lid_kini }}</label>
                                                        </div>
                                                        <div class="box-label">
                                                            <label for="">SP2LID</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-between mt-3">
                        <div class="mx-2">
                            Showing
                            <strong>{{ $accident->firstItem() }}</strong>
                            to
                            <strong>{{ $accident->lastItem() }}</strong>
                            of
                            <strong>{{ $accident->total() }}</strong>
                        </div>
                        <div class="mx-2">
                            {{ $accident->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('script')
        <script type="text/javascript">
            $('#date_from').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: "true",
                orientation: 'auto bottom'
            });
            $('#date_to').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: "true",
                orientation: 'auto bottom'
            });
            $('#date_from_now').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: "true",
                orientation: 'auto bottom'
            });
            $('#date_to_now').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: "true",
                orientation: 'auto bottom'
            });

            $(document).ready(function() {
                var get_pol = $('#polda_id').val();
                if (get_pol == null || get_pol == '-') {
                    $('#polres_id').prop('disabled', true);
                } else {
                    $('#polres_id').prop('disabled', false);
                }
            });

            $("#btn-export").click(function(e) {
                e.preventDefault();
                var polda = $("#polda_id").val();
                var polres = $('#polres_id').val();
                var start_date_then = $('#date_from').val();
                var end_date_then = $('#date_to').val();
                var start_date_now = $('#date_from_now').val();
                // alert(start_date_then);
                var end_date_now = $('#date_to_now').val();
                window.location = "{{ route('export_report_individu') }}" + '?polda=' + polda + '&polres=' + polres +
                    '&start_date_then=' + start_date_then + '&end_date_then=' + end_date_then + '&start_date_now=' +
                    start_date_now + '&end_date_now=' + end_date_now;

            });
            $("#button_individu").click(function(e) {
                var polda = $("#polda_id").val();
                var polres = $('#polres_id').val();
                var start_date_then = $('#date_from').val();
                var end_date_then = $('#date_to').val();
                var start_date_now = $('#date_from_now').val();
                var end_date_now = $('#date_to_now').val();
            });

            $('#polda_id').on('change', function(event) {
                event.preventDefault();
                var poldaId = $(this).val();
                $('#polres_id').prop('disabled', true);
                $('#polres_id').empty();
                $('#polres_id').append('<option value="">Pilih Polres</option>');
                if (!poldaId) {
                    return;
                }

                $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

                    $('#polres_id').empty()
                    var option = '<option value="">Pilih Polres</option>';
                    $('#polres_id').append(option);

                    $.each(data, function(key, polres) {
                        var id = polres.id;
                        var name = polres.name;
                        var option = '<option value="' + id + '">' + name + '</option>';

                        $('#polres_id').append(option);
                    });

                    $('#polres_id').prop('disabled', false);
                });
            });


            //     $("#button_individu").click(function(e){
            //         e.preventDefault();
            //         var _token = $("input[name='_token']").val();
            // 		var date_from = $('#date_from').val();
            // 		var date_to = $('#date_to').val();
            // 		var date_from_now = $('#date_from_now').val();
            // 		var date_to_now = $('#date_to_now').val();
            //         var polda = $('#polda_id').val();
            //         var polres = $('#polres_id').val();

            // 		// clearAnev();

            //        $.ajax({
            //         url: "{{ route('get_report_individu') }}",
            //         type: "GET",
            //         data : {
            //                 _token:_token,
            //                 date_from:date_from,
            //                 date_to:date_to,
            //                 date_from_now:date_from_now,
            //                 date_to_now:date_to_now,
            //                 polda:polda,
            //                 polres:polres,
            // 			  },
            // 		success: function(response) {

            // 		var accident = response.data
            //         $('#box-body').html(accident)

            //       }
            //     });
            //    });
        </script>
    @endpush
@endsection
