@php
    $_title = 'Surat Permintaan Perpanjangan Penahanan';
    $courtShow = isset($doc->court_id) ? \App\Models\Lib\Court::query()->find($doc->court_id) : null;
@endphp

@extends('layouts.app')

@section('content')
    <a class="btn-back" href="{{ route('view_produktivitas_accident', ['accident_id' => $accidentId]) }}">
        <i class="bi bi-arrow-left"></i> Kembali ke Progres Perkara
    </a>

    <div class="box">
        <div class="box-header">
            <h5 class="fw-bold text-blue-dark">{{ $_title }}</h5>
        </div>

        <div class="box-body">
            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Nomor LP</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control font-weight-bold" readonly value="{{ $accident->no_lp ?? '-' }}">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Nomor Surat</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control font-weight-bold" readonly value="{{ $doc->document_number ?? '-' }}">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Tanggal Surat</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" readonly value="{{ $doc->document_date ?? '-' }}">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Identitas Tersangka</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" readonly value="{{ $doc->suspect?->name ?? '-' }}">
                </div>
            </div>

            <div class="input-group row mb-3 ms-0">
                <label class="fw-bold col-sm-2 col-form-label">Nama Pengadilan</label>
                <div class="col-lg-10 col-md-10 col-sm-12 col-12 d-flex align-self-center">
                    <input type="text" class="form-control" readonly value="{{ $courtShow ? ($courtShow->full_name ?: $courtShow->name) : '-' }}">
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('doc.permintaan-perpanjangan-penahanan-document.edit', ['id' => $doc->id, 'accident_id' => $accidentId, 'document_category_id' => '0603']) }}"
                    class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
            </div>
        </div>
    </div>
@endsection

