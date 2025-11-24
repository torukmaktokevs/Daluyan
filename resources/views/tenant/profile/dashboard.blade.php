<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard | Apartment System</title>
    @vite(['resources/css/tenant-dashboard.css', 'resources/js/tenant-dashboard.js'])
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-left">
                <div class="logo">🏠</div>
                <div class="title">Apartment System</div>
            </div>
            <button class="icon-btn menu-toggle">☰</button>
        </div>

        <nav class="nav">
            <a href="#dashboard" class="nav-link active" data-section="dashboard">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="#application" class="nav-link" data-section="application">
                <span>📝</span>
                <span>Application</span>
            </a>
            <a href="#apartment" class="nav-link" data-section="apartment">
                <span>🏢</span>
                <span>Apartment</span>
            </a>
            <a href="{{ route('tenant.maintenance.index') }}" class="nav-link" data-section="maintenance">
                <span>🔧</span>
                <span>Maintenance</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <button class="btn small outline">Help</button>
            <button class="btn ghost small">Settings</button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <!-- Topbar -->
        <header class="topbar">
            <div class="search">
                <span>🔍</span>
                <input type="text" placeholder="Search...">
            </div>
            <div class="top-actions">
                <button class="icon-btn">🔔</button>
                <div class="profile">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop&crop=face" alt="Profile">
                    <span class="name">John Doe</span>
                    <ul class="profile-menu">
                        <li><a href="#">Profile</a></li>
                        <li><a href="#">Settings</a></li>
                        <li><a href="#" class="logout-link">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <div class="page-head">
                    <h1>Dashboard</h1>
                    <p class="muted">Welcome back, John! Here's your overview.</p>
                </div>

                <div class="grid stats">
                    <div class="stat-card">
                        <div>
                            <div class="muted">Rent Due</div>
                            <div class="accent">$1,200</div>
                            <div class="muted small">Due in 5 days</div>
                        </div>
                        <div>💰</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="muted">Maintenance</div>
                            <div>2 Requests</div>
                            <div class="muted small">1 pending</div>
                        </div>
                        <div>🔧</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="muted">Lease End</div>
                            <div>Aug 15, 2024</div>
                            <div class="muted small">180 days remaining</div>
                        </div>
                        <div>📅</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="muted">Notifications</div>
                            <div>3 Unread</div>
                            <div class="muted small">View all</div>
                        </div>
                        <div>🔔</div>
                    </div>
                </div>

                <div class="grid">
                    <div class="card">
                        <h3>Recent Activity</h3>
                        <ul class="activity">
                            <li>Rent payment received - Today</li>
                            <li>Maintenance request updated - Yesterday</li>
                            <li>Lease document signed - 3 days ago</li>
                            <li>Community notice posted - 1 week ago</li>
                        </ul>
                    </div>
                    <div class="card">
                        <h3>Quick Actions</h3>
                        <div class="actions">
                            <button class="btn">Pay Rent</button>
                            <a href="{{ route('tenant.maintenance.index') }}" class="btn outline">Request Maintenance</a>
                            <button class="btn ghost">View Documents</button>
                            <button class="btn ghost">Contact Manager</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Application Section -->
            <section id="application" class="content-section">
                <div class="page-head">
                    <h1>Application</h1>
                    <p class="muted">Manage your rental applications</p>
                </div>

                <div class="card">
                    <h3>Current Application Status</h3>
                    <div class="application-status">
                        <div class="status-item">
                            <span class="status-badge approved">Approved</span>
                            <p>Application #APP-2024-001</p>
                            <p class="muted">Submitted on January 15, 2024</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Application History</h3>
                    <div class="application-history">
                        <p>No previous applications</p>
                    </div>
                </div>
            </section>

            <!-- Apartment Section -->
            <section id="apartment" class="content-section">
                <div class="page-head">
                    <h1>Apartment</h1>
                    <p class="muted">Your apartment details and information</p>
                </div>

                <div class="grid">
                    <div class="card">
                        <h3>Apartment Details</h3>
                        <div class="apartment-info">
                            <p><strong>Unit:</strong> #304</p>
                            <p><strong>Building:</strong> Skyline Towers</p>
                            <p><strong>Address:</strong> 123 Main St, City, State 12345</p>
                            <p><strong>Type:</strong> 2 Bed, 2 Bath</p>
                            <p><strong>Square Feet:</strong> 950 sq ft</p>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Lease Information</h3>
                        <div class="lease-info">
                            <p><strong>Lease Start:</strong> February 1, 2024</p>
                            <p><strong>Lease End:</strong> August 15, 2024</p>
                            <p><strong>Monthly Rent:</strong> $1,200</p>
                            <p><strong>Security Deposit:</strong> $1,200</p>
                            <p><strong>Parking Spot:</strong> #P-42</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Apartment Documents</h3>
                    <div class="actions">
                        <button class="btn outline">View Lease Agreement</button>
                        <button class="btn outline">House Rules</button>
                        <button class="btn outline">Emergency Contacts</button>
                    </div>
                </div>
            </section>

            <!-- Maintenance Section -->
            <section id="maintenance" class="content-section">
                <div class="page-head">
                    <h1>Maintenance</h1>
                    <p class="muted">Submit and track maintenance requests</p>
                </div>

                <div class="grid">
                    <div class="card">
                        <h3>Submit New Request</h3>
                        <form class="maintenance-form">
                            <div class="form-group">
                                <label for="issue-type">Issue Type</label>
                                <select id="issue-type" class="form-input">
                                    <option value="">Select issue type</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="appliance">Appliance</option>
                                    <option value="heating">Heating/Cooling</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-input" rows="4" placeholder="Describe the issue..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="urgency">Urgency</label>
                                <select id="urgency" class="form-input">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                            <button type="submit" class="btn">Submit Request</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3>Active Requests</h3>
                        <div class="maintenance-requests">
                            <div class="request-item">
                                <div class="request-header">
                                    <span class="request-id">#MNT-2024-001</span>
                                    <span class="status-badge pending">Pending</span>
                                </div>
                                <p><strong>Issue:</strong> Leaky faucet in kitchen</p>
                                <p class="muted">Submitted: February 10, 2024</p>
                            </div>
                            <div class="request-item">
                                <div class="request-header">
                                    <span class="request-id">#MNT-2024-002</span>
                                    <span class="status-badge in-progress">In Progress</span>
                                </div>
                                <p><strong>Issue:</strong> AC not cooling properly</p>
                                <p class="muted">Submitted: February 5, 2024</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Maintenance History</h3>
                    <div class="maintenance-history">
                        <p>No completed maintenance requests</p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>