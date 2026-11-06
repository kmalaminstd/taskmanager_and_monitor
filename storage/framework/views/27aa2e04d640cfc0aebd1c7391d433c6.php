<?php $__env->startSection('title', $assignment->task->title); ?>
<?php $__env->startSection('page-title', 'Task Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('assignee.tasks.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to My Tasks
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge badge-priority-<?php echo e($assignment->effective_priority); ?>">
                        <?php echo e(ucfirst($assignment->effective_priority)); ?>

                    </span>
                    <span class="badge badge-status-<?php echo e($assignment->status); ?>">
                        <?php echo e(str_replace('_', ' ', ucfirst($assignment->status))); ?>

                    </span>
                    <?php if($assignment->isOverdue()): ?>
                        <span class="badge bg-danger">Overdue</span>
                    <?php endif; ?>
                </div>

                <h4><?php echo e($assignment->task->title); ?></h4>
                
                <?php if($assignment->task->description): ?>
                    <p class="text-muted"><?php echo e($assignment->task->description); ?></p>
                <?php endif; ?>

                <?php if($assignment->task->instructions): ?>
                    <div class="alert alert-light border">
                        <strong>Instructions:</strong><br>
                        <?php echo e($assignment->task->instructions); ?>

                    </div>
                <?php endif; ?>

                <div class="row small text-muted mt-3">
                    <div class="col-sm-6">
                        <strong>Assigned by:</strong> <?php echo e($assignment->assignedBy->name ?? '—'); ?><br>
                        <strong>Assigned at:</strong> <?php echo e($assignment->assigned_at?->format('M d, Y H:i')); ?>

                    </div>
                    <div class="col-sm-6">
                        <strong>Deadline:</strong> <?php echo e($assignment->deadline?->format('M d, Y') ?? '—'); ?><br>
                        <strong>Started:</strong> <?php echo e($assignment->started_at?->format('M d, Y H:i') ?? 'Not started'); ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Actions</div>
            <div class="card-body">
                <?php if($assignment->status === 'pending'): ?>
                    <form method="POST" action="<?php echo e(route('assignee.tasks.start', $assignment)); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-play-fill"></i> Start Task
                        </button>
                    </form>
                <?php endif; ?>

                <?php if(in_array($assignment->status, ['in_progress', 'returned'])): ?>
                    <button type="button" class="btn btn-success me-1" data-bs-toggle="modal" data-bs-target="#submitModal">
                        <i class="bi bi-send"></i> Submit Completed Work
                    </button>
                    <button type="button" class="btn btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#extensionModal">
                        <i class="bi bi-calendar-plus"></i> Request Extension
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#transferModal">
                        <i class="bi bi-arrow-left-right"></i> Request Transfer
                    </button>

                    <button type="button" class="btn btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#assistanceModal">
                        <i class="bi bi-people"></i> Request Assistance
                    </button>

                <?php endif; ?>

                <?php if($assignment->status === 'submitted'): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-hourglass-split"></i> Submitted on <?php echo e($assignment->submitted_at?->format('M d, Y H:i')); ?>. Waiting for admin approval.
                    </div>
                <?php endif; ?>

                <?php if($assignment->status === 'completed'): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> This task was completed and approved.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?php if($assignment->task->attachments->count()): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Attachments</div>
            <ul class="list-group list-group-flush">
                <?php $__currentLoopData = $assignment->task->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item small">
                    <i class="bi bi-paperclip"></i> <?php echo e($file->original_name); ?>

                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('assignee.tasks.submit', $assignment)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Submit Completed Work</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Completion Details <span class="text-danger">*</span></label>
                        <textarea name="completion_details" rows="4" class="form-control" required
                                  placeholder="Describe what you completed..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Note (optional)</label>
                        <textarea name="completion_note" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit for Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extension Modal -->
<div class="modal fade" id="extensionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('assignee.tasks.request-extension', $assignment)); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Deadline Extension</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Current deadline: <strong><?php echo e($assignment->deadline?->format('M d, Y') ?? 'None'); ?></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Requested New Deadline <span class="text-danger">*</span></label>
                        <input type="date" name="requested_deadline" class="form-control" required min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('assignee.tasks.request-transfer', $assignment)); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Task Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Transfer To <span class="text-danger">*</span></label>
                        <select name="requested_new_assignee_id" class="form-select" required>
                            <option value="">Select team member...</option>
                            <?php $__currentLoopData = \App\Models\User::where('role','assignee')->where('status','active')->where('id','!=',auth()->id())->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="alert alert-info small mb-0">
                        This request requires admin approval. Your assignment stays active until approved.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Transfer Request</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Assistance Modal -->
<div class="modal fade" id="assistanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('assignee.tasks.request-assistance', $assignment)); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Assistance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Team Members <span class="text-danger">*</span></label>
                        <select name="helper_ids[]" class="form-select" multiple size="6" required>
                            <?php $__currentLoopData = \App\Models\User::whereIn('role',['assignee','supervisor'])->where('status','active')->where('id','!=',auth()->id())->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select one or more people.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">What do you need help with? <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Describe the help you need..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white">Send Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.assignee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/assignee/tasks/show.blade.php ENDPATH**/ ?>