<?php $__env->startSection('title', 'Create Task'); ?>
<?php $__env->startSection('page-title', 'Create New Task'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Task Details</div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.tasks.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label">Task Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('title')); ?>" required>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="high" <?php if(old('priority') === 'high'): echo 'selected'; endif; ?>>High</option>
                                <option value="medium" <?php if(old('priority', 'medium') === 'medium'): echo 'selected'; endif; ?>>Medium</option>
                                <option value="low" <?php if(old('priority') === 'low'): echo 'selected'; endif; ?>>Low</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select select2-enable" data-placeholder="Select category">
                                <option value="">— None —</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>" <?php if(old('category_id') == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control <?php $__errorArgs = ['deadline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('deadline')); ?>" min="<?php echo e(date('Y-m-d')); ?>">
                            <?php $__errorArgs = ['deadline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Task Type <span class="text-danger">*</span></label>
                            <select name="task_type" id="task_type" class="form-select" required>
                                <option value="one_time" <?php if(old('task_type', 'one_time') === 'one_time'): echo 'selected'; endif; ?>>One-time (single task)</option>
                                <option value="recurring" <?php if(old('task_type') === 'recurring'): echo 'selected'; endif; ?>>Recurring (daily / weekly / monthly)</option>
                            </select>
                            <div class="form-text">Choose <strong>One-time</strong> for a single assignment, or <strong>Recurring</strong> for repeating work.</div>
                        </div>
                        <div class="col-md-6 mb-3" id="frequency_wrapper" style="display:none;">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" id="frequency" class="form-select">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="weekday_wrap" style="display:none;">
                            <label class="form-label">Day of week <span class="text-muted small">(optional)</span></label>
                            <select name="recurrence_weekday" class="form-select">
                                <option value="">— Not set —</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                                <option value="0">Sunday</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="monthday_wrap" style="display:none;">
                            <label class="form-label">Day of month <span class="text-muted small">(optional)</span></label>
                            <select name="recurrence_monthday" class="form-select">
                                <option value="">— Not set —</option>
                                <?php for($d=1;$d<=31;$d++): ?>
                                    <option value="<?php echo e($d); ?>"><?php echo e($d); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Remind days before deadline</label>
                            <input type="number" name="reminder_days_before" class="form-control" value="1" min="0" max="30">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select name="assignee_ids[]" class="form-select select2-enable <?php $__errorArgs = ['assignee_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple required data-placeholder="Search and select assignees...">
                            <?php $__currentLoopData = $assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php if(collect(old('assignee_ids', []))->contains($user->id)): echo 'selected'; endif; ?>>
                                    <?php echo e($user->name); ?><?php if(isset($workloads[$user->id])): ?> — <?php echo e($workloads[$user->id]['active_tasks']); ?> active (<?php echo e($workloads[$user->id]['label']); ?>)<?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text">Search and select one or more assignees. Each gets an independent assignment.</div>
                        <?php $__errorArgs = ['assignee_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Instructions</label>
                        <textarea name="instructions" rows="3" class="form-control"><?php echo e(old('instructions')); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Attachment (optional)</label>
                        <input type="file" name="attachment" class="form-control <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <div class="form-text">Max 10MB. Allowed: pdf, doc, docx, xls, xlsx, png, jpg, zip</div>
                        <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Create & Assign Task
                        </button>
                        <a href="<?php echo e(route('admin.tasks.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    function syncTaskTypeUI() {
        var typeSelect = document.getElementById('task_type');
        var freqWrapper = document.getElementById('frequency_wrapper');
        var weekdayWrap = document.getElementById('weekday_wrap');
        var monthdayWrap = document.getElementById('monthday_wrap');
        var freq = document.getElementById('frequency');
        if (!typeSelect || !freqWrapper) return;

        var isRecurring = typeSelect.value === 'recurring';
        freqWrapper.style.display = isRecurring ? '' : 'none';

        var f = freq ? freq.value : '';
        if (weekdayWrap) weekdayWrap.style.display = (isRecurring && f === 'weekly') ? '' : 'none';
        if (monthdayWrap) monthdayWrap.style.display = (isRecurring && f === 'monthly') ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var typeSelect = document.getElementById('task_type');
        var freq = document.getElementById('frequency');

        if (typeSelect) {
            typeSelect.addEventListener('change', syncTaskTypeUI);
            // Select2 support (if applied)
            if (window.jQuery) {
                jQuery(typeSelect).on('change select2:select', syncTaskTypeUI);
            }
        }
        if (freq) {
            freq.addEventListener('change', syncTaskTypeUI);
            if (window.jQuery) {
                jQuery(freq).on('change select2:select', syncTaskTypeUI);
            }
        }
        syncTaskTypeUI();
    });
})();
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/tasks/create.blade.php ENDPATH**/ ?>