@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Siswa</h5>
        <div>
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">Tambah Siswa</a>
            <a href="{{ route('students.export', request()->all()) }}" class="btn btn-success btn-sm">Export Excel</a>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">Import Excel</button>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('students.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIS / Nama" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="mb-2">
            <button type="button" class="btn btn-danger btn-sm" id="btn-bulk-delete" style="display: none;" onclick="confirmBulkDelete()">Hapus Terpilih</button>
        </div>

        <form action="{{ route('students.bulk-destroy') }}" method="POST" id="bulk-delete-form" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="check-all" class="form-check-input"></th>
                        <th>No</th>
                        <th>Foto</th>
                        <th>NIS/NISN</th>
                        <th>Nama</th>
                        <th>L/P</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr>
                            <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox"></td>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                @if($student->photo_url)
                                    <img src="{{ $student->photo_url }}" alt="Foto {{ $student->name }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">NA</div>
                                @endif
                            </td>
                            <td>
                                {{ $student->nis }}<br>
                                <small class="text-muted">{{ $student->nisn ?? '-' }}</small>
                            </td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->status === 'graduated' ? 'Alumni (' . ($student->latest_academic_year_name ?? '-') . ')' : ($student->schoolClass->name ?? '-') }}</td>
                            <td>
                                <span class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'graduated' ? 'primary' : 'danger') }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-info btn-sm">Detail</a>
                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data siswa.</td>
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

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">Import Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="file" class="form-label">Upload File Excel (.xlsx, .xls, .csv)</label>
            <input type="file" class="form-control" name="file" id="file" required accept=".xlsx,.xls,.csv">
          </div>
          <p class="text-muted small">Pastikan file memiliki header: <strong>nis, nisn, nama_siswa, jenis_kelamin, kelas, alamat, no_telp, status</strong>. <br> Kolom <strong>kelas</strong> harus persis dengan nama kelas di database.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const btnBulkDelete = document.getElementById('btn-bulk-delete');

        function toggleBulkDeleteButton() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            btnBulkDelete.style.display = anyChecked ? 'inline-block' : 'none';
        }

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                toggleBulkDeleteButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                checkAll.checked = allChecked;
                toggleBulkDeleteButton();
            });
        });
    });

    function confirmBulkDelete() {
        if(confirm('Yakin ingin menghapus siswa yang terpilih?')) {
            const form = document.getElementById('bulk-delete-form');
            // Bersihkan input sebelumnya jika ada
            form.querySelectorAll('input[name="student_ids[]"]').forEach(el => el.remove());
            
            const checkboxes = document.querySelectorAll('.student-checkbox:checked');
            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            form.submit();
        }
    }
</script>
@endpush
