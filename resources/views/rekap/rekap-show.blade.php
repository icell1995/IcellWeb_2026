@php
    $_title = 'Lihat Rekap';
@endphp

@extends('layouts.app')

@section('content')
<div class="content col-lg-12 col-md-12 col-sm-12" id="right">
    <div class="row justify-content-center">
        <div class="box box-info col-lg-12" style="margin-bottom: 20px">
            <div class="box-header with-border">
                <h3>Data Laporan Polisi</h3>

                <div class="row col-md-12">
                    <div class="col-md-6">
                        <label for="status_perkembangan">Nomor Laporan Polisi</label>
                        <li>{{ $no_lp }}</li>

                        <label for="status_perkembangan">Tanggal Kejadian</label>
                        <li>{{ $accident_date }}</li>

                        <label for="status_perkembangan">Tanggal Pelaporan</label>
                        <li>{{ $report_date }}</li>
                    </div>

                    <div class="col-md-6">
                        <label for="status_perkembangan">Tanggal Tindak Lanjut</label>
                        <li>{{ $created_at }}</li>

                        <label for="status_perkembangan">Status Perkembangan</label>
                        <li>{{ $selra_name }}</li>

                        <label for="status_perkembangan">Penyidik</label>
                        @if ($surat_perintah_penyidikan == null)
                        <li>
                            Belum ada petugas yang diberikan untuk melakukan penyidikan
                        </li>
                        @else
                        @foreach ($surat_perintah_penyidikan as $penyidik)
                        <li>{{ $penyidik->first_name }} {{ $penyidik->last_name }} </li>
                        @endforeach
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="box box-info col-lg-12" style="margin-bottom: 20px">
            <div class="box-header with-border">
                <h3>Daftar Perkembangan Perkara</h3>
                @if ($surat_perintah_penyelidikan != null)
                <li>Telah diterbitkan surat tugas penyelidikan pada tanggal
                    {{ $surat_perintah_penyelidikan[0]->created_at }}</li>
                @endif
            </div>
        </div> --}}

        {{-- <br>
        @if ($daftar_saksi != null)
        <div class="card">
            <div class="card-body">
                <h1> Daftar Saksi </h1>
                <php $no = 0; ?>
                @foreach ($daftar_saksi as $saksi)
                <php $no++; ?>
                {{ $no }}. {{ $saksi->name }}
                <li>{{ $saksi->address }}</li>
                <li>{{ $saksi->phone }}</li>
                <br>
                @endforeach
            </div>
        </div>
        @endif
        <br> --}}

        <div class="box box-info col-lg-12" style="margin-bottom: 20px">
            <div class="box-header with-border">
                <h3>Dokumen</h3>
                <div class="table-responsive">
                    <table class="display" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Kategori</th>
                                <th>Dibuat pada</th>
                                <th>Dibuat oleh</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <?php $no = 0; ?>

                        @foreach ($dokumen as $dok)
                        <?php $no++; ?>
                        <tbody>
                            {{-- <th scope="row">{{ $petugas->perPage() - $petugas->perPage() + ($index + 1) }}.</th>
                            --}}
                            <th scope="row">{{$no}}.</th>
                            <td>{{ $dok->dok_name }}</td>
                            <td>{{$dok->ref_name}}</td>
                            <td>{{ $dok->created_at }}</td>
                            <td>{{ $dok->officer_name }}</td>
                            <td><a target="_blank" href="/{{$dok->initial}}/{{$dok->accident_id}}" id="" }>Lihat</a></span></td>
                        </tbody>
                        @endforeach
                    </table>
                    {{-- <div class="pull-left">
                        Showing
                        {{ $petugas->firstItem() }}
                        to
                        {{ $petugas->lastItem() }}
                        of
                        {{ $petugas->total() }}
                    </div>
                    <div class="pull-right">
                        {{ $petugas->links() }}
                    </div> --}}
                </div>
                <!-- /.table-responsive -->
            </div>
        </div>
    </div>
</div>


@push('script')
<script type="text/javascript">
    $("#tglKejadian").datepicker();

</script>

@endpush

@endsection
