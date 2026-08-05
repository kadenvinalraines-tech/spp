@extends('layouts.admin')

@section('title', 'Laporan Kas')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Filter Laporan Buku Kas Umum</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.cash') }}" method="GET" class="row g-3 align-items-end">
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
        <h5 class="mb-0">Hasil Laporan Kas ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t['date'])->translatedFormat('d F Y') }}</td>
                            <td>
                                <span class="badge {{ $t['type'] == 'Pemasukan' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $t['type'] }}</span><br>
                                {{ $t['description'] }}
                            </td>
                            <td class="text-success">Rp {{ number_format($t['income'], 0, ',', '.') }}</td>
                            <td class="text-danger">Rp {{ number_format($t['expense'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">TOTAL</td>
                        <td class="text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end text-primary fs-5">SALDO PERIODE INI</td>
                        <td colspan="2" class="text-center text-primary fs-5">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
