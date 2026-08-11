@php
    $_title = 'Katalog Polres';
@endphp

@extends('layouts.app')
@section('content')
<div class="box">
    <div class="box-header">
        @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        <h3 class="fw-bold text-blue-dark">Daftar Polres</h5>
        </div>
        
        <div class="box-body">
            <div class="table-responsive mt-3">
                <table class="display table table-bordered table-dpo" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center align-middle text-blue-dark">No</th>
                            <th class="text-center align-middle text-blue-dark">Nama Polres</th>
                            <th class="text-center align-middle text-blue-dark">Kode Polres</th>
                            <th class="text-center align-middle text-blue-dark">Nama Polda</th>
                            <th class="text-center align-middle text-blue-dark">Kode Polda</th>
                            <th class="text-center align-middle text-blue-dark">Status</th>
                            <th class="text-center align-middle text-blue-dark">Action</th>
                        </tr>
                    </thead>
                    @foreach($polres as $index => $polress)
                    <tbody>
                        <th class="text-center align-middle" scope="row">{{ $index + $polres->firstItem() }}</th>
                        <td class="text-center align-middle text-blue-dark fw-bold">{{ $polress->name }}</td>
                        <td class="text-center align-middle text-blue-dark">{{ $polress->id }}</td>
                        <td class="text-center align-middle text-blue-dark">{{ $polress->polda->name }}</td>
                        <td class="text-center align-middle text-blue-dark">{{ $polress->polda_id }}</td>
                        <td class="text-center align-middle text-blue-dark">
                            @if($polress->state == 1)
                            <span style="display: inline-block; text-align: center; background-color: #00FF00;
                            padding: 3px 8px; border-radius: 5px; font-weight: 700;">Aktif</span>
                            @else
                            <span style="display: inline-block; text-align: center; background-color: #ee354f;
                            padding: 3px 8px; border-radius: 5px; font-weight: 700;">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-success mb-1"><a
                                    href="{{ URL($name.'/'.$polress->id.'/edit') }}">Edit</a></button>
                        <form method="POST" action="{{ route('polres.destroy', $polress->id) }}">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        </td>
                    </tbody>
                    @endforeach
                </table>
                <div class="pull-left">
                    {{ $polres->links() }}
                </div>
                <div class="text-start mb-2">
                    Menampilkan
                    <span class="font-blue font-w">
                        {{(($polres->currentPage()*$polres->perPage())-$polres->perPage())+1}}-{{(($polres->currentPage()*$polres->perPage())>$polres->total())
                        ?$polres->total()
                        :($polres->currentPage()*$polres->perPage())}}
                    </span>
                    dari
                    <span class="font-blue font-w">{{$polres->total()}}</span>
                    hasil
                    <div class="text-end">
                        <button id="btnPolres" type="button" class="material-icons floating-btn" data-toggle="modal"
                data-target="#add-data">add</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="text-end"> --}}
            {{-- <button type="button" class="material-icons floating-btn" data-toggle="modal" data-target="#add-data">
                <a href={{ URL($name.'/create') }} style="text-decoration: none; color: #ffffff">add</a></button> --}}
            
        {{-- </div> --}}
    </div>
</div>
@endsection

@push('script')
<script>
    $('#btnPolres').on("click",function(){
        window.location = 'polres/create'
    })
</script>
@endpush

{{-- <div class="content">
    <div class="col-md-12">
        <h3 class="box-title"><a href={{ URL($name . '/create' ) }}>Tambah {{ $name }}</a></h3>
    </div>
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
                @endif
                <h3 class="box-title">Daftar Polda<h3>

            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Polres</th>
                        <th>Kode Polres</th>
                        <th>Nama Polda</th>
                        <th>Kode Polda</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                @foreach ($polres as $index => $polress)
                <tbody>
                    <tr>
                        <th scope="row">{{ $index + $polres->firstItem() }}</th>
                        {{-- <td>{{ $polres->firstItem() + $key }}</td>
                        <td>{{ $polress->name }}</td>
                        <td>{{ $polress->id }}</td>
                        <td>{{ $polress->polda->name }}</td>
                        <td>{{ $polress->polda_id }}</td>
                        <td>{{ $polress->state == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                        <td>
                            <a href="{{ URL($name . '/' . $polress->id . '/edit') }}">edit</a>
                        </td>
                    </tr>
                </tbody>
                @endforeach
            </table>
            <div class="pull-left">
                Showing
                {{$polres->firstItem()}}
                to
                {{$polres->lastItem()}}
                of
                {{$polres->total()}}
            </div>

            <div class="pull-right">
                {{$polres->links()}}
            </div>
            {{-- {{ $polres->links() }}
        </div>
    </div>
</div> --}}

