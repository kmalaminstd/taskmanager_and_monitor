<?php $__env->startSection('title', 'Assistance Details'); ?>
<?php $__env->startSection('page-title', 'Assistance Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('admin.assistance.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5><?php echo e($assistance->task->title ?? '—'); ?></h5>
                <span class="badge bg-secondary mb-3"><?php echo e(ucfirst($assistance->status)); ?></span>
                <p><?php echo e($assistance->description); ?></p>
                <div class="small text-muted">
                    Requester: <?php echo e($assistance->requester->name ?? '—'); ?> ·
                    <?php echo e($assistance->created_at->format('M d, Y H:i')); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Helpers</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $assistance->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $helper): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between">
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/assistance/show.blade.php ENDPATH**/ ?>