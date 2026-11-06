<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Workspace') - TASK MASTER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-check2-square"></i> TASK MASTER</h5>
            <small>My Workspace</small>
        </div>
@php
    $assistanceIncoming = \App\Models\TaskAssistanceRequest::whereHas('users', function ($q) {
        $q->where('user_id', auth()->id())->where('task_assistance_users.status', 'pending');
    })->count();
    $assistanceReview = \App\Models\TaskAssistanceRequest::where('requester_id', auth()->id())
        ->whereHas('users', fn ($u) => $u->where('task_assistance_users.status', 'submitted'))
        ->count();
    $assistanceBadge = $assistanceIncoming + $assistanceReview;
    $personalPending = \App\Models\PersonalTask::where('user_id', auth()->id())
        ->whereIn('status', ['pending', 'in_progress'])
        ->count();
@endphp
        <nav class="nav flex-column">
            <a href="{{ route('assignee.dashboard') }}" class="nav-link {{ request()->routeIs('assignee.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('assignee.tasks.index') }}" class="nav-link {{ request()->routeIs('assignee.tasks.*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> My Tasks
            </a>
            <a href="{{ route('assignee.personal-tasks.index') }}" class="nav-link {{ request()->routeIs('assignee.personal-tasks.*') ? 'active' : '' }}">
                <i class="bi bi-journal-plus"></i> Personal Tasks
                @if($personalPending > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $personalPending }}</span>
                @endif
            </a>
            <a href="{{ route('assignee.assistance.index') }}" class="nav-link {{ request()->routeIs('assignee.assistance.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Assistance
                @if($assistanceBadge > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $assistanceBadge }}</span>
                @endif
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Calendar
            </a>
            <a href="{{ route('assignee.performance.index') }}" class="nav-link {{ request()->routeIs('assignee.performance.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Performance
            </a>
            <hr class="nav-divider">
            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notifications
                @if(auth()->user()->unreadNotifications->count())
                    <span class="badge bg-danger rounded-pill ms-auto">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <a href="{{ route('logout') }}" class="nav-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </nav>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5>@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light position-relative">
                    <i class="bi bi-bell"></i>
                    @if(auth()->user()->unreadNotifications->count())
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.75rem;font-weight:600;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
        });
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
