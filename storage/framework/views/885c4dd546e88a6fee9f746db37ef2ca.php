<?php $__env->startSection('title', 'Notifications'); ?>
<?php $__env->startSection('page-title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <?php if(auth()->user()->unreadNotifications->count()): ?>
    <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>">
        <?php echo csrf_field(); ?>
        <button class="btn btn-sm btn-outline-primary">Mark all as read</button>
    </form>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <ul class="list-group list-group-flush">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li class="list-group-item <?php echo e($notification->read_at ? '' : 'bg-light'); ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-medium">
                        <?php if(!$notification->read_at): ?>
                            <span class="badge bg-primary me-1">New</span>
                        <?php endif; ?>
                        <?php echo e($notification->data['title'] ?? 'Notification'); ?>

                    </div>
                    <div class="text-muted small"><?php echo e($notification->data['message'] ?? ''); ?></div>
                    <div class="text-muted small mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                </div>
                <div>
                    <?php if(!empty($notification->data['url'])): ?>
                        <form method="POST" action="<?php echo e(route('notifications.read', $notification->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-primary">
                                View
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('notifications.read', $notification->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-secondary">Mark read</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li class="list-group-item text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            No notifications yet
        </li>
        <?php endif; ?>
    </ul>
    <?php if($notifications->hasPages()): ?>
    <div class="card-footer bg-white"><?php echo e($notifications->links('vendor.pagination.bootstrap-5')); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isSupervisor() ? 'layouts.supervisor' : 'layouts.assignee'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/notifications/index.blade.php ENDPATH**/ ?>