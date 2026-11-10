<?php
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('task_subtasks');
    $subtasks = $tableExists ? $task->subtasks()->with('assignee')->get() : collect();
    $progress = $tableExists ? $task->subtaskProgress() : ['total' => 0, 'completed' => 0, 'percent' => 0];
?>

<div class="card border-0 shadow-sm mb-4" id="task-subtasks">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-1"></i> Checklist / Subtasks</span>
        <?php if($progress['total'] > 0): ?>
            <span class="small text-muted"><?php echo e($progress['completed']); ?> / <?php echo e($progress['total']); ?> — <?php echo e($progress['percent']); ?>%</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if(!$tableExists): ?>
            <div class="alert alert-warning mb-0">
                Subtasks table is missing. Run: <code>php artisan migrate</code>
            </div>
        <?php else: ?>
            <?php if($progress['total'] > 0): ?>
            <div class="progress mb-3" style="height:10px;">
                <div class="progress-bar bg-success" style="width:<?php echo e($progress['percent']); ?>%"></div>
            </div>
            <?php endif; ?>

            <ul class="list-group list-group-flush mb-3">
                <?php $__empty_1 = true; $__currentLoopData = $subtasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="list-group-item px-0 d-flex align-items-start gap-2">
                    <form method="POST" action="<?php echo e(route('tasks.subtasks.toggle', $st)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm <?php echo e($st->status === 'completed' ? 'btn-success' : 'btn-outline-secondary'); ?>">
                            <i class="bi bi-<?php echo e($st->status === 'completed' ? 'check-square' : 'square'); ?>"></i>
                        </button>
                    </form>
                    <div class="flex-grow-1">
                        <div class="<?php echo e($st->status === 'completed' ? 'text-decoration-line-through text-muted' : 'fw-medium'); ?>">
                            <?php echo e($st->title); ?>

                        </div>
                        <?php if($st->description): ?>
                            <div class="small text-muted"><?php echo e($st->description); ?></div>
                        <?php endif; ?>
                        <div class="small text-muted">
                            <?php if($st->assignee): ?> <?php echo e($st->assignee->name); ?> · <?php endif; ?>
                            <?php if($st->deadline): ?> Due <?php echo e($st->deadline->format('M d')); ?> · <?php endif; ?>
                            <?php echo e(ucfirst(str_replace('_',' ', $st->status))); ?>

                        </div>
                    </div>
                    <?php if(auth()->user()->canMonitor()): ?>
                    <form method="POST" action="<?php echo e(route('tasks.subtasks.destroy', $st)); ?>" onsubmit="return confirm('Delete subtask?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="list-group-item px-0 text-muted small">No subtasks yet.</li>
                <?php endif; ?>
            </ul>

            <?php if(auth()->user()->canMonitor()): ?>
            <form method="POST" action="<?php echo e(route('tasks.subtasks.store', $task)); ?>" class="row g-2">
                <?php echo csrf_field(); ?>
                <div class="col-md-5">
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="New subtask title" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="deadline" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <select name="assignee_id" class="form-select select2-enable form-select-sm">
                        <option value="">Assignee</option>
                        <?php $__currentLoopData = \App\Models\User::where('role','assignee')->where('status','active')->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-primary w-100">Add</button>
                </div>
            </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/components/task-subtasks.blade.php ENDPATH**/ ?>