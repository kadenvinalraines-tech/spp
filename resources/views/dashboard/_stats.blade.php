@if(Auth::user()->hasPermission('view_dashboard_stats'))
<!-- Dropdown Filter Range -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h5 class="mb-1 fw-bold text-dark">Ringkasan Statistik</h5>
        <p class="text-muted mb-0" style="font-size: .85rem;">Pantau keuangan sekolah secara real-time</p>
    </div>
    
    <!-- Widget Jam & Tanggal -->
    <div class="px-3 py-2 bg-light rounded-pill border d-flex align-items-center">
        <i class="bi bi-clock-history text-primary me-2 fs-5"></i>
        <div>
            <div id="dashboard-date" class="fw-semibold text-dark" style="font-size: 0.85rem;"></div>
            <div id="dashboard-clock" class="text-muted fw-bold" style="font-size: 1.1rem; line-height: 1;"></div>
        </div>
    </div>
    
    <script>
        function updateDashboardClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('dashboard-date').textContent = `${dayName}, ${date} ${monthName} ${year}`;
            document.getElementById('dashboard-clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        setInterval(updateDashboardClock, 1000);
        updateDashboardClock(); // Panggil sekali di awal
    </script>
    <form action="{{ url()->current() }}" method="GET" class="d-flex" id="filterForm">
        <select name="filter_range" class="form-select" onchange="document.getElementById('filterForm').submit();" style="min-width: 260px;">
            <optgroup label="Filter Cepat">
                <option value="up_to_active" {{ request('filter_range', 'up_to_active') == 'up_to_active' ? 'selected' : '' }}>Histori s/d Semester Aktif (Default)</option>
                <option value="active_semester" {{ request('filter_range') == 'active_semester' ? 'selected' : '' }}>Hanya Semester Aktif Ini</option>
                <option value="active_year" {{ request('filter_range') == 'active_year' ? 'selected' : '' }}>Hanya Tahun Ajaran Aktif (1 Tahun)</option>
                <option value="all_time" {{ request('filter_range') == 'all_time' ? 'selected' : '' }}>Seluruh Waktu (Termasuk Semua Alumni)</option>
            </optgroup>
            @if(isset($allAcademicYears))
            <optgroup label="Pilih Manual (Hanya Semester Tersebut)">
                @foreach($allAcademicYears as $ay)
                    <option value="manual_{{ $ay->id }}" {{ request('filter_range') == 'manual_'.$ay->id ? 'selected' : '' }}>
                        {{ $ay->name }} - {{ $ay->semester }}
                    </option>
                @endforeach
            </optgroup>
            @endif
        </select>
    </form>
</div>

<!-- Tahun Ajaran Aktif Banner -->
<div class="alert alert-info d-flex align-items-center mb-4" style="border-left: 4px solid var(--primary-600) !important;">
    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: var(--primary-100);">
        <i class="bi bi-calendar-check-fill text-primary fs-5"></i>
    </div>
    <div>
        <p class="mb-0 text-muted" style="font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; font-weight: 600;">Tahun Ajaran Aktif</p>
        <h5 class="mb-0 fw-bold" style="color: var(--primary-800);">{{ $stats['tahun_ajaran'] }}</h5>
    </div>
</div>

<!-- Stat Cards Row 1 -->
<div class="row g-3 mb-3">
    <!-- Total Siswa Aktif -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <p class="stat-label">Total Siswa Aktif</p>
                    <h4 class="stat-value">{{ number_format($stats['total_siswa'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Kas -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon" style="background: rgba(16,185,129,.1); color: #059669;">
                    <i class="bi bi-bank2"></i>
                </div>
                <div>
                    <p class="stat-label">Saldo Kas</p>
                    <h4 class="stat-value">Rp{{ number_format($stats['saldo_kas'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Tagihan -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon" style="background: rgba(139,92,246,.1); color: #7c3aed;">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <p class="stat-label">Total Tagihan</p>
                    <h4 class="stat-value">Rp{{ number_format($stats['total_tagihan'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pembayaran -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon" style="background: rgba(6,182,212,.1); color: #0891b2;">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
                <div>
                    <p class="stat-label">Total Pembayaran</p>
                    <h4 class="stat-value">Rp{{ number_format($stats['total_pembayaran'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards Row 2 -->
<div class="row g-3 mb-4">
    <!-- Total Pengeluaran -->
    <div class="col-xl-4 col-sm-6">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon" style="background: rgba(245,158,11,.1); color: #d97706;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <p class="stat-label">Total Pengeluaran</p>
                    <h4 class="stat-value">Rp{{ number_format($stats['total_pengeluaran'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Tunggakan -->
    <div class="col-xl-4 col-sm-6">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon" style="background: rgba(239,68,68,.1); color: #dc2626;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <p class="stat-label">Total Tunggakan</p>
                    <h4 class="stat-value text-danger">Rp{{ number_format($stats['total_tunggakan'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Pembayaran -->
    <div class="col-xl-4 col-sm-12">
        <div class="card border-0 h-100 stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon" style="background: rgba(236,72,153,.1); color: #db2777;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="w-100">
                    <p class="stat-label">Persentase Lunas</p>
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <h4 class="stat-value mb-0">{{ $stats['persentase_pembayaran'] }}%</h4>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $stats['persentase_pembayaran'] }}%; background: linear-gradient(90deg, #3b82f6, #8b5cf6);" aria-valuenow="{{ $stats['persentase_pembayaran'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row for Chart and Arrears Breakdown -->
<div class="row g-3 mb-4">
    <!-- Payment Trend Chart -->
    <div class="col-lg-12">
        <div class="card border-0 h-100">
            <div class="card-header border-0 bg-white d-flex align-items-center gap-2 pt-3 pb-0">
                <i class="bi bi-graph-up-arrow text-primary"></i>
                <h6 class="mb-0 fw-bold">Tren Pembayaran 12 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="paymentChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Tunggakan Per Pos -->
    <div class="col-lg-6">
        <div class="card border-0 h-100">
            <div class="card-header border-0 bg-white d-flex align-items-center gap-2 pt-3 pb-0">
                <i class="bi bi-exclamation-octagon text-danger"></i>
                <h6 class="mb-0 fw-bold text-danger">Rincian Tunggakan Berdasarkan Jenis</h6>
            </div>
            <div class="card-body">
                @if($tunggakanPerPos->count() > 0)
                    <div class="list-group list-group-flush bg-transparent" style="max-height: 300px; overflow-y: auto;">
                        @foreach($tunggakanPerPos as $pos)
                        <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-3" style="border-bottom: 1px dashed var(--border-color);">
                            <div>
                                <p class="mb-0 fw-semibold text-dark" style="font-size: .875rem;">{{ $pos['name'] }}</p>
                            </div>
                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                Rp{{ number_format($pos['tunggakan'], 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle-fill text-success mb-3" style="font-size: 3rem;"></i>
                        <h6 class="fw-bold text-muted">Luar Biasa!</h6>
                        <p class="text-muted small">Tidak ada tunggakan pada jenis tagihan manapun.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tunggakan Per Kelas -->
    <div class="col-lg-6">
        <div class="card border-0 h-100">
            <div class="card-header border-0 bg-white d-flex align-items-center gap-2 pt-3 pb-0">
                <i class="bi bi-people-fill text-warning"></i>
                <h6 class="mb-0 fw-bold text-warning">Rincian Tunggakan Berdasarkan Kelas</h6>
            </div>
            <div class="card-body">
                @if($tunggakanPerKelas->count() > 0)
                    <div class="list-group list-group-flush bg-transparent" style="max-height: 300px; overflow-y: auto;">
                        @foreach($tunggakanPerKelas as $kelas)
                        <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-3" style="border-bottom: 1px dashed var(--border-color);">
                            <div>
                                <p class="mb-0 fw-semibold text-dark" style="font-size: .875rem;">{{ $kelas['name'] }}</p>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                Rp{{ number_format($kelas['tunggakan'], 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle-fill text-success mb-3" style="font-size: 3rem;"></i>
                        <h6 class="fw-bold text-muted">Hebat!</h6>
                        <p class="text-muted small">Seluruh kelas sudah melunasi tagihannya.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-left: 4px solid transparent !important;
        transition: all 0.25s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,.08) !important;
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0;
        letter-spacing: -0.02em;
    }
</style>

<!-- Tambahkan CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('paymentChart').getContext('2d');
    
    // Gradient untuk area bawah grafik
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Total Pembayaran (Rp)',
                data: {!! json_encode($chartData['data']) !!},
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13, family: 'Inter' },
                    bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false,
                    },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp' + (value / 1000000) + ' Jt';
                            }
                            return 'Rp' + (value / 1000) + ' Rb';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        font: { family: 'Inter', size: 11 }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>
@else
<div class="row justify-content-center mt-5">
    <div class="col-md-8 text-center">
        <div class="card border-0">
            <div class="card-body py-5">
                <i class="bi bi-shield-lock-fill text-primary mb-3" style="font-size: 4rem;"></i>
                <h3 class="fw-bold text-dark">Selamat Datang, {{ Auth::user()->name }}!</h3>
                <p class="text-muted fs-5">Anda login sebagai <strong>{{ Auth::user()->roles->first()->name ?? 'Petugas' }}</strong></p>
                <hr class="my-4 mx-auto w-50">
                <p class="text-muted">Gunakan menu di samping kiri untuk mengelola sistem.<br>Untuk melihat ringkasan statistik keuangan sekolah, Anda memerlukan akses khusus (Dashboard Stats).</p>
            </div>
        </div>
    </div>
</div>
@endif
