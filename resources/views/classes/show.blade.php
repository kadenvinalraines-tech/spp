@extends('layouts.admin')

@section('title', 'Detail Kelas')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Kelas: {{ $class->name }}</h5>
        <div>
            <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-warning btn-sm">Edit Kelas</a>
            <a href="{{ route('classes.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Nama Kelas</th>
                        <td>{{ $class->name }}</td>
                    </tr>
                    <tr>
                        <th>Tingkat</th>
                        <td>{{ $class->level }}</td>
                    </tr>
                    <tr>
                        <th>Jurusan</th>
                        <td>{{ $class->major ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Siswa</th>
                        <td>{{ $students->total() }} Siswa</td>
                    </tr>
                </table>
            </div>
        </div>

        <h6 class="mb-3">Daftar Siswa di Kelas Ini</h6>
        
        <form action="{{ route('classes.show', $class->id) }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIS / Nama" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <a href="{{ route('classes.show', $class->id) }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIS/NISN</th>
                        <th>Nama Siswa</th>
                        <th>L/P</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                {{ $student->nis }}<br>
                                <small class="text-muted">{{ $student->nisn ?? '-' }}</small>
                            </td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>
                                <span class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'graduated' ? 'primary' : 'danger') }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-info btn-sm">Detail Siswa</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada siswa terdaftar di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
