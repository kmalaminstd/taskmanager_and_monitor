<?php $__env->startSection('title', $user->name . ' Workload'); ?>
<?php $__env->startSection('page-title', $user->name . ' — Workload'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('admin.workload.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Active Tasks</div>
            <div class="fs-2 fw-bold"><?php echo e($workload['active_tasks']); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-2 fw-bold text-danger"><?php echo e($workload['overdue_tasks']); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Workload Level</div>
            <div class="fs-2"><span class="badge bg-<?php echo e($workload['color']); ?> fs-5"><?php echo e($workload['label']); ?></span></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Active Assignments</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="<?php echo e($a->isOverdue() ? 'table-danger' : ''); ?>">
                    <td>
                        <a href="<?php echo e(route('admin.tasks.show', $a->task_id)); ?>"><?php echo e(Str::limit($a->task->title ?? '—', 40)); ?></a>
                    </td>
                    <td><span class="badge badge-status-<?php echo e($a->status); ?>"><?php echo e(str_replace('_',' ', $a->status)); ?></span></td>
                    <td><span class="badge badge-priority-<?php echo e($a->effective_priority); ?>"><?php echo e($a->effective_priority); ?></span></td>
                    <td><?php echo e($a->deadline?->format('M d, Y') ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No active assignments</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/workload/show.blade.php ENDPATH**/ ?>