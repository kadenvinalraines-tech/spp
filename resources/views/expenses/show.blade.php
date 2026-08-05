@extends('layouts.admin')

@section('title', 'Detail Pengeluaran')

@section('content')
<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informasi Pengajuan</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%" class="text-muted">Tanggal</td>
                        <td>: <strong>{{ $expense->date->translatedFormat('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>: {{ $expense->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diajukan Oleh</td>
                        <td>: {{ $expense->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nominal</td>
                        <td class="text-danger fs-5 fw-bold">: Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>: {{ $expense->description }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status Saat Ini</td>
                        <td>: 
                            @if($expense->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending / Menunggu Persetujuan</span>
                            @elseif($expense->status == 'approved')
                                <span class="badge bg-success">Disetujui oleh {{ $expense->approver->name ?? '-' }}</span>
                            @else
                                <span class="badge bg-danger">Ditolak oleh {{ $expense->approver->name ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($expense->status == 'rejected')
                    <tr>
                        <td class="text-muted">Alasan Penolakan</td>
                        <td class="text-danger">: {{ $expense->rejection_reason }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Bukti Nota / Struk</h5>
            </div>
            <div class="card-body text-center">
                @if($expense->receipt_path)
                    <img src="{{ Storage::url($expense->receipt_path) }}" class="img-fluid rounded border p-1" style="max-height: 250px" alt="Bukti Nota">
                    <div class="mt-3">
                        <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-up-right"></i> Buka Gambar Penuh</a>
                    </div>
                @else
                    <div class="text-muted py-5">
                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                        <p class="mt-2">Tidak ada foto bukti yang dilampirkan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($expense->status == 'pending')
<div class="card shadow-sm mt-2 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Aksi Persetujuan (Approval)</h5>
    </div>
    <div class="card-body">
        <p>Silakan periksa nominal dan bukti nota sebelum memberikan persetujuan. Setelah disetujui, uang dianggap telah keluar dari kas.</p>
        
        <div class="d-flex">
            <!-- Form Approve -->
            <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="me-3">
                @csrf
                <button type="submit" class="btn btn-success btn-lg px-4" onclick="return confirm('Setujui pengeluaran ini?');">
                    <i class="bi bi-check-circle"></i> Setujui Pengeluaran
                </button>
            </form>

            <!-- Tombol Reject Modal -->
            <button type="button" class="btn btn-danger btn-lg px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle"></i> Tolak
            </button>
        </div>
    </div>
</div>

<!-- Modal Penolakan -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('expenses.reject', $expense->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Pengajuan Pengeluaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-white">*</span></label>
                        <textarea class="form-control" name="rejection_reason" id="rejection_reason" rows="3" placeholder="Contoh: Bukti nota tidak jelas, atau anggaran tidak tersedia." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
</div>
@endsection
