<?php $__env->startSection('title', $user->name . ' - Performance'); ?>
<?php $__env->startSection('page-title', $user->name . ' - Performance'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('admin.performance.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Performance
    </a>
</div>

<!-- Filters -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e($from?->format('Y-m-d')); ?>">
    </div>
    <div class="col-auto">
        <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e($to?->format('Y-m-d')); ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
        <a href="<?php echo e(route('admin.performance.show', $user)); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Assignments</div>
            <div class="fs-3 fw-bold"><?php echo e($stats['total']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-3 fw-bold text-success"><?php echo e($stats['completed']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completion Rate</div>
            <div class="fs-3 fw-bold"><?php echo e($stats['completion_rate']); ?>%</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">On-Time Rate</div>
            <div class="fs-3 fw-bold"><?php echo e($stats['on_time_rate']); ?>%</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['pending']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['in_progress']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Submitted</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['submitted']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Returned</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['returned']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-4 fw-bold text-danger"><?php echo e($stats['overdue']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Late Completed</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['late']); ?></div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Status Distribution</div>
            <div class="card-body">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Completion Trend (6 months)</div>
            <div class="card-body">
                <canvas id="trendChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const statusData = <?php echo json_encode($chartData['status'], 15, 512) ?>;
const trendData = <?php echo json_encode($chartData['trend'], 15, 512) ?>;

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.replace('_', ' ')),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#6366F1', '#3B82F6', '#F59E0B', '#10B981', '#EF4444', '#94A3B8']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.labels || [],
        datasets: [{
            label: 'Completed',
            data: trendData.data || [],
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/performance/show.blade.php ENDPATH**/ ?>