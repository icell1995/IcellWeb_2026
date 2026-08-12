@php
    $_title = 'Surat Perintah Penangkapan';
    $p = $defaults ?? [];
    $ident = $p['identity'] ?? [];
    $issued = $p['issued'] ?? [];
    $hand = $p['handover'] ?? [];
    $refs = $p['references'] ?? [];
    $dasar = $p['dasar'] ?? [];
    $md = $p['manual_dasar'] ?? [];
    $crime = $p['crime'] ?? [];
@endphp

@extends('layouts.app')

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progress Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">{{ $_title }}</h5>
        </div>
        <div class="box-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nomor LP</dt>
                <dd class="col-sm-9">{{ $accident->no_lp ?? '-' }}</dd>

                <dt class="col-sm-3">Nomor / tanggal dokumen</dt>
                <dd class="col-sm-9">
                    {{ $doc->document_number ?? '-' }}
                    @if ($doc->document_date)
                        &mdash; {{ $doc->document_date->format('Y-m-d') }}
                    @endif
                </dd>

                <dt class="col-sm-3">Referensi</dt>
                <dd class="col-sm-9">
                    <div>No Sprindik: {{ $refs['sprindik_document_number'] ?? '—' }}</div>
                    <div>No Surat Ketetapan Tersangka: {{ $refs['sket_document_number'] ?? '—' }}</div>
                </dd>

                <dt class="col-sm-3">No Surat Perintah Tugas (redaksi)</dt>
                <dd class="col-sm-9">
                    @php
                        $sptNumShow = $refs['surat_perintah_tugas_document_number'] ?? ($md['surat_perintah_tugas_number'] ?? null);
                        $sptDateShow = $refs['surat_perintah_tugas_document_date'] ?? ($md['surat_perintah_tugas_date'] ?? null);
                    @endphp
                    {{ $sptNumShow ?: '—' }}
                    @if (! empty($sptDateShow))
                        &mdash; {{ $sptDateShow }}
                    @endif
                </dd>

                <dt class="col-sm-3">No Surat Ketetapan Tersangka (redaksi)</dt>
                <dd class="col-sm-9">
                    @php
                        $sketNumShow = $refs['sket_document_number'] ?? ($md['sket_number'] ?? null);
                        $sketDateShow = $refs['sket_document_date'] ?? ($md['sket_date'] ?? null);
                        $sketNamaShow = $doc->suspect?->name ?? ($md['sket_atas_nama'] ?? null);
                    @endphp
                    {{ $sketNumShow ?: '—' }}
                    @if (! empty($sketDateShow))
                        &mdash; {{ $sketDateShow }}
                    @endif
                    @if (! empty($sketNamaShow))
                        &mdash; {{ $sketNamaShow }}
                    @endif
                </dd>

                @if (! empty($crime['description']) || ! empty($crime['articles']))
                    <dt class="col-sm-3">Tindak pidana / pasal (teks Word)</dt>
                    <dd class="col-sm-9">
                        {{ $crime['description'] ?? '—' }}
                        @if (! empty($crime['articles']))
                            <div class="mt-1"><em>Pasal:</em> {{ $crime['articles'] }}</div>
                        @endif
                    </dd>
                @endif

                <dt class="col-sm-3">Laporan Polisi (otomatis dari perkara)</dt>
                <dd class="col-sm-9">
                    {{ $dasar['lp_number'] ?? ($accident->no_lp ?? '—') }}
                    @if (! empty($dasar['lp_date']))
                        — tanggal {{ $dasar['lp_date'] }}
                    @endif
                </dd>

                @if ($doc->valid_until_date)
                    <dt class="col-sm-3">Berlaku s.d. (otomatis +30 hari dari tanggal surat)</dt>
                    <dd class="col-sm-9">{{ $doc->valid_until_date->format('Y-m-d') }}</dd>
                @endif

                <dt class="col-sm-3">Tersangka</dt>
                <dd class="col-sm-9">{{ $doc->suspect?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Identitas (snapshot)</dt>
                <dd class="col-sm-9">
                    @if (! empty($ident))
                        <div>{{ $ident['name'] ?? '' }} — {{ $ident['nik'] ?? '' }}</div>
                        <div>{{ $ident['birth_place_date'] ?? '' }}</div>
                        <div>{{ $ident['gender'] ?? '' }}, {{ $ident['religion'] ?? '' }}, {{ $ident['job'] ?? '' }}</div>
                        <div>{{ $ident['nationality'] ?? '' }}</div>
                        <div style="white-space:pre-wrap;">{{ $ident['address'] ?? '' }}</div>
                    @else
                        —
                    @endif
                </dd>

                <dt class="col-sm-3">Kepada (ringkas)</dt>
                <dd class="col-sm-9"><pre class="mb-0" style="white-space:pre-wrap;">{{ $p['kepada_text'] ?? '-' }}</pre></dd>

                <dt class="col-sm-3">Dikeluarkan (otomatis)</dt>
                <dd class="col-sm-9">{{ $issued['location'] ?? '—' }}, {{ $issued['date'] ?? '—' }}</dd>

                <dt class="col-sm-3">Penyerahan salinan</dt>
                <dd class="col-sm-9">{{ $hand['date'] ?? '—' }}</dd>
            </dl>

            <div class="mt-3">
                <a href="{{ route('doc.surat-perintah-penangkapan-document.download', ['id' => $doc->id, 'accident_id' => $accidentId, 'document_category_id' => '0301']) }}"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-file-earmark-word"></i> Unduh Word
                </a>
                <a href="{{ route('doc.surat-perintah-penangkapan-document.edit', ['id' => $doc->id, 'accident_id' => $accidentId, 'document_category_id' => '0301']) }}"
                    class="btn btn-dark-blue">
                    <i class="bi bi-pencil"></i> Ubah
                </a>
            </div>
        </div>
    </div>
@endsection
