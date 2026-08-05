@extends('layouts.admin')

@section('title', 'Laporan Tunggakan')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Filter Laporan Tunggakan Siswa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.arrear') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tahun Pelajaran</label>
                <select name="academic_year_id" class="form-select">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($academicYears as $y)
                        <option value="{{ $y->id }}" {{ $academicYearId == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" name="action" value="view" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                <button type="submit" name="action" value="print" class="btn btn-secondary" formtarget="_blank"><i class="bi bi-printer"></i> Cetak</button>
                <button type="submit" name="action" value="pdf" class="btn btn-danger"><i class="bi bi-file-pdf"></i> PDF</button>
                <button type="submit" name="action" value="excel" class="btn btn-success"><i class="bi bi-file-excel"></i> Excel</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Daftar Tagihan Belum Lunas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis Tagihan</th>
                        <th>Tahun</th>
                        <th>Nominal Tagihan</th>
                        <th>Kurang Bayar</th>
                        <th>Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arrears as $index => $ar)
                        @php $sisa = $ar->total_amount - $ar->total_paid; @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ar->student->name ?? '-' }} <br><small class="text-muted">{{ $ar->student->nis ?? '-' }}</small></td>
                            <td>{{ $ar->student->schoolClass->name ?? '-' }}</td>
                            <td>{{ $ar->financePost->name ?? '-' }}</td>
                            <td>{{ $ar->academicYear->name ?? '-' }}</td>
                            <td>Rp {{ number_format($ar->total_amount, 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($ar->due_date)->translatedFormat('d F Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data tunggakan.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL TUNGGAKAN</td>
                        <td colspan="2" class="text-danger fs-5">Rp {{ number_format($totalArrears, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
