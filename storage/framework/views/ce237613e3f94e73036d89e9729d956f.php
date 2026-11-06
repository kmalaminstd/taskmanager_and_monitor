<?php $__env->startSection('title', 'Assistance'); ?>
<?php $__env->startSection('page-title', 'Assistance Requests'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Total</div><div class="fs-4 fw-bold"><?php echo e($counts['total']); ?></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Pending</div><div class="fs-4 fw-bold"><?php echo e($counts['pending']); ?></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Accepted</div><div class="fs-4 fw-bold text-success"><?php echo e($counts['accepted']); ?></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Completed</div><div class="fs-4 fw-bold"><?php echo e($counts['completed']); ?></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search task..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <?php $__currentLoopData = ['pending','accepted','rejected','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if(request('status')===$s): echo 'selected'; endif; ?>><?php echo e(ucfirst($s)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-sm btn-primary">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Requester</th>
                    <th>Helpers</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e(Str::limit($req->task->title ?? '—', 35)); ?></td>
                    <td><?php echo e($req->requester->name ?? '—'); ?></td>
                    <td>
                        <?php $__currentLoopData = $req->users->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-light text-dark"><?php echo e($h->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($req->users->count() > 3): ?>
                            <span class="badge bg-secondary">+<?php echo e($req->users->count()-3); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-<?php echo e($req->status === 'pending' ? 'warning' : ($req->status === 'accepted' ? 'success' : 'secondary')); ?>"><?php echo e(ucfirst($req->status)); ?></span></td>
                    <td><small><?php echo e($req->created_at->format('M d, Y')); ?></small></td>
                    <td><a href="<?php echo e(route('admin.assistance.show', $req)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No assistance requests</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($requests->hasPages()): ?>
    <div class="card-footer bg-white"><?php echo e($requests->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/admin/assistance/index.blade.php ENDPATH**/ ?>