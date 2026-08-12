@php
    $_title = 'Anev';
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
    <div class="box box-info">
        <div class="box-header">
            <h3 class="fw-bold text-blue-dark">ANEV</h3>
        </div>

        <div class="box-body">
            <div class="card mb-4">
                <div class="card-body" style="overflow: auto;">
                    <form class="form_anev" action="{{ route('export_report_anev') }}" method="GET">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <fieldset class="border p-2">
                                    <legend class="fw-bold text-blue-dark">Periode Lalu</legend>
                                    <div class="mb-3">
                                        <span class="fw-bold">Dari Tanggal <span class="text-danger">*</span></span>
                                        <input class="form-control" type="text" id="date_from" name="date_from"
                                            placeholder="DD - MM - YYYY" autocomplete="off" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <span class="fw-bold">Hingga Tanggal <span class="text-danger">*</span></span>
                                        <input class="form-control" type="text" id="date_to" name="date_to"
                                            placeholder="DD - MM - YYYY" autocomplete="off" readonly>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <fieldset class="border p-2">
                                    <legend class="fw-bold text-blue-dark">Periode Kini</legend>
                                    <div class="mb-3">
                                        <span class="fw-bold">Dari Tanggal <span class="text-danger">*</span></span>
                                        <input class="form-control" type="text" id="date_from_now" name="date_from_now"
                                            placeholder="DD - MM - YYYY" autocomplete="off" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <span class="fw-bold">Hingga Tanggal <span class="text-danger">*</span></span>
                                        <input class="form-control" type="text" id="date_to_now" name="date_to_now"
                                            placeholder="DD - MM - YYYY" autocomplete="off" readonly>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                                <div class="mb-3">
                                    <span class="fw-bold">Polda</span>
                                    @php
                                        $roleId = Auth::user()->role_id;
                                        $userPoldaId = Auth::user()->polda_id;
                                    @endphp
                                    @if ($roleId == 1 || ($roleId == 2 && (is_null($userPoldaId) || $userPoldaId === '' || $userPoldaId === '0' || $userPoldaId == '-')))
                                        <select id="polda_id" name="polda" class="form-select select2 @error('polda') is-invalid @enderror">
                                            <option value="-" }}>Semua Polda</option>
                                            @foreach ($polda as $poldas)
                                                @if(in_array($poldas->name, ['POLDA XE', 'PJR INDUK KORLANTAS', 'PUSDIKLANTAS', 'DIT GAKKUM KORLANTAS']))
                                                    @continue
                                                @endif
                                                <option value="{{ $poldas->id }}" {{ old('polda_id') == $poldas->id ? 'selected' : '' }}>{{ $poldas->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select id="polda_id" name="polda" class="form-select" disabled>
                                            @foreach ($polda as $poldas)
                                                @if ($poldas->id == $userPoldaId)
                                                    <option value="{{ $poldas->id }}" selected>{{ $poldas->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="polda" value="{{ $userPoldaId }}">
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                                <div class="mb-3">
                                    <span class="fw-bold">Polres</span>
                                    @php
                                        $roleId = Auth::user()->role_id;
                                        $userPolresId = Auth::user()->polres_id;
                                        $userPoldaId = Auth::user()->polda_id;
                                    @endphp
                                    @if ($roleId == 1 || ($roleId == 2 && (is_null($userPoldaId) || $userPoldaId === '' || $userPoldaId === '0' || $userPoldaId == '-')))
                                        <select id="polres_id" name="polres" class="form-select select2 @error('polres') is-invalid @enderror">
                                            <option value="-">- Pilih Polres -</option>
                                            @foreach ($polres as $polress)
                                                <option value="{{ $polress->id }}" data-polda="{{ $polress->polda_id }}" {{ old('polres_id') == $polress->id ? 'selected' : '' }}>{{ $polress->name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif (($roleId == 2 && !is_null($userPoldaId) && $userPoldaId !== '') || ($roleId == 3 && (is_null($userPolresId) || $userPolresId === '')))
                                        <select id="polres_id" name="polres" class="form-select select2 @error('polres') is-invalid @enderror">
                                            <option value="-">- Pilih Polres -</option>
                                            @foreach ($polres as $polress)
                                                @if ($polress->polda_id == $userPoldaId)
                                                    <option value="{{ $polress->id }}" data-polda="{{ $polress->polda_id }}" {{ old('polres_id') == $polress->id ? 'selected' : '' }}>{{ $polress->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @elseif (($roleId == 3 && !is_null($userPolresId) && $userPolresId !== '') || $roleId == 4)
                                        @php
                                            $namaPolres = '';
                                            foreach ($polres as $polress) {
                                                if ($polress->id == $userPolresId) {
                                                    $namaPolres = $polress->name;
                                                }
                                            }
                                        @endphp
                                        <select id="polres_id" name="polres" class="form-select" disabled>
                                            <option value="{{ $userPolresId }}" selected>{{ $namaPolres }}</option>
                                        </select>
                                        <input type="hidden" name="polres" value="{{ $userPolresId }}">
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 pt-2">
                                <div class="mb-3">
                                    <span class="fw-bold">Berdasarkan</span>
                                    <select id="type" name="type" class="form-select">
                                        <option value="1">Tanggal Kejadian</option>
                                        <option value="2">Tanggal Dilaporkan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" id="button_anev" class="btn btn-primary">Cari</button>
                            </div>
                            <div id="table-anev"></div>
                            <div id="hasil_table"></div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet">
    <style>
        th {
            text-align: center;
            vertical-align: middle !important;
        }
        .periode-lalu {
            background-color: #b3d9ff !important; /* biru muda */
        }
        .periode-ini {
            background-color: #222577 !important; /* biru tua */
            color: white !important;
        }
        mark {
            background: yellow;
            padding: 0;
        }
    </style>
@endpush

@push('script')
    <script>
        // expose role_id
        window.role_id = {{ (int) Auth::user()->role_id }};
    </script>

    {{-- Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://bartaz.github.io/sandbox.js/jquery.highlight.js"></script>

    <script>
    (function ($) {
        // --- Datepicker
        function initDatepickers() {
            if (!$.fn.datepicker) return;
            $('#date_from, #date_to, #date_from_now, #date_to_now')
                .datepicker({
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    container: 'body',
                    orientation: 'auto bottom',
                    todayHighlight: true,
                    endDate: new Date()
                })
                .each(function () {
                    if (!$(this).val()) $(this).datepicker('setDate', new Date());
                });
        }

        // --- Helper tabel
        function clearTable() {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
            }
            $('#hasil_table').empty();
        }

        // --- Link Polda â†’ Polres
        $('#polda_id').on('change', function(event) {
            event.preventDefault();
            var poldaId = $(this).val();
            var $polres = $('#polres_id');

            // Cek role dan polres_id user dari window (expose di bawah)
            var roleId = window.role_id;
            var userPolresId = window.user_polres_id;
            // Level 3 Polres: role_id 3, polres_id terisi
            var isLevel3Polres = roleId == 3 && userPolresId && userPolresId !== '' && userPolresId !== 'null';
            // Level 3 Polda: role_id 3, polres_id null/kosong
            var isLevel3Polda = roleId == 3 && (!userPolresId || userPolresId === '' || userPolresId === 'null');

            // Hanya skip jika memang polres user atau level 3 polres
            var isPolresUser = roleId == 4;
            if ((isPolresUser || isLevel3Polres) && $polres.prop('disabled')) {
                // No need to initialize polres select
                return;
            }

            // Untuk polres user & level 3 polres, polres tetap disabled, selain itu enabled
            if (isPolresUser || isLevel3Polres) {
                $polres.prop('disabled', true).empty();
            } else {
                $polres.prop('disabled', false).empty();
            }
            $polres.trigger('change');

            // Jika semua polda ('-' atau kosong), kunci polres
            if (!poldaId || poldaId === '-') {
                $polres.append('<option value="-">- Pilih Polres -</option>').val('-');
                $polres.trigger('change');
                return;
            }

            // Load polres berdasar polda
            $polres.append('<option value="">Memuat...</option>');
            $polres.trigger('change');
            $.get('{{ url('pengguna/polres_list') }}/' + encodeURIComponent(poldaId), function(data) {
                $polres.empty();
                $polres.append('<option value="-">- Pilih Polres -</option>');
                $.each(data || [], function(_, polres) {
                    $polres.append('<option value="' + polres.id + '">' + polres.name + '</option>');
                });
                // Untuk polres user & level 3 polres, polres tetap disabled, selain itu enabled
                if (isPolresUser || isLevel3Polres) {
                    $polres.prop('disabled', true);
                } else {
                    $polres.prop('disabled', false);
                }
                $polres.trigger('change');
            }).fail(function() {
                $polres.empty().append('<option value="-">- Pilih Polres -</option>');
                if (isPolresUser || isLevel3Polres) {
                    $polres.prop('disabled', true);
                } else {
                    $polres.prop('disabled', false);
                }
                $polres.trigger('change');
                Swal.fire({ icon: 'error', title: 'Gagal memuat Polres', text: 'Coba pilih Polda lagi.' });
            });
        });

        // --- Init awal saat halaman dimuat
        $(function () {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            initDatepickers();

            // Expose polres_id user ke window agar bisa dipakai di JS
            window.user_polres_id = "{{ Auth::user()->polres_id }}";

            var $polres = $('#polres_id');
            var initPolda = $('#polda_id').val();
            var roleId = window.role_id;
            var userPolresId = window.user_polres_id;
            var isLevel3Polres = roleId == 3 && userPolresId && userPolresId !== '' && userPolresId !== 'null';
            var isLevel3Polda = roleId == 3 && (!userPolresId || userPolresId === '' || userPolresId === 'null');

            // If polres select is disabled (for polres user atau level 3 polres), do nothing
            if ($polres.prop('disabled') && !isLevel3Polda) {
                // No need to initialize polres select
                return;
            }

            if (!initPolda || initPolda === '-') {
                if (!isLevel3Polda) {
                    $polres.prop('disabled', true)
                           .empty()
                           .append('<option value="-">- Pilih Polres -</option>')
                           .val('-');
                } else {
                    $polres.empty()
                           .append('<option value="-">- Pilih Polres -</option>')
                           .val('-');
                }
                $polres.trigger('change');
            } else {
                // trigger untuk mengisi polres
                $('#polda_id').trigger('change');
                // Untuk level 3 Polda, pastikan polres enabled setelah trigger
                if (isLevel3Polda) {
                    setTimeout(function() { 
                        $polres.prop('disabled', false); 
                        $polres.trigger('change');
                    }, 300);
                }
            }
        });

        // --- Tombol Generate
        $("#button_anev").on('click', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const _token = $("input[name='_token']").val();
            const date_from = $('#date_from').val();
            const date_to = $('#date_to').val();
            const date_from_now = $('#date_from_now').val();
            const date_to_now = $('#date_to_now').val();
            const polda = $('#polda_id').val() || '-';
            // polres: jika disabled, ambil dari hidden input jika ada, jika tidak, '-'.
            let polres;
            if ($('#polres_id').prop('disabled')) {
                // Cek hidden input
                const hiddenPolres = $("input[type='hidden'][name='polres_id']").val();
                polres = hiddenPolres || '-';
            } else {
                polres = $('#polres_id').val() || '-';
            }
            const type = $('#type').val();

            if (!date_from || !date_to || !date_from_now || !date_to_now) {
                Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Semua tanggal harus diisi.' });
                return;
            }

            clearTable();

            $btn.prop('disabled', true).data('old-text', $btn.text()).text('Memuat...');
            Swal.fire({
                title: 'Memuat data...',
                html: '<b>Mohon tunggu sebentar</b>',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('get_report_anev') }}",
                type: "GET",
                data: { _token, date_from, date_to, date_from_now, date_to_now, polda, polres, type },
                success: function(response) {
                    Swal.close();
                    $btn.prop('disabled', false).text($btn.data('old-text') || 'Tampilkan');

                    const data = response?.data?.summary?.tahun_lalu || [];
                    if (!Array.isArray(data) || data.length === 0) {
                        $('#hasil_table').html('<div class="alert alert-info">Tidak ada data untuk rentang/periode yang dipilih.</div>');
                        return;
                    }

                    // urutkan berdasarkan % periode ini (desc)
                    data.sort((a, b) => Number(b?.persen_selra_ini || 0) - Number(a?.persen_selra_ini || 0));

                    let html = `
                    <div class="container-fluid mt-3">
                      <div style="overflow-x:auto; width:100%">
                        <table id="dataTable" class="display table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th colspan="${[1, 2].includes(window.role_id) ? 10 : 8}" class="periode-lalu text-center" style="background-color: #99ceff">Periode Lalu</th>
                                    <th colspan="${[1, 2].includes(window.role_id) ? 8 : 6}" class="periode-ini text-center text-white" style="background-color: #0000cc">Periode Ini</th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="text-center" style="background-color: #cce6ff">No</th>
                                    <th rowspan="2" class="text-center" style="background-color: #cce6ff">POLDA / POLRES</th>
                                    ${[1, 2].includes(window.role_id) ? `<th rowspan="2" class="text-center" style="background-color: #99ceff">LP IRSMS<br> (TGL KEJADIAN)</th>` : ''}
                                    <th colspan="4" class="text-center" style="background-color: #99ceff">SELRA</th>
                                    <th rowspan="2" class="text-center" style="background-color: #99ceff">POM/TNI</th>
                                    <th rowspan="2" class="text-center" style="background-color: #99ceff">TOTAL <br>SELRA</th>
                                    ${[1, 2].includes(window.role_id) ? `<th rowspan="2" class="text-center" style="background-color: #99ceff">%</th>` : ''}
                                    ${[1, 2].includes(window.role_id) ? `<th rowspan="2" class="text-center text-white" style="background-color: #0000cc">LP IRSMS<br> (TGL KEJADIAN)</th>` : ''}
                                    <th colspan="4" class="text-center text-white" style="background-color: #0000cc;">SELRA</th>
                                    <th rowspan="2" class="text-center text-white" style="background-color: #0000cc;">POM/TNI</th>
                                    <th rowspan="2" class="text-center text-white" style="background-color: #0000cc;">TOTAL <br>SELRA</th>
                                    ${[1, 2].includes(window.role_id) ? `<th rowspan="2" class="text-center text-white" style="background-color: #0000cc;">%</th>` : ''}
                                </tr>
                                <tr>
                                    <th class="text-center" style="background-color: #99ceff">P21</th>
                                    <th class="text-center" style="background-color: #99ceff">SP3</th>
                                    <th class="text-center" style="background-color: #99ceff">DIVERSI</th>
                                    <th class="text-center" style="background-color: #99ceff">SP2LID</th>
                                    <th class="text-center text-white" style="background-color: #0000cc;">P21</th>
                                    <th class="text-center text-white" style="background-color: #0000cc;">SP3</th>
                                    <th class="text-center text-white" style="background-color: #0000cc;">DIVERSI</th>
                                    <th class="text-center text-white" style="background-color: #0000cc;">SP2LID</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    const totalLalu = { laka: 0, p21: 0, sp3: 0, diversi: 0, sp2lid: 0, pom_tni: 0, selra: 0 };
                    const totalIni  = { laka: 0, p21: 0, sp3: 0, diversi: 0, sp2lid: 0, pom_tni: 0, selra: 0 };

                    data.forEach((row, i) => {
                        html += `
                            <tr>
                                <td class="text-center fw-bold" style="background-color: #cce6ff">${i + 1}</td>
                                <td class="text-center fw-bold" style="background-color: #cce6ff">${row.polda}</td>
                                ${[1, 2].includes(window.role_id) ? `<td class="text-center fw-bold" style="background-color: #99ceff">${Number(row.total_laka_lalu ?? 0).toLocaleString('id-ID')}</td>` : ''}
                                <td class="text-center" style="background-color: #99ceff">${Number(row.p21_lalu ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center" style="background-color: #99ceff">${Number(row.sp3_lalu ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center" style="background-color: #99ceff">${Number(row.diversi_lalu ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center" style="background-color: #99ceff">${Number(row.sp2lid_lalu ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center" style="background-color: #99ceff">${Number(row.pom_tni_lalu ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center" style="background-color: #99ceff">${Number(row.total_selra_lalu ?? 0).toLocaleString('id-ID')}</td>
                                ${[1, 2].includes(window.role_id) ? `<td class="text-center" style="background-color: #99ceff">${Number(row.persen_selra_lalu ?? 0).toLocaleString('id-ID')}%</td>` : ''}
                                ${[1, 2].includes(window.role_id) ? `<td class="text-center text-white" style="background-color: #0000cc;">${Number(row.total_laka_ini ?? 0).toLocaleString('id-ID')}</td>` : ''}
                                <td class="text-center text-white" style="background-color: #0000cc;">${Number(row.p21_tahun_ini ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center text-white" style="background-color: #0000cc;">${Number(row.sp3_tahun_ini ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center text-white" style="background-color: #0000cc;">${Number(row.diversi_tahun_ini ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center text-white" style="background-color: #0000cc;">${Number(row.sp2lid_tahun_ini ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center text-white" style="background-color: #0000cc;">${Number(row.pom_tni_tahun_ini ?? 0).toLocaleString('id-ID')}</td>
                                <td class="text-center text-white" style="background-color: #0000cc;">${Number(row.total_selra_ini ?? 0).toLocaleString('id-ID')}</td>
                                ${[1, 2].includes(window.role_id) ? `<td class="text-center text-white" style="background-color: #0000cc;">${Number(row.persen_selra_ini ?? 0).toLocaleString('id-ID')}%</td>` : ''}
                            </tr>
                        `;

                        totalLalu.laka    += Number(row.total_laka_lalu   || 0);
                        totalLalu.p21     += Number(row.p21_lalu          || 0);
                        totalLalu.sp3     += Number(row.sp3_lalu          || 0);
                        totalLalu.diversi += Number(row.diversi_lalu      || 0);
                        totalLalu.sp2lid  += Number(row.sp2lid_lalu       || 0);
                        totalLalu.pom_tni += Number(row.pom_tni_lalu      || 0);
                        totalLalu.selra   += Number(row.total_selra_lalu  || 0);

                        totalIni.laka     += Number(row.total_laka_ini    || 0);
                        totalIni.p21      += Number(row.p21_tahun_ini     || 0);
                        totalIni.sp3      += Number(row.sp3_tahun_ini     || 0);
                        totalIni.diversi  += Number(row.diversi_tahun_ini || 0);
                        totalIni.sp2lid   += Number(row.sp2lid_tahun_ini  || 0);
                        totalIni.pom_tni  += Number(row.pom_tni_tahun_ini || 0);
                        totalIni.selra    += Number(row.total_selra_ini   || 0);
                    });

                    const totalLakaLalu = Math.max(0, totalLalu.laka - totalLalu.pom_tni);
                    const totalLakaIni = Math.max(0, totalIni.laka - totalIni.pom_tni);

                    // const persenLalu = totalLalu.laka > 0 ? ((totalLalu.selra / totalLakaLalu) * 100).toFixed(2) : 0;
                    // const persenIni  = totalIni.laka  > 0 ? ((totalIni.selra  / totalLakaIni ) * 100).toFixed(2) : 0;

                    const persenLalu = (totalLalu.laka === 0 && totalLalu.selra === 0) ||
                                       (totalLakaLalu === 0 && totalLalu.selra === 0)
                                       ? '100.00'
                                       : totalLakaLalu > 0 ? ((totalLalu.selra / totalLakaLalu) * 100).toFixed(2) : '0.00';

                    const persenIni = (totalIni.laka === 0 && totalIni.selra === 0) ||
                                      (totalLakaIni === 0 && totalIni.selra === 0)
                                      ? '100.00'
                                      : totalLakaIni > 0 ? ((totalIni.selra / totalLakaIni) * 100).toFixed(2) : '0.00';

                    html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center" colspan="2" style="background-color: #cce6ff">TOTAL</th>
                                ${[1, 2].includes(window.role_id) ? `<th class="text-center" style="background-color: #99ceff">${Number(totalLalu.laka).toLocaleString('id-ID')}</th>` : ''}
                                <th class="text-center" style="background-color: #99ceff">${Number(totalLalu.p21).toLocaleString('id-ID')}</th>
                                <th class="text-center" style="background-color: #99ceff">${Number(totalLalu.sp3).toLocaleString('id-ID')}</th>
                                <th class="text-center" style="background-color: #99ceff">${Number(totalLalu.diversi).toLocaleString('id-ID')}</th>
                                <th class="text-center" style="background-color: #99ceff">${Number(totalLalu.sp2lid).toLocaleString('id-ID')}</th>
                                <th class="text-center" style="background-color: #99ceff">${Number(totalLalu.pom_tni).toLocaleString('id-ID')}</th>
                                <th class="text-center" style="background-color: #99ceff">${Number(totalLalu.selra).toLocaleString('id-ID')}</th>
                                ${[1, 2].includes(window.role_id) ? `<th class="text-center" style="background-color: #99ceff">${Number(persenLalu).toLocaleString('id-ID')}%</th>` : ''}
                                ${[1, 2].includes(window.role_id) ? `<th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.laka).toLocaleString('id-ID')}</th>` : ''}
                                <th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.p21).toLocaleString('id-ID')}</th>
                                <th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.sp3).toLocaleString('id-ID')}</th>
                                <th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.diversi).toLocaleString('id-ID')}</th>
                                <th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.sp2lid).toLocaleString('id-ID')}</th>
                                <th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.pom_tni).toLocaleString('id-ID')}</th>
                                <th class="text-center text-white" style="background-color: #0000cc;">${Number(totalIni.selra).toLocaleString('id-ID')}</th>
                                ${[1, 2].includes(window.role_id) ? `<th class="text-center text-white" style="background-color: #0000cc;">${Number(persenIni).toLocaleString('id-ID')}%</th>` : ''}
                            </tr>
                        </tfoot>
                    </table>`;

                    $('#hasil_table').html(html);

                    $(document).ready(function () {
                        $('#dataTable').DataTable({
                            responsive: true,
                            paging: false,
                            scrollX: true,
                            autoWidth: false,
                            ordering: false,
                            dom: {!! Auth::user()->hasPermission('anev.E') ? "'Bfrtip'" : "'frtip'" !!},
                            buttons: [
                            @if(Auth::user()->hasPermission('anev.E'))
                            {
                                extend: 'copyHtml5',
                                footer: true,
                                className: 'btn btn-danger',
                                exportOptions: {
                                    columns: ':visible',
                                    footer: true,
                                    modifier: { order: 'current', page: 'all', search: 'applied' }
                                }
                            },
                            {
                                extend: 'excelHtml5',
                                footer: true,
                                className: 'btn btn-success',
                                filename: function () {
                                    const polda = ($('#polda_id option:selected').text().trim() || 'ICELL');
                                    const polres = ($('#polres_id option:selected').text().trim());
                                    const pad = n => n.toString().padStart(2, '0');
                                    const d = new Date(), tgl = `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${d.getFullYear()}_${pad(d.getHours())}${pad(d.getMinutes())}`;
                                    return `${polda}${(polres && !['-','- Pilih Polres -','Pilih Polres'].includes(polres)) ? ' - '+polres : ''} - ${tgl}`;
                                },
                                exportOptions: {
                                    columns: ':visible',
                                    footer: true,
                                    modifier: { order: 'current', page: 'all', search: 'applied' },
                                    format: {
                                    body: function (data, row, col, node) {
                                        let text = $('<div>').html(data).text().trim();

                                        // --- JANGAN sentuh sel yang berisi persentase: biarkan koma (,) apa adanya
                                        if (text.includes('%')) {
                                        // untuk menghindari auto-format Excel, kembalikan sebagai teks
                                        return "'" + text;
                                        }

                                        // --- Di luar persen: bersihkan pemisah ribuan
                                        // hapus semua koma
                                        text = text.replace(/,/g, '');
                                        // hapus titik yang berperan sebagai pemisah ribuan (sebelum 3 digit)
                                        text = text.replace(/\.(?=\d{3}(?:\D|$))/g, '');

                                        // pertahankan leading zero untuk string numerik murni
                                        if (/^\d+$/.test(text) && text.length > 1 && text.startsWith('0')) {
                                        return "'" + text;
                                        }
                                        return text;
                                    },
                                    footer: function (data, col) {
                                        let text = $('<div>').html(data).text().trim();

                                        if (text.includes('%')) {
                                        return "'" + text; // persen: jangan hapus koma
                                        }
                                        text = text.replace(/,/g, '').replace(/\.(?=\d{3}(?:\D|$))/g, '');
                                        if (/^\d+$/.test(text) && text.length > 1 && text.startsWith('0')) {
                                        return "'" + text;
                                        }
                                        return text;
                                    }
                                    }
                                },
                                // (opsional) kalau sebelumnya pakai customizeData untuk kolom persen, biarkan—jangan ubah koma di sana
                                customize: function (xlsx) {
                                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                    $('row[r="1"] c', sheet).attr('s', '2'); // header bold
                                }
                            }
                            @endif
                            ]          // {
                            //     extend: 'pdfHtml5',
                            //     footer: true,
                            //     className: 'btn btn-primary',
                            //     filename: function () {
                            //         const polda = ($('#polda_id option:selected').text().trim() || 'ICELL');
                            //         const polres = ($('#polres_id option:selected').text().trim());
                            //         const pad = n => n.toString().padStart(2, '0');
                            //         const d = new Date(), tgl = `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${d.getFullYear()}_${pad(d.getHours())}${pad(d.getMinutes())}`;
                            //         return `${polda}${(polres && !['-','- Pilih Polres -','Pilih Polres'].includes(polres)) ? ' - '+polres : ''} - ${tgl}`;
                            //     },
                            //     orientation: 'landscape',
                            //     pageSize: 'A4', // default A4; nanti auto jadi A3 kalau kolom terlalu banyak
                            //     exportOptions: {
                            //         columns: ':visible',
                            //         footer: true,
                            //         modifier: { order: 'current', page: 'all', search: 'applied' }
                            //     },
                            //     customize: function (doc) {
                            //         // margin & font kecil agar muat
                            //         doc.pageMargins = [8, 10, 8, 10];
                            //         doc.defaultStyle = doc.defaultStyle || {};
                            //         doc.defaultStyle.fontSize = 8;

                            //         doc.styles = doc.styles || {};
                            //         doc.styles.tableHeader = Object.assign(doc.styles.tableHeader || {}, {
                            //         fontSize: 9, bold: true, fillColor: '#222577', color: 'white', alignment: 'center'
                            //         });

                            //         // Cari tabel di konten
                            //         var tableNode = doc.content.find(function (n) { return n.table; });
                            //         if (tableNode && tableNode.table && tableNode.table.body && tableNode.table.body.length) {
                            //         var colCount = tableNode.table.body[0].length;

                            //         // 1) Lebar kolom fleksibel agar diskalakan ke halaman
                            //         tableNode.table.widths = Array(colCount).fill('*');

                            //         // 2) Layout tipis biar hemat ruang
                            //         tableNode.layout = {
                            //             hLineWidth: function(){ return 0.2; },
                            //             vLineWidth: function(){ return 0.2; },
                            //             hLineColor: function(){ return '#cccccc'; },
                            //             vLineColor: function(){ return '#cccccc'; },
                            //             paddingLeft: function(){ return 2; },
                            //             paddingRight: function(){ return 2; },
                            //             paddingTop: function(){ return 2; },
                            //             paddingBottom: function(){ return 2; }
                            //         };

                            //         // 3) Jika kolom banyak banget, auto naik ke A3 landscape
                            //         if (colCount >= 14) { // threshold boleh kamu ubah
                            //             doc.pageSize = 'A3';
                            //         }
                            //         }
                            //     }
                            // }
                         });
                     });

                    // Highlight kata saat pencarian
                    table.on('draw', function() {
                        var body = $(table.table().body());
                        body.unhighlight();
                        var term = table.search();
                        if (term) body.highlight(term);
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    $btn.prop('disabled', false).text($btn.data('old-text') || 'Tampilkan');

                    let msg = xhr?.responseJSON?.message || xhr.statusText || 'Terjadi kesalahan.';
                    Swal.fire({ icon: 'error', title: 'Gagal memuat data', text: msg });

                    // Jika validasi salah (422), reset polres agar aman
                    if (xhr.status === 422) {
                        const poldaNow = $('#polda_id').val();
                        if (!poldaNow || poldaNow === '-') {
                            $('#polres_id').prop('disabled', true)
                                           .empty()
                                           .append('<option value="-">- Semua Polres -</option>')
                                           .val('-');
                        }
                    }
                }
            });
        });
    })(jQuery);
    </script>
@endpush


