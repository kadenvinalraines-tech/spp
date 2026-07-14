@extends('layouts.admin')

@section('title', 'Daftar Tagihan')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Tagihan</h5>
        <a href="{{ route('bills.generate') }}" class="btn btn-primary btn-sm">Generate Tagihan Kelas</a>
    </div>
    <div class="card-body">
        <form action="{{ route('bills.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Siswa atau No. Invoice..." value="{{ request('search') }}">
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
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Cari</button>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
            </div>
        </form>

        <form action="{{ route('wa-gateway.send-bulk') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="bulk">
            <div class="d-flex justify-content-between mb-3">
                <button type="submit" class="btn btn-success" onclick="return confirm('Kirim pesan tagihan otomatis ke siswa yang dicentang?');">
                    <i class="bi bi-whatsapp"></i> Kirim Tagihan WA (Massal)
                </button>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%"><input type="checkbox" id="checkAll"></th>
                        <th>No</th>
                        <th>No. Invoice</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>T.A. / Semester</th>
                        <th>Jenis Tagihan</th>
                        <th>Total</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $index => $bill)
                        <tr>
                            <td>
                                @if($bill->status !== 'paid' && $bill->student->phone)
                                    <input type="checkbox" name="student_ids[]" value="{{ $bill->student_id }}" class="checkItem">
                                @endif
                            </td>
                            <td>{{ $bills->firstItem() + $index }}</td>
                            <td>{{ $bill->invoice_number }}</td>
                            <td>
                                {{ $bill->student->name ?? '-' }}<br>
                                <small class="text-muted">NIS: {{ $bill->student->nis ?? '-' }} | WA: {{ $bill->student->phone ?? 'Tidak Ada' }}</small>
                            </td>
                            <td>
                                @php
                                    // Tampilkan kelas pada saat tagihan dibuat berdasarkan tahun ajarannya
                                    $studentClass = $bill->student->studentClasses->where('academic_year_id', $bill->academic_year_id)->first();
                                    $className = $studentClass ? $studentClass->schoolClass->name : ($bill->student->schoolClass->name ?? '-');
                                @endphp
                                <span class="badge bg-secondary">{{ $className }}</span>
                            </td>
                            <td>
                                {{ $bill->academicYear->name ?? '-' }}<br>
                                <small class="text-muted">{{ $bill->academicYear->semester ?? '-' }}</small>
                            </td>
                            <td>{{ $bill->financePost->name ?? '-' }}</td>
                            <td>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</td>
                            <td>
                                @if($bill->status == 'paid')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($bill->status == 'partial')
                                    <span class="badge bg-warning">Sebagian</span>
                                @else
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $bills->links() }}
        </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.checkItem');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
@endsection
