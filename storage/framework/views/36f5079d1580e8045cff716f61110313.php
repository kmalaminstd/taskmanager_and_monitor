<?php $__env->startSection('title', 'Transfers'); ?>
<?php $__env->startSection('page-title', 'Transfer History'); ?>

<?php $__env->startSection('content'); ?>
<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Transfers</div>
            <div class="fs-3 fw-bold"><?php echo e($counts['total']); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Admin Direct</div>
            <div class="fs-3 fw-bold"><?php echo e($counts['admin_direct']); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Request Approved</div>
            <div class="fs-3 fw-bold"><?php echo e($counts['request_approved']); ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Transfer History -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Transfer History</span>
            </div>

            <!-- Filters -->
            <div class="card-body border-bottom">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search task..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="transfer_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="admin_direct" <?php if(request('transfer_type')==='admin_direct'): echo 'selected'; endif; ?>>Admin Direct</option>
                            <option value="request_approved" <?php if(request('transfer_type')==='request_approved'): echo 'selected'; endif; ?>>Request Approved</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex gap-1">
                        <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e(request('from')); ?>">
                        <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e(request('to')); ?>">
                        <button class="btn btn-sm btn-primary">Filter</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Type</th>
                            <th>By</th>
                            <th>Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('admin.tasks.show', $t->task_id)); ?>">
                                    <?php echo e(Str::limit($t->task->title ?? '—', 30)); ?>

                                </a>
                            </td>
                            <td><?php echo e($t->previousAssignee->name ?? '—'); ?></td>
                            <td class="fw-medium"><?php echo e($t->newAssignee->name ?? '—'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($t->transfer_type === 'admin_direct' ? 'primary' : 'info'); ?>">
                                    <?php echo e($t->transfer_type === 'admin_direct' ? 'Admin Direct' : 'Request'); ?>

                                </span>
                            </td>
                            <td><?php echo e($t->transferredBy->name ?? '—'); ?></td>
                            <td>
                                <small><?php echo e($t->transferred_at?->format('M d, Y')); ?></small>
                            </td>
                            <td><small><?php echo e(Str::limit($t->reason, 40)); ?></small></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No transfers recorded yet
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($transfers->hasPages()): ?>
            <div class="card-footer bg-white"><?php echo e($transfers->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Transfer -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Quick Transfer</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $activeTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-group-item">
                        <div class="fw-medium small mb-1"><?php echo e(Str::limit($task->title, 35)); ?></div>
                        <div class="mb-2">
                            <?php $__currentLoopData = $task->activeAssignments->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-light text-dark"><?php echo e($a->assignee->name ?? '—'); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <a href="<?php echo e(route('admin.transfers.create', $task)); ?>" class="btn btn-sm btn-warning w-100">
                            <i class="bi bi-arrow-left-right"></i> Transfer
                        </a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="list-group-item text-center text-muted py-4">
                        No active tasks available
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/transfers/index.blade.php ENDPATH**/ ?>