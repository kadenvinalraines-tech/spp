@extends('layouts.admin')

@section('title', 'Tahun Ajaran')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Tahun Ajaran</h5>
        <a href="{{ route('academic-years.create') }}" class="btn btn-primary btn-sm">Tambah Tahun Ajaran</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $index => $year)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $year->name }}</td>
                            <td>{{ $year->semester }}</td>
                            <td>{{ \Carbon\Carbon::parse($year->start_date)->translatedFormat('d F Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($year->end_date)->translatedFormat('d F Y') }}</td>
                            <td>
                                @if($year->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aktif Saat Ini</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                @if(!$year->is_active)
                                <form action="{{ route('academic-years.set-active', $year->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Jadikan tahun ajaran ini aktif?');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-success btn-sm" title="Jadikan Aktif"><i class="bi bi-check-lg"></i></button>
                                </form>
                                @endif
                                <a href="{{ route('academic-years.edit', $year->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('academic-years.destroy', $year->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tahun ajaran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data Tahun Ajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $academicYears->links() }}
        </div>
    </div>
</div>
@endsection
