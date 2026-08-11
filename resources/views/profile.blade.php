@php
    $_title = 'Profile';
@endphp

@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                <div class="card shadow border-0 profile-card">
                    <div class="card-body text-center">
                        <img src="/image-profile/profile640/{{ $user->avatar }}" class="rounded-circle mb-3">
                        <h5 class="fw-bold text-blue-dark">{{ $user->first_name ?? null }}{{ ' ' }}{{ $user->last_name ?? null }}</h5>
                        <p>{{ $user->pangkat ?? null }} / {{ $user->register_number ?? null }}</p>
                        <p>{{ $user->position->name ?? null }}</p>
                        <form class="mt-3" enctype="multipart/form-data" action="/update_profile" method="POST">
                            @csrf
                            <label for="" class="fw-bold mb-2">Update Profile</label>
                            <div class="input-group">
                                <input type="file" name="avatar" class="form-control" aria-describedby="inputAvatar">
                                <input type="submit" class="btn btn-primary" id="inputAvatar"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                <div class="col-12 mb-3">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('Nama') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"
                                        value="{{ $user->first_name }}{{ ' ' }}{{ $user->last_name }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('E-mail') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ $user->email }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('Jenis Kelamin') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"
                                        value="@if(isset($user->officer->gender->name)) @if ($user->officer->gender->name == null) -@else {{ $user->officer->gender->name ?? null }} @endif @endif"
                                        disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('Agama') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"
                                        value="@if(isset($user->officer->religion->name)) @if ($user->officer->religion->name == null) -@else {{ $user->officer->religion->name ?? null }} @endif @endif"
                                        disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('Tingkat Akses') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ $user->role->name }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('Polda') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"
                                        value="@if ($user->polda_id == null)- @else {{ $user->polda->name ?? null }} @endif"
                                        disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label fw-bold">{{ __('Polres') }}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control"
                                        value="@if ($user->polres_id == null)- @else{{ $user->polres->name ?? null }} @endif"
                                        disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="col-2 selra text-center">
                                    <h6 class="fw-bold">P21</h6>
                                    <p class="count-selra">{{ $userData->total_p21 }}</p>
                                </div>
                                <div class="col-2 selra text-center">
                                    <h6 class="fw-bold">SP3</h6>
                                    <p class="count-selra">{{ $userData->total_sp3 }}</p>
                                </div>
                                <div class="col-2 selra text-center">
                                    <h6 class="fw-bold">DIVERSI</h6>
                                    <p class="count-selra">{{ $userData->total_diversi }}</p>
                                </div>
                                <div class="col-2 selra text-center">
                                    <h6 class="fw-bold">POM/TNI</h6>
                                    <p class="count-selra">{{ $userData->total_pom_tni }}</p>
                                </div>
                                <div class="col-2 selra text-center">
                                    <h6 class="fw-bold">SP2LID</h6>
                                    <p class="count-selra">{{ $userData->total_sp2lid }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
