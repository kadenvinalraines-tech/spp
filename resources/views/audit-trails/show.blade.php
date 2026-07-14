@extends('layouts.admin')

@section('title', 'Detail Audit Trail')

@section('content')
@php
    // Terjemahan nama model ke bahasa yang mudah dimengerti
    $modelNames = [
        'App\\Models\\Student' => 'Data Siswa',
        'App\\Models\\User' => 'Data Pengguna',
        'App\\Models\\Bill' => 'Tagihan',
        'App\\Models\\BillDetail' => 'Detail Tagihan',
        'App\\Models\\Payment' => 'Pembayaran',
        'App\\Models\\PaymentDetail' => 'Detail Pembayaran',
        'App\\Models\\FinancePost' => 'Pos Keuangan',
        'App\\Models\\Expense' => 'Pengeluaran',
        'App\\Models\\ExpenseCategory' => 'Kategori Pengeluaran',
        'App\\Models\\SchoolClass' => 'Kelas',
        'App\\Models\\AcademicYear' => 'Tahun Ajaran',
        'App\\Models\\Role' => 'Hak Akses',
        'App\\Models\\SchoolSetting' => 'Pengaturan Aplikasi',
        'App\\Models\\StudentClass' => 'Kelas Siswa',
    ];

    $modelLabel = $modelNames[$auditTrail->model_type] ?? class_basename($auditTrail->model_type);

    // Terjemahan aksi
    $actionLabels = [
        'login' => 'Masuk ke Sistem',
        'logout' => 'Keluar dari Sistem',
        'create' => 'Menambahkan Data Baru',
        'update' => 'Mengubah Data',
        'delete' => 'Menghapus Data',
    ];
    $actionLabel = $actionLabels[$auditTrail->action] ?? $auditTrail->action;

    // Terjemahan nama kolom ke bahasa Indonesia
    $fieldNames = [
        'name' => 'Nama',
        'email' => 'Email',
        'nis' => 'NIS',
        'phone' => 'No. Telepon',
        'address' => 'Alamat',
        'gender' => 'Jenis Kelamin',
        'birth_date' => 'Tanggal Lahir',
        'birth_place' => 'Tempat Lahir',
        'parent_name' => 'Nama Orang Tua',
        'parent_phone' => 'Telepon Orang Tua',
        'status' => 'Status',
        'class_id' => 'Kelas',
        'school_class_id' => 'Kelas',
        'student_id' => 'Siswa',
        'academic_year_id' => 'Tahun Ajaran',
        'finance_post_id' => 'Pos Keuangan',
        'total_amount' => 'Total Tagihan',
        'total_paid' => 'Total Dibayar',
        'amount' => 'Nominal',
        'paid_amount' => 'Nominal Dibayar',
        'due_date' => 'Tanggal Jatuh Tempo',
        'invoice_number' => 'Nomor Invoice',
        'transaction_number' => 'Nomor Transaksi',
        'payment_method' => 'Metode Pembayaran',
        'date' => 'Tanggal',
        'description' => 'Keterangan',
        'type' => 'Jenis',
        'default_amount' => 'Nominal Default',
        'semester' => 'Semester',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Selesai',
        'is_active' => 'Aktif',
        'month' => 'Bulan',
        'key' => 'Pengaturan',
        'value' => 'Nilai',
        'user_id' => 'ID Pengguna',
        'bill_id' => 'ID Tagihan',
        'bill_detail_id' => 'ID Detail Tagihan',
        'payment_id' => 'ID Pembayaran',
        'amount_paid' => 'Jumlah Dibayar',
        'password' => 'Password',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
        'deleted_at' => 'Dihapus Pada',
        'remember_token' => 'Token',
        'email_verified_at' => 'Email Terverifikasi',
        'role_id' => 'ID Hak Akses',
        'signature' => 'Tanda Tangan',
        'category_id' => 'ID Kategori',
    ];

    // Nilai-nilai yang perlu diterjemahkan
    $valueTranslations = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'paid' => 'Lunas',
        'unpaid' => 'Belum Bayar',
        'partial' => 'Sebagian',
        'success' => 'Berhasil',
        'pending' => 'Menunggu',
        'graduated' => 'Lulus / Alumni',
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
    ];
@endphp

<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill text-primary"></i>
        <h6 class="mb-0 fw-bold">Informasi Aktivitas</h6>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="35%" class="text-muted py-2"><i class="bi bi-person me-2"></i>Dilakukan oleh</td>
                        <td class="py-2"><strong>{{ $auditTrail->user->name ?? 'Sistem' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2"><i class="bi bi-lightning me-2"></i>Aksi yang dilakukan</td>
                        <td class="py-2">
                            @if($auditTrail->action == 'login')
                                <span class="badge bg-success"><i class="bi bi-box-arrow-in-right me-1"></i>{{ $actionLabel }}</span>
                            @elseif($auditTrail->action == 'logout')
                                <span class="badge bg-secondary"><i class="bi bi-box-arrow-right me-1"></i>{{ $actionLabel }}</span>
                            @elseif($auditTrail->action == 'create')
                                <span class="badge bg-primary"><i class="bi bi-plus-circle me-1"></i>{{ $actionLabel }}</span>
                            @elseif($auditTrail->action == 'update')
                                <span class="badge bg-warning text-dark"><i class="bi bi-pencil me-1"></i>{{ $actionLabel }}</span>
                            @elseif($auditTrail->action == 'delete')
                                <span class="badge bg-danger"><i class="bi bi-trash me-1"></i>{{ $actionLabel }}</span>
                            @else
                                <span class="badge bg-info">{{ $actionLabel }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2"><i class="bi bi-folder me-2"></i>Bagian / Menu</td>
                        <td class="py-2"><strong>{{ $modelLabel }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2"><i class="bi bi-hash me-2"></i>Nomor Data</td>
                        <td class="py-2">{{ $auditTrail->model_id ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="35%" class="text-muted py-2"><i class="bi bi-clock me-2"></i>Waktu kejadian</td>
                        <td class="py-2">{{ $auditTrail->created_at->format('d M Y, H:i:s') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2"><i class="bi bi-geo-alt me-2"></i>Alamat IP</td>
                        <td class="py-2"><code>{{ $auditTrail->ip_address }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-2"><i class="bi bi-laptop me-2"></i>Perangkat</td>
                        <td class="py-2">
                            @php
                                $ua = $auditTrail->user_agent ?? '';
                                $browser = 'Tidak Diketahui';
                                $os = '';
                                if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')) $browser = 'Google Chrome';
                                elseif (str_contains($ua, 'Edg')) $browser = 'Microsoft Edge';
                                elseif (str_contains($ua, 'Firefox')) $browser = 'Mozilla Firefox';
                                elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) $browser = 'Safari';
                                elseif (str_contains($ua, 'Opera') || str_contains($ua, 'OPR')) $browser = 'Opera';
                                
                                if (str_contains($ua, 'Windows')) $os = 'Windows';
                                elseif (str_contains($ua, 'Mac')) $os = 'MacOS';
                                elseif (str_contains($ua, 'Linux')) $os = 'Linux';
                                elseif (str_contains($ua, 'Android')) $os = 'Android';
                                elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';
                            @endphp
                            <span>{{ $browser }}</span>
                            @if($os) <span class="text-muted">di {{ $os }}</span> @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if(in_array($auditTrail->action, ['update', 'create', 'delete']))
<div class="row g-3">
    {{-- Data Sebelum Perubahan --}}
    @if(($auditTrail->action == 'update' || $auditTrail->action == 'delete') && $auditTrail->old_values)
    <div class="col-md-6 mb-3">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger bg-opacity-10 text-danger d-flex align-items-center gap-2">
                <i class="bi bi-arrow-counterclockwise"></i>
                <h6 class="mb-0 fw-bold">
                    @if($auditTrail->action == 'delete')
                        Data yang Dihapus
                    @else
                        Data Sebelum Diubah
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%;">Kolom</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $oldData = is_array($auditTrail->old_values) ? $auditTrail->old_values : json_decode($auditTrail->old_values, true) ?? []; @endphp
                        @foreach($oldData as $key => $val)
                            @if(!in_array($key, ['remember_token', 'email_verified_at', 'password']))
                            <tr>
                                <td class="text-muted fw-semibold" style="font-size: .85rem;">{{ $fieldNames[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td style="font-size: .85rem;">
                                    @if(is_null($val))
                                        <span class="text-muted fst-italic">kosong</span>
                                    @elseif(in_array($key, ['student_id', 'class_id', 'school_class_id', 'academic_year_id', 'finance_post_id', 'user_id', 'role_id', 'category_id']) && is_numeric($val))
                                        @php
                                            $resolvedName = $val;
                                            try {
                                                if ($key === 'student_id') $resolvedName = \App\Models\Student::find($val)->name ?? $val;
                                                elseif ($key === 'class_id' || $key === 'school_class_id') $resolvedName = \App\Models\SchoolClass::find($val)->name ?? $val;
                                                elseif ($key === 'academic_year_id') {
                                                    $ay = \App\Models\AcademicYear::find($val);
                                                    $resolvedName = $ay ? $ay->name . ' (' . $ay->semester . ')' : $val;
                                                }
                                                elseif ($key === 'finance_post_id') $resolvedName = \App\Models\FinancePost::find($val)->name ?? $val;
                                                elseif ($key === 'user_id') $resolvedName = \App\Models\User::find($val)->name ?? $val;
                                                elseif ($key === 'role_id') $resolvedName = \App\Models\Role::find($val)->name ?? $val;
                                                elseif ($key === 'category_id') $resolvedName = \App\Models\ExpenseCategory::find($val)->name ?? $val;
                                            } catch (\Exception $e) {}
                                        @endphp
                                        <span class="fw-semibold text-primary">{{ $resolvedName }}</span> <small class="text-muted">(ID: {{ $val }})</small>
                                    @elseif(is_bool($val) || $val === '1' || $val === '0' || $val === 1 || $val === 0)
                                        @if(in_array($key, ['is_active']))
                                            <span class="badge {{ $val ? 'bg-success' : 'bg-secondary' }}">{{ $val ? 'Ya' : 'Tidak' }}</span>
                                        @else
                                            {{ $valueTranslations[strval($val)] ?? $val }}
                                        @endif
                                    @elseif(is_string($val) && isset($valueTranslations[strtolower($val)]))
                                        {{ $valueTranslations[strtolower($val)] }}
                                    @elseif(is_numeric($val) && $val >= 1000 && in_array($key, ['amount', 'total_amount', 'total_paid', 'paid_amount', 'default_amount', 'amount_paid']))
                                        Rp {{ number_format($val, 0, ',', '.') }}
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Sesudah Perubahan --}}
    @if(($auditTrail->action == 'update' || $auditTrail->action == 'create') && $auditTrail->new_values)
    <div class="col-md-6 mb-3">
        <div class="card border-success h-100">
            <div class="card-header bg-success bg-opacity-10 text-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i>
                <h6 class="mb-0 fw-bold">
                    @if($auditTrail->action == 'create')
                        Data yang Ditambahkan
                    @else
                        Data Sesudah Diubah
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%;">Kolom</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $newData = is_array($auditTrail->new_values) ? $auditTrail->new_values : json_decode($auditTrail->new_values, true) ?? []; @endphp
                        @foreach($newData as $key => $val)
                            @if(!in_array($key, ['remember_token', 'email_verified_at', 'password']))
                            <tr>
                                <td class="text-muted fw-semibold" style="font-size: .85rem;">{{ $fieldNames[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td style="font-size: .85rem;">
                                    @if(is_null($val))
                                        <span class="text-muted fst-italic">kosong</span>
                                    @elseif(in_array($key, ['student_id', 'class_id', 'school_class_id', 'academic_year_id', 'finance_post_id', 'user_id', 'role_id', 'category_id']) && is_numeric($val))
                                        @php
                                            $resolvedName = $val;
                                            try {
                                                if ($key === 'student_id') $resolvedName = \App\Models\Student::find($val)->name ?? $val;
                                                elseif ($key === 'class_id' || $key === 'school_class_id') $resolvedName = \App\Models\SchoolClass::find($val)->name ?? $val;
                                                elseif ($key === 'academic_year_id') {
                                                    $ay = \App\Models\AcademicYear::find($val);
                                                    $resolvedName = $ay ? $ay->name . ' (' . $ay->semester . ')' : $val;
                                                }
                                                elseif ($key === 'finance_post_id') $resolvedName = \App\Models\FinancePost::find($val)->name ?? $val;
                                                elseif ($key === 'user_id') $resolvedName = \App\Models\User::find($val)->name ?? $val;
                                                elseif ($key === 'role_id') $resolvedName = \App\Models\Role::find($val)->name ?? $val;
                                                elseif ($key === 'category_id') $resolvedName = \App\Models\ExpenseCategory::find($val)->name ?? $val;
                                            } catch (\Exception $e) {}
                                        @endphp
                                        <span class="fw-semibold text-primary">{{ $resolvedName }}</span> <small class="text-muted">(ID: {{ $val }})</small>
                                    @elseif(is_bool($val) || $val === '1' || $val === '0' || $val === 1 || $val === 0)
                                        @if(in_array($key, ['is_active']))
                                            <span class="badge {{ $val ? 'bg-success' : 'bg-secondary' }}">{{ $val ? 'Ya' : 'Tidak' }}</span>
                                        @else
                                            {{ $valueTranslations[strval($val)] ?? $val }}
                                        @endif
                                    @elseif(is_string($val) && isset($valueTranslations[strtolower($val)]))
                                        {{ $valueTranslations[strtolower($val)] }}
                                    @elseif(is_numeric($val) && $val >= 1000 && in_array($key, ['amount', 'total_amount', 'total_paid', 'paid_amount', 'default_amount', 'amount_paid']))
                                        Rp {{ number_format($val, 0, ',', '.') }}
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<div class="mt-3">
    <a href="{{ route('audit-trails.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Log
    </a>
</div>
@endsection
