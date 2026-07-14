@extends('layouts.admin')

@section('title', 'Dashboard Yayasan')

@section('content')
<div class="card border-0 mb-4 text-white" style="border-radius: 16px; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); overflow: hidden; box-shadow: 0 10px 30px rgba(124,58,237,0.15);">
    <div class="card-body p-4 p-lg-5 position-relative">
        <div class="position-relative z-1">
            <h3 class="fw-bold mb-2" style="letter-spacing: -0.02em;">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
            <p class="mb-0 text-white-50" style="font-size: 1.05rem; max-width: 600px;">
                Anda login sebagai <strong class="text-white fw-semibold">Yayasan</strong>. Anda memiliki akses monitoring untuk memantau ringkasan laporan keuangan dan statistik sekolah.
            </p>
        </div>
        <!-- Decorative Icon -->
        <i class="bi bi-building position-absolute text-white" style="font-size: 12rem; right: -20px; bottom: -40px; opacity: 0.1; transform: rotate(-10deg);"></i>
    </div>
</div>

@include('dashboard._stats')

@endsection
