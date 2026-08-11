@php
    $pageParam = request()->query('page');

    $pageParam = strtolower($pageParam);
@endphp

<div class="card my-4">
    <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
            <a class="nav-link {{(empty($pageParam)) ? 'active' : null}}" aria-current="page" href="{{route('view_produktivitas_accident', ['accident_id' => request()->query('accident_id')])}}">
                <i class="bi bi-file-earmark fs-4"></i> Berkas Perkara
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{($pageParam == 'participants') ? 'active' : null}}" href="{{route('view_produktivitas_accident', ['accident_id' => request()->query('accident_id'), 'page'=>'participants'])}}">
                <i class="bi bi-people fs-4"></i> Pihak Terlibat
            </a>
        </li>
    </ul>      
</div>