@extends('layouts.admin')

@section('title', 'Generate Tagihan Baru')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Generate Tagihan Kelas Secara Massal</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Info:</strong> Fitur ini akan otomatis membuatkan tagihan untuk seluruh siswa aktif di kelas yang dipilih. Tagihan tidak akan digenerate ganda jika kombinasi Tahun Pelajaran dan Pos Keuangannya sudah ada pada siswa tersebut. Siswa yang memiliki <strong>pengecualian (pembebasan)</strong> untuk Pos Keuangan ini juga tidak akan dibuatkan tagihan.
        </div>

        <form action="{{ route('bills.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="academic_year_id" class="form-label">Tahun Pelajaran <span class="text-danger">*</span></label>
                    <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                        <option value="">-- Pilih Tahun Pelajaran --</option>
                        @foreach($academic_years as $year)
                            <option value="{{ $year->id }}" {{ (old('academic_year_id') == $year->id) || $year->is_active ? 'selected' : '' }}>
                                {{ $year->name }} (Semester {{ $year->semester }}) {{ $year->is_active ? '- Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="class_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required onchange="fetchStudents(this.value)">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="all" {{ old('class_id') == 'all' ? 'selected' : '' }}>-- Semua Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card bg-light mb-4 border-0 d-none" id="student_selection_card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title fw-bold mb-0">Pilih Siswa</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check_all_students" checked onchange="toggleAllStudents(this)">
                            <label class="form-check-label small" for="check_all_students">Pilih Semua</label>
                        </div>
                    </div>
                    <div class="row" id="student_checkboxes_container">
                        <!-- Checkboxes will be populated here via AJAX -->
                    </div>
                    @error('student_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="finance_post_id" class="form-label">Jenis Tagihan (Pos Keuangan) <span class="text-danger">*</span></label>
                    <select class="form-select @error('finance_post_id') is-invalid @enderror" id="finance_post_id" name="finance_post_id" required onchange="fetchDefaultAmount(this.value)">
                        <option value="">-- Pilih Pos Keuangan --</option>
                        @foreach($finance_posts as $post)
                            <option value="{{ $post->id }}" {{ old('finance_post_id') == $post->id ? 'selected' : '' }}>
                                {{ $post->name }} ({{ $post->type }})
                            </option>
                        @endforeach
                    </select>
                    @error('finance_post_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="amount" class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 0) }}" min="0" required>
                    <div class="form-text">Nominal otomatis terisi sesuai default Pos, namun bisa diubah.</div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d')) }}" required>
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('bills.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menggenerate tagihan untuk siswa-siswa yang dipilih?');">
                    <i class="bi bi-lightning-charge"></i> Generate Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function fetchDefaultAmount(postId) {
        if (!postId) {
            document.getElementById('amount').value = 0;
            return;
        }
        
        fetch(`/api/finance-posts/${postId}/amount`)
            .then(response => response.json())
            .then(data => {
                if(data && data.default_amount !== undefined) {
                    document.getElementById('amount').value = data.default_amount;
                }
            })
            .catch(error => console.error('Error fetching amount:', error));
    }

    // Jika terjadi validasi error tapi pos keuangan sudah terpilih, coba set nilainya
    document.addEventListener("DOMContentLoaded", function() {
        const postId = document.getElementById('finance_post_id').value;
        const amountInput = document.getElementById('amount').value;
        // Jika amount masih 0 atau kosong, coba tarik data lagi
        if (postId && (!amountInput || amountInput == 0)) {
            fetchDefaultAmount(postId);
        }

        // Fetch students if class is already selected (e.g. old input)
        const classId = document.getElementById('class_id').value;
        if (classId) {
            fetchStudents(classId);
        }
    });

    function fetchStudents(classId) {
        const container = document.getElementById('student_checkboxes_container');
        const card = document.getElementById('student_selection_card');
        
        if (!classId) {
            card.classList.add('d-none');
            container.innerHTML = '';
            return;
        }

        container.innerHTML = '<div class="col-12 text-center small text-muted">Memuat data siswa...</div>';
        card.classList.remove('d-none');

        fetch(`/api/classes/${classId}/students`)
            .then(response => response.json())
            .then(students => {
                container.innerHTML = '';
                if (students.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center small text-danger">Tidak ada siswa aktif di kelas ini.</div>';
                    return;
                }

                students.forEach(student => {
                    const div = document.createElement('div');
                    div.className = 'col-md-4 mb-2';
                    div.innerHTML = `
                        <div class="form-check">
                            <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="${student.id}" id="std_${student.id}" checked>
                            <label class="form-check-label" for="std_${student.id}">
                                ${student.name} <span class="text-muted small">(${student.nis})</span>
                            </label>
                        </div>
                    `;
                    container.appendChild(div);
                });
                document.getElementById('check_all_students').checked = true;
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                container.innerHTML = '<div class="col-12 text-center small text-danger">Gagal memuat data siswa.</div>';
            });
    }

    function toggleAllStudents(source) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }
</script>
@endsection
