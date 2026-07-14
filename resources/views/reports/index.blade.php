@extends('layouts.admin')

@section('title', 'Modul Laporan')

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm text-center h-100 py-3 border-primary border-bottom border-3">
            <div class="card-body">
                <i class="bi bi-wallet2 text-primary mb-3" style="font-size: 3rem;"></i>
                <h5 class="card-title">Buku Kas</h5>
                <p class="text-muted small">Gabungan Pemasukan & Pengeluaran untuk melihat Saldo.</p>
                <a href="{{ route('reports.cash') }}" class="btn btn-outline-primary mt-2">Buka Laporan</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm text-center h-100 py-3 border-success border-bottom border-3">
            <div class="card-body">
                <i class="bi bi-cash-coin text-success mb-3" style="font-size: 3rem;"></i>
                <h5 class="card-title">Pemasukan</h5>
                <p class="text-muted small">Rekapitulasi pembayaran SPP & Tagihan siswa.</p>
                <a href="{{ route('reports.payment') }}" class="btn btn-outline-success mt-2">Buka Laporan</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card shadow-sm text-center h-100 py-3 border-warning border-bottom border-3">
            <div class="card-body">
                <i class="bi bi-cart-dash text-warning mb-3" style="font-size: 3rem;"></i>
                <h5 class="card-title">Pengeluaran</h5>
                <p class="text-muted small">Rincian uang keluar operasional yang disetujui.</p>
                <a href="{{ route('reports.expense') }}" class="btn btn-outline-warning mt-2">Buka Laporan</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card shadow-sm text-center h-100 py-3 border-danger border-bottom border-3">
            <div class="card-body">
                <i class="bi bi-person-exclamation text-danger mb-3" style="font-size: 3rem;"></i>
                <h5 class="card-title">Tunggakan</h5>
                <p class="text-muted small">Daftar siswa dengan tagihan yang belum lunas.</p>
                <a href="{{ route('reports.arrear') }}" class="btn btn-outline-danger mt-2">Buka Laporan</a>
            </div>
        </div>
    </div>
</div>
@endsection
