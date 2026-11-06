<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\TaskAssistanceRequest;
use Illuminate\Http\Request;

class AssistanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskAssistanceRequest::with(['task', 'requester', 'users', 'assignment']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $requests = $query->latest()->paginate(20)->withQueryString();
        $counts = [
            'total' => TaskAssistanceRequest::count(),
            'pending' => TaskAssistanceRequest::where('status', 'pending')->count(),
            'accepted' => TaskAssistanceRequest::where('status', 'accepted')->count(),
            'completed' => TaskAssistanceRequest::where('status', 'completed')->count(),
        ];
        return view('supervisor.assistance.index', compact('requests', 'counts'));
    }

    public function show(TaskAssistanceRequest $assistance)
    {
        $assistance->load(['task', 'requester', 'users', 'assignment.assignee']);
        return view('supervisor.assistance.show', compact('assistance'));
    }
}
