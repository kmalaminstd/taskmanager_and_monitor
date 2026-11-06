<?php $__env->startSection('title', 'Assistance Details'); ?>
<?php $__env->startSection('page-title', 'Assistance Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('assignee.assistance.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="mb-0"><?php echo e($assistance->task->title ?? '—'); ?></h5>
                    <span class="badge bg-<?php echo e($assistance->status === 'pending' ? 'warning' : ($assistance->status === 'accepted' ? 'success' : ($assistance->status === 'completed' ? 'primary' : 'secondary'))); ?>">
                        <?php echo e(ucfirst($assistance->status)); ?>

                    </span>
                </div>
                <p class="text-muted mb-3"><?php echo e($assistance->description); ?></p>
                <div class="small text-muted">
                    <div><strong>Requested by:</strong> <?php echo e($assistance->requester->name ?? '—'); ?></div>
                    <div><strong>Date:</strong> <?php echo e($assistance->created_at->format('M d, Y H:i')); ?></div>
                </div>
            </div>
        </div>

        <?php if($isHelper && $myPivotStatus === 'pending'): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Your Response</div>
            <div class="card-body d-flex gap-2">
                <form method="POST" action="<?php echo e(route('assignee.assistance.respond', $assistance)); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="accepted">
                    <button class="btn btn-success"><i class="bi bi-check-lg"></i> Accept</button>
                </form>
                <form method="POST" action="<?php echo e(route('assignee.assistance.respond', $assistance)); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="declined">
                    <button class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> Decline</button>
                </form>
            </div>
        </div>
        <?php elseif($isHelper): ?>
        <div class="alert alert-info">
            You have <strong><?php echo e($myPivotStatus); ?></strong> this request.
        </div>
        <?php endif; ?>

        <?php if($isRequester && in_array($assistance->status, ['accepted', 'pending'])): ?>
        <form method="POST" action="<?php echo e(route('assignee.assistance.complete', $assistance)); ?>" class="mb-4">
            <?php echo csrf_field(); ?>
            <button class="btn btn-primary" onclick="return confirm('Mark assistance as completed?')">
                <i class="bi bi-flag"></i> Mark Assistance Completed
            </button>
        </form>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Helpers</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $assistance->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $helper): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?php echo e($helper->name); ?></span>
                    <span class="badge bg-<?php echo e($helper->pivot->status === 'accepted' ? 'success' : ($helper->pivot->status === 'declined' ? 'danger' : 'warning')); ?>">
                        <?php echo e(ucfirst($helper->pivot->status)); ?>

                    </span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/assistance/show.blade.php ENDPATH**/ ?>