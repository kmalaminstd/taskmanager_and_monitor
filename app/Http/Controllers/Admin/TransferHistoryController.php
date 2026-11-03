<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskTransferHistory;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TransferHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskTransferHistory::with([
            'task',
            'previousAssignee',
            'newAssignee',
            'transferredBy'
        ]);

        if ($request->filled('transfer_type')) {
            $query->where('transfer_type', $request->transfer_type);
        }

        if ($request->filled('search')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('transferred_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('transferred_at', '<=', $request->to);
        }

        $transfers = $query->orderBy('transferred_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'total' => TaskTransferHistory::count(),
            'admin_direct' => TaskTransferHistory::where('transfer_type', 'admin_direct')->count(),
            'request_approved' => TaskTransferHistory::where('transfer_type', 'request_approved')->count(),
        ];

        // Tasks that can still be transferred (for quick action)
        $activeTasks = Task::where('status', 'active')
            ->whereHas('activeAssignments')
            ->with('activeAssignments.assignee')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.transfers.index', compact('transfers', 'counts', 'activeTasks'));
    }
}
