
<?php
    $canRemove = $canRemove ?? false;
    $assistances = $assistances ?? collect();
?>

<div class="card border-0 shadow-sm mb-4" id="task-assistance-list">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-1"></i> Assistance</span>
        <span class="badge bg-secondary"><?php echo e($assistances->count()); ?></span>
    </div>
    <div class="list-group list-group-flush">
        <?php $__empty_1 = true; $__currentLoopData = $assistances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                        <span class="badge bg-<?php echo e($req->status === 'completed' ? 'success' : ($req->status === 'accepted' ? 'info' : ($req->status === 'pending' ? 'warning' : 'secondary'))); ?>">
                            <?php echo e(ucfirst($req->status)); ?>

                        </span>
                        <small class="text-muted"><?php echo e($req->created_at?->format('M d, Y H:i')); ?></small>
                        <?php if($req->requester): ?>
                            <small class="text-muted">· For: <?php echo e($req->requester->name); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="small mb-2"><?php echo e($req->description); ?></div>
                    <div class="small">
                        <strong>Helpers:</strong>
                        <?php $__empty_2 = true; $__currentLoopData = $req->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $helper): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <span class="badge bg-light text-dark border">
                                <?php echo e($helper->name); ?>

                                <span class="text-muted">(<?php echo e($helper->pivot->status ?? 'pending'); ?>)</span>
                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex flex-column gap-1">
                    <?php if(auth()->user()->isAdmin() || auth()->user()->isAssignee()): ?>
                        <?php
                            $detailRoute = auth()->user()->isAdmin()
                                ? route('admin.assistance.show', $req)
                                : route('assignee.assistance.show', $req);
                        ?>
                        <a href="<?php echo e($detailRoute); ?>" class="btn btn-sm btn-outline-primary">View</a>
                    <?php endif; ?>
                    <?php if($canRemove && auth()->user()->isAdmin()): ?>
                        <form method="POST" action="<?php echo e(route('admin.assistance.destroy', $req)); ?>"
                              onsubmit="return confirm('Remove this assistance request?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="list-group-item text-center text-muted py-4">No assistance on this task yet</div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/components/task-assistance-list.blade.php ENDPATH**/ ?>