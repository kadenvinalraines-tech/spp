@extends('layouts.admin')

@section('title', 'Mutasi & Kenaikan Kelas')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Kelas Asal</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('promotions.index') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="class_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle me-2"></i> Pilih kelas asal untuk memunculkan daftar siswa. Anda bisa memilih sebagian atau seluruh siswa untuk dipindahkan.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Daftar Siswa Aktif</h5>
            </div>
            <div class="card-body">
                @if(!$class_id)
                    <div class="text-center text-muted my-5">
                        <i class="bi bi-arrow-left-circle" style="font-size: 3rem;"></i>
                        <p class="mt-3">Pilih kelas di samping kiri terlebih dahulu.</p>
                    </div>
                @elseif($students->isEmpty())
                    <div class="text-center text-muted my-5">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <p class="mt-3">Tidak ada siswa aktif di kelas ini.</p>
                    </div>
                @else
                    <form action="{{ route('promotions.process') }}" method="POST" id="formPromotion">
                        @csrf
                        
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="5%" class="text-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>L/P</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $s)
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $s->id }}">
                                        </td>
                                        <td>{{ $s->nis }}</td>
                                        <td class="fw-bold">{{ $s->name }}</td>
                                        <td>{{ $s->gender }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 p-4 rounded-3 border-0 shadow-sm" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                            <h6 class="mb-3 fw-bold text-primary"><i class="bi bi-gear-fill me-2"></i>Aksi Mutasi / Kenaikan</h6>
                            <div class="row align-items-end g-3">
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-semibold">Tentukan Aksi</label>
                                    <select name="action" id="actionSelect" class="form-select" required>
                                        <option value="promote">Naik / Pindah Kelas</option>
                                        <option value="graduate">Luluskan Siswa</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="targetYearWrapper">
                                    <label class="form-label text-muted small fw-semibold">Tahun Ajaran Baru</label>
                                    <select name="target_academic_year_id" id="targetYearSelect" class="form-select">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @if(isset($academicYears))
                                            @foreach($academicYears as $y)
                                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3" id="targetClassWrapper">
                                    <label class="form-label text-muted small fw-semibold">Pilih Kelas Tujuan</label>
                                    <select name="target_class_id" id="targetClassSelect" class="form-select">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100 fw-bold" id="btnProcess">Eksekusi <i class="bi bi-chevron-double-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Check All Checkboxes
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }

    // Toggle Target Class Select based on Action
    const actionSelect = document.getElementById('actionSelect');
    const targetClassWrapper = document.getElementById('targetClassWrapper');
    const targetClassSelect = document.getElementById('targetClassSelect');
    const targetYearWrapper = document.getElementById('targetYearWrapper');
    const targetYearSelect = document.getElementById('targetYearSelect');

    if (actionSelect) {
        actionSelect.addEventListener('change', function() {
            if (this.value === 'graduate') {
                targetClassSelect.removeAttribute('required');
                targetClassWrapper.style.display = 'none';
                targetYearSelect.removeAttribute('required');
                targetYearWrapper.style.display = 'none';
            } else {
                targetClassSelect.setAttribute('required', 'required');
                targetClassWrapper.style.display = 'block';
                targetYearSelect.setAttribute('required', 'required');
                targetYearWrapper.style.display = 'block';
            }
        });
        // trigger on load
        actionSelect.dispatchEvent(new Event('change'));
    }

    // Process Confirmation
    const btnProcess = document.getElementById('btnProcess');
    if (btnProcess) {
        btnProcess.addEventListener('click', function() {
            const checked = document.querySelectorAll('.student-checkbox:checked');
            if (checked.length === 0) {
                alert('Pilih minimal satu siswa untuk dieksekusi.');
                return;
            }

            const action = actionSelect.value === 'graduate' ? 'meluluskan' : 'memindahkan';
            if (confirm(`Anda yakin ingin ${action} ${checked.length} siswa terpilih?`)) {
                document.getElementById('formPromotion').submit();
            }
        });
    }
});
</script>
@endsection
