@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Update Profile Information -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-circle text-primary"></i>
                <h6 class="mb-0 fw-bold">Informasi Profil</h6>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-key-fill text-warning"></i>
                <h6 class="mb-0 fw-bold">Ubah Password</h6>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="card border-danger mb-4">
            <div class="card-header d-flex align-items-center gap-2 border-danger bg-white">
                <i class="bi bi-trash3-fill text-danger"></i>
                <h6 class="mb-0 fw-bold text-danger">Hapus Akun</h6>
            </div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
