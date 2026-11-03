<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'My Workspace'); ?> - TASK MASTER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-check2-square"></i> TASK MASTER</h5>
            <small>My Workspace</small>
        </div>
        <nav class="nav flex-column">
            <a href="<?php echo e(route('assignee.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('assignee.dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?php echo e(route('assignee.tasks.index')); ?>" class="nav-link <?php echo e(request()->routeIs('assignee.tasks.*') ? 'active' : ''); ?>">
                <i class="bi bi-list-check"></i> My Tasks
            </a>
            <a href="<?php echo e(route('assignee.personal-tasks.index')); ?>" class="nav-link <?php echo e(request()->routeIs('assignee.personal-tasks.*') ? 'active' : ''); ?>">
                <i class="bi bi-journal-plus"></i> Personal Tasks
            </a>
            <a href="<?php echo e(route('calendar.index')); ?>" class="nav-link <?php echo e(request()->routeIs('calendar.*') ? 'active' : ''); ?>">
                <i class="bi bi-calendar3"></i> Calendar
            </a>
            <a href="<?php echo e(route('assignee.performance.index')); ?>" class="nav-link <?php echo e(request()->routeIs('assignee.performance.*') ? 'active' : ''); ?>">
                <i class="bi bi-graph-up"></i> Performance
            </a>
            <hr class="nav-divider">
            <a href="<?php echo e(route('notifications.index')); ?>" class="nav-link <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
                <i class="bi bi-bell"></i> Notifications
                <?php if(auth()->user()->unreadNotifications->count()): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?php echo e(auth()->user()->unreadNotifications->count()); ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo e(route('logout')); ?>" class="nav-link"
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
                <h5><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-sm btn-light position-relative">
                    <i class="bi bi-bell"></i>
                    <?php if(auth()->user()->unreadNotifications->count()): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;">
                            <?php echo e(auth()->user()->unreadNotifications->count()); ?>

                        </span>
                    <?php endif; ?>
                </a>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.75rem;font-weight:600;">
                            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                        </div>
                        <span class="d-none d-md-inline"><?php echo e(auth()->user()->name); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted"><?php echo e(auth()->user()->email); ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="page-content">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-1"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-1"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>

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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/layouts/assignee.blade.php ENDPATH**/ ?>