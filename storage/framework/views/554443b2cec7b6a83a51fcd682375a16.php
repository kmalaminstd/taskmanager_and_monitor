<?php $__env->startSection('title', 'Transfer Task'); ?>
<?php $__env->startSection('page-title', 'Direct Task Transfer'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-1"><?php echo e($task->title); ?></h5>
                <p class="text-muted mb-0">Admin Direct Transfer — no approval required</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Transfer Details</div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label text-muted">Current Active Assignees</label>
                    <div>
                        <?php $__empty_1 = true; $__currentLoopData = $task->activeAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="badge bg-primary me-1 mb-1"><?php echo e($a->assignee->name); ?> (<?php echo e(str_replace('_',' ', $a->status)); ?>)</span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="text-muted">None</span>
                        <?php endif; ?>
                    </div>
                </div>

                <form method="POST" action="<?php echo e(route('admin.transfers.store', $task)); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label">New Assignee(s) <span class="text-danger">*</span></label>
                        <select name="new_assignee_ids[]" class="form-select <?php $__errorArgs = ['new_assignee_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple size="7" required>
                            <?php $__currentLoopData = $assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->email); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple. These will become the new active assignees.</div>
                        <?php $__errorArgs = ['new_assignee_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="keep_existing" value="1" id="keep_existing" class="form-check-input"
                               <?php echo e(old('keep_existing') ? 'checked' : ''); ?>>
                        <label for="keep_existing" class="form-check-label">
                            Keep existing assignees (only add the selected ones)
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transfer Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('reason')); ?></textarea>
                        <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Additional Notes (optional)</label>
                        <textarea name="notes" rows="2" class="form-control"><?php echo e(old('notes')); ?></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Important:</strong> Previous assignees will be deactivated (history preserved). 
                        New assignees start with status <strong>Pending</strong>.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Confirm this transfer? This action cannot be undone.')">
                            <i class="bi bi-arrow-left-right"></i> Confirm Transfer
                        </button>
                        <a href="<?php echo e(route('admin.tasks.show', $task)); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/transfers/create.blade.php ENDPATH**/ ?>