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

    // Jalankan pertama kali saat halaman dimuat
    document.addEventListener("DOMContentLoaded", () => checkStatus(false));
</script>
@endpush
@endsection
