@extends('layouts.admin')

@section('title', 'Pos Keuangan')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Pos Keuangan</h5>
        <a href="{{ route('finance-posts.create') }}" class="btn btn-primary btn-sm">Tambah Pos Keuangan</a>
    </div>
    <div class="card-body">
        <form action="{{ route('finance-posts.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama / Jenis" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <a href="{{ route('finance-posts.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Pos</th>
                        <th>Jenis</th>
                        <th>Nominal Default</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $index => $post)
                        <tr>
                            <td>{{ $posts->firstItem() + $index }}</td>
                            <td>{{ $post->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $post->type }}</span>
                            </td>
                            <td>Rp {{ number_format($post->default_amount, 0, ',', '.') }}</td>
                            <td>{{ Str::limit($post->description, 50) }}</td>
                            <td>
                                @if($post->status == 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('finance-posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('finance-posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pos keuangan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data pos keuangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
