<?php $__env->startSection('title', 'My Performance'); ?>
<?php $__env->startSection('page-title', 'My Performance'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.period-filter', ['period' => $period ?? request('period')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e($from?->format('Y-m-d')); ?>">
    </div>
    <div class="col-auto">
        <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e($to?->format('Y-m-d')); ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Tasks</div>
            <div class="fs-2 fw-bold"><?php echo e($stats['assigned'] ?? $stats['total']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-2 fw-bold text-success"><?php echo e($stats['completed']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completion Rate</div>
            <div class="fs-2 fw-bold"><?php echo e($stats['completion_rate']); ?>%</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">On-Time Rate</div>
            <div class="fs-2 fw-bold"><?php echo e($stats['on_time_rate']); ?>%</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['pending']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['in_progress']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-4 fw-bold text-danger"><?php echo e($stats['overdue']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Late Completed</div>
            <div class="fs-4 fw-bold"><?php echo e($stats['late']); ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">My Status Distribution</div>
            <div class="card-body"><canvas id="statusChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Completion Trend</div>
            <div class="card-body"><canvas id="trendChart" height="220"></canvas></div>
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
        labels: Object.keys(statusData).map(s => s.replace('_',' ')),
        datasets: [{ data: Object.values(statusData), backgroundColor: ['#6366F1','#3B82F6','#F59E0B','#10B981','#EF4444','#94A3B8'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: trendData.labels,
        datasets: [{ label: 'Completed', data: trendData.data, backgroundColor: '#2563EB' }]
    },
    options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/performance/index.blade.php ENDPATH**/ ?>