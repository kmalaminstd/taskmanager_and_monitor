<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssistanceRequest;
use App\Models\TaskHistory;
use App\Notifications\TaskStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssistanceController extends Controller
{
    public function index()
    {
        $incoming = TaskAssistanceRequest::with(['task', 'requester', 'assignment'])
            ->whereHas('users', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->paginate(15, ['*'], 'incoming');

        $outgoing = TaskAssistanceRequest::with(['task', 'users'])
            ->where('requester_id', auth()->id())
            ->latest()
            ->paginate(15, ['*'], 'outgoing');

        return view('assignee.assistance.index', compact('incoming', 'outgoing'));
    }

    public function show(TaskAssistanceRequest $assistance)
    {
        $assistance->load(['task', 'requester', 'assignment', 'users']);

        $isHelper = $assistance->users->contains('id', auth()->id());
        $isRequester = $assistance->requester_id === auth()->id();

        if (! $isHelper && ! $isRequester && ! (method_exists(auth()->user(), 'canMonitor') && auth()->user()->canMonitor())) {
            abort(403);
        }

        $myPivot = $isHelper
            ? $assistance->users->firstWhere('id', auth()->id())?->pivot
            : null;

        return view('assignee.assistance.show', compact('assistance', 'isHelper', 'isRequester', 'myPivot'));
    }

    /** Helper accepts or declines */
    public function respond(Request $request, TaskAssistanceRequest $assistance)
    {
        $data = $request->validate(['action' => ['required', 'in:accepted,declined']]);

        if (! $assistance->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        DB::transaction(function () use ($assistance, $data) {
            $assistance->users()->updateExistingPivot(auth()->id(), [
                'status' => $data['action'],
                'responded_at' => now(),
            ]);

            if ($data['action'] === 'accepted' && $assistance->status === 'pending') {
                $assistance->update(['status' => 'accepted']);
            }

            $statuses = $assistance->users()->pluck('task_assistance_users.status');
            if ($statuses->every(fn ($s) => $s === 'declined')) {
                $assistance->update(['status' => 'rejected']);
            }

            TaskHistory::create([
                'task_id' => $assistance->task_id,
                'task_assignment_id' => $assistance->task_assignment_id,
                'user_id' => auth()->id(),
                'action' => 'assistance_' . $data['action'],
                'description' => auth()->user()->name . ' ' . $data['action'] . ' assistance request',
            ]);

            $assistance->requester?->notify(new TaskStatusNotification(
                'Assistance ' . ucfirst($data['action']),
                auth()->user()->name . ' ' . $data['action'] . ' your help request on: ' . ($assistance->task->title ?? 'task'),
                route('assignee.assistance.show', $assistance->id),
                'assistance'
            ));
        });

        return back()->with('success', 'You have ' . $data['action'] . ' this assistance request.');
    }

    /** Helper submits work with a note (ready for assignee review) */
    public function submitWork(Request $request, TaskAssistanceRequest $assistance)
    {
        $data = $request->validate([
            'helper_note' => ['required', 'string', 'max:3000'],
        ]);

        $pivot = $assistance->users()->where('user_id', auth()->id())->first();
        if (! $pivot || $pivot->pivot->status !== 'accepted') {
            return back()->with('error', 'You can only submit after accepting this request.');
        }

        DB::transaction(function () use ($assistance, $data) {
            $assistance->users()->updateExistingPivot(auth()->id(), [
                'status' => 'submitted',
                'helper_note' => $data['helper_note'],
                'submitted_at' => now(),
            ]);

            TaskHistory::create([
                'task_id' => $assistance->task_id,
                'task_assignment_id' => $assistance->task_assignment_id,
                'user_id' => auth()->id(),
                'action' => 'assistance_submitted',
                'description' => auth()->user()->name . ' submitted assistance work',
            ]);

            $assistance->requester?->notify(new TaskStatusNotification(
                'Assistance Work Submitted',
                auth()->user()->name . ' completed help on: ' . ($assistance->task->title ?? 'task') . ' — please review',
                route('assignee.assistance.show', $assistance->id),
                'assistance'
            ));
        });

        return back()->with('success', 'Your work note was sent to the task assignee for review.');
    }

    /** Assignee approves helper's submitted work */
    public function approveHelper(Request $request, TaskAssistanceRequest $assistance, $userId)
    {
        if ($assistance->requester_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'requester_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $helper = $assistance->users()->where('user_id', $userId)->first();
        if (! $helper || $helper->pivot->status !== 'submitted') {
            return back()->with('error', 'This helper has not submitted work for review.');
        }

        DB::transaction(function () use ($assistance, $userId, $data, $helper) {
            $assistance->users()->updateExistingPivot($userId, [
                'status' => 'completed',
                'requester_note' => $data['requester_note'] ?? null,
            ]);

            // If all non-declined helpers are completed, mark request completed
            $active = $assistance->users()
                ->whereNotIn('task_assistance_users.status', ['declined', 'rejected'])
                ->get();
            if ($active->isNotEmpty() && $active->every(fn ($u) => $u->pivot->status === 'completed')) {
                $assistance->update(['status' => 'completed']);
            }

            TaskHistory::create([
                'task_id' => $assistance->task_id,
                'task_assignment_id' => $assistance->task_assignment_id,
                'user_id' => auth()->id(),
                'action' => 'assistance_helper_approved',
                'description' => 'Approved assistance from ' . ($helper->name ?? 'helper'),
            ]);

            $helper->notify(new TaskStatusNotification(
                'Assistance Approved',
                'Your help on "' . ($assistance->task->title ?? 'task') . '" was approved.',
                route('assignee.assistance.show', $assistance->id),
                'assistance'
            ));
        });

        return back()->with('success', 'Helper work marked as completed.');
    }

    /** Assignee rejects helper's submitted work with note */
    public function rejectHelper(Request $request, TaskAssistanceRequest $assistance, $userId)
    {
        if ($assistance->requester_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'requester_note' => ['required', 'string', 'max:2000'],
        ]);

        $helper = $assistance->users()->where('user_id', $userId)->first();
        if (! $helper || $helper->pivot->status !== 'submitted') {
            return back()->with('error', 'This helper has not submitted work for review.');
        }

        DB::transaction(function () use ($assistance, $userId, $data, $helper) {
            // Send back to accepted so helper can resubmit
            $assistance->users()->updateExistingPivot($userId, [
                'status' => 'accepted',
                'requester_note' => $data['requester_note'],
                'helper_note' => null,
                'submitted_at' => null,
            ]);

            TaskHistory::create([
                'task_id' => $assistance->task_id,
                'task_assignment_id' => $assistance->task_assignment_id,
                'user_id' => auth()->id(),
                'action' => 'assistance_helper_rejected',
                'description' => 'Rejected assistance from ' . ($helper->name ?? 'helper') . ': ' . $data['requester_note'],
            ]);

            $helper->notify(new TaskStatusNotification(
                'Assistance Needs Changes',
                'Your help on "' . ($assistance->task->title ?? 'task') . '" was returned with a note.',
                route('assignee.assistance.show', $assistance->id),
                'assistance'
            ));
        });

        return back()->with('success', 'Helper work returned with your note.');
    }

    /** Requester cancels entire assistance request */
    public function cancel(TaskAssistanceRequest $assistance)
    {
        if ($assistance->requester_id !== auth()->id()) {
            abort(403);
        }

        if (in_array($assistance->status, ['completed', 'cancelled'], true)) {
            return back()->with('error', 'This request cannot be cancelled.');
        }

        $assistance->update(['status' => 'cancelled']);

        TaskHistory::create([
            'task_id' => $assistance->task_id,
            'task_assignment_id' => $assistance->task_assignment_id,
            'user_id' => auth()->id(),
            'action' => 'assistance_cancelled',
            'description' => 'Assistance request cancelled by requester',
        ]);

        return back()->with('success', 'Assistance request cancelled.');
    }

    /** Requester removes a helper from the request */
    public function removeHelper(TaskAssistanceRequest $assistance, $userId)
    {
        if ($assistance->requester_id !== auth()->id()) {
            abort(403);
        }

        if (in_array($assistance->status, ['completed', 'cancelled'], true)) {
            return back()->with('error', 'Cannot modify a closed request.');
        }

        $helper = $assistance->users()->where('user_id', $userId)->first();
        if (! $helper) {
            return back()->with('error', 'Helper not found on this request.');
        }

        $assistance->users()->detach($userId);

        TaskHistory::create([
            'task_id' => $assistance->task_id,
            'task_assignment_id' => $assistance->task_assignment_id,
            'user_id' => auth()->id(),
            'action' => 'assistance_helper_removed',
            'description' => 'Removed helper: ' . $helper->name,
        ]);

        // No helpers left → cancel
        if ($assistance->users()->count() === 0) {
            $assistance->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Helper removed from assistance request.');
    }

    /** Requester marks whole request completed (legacy / force) */
    public function complete(TaskAssistanceRequest $assistance)
    {
        if ($assistance->requester_id !== auth()->id()) {
            abort(403);
        }

        $assistance->update(['status' => 'completed']);

        TaskHistory::create([
            'task_id' => $assistance->task_id,
            'task_assignment_id' => $assistance->task_assignment_id,
            'user_id' => auth()->id(),
            'action' => 'assistance_completed',
            'description' => 'Assistance marked as completed',
        ]);

        return back()->with('success', 'Assistance marked as completed.');
    }
}
