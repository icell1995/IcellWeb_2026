@php
  $_title = 'Cari Rekap';
@endphp

@extends('layouts.app')

@section('content')
<div class="border light content col-lg-12 col-md-12 col-xs-12">
    <div class="card">
      <h1 class="text-center"> Halaman Rekap </h1>
      <div class="card-body">
      <form method="GET" class="search" action="{{route('rekap-search')}}">
            <div class="form-row">
              <div class="col-md-4">
                <input type="text" id="no_LP" class="form-control"name="no_lp" placeholder="Nomor lp" value="{{old('no_LP')}}">
              </div>

              <div class="col-md-4">
                <input class="form-control datepicker" type="text" id="tglKejadian" name="bulan" placeholder="Masukan Tanggal Kejadiana disini" value="{{old('tanggal_kejadian')}}" autocomplete="off">
              </div>

              <div class="col-md-4">
                <input type="text" id="status" name="status" class="form-control" placeholder="Status" value="{{old('status')}}">
              </div>
            </div>
            <div class="p-2">
                <button type="submit" id="search_btn" class="btn btn-primary"> {{ __('Search') }} </button>
            </div>
      </form>

      <div class="table-responsive pagination">
      @if(isset($accidentsearch))
        <table class="center display" width="100%">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">No LP</th>
              <th scope="col">Tanggal Kejadian</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
          @if(count($accidentsearch) > 0)
            @foreach($accidentsearch as $sch)
                <tr>
                <th scope="row">{{$loop->iteration}}</th>
                <td>{{$sch->no_lp}}</td>
                <td>{{$sch->accident_date}}</td>
                <td>{{$sch->state}}</td>
                <td><a href="/rekap/rekap-show/{{ $sch->id }}">Lihat </a></td>
                </tr>
            @endforeach
          @else
            <tr><td> User not found</tr></td>
          @endif
          </tbody>
        </table>
        @endif
      </div>


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
