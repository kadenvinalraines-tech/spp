@extends('layouts.admin')

@section('title', 'Edit Pos Keuangan')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Edit Pos Keuangan: {{ $finance_post->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('finance-posts.update', $finance_post->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Pos Keuangan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $finance_post->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="type_select" class="form-label">Jenis <span class="text-danger">*</span></label>
                    <select class="form-select mb-2" id="type_select" onchange="checkCustomType()">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="SPP">SPP</option>
                        <option value="Gedung">Gedung</option>
                        <option value="Seragam">Seragam</option>
                        <option value="Prakerin">Prakerin</option>
                        <option value="Ujian">Ujian</option>
                        <option value="Praktikum">Praktikum</option>
                        <option value="Lainnya">Lainnya (Ketik Manual)</option>
                    </select>
                    
                    <input type="text" class="form-control d-none @error('type') is-invalid @enderror" id="type_manual" placeholder="Ketik jenis keuangan baru...">
                    <input type="hidden" name="type" id="real_type" value="{{ old('type', $finance_post->type) }}">
                    @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="default_amount" class="form-label">Nominal Default (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('default_amount') is-invalid @enderror" id="default_amount" name="default_amount" value="{{ old('default_amount', intval($finance_post->default_amount)) }}" min="0" required>
                    @error('default_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $finance_post->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $finance_post->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Keterangan</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $finance_post->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('finance-posts.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Update Pos Keuangan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function checkCustomType() {
        const select = document.getElementById('type_select');
        const manualInput = document.getElementById('type_manual');
        const realInput = document.getElementById('real_type');

        if (select.value === 'Lainnya') {
            manualInput.classList.remove('d-none');
            manualInput.required = true;
            realInput.value = manualInput.value;
        } else {
            manualInput.classList.add('d-none');
            manualInput.required = false;
            realInput.value = select.value;
        }
    }

    document.getElementById('type_manual').addEventListener('input', function() {
        document.getElementById('real_type').value = this.value;
    });

    // Jalankan sekali saat load (jika old value ada)
    document.addEventListener("DOMContentLoaded", function() {
        const select = document.getElementById('type_select');
        const oldType = "{{ old('type', $finance_post->type) }}";
        
        if (oldType) {
            let found = false;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === oldType) {
                    found = true;
                    select.value = oldType;
                    break;
                }
            }
            if (!found && oldType !== '') {
                select.value = 'Lainnya';
                document.getElementById('type_manual').value = oldType;
            }
        }
        checkCustomType();
    });
</script>
@endsection
