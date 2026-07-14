@extends('layouts.admin')

@section('title', 'Hak Akses & Roles')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Roles (Hak Akses)</h5>
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">Tambah Role Baru</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Role</th>
                        <th>Daftar Akses (Permissions)</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $role->name }}</td>
                            <td>
                                @if($role->name === 'Super Admin')
                                    <span class="badge bg-success">All Access (Bypass)</span>
                                @else
                                    @foreach($role->permissions as $perm)
                                        <span class="badge bg-secondary mb-1">{{ $perm->name }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if($role->name !== 'Super Admin')
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endif
                                @if($role->name !== 'Super Admin')
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus role ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data Role.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
