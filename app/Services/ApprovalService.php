<?php

namespace App\Services;

use App\Models\TaskAssignment;
use App\Models\TaskSubmission;
use App\Models\TaskExtensionRequest;
use App\Models\TaskTransferRequest;
use App\Models\TaskHistory;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function __construct(protected TransferService $transferService) {}

    public function approveSubmission(TaskSubmission $submission, User $admin, ?string $note = null): void
    {
        DB::transaction(function () use ($submission, $admin, $note) {
            $assignment = $submission->assignment;

            $submission->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $assignment->update([
                'status' => 'completed',
                'approved_at' => now(),
                'completed_at' => now(),
                'admin_notes' => $note,
            ]);

            TaskHistory::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'user_id' => $admin->id,
                'action' => 'approved',
                'description' => 'Submission approved' . ($note ? ": {$note}" : ''),
            ]);
        });
    }

    public function returnSubmission(TaskSubmission $submission, User $admin, string $returnNote): void
    {
        DB::transaction(function () use ($submission, $admin, $returnNote) {
            $assignment = $submission->assignment;

            $submission->update([
                'status' => 'returned',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'return_note' => $returnNote,
            ]);

            $assignment->update([
                'status' => 'returned',
                'admin_notes' => $returnNote,
            ]);

            TaskHistory::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'user_id' => $admin->id,
                'action' => 'returned',
                'description' => "Returned with note: {$returnNote}",
            ]);
        });
    }

    public function approveExtension(TaskExtensionRequest $request, User $admin, ?string $adminNote = null): void
    {
        DB::transaction(function () use ($request, $admin, $adminNote) {
            $assignment = $request->assignment;
            $oldDeadline = $assignment->deadline;

            $assignment->update([
                'deadline' => $request->requested_deadline,
            ]);

            $request->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'admin_note' => $adminNote,
            ]);

            TaskHistory::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'user_id' => $admin->id,
                'action' => 'extension_approved',
                'old_values' => ['deadline' => $oldDeadline],
                'new_values' => ['deadline' => $request->requested_deadline],
                'description' => 'Deadline extension approved',
            ]);
        });
    }

    public function rejectExtension(TaskExtensionRequest $request, User $admin, ?string $adminNote = null): void
    {
        $request->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'admin_note' => $adminNote,
        ]);

        TaskHistory::create([
            'task_id' => $request->assignment->task_id,
            'task_assignment_id' => $request->task_assignment_id,
            'user_id' => $admin->id,
            'action' => 'extension_rejected',
            'description' => 'Deadline extension rejected' . ($adminNote ? ": {$adminNote}" : ''),
        ]);
    }
}
