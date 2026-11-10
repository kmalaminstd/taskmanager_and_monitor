<?php $__env->startSection('title', 'My Dashboard'); ?>
<?php $__env->startSection('page-title', 'My Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="text-muted small">Assigned</div>
                    <div class="fs-4 fw-bold"><?php echo e($stats['total_assigned']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass"></i></div>
                <div>
                    <div class="text-muted small">Pending</div>
                    <div class="fs-4 fw-bold"><?php echo e($stats['pending']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-play-circle"></i></div>
                <div>
                    <div class="text-muted small">In Progress</div>
                    <div class="fs-4 fw-bold"><?php echo e($stats['in_progress']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Completed</div>
                    <div class="fs-4 fw-bold"><?php echo e($stats['completed']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-semibold"><i class="bi bi-bell me-1 text-warning"></i> Today's Reminders</h6>
    <span class="badge bg-primary"><?php echo e($recurringDone); ?>/<?php echo e($recurringTotal); ?> completed this period</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-briefcase me-1"></i> Assigned Tasks</span>
                <span class="badge bg-primary"><?php echo e($reminderData['assigned']->count()); ?></span>
            </div>
            <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $reminderData['assigned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-group-item <?php echo e(!empty($item['done_this_period']) ? 'opacity-75' : ''); ?>">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <a href="<?php echo e($item['url']); ?>" class="fw-medium text-decoration-none"><?php echo e($item['title']); ?></a>
                            <div class="small text-muted">
                                <?php switch($item['reason']):
                                    case ('daily'): ?> Daily work <?php break; ?>
                                    <?php case ('weekly_day'): ?>
                                    <?php case ('weekly_default'): ?> Weekly work <?php break; ?>
                                    <?php case ('monthly_day'): ?>
                                    <?php case ('monthly_default'): ?> Monthly work <?php break; ?>
                                    <?php case ('deadline_today'): ?> <span class="text-danger">Deadline today</span> <?php break; ?>
                                    <?php case ('deadline_soon'): ?> Deadline approaching <?php break; ?>
                                    <?php case ('overdue'): ?> <span class="text-danger">Overdue</span> <?php break; ?>
                                    <?php default: ?> Reminder
                                <?php endswitch; ?>
                                · <?php echo e(ucfirst(str_replace('_',' ', $item['frequency']))); ?>

                                <?php if($item['deadline']): ?> · Due <?php echo e(\Carbon\Carbon::parse($item['deadline'])->format('M d')); ?> <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <?php if(!empty($item['done_this_period'])): ?>
                                <span class="badge bg-success">Done</span>
                            <?php elseif(in_array($item['frequency'], ['daily','weekly','monthly'])): ?>
                                <form method="POST" action="<?php echo e(route('assignee.reminders.complete')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="assigned">
                                    <input type="hidden" name="id" value="<?php echo e($item['source']->id); ?>">
                                    <input type="hidden" name="frequency" value="<?php echo e($item['frequency']); ?>">
                                    <button class="btn btn-sm btn-outline-success">Mark done</button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo e($item['url']); ?>" class="btn btn-sm btn-outline-primary">Open</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center text-muted py-4">No assigned reminders today</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-plus me-1"></i> Personal Tasks</span>
                <span class="badge bg-secondary"><?php echo e($reminderData['personal']->count()); ?></span>
            </div>
            <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $reminderData['personal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-group-item <?php echo e(!empty($item['done_this_period']) ? 'opacity-75' : ''); ?>">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <a href="<?php echo e($item['url']); ?>" class="fw-medium text-decoration-none"><?php echo e($item['title']); ?></a>
                            <div class="small text-muted">
                                <?php switch($item['reason']):
                                    case ('daily'): ?> Daily work <?php break; ?>
                                    <?php case ('weekly_day'): ?>
                                    <?php case ('weekly_default'): ?> Weekly work <?php break; ?>
                                    <?php case ('monthly_day'): ?>
                                    <?php case ('monthly_default'): ?> Monthly work <?php break; ?>
                                    <?php case ('deadline_today'): ?> <span class="text-danger">Deadline today</span> <?php break; ?>
                                    <?php case ('deadline_soon'): ?> Deadline approaching (1 day before) <?php break; ?>
                                    <?php case ('overdue'): ?> <span class="text-danger">Overdue</span> <?php break; ?>
                                    <?php default: ?> Reminder
                                <?php endswitch; ?>
                                · <?php echo e(ucfirst(str_replace('_',' ', $item['frequency']))); ?>

                                <?php if($item['deadline']): ?> · Due <?php echo e(\Carbon\Carbon::parse($item['deadline'])->format('M d')); ?> <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <?php if(!empty($item['done_this_period'])): ?>
                                <span class="badge bg-success">Done</span>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('assignee.reminders.complete')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="personal">
                                    <input type="hidden" name="id" value="<?php echo e($item['source']->id); ?>">
                                    <input type="hidden" name="frequency" value="<?php echo e($item['frequency']); ?>">
                                    <button class="btn btn-sm btn-outline-success">Mark done</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center text-muted py-4">No personal reminders today</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Recent Assigned Tasks</div>
            <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $recentAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-medium"><?php echo e($assignment->task->title ?? '—'); ?></div>
                            <small class="text-muted">
                                Assigned by <?php echo e($assignment->assignedBy->name ?? '—'); ?> ·
                                <?php echo e($assignment->assigned_at?->diffForHumans()); ?>

                            </small>
                        </div>
                        <span class="badge bg-<?php echo e($assignment->status === 'completed' ? 'success' : ($assignment->status === 'pending' ? 'secondary' : 'primary')); ?>">
                            <?php echo e(str_replace('_', ' ', ucfirst($assignment->status))); ?>

                        </span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center text-muted py-4">No assigned tasks yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-calendar-event me-1"></i> Upcoming Deadlines
            </div>
            <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $upcomingDeadlines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-medium"><?php echo e(Str::limit($assignment->task->title ?? '—', 30)); ?></div>
                        <small class="text-muted"><?php echo e(ucfirst($assignment->status)); ?></small>
                    </div>
                    <div class="text-end">
                        <div class="small fw-medium"><?php echo e($assignment->deadline?->format('M d')); ?></div>
                        <small class="text-muted"><?php echo e($assignment->deadline?->diffForHumans()); ?></small>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center text-muted py-4">No upcoming deadlines</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/dashboard.blade.php ENDPATH**/ ?>