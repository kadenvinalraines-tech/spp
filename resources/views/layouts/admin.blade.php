<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin SPP') — {{ \App\Models\SchoolSetting::get('app_name', 'SKS - Sistem Keuangan Sekolah') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ==========================================
           DESIGN SYSTEM — CSS CUSTOM PROPERTIES
           ========================================== */
        :root {
            --sidebar-width: 264px;
            --topbar-height: 64px;
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-200: #bfdbfe;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --primary-800: #1e40af;
            --primary-900: #1e3a8a;
            --surface: #f1f5f9;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --sidebar-bg: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            --shadow-sm: 0 1px 2px rgba(0,0,0,.04), 0 2px 6px rgba(0,0,0,.04);
            --shadow-md: 0 2px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
            --shadow-lg: 0 4px 8px rgba(0,0,0,.08), 0 8px 24px rgba(0,0,0,.08);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --transition: all 0.2s cubic-bezier(.4,0,.2,1);
        }

        /* ==========================================
           GLOBAL BASE
           ========================================== */
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--surface);
            color: var(--text-primary);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ==========================================
           SIDEBAR
           ========================================== */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.15rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(59,130,246,.35);
        }

        .sidebar-brand-text {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .sidebar-brand-text small {
            display: block;
            font-weight: 400;
            font-size: 0.7rem;
            color: rgba(255,255,255,.45);
            letter-spacing: 0;
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 12px 80px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.1) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,.3);
            padding: 20px 12px 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.6);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
            position: relative;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            opacity: .7;
            transition: var(--transition);
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.9);
        }

        .sidebar-link:hover i { opacity: 1; }

        .sidebar-link.active {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(37,99,235,.35);
        }

        .sidebar-link.active i { opacity: 1; }

        /* Dropdown inside sidebar */
        .sidebar-dropdown .sidebar-dropdown-toggle::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.2s ease;
        }

        .sidebar-dropdown.open .sidebar-dropdown-toggle::after {
            transform: rotate(180deg);
        }

        .sidebar-dropdown-menu {
            display: none;
            padding-left: 34px;
        }

        .sidebar-dropdown.open .sidebar-dropdown-menu {
            display: block;
        }

        .sidebar-dropdown-menu .sidebar-link {
            font-size: 0.82rem;
            padding: 8px 12px;
            color: rgba(255,255,255,.5);
        }

        .sidebar-dropdown-menu .sidebar-link::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,.2);
            flex-shrink: 0;
            transition: var(--transition);
        }

        .sidebar-dropdown-menu .sidebar-link:hover::before,
        .sidebar-dropdown-menu .sidebar-link.active::before {
            background: var(--primary-500);
        }

        .sidebar-dropdown-menu .sidebar-link.active {
            background: rgba(59,130,246,.15);
            color: var(--primary-200);
            box-shadow: none;
        }

        /* ==========================================
           TOPBAR
           ========================================== */
        .app-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0,0,0,.03);
            transition: left 0.3s cubic-bezier(.4,0,.2,1);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 4px;
        }

        .topbar-page-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-ta-form .form-select {
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            background-color: var(--primary-50);
            color: var(--primary-700);
            padding: 6px 32px 6px 12px;
            transition: var(--transition);
        }

        .topbar-ta-form .form-select:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }

        .topbar-user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: 1px solid transparent;
            padding: 5px 12px 5px 6px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
        }

        .topbar-user-btn:hover {
            background: var(--primary-50);
            border-color: var(--border-color);
        }

        .topbar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .topbar-user-info {
            text-align: left;
        }

        .topbar-user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .topbar-user-role {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* ==========================================
           MAIN CONTENT
           ========================================== */
        .app-main {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(.4,0,.2,1);
        }

        .app-content {
            padding: 28px;
        }

        /* ==========================================
           SIDEBAR OVERLAY (MOBILE)
           ========================================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1035;
            backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-sidebar.show {
                transform: translateX(0);
            }

            .app-topbar {
                left: 0;
            }

            .app-main {
                margin-left: 0;
            }

            .topbar-toggle {
                display: block;
            }

            .topbar-user-info {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .app-content {
                padding: 12px;
            }

            .card-body {
                padding: 14px;
            }

            .card-header {
                padding: 12px 14px;
            }

            .card-header.d-flex.justify-content-between.align-items-center {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px;
            }

            .card-header > div {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                width: 100%;
            }

            .card-header > div > .btn {
                flex: 1 1 auto;
                text-align: center;
            }

            form.row.g-3 .btn {
                width: 100%;
                margin-bottom: 6px;
            }

            form.row.g-3 > div {
                margin-bottom: 4px;
            }

            .topbar-ta-form {
                display: none;
            }

            .app-topbar {
                padding: 0 16px;
            }
        }

        /* ==========================================
           CARDS
           ========================================== */
        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md) !important;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            background: var(--card-bg);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        /* ==========================================
           TABLES
           ========================================== */
        .table {
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--primary-50);
            color: var(--primary-800);
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 16px;
            border-bottom: 2px solid var(--primary-200);
            white-space: nowrap;
        }

        .table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background-color 0.15s ease;
        }

        .table tbody tr:hover {
            background-color: var(--primary-50) !important;
        }

        .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(241,245,249,.5);
        }

        /* ==========================================
           BUTTONS
           ========================================== */
        .btn {
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: var(--radius-sm);
            padding: 8px 16px;
            transition: var(--transition);
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
            border: none;
            box-shadow: 0 1px 3px rgba(37,99,235,.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-800) 100%);
            box-shadow: 0 3px 8px rgba(37,99,235,.35);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(135deg, #d97706, #b45309);
            border: none;
            color: #fff;
        }

        .btn-warning:hover { color: #fff; }

        .btn-secondary {
            background: var(--surface);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
            color: var(--text-primary);
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 0.8rem;
        }

        /* ==========================================
           FORM CONTROLS
           ========================================== */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            padding: 9px 14px;
            font-size: 0.875rem;
            transition: var(--transition);
            background-color: var(--card-bg);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-text {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* ==========================================
           BADGES
           ========================================== */
        .badge {
            font-weight: 600;
            font-size: 0.72rem;
            padding: 5px 10px;
            border-radius: 6px;
            letter-spacing: 0.02em;
        }

        /* ==========================================
           ALERTS
           ========================================== */
        .alert {
            border-radius: var(--radius-md);
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 14px 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2, #fecaca);
            color: #991b1b;
        }

        .alert-info {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #1e40af;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            color: #92400e;
        }

        .alert-dismissible .btn-close {
            padding: 18px;
        }

        /* ==========================================
           PAGINATION
           ========================================== */
        .pagination {
            gap: 4px;
        }

        .page-link {
            border-radius: var(--radius-sm) !important;
            font-size: 0.82rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 6px 12px;
        }

        .page-item.active .page-link {
            background: var(--primary-600);
            border-color: var(--primary-600);
        }

        /* ==========================================
           MODAL
           ========================================== */
        .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 18px 24px;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 14px 24px;
        }

        /* ==========================================
           NAV TABS
           ========================================== */
        .nav-tabs .nav-link {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-secondary);
            border: none;
            padding: 10px 18px;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            transition: var(--transition);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-700);
            border-bottom: 2px solid var(--primary-600);
            background: transparent;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            color: var(--text-primary);
            background: var(--primary-50);
        }

        /* ==========================================
           DROPDOWN MENU
           ========================================== */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: 6px;
        }

        .dropdown-item {
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            padding: 8px 14px;
            font-weight: 500;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: var(--primary-50);
            color: var(--primary-700);
        }

        /* ==========================================
           PROGRESS BARS
           ========================================== */
        .progress {
            border-radius: 100px;
            background: var(--border-color);
        }

        .progress-bar {
            border-radius: 100px;
        }

        /* ==========================================
           SCROLLBAR GLOBAL
           ========================================== */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ==========================================
           ANIMATIONS
           ========================================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .app-content > * {
            animation: fadeInUp 0.3s ease-out;
        }


        /* ==========================================
           UTILITY ADDITIONS
           ========================================== */
        .fs-7 {
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- ==========================================
     SIDEBAR
     ========================================== -->
@php
    $appNameFull = \App\Models\SchoolSetting::get('app_name', 'SKS - Sistem Keuangan Sekolah');
    $appNameShort = explode('-', $appNameFull)[0] ?? 'SKS';
    $appNameDesc = explode('-', $appNameFull)[1] ?? 'Sistem Keuangan Sekolah';
    $appLogo = \App\Models\SchoolSetting::get('app_logo');
@endphp
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        @if($appLogo)
        <div class="sidebar-brand-icon" style="background: transparent; box-shadow: none;">
            <img src="{{ Storage::url($appLogo) }}" alt="Logo" style="width: 38px; height: 38px; object-fit: contain; border-radius: 10px;">
        </div>
        @else
        <div class="sidebar-brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        @endif
        <div class="sidebar-brand-text">
            {{ trim($appNameShort) }}
            <small>{{ trim($appNameDesc) }}</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        {{-- MENU UTAMA --}}
        <div class="sidebar-section-title">Menu Utama</div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        @if(Auth::check())
            @if(Auth::user()->hasPermission('manage_classes'))
            <a href="{{ route('classes.index') }}" class="sidebar-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Data Kelas
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_students'))
            <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') && !request()->routeIs('promotions.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Data Siswa
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_promotions'))
            <a href="{{ route('promotions.index') }}" class="sidebar-link {{ request()->routeIs('promotions.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-up-circle-fill"></i> Kenaikan Kelas
            </a>
            @endif
            
            @if(Auth::user()->hasPermission('manage_students'))
            <a href="{{ route('alumni.index') }}" class="sidebar-link {{ request()->routeIs('alumni.*') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i> Daftar Alumni
            </a>
            @endif

            {{-- KEUANGAN --}}
            <div class="sidebar-section-title">Keuangan</div>

            @if(Auth::user()->hasPermission('manage_finance_posts'))
            <a href="{{ route('finance-posts.index') }}" class="sidebar-link {{ request()->routeIs('finance-posts.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Pos Keuangan
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_bills'))
            <div class="sidebar-dropdown {{ request()->routeIs('bills.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="sidebar-link sidebar-dropdown-toggle {{ request()->routeIs('bills.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i> Tagihan
                </a>
                <div class="sidebar-dropdown-menu">
                    <a href="{{ route('bills.index') }}" class="sidebar-link {{ request()->routeIs('bills.index') ? 'active' : '' }}">
                        Daftar Tagihan
                    </a>
                    <a href="{{ route('bills.generate') }}" class="sidebar-link {{ request()->routeIs('bills.generate') ? 'active' : '' }}">
                        Generate Tagihan
                    </a>
                    <a href="{{ route('bills.due-dates') }}" class="sidebar-link {{ request()->routeIs('bills.due-dates') ? 'active' : '' }}">
                        Jatuh Tempo Massal
                    </a>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasPermission('manage_payments'))
            <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front-fill"></i> Pembayaran
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_expenses'))
            <div class="sidebar-dropdown {{ request()->routeIs('expense*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="sidebar-link sidebar-dropdown-toggle {{ request()->routeIs('expense*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Pengeluaran
                </a>
                <div class="sidebar-dropdown-menu">
                    <a href="{{ route('expense-categories.index') }}" class="sidebar-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">
                        Kategori
                    </a>
                    <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.index') || request()->routeIs('expenses.create') || request()->routeIs('expenses.edit') ? 'active' : '' }}">
                        Daftar Pengeluaran
                    </a>
                </div>
            </div>
            @endif

            @if(Auth::user()->hasPermission('view_reports'))
            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill"></i> Laporan
            </a>
            @endif

            {{-- SISTEM --}}
            <div class="sidebar-section-title">Sistem</div>

            @if(Auth::user()->hasPermission('manage_academic_years'))
            <a href="{{ route('academic-years.index') }}" class="sidebar-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Tahun Ajaran
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_students') || Auth::user()->hasPermission('manage_classes'))
            <a href="{{ route('data-master.index') }}" class="sidebar-link {{ request()->routeIs('data-master.*') ? 'active' : '' }}">
                <i class="bi bi-database-fill-down"></i> Import/Export Data
            </a>
            @endif

            @if(Auth::user()->hasPermission('view_audit_trails'))
            <a href="{{ route('audit-trails.index') }}" class="sidebar-link {{ request()->routeIs('audit-trails.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Audit Trail
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_users'))
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Pengguna
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_roles'))
            <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i> Hak Akses
            </a>
            @endif

            <a href="{{ route('wa-gateway.index') }}" class="sidebar-link {{ request()->routeIs('wa-gateway.*') ? 'active' : '' }}">
                <i class="bi bi-whatsapp"></i> WA Gateway
            </a>

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan
            </a>
            @endif
        @endif
    </nav>
</aside>

<!-- ==========================================
     SIDEBAR OVERLAY (MOBILE)
     ========================================== -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ==========================================
     TOPBAR
     ========================================== -->
<header class="app-topbar">
    <div class="topbar-left">
        <button class="topbar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="topbar-page-title">@yield('title', 'Dashboard')</h1>
    </div>

    <div class="topbar-right">
        @auth
            {{-- Academic Year Selector --}}
            @if(isset($globalAcademicYears) && $globalAcademicYears->count() > 0 && isset($globalActiveYear))
            <form action="{{ route('academic-years.set-session') }}" method="POST" class="topbar-ta-form">
                @csrf
                <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                    @foreach($globalAcademicYears as $year)
                        <option value="{{ $year->id }}" {{ $globalActiveYear->id == $year->id ? 'selected' : '' }}>
                            TA {{ $year->name }} ({{ $year->semester }})
                        </option>
                    @endforeach
                </select>
            </form>
            @endif

            {{-- User Dropdown --}}
            <div class="dropdown">
                <button class="topbar-user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="true">
                    <div class="topbar-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="topbar-user-info">
                        <div class="topbar-user-name">{{ Auth::user()->name }}</div>
                        <div class="topbar-user-role">{{ Auth::user()->roles->first()->name ?? 'No Role' }}</div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2"></i> Profil Saya
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
        @endauth
    </div>
</header>

<!-- ==========================================
     MAIN CONTENT
     ========================================== -->
<main class="app-main">
    <div class="app-content">
        {{-- WA Gateway Global Notification --}}
        @if(Auth::check())
            <div id="wa-global-notification" class="alert alert-danger d-none align-items-center shadow-sm mb-4" role="alert" style="border-left: 4px solid #dc3545;">
                <i class="bi bi-whatsapp fs-4 me-3"></i>
                <div>
                    <strong>WhatsApp Gateway Disconnected!</strong><br>
                    <span class="small">Sistem tidak terhubung ke WA Gateway. Notifikasi otomatis tidak dapat dikirim. Silakan periksa server atau <a href="{{ route('wa-gateway.index') }}" class="alert-link">buka menu WA Gateway</a> untuk menghubungkan ulang.</span>
                </div>
            </div>
        @endif

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle  = document.getElementById('sidebarToggle');

    // Toggle sidebar on mobile
    if (toggle) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    // Close sidebar when clicking overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Auto wrap tables with .table-responsive if not already wrapped
    document.querySelectorAll('table.table').forEach(function(table) {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('table-responsive');
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });

    // Sidebar dropdown toggle
    document.querySelectorAll('.sidebar-dropdown-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.sidebar-dropdown').classList.toggle('open');
        });
    });

    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.classList.contains('show')) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000);
    });

    // Global WA Gateway Status Checker
    @if(Auth::check())
    function checkGlobalWaStatus() {
        // Skip if we are on the WA Gateway page itself, since it handles its own UI
        if (window.location.pathname.includes('/wa-gateway') && !window.location.pathname.includes('status')) return;

        fetch('{{ route("wa-gateway.status") }}')
            .then(res => res.json())
            .then(data => {
                const notif = document.getElementById('wa-global-notification');
                if (data.status !== 'ready') {
                    notif.classList.remove('d-none');
                    notif.classList.add('d-flex');
                } else {
                    notif.classList.add('d-none');
                    notif.classList.remove('d-flex');
                }
            })
            .catch(err => {
                const notif = document.getElementById('wa-global-notification');
                notif.classList.remove('d-none');
                notif.classList.add('d-flex');
            });
    }
    
    // Check shortly after load, then every 30 seconds
    setTimeout(checkGlobalWaStatus, 2000);
    setInterval(checkGlobalWaStatus, 30000);
    @endif
});
</script>

@stack('scripts')
</body>
</html>
