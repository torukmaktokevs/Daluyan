<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Daluyan Admin — Dashboard</title>

  <!-- Fonts + Icons + Chart.js -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Shared CSS -->
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-left">
        <span class="logo">🏠</span>
        <span class="title">Daluyan Admin</span>
      </div>
      <button id="btnToggle" class="icon-btn" aria-label="Toggle sidebar"><i class="fa fa-bars"></i></button>
    </div>

    <nav class="nav">
      <a class="nav-link active" href="index.html"><i class="fa fa-chart-line"></i><span>Dashboard</span></a>
      <a class="nav-link" href="tasks.html"><i class="fa fa-tasks"></i><span>Tasks</span></a>
      <a class="nav-link" href="people.html"><i class="fa fa-users"></i><span>People</span></a>
      <a class="nav-link" href="properties.html"><i class="fa fa-map-marker-alt"></i><span>Properties</span></a>
      <div class="nav-link dropdown">
  <i class="fa fa-cog"></i><span>Settings</span><i class="fa fa-chevron-down"></i>
</div>

<div class="submenu">
  <a href="profile.html">Profile</a>
  <a href="settings.html">Settings</a>
  </div>
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
          <img src="https://i.pravatar.cc/150?img=12" alt="Profile" />
          <span class="name">Admin</span>
          <ul class="profile-menu" id="profileMenu">
            <li><a href="profile.html">View profile</a></li>
            <li><a href="settings.html">Settings</a></li>
            <li><a href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </header>

    <section class="page-content">
      <div class="page-head">
        <h1>Dashboard</h1>
        <p class="muted">Overview of rentals, users, and activity.</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid stats">
        <div class="card stat-card">
          <div>
            <h3 id="cardPending">0</h3>
            <p class="muted">Pending Approvals</p>
          </div>
          <i class="fa fa-clock fa-2x accent"></i>
        </div>

        <div class="card stat-card">
          <div>
            <h3 id="cardNewUsers">0</h3>
            <p class="muted">New Users (7d)</p>
          </div>
          <i class="fa fa-user-plus fa-2x accent"></i>
        </div>

        <div class="card stat-card">
          <div>
            <h3 id="cardVerified">0</h3>
            <p class="muted">Verified Users</p>
          </div>
          <i class="fa fa-user-check fa-2x accent"></i>
        </div>

        <div class="card stat-card">
          <div>
            <h3 id="cardProperties">0</h3>
            <p class="muted">Properties</p>
          </div>
          <i class="fa fa-building fa-2x accent"></i>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid">
        <div class="card">
          <h3>Registrations (last 12 months)</h3>
          <canvas id="regChart" height="140"></canvas>
        </div>

        <div class="card">
          <h3>Verification Status</h3>
          <canvas id="verChart" height="140"></canvas>
        </div>
      </div>

      <!-- Quick actions & Activity -->
      <div class="grid">
        <div class="card">
          <h3>Quick Actions</h3>
          <div class="actions">
            <button id="btnNewProperty" class="btn">+ Add Property</button>
            <button id="btnExport" class="btn ghost">Export CSV</button>
            <a href="tasks.html" class="btn outline"><i class="fa fa-tasks"></i> Manage Tasks</a>
          </div>
        </div>

        <div class="card">
          <h3>Recent Activity</h3>
          <ul class="activity" id="recentActivity"></ul>
        </div>
      </div>
    </section>
  </main>

  <!-- Shared script -->
  <script src="script.js"></script>
</body>
</html>
