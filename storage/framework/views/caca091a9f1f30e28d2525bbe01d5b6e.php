<?php $__env->startSection('title', 'Users'); ?>
<?php $__env->startSection('page-title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?php echo e(request('search')); ?>">
        <select name="role" class="form-select form-select-sm">
            <option value="">All Roles</option>
            <option value="admin" <?php if(request('role')==='admin'): echo 'selected'; endif; ?>>Admin</option>
            <option value="assignee" <?php if(request('role')==='assignee'): echo 'selected'; endif; ?>>Assignee</option>
        </select>
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Active</option>
            <option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>>Inactive</option>
        </select>
        <button class="btn btn-sm btn-primary">Filter</button>
    </form>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add User
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="fw-medium"><?php echo e($user->name); ?></td>
                    <td><?php echo e($user->email); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e($user->role === 'admin' ? 'dark' : 'primary'); ?>">
                            <?php echo e(ucfirst($user->role)); ?>

                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo e($user->status === 'active' ? 'success' : 'secondary'); ?>">
                            <?php echo e(ucfirst($user->status)); ?>

                        </span>
                    </td>
                    <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?php echo e(route('admin.users.toggle-status', $user)); ?>" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-<?php echo e($user->status === 'active' ? 'warning' : 'success'); ?>" title="Toggle status">
                                <i class="bi bi-<?php echo e($user->status === 'active' ? 'pause' : 'play'); ?>"></i>
                            </button>
                        </form>
                        <?php if($user->id !== auth()->id()): ?>
                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>" class="d-inline" onsubmit="return confirm('Delete this user?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php if($users->hasPages()): ?>
    <div class="card-footer bg-white"><?php echo e($users->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/Three_grok/taskmaster/resources/views/admin/users/index.blade.php ENDPATH**/ ?>