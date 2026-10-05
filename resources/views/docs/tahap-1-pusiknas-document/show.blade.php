@php
    $_title = 'Detail BPT1 — Surat Pengiriman Berkas Perkara Tahap I';
@endphp

@extends('layouts.app')

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-blue-dark mb-0">
                Surat Pengiriman Berkas Perkara Tahap I (S-50)
                <span class="badge bg-info text-white ms-2">SPPT-TI</span>
            </h5>
            <div class="d-flex gap-2">
                @if(in_array($document->status_id, [1, 2]))
                    <form action="{{ route('doc.tahap-1-document.submit', $document->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengajukan dokumen ini?');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Ajukan</button>
                    </form>
                @endif

                @if($document->status_id == 3)
                    <form action="{{ route('doc.tahap-1-document.approve', $document->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menyetujui dokumen ini?');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Approve</button>
                    </form>
                @endif

                <a href="{{ route('doc.tahap-1-document.edit', [$document->id, 'accident_id' => $accidentId]) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('doc.tahap-1-document.download', [$document->id, 'accident_id' => $accidentId]) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-word"></i> Unduh Word
                </a>
            </div>
        </div>

        <div class="box-body">
            @php
                $daftarBB    = $messages['daftar_barang_bukti'] ?? [];
                $daftarSaksi = $messages['daftar_saksi'] ?? [];
                $carbonCopies = $document->carbon_copies ?? [];
                if(is_string($carbonCopies)) $carbonCopies = json_decode($carbonCopies, true) ?? [];
                $signatory = $document->officers->where('class', 'SIGNATORY')->first();
            @endphp

            <table class="table table-borderless">
                <tr>
                    <th width="30%">Nomor LP</th>
                    <td>{{ $accident->no_lp ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nomor SPDP Terkait</th>
                    <td>{{ $document->no_spdp ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nomor Surat Pengantar</th>
                    <td>{{ $document->document_number ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Surat</th>
                    <td>{{ $document->document_date ? $document->document_date->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <th>Nomor Berkas Perkara</th>
                    <td>{{ $document->no_berkas_perkara ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kejaksaan Tujuan</th>
                    <td>{{ $document->prosecutor->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Uraian Singkat Perkara</th>
                    <td>{{ $messages['uraian_singkat_perkara'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Lokasi Kejadian</th>
                    <td>{{ $messages['lokasi_kejadian'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Waktu Kejadian</th>
                    <td>{{ $messages['waktu_kejadian'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Kejadian</th>
                    <td>{{ ($messages['tanggal_kejadian'] ?? '') . '/' . ($messages['bulan_kejadian'] ?? '') . '/' . ($messages['tahun_kejadian'] ?? '') }}</td>
                </tr>
            </table>

            <hr>
            <h6 class="fw-bold">Tersangka</h6>
            @if($document->suspects->count() > 0)
                <ul>
                    @foreach($document->suspects as $s)
                        <li>{{ $s->name }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Tidak ada tersangka yang terdaftar.</p>
            @endif

            @if(count($daftarBB) > 0)
                <hr>
                <h6 class="fw-bold">Daftar Barang Bukti</h6>
                <table class="table table-bordered table-sm">
                    <thead><tr><th>No.</th><th>Nama BB</th><th>Jumlah</th><th>Satuan</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @foreach($daftarBB as $i => $bb)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $bb['nama'] ?? '' }}</td>
                                <td>{{ $bb['jumlah'] ?? '' }}</td>
                                <td>{{ $bb['satuan'] ?? '' }}</td>
                                <td>{{ $bb['keterangan'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(count($daftarSaksi) > 0)
                <hr>
                <h6 class="fw-bold">Daftar Saksi</h6>
                <table class="table table-bordered table-sm">
                    <thead><tr><th>No.</th><th>Nama</th><th>Pekerjaan</th><th>Alamat</th></tr></thead>
                    <tbody>
                        @foreach($daftarSaksi as $i => $saksi)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $saksi['nama'] ?? '' }}</td>
                                <td>{{ $saksi['pekerjaan'] ?? '' }}</td>
                                <td>{{ $saksi['alamat'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(count($carbonCopies) > 0)
                <hr>
                <h6 class="fw-bold">Tembusan</h6>
                <ol>
                    @foreach($carbonCopies as $copy)
                        <li>{{ $copy }}</li>
                    @endforeach
                </ol>
            @endif

            @if($signatory)
                <hr>
                <h6 class="fw-bold">Penandatangan</h6>
                <p>{{ $signatory->first_title }} {{ $signatory->first_name }} {{ $signatory->last_name }}, {{ $signatory->rank->full_name ?? '' }}, NRP. {{ $signatory->register_number }}</p>
            @endif
        </div>
    </div>
@endsection
