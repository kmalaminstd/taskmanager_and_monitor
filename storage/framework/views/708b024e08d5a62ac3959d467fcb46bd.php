<?php $__env->startSection('title', $task->title); ?>
<?php $__env->startSection('page-title', 'Task Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="<?php echo e(route('admin.tasks.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="<?php echo e(route('admin.tasks.edit', $task)); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="<?php echo e(route('admin.transfers.create', $task)); ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-arrow-left-right"></i> Transfer
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-1"><?php echo e($task->title); ?></h4>
                <div class="mb-3">
                    <span class="badge badge-priority-<?php echo e($task->priority); ?>"><?php echo e(ucfirst($task->priority)); ?></span>
                    <?php if($task->category): ?>
                        <span class="badge bg-light text-dark"><?php echo e($task->category->name); ?></span>
                    <?php endif; ?>
                    <span class="badge bg-secondary"><?php echo e(str_replace('_', ' ', ucfirst($task->task_type))); ?></span>
                </div>

                <?php if($task->description): ?>
                    <p class="text-muted"><?php echo e($task->description); ?></p>
                <?php endif; ?>

                <?php if($task->instructions): ?>
                    <div class="alert alert-light border">
                        <strong>Instructions:</strong><br>
                        <?php echo e($task->instructions); ?>

                    </div>
                <?php endif; ?>

                <div class="row text-muted small">
                    <div class="col-sm-4"><strong>Created by:</strong> <?php echo e($task->creator->name ?? '—'); ?></div>
                    <div class="col-sm-4"><strong>Deadline:</strong> <?php echo e($task->deadline?->format('M d, Y H:i') ?? '—'); ?></div>
                    <div class="col-sm-4"><strong>Created:</strong> <?php echo e($task->created_at->format('M d, Y')); ?></div>
                </div>
            </div>
        </div>

        <!-- Assignments -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Assignments (<?php echo e($task->assignments->where('is_active', true)->count()); ?> active)</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Submitted</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $task->assignments->sortByDesc('is_active'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e(!$assignment->is_active ? 'table-secondary' : ''); ?>">
                            <td>
                                <?php echo e($assignment->assignee->name ?? '—'); ?>

                                <?php if(!$assignment->is_active): ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-status-<?php echo e($assignment->status); ?>">
                                    <?php echo e(str_replace('_', ' ', ucfirst($assignment->status))); ?>

                                </span>
                                <?php if($assignment->isOverdue()): ?>
                                    <span class="badge bg-danger">Overdue</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($assignment->started_at?->format('M d, H:i') ?? '—'); ?></td>
                            <td><?php echo e($assignment->submitted_at?->format('M d, H:i') ?? '—'); ?></td>
                            <td><?php echo e($assignment->deadline?->format('M d, Y') ?? '—'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History -->
        <?php if($task->histories->count()): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Activity History</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $task->histories->sortByDesc('created_at')->take(15); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong><?php echo e(ucfirst(str_replace('_', ' ', $history->action))); ?></strong>
                            <?php if($history->description): ?>
                                — <?php echo e($history->description); ?>

                            <?php endif; ?>
                            <br>
                            <small class="text-muted">by <?php echo e($history->user->name ?? 'System'); ?></small>
                        </div>
                        <small class="text-muted"><?php echo e($history->created_at->diffForHumans()); ?></small>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <?php if($task->attachments->count()): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Attachments</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $task->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-paperclip"></i> <?php echo e($file->original_name); ?></span>
                    <small class="text-muted"><?php echo e(number_format($file->file_size / 1024, 1)); ?> KB</small>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if($task->transferHistories->count()): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Transfer History</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $task->transferHistories->sortByDesc('transferred_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item small">
                    <div>
                        <?php echo e($transfer->previousAssignee->name ?? '—'); ?>

                        <i class="bi bi-arrow-right"></i>
                        <?php echo e($transfer->newAssignee->name ?? '—'); ?>

                    </div>
                    <div class="text-muted">
                        <?php echo e($transfer->transfer_type === 'admin_direct' ? 'Admin Direct' : 'Request Approved'); ?>

                        · <?php echo e($transfer->transferred_at->format('M d, Y')); ?>

                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/tasks/show.blade.php ENDPATH**/ ?>