@php
    $_title = 'Katalog Polda'
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
        <h3 class="fw-bold text-blue-dark">Daftar Polda</h3>
    </div>

    <div class="box-body">
        <div class="table-responsive mt-3">
            <table class="display table table-bordered table-dpo" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle text-blue-dark">No</th>
                        <th class="text-center align-middle text-blue-dark">Nama Polda</th>
                        <th class="text-center align-middle text-blue-dark">Kode Polda</th>
                        <th class="text-center align-middle text-blue-dark">GMT</th>
                        <th class="text-center align-middle text-blue-dark">Status</th>
                        <th class="text-center align-middle text-blue-dark">Action</th>
                    </tr>
                </thead>
                @foreach($polda as $index => $poldas)
                <tbody>
                    <th class="text-center align-middle" scope="row">{{ $index + $polda->firstItem() }}</th>
                    <td class="text-center align-middle text-blue-dark fw-bold">{{ $poldas->name}}</td>
                    <td class="text-center align-middle text-blue-dark">{{ $poldas->id}}</td>
                    <td class="text-center align-middle text-blue-dark">{{ $poldas->timezone}}</td>
                    <td class="text-center align-middle text-blue-dark">
                        @if($poldas->state == 1)
                        <span style="display: inline-block; text-align: center; background-color: #00FF00;
                        padding: 3px 8px; border-radius: 5px; font-weight: 700;">Aktif</span>
                        @else
                        <span style="display: inline-block; text-align: center; background-color: #ee354f;
                        padding: 3px 8px; border-radius: 5px; font-weight: 700;">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-success mb-1"><a
                                href="{{ URL($name.'/'.$poldas->id.'/edit') }}">Edit</a></button>
                        <form method="POST" action="{{ route('polda.destroy', $poldas->id) }}">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tbody>
                @endforeach
            </table>
            <div class="pull-left">
                {{ $polda->links() }}
            </div>
            <div class="text-start mb-2">
                Menampilkan
                <span class="font-blue font-w">
                    {{(($polda->currentPage()*$polda->perPage())-$polda->perPage())+1}}-{{(($polda->currentPage()*$polda->perPage())>$polda->total())
                    ?$polda->total()
                    :($polda->currentPage()*$polda->perPage())}}
                </span>
                dari
                <span class="font-blue font-w">{{$polda->total()}}</span>
                hasil
                <div class="text-end">
                    <button id="btnPolda" type="button" class="material-icons floating-btn" data-toggle="modal"
                    data-target="#add-data">add</button>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="pull-right"> --}}
        {{-- <button type="button" class="material-icons floating-btn" data-toggle="modal" data-target="#add-data">
            <a href={{ URL($name.'/create') }} style="text-decoration: none; color: #ffffff">add</a></button> --}}
        
    {{-- </div> --}}
</div>
@endsection

@push('script')
<script>
    $('#btnPolda').on("click",function(){
        window.location = 'polda/create'
    })
</script>
@endpush
