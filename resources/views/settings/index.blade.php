@extends('layouts.admin')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="row g-4">
    <div class="col-lg-3 col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="nav flex-column nav-pills border-0" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-4 border-bottom-0 fw-semibold text-secondary" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-building me-2"></i> Profil Sekolah
                    </button>
                    <button class="nav-link text-start py-3 px-4 border-bottom-0 fw-semibold text-secondary" id="v-pills-academic-tab" data-bs-toggle="pill" data-bs-target="#v-pills-academic" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-calendar-event me-2"></i> Tahun Ajaran
                    </button>
                    <button class="nav-link text-start py-3 px-4 border-bottom-0 fw-semibold text-secondary" id="v-pills-payment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-payment" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-credit-card me-2"></i> Pembayaran
                    </button>
                    <button class="nav-link text-start py-3 px-4 border-bottom-0 fw-semibold text-secondary" id="v-pills-smtp-tab" data-bs-toggle="pill" data-bs-target="#v-pills-smtp" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-envelope-at me-2"></i> SMTP Email
                    </button>
                    <button class="nav-link text-start py-3 px-4 fw-semibold text-secondary" id="v-pills-wa-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wa" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-whatsapp me-2"></i> WhatsApp Gateway
                    </button>
                    <button class="nav-link text-start py-3 px-4 fw-semibold text-secondary" id="v-pills-backup-tab" data-bs-toggle="pill" data-bs-target="#v-pills-backup" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-hdd-network me-2"></i> Backup Database
                    </button>
                    <button class="nav-link text-start py-3 px-4 fw-semibold text-secondary" id="v-pills-timesync-tab" data-bs-toggle="pill" data-bs-target="#v-pills-timesync" type="button" role="tab" style="border-radius: 0; position: relative;">
                        <i class="bi bi-clock-history me-2"></i> Sinkronisasi Waktu
                    </button>
                    <button class="nav-link text-start py-3 px-4 fw-semibold text-danger" type="button" data-bs-toggle="modal" data-bs-target="#resetModal" style="border-radius: 0;">
                        <i class="bi bi-exclamation-triangle me-2"></i> Reset Database
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-pills .nav-link { transition: all 0.2s ease; }
        .nav-pills .nav-link:hover:not(.active) { background-color: var(--primary-50); color: var(--primary-700) !important; }
        .nav-pills .nav-link.active { background-color: var(--primary-50) !important; color: var(--primary-700) !important; border-right: 3px solid var(--primary-600) !important; }
    </style>

    <div class="col-lg-9 col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form">
                    @csrf

                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- TAB PROFIL SEKOLAH -->
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                            <h5 class="mb-4 text-primary">Profil Identitas Sekolah</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Sekolah</label>
                                        <input type="text" name="school_name" class="form-control" value="{{ $settings['school_name'] ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alamat Lengkap</label>
                                        <textarea name="school_address" class="form-control" rows="3">{{ $settings['school_address'] ?? '' }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kota / Kabupaten (Untuk Cetak Kwitansi)</label>
                                        <input type="text" name="school_city" class="form-control" value="{{ $settings['school_city'] ?? '' }}" placeholder="Contoh: Tanjung Batu Timur">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Logo Sekolah</label>
                                        @if(isset($settings['school_logo']))
                                            <img src="{{ Storage::url($settings['school_logo']) }}" alt="Logo" class="img-fluid mb-2 border p-1 rounded" style="max-height: 120px;">
                                        @endif
                                        <input type="file" name="school_logo" class="form-control form-control-sm @error('school_logo') is-invalid @enderror" accept="image/*">
                                        @error('school_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Stempel Sekolah</label>
                                        @if(isset($settings['school_stamp']))
                                            <img src="{{ Storage::url($settings['school_stamp']) }}" alt="Stempel" class="img-fluid mb-2 border p-1 rounded" style="max-height: 120px;">
                                        @endif
                                        <input type="file" name="school_stamp" class="form-control form-control-sm @error('school_stamp') is-invalid @enderror" accept="image/*">
                                        @error('school_stamp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Telepon / HP</label>
                                    <input type="text" name="school_phone" class="form-control" value="{{ $settings['school_phone'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Resmi Sekolah</label>
                                    <input type="email" name="school_email" class="form-control" value="{{ $settings['school_email'] ?? '' }}">
                                </div>
                            </div>
                            
                            <hr class="my-4 border-light">
                            <h5 class="mb-4 text-primary">Identitas Aplikasi (UI)</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Aplikasi</label>
                                        <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? 'SKS - Sistem Keuangan Sekolah' }}" placeholder="Contoh: SKS - Sistem Keuangan Sekolah">
                                        <div class="form-text">Nama ini akan muncul di pojok kiri atas dan di halaman awal.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Logo Aplikasi</label>
                                        @if(isset($settings['app_logo']))
                                            <img src="{{ Storage::url($settings['app_logo']) }}" alt="App Logo" class="img-fluid mb-2 border p-1 rounded" style="max-height: 80px;">
                                        @endif
                                        <input type="file" name="app_logo" class="form-control form-control-sm @error('app_logo') is-invalid @enderror" accept="image/*">
                                        @error('app_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Logo untuk pojok kiri atas aplikasi dan halaman login. (Maks 2MB)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB TAHUN AJARAN -->
                        <div class="tab-pane fade" id="v-pills-academic" role="tabpanel">
                            <h5 class="mb-4 text-primary">Konfigurasi Tahun Ajaran Aktif</h5>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> Pilih tahun ajaran berjalan. Tagihan dan menu sistem secara default akan diarahkan ke tahun ajaran ini.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Ajaran Aktif</label>
                                <select name="active_academic_year_id" class="form-select w-50">
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ (isset($settings['active_academic_year_id']) && $settings['active_academic_year_id'] == $year->id) ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- TAB PAYMENT GATEWAY -->
                        <div class="tab-pane fade" id="v-pills-payment" role="tabpanel">
                            <h5 class="mb-4 text-primary">Konfigurasi Payment Gateway</h5>
                            <div class="mb-3">
                                <label class="form-label">Mode Gateway</label>
                                <select name="payment_gateway_mode" class="form-select w-50">
                                    <option value="sandbox" {{ (isset($settings['payment_gateway_mode']) && $settings['payment_gateway_mode'] == 'sandbox') ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="production" {{ (isset($settings['payment_gateway_mode']) && $settings['payment_gateway_mode'] == 'production') ? 'selected' : '' }}>Production (Live)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">API Key / Client Key</label>
                                <input type="text" name="payment_gateway_api_key" class="form-control" value="{{ $settings['payment_gateway_api_key'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Secret Key / Server Key</label>
                                <input type="password" name="payment_gateway_secret" class="form-control" value="{{ $settings['payment_gateway_secret'] ?? '' }}">
                            </div>
                        </div>

                        <!-- TAB SMTP -->
                        <div class="tab-pane fade" id="v-pills-smtp" role="tabpanel">
                            <h5 class="mb-4 text-primary">Konfigurasi Mail SMTP</h5>
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">SMTP Host</label>
                                    <input type="text" name="smtp_host" class="form-control" value="{{ $settings['smtp_host'] ?? '' }}" placeholder="smtp.gmail.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">SMTP Port</label>
                                    <input type="number" name="smtp_port" class="form-control" value="{{ $settings['smtp_port'] ?? '587' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">SMTP Username (Email)</label>
                                    <input type="text" name="smtp_user" class="form-control" value="{{ $settings['smtp_user'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SMTP Password</label>
                                    <input type="password" name="smtp_pass" class="form-control" value="{{ $settings['smtp_pass'] ?? '' }}">
                                </div>
                            </div>
                            <div class="mb-3 w-50">
                                <label class="form-label">Encryption</label>
                                <select name="smtp_encryption" class="form-select">
                                    <option value="tls" {{ (isset($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'tls') ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ (isset($settings['smtp_encryption']) && $settings['smtp_encryption'] == 'ssl') ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>

                        <!-- TAB WHATSAPP -->
                        <div class="tab-pane fade" id="v-pills-wa" role="tabpanel">
                            <h5 class="mb-4 text-primary">Konfigurasi WhatsApp Gateway (Lokal)</h5>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> Sistem secara default terhubung ke <code>http://localhost:3000</code> jika tidak diubah.
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">URL WA Gateway (IP / Localhost)</label>
                                <input type="text" name="wa_gateway_url" class="form-control" value="{{ $settings['wa_gateway_url'] ?? env('WA_GATEWAY_URL', 'http://localhost:3000') }}" placeholder="http://localhost:3000">
                                <div class="form-text">Biarkan <code>http://localhost:3000</code> jika aplikasi SPP dan WA Gateway berada di 1 VPS (server) yang sama. Ubah ke IP publik (contoh: <code>http://192.168.1.1:3000</code>) jika dipisah.</div>
                            </div>

                            {{-- PENGATURAN DELAY --}}
                            <div class="card border mb-4">
                                <div class="card-header bg-light fw-bold">
                                    <i class="bi bi-stopwatch me-2"></i> Pengaturan Delay Anti-Spam
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">Sistem akan menunggu waktu acak di antara nilai <strong>Minimal</strong> dan <strong>Maksimal</strong> sebelum mengirim pesan berikutnya. Ini mencegah akun terkena blokir WhatsApp.</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Delay Minimal (detik)</label>
                                            <div class="input-group">
                                                <input type="number" name="wa_delay_min" class="form-control" min="1" max="60" value="{{ $settings['wa_delay_min'] ?? 3 }}">
                                                <span class="input-group-text">detik</span>
                                            </div>
                                            <div class="form-text">Nilai minimal: 1 detik.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Delay Maksimal (detik)</label>
                                            <div class="input-group">
                                                <input type="number" name="wa_delay_max" class="form-control" min="1" max="120" value="{{ $settings['wa_delay_max'] ?? 7 }}">
                                                <span class="input-group-text">detik</span>
                                            </div>
                                            <div class="form-text">Nilai maksimal: 120 detik. Harus lebih besar dari Minimal.</div>
                                        </div>
                                    </div>
                                    <div class="mt-2 p-2 bg-light rounded small text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Contoh: Minimal <strong>{{ $settings['wa_delay_min'] ?? 3 }}</strong> detik, Maksimal <strong>{{ $settings['wa_delay_max'] ?? 7 }}</strong> detik → sistem akan acak delay antara {{ $settings['wa_delay_min'] ?? 3 }}–{{ $settings['wa_delay_max'] ?? 7 }} detik per pesan.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Template Pesan Pribadi (Menu Pembayaran)</label>
                                <textarea name="wa_template_personal" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [TOTAL_TUNGGAKAN], [RINCIAN]">{{ $settings['wa_template_personal'] ?? "Halo, Orang Tua/Wali dari *[NAMA_SISWA]*\n\nBerdasarkan catatan kami, ananda memiliki tagihan biaya sekolah sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nBerikut rinciannya:\n[RINCIAN]\nMohon untuk segera diselesaikan. Terima kasih." }}</textarea>
                                <div class="form-text">Gunakan penanda: <code>[NAMA_SISWA]</code>, <code>[TOTAL_TUNGGAKAN]</code>, <code>[RINCIAN]</code>. Watermark petugas akan ditambahkan otomatis di bawah.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Template Pesan Massal (Menu Daftar Tagihan)</label>
                                <textarea name="wa_template_bulk" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [TOTAL_TUNGGAKAN], [RINCIAN]">{{ $settings['wa_template_bulk'] ?? "PEMBERITAHUAN MASSAL\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami menginformasikan adanya tagihan sekolah yang belum lunas sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nRincian:\n[RINCIAN]\nHarap segera melunasi tagihan tersebut. Abaikan pesan ini jika sudah membayar." }}</textarea>
                                <div class="form-text">Pesan ini digunakan saat Anda menekan tombol Kirim Tagihan Massal.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Template Kuitansi Pembayaran (Otomatis)</label>
                                <textarea name="wa_template_receipt" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [NOMINAL_BAYAR], [RINCIAN_BAYAR], [SISA_TAGIHAN]">{{ $settings['wa_template_receipt'] ?? "TERIMA KASIH\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\nKami telah menerima pembayaran sebesar *Rp [NOMINAL_BAYAR]*.\n\nRincian Pembayaran:\n[RINCIAN_BAYAR]\n\nSisa Tunggakan Saat Ini: *Rp [SISA_TAGIHAN]*\nTerima kasih atas kerja samanya." }}</textarea>
                                <div class="form-text">Pesan ini akan <b>otomatis terkirim</b> setiap kali petugas memproses pembayaran. Gunakan penanda: <code>[NAMA_SISWA]</code>, <code>[NOMINAL_BAYAR]</code>, <code>[RINCIAN_BAYAR]</code>, <code>[SISA_TAGIHAN]</code>.</div>
                            </div>
                            
                            <hr class="my-4 border-light">
                            <h6 class="fw-bold mb-3">Pengaturan Pengingat Jatuh Tempo</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hari Pengingat (Sebelum/Sesudah Jatuh Tempo)</label>
                                <div class="input-group w-50">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Jeda (Delay) Kirim Minimal (Detik)</label>
                                    <input type="number" name="wa_delay_min" class="form-control" value="{{ $settings['wa_delay_min'] ?? 3 }}" min="1">
                                    <div class="form-text">Mencegah blokir WA karena spamming.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Jeda (Delay) Kirim Maksimal (Detik)</label>
                                    <input type="number" name="wa_delay_max" class="form-control" value="{{ $settings['wa_delay_max'] ?? 7 }}" min="2">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i> Simpan Konfigurasi WA
                                </button>
                            </div>
                        </div>

                        <!-- TAB TEMPLATE PESAN WA -->
                        <div class="tab-pane fade" id="v-pills-templates" role="tabpanel">
                            <h5 class="mb-4 text-primary">Template Pesan WhatsApp</h5>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Template Bukti Pembayaran Lunas</label>
                                <textarea name="wa_template_payment_success" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [TOTAL_BAYAR], [BULAN_BAYAR], [PETUGAS]">{{ $settings['wa_template_payment_success'] ?? "Terima kasih, pembayaran untuk *[NAMA_SISWA]* sebesar *Rp [TOTAL_BAYAR]* telah kami terima.\n\nRincian Pembayaran:\n[RINCIAN]\n\nTanggal: [TANGGAL]\nPetugas: [PETUGAS]\n\nStruk digital ini adalah bukti pembayaran yang sah." }}</textarea>
                                <div class="form-text">Pesan ini akan otomatis terkirim sesaat setelah pembayaran berhasil disimpan. Penanda yang bisa digunakan: <code>[NAMA_SISWA]</code>, <code>[TOTAL_BAYAR]</code>, <code>[RINCIAN]</code>, <code>[TANGGAL]</code>, <code>[PETUGAS]</code>.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Template Tagihan Massal</label>
                                <textarea name="wa_template_bulk" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [TOTAL_TUNGGAKAN]">{{ $settings['wa_template_bulk'] ?? "PEMBERITAHUAN MASSAL\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami menginformasikan adanya tagihan sekolah yang belum lunas sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nRincian:\n[RINCIAN]\n\nHarap segera melunasi tagihan tersebut. Abaikan pesan ini jika sudah membayar." }}</textarea>
                                <div class="form-text">Pesan ini dikirim saat Broadcast Pesan Massal di halaman tagihan. Gunakan penanda: <code>[NAMA_SISWA]</code>, <code>[TOTAL_TUNGGAKAN]</code>, <code>[RINCIAN]</code>.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Template Pesan Pribadi (Tunggakan)</label>
                                <textarea name="wa_template_personal" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [TOTAL_TUNGGAKAN]">{{ $settings['wa_template_personal'] ?? "Halo, Orang Tua/Wali dari *[NAMA_SISWA]*\n\nBerdasarkan catatan kami, ananda memiliki tagihan biaya sekolah sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nBerikut rinciannya:\n[RINCIAN]\n\nMohon untuk segera diselesaikan. Terima kasih." }}</textarea>
                                <div class="form-text">Pesan ini dikirim secara personal saat menekan tombol WA di profil siswa. Gunakan penanda: <code>[NAMA_SISWA]</code>, <code>[TOTAL_TUNGGAKAN]</code>, <code>[RINCIAN]</code>.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Template Pengingat Jatuh Tempo</label>
                                <textarea name="wa_template_due_reminder" class="form-control" rows="5" placeholder="Gunakan tag [NAMA_SISWA], [TOTAL_TUNGGAKAN], [JATUH_TEMPO]">{{ $settings['wa_template_due_reminder'] ?? "PENGINGAT TAGIHAN\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami mengingatkan bahwa tagihan sekolah ananda sebesar *Rp [TOTAL_TUNGGAKAN]* akan jatuh tempo pada *[JATUH_TEMPO]*.\n\nMohon kerjasamanya untuk menyelesaikan pembayaran sebelum tanggal tersebut.\nAbaikan pesan ini jika sudah melakukan pembayaran. Terima kasih." }}</textarea>
                                <div class="form-text">Pesan ini dikirim saat perintah pengingat dijalankan (otomatis via Cron Job atau manual). Gunakan penanda: <code>[NAMA_SISWA]</code>, <code>[TOTAL_TUNGGAKAN]</code>, <code>[JATUH_TEMPO]</code>, <code>[RINCIAN]</code>.</div>
                            </div>
                        </div>

                        <!-- TAB SINKRONISASI WAKTU -->
                        <div class="tab-pane fade" id="v-pills-timesync" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Sinkronisasi Waktu (NTP)</h5>
                                <div>
                                    <form action="{{ route('settings.sync-time') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Sistem akan mencoba menyinkronkan waktu dengan NTP Server. Pastikan web server dijalankan sebagai Administrator. Lanjutkan?')">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Sinkronisasi Sekarang
                                        </button>
                                    </form>
                                    <button type="submit" form="settings-form" class="btn btn-primary btn-sm">
                                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            </div>
                            
                            <div class="alert alert-info border-0 shadow-sm">
                                <i class="bi bi-info-circle-fill me-2"></i> Pengaturan ini berfungsi untuk mencocokkan jam pada sistem operasi Server/Komputer dengan jam internet (NTP). Hal ini berguna untuk mencegah masalah pada koneksi WhatsApp Gateway (Node.js) akibat jam yang tidak akurat. <strong>Penting: Fitur ini wajib membutuhkan hak akses Administrator pada OS Windows.</strong>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">NTP Server</label>
                                    <input type="text" name="ntp_server" class="form-control" value="{{ $settings['ntp_server'] ?? 'time.windows.com' }}" placeholder="Contoh: time.windows.com atau pool.ntp.org">
                                    <div class="form-text">Server internet referensi waktu.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Otomatis Sinkronisasi Harian</label>
                                    <select name="auto_sync_time" class="form-select">
                                        <option value="0" {{ isset($settings['auto_sync_time']) && $settings['auto_sync_time'] == '0' ? 'selected' : '' }}>Nonaktif</option>
                                        <option value="1" {{ isset($settings['auto_sync_time']) && $settings['auto_sync_time'] == '1' ? 'selected' : '' }}>Aktif (Jalankan via Cron Job)</option>
                                    </select>
                                    <div class="form-text">Jika aktif, sistem akan menyinkronkan waktu setiap hari pukul 00:00 (membutuhkan Cron Job/Scheduler).</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB BACKUP DATABASE -->
                        <div class="tab-pane fade" id="v-pills-backup" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="text-primary mb-0">Manajemen Backup & Restore</h5>
                                <div>
                                    <a href="{{ route('backup.download') }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-download me-1"></i> Unduh Langsung
                                    </a>
                                    <a href="{{ route('backup.manual') }}" class="btn btn-sm btn-success me-2" onclick="return confirm('Proses backup akan berjalan dan hasilnya akan dikirim ke WhatsApp Anda. Lanjutkan?')">
                                        <i class="bi bi-whatsapp me-1"></i> Kirim ke WA
                                    </a>
                                    <button type="submit" form="settings-form" class="btn btn-primary btn-sm">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i> Sistem dapat mencadangkan seluruh data (database) Anda secara otomatis ke WhatsApp atau diunduh langsung.
                            </div>

                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Pengaturan Auto Backup WA</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Nomor WhatsApp Penerima Backup</label>
                                        <input type="text" name="backup_wa_number" class="form-control" value="{{ $settings['backup_wa_number'] ?? '' }}" placeholder="Contoh: 08123456789">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Jadwal Auto Backup</label>
                                        <select name="backup_schedule" class="form-select">
                                            <option value="none" {{ (isset($settings['backup_schedule']) && $settings['backup_schedule'] == 'none') ? 'selected' : '' }}>Nonaktif</option>
                                            <option value="daily" {{ (isset($settings['backup_schedule']) && $settings['backup_schedule'] == 'daily') ? 'selected' : '' }}>Setiap Hari (23:00)</option>
                                            <option value="weekly" {{ (isset($settings['backup_schedule']) && $settings['backup_schedule'] == 'weekly') ? 'selected' : '' }}>Setiap Minggu</option>
                                            <option value="monthly" {{ (isset($settings['backup_schedule']) && $settings['backup_schedule'] == 'monthly') ? 'selected' : '' }}>Setiap Bulan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 border-start ps-4">
                                    <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i> Restore Database</h6>
                                    <p class="small text-muted mb-3">Pilih file <code>.sql</code> untuk memulihkan sistem. <b>Tindakan ini akan menimpa seluruh data yang ada saat ini secara permanen!</b></p>
                                    
                                    <div class="input-group mb-2">
                                        <input type="file" form="restoreForm" name="backup_file" class="form-control" accept=".sql" required>
                                        <button type="submit" form="restoreForm" class="btn btn-danger" onclick="return confirm('PERINGATAN KERAS! Seluruh data saat ini akan terhapus dan digantikan oleh file backup ini. Apakah Anda 100% yakin ingin melanjutkan?')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <hr class="my-4 border-light">
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 8px;">
                            <i class="bi bi-floppy me-2"></i> Simpan Pengaturan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Restore Database Form (Hidden, triggered by inputs in the tab) -->
<form id="restoreForm" action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
    @csrf
</form>
<!-- Modal Reset Database -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="resetModalLabel"><i class="bi bi-exclamation-triangle me-2"></i> Reset Data Aplikasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('settings.reset') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning mb-4">
                        <strong><i class="bi bi-cone-striped me-2"></i> PERINGATAN ZONA BERBAHAYA!</strong><br>
                        Tindakan ini akan <b>MENGHAPUS SEMUA DATA</b> yang Anda pilih dari database secara permanen. Auto-increment ID akan dikembalikan ke angka 1. Pastikan Anda sudah membackup data jika diperlukan.
                    </div>
                    
                    <h6 class="fw-bold mb-3">Pilih data yang ingin dikosongkan:</h6>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_students" value="1" id="reset_students">
                                <label class="form-check-label" for="reset_students">Data Siswa & Kelas Siswa</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_classes" value="1" id="reset_classes">
                                <label class="form-check-label" for="reset_classes">Data Master Kelas</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_academic_years" value="1" id="reset_academic_years">
                                <label class="form-check-label" for="reset_academic_years">Data Tahun Ajaran</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_finance_posts" value="1" id="reset_finance_posts">
                                <label class="form-check-label" for="reset_finance_posts">Data Pos Keuangan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_bills" value="1" id="reset_bills">
                                <label class="form-check-label" for="reset_bills">Data Tagihan SPP/Lainnya</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_payments" value="1" id="reset_payments">
                                <label class="form-check-label" for="reset_payments">Data Transaksi Pembayaran</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input reset-checkbox" type="checkbox" name="reset_expenses" value="1" id="reset_expenses">
                                <label class="form-check-label" for="reset_expenses">Data Pengeluaran (Buku Kas)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSelectAllReset">Pilih Semua</button>
                    </div>

                    <div class="bg-light p-3 border rounded">
                        <label class="form-label fw-bold text-danger">Masukkan Password Anda Untuk Melanjutkan:</label>
                        <input type="password" name="password" class="form-control border-danger" placeholder="Password Admin" required>
                        <div class="form-text text-muted">Untuk alasan keamanan, ketik password akun Anda saat ini untuk mengonfirmasi penghapusan.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold" onclick="return confirm('Apakah Anda benar-benar yakin ingin menghapus data yang dipilih? Data yang dihapus tidak dapat dikembalikan!')">
                        <i class="bi bi-trash-fill me-1"></i> Kosongkan Data Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnSelectAll = document.getElementById('btnSelectAllReset');
        const checkboxes = document.querySelectorAll('.reset-checkbox');
        let allSelected = false;

        if(btnSelectAll) {
            btnSelectAll.addEventListener('click', function() {
                allSelected = !allSelected;
                checkboxes.forEach(cb => cb.checked = allSelected);
                this.textContent = allSelected ? 'Batal Pilih Semua' : 'Pilih Semua';
                this.classList.toggle('btn-outline-secondary', !allSelected);
                this.classList.toggle('btn-secondary', allSelected);
            });
        }
    });
</script>

@endsection
