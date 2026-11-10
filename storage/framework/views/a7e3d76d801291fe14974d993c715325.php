<?php $__env->startSection('title', 'My Personal Tasks'); ?>
<?php $__env->startSection('page-title', 'My Personal Tasks'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All</option>
            <?php $__currentLoopData = ['pending','in_progress','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s); ?>" <?php if(request('status')===$s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>
    <a href="<?php echo e(route('assignee.personal-tasks.create')); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> New Personal Task
    </a>
</div>

<div class="row g-3">
    <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 <?php echo e($task->status === 'completed' ? 'opacity-75' : ''); ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge badge-priority-<?php echo e($task->priority); ?>"><?php echo e(ucfirst($task->priority)); ?></span>
                    <span class="badge bg-<?php echo e($task->status === 'completed' ? 'success' : 'secondary'); ?>">
                        <?php echo e(ucfirst($task->status)); ?>

                    </span>
                </div>
                <h6 class="<?php echo e($task->status === 'completed' ? 'text-decoration-line-through' : ''); ?>">
                    <?php echo e($task->title); ?>

                </h6>
                <?php if($task->description): ?>
                    <p class="small text-muted mb-2"><?php echo e(Str::limit($task->description, 70)); ?></p>
                <?php endif; ?>
                <div class="small text-muted">
                    <?php if($task->deadline): ?>
                        <i class="bi bi-calendar"></i> <?php echo e($task->deadline->format('M d, Y')); ?>

                    <?php endif; ?>
                    <span class="ms-2"><i class="bi bi-arrow-repeat"></i> <?php echo e(str_replace('_',' ', ucfirst($task->frequency))); ?></span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 d-flex gap-1">
                <?php if($task->status !== 'completed'): ?>
                    <form method="POST" action="<?php echo e(route('assignee.personal-tasks.complete', $task)); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-success" title="Mark Complete"><i class="bi bi-check-lg"></i></button>
                    </form>
                <?php endif; ?>
                <a href="<?php echo e(route('assignee.personal-tasks.edit', $task)); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?php echo e(route('assignee.personal-tasks.destroy', $task)); ?>" onsubmit="return confirm('Delete this task?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                <button type="button" class="btn-solid btn btn-sm btn-outline-dark theme-primary" data-bs-toggle="modal" data-bs-target="#personalTaskViewModal<?php echo e($task->id); ?>">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="personalTaskViewModal<?php echo e($task->id); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Personal Task - <?php echo e($task->title); ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <?php if($task->description): ?>
                            <p><?php echo e($task->description); ?></p>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge badge-priority-<?php echo e($task->priority); ?>">Priority: <?php echo e(ucfirst($task->priority)); ?></span>
                            <span class="badge bg-<?php echo e($task->status === 'completed' ? 'success' : 'secondary'); ?>">
                               Status: <?php echo e(ucfirst($task->status)); ?>

                            </span>
                        </div>

                        <div class="small text-muted">
                            <?php if($task->deadline): ?>
                                <i class="bi bi-calendar"></i> <?php echo e($task->deadline->format('M d, Y')); ?>

                            <?php endif; ?>
                            <span class="ms-2"><i class="bi bi-arrow-repeat"></i> <?php echo e(str_replace('_',' ', ucfirst($task->frequency))); ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-journal-plus fs-1 d-block mb-2"></i>
        No personal tasks yet. Create one!
    </div>
    <?php endif; ?>
</div>

<?php if($tasks->hasPages()): ?>
<div class="mt-4"><?php echo e($tasks->links('vendor.pagination.bootstrap-5')); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/personal-tasks/index.blade.php ENDPATH**/ ?>