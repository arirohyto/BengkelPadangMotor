<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Bengkel Padang Motor</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #ea580c;
            --primary-dark: #c2410c;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --sidebar-width: 260px;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 1rem;
            left: 1rem;
            width: 280px;
            height: calc(100vh - 2rem);
            background: #FFFFFF;
            padding: 0;
            z-index: 1000;
            box-shadow: 0px 16px 44px rgba(0, 0, 0, 0.07);
            border-radius: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
            isolation: isolate;
        }

        .sidebar-brand {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            background: transparent;
            flex-shrink: 0;
        }

        .sidebar-brand h4 {
            color: #1e293b;
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar-brand small {
            color: #64748b;
            font-size: 0.75rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
            flex-grow: 1;
        }

        .sidebar-menu .menu-label {
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 1.5rem 0.5rem;
            border-top: 1px solid #f1f5f9;
            margin-top: 0.75rem;
            padding-top: 1.25rem;
        }

        .sidebar-menu .nav-link {
            color: #475569;
            padding: 10px 1.25rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            margin: 0.25rem 0.75rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .sidebar-menu .nav-link:hover {
            color: var(--primary-color);
            background: #fff7ed;
        }

        .sidebar-menu .nav-link.active {
            color: #ffffff;
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);
        }

        .sidebar-menu .nav-link i {
            width: 24px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 312px; /* 280px width + 16px left + 16px gap */
            min-height: 100vh;
            padding-right: 1rem; /* 16px padding on the right edge for symmetry */
        }

        /* Top Navbar */
        .top-navbar {
            background: transparent;
            padding: 1.5rem 0 0.5rem 0; /* Align with content cards, transparent layout */
            box-shadow: none;
            position: relative;
            z-index: 100;
        }

        .top-navbar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
        }

        .top-navbar .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .top-navbar .breadcrumb-item.active {
            color: var(--dark-color);
            font-weight: 500;
        }

        /* Content Area */
        .content-area {
            padding: 1rem 0; /* Paddings handled by main-content left and right margins */
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 12px;
            padding: 1.25rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color), #16a34a);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .stat-card.danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .stat-card.info {
            background: linear-gradient(135deg, var(--info-color), #0891b2);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Tables */
        .table th {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
        }

        /* Forms */
        .form-control,
        .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            padding: 0.625rem 0.875rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        /* Alert */
        .alert {
            border: none;
            border-radius: 10px;
        }

        /* Prediction Box */
        .prediction-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid var(--info-color);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .prediction-result {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4>Padang Motor</h4>
            @auth
                <small>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</small>
            @endauth
        </div>
        <div class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="menu-label">Data Master</div>
            <a href="{{ route('pelanggan.index') }}"
                class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Pelanggan
            </a>
            <a href="{{ route('motor.index') }}" class="nav-link {{ request()->routeIs('motor.*') ? 'active' : '' }}">
                <i class="bi bi-bicycle"></i> Data Motor
            </a>
            <a href="{{ route('sparepart.index') }}"
                class="nav-link {{ request()->routeIs('sparepart.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Data Sparepart
            </a>

            <div class="menu-label">Transaksi</div>
            <a href="{{ route('transaksi.index') }}"
                class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> Riwayat Servis
            </a>
            <a href="{{ route('transaksi.create') }}"
                class="nav-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Input Servis Baru
            </a>

            @auth
                @if(auth()->user()->isPemilik())
                    <div class="menu-label">Pemilik</div>
                    <a href="{{ route('laporan.index') }}"
                        class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Laporan Keuangan
                    </a>
                    <a href="{{ route('monitoring.index') }}"
                        class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                        <i class="bi bi-cpu"></i> Monitoring Model CART
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
                @auth
                    <span class="text-muted">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                @endauth
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Format currency
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }
    </script>
    @stack('scripts')
</body>

</html>