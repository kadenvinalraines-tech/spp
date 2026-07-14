@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Edit Hak Akses: {{ $role->name }}</h5>
    </div>
    <div class="card-body">
        @if($role->name === 'Super Admin')
            <div class="alert alert-warning">
                <strong>Perhatian!</strong> Anda sedang mengedit role <code>Super Admin</code>. Secara sistem, role ini sudah memiliki *Bypass* ke semua fitur. Perubahan permission di bawah ini tidak akan banyak berpengaruh.
            </div>
        @endif

        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="form-label fw-bold">Nama Role <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $role->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Hak Akses (Permissions)</label>
                <div class="row">
                    @foreach($permissions as $perm)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" 
                                {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $perm->id }}">
                                {{ $perm->name }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Update Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
