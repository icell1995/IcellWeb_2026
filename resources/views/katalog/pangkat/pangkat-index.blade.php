@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="text-blue-dark fw-semibold mb-2">Daftar Pangkat</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive mt-3">
                <table class="table align-middle table-striped table-bordered" id="dataTable" name="dataTable">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center">Nama Pangkat</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reference as $index => $ref)
                            <tr>
                                <td class="text-center">
                                    {{-- {{ $reference->currentPage() * $reference->perPage() - $reference->perPage() + ($index + 1) }} --}}
                                    {{ $index + 1 }}
                                </td>
                                <td>{{ $ref->name }}</td>
                                @if ($ref->state == 1)
                                    <td class="text-center"><span class="active">Aktif</span></td>
                                @else
                                    <td class="text-center"><span class="inactive">Tidak Aktif</span></td>
                                @endif
                                <td class="d-flex justify-content-center">
                                    <a class="btn btn-secondary mx-2" href={{ URL($name . '/' . $ref->id . '/edit') }}>Edit</a>
                                    <form method="POST" action="{{ route('pangkat.destroy', $ref->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mx-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
            });
        });
    </script>
@endpush
