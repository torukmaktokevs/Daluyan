<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daluyan Admin — Dashboard</title>

    <!-- Fonts + Icons + Chart.js -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Admin-only CSS/JS via Vite -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @stack('styles')
</head>
<body class="admin-body">
    <x-banner />

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-left">
                <span class="logo">🏠</span>
                <span class="title">Admin</span>
            </div>
            <button id="btnToggle" class="icon-btn" aria-label="Toggle sidebar"><i class="fa fa-bars"></i></button>
        </div>

        <nav class="nav">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fa fa-chart-line"></i><span>Dashboard</span>
            </a>
            <div class="nav-link dropdown" id="usersDropdown">
                <i class="fa fa-users"></i><span>Users</span><i class="fa fa-chevron-down"></i>
            </div>
            <div class="submenu" id="usersSubmenu">
                <a href="{{ route('admin.users.tenants.index') }}" class="{{ request()->routeIs('admin.users.tenants.*') ? 'active' : '' }}">Tenants</a>
                <a href="{{ route('admin.users.hosts.index') }}" class="{{ request()->routeIs('admin.users.hosts.*') ? 'active' : '' }}">Hosts</a>
            </div>
            <a class="nav-link" href="#"><i class="fa fa-map-marker-alt"></i><span>Properties</span></a>

            <!-- Removed Tasks and Settings per request -->
           
        </nav>

        <div class="sidebar-footer">
            <button id="themeToggle" class="btn small">🌙 Dark</button>
            <small class="muted">v1.0</small>
        </div>
    </aside>

    <!-- Main -->
    <main class="main">
        <header class="topbar">
            <div class="search">
                <input id="globalSearch" type="search" placeholder="Search anything..." />
                <button class="icon-btn" aria-label="Search"><i class="fa fa-search"></i></button>
            </div>

            <div class="top-actions">
                <div class="profile" id="profileBtn">
                    <img src="{{ auth()->user()?->profile_photo_url ?? 'https://i.pravatar.cc/150' }}" alt="Profile" />
                    <span class="name">{{ auth()->user()?->name ?? 'Admin' }}</span>
                    <ul class="profile-menu" id="profileMenu">
                        <li><a href="{{ route('profile.dashboard') }}">View profile</a></li>
                        <li><a href="#">Settings</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-link">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <section class="page-content">
            {{ $slot }}
        </section>
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>
