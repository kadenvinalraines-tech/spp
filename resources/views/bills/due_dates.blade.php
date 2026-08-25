@extends('layouts.admin')

@section('title', 'Atur Jatuh Tempo Massal')

@section('content')
<div class="mb-3">
    <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tagihan
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kelompok Tagihan (Batch)</h5>
        <span class="badge bg-primary">Total Kelompok: {{ $batches->total() }}</span>
    </div>
    <div class="card-body">
        <p class="text-muted small">
            Daftar di bawah ini mengelompokkan tagihan-tagihan yang memiliki <strong>Pos Keuangan</strong>, <strong>Tahun Ajaran</strong>, dan <strong>Tanggal Jatuh Tempo</strong> yang sama. 
            Anda dapat mengubah tanggal jatuh tempo secara massal untuk semua siswa di kelompok tersebut.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Pos Keuangan</th>
                        <th>Jumlah Siswa / Tagihan</th>
                        <th>Tanggal Jatuh Tempo Saat Ini</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $index => $batch)
                        <tr>
                            <td>{{ $batches->firstItem() + $index }}</td>
                            <td>{{ $batch->academicYear->name ?? '-' }} <br><small class="text-muted">{{ $batch->academicYear->semester ?? '-' }}</small></td>
                            <td><strong>{{ $batch->financePost->name ?? '-' }}</strong></td>
                            <td><span class="badge bg-info">{{ $batch->total_bills }} Tagihan</span></td>
                            <td class="text-danger fw-bold">{{ \Carbon\Carbon::parse($batch->due_date)->translatedFormat('d F Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editBulkDueDateModal{{ $index }}" title="Edit Jatuh Tempo Massal">
                                    <i class="bi bi-calendar-event"></i> Edit Massal
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="editBulkDueDateModal{{ $index }}" tabindex="-1" aria-labelledby="editBulkDueDateModalLabel{{ $index }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editBulkDueDateModalLabel{{ $index }}">Ubah Jatuh Tempo Massal</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('bills.bulk-update-due-date') }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <input type="hidden" name="finance_post_id" value="{{ $batch->finance_post_id }}">
                                                    <input type="hidden" name="academic_year_id" value="{{ $batch->academic_year_id }}">
                                                    <input type="hidden" name="old_due_date" value="{{ $batch->due_date }}">
                                                    
                                                    <div class="alert alert-info">
                                                        Anda akan mengubah jatuh tempo untuk <strong>{{ $batch->total_bills }} tagihan</strong> pada pos <strong>{{ $batch->financePost->name ?? '-' }}</strong>.
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Jatuh Tempo Saat Ini</label>
                                                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($batch->due_date)->translatedFormat('d F Y') }}" disabled>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="new_due_date_{{ $index }}" class="form-label fw-bold">Tanggal Jatuh Tempo Baru</label>
                                                        <input type="date" class="form-control" id="new_due_date_{{ $index }}" name="new_due_date" value="{{ \Carbon\Carbon::parse($batch->due_date)->format('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada tagihan yang di-generate.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $batches->links() }}
        </div>
    </div>
</div>
@endsection
