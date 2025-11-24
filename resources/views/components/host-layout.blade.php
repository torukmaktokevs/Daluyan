<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Host — Dashboard</title>

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
                    <small class="muted" style="font-size:12px;opacity:.9;">Host</small>
                </div>
            </div>
            <button id="btnToggle" class="icon-btn" aria-label="Toggle sidebar"><i class="fa fa-bars"></i></button>
        </div>

        <nav class="nav">
        
            </a>

            <div class="muted" style="padding:8px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em;">Profile</div>
            <a class="nav-link {{ request()->routeIs('profile.dashboard') ? 'active' : '' }}" href="{{ route('profile.dashboard') }}"><i class="fa fa-user"></i><span>Profile</span></a>

            <div class="muted" style="padding:8px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em;">Property Management</div>
            <a class="nav-link {{ request()->routeIs('host.properties.*') ? 'active' : '' }}" href="{{ route('host.properties.index') }}"><i class="fa fa-building"></i><span>Manage Apartments</span></a>
            <a class="nav-link" href="{{ route('host.properties.create') }}"><i class="fa fa-plus"></i><span>Add Property</span></a>

            <a class="nav-link {{ request()->routeIs('host.tenants.*') ? 'active' : '' }}" href="{{ route('host.tenants.index') }}"><i class="fa fa-users"></i><span>Tenant List</span></a>
            <a class="nav-link {{ request()->routeIs('host.applications.*') ? 'active' : '' }}" href="{{ route('host.applications.index') }}"><i class="fa fa-inbox"></i><span>Applications</span></a>

    
            <a class="nav-link {{ request()->routeIs('host.maintenance.*') ? 'active' : '' }}" href="{{ route('host.maintenance.index') }}"><i class="fa fa-screwdriver-wrench"></i><span>Maintenance Requests</span></a>

            <!-- Messages / Chat link removed for host sidebar -->
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
