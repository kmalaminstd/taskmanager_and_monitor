<?php $__env->startSection('title', 'Assistance'); ?>
<?php $__env->startSection('page-title', 'Assistance Requests'); ?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#incoming">Incoming</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#outgoing">My Requests</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="incoming">
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $incoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('assignee.assistance.show', $req)); ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-medium"><?php echo e($req->task->title ?? '—'); ?></div>
                            <small class="text-muted">From <?php echo e($req->requester->name ?? '—'); ?> · <?php echo e($req->created_at->diffForHumans()); ?></small>
                        </div>
                        <span class="badge bg-<?php echo e($req->status === 'pending' ? 'warning' : ($req->status === 'accepted' ? 'success' : 'secondary')); ?>">
                            <?php echo e(ucfirst($req->status)); ?>

                        </span>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center text-muted py-4">No incoming assistance requests</div>
                <?php endif; ?>
            </div>
            <?php if($incoming->hasPages()): ?>
            <div class="card-footer bg-white"><?php echo e($incoming->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="tab-pane fade" id="outgoing">
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $outgoing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('assignee.assistance.show', $req)); ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-medium"><?php echo e($req->task->title ?? '—'); ?></div>
                            <small class="text-muted">
                                Helpers: <?php echo e($req->users->pluck('name')->join(', ') ?: '—'); ?>

                                · <?php echo e($req->created_at->diffForHumans()); ?>

                            </small>
                        </div>
                        <span class="badge bg-<?php echo e($req->status === 'pending' ? 'warning' : ($req->status === 'accepted' ? 'success' : 'secondary')); ?>">
                            <?php echo e(ucfirst($req->status)); ?>

                        </span>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-center text-muted py-4">You have not requested assistance yet</div>
                <?php endif; ?>
            </div>
            <?php if($outgoing->hasPages()): ?>
            <div class="card-footer bg-white"><?php echo e($outgoing->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/assistance/index.blade.php ENDPATH**/ ?>