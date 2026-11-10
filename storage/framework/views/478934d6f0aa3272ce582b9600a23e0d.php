<?php $__env->startSection('title', 'New Personal Task'); ?>
<?php $__env->startSection('page-title', 'Create Personal Task'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('assignee.personal-tasks.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" id="frequency" class="form-select">
                                <option value="one_time">One Time</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" value="<?php echo e(old('deadline')); ?>">
                        </div>
                    </div>

                    <div class="row" id="recurrence_options">
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
                            <div class="form-text">Default is 1 day before deadline.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Task</button>
                        <a href="<?php echo e(route('assignee.personal-tasks.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleRecurrence() {
    const f = document.getElementById('frequency').value;
    document.getElementById('weekday_wrap').style.display = f === 'weekly' ? 'block' : 'none';
    document.getElementById('monthday_wrap').style.display = f === 'monthly' ? 'block' : 'none';
}
document.getElementById('frequency').addEventListener('change', toggleRecurrence);
toggleRecurrence();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/personal-tasks/create.blade.php ENDPATH**/ ?>