@php
    $pageParam = request()->query('page');
    $policeId = request()->query('policeId');

    $pageParam = strtolower($pageParam);
@endphp

<div class="card my-4">
    <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
            <a class="nav-link {{(empty($pageParam) || $pageParam == 'active') ? 'active' : null}}" aria-current="page" href="{{ route('personnel.index', ['policeId' => $policeId]) }}">Anggota Aktif</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{($pageParam == 'inactive') ? 'active' : null}}" href="{{ route('personnel.index', ['page' => 'inactive', 'policeId' => $policeId]) }}">Anggota Tidak Aktif</a>
        </li>

        @if(Auth::user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link {{($pageParam == 'verification') ? 'active' : null}}" href="{{ route('personnel.index', ['page' => 'verification', 'policeId' => $policeId]) }}">Verifikasi Request</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{($pageParam == 'signatory') ? 'active' : null}}" href="{{ route('personnel.index', ['page' => 'signatory']) }}">Pejabat TTE</a>
            </li>
        @endif
    </ul>      
</div>