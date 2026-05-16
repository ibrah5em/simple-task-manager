@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <h4 class="fw-bold mb-0" style="color:var(--text)">Profile</h4>
        <p style="color:var(--text-muted);font-size:.875rem">Manage your account details.</p>
    </div>

    {{-- Profile info card --}}
    <div class="col-lg-6">
        <div class="glass-card p-4">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Password card --}}
    <div class="col-lg-6">
        <div class="glass-card p-4">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Delete account card --}}
    <div class="col-lg-6">
        <div class="glass-card p-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
