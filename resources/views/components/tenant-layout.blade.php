<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tenant — Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @stack('styles')
    @stack('head')
</head>
<body class="admin-body">
    <x-banner />

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-left">
                <div style="display:flex;flex-direction:column;line-height:1.2">
                    <span class="title">{{ auth()->user()?->name ?? 'User' }}</span>
                    <small class="muted" style="font-size:12px;opacity:.9;">Tenant</small>
                </div>
            </div>
            <button id="btnToggle" class="icon-btn" aria-label="Toggle sidebar"><i class="fa fa-bars"></i></button>
        </div>

        <nav class="nav">
            <div class="muted" style="padding:8px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em;">Main</div>
            <a class="nav-link active" href="#tenant-dashboard">
                <i class="fa fa-chart-line"></i><span>Dashboard</span>
            </a>
            <div class="muted" style="padding:8px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em;">Profile</div>
            <a class="nav-link" href="#my-profile"><i class="fa fa-user"></i><span>My Profile</span></a>
            <a class="nav-link" href="{{ route('tenant.my-apartment') }}"><i class="fa fa-building"></i><span>My Apartment</span></a>

            <div class="nav-link dropdown"><i class="fa fa-file-invoice-dollar"></i><span>Applications & Payments</span><i class="fa fa-chevron-down"></i></div>
            <div class="submenu">
                 <a href="{{ route('tenant.applications.index') }}">Application Status</a>
                <a href="{{ route('tenant.payments.index') }}" class="{{ request()->routeIs('tenant.payments.*') ? 'active' : '' }}">Payments</a>
            </div>

            <div class="nav-link dropdown"><i class="fa fa-screwdriver-wrench"></i><span>Maintenance & Requests</span><i class="fa fa-chevron-down"></i></div>
            <div class="submenu">
                <a href="{{ route('tenant.maintenance.index') }}" class="{{ request()->routeIs('tenant.maintenance.*') ? 'active' : '' }}">Maintenance Requests</a>
                <a href="{{ route('tenant.maintenance.index', ['tab' => 'history']) }}" class="{{ request()->routeIs('tenant.maintenance.*') && request('tab')==='history' ? 'active' : '' }}">Request History</a>
            </div>

           

            <div class="muted" style="padding:8px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em;">Account</div>
            <a class="nav-link" href="{{ route('tenant.browsing') }}"><i class="fa fa-compass"></i><span>Browse Apartments</span></a>
        </nav>

    </aside>

    <!-- Main -->
    <main class="main">
        <header class="topbar">
            <div class="search">
                <input id="globalSearch" type="search" placeholder="Search anything..." />
                <button class="icon-btn" aria-label="Search"><i class="fa fa-search"></i></button>
            </div>

            <div class="top-actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn ghost logout-link">Logout</button>
                </form>
            </div>
        </header>

        <section class="page-content">
            {{ $slot }}
        </section>
    </main>

    @stack('modals')
    @stack('scripts')
    @stack('body')
</body>
</html>
