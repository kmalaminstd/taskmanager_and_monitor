<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WorkloadService;

class WorkloadController extends Controller
{
    public function __construct(protected WorkloadService $workloadService) {}

    public function index()
    {
        $workloads = $this->workloadService->forAllAssignees();

        $summary = [
            'high' => $workloads->where('level', 'high')->count(),
            'normal' => $workloads->where('level', 'normal')->count(),
            'low' => $workloads->where('level', 'low')->count(),
            'total_active' => $workloads->sum('active_tasks'),
            'total_overdue' => $workloads->sum('overdue_tasks'),
        ];

        return view('admin.workload.index', compact('workloads', 'summary'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'assignee') {
            abort(404);
        }

        $workload = $this->workloadService->forUser($user);

        $assignments = $user->assignments()
            ->with('task')
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress', 'submitted', 'returned'])
            ->orderBy('deadline')
            ->get();

        return view('admin.workload.show', compact('user', 'workload', 'assignments'));
    }
}
