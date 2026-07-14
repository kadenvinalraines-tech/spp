@extends('layouts.admin')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Master Kelas</h5>
        <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm">Tambah Kelas</a>
    </div>
    <div class="card-body">
        <form action="{{ route('classes.index') }}" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari nama kelas, tingkat, atau jurusan..." value="{{ $search ?? '' }}">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                @if($search ?? false)
                    <a href="{{ route('classes.index') }}" class="btn btn-outline-danger">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Jumlah Siswa</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $key => $class)
                        <tr>
                            <td>{{ $classes->firstItem() + $key }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->level }}</td>
                            <td>{{ $class->major ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $class->students_count }} Siswa</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('classes.show', $class->id) }}" class="btn btn-info btn-sm">Detail</a>
                                <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Data kelas tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $classes->links() }}
        </div>
    </div>
</div>
@endsection
