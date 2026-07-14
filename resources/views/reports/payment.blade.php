@extends('layouts.admin')

@section('title', 'Laporan Pemasukan / Pembayaran')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Filter Laporan Pemasukan (Pembayaran SPP/Tagihan)</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.payment') }}" method="GET" class="row g-3 align-items-end">
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
        <h5 class="mb-0">Daftar Pemasukan ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Petugas</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td>{{ $p->transaction_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->date)->format('d M Y') }}</td>
                            <td>{{ $p->student->name ?? '-' }} <br><small class="text-muted">{{ $p->student->nis ?? '-' }}</small></td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td>{{ $p->payment_method }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">TOTAL PEMASUKAN</td>
                        <td class="text-success fs-5">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
