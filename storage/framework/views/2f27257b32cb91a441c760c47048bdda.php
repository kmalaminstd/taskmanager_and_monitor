<?php $__env->startSection('title', $user->name . ' - Profile'); ?>
<?php $__env->startSection('page-title', 'User Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-pencil"></i> Edit User
    </a>
    <a href="<?php echo e(route('admin.performance.show', $user)); ?>" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-bar-chart"></i> Performance
    </a>
</div>

<!-- Profile Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:64px;height:64px;font-size:1.5rem;background:linear-gradient(135deg,#2563EB,#1D4ED8);">
                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1"><?php echo e($user->name); ?></h4>
                <div class="text-muted small">
                    <i class="bi bi-envelope me-1"></i> <?php echo e($user->email); ?>

                    <span class="mx-2">·</span>
                    <span class="badge bg-<?php echo e($user->role === 'admin' ? 'dark' : 'primary'); ?>"><?php echo e(ucfirst($user->role)); ?></span>
                    <span class="badge bg-<?php echo e($user->status === 'active' ? 'success' : 'secondary'); ?>"><?php echo e(ucfirst($user->status)); ?></span>
                </div>
                <div class="text-muted small mt-1">
                    Joined <?php echo e($user->created_at->format('M d, Y')); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Tasks Overview -->
<div class="mb-2">
    <h6 class="fw-semibold text-muted text-uppercase" style="font-size:0.75rem;letter-spacing:0.05em;">
        Admin Assigned Tasks
    </h6>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Total</div>
            <div class="fs-4 fw-bold"><?php echo e($assignedStats['total']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold"><?php echo e($assignedStats['pending']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold"><?php echo e($assignedStats['in_progress']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Submitted</div>
            <div class="fs-4 fw-bold"><?php echo e($assignedStats['submitted']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-4 fw-bold text-success"><?php echo e($assignedStats['completed']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-4 fw-bold text-danger"><?php echo e($assignedStats['overdue']); ?></div>
        </div>
    </div>
</div>

<!-- Personal Tasks Overview -->
<div class="mb-2">
    <h6 class="fw-semibold text-muted text-uppercase" style="font-size:0.75rem;letter-spacing:0.05em;">
        Personal Tasks
    </h6>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Total</div>
            <div class="fs-4 fw-bold"><?php echo e($personalStats['total']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold"><?php echo e($personalStats['pending']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold"><?php echo e($personalStats['in_progress']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-4 fw-bold text-success"><?php echo e($personalStats['completed']); ?></div>
        </div>
    </div>
</div>

<!-- Tabs: Assigned Tasks + Personal Tasks -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#assignedTab" type="button">
            Assigned Tasks <span class="badge bg-primary ms-1"><?php echo e($assignedStats['total']); ?></span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#personalTab" type="button">
            Personal Tasks <span class="badge bg-secondary ms-1"><?php echo e($personalStats['total']); ?></span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Assigned Tasks Tab -->
    <div class="tab-pane fade show active" id="assignedTab">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Assigned</th>
                            <th>Assigned By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $assignedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($a->isOverdue() ? 'table-danger' : ''); ?>">
                            <td>
                                <a href="<?php echo e(route('admin.tasks.show', $a->task_id)); ?>" class="fw-medium">
                                    <?php echo e(Str::limit($a->task->title ?? '—', 40)); ?>

                                </a>
                                <?php if($a->task->category ?? false): ?>
                                    <br><small class="text-muted"><?php echo e($a->task->category->name); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-priority-<?php echo e($a->effective_priority); ?>">
                                    <?php echo e(ucfirst($a->effective_priority)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge badge-status-<?php echo e($a->status); ?>">
                                    <?php echo e(str_replace('_', ' ', ucfirst($a->status))); ?>

                                </span>
                                <?php if($a->isOverdue()): ?>
                                    <span class="badge bg-danger">Overdue</span>
                                <?php endif; ?>
                                <?php if(!$a->is_active): ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e($a->deadline?->format('M d, Y') ?? '—'); ?>

                            </td>
                            <td><small><?php echo e($a->assigned_at?->format('M d, Y')); ?></small></td>
                            <td><small><?php echo e($a->assignedBy->name ?? '—'); ?></small></td>
                            <td>
                                <a href="<?php echo e(route('admin.assignments.show', $a)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No assigned tasks</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($assignedTasks->hasPages()): ?>
            <div class="card-footer bg-white">
                <?php echo e($assignedTasks->appends(request()->except('assigned_page'))->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Personal Tasks Tab -->
    <div class="tab-pane fade" id="personalTab">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Frequency</th>
                            <th>Deadline</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $personalTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($pt->status === 'completed' ? 'opacity-75' : ''); ?>">
                            <td>
                                <span class="fw-medium <?php echo e($pt->status === 'completed' ? 'text-decoration-line-through' : ''); ?>">
                                    <?php echo e($pt->title); ?>

                                </span>
                                <?php if($pt->description): ?>
                                    <br><small class="text-muted"><?php echo e(Str::limit($pt->description, 50)); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-priority-<?php echo e($pt->priority); ?>">
                                    <?php echo e(ucfirst($pt->priority)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($pt->status === 'completed' ? 'success' : ($pt->status === 'in_progress' ? 'primary' : 'secondary')); ?>">
                                    <?php echo e(ucfirst($pt->status)); ?>

                                </span>
                            </td>
                            <td><small><?php echo e(str_replace('_', ' ', ucfirst($pt->frequency))); ?></small></td>
                            <td><?php echo e($pt->deadline?->format('M d, Y') ?? '—'); ?></td>
                            <td><?php echo e($pt->completed_at?->format('M d, Y') ?? '—'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No personal tasks</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($personalTasks->hasPages()): ?>
            <div class="card-footer bg-white">
                <?php echo e($personalTasks->appends(request()->except('personal_page'))->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/users/show.blade.php ENDPATH**/ ?>