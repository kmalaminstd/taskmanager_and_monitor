<?php $__env->startSection('title', 'Calendar'); ?>
<?php $__env->startSection('page-title', 'Task Calendar'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group">
        <a href="<?php echo e(route('calendar.index', ['month' => $prevMonth])); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-left"></i>
        </a>
        <a href="<?php echo e(route('calendar.index', ['month' => now()->format('Y-m')])); ?>" class="btn btn-outline-secondary btn-sm">Today</a>
        <a href="<?php echo e(route('calendar.index', ['month' => $nextMonth])); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>
    <h5 class="mb-0"><?php echo e(\Carbon\Carbon::parse($month)->format('F Y')); ?></h5>
    <div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered mb-0 calendar-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Sun</th>
                    <th class="text-center">Mon</th>
                    <th class="text-center">Tue</th>
                    <th class="text-center">Wed</th>
                    <th class="text-center">Thu</th>
                    <th class="text-center">Fri</th>
                    <th class="text-center">Sat</th>
                </tr>
            </thead>
            <tbody>
                <?php $chunks = collect($days)->chunk(7); ?>
                <?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php $__currentLoopData = $week; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateStr => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="calendar-day <?php echo e(!$day['isCurrentMonth'] ? 'bg-light text-muted' : ''); ?> <?php echo e($day['isToday'] ? 'today' : ''); ?>" style="height:110px;vertical-align:top;width:14.28%;">
                        <div class="fw-medium small mb-1"><?php echo e($day['date']->day); ?></div>
                        <?php $__currentLoopData = $day['assignments']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="badge bg-primary text-truncate d-block mb-1" style="max-width:100%;font-size:0.65rem;" title="<?php echo e($a->task->title ?? ''); ?>">
                                <?php echo e(Str::limit($a->task->title ?? '', 18)); ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $day['personal']->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="badge bg-secondary text-truncate d-block mb-1" style="max-width:100%;font-size:0.65rem;" title="<?php echo e($p->title); ?>">
                                <?php echo e(Str::limit($p->title, 18)); ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($day['assignments']->count() + $day['personal']->count() > 5): ?>
                            <small class="text-muted">+ more</small>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .calendar-day.today { background: #EFF6FF !important; }
    .calendar-table td { padding: 0.4rem; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /run/media/k-m-al-amin/New Volume/TaskMasterTest/Three_grok/taskmaster/resources/views/calendar/index.blade.php ENDPATH**/ ?>