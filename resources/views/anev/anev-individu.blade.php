@php
    $_title = 'Anev Individu';
@endphp

@extends('layouts.app')

@push('style')
    <link href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: 38px !important;
            line-height: 38px !important;
        }
    </style>
@endpush

@section('content')
    <style>
        .officer-card {
            border: none !important;
            border-top: 4px solid #1a365d !important; /* Deep navy */
            border-radius: 12px !important;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05) !important;
            background: #ffffff;
        }
        .officer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(26, 54, 93, 0.15) !important;
            border-top: 4px solid #eab308 !important; /* Gold hover accent */
        }
        .avatar-container {
            border: 2px solid #e2e8f0;
            padding: 2px;
            background: #fff;
            width: 60px;
            height: 60px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .badge-metric {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 4px;
            transition: all 0.2s ease;
        }
        .badge-metric:hover {
            background-color: #e2e8f0;
            transform: scale(1.05);
        }
        .metric-number {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }
        .metric-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .card-header-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>

    <div class="box">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark">Laporan Individu</h3>
            <div class="card">
                <div class="card-body">
                    <form class="form_anev" method="GET" action="{{ route('get_report_individu') }}">
                        @csrf
                        <div class="row col-12 mb-2">
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
                                        class="form-select select2 @error('polda_id') is-invalid @enderror">
                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                            <option value="-">Semua Polda</option>
                                        @endif
                                        @foreach ($polda as $poldas)
                                            @if(in_array($poldas->name, ['POLDA XE', 'PJR INDUK KORLANTAS', 'PUSDIKLANTAS', 'DIT GAKKUM KORLANTAS']))
                                                @continue
                                            @endif
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
                                        class="form-select select2 @error('polres_id') is-invalid @enderror">
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

                    </form>
                </div>
            </div>
        </div>


        <div class="box-body mt-3">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($accident as $index => $accidents)
                    <div class="col">
                        <div class="card h-100 officer-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3 flex-shrink-0 avatar-container">
                                        @if ($accidents->avatars == null)
                                            <img src="/image-profile/profile640/user.png" class="img-fluid rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img src="/image-profile/profile640/{{ $accidents->avatars }}" class="img-fluid rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='/image-profile/profile640/user.png';">
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-0 text-blue-dark" style="font-size: 0.95rem; color: #1e3a8a;">
                                            {{ $accidents->first_name }} {{ $accidents->last_name }}
                                        </h6>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $accidents->rank_short_name }} / {{ $accidents->id }}</small>
                                        <small class="text-blue-dark fw-semibold d-block text-primary" style="font-size: 0.75rem;">
                                            Polres: {{ $accidents->polres_name }}
                                        </small>
                                    </div>
                                </div>

                                @if ($format_start_date_then == null)
                                    <div class="p-2 rounded shadow-sm" style="font-size: 0.85rem; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="text-center fw-bold mb-2 text-secondary pb-1" style="border-bottom: 1px solid #dee2e6; color: #1e3a8a !important; font-size: 0.8rem;">
                                            Jumlah Penyelesaian Perkara (SELRA)
                                        </div>
                                        <div class="row g-2 text-center">
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number">{{ $accidents->total_p21_lalu }}</div>
                                                    <div class="metric-label">P21</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number">{{ $accidents->total_sp3_lalu }}</div>
                                                    <div class="metric-label">SP3</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number">{{ $accidents->total_diversi_lalu }}</div>
                                                    <div class="metric-label">Diversi</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="badge-metric">
                                                    <div class="metric-number">{{ $accidents->total_pom_tni_lalu }}</div>
                                                    <div class="metric-label">POM/TNI</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="badge-metric">
                                                    <div class="metric-number">{{ $accidents->total_sp2lid_lalu }}</div>
                                                    <div class="metric-label">SP2LID</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-2 rounded mb-2 shadow-sm" style="font-size: 0.85rem; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="text-center fw-bold mb-2 text-primary pb-1" style="font-size: 0.75rem; border-bottom: 1px solid #dee2e6; color: #1e3a8a !important;">
                                            Periode {{ $format_start_date_then }} s/d {{ $format_end_date_then }}
                                        </div>
                                        <div class="row g-2 text-center">
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_p21_lalu }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">P21</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_sp3_lalu }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">SP3</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_diversi_lalu }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">Div</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_pom_tni_lalu }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">POM/TNI</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_sp2lid_lalu }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">SP2LID</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 rounded shadow-sm" style="font-size: 0.85rem; background: #f0f7ff; border: 1px solid #cbd5e1;">
                                        <div class="text-center fw-bold mb-2 text-success pb-1" style="font-size: 0.75rem; border-bottom: 1px solid #cbd5e1; color: #15803d !important;">
                                            Periode {{ $format_start_date_now }} s/d {{ $format_end_date_now }}
                                        </div>
                                        <div class="row g-2 text-center">
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_p21_kini }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">P21</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_sp3_kini }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">SP3</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_diversi_kini }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">Div</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_pom_tni_kini }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">POM/TNI</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="badge-metric">
                                                    <div class="metric-number" style="font-size: 0.95rem;">{{ $accidents->total_sp2lid_kini }}</div>
                                                    <div class="metric-label" style="font-size: 0.6rem;">SP2LID</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-4 align-items-center bg-white p-3 rounded shadow-sm border">
                <div>
                    Showing
                    <strong>{{ $accident->firstItem() }}</strong>
                    to
                    <strong>{{ $accident->lastItem() }}</strong>
                    of
                    <strong>{{ $accident->total() }}</strong>
                </div>
                <div>
                    {{ $accident->links() }}
                </div>
            </div>
        </div>

    </div>

    @push('script')
        <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            });

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
                $('#polres_id').trigger('change');
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
                    $('#polres_id').trigger('change');
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
