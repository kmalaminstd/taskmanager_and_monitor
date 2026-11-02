<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Workspace') - TASK MASTER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --sidebar-bg: #0F172A;
            --sidebar-width: 240px;
            --bg: #F8FAFC;
        }
        body { background: var(--bg); font-family: system-ui, -apple-system, sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #94A3B8;
            padding: 0.7rem 1.1rem;
            border-radius: 0.5rem;
            margin: 0.12rem 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(37, 99, 235, 0.2);
            color: #fff;
        }
        .sidebar .nav-link.active { background: var(--primary); }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: 0.7rem 1.25rem;
            position: sticky; top: 0; z-index: 999;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.15rem;
            border: 1px solid #E2E8F0;
        }
        .stat-card .icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="p-3 border-bottom border-secondary">
            <h5 class="text-white mb-0 fw-bold"><i class="bi bi-check2-square"></i> TASK MASTER</h5>
            <small class="text-secondary">My Workspace</small>
        </div>
        <nav class="nav flex-column py-3">
            <a href="{{ route('assignee.dashboard') }}" class="nav-link {{ request()->routeIs('assignee.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('assignee.tasks.index') }}" class="nav-link {{ request()->routeIs('assignee.tasks.*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> My Tasks
            </a>
            <a href="{{ route('assignee.personal-tasks.index') }}" class="nav-link {{ request()->routeIs('assignee.personal-tasks.*') ? 'active' : '' }}">
                <i class="bi bi-journal-plus"></i> Personal Tasks
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Calendar
            </a>
            <a href="{{ route('assignee.performance.index') }}" class="nav-link {{ request()->routeIs('assignee.performance.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Performance
            </a>
            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notifications
                @if(auth()->user()->unreadNotifications->count())
                    <span class="badge bg-danger rounded-pill ms-auto">{{ auth()->user()->unreadNotifications->count() }}</span>
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
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-md-inline">{{ auth()->user()->name }}</span>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="p-3 p-md-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
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
