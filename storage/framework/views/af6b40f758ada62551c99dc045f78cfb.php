
<?php
    $hasParentCol = \Illuminate\Support\Facades\Schema::hasColumn('task_notes', 'parent_id');
    $notesQuery = $task->notes()->with(['user']);
    if ($hasParentCol) {
        $notesQuery->whereNull('parent_id')->with(['replies.user', 'attachments']);
        if (\Illuminate\Support\Facades\Schema::hasColumn('task_notes', 'is_pinned')) {
            $notesQuery->orderByDesc('is_pinned');
        }
    }
    $notes = $notesQuery->orderBy('created_at')->get();
?>

<div class="card border-0 shadow-sm mb-4" id="task-discussion">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-chat-dots me-1"></i> Discussion</span>
        <span class="badge bg-secondary"><?php echo e($notes->count()); ?></span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('tasks.comments.store', $task)); ?>" enctype="multipart/form-data" class="mb-4">
            <?php echo csrf_field(); ?>
            <textarea name="note" class="form-control mb-2" rows="3" required placeholder="Write a comment..."></textarea>
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <input type="file" name="attachments[]" class="form-control form-control-sm" style="max-width:260px" multiple>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-send"></i> Post Comment
                </button>
            </div>
        </form>

        <?php $__empty_1 = true; $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="border rounded p-3 mb-3 <?php echo e(($note->is_pinned ?? false) ? 'border-warning bg-light' : ''); ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong><?php echo e($note->user->name ?? 'User'); ?></strong>
                    <?php if($note->is_pinned ?? false): ?>
                        <span class="badge bg-warning text-dark">Pinned</span>
                    <?php endif; ?>
                    <span class="text-muted small ms-1"><?php echo e($note->created_at->diffForHumans()); ?>

                        <?php if($note->edited_at ?? false): ?> (edited) <?php endif; ?>
                    </span>
                </div>
                <div class="d-flex gap-1">
                    <?php if(auth()->user()->canMonitor()): ?>
                    <form method="POST" action="<?php echo e(route('tasks.comments.pin', $note)); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-outline-warning" title="Pin/Unpin"><i class="bi bi-pin-angle"></i></button>
                    </form>
                    <?php endif; ?>
                    <?php if($note->user_id === auth()->id() || auth()->user()->isAdmin()): ?>
                    <form method="POST" action="<?php echo e(route('tasks.comments.destroy', $note)); ?>" onsubmit="return confirm('Delete this comment?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-2"><?php echo nl2br(e($note->note)); ?></div>

            <?php if(isset($note->attachments) && $note->attachments->count()): ?>
            <div class="mt-2">
                <?php $__currentLoopData = $note->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(asset('storage/'.$att->file_path)); ?>" target="_blank" class="badge bg-light text-dark text-decoration-none me-1">
                        <i class="bi bi-paperclip"></i> <?php echo e($att->original_name); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($hasParentCol && $note->relationLoaded('replies')): ?>
                <?php $__currentLoopData = $note->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="ms-3 mt-2 border-start ps-3">
                    <strong class="small"><?php echo e($reply->user->name ?? ''); ?></strong>
                    <span class="text-muted small"><?php echo e($reply->created_at->diffForHumans()); ?></span>
                    <div class="small"><?php echo e($reply->note); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <form method="POST" action="<?php echo e(route('tasks.comments.store', $task)); ?>" class="mt-2 ms-3">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="parent_id" value="<?php echo e($note->id); ?>">
                    <div class="input-group input-group-sm">
                        <input type="text" name="note" class="form-control" placeholder="Reply..." required>
                        <button class="btn btn-outline-primary">Reply</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-muted text-center mb-0">No comments yet. Start the discussion.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/components/task-discussion.blade.php ENDPATH**/ ?>