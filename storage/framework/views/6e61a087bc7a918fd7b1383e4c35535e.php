<?php $__env->startSection('title', 'Assignments'); ?>
<?php $__env->startSection('page-title', 'All Assignments'); ?>

<?php $__env->startSection('content'); ?>
<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Active</div>
            <div class="fs-4 fw-bold"><?php echo e($counts['total']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold"><?php echo e($counts['pending']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold"><?php echo e($counts['in_progress']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Submitted</div>
            <div class="fs-4 fw-bold"><?php echo e($counts['submitted']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small text-danger">Overdue</div>
            <div class="fs-4 fw-bold text-danger"><?php echo e($counts['overdue']); ?></div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search task..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <?php $__currentLoopData = ['pending','in_progress','submitted','completed','returned','overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if(request('status')===$s): echo 'selected'; endif; ?>><?php echo e(str_replace('_',' ', ucfirst($s))); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assignee_id" class="form-select form-select-sm">
                    <option value="">All Assignees</option>
                    <?php $__currentLoopData = $assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php if(request('assignee_id')==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    <?php $__currentLoopData = ['high','medium','low']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p); ?>" <?php if(request('priority')===$p): echo 'selected'; endif; ?>><?php echo e(ucfirst($p)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?php echo e(route('admin.assignments.index')); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Assignee</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Assigned</th>
                    <th>Assigned By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e($a->isOverdue() ? 'table-danger' : ''); ?>">
                    <td>
                        <a href="<?php echo e(route('admin.tasks.show', $a->task)); ?>" class="fw-medium text-decoration-none">
                            <?php echo e(Str::limit($a->task->title ?? '—', 40)); ?>

                        </a>
                        <?php if($a->task->category ?? false): ?>
                            <br><small class="text-muted"><?php echo e($a->task->category->name); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($a->assignee->name ?? '—'); ?></td>
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
                    </td>
                    <td>
                        <?php echo e($a->deadline?->format('M d, Y') ?? '—'); ?>

                        <?php if($a->deadline): ?>
                            <br><small class="text-muted"><?php echo e($a->deadline->diffForHumans()); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><small><?php echo e($a->assigned_at?->format('M d, Y')); ?></small></td>
                    <td><small><?php echo e($a->assignedBy->name ?? '—'); ?></small></td>
                    <td>
                        <a href="<?php echo e(route('admin.assignments.show', $a)); ?>" class="btn btn-sm btn-outline-primary" title="View Assignment">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?php echo e(route('admin.transfers.create', $a->task)); ?>" class="btn btn-sm btn-outline-warning" title="Transfer">
                            <i class="bi bi-arrow-left-right"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No assignments found
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($assignments->hasPages()): ?>
    <div class="card-footer bg-white"><?php echo e($assignments->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/assignments/index.blade.php ENDPATH**/ ?>