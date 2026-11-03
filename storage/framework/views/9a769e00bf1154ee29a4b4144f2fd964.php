<?php $__env->startSection('title', 'Performance'); ?>
<?php $__env->startSection('page-title', 'Performance Monitoring'); ?>

<?php $__env->startSection('content'); ?>
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
        <a href="<?php echo e(route('admin.performance.index')); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Overall Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Total Assignments</div>
            <div class="fs-3 fw-bold"><?php echo e($overall['total']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Completed</div>
            <div class="fs-3 fw-bold text-success"><?php echo e($overall['completed']); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Completion Rate</div>
            <div class="fs-3 fw-bold"><?php echo e($overall['completion_rate']); ?>%</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">On-Time Rate</div>
            <div class="fs-3 fw-bold"><?php echo e($overall['on_time_rate']); ?>%</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
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

<!-- Employee Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Employee Performance</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Total</th>
                    <th>Completed</th>
                    <th>On-Time</th>
                    <th>Late</th>
                    <th>Overdue</th>
                    <th>Completion %</th>
                    <th>On-Time %</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="fw-medium"><?php echo e($row['user']->name); ?></td>
                    <td><?php echo e($row['stats']['total']); ?></td>
                    <td><?php echo e($row['stats']['completed']); ?></td>
                    <td><?php echo e($row['stats']['on_time']); ?></td>
                    <td><?php echo e($row['stats']['late']); ?></td>
                    <td>
                        <?php if($row['stats']['overdue'] > 0): ?>
                            <span class="text-danger fw-medium"><?php echo e($row['stats']['overdue']); ?></span>
                        <?php else: ?>
                            0
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="progress" style="height:8px;width:80px;display:inline-block;vertical-align:middle;">
                            <div class="progress-bar bg-success" style="width:<?php echo e($row['stats']['completion_rate']); ?>%"></div>
                        </div>
                        <?php echo e($row['stats']['completion_rate']); ?>%
                    </td>
                    <td><?php echo e($row['stats']['on_time_rate']); ?>%</td>
                    <td>
                        <a href="<?php echo e(route('admin.performance.show', $row['user'])); ?>" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
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
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#6366F1','#3B82F6','#F59E0B','#10B981','#EF4444','#94A3B8']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: [{
            label: 'Completed',
            data: trendData.data,
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/performance/index.blade.php ENDPATH**/ ?>