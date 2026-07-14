@extends('layouts.admin')

@section('title', 'Detail Alumni')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Profil Alumni</h5>
            </div>
            <div class="card-body text-center">
                @if($alumni->photo_url)
                    <img src="{{ $alumni->photo_url }}" alt="Foto" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; font-size: 3rem;">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
                <h5 class="fw-bold">{{ $alumni->name }}</h5>
                <p class="text-muted mb-1">{{ $alumni->nis }} / {{ $alumni->nisn ?? '-' }}</p>
                <span class="badge bg-primary">Alumni</span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-gender-ambiguous me-2"></i> Jenis Kelamin</span>
                    <span>{{ $alumni->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-telephone me-2"></i> No. Telp</span>
                    <span>{{ $alumni->phone ?? '-' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building me-2"></i> Terakhir Kelas</span>
                    <span>{{ $alumni->schoolClass->name ?? '-' }} (TA {{ $alumni->latest_academic_year_name }})</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Rincian Tunggakan</h5>
                <a href="{{ route('alumni.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if($unpaidBills->count() > 0)
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Alumni ini memiliki total tunggakan sebesar 
                        <strong>Rp {{ number_format($unpaidBills->sum(function($b) { return $b->total_amount - $b->total_paid; }), 0, ',', '.') }}</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Pos Tagihan</th>
                                    <th>Tahun Ajaran</th>
                                    <th class="text-end">Total Tagihan</th>
                                    <th class="text-end">Telah Dibayar</th>
                                    <th class="text-end">Sisa Tunggakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unpaidBills as $bill)
                                @php
                                    $sisa = $bill->total_amount - $bill->total_paid;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $bill->financePost->name }}</span>
                                    </td>
                                    <td>{{ $bill->academicYear->name }} ({{ $bill->academicYear->semester }})</td>
                                    <td class="text-end">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($bill->total_paid, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger fw-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Alumni Bersih dari Tunggakan!</strong><br>
                            Alumni ini telah melunasi semua tagihan administrasinya.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
