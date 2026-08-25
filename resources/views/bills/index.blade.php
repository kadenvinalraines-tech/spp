@extends('layouts.admin')

@section('title', 'Daftar Tagihan')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Tagihan</h5>
        <div>
            <a href="{{ route('bills.print-report', request()->all()) }}" target="_blank" class="btn btn-warning btn-sm text-dark me-2">
                <i class="bi bi-printer"></i> Cetak Laporan (PDF)
            </a>
            <a href="{{ route('bills.generate') }}" class="btn btn-primary btn-sm">Generate Tagihan Kelas</a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('bills.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari Siswa/Invoice..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="finance_post_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pos Tagihan --</option>
                    @foreach($financePosts as $post)
                        <option value="{{ $post->id }}" {{ request('finance_post_id') == $post->id ? 'selected' : '' }}>
                            {{ $post->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 Baris</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Baris</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua Data</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Cari</button>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
            </div>
        </form>

        <form action="{{ route('wa-gateway.send-bulk') }}" method="POST">
            @csrf
            <div class="mb-3 d-flex gap-2 align-items-center">
                <button type="submit" name="type" value="bulk" class="btn btn-success" onclick="return confirm('Kirim pemberitahuan tagihan massal ke siswa yang dicentang?');">
                    <i class="bi bi-whatsapp"></i> Kirim Tagihan WA (Massal)
                </button>
                <button type="submit" name="type" value="due_reminder" class="btn btn-warning" onclick="return confirm('Kirim PENGINGAT JATUH TEMPO ke siswa yang dicentang?');">
                    <i class="bi bi-bell-fill"></i> Kirim Pengingat Jatuh Tempo (Terpilih)
                </button>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%"><input type="checkbox" id="checkAll"></th>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Rincian Tagihan Belum Lunas</th>
                        <th>Total Tunggakan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $dueUnpaidDetails = collect();
                            foreach($student->bills as $bill) {
                                if($bill->status !== 'paid') {
                                    foreach($bill->details as $detail) {
                                        if($detail->status !== 'paid' && $detail->isDue()) {
                                            $dueUnpaidDetails->push($detail);
                                        }
                                    }
                                }
                            }
                            $totalTunggakan = $dueUnpaidDetails->sum(function($detail) {
                                return $detail->amount - $detail->paid_amount;
                            });
                            
                            $activeYearId = \App\Models\AcademicYear::getActiveId();
                            $studentClass = $student->studentClasses->where('academic_year_id', $activeYearId)->first();
                            $className = $studentClass ? $studentClass->schoolClass->name : ($student->schoolClass->name ?? '-');
                        @endphp
                        <tr>
                            <td>
                                @if($dueUnpaidDetails->count() > 0 && $student->phone)
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="checkItem">
                                @endif
                            </td>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                <strong><a href="{{ route('bills.student.show', $student->id) }}" class="text-decoration-none">{{ $student->name }}</a></strong><br>
                                <small class="text-muted">NIS: {{ $student->nis ?? '-' }} | WA: {{ $student->phone ?? 'Tidak Ada' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $className }}</span>
                            </td>
                            <td>
                                @if($dueUnpaidDetails->isEmpty())
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Tidak ada tunggakan bulan ini</span>
                                @else
                                    <ul class="mb-0 ps-3 small text-danger">
                                        @foreach($dueUnpaidDetails as $detail)
                                            <li>
                                                {{ $detail->bill->financePost->name ?? 'Tagihan' }}
                                                @if($detail->month) (Bulan {{ $detail->month }}) @endif: 
                                                <strong>Rp {{ number_format($detail->amount - $detail->paid_amount, 0, ',', '.') }}</strong>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                <strong>Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($totalTunggakan == 0)
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger">Ada Tunggakan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('payments.index', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-cash"></i> Bayar
                                </a>
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
            {{ $students->links() }}
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
