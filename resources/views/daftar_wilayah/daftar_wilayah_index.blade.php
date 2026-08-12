@php
    $_title = 'Daftar Wilayah';
@endphp

@extends('layouts.app')

@section('content')

<div class="content col-lg-12 col-md-12 col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="text-blue-dark fw-semibold mb-2">Daftar Wilayah</h3>
            </div>

            <div class="box-body">
                <div class="table-responsive mt-3">
                    <table class="display table table-bordered table-dafta-wilayah" cellspacing="0" width="100%" id="dataTable" name="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center align-middle text-blue-dark">No</th>
                                <th class="text-center align-middle text-blue-dark">POLDA</th>
                                <th class="text-center align-middle text-blue-dark">POLRES</th>
                                <th class="text-center align-middle text-blue-dark">Alamat</th>
                                <th class="text-center align-middle text-blue-dark">Status</th>
                            </tr>
                        </thead>
                        @foreach($officer as $key => $ofc)
                        <tbody>
                            <th class="text-center align-middle text-blue-dark">{{ $officer->firstItem() + $key }}</th>
                            <td class="align-middle">{{$ofc->polda_name}}</td>
                            <td class="align-middle">{{$ofc->polres_name}}</td>
                            <td class="align-middle">{{$ofc->polres_alamat}}</td>
                            <td class="align-middle text-center">
                                @if($ofc->state == 1)
                                <span style="display: inline-block; text-align: center; background-color: #00FF00;
                                padding: 3px 8px; border-radius: 5px; font-weight: 700;">Aktif</span>
                                @else
                                <span style="display: inline-block; text-align: center; background-color: #ee354f;
                                padding: 3px 8px; border-radius: 5px; font-weight: 700;">Tidak Aktif</span>
                                @endif
                            </td>
                        </tbody>
                        @endforeach
                    </table>
                    <div class="pull-left">
                        Showing
                        {{$officer->firstItem()}}
                        to
                        {{$officer->lastItem()}}
                        of
                        {{$officer->total()}}
                    </div>
                    <div id="pagination" class="pull-right">
                        {{$officer->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- <div class="border light content col-lg-12 col-md-12 col-xs-12">
    <h1 class="text-center"> Daftar wilayah </h1>
    @foreach($officer as $key => $ofc)
    <div class="card ">
        <div class="card-body">
            <div> No : {{ $officer->firstItem() + $key }} </div>
            <div> Polda : {{$ofc->polda_name}} </div>
            <div> Polres : {{$ofc->polres_name}} </div>
        </div>
    </div>
    @endforeach

    <div id="pagination">
        {{$officer->links()}}
    </div>
    <div class="pull-left">
        Showing
        {{$officer->firstItem()}}
        to
        {{$officer->lastItem()}}
        of
        {{$officer->total()}}
    </div>

</div> --}}



@push('script')
<script type="text/javascript">
    $("#tglKejadian").datepicker({
        format  : 'yyyy-mm-dd'
    });
</script>

@endpush

@endsection
