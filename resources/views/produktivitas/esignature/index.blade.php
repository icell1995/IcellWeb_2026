@extends('layouts.app')

@section('content')
<div class="loaderbg" style="display:none"></div>

<div class="content col-xs-12 col-md-12 col-lg-12 col-sm-12">
    <div class="box">
        <form action="{{ route('signature.index') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h3 class="fw-bold text-center text-blue-dark">DOKUMEN BELUM DITANDATANGAN</h3>
                <table class="display" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <th class="text-center">Laporan Polisi</th>
                        <th class="text-center">Jenis Dokumen</th>
                        <th class="text-center">Berkas Surat</th>
                        <th class="text-center">Dibuat Oleh</th>
                        <th class="text-center">Tanggal Dibuat</th>
                        <th class="text-center">Tanggal Diverifikasi</th>
                        <th class="text-center">Aksi</th>
                    </thead>
                    <tbody>
                        @foreach ($esign as $sign)
                            <tr>
                                <td class="text-center">{{$sign->no_lp}}</td>
                                <td class="text-center">{{$sign->category_spdp}}</td>
                                <td class="text-center">{{$sign->no_spdp}} <br>{{ \Carbon\Carbon::parse($sign->spdp_date)->translatedFormat('d F Y') }}</td>
                                {{-- <td class="text-center">{{$sign->latter_signature}}</td> --}}
                                <td class="text-center">{{$sign->first_title .' '. $sign->first_name .' '. $sign->last_name .', '. $sign->last_title}}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($sign->sprindik_date)->translatedFormat('d F Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($sign->sprindik_date)->translatedFormat('d F Y') }}</td>
                                {{-- <td class="text-center"><button class="btn btn-primary">Tanda Tangani Dokumen</button></td> --}}
                                <td class="text-center">
                                    <a href="{{ route('signature.print', ['id' => $sign->id]) }}" class="btn btn-primary">Tanda Tangani Dokumen</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </form>
    </div>
    <div class="box">
        <form action="{{ route('signature.index') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h3 class="fw-bold text-center text-blue-dark">DOKUMEN SUDAH DITANDATANGAN</h3>
                <table class="display" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <th class="text-center">Laporan Polisi</th>
                        <th class="text-center">Jenis Dokumen</th>
                        <th class="text-center">Berkas Surat</th>
                        <th class="text-center">Dibuat Oleh</th>
                        <th class="text-center">Tanggal Dibuat</th>
                        <th class="text-center">Tanggal Di TTE</th>
                        <th class="text-center">Aksi</th>
                    </thead>
                    <tr>
                        <td class="text-center">laporan polisi di sini</td>
                        <td class="text-center">jenis dokumen di sini</td>
                        <td class="text-center">berkas surat di sini</td>
                        <td class="text-center">dibuat oleh di sini</td>
                        <td class="text-center">tanggal dibuat di sini</td>
                        <td class="text-center">tanggal di tte di sini</td>
                        <td class="text-center"><button class="btn btn-primary">Lihat Dokumen</button></td>
                    </tr>
                  </table>
            </div>
        </form>
    </div>
</div>
@endsection
