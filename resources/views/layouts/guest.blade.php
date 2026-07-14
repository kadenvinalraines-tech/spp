@php
    $appNameFull = \App\Models\SchoolSetting::get('app_name', 'Sistem Keuangan Sekolah');
    $appLogo = \App\Models\SchoolSetting::get('app_logo');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login — {{ $appNameFull }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
            -webkit-font-smoothing: antialiased;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .login-brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #fff;
        }

        .login-brand h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 4px;
            letter-spacing: -0.03em;
        }

        .login-brand p {
            color: rgba(255,255,255,.55);
            font-size: 0.85rem;
            margin: 0;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
        }

        .login-card .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 6px;
        }

        .login-card .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .login-card .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }

        .login-card .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(37,99,235,.3);
        }

        .login-card .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(37,99,235,.4);
        }

        .login-card .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(255,255,255,.4);
            font-size: 0.78rem;
        }

        .invalid-feedback {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-brand">
            <div class="login-brand-icon">
                @if($appLogo)
                    <img src="{{ Storage::url($appLogo) }}" alt="Logo">
                @else
                    <i class="bi bi-mortarboard-fill"></i>
                @endif
            </div>
            <h1>{{ $appNameFull }}</h1>
            <p>Masuk ke panel administrasi</p>
        </div>

        <div class="login-card">
            {{ $slot }}
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} {{ $appNameFull }}. All rights reserved.
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
