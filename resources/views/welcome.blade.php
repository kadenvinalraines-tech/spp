@php
    $appNameFull = \App\Models\SchoolSetting::get('app_name', 'SKS - Sistem Keuangan Sekolah');
    $appNameShort = explode('-', $appNameFull)[0] ?? 'SKS';
    $appNameDesc = explode('-', $appNameFull)[1] ?? 'Sistem Keuangan Sekolah';
    $appLogo = \App\Models\SchoolSetting::get('app_logo');
    $schoolName = \App\Models\SchoolSetting::get('school_name', 'Sekolah Demo');
    $schoolAddress = \App\Models\SchoolSetting::get('school_address', 'Jl. Pendidikan No. 1');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trim($appNameFull) }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 4rem 0;
        }
        
        .hero-bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            opacity: 0.1;
        }
        
        .hero-bg-shapes .shape-1 {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: #ffffff;
            filter: blur(80px);
        }
        
        .hero-bg-shapes .shape-2 {
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: #38bdf8;
            filter: blur(100px);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .card-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .app-logo-container {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
            margin: 0 auto 1.5rem;
        }
        
        .app-logo-container img {
            max-width: 50px;
            max-height: 50px;
            object-fit: contain;
        }
        
        .app-logo-container i {
            font-size: 2.5rem;
            color: #2563eb;
        }
        
        .btn-custom-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-custom-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(37, 99, 235, 0.4);
            color: white;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        
        .feature-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .school-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="hero-bg-shapes">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
        </div>
        
        <div class="container hero-content">
            <div class="row align-items-center justify-content-between g-5">
                
                <!-- Left Content -->
                <div class="col-lg-6 text-white text-center text-lg-start">
                    <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 0.85rem;">
                        <i class="bi bi-shield-check me-1"></i> Versi 2.0 Modern
                    </span>
                    
                    <h1 class="display-4 fw-bold mb-3" style="letter-spacing: -0.02em;">
                        {{ trim($appNameShort) }} <br>
                        <span class="text-info" style="font-size: 0.8em;">{{ trim($appNameDesc) }}</span>
                    </h1>
                    
                    <p class="lead mb-4 opacity-75" style="max-width: 500px; margin: 0 auto 0 auto;">
                        Kelola administrasi dan pembayaran sekolah dengan lebih cepat, aman, dan profesional. Dilengkapi dengan notifikasi WhatsApp otomatis.
                    </p>
                    
                    <div class="school-info mb-4 text-start d-inline-block d-lg-block mx-auto mx-lg-0" style="max-width: 400px;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-building fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $schoolName }}</h6>
                                <small class="opacity-75">{{ $schoolAddress }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content (Card) -->
                <div class="col-lg-5">
                    <div class="card card-glass p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="app-logo-container">
                                @if($appLogo)
                                    <img src="{{ Storage::url($appLogo) }}" alt="Logo">
                                @else
                                    <i class="bi bi-wallet2"></i>
                                @endif
                            </div>
                            <h3 class="fw-bold text-dark mb-1">Selamat Datang</h3>
                            <p class="text-muted small">Silakan masuk ke akun Anda</p>
                        </div>
                        
                        <div class="features-list mb-4">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="bi bi-whatsapp"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">Notifikasi WhatsApp</h6>
                                    <p class="text-muted small mb-0">Informasi tagihan otomatis ke wali murid.</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">Laporan Real-time</h6>
                                    <p class="text-muted small mb-0">Pantau kas dan tunggakan kapan saja.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-3">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-custom-primary">
                                        <i class="bi bi-grid-1x2-fill me-2"></i> Masuk ke Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-custom-primary">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Log in
                                    </a>
                                @endauth
                            @endif
                        </div>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">&copy; {{ date('Y') }} {{ trim($appNameShort) }}. All rights reserved.</small>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

</body>
</html>
