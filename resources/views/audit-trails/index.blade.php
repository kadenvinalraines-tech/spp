@extends('layouts.admin')

@section('title', 'Audit Trail')

@section('content')
@php
    $modelNames = [
        'App\\Models\\Student' => 'Data Siswa',
        'App\\Models\\User' => 'Pengguna',
        'App\\Models\\Bill' => 'Tagihan',
        'App\\Models\\BillDetail' => 'Detail Tagihan',
        'App\\Models\\Payment' => 'Pembayaran',
        'App\\Models\\PaymentDetail' => 'Detail Pembayaran',
        'App\\Models\\FinancePost' => 'Pos Keuangan',
        'App\\Models\\Expense' => 'Pengeluaran',
        'App\\Models\\ExpenseCategory' => 'Kategori Pengeluaran',
        'App\\Models\\SchoolClass' => 'Kelas',
        'App\\Models\\AcademicYear' => 'Tahun Ajaran',
        'App\\Models\\Role' => 'Hak Akses',
        'App\\Models\\SchoolSetting' => 'Pengaturan',
        'App\\Models\\StudentClass' => 'Kelas Siswa',
    ];
@endphp

<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill text-primary"></i>
        <h6 class="mb-0 fw-bold">Filter Log Aktivitas</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('audit-trails.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua Aksi</option>
                    <option value="login" {{ $action == 'login' ? 'selected' : '' }}>Masuk ke Sistem (Login)</option>
                    <option value="logout" {{ $action == 'logout' ? 'selected' : '' }}>Keluar dari Sistem (Logout)</option>
                    <option value="create" {{ $action == 'create' ? 'selected' : '' }}>Menambahkan Data Baru</option>
                    <option value="update" {{ $action == 'update' ? 'selected' : '' }}>Mengubah Data</option>
                    <option value="delete" {{ $action == 'delete' ? 'selected' : '' }}>Menghapus Data</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        <h6 class="mb-0 fw-bold">Daftar Riwayat Aktivitas</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th>Bagian / Menu</th>
                        <th>Alamat IP</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditTrails as $log)
                        <tr>
                            <td>
                                <div class="fw-semibold" style="font-size: .85rem;">{{ $log->created_at->translatedFormat('d F Y') }}</div>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: var(--primary-100, #dbeafe); color: var(--primary-700, #1d4ed8); font-weight: 700; font-size: .7rem;">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <span style="font-size: .875rem;">{{ $log->user->name ?? 'Sistem' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($log->action == 'login')
                                    <span class="badge bg-success"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk</span>
                                @elseif($log->action == 'logout')
                                    <span class="badge bg-secondary"><i class="bi bi-box-arrow-right me-1"></i>Keluar</span>
                                @elseif($log->action == 'create')
                                    <span class="badge bg-primary"><i class="bi bi-plus-circle me-1"></i>Tambah</span>
                                @elseif($log->action == 'update')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-pencil me-1"></i>Ubah</span>
                                @elseif($log->action == 'delete')
                                    <span class="badge bg-danger"><i class="bi bi-trash me-1"></i>Hapus</span>
                                @else
                                    <span class="badge bg-info">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->model_type)
                                    <span style="font-size: .875rem;">{{ $modelNames[$log->model_type] ?? class_basename($log->model_type) }}</span>
                                    <small class="text-muted d-block">#{{ $log->model_id }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><code style="font-size: .78rem;">{{ $log->ip_address }}</code></td>
                            <td class="text-center">
                                <a href="{{ route('audit-trails.show', $log->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i>Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada riwayat aktivitas yang tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($auditTrails->hasPages())
    <div class="card-footer bg-white d-flex justify-content-end">
        {{ $auditTrails->links() }}
    </div>
    @endif
</div>
@endsection
