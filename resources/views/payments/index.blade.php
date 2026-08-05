@extends('layouts.admin')

@section('title', 'Transaksi Pembayaran')

@section('content')
<div class="row">
    <!-- Kolom Pencarian Siswa -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-search"></i> Cari Siswa</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.index') }}" method="GET">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label fw-bold">Tahun Ajaran</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select">
                            <option value="all" {{ $selectedAcademicYearId === 'all' ? 'selected' : '' }}>-- Semua Tahun Ajaran --</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $selectedAcademicYearId == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} ({{ $ay->semester }}) {{ $ay->is_active ? '- Aktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="student_id" class="form-label fw-bold">Ketik Nama atau NIS</label>
                        <input class="form-control" list="studentOptions" id="student_id_search" placeholder="Cari..." autocomplete="off">
                        <datalist id="studentOptions">
                            @foreach($students as $s)
                                @php
                                    $info = $s->status == 'graduated' ? 'Alumni' : ($s->schoolClass->name ?? '-');
                                @endphp
                                <option data-id="{{ $s->id }}" value="{{ $s->nis }} - {{ $s->name }} ({{ $info }})"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="student_id" id="real_student_id" value="{{ request('student_id') }}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnSearch" {{ request('student_id') ? '' : 'disabled' }}>Lihat Tagihan</button>
                </form>

                @if($student)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-muted mb-2">Profil Siswa:</h6>
                    <h5>{{ $student->name }}</h5>
                    <p class="mb-1"><strong>NIS:</strong> {{ $student->nis }}</p>
                    <p class="mb-1"><strong>Kelas:</strong> {{ $student->schoolClass->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Status:</strong> 
                        @php $yearLabel = $student->latest_academic_year_name ? ' (' . $student->latest_academic_year_name . ')' : ''; @endphp
                        @if($student->status == 'active')
                            <span class="badge bg-success">Aktif{{ $yearLabel }}</span>
                        @elseif($student->status == 'graduated')
                            <span class="badge bg-primary">Alumni / Lulus{{ $yearLabel }}</span>
                        @else
                            <span class="badge bg-danger">Tidak Aktif{{ $yearLabel }}</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Tagihan & Riwayat -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            @if(!$student)
                <div class="card-body d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="bi bi-wallet2" style="font-size: 3rem;"></i>
                        <p class="mt-3">Pilih siswa di menu samping untuk melihat tagihan dan memproses pembayaran.</p>
                    </div>
                </div>
            @else
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs mb-0" id="paymentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tagihan-tab" data-bs-toggle="tab" data-bs-target="#tagihan" type="button" role="tab">Tagihan Aktif</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">Riwayat Pembayaran</button>
                        </li>
                    </ul>
                    @if($student->phone)
                    <form action="{{ route('wa-gateway.send-bulk') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                        <input type="hidden" name="type" value="personal">
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Kirim rincian seluruh tagihan via WhatsApp ke siswa ini?');">
                            <i class="bi bi-whatsapp"></i> Tagih via WA
                        </button>
                    </form>
                    @endif
                </div>
                <div class="card-body">
                    <div class="tab-content" id="paymentTabsContent">
                        <!-- TAB TAGIHAN -->
                        <div class="tab-pane fade show active" id="tagihan" role="tabpanel">
                            @if($unpaidBills->isEmpty())
                                <div class="alert alert-success">Semua tagihan siswa ini sudah Lunas.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                                                <th>Deskripsi</th>
                                                <th>Total Tagihan</th>
                                                <th>Sisa Tagihan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $grandTotalSisa = 0; @endphp
                                            @foreach($unpaidBills as $bill)
                                                @foreach($bill->details as $detail)
                                                    @php $sisa = $detail->amount - $detail->paid_amount; @endphp
                                                    @if($sisa > 0)
                                                    @php $grandTotalSisa += $sisa; @endphp
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" class="form-check-input bill-check" 
                                                                data-id="{{ $detail->id }}" 
                                                                data-sisa="{{ $sisa }}" 
                                                                data-desc="{{ $bill->financePost->name }} {{ $detail->month ? '('.$detail->month.')' : '' }}">
                                                        </td>
                                                        <td>
                                                            <strong>{{ $bill->financePost->name }}</strong><br>
                                                            <small class="text-muted">{{ $bill->academicYear->name }} (Semester {{ $bill->academicYear->semester }}) | Inv: {{ $bill->invoice_number }}</small>
                                                        </td>
                                                        <td>Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                                                        <td class="text-danger fw-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="4">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <button class="btn btn-primary" id="btnPaySelected" disabled data-bs-toggle="modal" data-bs-target="#payModal">
                                                            Bayar Terpilih (0 item)
                                                        </button>
                                                        <div class="text-end">
                                                            <span class="text-muted">Total Seluruh Tunggakan:</span>
                                                            <strong class="text-danger fs-6 ms-2">Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- TAB RIWAYAT -->
                        <div class="tab-pane fade" id="riwayat" role="tabpanel">
                            @if($paymentHistory->isEmpty())
                                <p class="text-muted">Belum ada riwayat transaksi.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>No. Transaksi</th>
                                                <th>Nominal</th>
                                                <th>Metode</th>
                                                <th>Petugas</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paymentHistory as $pay)
                                            <tr>
                                                <td>{{ $pay->date->translatedFormat('d F Y') }}</td>
                                                <td>{{ $pay->transaction_number }}</td>
                                                <td>Rp {{ number_format($pay->total_amount, 0, ',', '.') }}</td>
                                                <td>{{ $pay->payment_method }}</td>
                                                <td>{{ $pay->user->name ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('payments.print', $pay->id) }}" target="_blank" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-file-pdf"></i> Cetak
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Proses Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="hiddenInputsContainer"></div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rincian Tagihan yang Dipilih</label>
                        <ul class="list-group mb-2" id="modal_desc_list">
                            <!-- Diisi via JS -->
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Harus Dibayar</label>
                        <input type="text" class="form-control text-danger fw-bold fs-4" id="modal_sisa_text" readonly>
                        <input type="hidden" id="modal_sisa_val">
                    </div>
                    <div class="alert alert-info">
                        <strong>Perhatian:</strong> Pembayaran massal akan melunasi seluruh item tagihan yang dipilih secara penuh. Cicilan (pembayaran sebagian) tidak didukung pada fitur Bayar Sekaligus.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Proses pembayaran ini?');">Bayar Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('student_id_search');
    const hiddenId = document.getElementById('real_student_id');
    const btnSearch = document.getElementById('btnSearch');

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            let val = this.value;
            let options = document.getElementById('studentOptions').childNodes;
            let found = false;
            hiddenId.value = '';
            
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    hiddenId.value = options[i].getAttribute('data-id');
                    found = true;
                    break;
                }
            }
            btnSearch.disabled = !found;
            if (found) {
                btnSearch.closest('form').submit();
            }
        });
    }

    const checkAll = document.getElementById('checkAll');
    const billChecks = document.querySelectorAll('.bill-check');
    const btnPaySelected = document.getElementById('btnPaySelected');

    function updatePayButton() {
        const checkedCount = document.querySelectorAll('.bill-check:checked').length;
        if (btnPaySelected) {
            btnPaySelected.disabled = checkedCount === 0;
            btnPaySelected.textContent = `Bayar Terpilih (${checkedCount} item)`;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            billChecks.forEach(cb => {
                cb.checked = this.checked;
            });
            updatePayButton();
        });
    }

    billChecks.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked && checkAll) {
                checkAll.checked = false;
            }
            updatePayButton();
        });
    });

    const payModal = document.getElementById('payModal');
    if (payModal) {
        payModal.addEventListener('show.bs.modal', function () {
            const selectedChecks = document.querySelectorAll('.bill-check:checked');
            const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
            const listContainer = document.getElementById('modal_desc_list');
            const sisaText = document.getElementById('modal_sisa_text');
            const sisaVal = document.getElementById('modal_sisa_val');

            hiddenInputsContainer.innerHTML = '';
            listContainer.innerHTML = '';
            let total = 0;
            let index = 0;

            selectedChecks.forEach(cb => {
                const id = cb.getAttribute('data-id');
                const sisa = parseInt(cb.getAttribute('data-sisa'));
                const desc = cb.getAttribute('data-desc');

                total += sisa;

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = `payments[${index}][bill_detail_id]`;
                inputId.value = id;
                
                const inputAmount = document.createElement('input');
                inputAmount.type = 'hidden';
                inputAmount.name = `payments[${index}][amount]`;
                inputAmount.value = sisa;

                hiddenInputsContainer.appendChild(inputId);
                hiddenInputsContainer.appendChild(inputAmount);

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `${desc} <span>Rp ${sisa.toLocaleString('id-ID')}</span>`;
                listContainer.appendChild(li);

                index++;
            });

            sisaVal.value = total;
            sisaText.value = 'Rp ' + total.toLocaleString('id-ID');
        });
    }
});
</script>
@endsection
