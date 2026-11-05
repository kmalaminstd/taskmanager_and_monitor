@extends('layouts.admin')

@section('title', 'Transfers')
@section('page-title', 'Transfer History')

@section('content')
<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Transfers</div>
            <div class="fs-3 fw-bold">{{ $counts['total'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Admin Direct</div>
            <div class="fs-3 fw-bold">{{ $counts['admin_direct'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Request Approved</div>
            <div class="fs-3 fw-bold">{{ $counts['request_approved'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Transfer History -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Transfer History</span>
            </div>

            <!-- Filters -->
            <div class="card-body border-bottom" style="overflow: scroll;">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search task..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="transfer_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="admin_direct" @selected(request('transfer_type')==='admin_direct')>Admin Direct</option>
                            <option value="request_approved" @selected(request('transfer_type')==='request_approved')>Request Approved</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex gap-1">
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                        <button class="btn btn-sm btn-primary">Filter</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive transfer-table-wrapper">
                <table class="table table-hover mb-0 transfer-table">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Type</th>
                            <th>By</th>
                            <th>Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $t)
                        <tr>
                            <td>
                                <a href="{{ route('admin.tasks.show', $t->task_id) }}">
                                    {{ Str::limit($t->task->title ?? '—', 30) }}
                                </a>
                            </td>
                            <td>{{ $t->previousAssignee->name ?? '—' }}</td>
                            <td class="fw-medium">{{ $t->newAssignee->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $t->transfer_type === 'admin_direct' ? 'primary' : 'info' }}">
                                    {{ $t->transfer_type === 'admin_direct' ? 'Admin Direct' : 'Request' }}
                                </span>
                            </td>
                            <td>{{ $t->transferredBy->name ?? '—' }}</td>
                            <td>
                                <small>{{ $t->transferred_at?->format('M d, Y') }}</small>
                            </td>
                            <td><small>{{ Str::limit($t->reason, 40) }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No transfers recorded yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transfers->hasPages())
            <div class="card-footer bg-white">{{ $transfers->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Quick Transfer -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Quick Transfer</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($activeTasks as $task)
                    <div class="list-group-item">
                        <div class="fw-medium small mb-1">{{ Str::limit($task->title, 35) }}</div>
                        <div class="mb-2">
                            @foreach($task->activeAssignments->take(3) as $a)
                                <span class="badge bg-light text-dark">{{ $a->assignee->name ?? '—' }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.transfers.create', $task) }}" class="btn btn-sm btn-warning w-100">
                            <i class="bi bi-arrow-left-right"></i> Transfer
                        </a>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted py-4">
                        No active tasks available
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
