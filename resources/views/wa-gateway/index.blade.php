@extends('layouts.admin')

@section('title', 'WhatsApp Gateway')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 mb-4" style="border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden;">
            <div class="card-header border-0 text-white text-center py-4" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <i class="bi bi-whatsapp mb-2 d-block" style="font-size: 2.5rem; opacity: 0.9;"></i>
                <h5 class="mb-0 fw-bold">WhatsApp Gateway</h5>
                <p class="mb-0 text-white-50 small">Status Koneksi Server</p>
            </div>
            <div class="card-body text-center p-4 p-md-5">
                <div id="loading" class="py-4">
                    <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="mt-4 fw-semibold text-dark">Mengecek koneksi...</h6>
                    <p class="text-muted small mb-0">Menghubungi server Node.js</p>
                </div>

                <div id="qr-container" class="d-none py-2">
                    <div class="mb-4">
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 mb-3">
                            <i class="bi bi-x-circle-fill me-1"></i> Belum Terhubung
                        </span>
                        <h5 class="fw-bold text-dark">Scan QR Code</h5>
                        <p class="text-muted small">Buka WhatsApp di HP Anda > Tautkan Perangkat > Arahkan kamera ke QR Code di bawah ini.</p>
                    </div>
                    
                    <div class="bg-light p-3 rounded-4 d-inline-block mx-auto mb-3 border">
                        <img id="qr-image" src="" alt="QR Code" class="img-fluid" style="max-width: 240px;">
                    </div>
                    
                    <div class="alert alert-warning border-warning border-opacity-50 text-start small mb-0 rounded-3">
                        <i class="bi bi-info-circle-fill me-1"></i> Jika barcode kedaluwarsa, silakan klik tombol Refresh di bawah.
                    </div>
                </div>

                <div id="ready-container" class="d-none py-4">
                    <div class="mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                            <i class="bi bi-check-lg text-success" style="font-size: 4rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Terhubung!</h4>
                    <p class="text-muted mb-0">WhatsApp Gateway aktif dan siap digunakan untuk mengirim notifikasi tagihan & pembayaran otomatis.</p>
                </div>

                <div id="error-container" class="d-none py-4">
                    <div class="mb-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark">Gagal Terhubung</h5>
                    <p id="error-message" class="text-muted small">Server Node.js tidak merespons atau belum dijalankan.</p>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 text-center pb-4 pt-0">
                <button onclick="checkStatus()" class="btn btn-light border w-100 fw-semibold text-secondary mb-2" style="border-radius: 10px; padding: 10px;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center mt-4">
    <div class="col-md-10 col-lg-8">
        <div class="card border-0 mb-4" style="border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pengiriman Terbaru</h6>
                <button onclick="fetchLogs()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Segarkan</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0 text-sm align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-4">Tujuan</th>
                                <th>Status</th>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="logs-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengiriman terbaru.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkStatus(isPolling = false) {
        if (!isPolling) {
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('qr-container').classList.add('d-none');
            document.getElementById('ready-container').classList.add('d-none');
            document.getElementById('error-container').classList.add('d-none');
        }

        fetch('{{ route('wa-gateway.status') }}')
            .then(response => response.json())
            .then(data => {
                // Sembunyikan loading setelah respon didapat
                document.getElementById('loading').classList.add('d-none');
                
                if (data.status === 'ready') {
                    document.getElementById('qr-container').classList.add('d-none');
                    document.getElementById('ready-container').classList.remove('d-none');
                    document.getElementById('error-container').classList.add('d-none');
                } else if (data.status === 'qr') {
                    document.getElementById('ready-container').classList.add('d-none');
                    document.getElementById('error-container').classList.add('d-none');
                    
                    document.getElementById('qr-image').src = data.qr;
                    document.getElementById('qr-container').classList.remove('d-none');
                    
                    // Polling setiap 3 detik secara senyap
                    setTimeout(() => checkStatus(true), 3000);
                } else if (data.status === 'loading') {
                    document.getElementById('qr-container').classList.add('d-none');
                    document.getElementById('ready-container').classList.add('d-none');
                    document.getElementById('error-container').classList.add('d-none');
                    
                    document.getElementById('loading').classList.remove('d-none');
                    document.querySelector('#loading p').innerText = "Menunggu mesin WhatsApp menyiapkan barcode...";
                    
                    // Polling senyap
                    setTimeout(() => checkStatus(true), 2000);
                } else {
                    document.getElementById('qr-container').classList.add('d-none');
                    document.getElementById('ready-container').classList.add('d-none');
                    
                    document.getElementById('error-container').classList.remove('d-none');
                    if (data.message) {
                        document.getElementById('error-message').innerText = data.message;
                    }
                }
            })
            .catch(error => {
                if (!isPolling) {
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('error-container').classList.remove('d-none');
                    document.getElementById('error-message').innerText = "Server Node.js belum berjalan. Buka terminal dan jalankan 'node server.js' di folder wa-gateway.";
                }
            });
    }

    function fetchLogs() {
        fetch('{{ route('wa-gateway.logs') }}')
            .then(response => response.json())
            .then(logs => {
                const tbody = document.getElementById('logs-tbody');
                if (!logs || logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengiriman terbaru.</td></tr>';
                    return;
                }
                
                tbody.innerHTML = '';
                logs.forEach(log => {
                    let statusHtml = '';
                    if (log.status === 'success') {
                        statusHtml = '<span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i> Berhasil</span>';
                    } else if (log.status === 'error') {
                        statusHtml = '<span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle me-1"></i> Gagal</span>';
                    } else {
                        statusHtml = '<span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Antre...</span>';
                    }
                    
                    const time = new Date(log.time).toLocaleTimeString('id-ID', { hour: '2-digit', minute:'2-digit', second:'2-digit' });
                    
                    let nameHtml = `<div class="fw-bold text-dark">${log.name || 'Siswa'}</div><div class="small text-muted">${log.phone}</div>`;
                    let actionHtml = '';
                    if (log.status === 'error' && log.message_content) {
                        const safeName = (log.name || '').replace(/'/g, "\\'");
                        const b64Msg = btoa(encodeURIComponent(log.message_content));
                        actionHtml = `<button class="btn btn-sm btn-outline-danger" onclick="resendMessage('${log.phone}', '${safeName}', '${b64Msg}')" title="Kirim Ulang"><i class="bi bi-arrow-repeat"></i> Ulang</button>`;
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4">${nameHtml}</td>
                            <td>${statusHtml}</td>
                            <td class="text-muted small">${time}</td>
                            <td class="text-muted small text-truncate" style="max-width: 150px;">${log.error || 'Terkirim'}</td>
                            <td class="pe-4 text-end">${actionHtml}</td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error('Error fetching logs:', error));
    }

    function resendMessage(phone, name, base64Msg) {
        if (!confirm('Kirim ulang tagihan ke ' + (name || phone) + '?')) return;
        
        const message = decodeURIComponent(atob(base64Msg));
        
        fetch('{{ route('wa-gateway.resend') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone, name, message })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                alert('Pesan berhasil dimasukkan kembali ke antrean!');
                fetchLogs();
            } else {
                alert('Gagal: ' + res.message);
            }
        })
        .catch(e => alert('Terjadi kesalahan koneksi.'));
    }

    // Jalankan pertama kali saat halaman dimuat
    document.addEventListener("DOMContentLoaded", () => {
        checkStatus(false);
        fetchLogs();
        // Polling setiap 5 detik
        setInterval(fetchLogs, 5000);
    });
</script>
@endpush
@endsection
