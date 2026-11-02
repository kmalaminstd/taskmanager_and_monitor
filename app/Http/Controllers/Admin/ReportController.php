<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\TaskTransferHistory;
use App\Models\TaskExtensionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function generate(Request $request)
    {
        $type = $request->get('type', 'overall');
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->subMonths(3);
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now();
        $userId = $request->get('user_id');

        $data = match ($type) {
            'overall' => $this->overallReport($from, $to),
            'employee' => $this->employeeReport($from, $to, $userId),
            'completed' => $this->completedReport($from, $to, $userId),
            'overdue' => $this->overdueReport($userId),
            'late' => $this->lateReport($from, $to, $userId),
            'transfers' => $this->transferReport($from, $to),
            'extensions' => $this->extensionReport($from, $to),
            default => [],
        };

        if ($request->get('export') === 'csv') {
            return $this->exportCsv($type, $data);
        }

        $users = User::where('role', 'assignee')->orderBy('name')->get();

        return view('admin.reports.show', compact('type', 'data', 'from', 'to', 'users', 'userId'));
    }

    protected function overallReport($from, $to)
    {
        $q = TaskAssignment::whereBetween('assigned_at', [$from, $to])->where('status', '!=', 'cancelled');
        return [
            'total' => (clone $q)->count(),
            'completed' => (clone $q)->where('status', 'completed')->count(),
            'pending' => (clone $q)->where('status', 'pending')->count(),
            'in_progress' => (clone $q)->where('status', 'in_progress')->count(),
            'submitted' => (clone $q)->where('status', 'submitted')->count(),
            'overdue' => TaskAssignment::where('is_active', true)
                ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                ->where('deadline', '<', now())->count(),
        ];
    }

    protected function employeeReport($from, $to, $userId)
    {
        $query = TaskAssignment::with(['task', 'assignee'])
            ->whereBetween('assigned_at', [$from, $to])
            ->where('status', '!=', 'cancelled');

        if ($userId) $query->where('assignee_id', $userId);

        return $query->orderBy('assigned_at', 'desc')->get();
    }

    protected function completedReport($from, $to, $userId)
    {
        $query = TaskAssignment::with(['task', 'assignee'])
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to]);

        if ($userId) $query->where('assignee_id', $userId);

        return $query->orderBy('completed_at', 'desc')->get();
    }

    protected function overdueReport($userId)
    {
        $query = TaskAssignment::with(['task', 'assignee'])
            ->where('is_active', true)
            ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
            ->where('deadline', '<', now());

        if ($userId) $query->where('assignee_id', $userId);

        return $query->orderBy('deadline')->get();
    }

    protected function lateReport($from, $to, $userId)
    {
        $query = TaskAssignment::with(['task', 'assignee'])
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->whereColumn('submitted_at', '>', 'deadline');

        if ($userId) $query->where('assignee_id', $userId);

        return $query->get();
    }

    protected function transferReport($from, $to)
    {
        return TaskTransferHistory::with(['task', 'previousAssignee', 'newAssignee', 'transferredBy'])
            ->whereBetween('transferred_at', [$from, $to])
            ->orderBy('transferred_at', 'desc')
            ->get();
    }

    protected function extensionReport($from, $to)
    {
        return TaskExtensionRequest::with(['assignment.task', 'requester'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function exportCsv(string $type, $data): StreamedResponse
    {
        $filename = "report_{$type}_" . now()->format('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($type, $data) {
            $handle = fopen('php://output', 'w');

            if ($type === 'overall') {
                fputcsv($handle, ['Metric', 'Value']);
                foreach ($data as $key => $value) {
                    fputcsv($handle, [str_replace('_', ' ', ucfirst($key)), $value]);
                }
            } elseif (in_array($type, ['employee', 'completed', 'overdue', 'late'])) {
                fputcsv($handle, ['Task', 'Assignee', 'Status', 'Deadline', 'Submitted At', 'Completed At']);
                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->task->title ?? '',
                        $row->assignee->name ?? '',
                        $row->status,
                        $row->deadline?->format('Y-m-d'),
                        $row->submitted_at?->format('Y-m-d H:i'),
                        $row->completed_at?->format('Y-m-d H:i'),
                    ]);
                }
            } elseif ($type === 'transfers') {
                fputcsv($handle, ['Task', 'From', 'To', 'Type', 'By', 'Date', 'Reason']);
                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->task->title ?? '',
                        $row->previousAssignee->name ?? '',
                        $row->newAssignee->name ?? '',
                        $row->transfer_type,
                        $row->transferredBy->name ?? '',
                        $row->transferred_at?->format('Y-m-d H:i'),
                        $row->reason,
                    ]);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
