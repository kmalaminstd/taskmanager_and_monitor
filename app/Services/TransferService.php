<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskTransferHistory;
use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskTransferredNotification;

class TransferService
{
    /**
     * Admin direct transfer / manage assignees.
     * Supports: replace, add, remove.
     */
    public function adminDirectTransfer(
        Task $task,
        array $newAssigneeIds,
        string $reason,
        ?string $notes,
        User $admin,
        bool $keepExisting = false
    ): void {
        DB::transaction(function () use ($task, $newAssigneeIds, $reason, $notes, $admin, $keepExisting) {
            $currentActive = $task->assignments()
                ->where('is_active', true)
                ->get();

            $currentIds = $currentActive->pluck('assignee_id')->toArray();

            if ($keepExisting) {
                // Only add new ones that are not already active
                $toAdd = array_diff($newAssigneeIds, $currentIds);
                $toRemove = [];
            } else {
                // Full replace logic
                $toAdd = array_diff($newAssigneeIds, $currentIds);
                $toRemove = array_diff($currentIds, $newAssigneeIds);
            }

            // Deactivate removed assignees
            foreach ($currentActive->whereIn('assignee_id', $toRemove) as $oldAssignment) {
                $oldAssignment->update(['is_active' => false]);

                TaskTransferHistory::create([
                    'task_id' => $task->id,
                    'old_assignment_id' => $oldAssignment->id,
                    'previous_assignee_id' => $oldAssignment->assignee_id,
                    'new_assignee_id' => $newAssigneeIds[0] ?? $oldAssignment->assignee_id, // placeholder if pure remove
                    'transferred_by' => $admin->id,
                    'transfer_type' => 'admin_direct',
                    'reason' => $reason,
                    'notes' => $notes,
                    'transferred_at' => now(),
                ]);
            }

            // Create new assignments
            foreach ($toAdd as $assigneeId) {
                $newAssignment = TaskAssignment::create([
                    'task_id' => $task->id,
                    'assignee_id' => $assigneeId,
                    'assigned_by' => $admin->id,
                    'status' => 'pending',
                    'assigned_at' => now(),
                    'deadline' => $task->deadline,
                    'priority' => $task->priority,
                    'is_active' => true,
                ]);

                // Link history for each new assignee
                $previousId = !empty($toRemove) ? $toRemove[0] : null;
                TaskTransferHistory::create([
                    'task_id' => $task->id,
                    'old_assignment_id' => $currentActive->where('assignee_id', $previousId)->first()?->id,
                    'previous_assignee_id' => $previousId,
                    'new_assignee_id' => $assigneeId,
                    'transferred_by' => $admin->id,
                    'transfer_type' => 'admin_direct',
                    'reason' => $reason,
                    'notes' => $notes,
                    'transferred_at' => now(),
                ]);
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => $admin->id,
                'action' => 'transferred',
                'description' => "Admin direct transfer. Reason: {$reason}",
                'new_values' => [
                    'added' => $toAdd,
                    'removed' => $toRemove,
                ],
            ]);

            // TODO: Send notifications to previous and new assignees
        });
    }

    /**
     * Approve an assignee-initiated transfer request.
     */
    public function approveTransferRequest($transferRequest, User $admin, ?string $adminNote = null): void
    {
        DB::transaction(function () use ($transferRequest, $admin, $adminNote) {
            $oldAssignment = $transferRequest->assignment;

            // Deactivate old
            $oldAssignment->update(['is_active' => false]);

            // Create new assignment
            $newAssignment = TaskAssignment::create([
                'task_id' => $oldAssignment->task_id,
                'assignee_id' => $transferRequest->requested_new_assignee_id,
                'assigned_by' => $admin->id,
                'status' => 'pending',
                'assigned_at' => now(),
                'deadline' => $oldAssignment->deadline,
                'priority' => $oldAssignment->priority,
                'is_active' => true,
            ]);

            TaskTransferHistory::create([
                'task_id' => $oldAssignment->task_id,
                'old_assignment_id' => $oldAssignment->id,
                'previous_assignee_id' => $oldAssignment->assignee_id,
                'new_assignee_id' => $transferRequest->requested_new_assignee_id,
                'transferred_by' => $admin->id,
                'transfer_type' => 'request_approved',
                'reason' => $transferRequest->reason,
                'notes' => $adminNote,
                'transferred_at' => now(),
            ]);

            $transferRequest->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'admin_note' => $adminNote,
            ]);

            TaskHistory::create([
                'task_id' => $oldAssignment->task_id,
                'user_id' => $admin->id,
                'action' => 'transfer_approved',
                'description' => 'Transfer request approved',
            ]);
        });
    }

    public function rejectTransferRequest($transferRequest, User $admin, ?string $adminNote = null): void
    {
        $transferRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_note' => $adminNote,
        ]);
    }
}
