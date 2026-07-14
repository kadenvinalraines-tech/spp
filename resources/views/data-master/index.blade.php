@extends('layouts.admin')

@section('title', 'Export / Import Data Master')

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Data Siswa</span>
                <a href="{{ route('students.export') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-download"></i> Download Format / Export
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" name="file" required accept=".xlsx,.xls,.csv">
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Import Data Siswa
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Data Kelas</span>
                <a href="{{ route('classes.export') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-download"></i> Download Format / Export
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('classes.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" name="file" required accept=".xlsx,.xls,.csv">
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Import Data Kelas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
