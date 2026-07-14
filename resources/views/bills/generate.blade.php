@extends('layouts.admin')

@section('title', 'Generate Tagihan Baru')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Generate Tagihan Kelas Secara Massal</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Info:</strong> Fitur ini akan otomatis membuatkan tagihan untuk seluruh siswa aktif di kelas yang dipilih. Tagihan tidak akan digenerate ganda jika kombinasi Tahun Pelajaran dan Pos Keuangannya sudah ada pada siswa tersebut.
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
                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menggenerate tagihan ke seluruh siswa di kelas tersebut?');">
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
    });
</script>
@endsection
