@php
    $_title = 'Struktur Organisasi'
@endphp

@extends('layouts.app')

@section('content')

<div class="content col-lg-12 col-md-12 col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="text-blue-dark fw-semibold mb-2">Struktur Organisasi</h3>
            </div>

            <div class="box-body">
                <form method="post" action="{{route('store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-lg-12 mb-2">
                            <input type="file" class="form-control" required name="filename">
                        </div>
                        <div class="col-lg-6 text-start">
                            <button type="submit" class="btn btn-dark-blue">Add</button>
                        </div>
                    </div>
                </form>

                <div class="struktur-organisasi">
                    @foreach ($image as $key => $images)
                    <img src="{{asset('struktur-organisasi/'. $images->filename)}}">
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <form method="post" action="{{route('delete_img', $images->filename)}}">
                        @csrf
                        <div class="">
                            <button class="btn btn-danger" value="{{$images->filename}}"
                                type="submit"> Hapus </button>
                        </div>
                    </form>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('script')
    <script>
    </script>
    @endpush
