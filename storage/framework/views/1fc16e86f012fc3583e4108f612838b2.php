<?php $__env->startSection('title', 'Reports'); ?>
<?php $__env->startSection('page-title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <?php
    $reports = [
        ['type' => 'overall', 'title' => 'Overall Task Report', 'icon' => 'bi-bar-chart', 'desc' => 'High-level summary of all tasks'],
        ['type' => 'employee', 'title' => 'Employee Performance', 'icon' => 'bi-people', 'desc' => 'Detailed assignments per employee'],
        ['type' => 'completed', 'title' => 'Completed Tasks', 'icon' => 'bi-check-circle', 'desc' => 'All completed assignments'],
        ['type' => 'overdue', 'title' => 'Overdue Tasks', 'icon' => 'bi-exclamation-triangle', 'desc' => 'Currently overdue assignments'],
        ['type' => 'late', 'title' => 'Late Completions', 'icon' => 'bi-clock-history', 'desc' => 'Tasks completed after deadline'],
        ['type' => 'transfers', 'title' => 'Transfer History', 'icon' => 'bi-arrow-left-right', 'desc' => 'All task transfers'],
        ['type' => 'extensions', 'title' => 'Extension Requests', 'icon' => 'bi-calendar-plus', 'desc' => 'Deadline extension history'],
    ];
    ?>

    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="icon bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi <?php echo e($report['icon']); ?> fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0"><?php echo e($report['title']); ?></h6>
                        <small class="text-muted"><?php echo e($report['desc']); ?></small>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.reports.generate', ['type' => $report['type']])); ?>" class="btn btn-outline-primary btn-sm w-100">
                    Generate Report
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/reports/index.blade.php ENDPATH**/ ?>