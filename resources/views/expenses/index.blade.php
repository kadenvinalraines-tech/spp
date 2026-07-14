@extends('layouts.admin')

@section('title', 'Daftar Pengeluaran')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Pengeluaran Kas</h5>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">Ajukan Pengeluaran</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Diajukan Oleh</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->date->format('d M Y') }}</td>
                            <td>{{ Str::limit($expense->description, 40) }}</td>
                            <td><span class="badge bg-secondary">{{ $expense->category->name ?? '-' }}</span></td>
                            <td>{{ $expense->user->name ?? '-' }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($expense->status == 'pending')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                                @elseif($expense->status == 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>
                                    <br><small class="text-muted">oleh {{ $expense->approver->name ?? '-' }}</small>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-info btn-sm text-white">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection
