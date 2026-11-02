<?php

namespace App\Policies;

use App\Models\TaskAssignment;
use App\Models\User;

class TaskAssignmentPolicy
{
    public function view(User $user, TaskAssignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $assignment->assignee_id && $assignment->is_active;
    }

    public function update(User $user, TaskAssignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $assignment->assignee_id 
            && $assignment->is_active 
            && in_array($assignment->status, ['pending', 'in_progress', 'returned']);
    }

    public function submit(User $user, TaskAssignment $assignment): bool
    {
        return $user->id === $assignment->assignee_id 
            && $assignment->is_active 
            && in_array($assignment->status, ['in_progress', 'returned']);
    }

    public function start(User $user, TaskAssignment $assignment): bool
    {
        return $user->id === $assignment->assignee_id 
            && $assignment->is_active 
            && $assignment->status === 'pending';
    }
}
