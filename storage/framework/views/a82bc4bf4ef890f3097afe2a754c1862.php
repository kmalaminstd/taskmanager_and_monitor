<?php $__env->startSection('title', 'Workload'); ?>
<?php $__env->startSection('page-title', 'Employee Workload'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">High Workload</div>
            <div class="fs-3 fw-bold text-danger"><?php echo e($summary['high']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Normal</div>
            <div class="fs-3 fw-bold text-warning"><?php echo e($summary['normal']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Low / Available</div>
            <div class="fs-3 fw-bold text-success"><?php echo e($summary['low']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Active Tasks</div>
            <div class="fs-3 fw-bold"><?php echo e($summary['total_active']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Overdue</div>
            <div class="fs-3 fw-bold text-danger"><?php echo e($summary['total_overdue']); ?></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Active Tasks</th>
                    <th>Overdue</th>
                    <th>Workload</th>
                    <th width="120"></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $workloads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="fw-medium"><?php echo e($row['user']->name); ?></td>
                    <td><?php echo e($row['active_tasks']); ?></td>
                    <td>
                        <?php if($row['overdue_tasks'] > 0): ?>
                            <span class="text-danger fw-semibold"><?php echo e($row['overdue_tasks']); ?></span>
                        <?php else: ?>
                            0
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo e($row['color']); ?>"><?php echo e($row['label']); ?></span>
                        <div class="progress mt-1" style="height:6px;width:100px;">
                            <?php
                                $pct = min(100, ($row['active_tasks'] / 12) * 100);
                            ?>
                            <div class="progress-bar bg-<?php echo e($row['color']); ?>" style="width:<?php echo e($pct); ?>%"></div>
                        </div>
                    </td>
                    <td>
                        <a href="<?php echo e(route('admin.workload.show', $row['user'])); ?>" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/workload/index.blade.php ENDPATH**/ ?>