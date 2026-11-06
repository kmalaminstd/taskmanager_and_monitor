<?php $__env->startSection('title', 'Approvals'); ?>
<?php $__env->startSection('page-title', 'Approval Management'); ?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#submissions" type="button">
            Submissions <span class="badge bg-primary"><?php echo e($pendingSubmissions->count()); ?></span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#extensions" type="button">
            Extensions <span class="badge bg-warning text-dark"><?php echo e($pendingExtensions->count()); ?></span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transfers" type="button">
            Transfers <span class="badge bg-info"><?php echo e($pendingTransfers->count()); ?></span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Submissions -->
    <div class="tab-pane fade show active" id="submissions">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pending Task Submissions</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Submitted</th>
                            <th>Details</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('admin.tasks.show', $submission->assignment->task)); ?>">
                                    <?php echo e(Str::limit($submission->assignment->task->title ?? '—', 40)); ?>

                                </a>
                            </td>
                            <td><?php echo e($submission->assignment->assignee->name ?? '—'); ?></td>
                            <td><?php echo e($submission->created_at->format('M d, H:i')); ?></td>
                            <td>
                                <small><?php echo e(Str::limit($submission->completion_details, 60)); ?></small>
                            </td>
                            <td>
                                <form method="POST" action="<?php echo e(route('admin.approvals.submissions.approve', $submission)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this submission?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#returnModal<?php echo e($submission->id); ?>">
                                    <i class="bi bi-arrow-return-left"></i> Return
                                </button>

                                <!-- Return Modal -->
                                <div class="modal fade" id="returnModal<?php echo e($submission->id); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="<?php echo e(route('admin.approvals.submissions.return', $submission)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Return Submission</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label">Return Note <span class="text-danger">*</span></label>
                                                    <textarea name="return_note" class="form-control" rows="3" required placeholder="Explain what needs to be fixed..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Return to Assignee</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No pending submissions</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Extensions -->
    <div class="tab-pane fade" id="extensions">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pending Extension Requests</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Requester</th>
                            <th>Current</th>
                            <th>Requested</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingExtensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ext): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(Str::limit($ext->assignment->task->title ?? '—', 30)); ?></td>
                            <td><?php echo e($ext->requester->name ?? '—'); ?></td>
                            <td><?php echo e($ext->current_deadline?->format('M d, Y')); ?></td>
                            <td class="fw-medium"><?php echo e($ext->requested_deadline?->format('M d, Y')); ?></td>
                            <td><small><?php echo e(Str::limit($ext->reason, 50)); ?></small></td>
                            <td>
                                <form method="POST" action="<?php echo e(route('admin.approvals.extensions.approve', $ext)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-success" onclick="return confirm('Approve extension?')">Approve</button>
                                </form>
                                <form method="POST" action="<?php echo e(route('admin.approvals.extensions.reject', $ext)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject extension?')">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No pending extension requests</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfers -->
    <div class="tab-pane fade" id="transfers">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pending Transfer Requests</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingTransfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(Str::limit($tr->assignment->task->title ?? '—', 30)); ?></td>
                            <td><?php echo e($tr->requester->name ?? '—'); ?></td>
                            <td><?php echo e($tr->newAssignee->name ?? '—'); ?></td>
                            <td><small><?php echo e(Str::limit($tr->reason, 50)); ?></small></td>
                            <td>
                                <form method="POST" action="<?php echo e(route('admin.approvals.transfers.approve', $tr)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-success" onclick="return confirm('Approve transfer?')">Approve</button>
                                </form>
                                <form method="POST" action="<?php echo e(route('admin.approvals.transfers.reject', $tr)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject transfer?')">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No pending transfer requests</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/approvals/index.blade.php ENDPATH**/ ?>