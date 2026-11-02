<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('page-title', 'System Settings'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('admin.settings.update')); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold text-capitalize"><?php echo e($group); ?> Settings</div>
        <div class="card-body">
            <div class="row g-3">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <label class="form-label"><?php echo e(str_replace('_', ' ', ucfirst($setting->key))); ?></label>
                    <?php if($setting->type === 'boolean'): ?>
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[<?php echo e($setting->key); ?>]" value="0">
                            <input class="form-check-input" type="checkbox" name="settings[<?php echo e($setting->key); ?>]" value="1"
                                   <?php echo e($setting->value == '1' || $setting->value === true ? 'checked' : ''); ?>>
                        </div>
                    <?php elseif($setting->type === 'integer'): ?>
                        <input type="number" name="settings[<?php echo e($setting->key); ?>]" class="form-control"
                               value="<?php echo e($setting->value); ?>">
                    <?php else: ?>
                        <input type="text" name="settings[<?php echo e($setting->key); ?>]" class="form-control"
                               value="<?php echo e($setting->value); ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Save Settings
    </button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /run/media/k-m-al-amin/New Volume/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>