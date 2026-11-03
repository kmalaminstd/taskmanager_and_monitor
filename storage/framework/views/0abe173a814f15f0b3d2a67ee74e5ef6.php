<?php $__env->startSection('title', 'Report'); ?>
<?php $__env->startSection('page-title', ucfirst(str_replace('_', ' ', $type)) . ' Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <a href="<?php echo e(route('admin.reports.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> All Reports
    </a>
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="hidden" name="type" value="<?php echo e($type); ?>">
        <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e($from->format('Y-m-d')); ?>">
        <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e($to->format('Y-m-d')); ?>">
        <?php if(in_array($type, ['employee','completed','overdue','late'])): ?>
        <select name="user_id" class="form-select form-select-sm">
            <option value="">All Employees</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($u->id); ?>" <?php if($userId == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php endif; ?>
        <button class="btn btn-sm btn-primary">Apply</button>
        <a href="<?php echo e(route('admin.reports.generate', request()->all() + ['export' => 'csv'])); ?>" class="btn btn-sm btn-success">
            <i class="bi bi-download"></i> CSV
        </a>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if($type === 'overall'): ?>
            <div class="row g-3">
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="text-muted small"><?php echo e(str_replace('_', ' ', ucfirst($key))); ?></div>
                        <div class="fs-3 fw-bold"><?php echo e($value); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php elseif(in_array($type, ['employee', 'completed', 'overdue', 'late'])): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(Str::limit($row->task->title ?? '—', 40)); ?></td>
                            <td><?php echo e($row->assignee->name ?? '—'); ?></td>
                            <td><span class="badge badge-status-<?php echo e($row->status); ?>"><?php echo e($row->status); ?></span></td>
                            <td><?php echo e($row->deadline?->format('M d, Y')); ?></td>
                            <td><?php echo e($row->submitted_at?->format('M d, Y H:i') ?? '—'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif($type === 'transfers'): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Task</th><th>From</th><th>To</th><th>Type</th><th>By</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(Str::limit($row->task->title ?? '—', 30)); ?></td>
                            <td><?php echo e($row->previousAssignee->name ?? '—'); ?></td>
                            <td><?php echo e($row->newAssignee->name ?? '—'); ?></td>
                            <td><?php echo e($row->transfer_type); ?></td>
                            <td><?php echo e($row->transferredBy->name ?? '—'); ?></td>
                            <td><?php echo e($row->transferred_at?->format('M d, Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No transfers</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif($type === 'extensions'): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Task</th><th>Requester</th><th>Current</th><th>Requested</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(Str::limit($row->assignment->task->title ?? '—', 30)); ?></td>
                            <td><?php echo e($row->requester->name ?? '—'); ?></td>
                            <td><?php echo e($row->current_deadline?->format('M d, Y')); ?></td>
                            <td><?php echo e($row->requested_deadline?->format('M d, Y')); ?></td>
                            <td><span class="badge bg-<?php echo e($row->status === 'approved' ? 'success' : ($row->status === 'rejected' ? 'danger' : 'warning')); ?>"><?php echo e($row->status); ?></span></td>
                            <td><?php echo e($row->created_at->format('M d, Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No extension requests</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/reports/show.blade.php ENDPATH**/ ?>