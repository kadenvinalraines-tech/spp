@extends('layouts.admin')

@section('title', 'Detail Siswa')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Data Siswa</h5>
        <div>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                @if($student->photo_url)
                    <img src="{{ $student->photo_url }}" alt="Foto {{ $student->name }}" class="img-thumbnail" style="max-width: 100%;">
                @else
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 200px; height: 250px;">
                        Tanpa Foto
                    </div>
                @endif
            </div>
            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">NIS / NISN</th>
                        <td>{{ $student->nis }} / {{ $student->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>{{ $student->schoolClass->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $student->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>{{ $student->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'graduated' ? 'primary' : 'danger') }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Terdaftar Pada</th>
                        <td>{{ $student->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
