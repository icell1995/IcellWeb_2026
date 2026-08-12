@php
    $_title = 'Daftar Produktivitas';
@endphp

@extends('layouts.app')

@section('title', $_title)

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Produktivitas</h3>
            <?php $no = 0; ?>
            <form method="GET" class="row mt-2 search" action="{{ route('produktivitas-search') }}">
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-3">
                    <input type="text" id="no_LP" class="form-control" name="no_lp" placeholder="Nomor lp" value="{{ old('no_LP') }}">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-3">
                    <input class="form-select" type="text" id="tgl_kejadian" name="tgl_kejadian"
                        placeholder="Masukan Tanggal Kejadian disini" autocomplete="off">
                    <span class="text-danger error-text birth_date_err"></span>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-3">
                    <select id="status" name="status" class="form-select">
                        <option value="">Status</option>
                        <option value="S0107">Dalam Proses</option>
                        <option value="S0101">P21</option>
                        <option value="S0102">SP3</option>
                        <option value="S0103">Diversi</option>
                        <option value="S0104">POM/TNI</option>
                        <option value="S0108">SP2LID</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-3">
                    <select id="level" name="level" class="form-select">
                        <option value="">Tingkat Kecelakaan</option>
                        <option value="1">Berat</option>
                        <option value="2">Sedang</option>
                        <option value="3">Ringan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-3">
                    <select id="polda" name="polda" class="form-select @error('polda') is-invalid @enderror">
                        @if (Auth::user()->role_id == 1)
                            <option value="-">Semua Polda</option>
                        @endif
                        @foreach ($poldas as $polda)
                            <option value="{{ $polda->id }}" {{ old('polda_id') == $polda->id ? 'selected' : '' }}>
                                {{ $polda->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-3">
                    <select id="polres" name="polres" class="form-select @error('polres') is-invalid @enderror">
                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 3)
                            <option value="-">Pilih Polres</option>
                        @endif
                        @foreach ($polress as $polres)
                            <option value="{{ $polres->id }}" {{ old('polres_id') == $polres->id ? 'selected' : '' }}>
                                {{ $polres->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="text-start">
                    <button type="submit" id="search_btn" class="btn btn-dark-blue">{{ __('Cari') }}</button>
                    <button class="btn btn-secondary" type="button" id="reset_btn" class="btn btn-primary"><a
                            class="text-decoration-none text-white" href="{{ url('/produktivitas') }}"> Reset </a>
                    </button>
                </div> --}}
                <div class="text-start">
                    <button type="submit" id="search_btn" class="btn btn-dark-blue">Cari</button>
                    <a href="{{ url('/produktivitas') }}" class="btn btn-secondary" id="reset_btn">Reset</a>
                </div>
            </form>
        </div>
        <div class="box-body">
            @foreach ($accident as $index => $accidents)
                <?php $no++; ?>
                <fieldset class="border border-2 rounded-3 p-3 my-3">
                    <div class="row">
                        <span class="fw-semibold"
                            style="width: 0em">{{ $accident->perPage() - $accident->perPage() + ($index + 1) }}.</span>
                        <div class="col-12 row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Nomor LP</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    {{-- <a class="col-7 p-0 text-link"
                                        href="/produktivitas/view-produktivitas?accident_id={{ $accidents->id }}">{{ $accidents->no_lp }}</a> --}}

                                    @php
                                        $locked   = (int)($accidents->is_locked_by_irsms ?? 0) === 1;
                                        $reason   = in_array((int)($accidents->state_irsms ?? -1), [0,1,2], true)
                                                    ? 'Status IRSMS (' . $accidents->state_irsms . ') belum final (0/1/2).'
                                                    : '';
                                        $detailUrl = url('/produktivitas/view-produktivitas?accident_id='.$accidents->id);
                                        $isRole1   = (int) Auth::user()->role_id === 1;
                                        $softOpen  = $isRole1 && $locked; // role 1: modal muncul tapi boleh lanjut
                                    @endphp

                                    <a class="col-7 p-0 text-link lp-link"
                                    href="{{ $locked && !$isRole1 ? '#' : $detailUrl }}"
                                    data-locked="{{ $locked ? '1' : '0' }}"
                                    data-softopen="{{ $softOpen ? '1' : '0' }}"
                                    data-next="{{ $detailUrl }}"
                                    data-reason="{{ $locked ? $reason : '' }}">
                                    {{ $accidents->no_lp }}
                                    </a>
                                    @if($locked)
                                    <span class="badge bg-danger ms-2 small">LP di IRSMS Dikembalikan</span>
                                    @endif
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Status</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    @if ($accidents->selra_id == 'S0101')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #00FF00">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @elseif($accidents->selra_id == 'S0102')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #00FFFF">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @elseif($accidents->selra_id == 'S0103')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #0000FF">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @elseif($accidents->selra_id == 'S0104')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #FF00FF">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @elseif($accidents->selra_id == 'S0106')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #FFFF00">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @elseif($accidents->selra_id == 'S0107')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #EE354F">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @elseif($accidents->selra_id == 'S0108')
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #FF9900">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @else
                                        <strong class="col-7 p-0">
                                            <div class="px-3 rounded"
                                                style="width: max-content !important; background-color: #FF9900">
                                                {{ $accidents->selra }}</div>
                                        </strong>
                                    @endif
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Petugas Pelapor</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    {{-- <span class="col-7 p-0">{{ $accidents->rank_id }}
                                        {{ $accidents->officer_first_name }}
                                        {{ $accidents->officer_last_name }}</span> --}}
                                    <span class="col-7 p-0">{{ $accidents->rank_id }}
                                        {{ $accidents->officer_name }}</span>
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Tanggal Kejadian</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    <span class="col-7 p-0">{{ $accidents->accident_date }}</span>
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Tanggal Aktivitas
                                            Terakhir</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    <span class="col-7 p-0">{{ $accidents->accident_last_update }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Polda</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    <span class="col-7 p-0">{{ $accidents->polda_name }}</span>
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Polres</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    {{-- <span class="col-7 p-0">{{ $accidents->name }}</span> --}}
                                    <span class="col-7 p-0">{{ $accidents->polres_name }}</span>
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Tingkat Kecelakaan</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    <span class="col-7 p-0">
                                        @if ($accidents->md >= 1)
                                            Berat
                                        @elseif($accidents->lb >= 1 && $accidents->md == 0)
                                            Sedang
                                        @elseif($accidents->lr >= 1 && $accidents->lb == 0 && $accidents->md == 0)
                                            Ringan
                                        @else
                                            Ringan
                                        @endif
                                    </span>
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">MD / LB / LR</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    <span class="col-7 p-0">{{ $accidents->md }} /
                                        {{ $accidents->lb }} / {{ $accidents->lr }}</span>
                                </div>
                                <div class="row col-12 mb-1">
                                    <div class="row col-5 fw-semibold">
                                        <span class="col-11">Nama Jalan</span>
                                        <span class="col-1 p-0">:</span>
                                    </div>
                                    <span class="col-7 p-0">{{ $accidents->road_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            @endforeach
        </div>

        {{ $accident->links() }}
    </div>
    @if(($rejectedToShow ?? collect())->isNotEmpty())
        <div class="modal fade" id="rejectedSelraModal" tabindex="-1" aria-labelledby="rejectedSelraLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="max-width: 85rem;">
                <div class="modal-content rounded-3">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="rejectedSelraLabel">LP dengan Selra dikembalikan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:48px;">No</th>
                                        <th class="text-center">No LP</th>
                                        <th class="text-center">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedToShow as $i => $row)
                                    @php $ts = \Carbon\Carbon::parse($row->rejected_at ?? $row->log_created_at)->format('d-m-Y H:i'); @endphp
                                        <tr>
                                            <td class="text-center">{{ $i+1 }}</td>
                                            <td class="fw-semibold">{{ $row->no_lp ?? $row->accident_number ?? '-' }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ url('/produktivitas/view-produktivitas?accident_id='.$row->accident_id) }}"
                                                    target="_blank">Buka LP</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark-blue" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="irsmsReturnModal" tabindex="-1" aria-labelledby="irsmsReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="irsmsReturnModalLabel">LP Terkunci (IRSMS)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center rounded-2 p-3">
                    <p class="mb-2">
                        <h5>LP ini <b>terkunci</b> karena LP di IRSMS <b>dikembalikan oleh HELPDESK IRSMS</b>.</h5>
                    </p>
                    <hr>
                    <p class="mb-0">
                        <b>Keterangan:</b> LP di IRSMS dikembalikan oleh Helpdesk IRSMS, karena data belum lengkap. Silakan lengkapi data pada IRSMS terlebih dahulu.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-2" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary rounded-2 d-none" id="irsmsOpenAnywayBtn">Buka LP</button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        @if(($rejectedToShow ?? collect())->isNotEmpty())
            var el = document.getElementById('rejectedSelraModal');
            if (el && window.bootstrap && bootstrap.Modal) {
            new bootstrap.Modal(el).show();
            }
        @endif
        });

        (function() {
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a.lp-link');
            if (!a) return;

            var locked   = a.getAttribute('data-locked') === '1';
            var softOpen = a.getAttribute('data-softopen') === '1';
            var nextUrl  = a.getAttribute('data-next') || '#';

            if (!locked) return; // tidak terkunci -> lanjut default

            e.preventDefault();

            var modalEl = document.getElementById('irsmsReturnModal');
            if (!modalEl) return;

            // Atur tombol “Buka LP” sesuai soft/hard lock
            var openBtn = document.getElementById('irsmsOpenAnywayBtn');
            if (softOpen && openBtn) {
            openBtn.classList.remove('d-none');
            // bind click ke URL LP
            openBtn.onclick = function() {
                window.location.href = nextUrl;
            };
            } else if (openBtn) {
            openBtn.classList.add('d-none');
            openBtn.onclick = null;
            }

            if (window.bootstrap && bootstrap.Modal) {
            new bootstrap.Modal(modalEl).show();
            }
        }, false);
        })();

        $('#tgl_kejadian').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: "true",
            orientation: 'auto bottom'
        });

        $(document).ready(function() {
            var get_pol = $('#polda').val();
            if (get_pol == null || get_pol == '-') {
                $('#polres').prop('disabled', true);
            } else {
                $('#polres').prop('disabled', false);
            }
        });

        $('#polda').on('change', function(event) {
            event.preventDefault();
            var poldaId = $(this).val();
            $('#polres').prop('disabled', true);
            $('#polres').empty();
            $('#polres').append('<option value="">Pilih Polres</option>');
            if (!poldaId) {
                return;
            }

            $.get('{{ url('pengguna/polres_list') }}/' + poldaId, function(data) {

                $('#polres').empty()
                var option = '<option value="">Pilih Polres</option>';
                $('#polres').append(option);

                $.each(data, function(key, polres) {
                    var id = polres.id;
                    var name = polres.name;
                    var option = '<option value="' + id + '">' + name + '</option>';

                    $('#polres').append(option);
                });

                $('#polres').prop('disabled', false);
            });
        });

        $('#search_btn').click(function() {
            $('#polres').prop('disabled', false);
        });
    </script>
@endpush
