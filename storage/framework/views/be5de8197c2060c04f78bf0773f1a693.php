<?php $__env->startSection('title', 'Task Monitoring'); ?>
<?php $__env->startSection('page-title', 'Task Monitoring'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="<?php echo e(route('admin.tasks.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Task
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.tasks.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search task name..." value="<?php echo e($filters['search'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <?php $__currentLoopData = ['pending','in_progress','submitted','completed','returned','cancelled','overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php if(($filters['status'] ?? '') === $status): echo 'selected'; endif; ?>>
                            <?php echo e(str_replace('_', ' ', ucfirst($status))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <?php $__currentLoopData = ['high','medium','low']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p); ?>" <?php if(($filters['priority'] ?? '') === $p): echo 'selected'; endif; ?>><?php echo e(ucfirst($p)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assignee_id" class="form-select">
                    <option value="">All Assignees</option>
                    <?php $__currentLoopData = $assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php if(($filters['assignee_id'] ?? '') == $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?php echo e(route('admin.tasks.index')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e($assignment->isOverdue() ? 'table-danger' : ''); ?>">
                    <td>
                        <a href="<?php echo e(route('admin.tasks.show', $assignment->task)); ?>" class="text-decoration-none fw-medium">
                            <?php echo e(Str::limit($assignment->task->title ?? '—', 45)); ?>

                        </a>
                        <?php if($assignment->task->category): ?>
                            <br><small class="text-muted"><?php echo e($assignment->task->category->name); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($assignment->assignee->name ?? '—'); ?></td>
                    <td>
                        <?php $prio = $assignment->effective_priority; ?>
                        <span class="badge badge-priority-<?php echo e($prio); ?>"><?php echo e(ucfirst($prio)); ?></span>
                    </td>
                    <td>
                        <span class="badge badge-status-<?php echo e($assignment->status); ?>">
                            <?php echo e(str_replace('_', ' ', ucfirst($assignment->status))); ?>

                        </span>
                        <?php if($assignment->isOverdue()): ?>
                            <span class="badge bg-danger ms-1">Overdue</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo e($assignment->deadline ? $assignment->deadline->format('M d, Y') : '—'); ?>

                        <?php if($assignment->deadline): ?>
                            <br><small class="text-muted"><?php echo e($assignment->deadline->diffForHumans()); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?php echo e($assignment->assigned_at?->format('M d, Y')); ?></small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?php echo e(route('admin.tasks.show', $assignment->task)); ?>" class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.tasks.edit', $assignment->task)); ?>" class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No assignments found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($assignments->hasPages()): ?>
    <div class="card-footer bg-white">
        <?php echo e($assignments->links('vendor.pagination.bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/tasks/index.blade.php ENDPATH**/ ?>