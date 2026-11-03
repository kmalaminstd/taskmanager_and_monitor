<?php $__env->startSection('title', 'My Tasks'); ?>
<?php $__env->startSection('page-title', 'My Assigned Tasks'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <form method="GET" class="row g-2">
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php $__currentLoopData = ['pending','in_progress','submitted','completed','returned','overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if(request('status') === $s): echo 'selected'; endif; ?>><?php echo e(str_replace('_',' ', ucfirst($s))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>
</div>

<div class="row g-3">
    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 <?php echo e($assignment->isOverdue() ? 'border-danger' : ''); ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge badge-priority-<?php echo e($assignment->effective_priority); ?>">
                        <?php echo e(ucfirst($assignment->effective_priority)); ?>

                    </span>
                    <span class="badge badge-status-<?php echo e($assignment->status); ?>">
                        <?php echo e(str_replace('_', ' ', ucfirst($assignment->status))); ?>

                    </span>
                </div>
                <h6 class="card-title">
                    <a href="<?php echo e(route('assignee.tasks.show', $assignment)); ?>" class="text-decoration-none">
                        <?php echo e(Str::limit($assignment->task->title, 50)); ?>

                    </a>
                </h6>
                <p class="card-text small text-muted mb-2">
                    <?php echo e(Str::limit($assignment->task->description, 80)); ?>

                </p>
                <div class="small text-muted">
                    <div><i class="bi bi-calendar"></i> 
                        <?php echo e($assignment->deadline ? $assignment->deadline->format('M d, Y') : 'No deadline'); ?>

                        <?php if($assignment->isOverdue()): ?>
                            <span class="text-danger fw-medium"> (Overdue)</span>
                        <?php endif; ?>
                    </div>
                    <div><i class="bi bi-person"></i> Assigned by <?php echo e($assignment->assignedBy->name ?? '—'); ?></div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?php echo e(route('assignee.tasks.show', $assignment)); ?>" class="btn btn-sm btn-outline-primary w-100">
                    View Details
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12">
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No tasks found.
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if($assignments->hasPages()): ?>
<div class="mt-4">
    <?php echo e($assignments->links()); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/assignee/tasks/index.blade.php ENDPATH**/ ?>