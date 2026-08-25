@extends('layouts.admin')

@section('title', 'Detail Tagihan Siswa')

@section('content')
<div class="mb-3">
    <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tagihan
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Profil Siswa</h5>
        <a href="{{ route('payments.index', ['student_id' => $student->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-cash"></i> Buka Kasir Pembayaran
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="150">Nama Lengkap</th>
                        <td>: {{ $student->name }}</td>
                    </tr>
                    <tr>
                        <th>NIS</th>
                        <td>: {{ $student->nis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. WhatsApp</th>
                        <td>: {{ $student->phone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="150">Status</th>
                        <td>: 
                            @if($student->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Total Tagihan</th>
                        <td>: <strong>Rp {{ number_format($bills->sum('total_amount'), 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Sisa Tunggakan</th>
                        <td class="text-danger">: <strong>Rp {{ number_format($bills->sum('total_amount') - $bills->sum('total_paid'), 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Riwayat Seluruh Tagihan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Invoice</th>
                        <th>T.A. / Semester</th>
                        <th>Jenis Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th>Nominal</th>
                        <th>Sudah Dibayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td>{{ $bill->invoice_number }}<br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                {{ $bill->academicYear->name ?? '-' }}<br>
                                <small class="text-muted">{{ $bill->academicYear->semester ?? '-' }}</small>
                            </td>
                            <td>{{ $bill->financePost->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d F Y') }}</td>
                            <td>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($bill->total_paid, 0, ',', '.') }}</td>
                            <td class="text-danger">Rp {{ number_format($bill->total_amount - $bill->total_paid, 0, ',', '.') }}</td>
                            <td>
                                @if($bill->status == 'paid')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($bill->status == 'partial')
                                    <span class="badge bg-warning">Sebagian</span>
                                @else
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editDueDateModal{{ $bill->id }}" title="Edit Jatuh Tempo">
                                    <i class="bi bi-calendar-event"></i> Edit
                                </button>
                                
                                @if($bill->status == 'unpaid' && $bill->total_paid == 0)
                                    <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tagihan ini secara permanen?');" title="Hapus Tagihan">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Tagihan yang sudah dibayar (sebagian/lunas) tidak bisa dihapus">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                @endif
                                
                                <!-- Edit Due Date Modal -->
                                <div class="modal fade" id="editDueDateModal{{ $bill->id }}" tabindex="-1" aria-labelledby="editDueDateModalLabel{{ $bill->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editDueDateModalLabel{{ $bill->id }}">Edit Jatuh Tempo</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('bills.update-due-date', $bill->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="due_date{{ $bill->id }}" class="form-label">Tanggal Jatuh Tempo Baru</label>
                                                        <input type="date" class="form-control" id="due_date{{ $bill->id }}" name="due_date" value="{{ \Carbon\Carbon::parse($bill->due_date)->format('Y-m-d') }}" required>
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
                            <td colspan="9" class="text-center">Siswa ini belum memiliki tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

