@extends('layouts.admin')

@section('title', 'Kategori Pengeluaran')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kategori Pengeluaran</h5>
        <a href="{{ route('expense-categories.create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $cat)
                        <tr>
                            <td>{{ $categories->firstItem() + $index }}</td>
                            <td>{{ $cat->name }}</td>
                            <td>{{ Str::limit($cat->description, 50) }}</td>
                            <td>
                                <a href="{{ route('expense-categories.edit', $cat->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('expense-categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada kategori pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
