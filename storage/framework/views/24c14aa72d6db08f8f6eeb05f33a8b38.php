<?php $__env->startSection('title', 'Assignment Details'); ?>
<?php $__env->startSection('page-title', 'Assignment Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('admin.assignments.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Assignments
    </a>
    <a href="<?php echo e(route('admin.tasks.show', $assignment->task)); ?>" class="btn btn-outline-primary btn-sm">
        View Parent Task
    </a>
    <a href="<?php echo e(route('admin.transfers.create', $assignment->task)); ?>" class="btn btn-warning btn-sm">
        <i class="bi bi-arrow-left-right"></i> Transfer
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h4><?php echo e($assignment->task->title); ?></h4>
                <div class="mb-3">
                    <span class="badge badge-status-<?php echo e($assignment->status); ?>">
                        <?php echo e(str_replace('_', ' ', ucfirst($assignment->status))); ?>

                    </span>
                    <span class="badge badge-priority-<?php echo e($assignment->effective_priority); ?>">
                        <?php echo e(ucfirst($assignment->effective_priority)); ?>

                    </span>
                    <?php if($assignment->isOverdue()): ?>
                        <span class="badge bg-danger">Overdue</span>
                    <?php endif; ?>
                    <?php if(!$assignment->is_active): ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>

                <?php if($assignment->task->description): ?>
                    <p class="text-muted"><?php echo e($assignment->task->description); ?></p>
                <?php endif; ?>

                <div class="row small">
                    <div class="col-sm-6">
                        <strong>Assignee:</strong> <?php echo e($assignment->assignee->name ?? '—'); ?><br>
                        <strong>Assigned By:</strong> <?php echo e($assignment->assignedBy->name ?? '—'); ?><br>
                        <strong>Assigned At:</strong> <?php echo e($assignment->assigned_at?->format('M d, Y H:i')); ?>

                    </div>
                    <div class="col-sm-6">
                        <strong>Deadline:</strong> <?php echo e($assignment->deadline?->format('M d, Y') ?? '—'); ?><br>
                        <strong>Started:</strong> <?php echo e($assignment->started_at?->format('M d, Y H:i') ?? '—'); ?><br>
                        <strong>Submitted:</strong> <?php echo e($assignment->submitted_at?->format('M d, Y H:i') ?? '—'); ?>

                    </div>
                </div>
            </div>
        </div>

        <?php if($assignment->submissions->count()): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Submissions</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $assignment->submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-<?php echo e($sub->status === 'approved' ? 'success' : ($sub->status === 'returned' ? 'danger' : 'warning')); ?>">
                                <?php echo e(ucfirst($sub->status)); ?>

                            </span>
                            <div class="mt-1"><?php echo e($sub->completion_details); ?></div>
                            <?php if($sub->return_note): ?>
                                <div class="text-danger small mt-1">Return note: <?php echo e($sub->return_note); ?></div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?php echo e($sub->created_at->format('M d, H:i')); ?></small>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if($assignment->histories->count()): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Activity History</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $assignment->histories->sortByDesc('created_at')->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item small">
                    <strong><?php echo e(ucfirst(str_replace('_',' ',$h->action))); ?></strong>
                    <?php if($h->description): ?> — <?php echo e($h->description); ?> <?php endif; ?>
                    <br>
                    <span class="text-muted"><?php echo e($h->user->name ?? 'System'); ?> · <?php echo e($h->created_at->diffForHumans()); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <?php if($assignment->notes->count()): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Notes</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $assignment->notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item small">
                    <div><?php echo e($note->note); ?></div>
                    <div class="text-muted"><?php echo e($note->user->name ?? '—'); ?> · <?php echo e($note->created_at->diffForHumans()); ?></div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/assignments/show.blade.php ENDPATH**/ ?>