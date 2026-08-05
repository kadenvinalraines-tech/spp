@extends('layouts.admin')

@section('title', 'Laporan Pengeluaran')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Filter Laporan Pengeluaran</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.expense') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="action" value="view" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                <button type="submit" name="action" value="print" class="btn btn-secondary" formtarget="_blank"><i class="bi bi-printer"></i> Cetak</button>
                <button type="submit" name="action" value="pdf" class="btn btn-danger"><i class="bi bi-file-pdf"></i> PDF</button>
                <button type="submit" name="action" value="excel" class="btn btn-success"><i class="bi bi-file-excel"></i> Excel</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Daftar Pengeluaran Disetujui ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Diajukan Oleh</th>
                        <th>Disetujui Oleh</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($e->date)->translatedFormat('d F Y') }}</td>
                            <td>{{ $e->category->name ?? '-' }}</td>
                            <td>{{ $e->description }}</td>
                            <td>{{ $e->user->name ?? '-' }}</td>
                            <td>{{ $e->approver->name ?? '-' }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada pengeluaran yang disetujui.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">TOTAL PENGELUARAN</td>
                        <td class="text-danger fs-5">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
