<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - TASK MASTER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --sidebar-bg: #0F172A;
            --sidebar-width: 260px;
            --bg: #F8FAFC;
            --text: #1E293B;
        }
        body { background: var(--bg); color: var(--text); font-family: system-ui, -apple-system, sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            transition: transform 0.3s;
        }
        .sidebar .nav-link {
            color: #94A3B8;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            margin: 0.15rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(37, 99, 235, 0.15);
            color: #fff;
        }
        .sidebar .nav-link.active { background: var(--primary); color: #fff; }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #E2E8F0;
            transition: box-shadow 0.2s;
        }
        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .stat-card .icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .badge-priority-high { background: #FEE2E2; color: #DC2626; }
        .badge-priority-medium { background: #FEF3C7; color: #D97706; }
        .badge-priority-low { background: #D1FAE5; color: #059669; }
        .badge-status-pending { background: #E0E7FF; color: #4338CA; }
        .badge-status-in_progress { background: #DBEAFE; color: #1D4ED8; }
        .badge-status-submitted { background: #FEF3C7; color: #B45309; }
        .badge-status-completed { background: #D1FAE5; color: #047857; }
        .badge-status-returned { background: #FEE2E2; color: #B91C1C; }
        .badge-status-cancelled { background: #F1F5F9; color: #64748B; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="p-3 border-bottom border-secondary">
            <h5 class="text-white mb-0 fw-bold"><i class="bi bi-check2-square"></i> TASK MASTER</h5>
            <small class="text-secondary">Admin Panel</small>
        </div>
        <nav class="nav flex-column py-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                <i class="bi bi-list-task"></i> Tasks
            </a>
            <a href="{{ route('admin.assignments.index') }}" class="nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Assignments
            </a>
            <a href="{{ route('admin.transfers.index') }}" class="nav-link {{ request()->routeIs('admin.transfers.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i> Transfers
            </a>
            <a href="{{ route('admin.approvals.index') }}" class="nav-link {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                <i class="bi bi-check2-circle"></i> Approvals
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Calendar
            </a>
            <a href="{{ route('admin.performance.index') }}" class="nav-link {{ request()->routeIs('admin.performance.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> Performance
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Reports
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Users
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Settings
            </a>
            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notifications
                @if(auth()->user()->unreadNotifications->count())
                    <span class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <hr class="border-secondary mx-3">
            <a href="{{ route('logout') }}" class="nav-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </nav>
    </aside>

    <div class="main-content">
        <header class="topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
