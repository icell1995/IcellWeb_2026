@php
    $pageParam = request()->query('page');

    $pageParam = strtolower($pageParam);
@endphp

<div class="card my-4">
    <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
            <a class="nav-link {{(empty($pageParam)) ? 'active' : null}}" aria-current="page" href="">
                <i class="bi bi-file-earmark fs-4"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{($pageParam == 'participants') ? 'active' : null}}" href="">
                <i class="bi bi-people fs-4"></i> 
            </a>
        </li>
    </ul>      
</div>