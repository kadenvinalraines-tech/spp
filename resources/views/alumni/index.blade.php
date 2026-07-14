@extends('layouts.admin')

@section('title', 'Daftar Alumni')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Alumni</h5>
        <form action="{{ route('alumni.bulk-destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA data alumni yang SUDAH LUNAS? Tindakan ini tidak dapat dibatalkan.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="bi bi-trash-fill"></i> Hapus Semua Lunas
            </button>
        </form>
    </div>
    <div class="card-body">
        <form action="{{ route('alumni.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIS, NISN, atau Nama..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <select name="academic_year_id" class="form-select">
                    <option value="">-- Semua Tahun Ajaran Kelulusan --</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $academic_year_id == $ay->id ? 'selected' : '' }}>
                            TA {{ $ay->name }} ({{ $ay->semester }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
            @if($search || $academic_year_id)
            <div class="col-md-2">
                <a href="{{ route('alumni.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS / NISN</th>
                        <th>Nama Alumni</th>
                        <th>Terakhir Kelas</th>
                        <th>Status Tunggakan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alumnis as $index => $alumni)
                        <tr>
                            <td>{{ $alumnis->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold">{{ $alumni->nis }}</span><br>
                                <small class="text-muted">{{ $alumni->nisn ?? '-' }}</small>
                            </td>
                            <td>
                                <strong>{{ $alumni->name }}</strong>
                            </td>
                            <td>
                                @if($alumni->schoolClass)
                                    {{ $alumni->schoolClass->name }} 
                                    <br><small class="text-muted">TA {{ $alumni->latest_academic_year_name }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($alumni->tunggakan > 0)
                                    <span class="badge bg-danger">Ada Tunggakan</span>
                                    <br>
                                    <small class="text-danger fw-bold">Rp {{ number_format($alumni->tunggakan, 0, ',', '.') }}</small>
                                @else
                                    <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('alumni.show', $alumni->id) }}" class="btn btn-sm btn-info mb-1" title="Detail Alumni">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="{{ route('alumni.edit', $alumni->id) }}" class="btn btn-sm btn-warning mb-1" title="Edit Alumni">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                @if($alumni->tunggakan > 0)
                                    <form action="{{ route('alumni.send-wa', $alumni->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim pemberitahuan tagihan via WhatsApp ke alumni ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-1" title="Kirim Tagihan via WA">
                                            <i class="bi bi-whatsapp"></i> WA
                                        </button>
                                    </form>
                                @endif
                                
                                @if($alumni->tunggakan == 0)
                                    <form action="{{ route('alumni.destroy', $alumni->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen data alumni ini? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus Permanen">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-secondary mb-1" title="Tidak dapat dihapus karena masih ada tunggakan" disabled>
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data alumni yang sesuai pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $alumnis->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
