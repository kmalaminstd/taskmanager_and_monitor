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

        $myPivotStatus = null;
        if ($isHelper) {
            $myPivotStatus = $assistance->users->firstWhere('id', auth()->id())?->pivot?->status;
        }

        return view('assignee.assistance.show', compact('assistance', 'isHelper', 'isRequester', 'myPivotStatus'));
    }

    public function respond(Request $request, TaskAssistanceRequest $assistance)
    {
        $data = $request->validate(['action' => ['required', 'in:accepted,declined']]);

        if (! $assistance->users()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        DB::transaction(function () use ($assistance, $data) {
            $assistance->users()->updateExistingPivot(auth()->id(), ['status' => $data['action']]);

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
