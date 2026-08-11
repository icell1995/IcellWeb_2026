@php
    $isCanEntryDocument = false;

    if(isset(Auth::user()->properties['is_can_entry_document'])) {
        $isCanEntryDocument = Auth::user()->properties['is_can_entry_document'];
    }
@endphp

<div class="box-body">

    @include('produktivitas.components.case-navigation')

    <div class="card">
        <div class="card">
            <div class="card-header tahapan d-flex justify-content-between align-items-center">
                <h5 class="fw-bold card-title m-0">
                    TERLAPOR
                </h5>
                @if (Auth::getUser()->role_id == 4 || Auth::getUser()->role_id == 1 || $isCanEntryDocument)
                    <a href="{{ route('case.participant.reported-person.create', ['accidentId' => $id, 'accident_id' => $id]) }}" class="btn btn-dark-blue">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Terlapor
                    </a>
                @endif
            </div>

            <div class="card-body">
                @if (session()->has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <table class="table table-striped table-bordered table-users" id="dataTable" name="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Jenis Kelamin</th>
                            <th class="text-center">Jenis Identitas</th>
                            <th class="text-center">Nomor Identitas</th>
                            <th class="text-center">Alamat</th>
                            <th class="text-center">No Telp</th>
                            <th class="text-center">Opsi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @if (!empty($reportedPersons))
                            @foreach ($reportedPersons as $reportedPerson)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $reportedPerson->name }} {{ (!empty($reportedPerson->alias_name)) ? ' (' . $reportedPerson->alias_name . ')' : '' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $reportedPerson->gender->name ?? '-' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $reportedPerson->identityType->name ?? '-' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $reportedPerson->identity_number }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $reportedPerson->address }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $reportedPerson->phone_number }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="{{ route('case.participant.reported-person.show', ['id' => $reportedPerson->id, 'accidentId' => $id, 'accident_id' => $id]) }}"
                                                    class="btn btn-success btn-sm m-1"><i class="bi bi-folder"></i> Lihat</a>
                                        <br>
                                        <a href="{{ route('case.participant.reported-person.edit', ['id' => $reportedPerson->id, 'accidentId' => $id, 'accident_id' => $id]) }}"
                                                    class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                        <br>
                                        <a href="{{ route('case.participant.reported-person.delete', ['id' => $reportedPerson->id, 'accidentId' => $id, 'accident_id' => $id]) }}"
                                            class="btn btn-danger btn-sm m-1" data-method="delete"
                                            data-token="{{ csrf_token() }}"
                                            data-confirm="Apakah Anda yakin ingin menghapus ini?"><i
                                                class="bi bi-trash"></i> Hapus</a>
                                    </td>
            
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('case-participants-script')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
            });
        });
    </script>
@endpush