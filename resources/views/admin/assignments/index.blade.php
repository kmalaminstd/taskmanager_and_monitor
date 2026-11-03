@extends('layouts.admin')

@section('title', 'Assignments')
@section('page-title', 'All Assignments')

@section('content')
<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Active</div>
            <div class="fs-4 fw-bold">{{ $counts['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold">{{ $counts['pending'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold">{{ $counts['in_progress'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Submitted</div>
            <div class="fs-4 fw-bold">{{ $counts['submitted'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small text-danger">Overdue</div>
            <div class="fs-4 fw-bold text-danger">{{ $counts['overdue'] }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search task..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','in_progress','submitted','completed','returned','overdue'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ str_replace('_',' ', ucfirst($s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="assignee_id" class="form-select form-select-sm">
                    <option value="">All Assignees</option>
                    @foreach($assignees as $u)
                        <option value="{{ $u->id }}" @selected(request('assignee_id')==$u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    @foreach(['high','medium','low'] as $p)
                        <option value="{{ $p }}" @selected(request('priority')===$p)>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Assignee</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Assigned</th>
                    <th>Assigned By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $a)
                <tr class="{{ $a->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <a href="{{ route('admin.tasks.show', $a->task) }}" class="fw-medium text-decoration-none">
                            {{ Str::limit($a->task->title ?? '—', 40) }}
                        </a>
                        @if($a->task->category ?? false)
                            <br><small class="text-muted">{{ $a->task->category->name }}</small>
                        @endif
                    </td>
                    <td>{{ $a->assignee->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-priority-{{ $a->effective_priority }}">
                            {{ ucfirst($a->effective_priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-status-{{ $a->status }}">
                            {{ str_replace('_', ' ', ucfirst($a->status)) }}
                        </span>
                        @if($a->isOverdue())
                            <span class="badge bg-danger">Overdue</span>
                        @endif
                    </td>
                    <td>
                        {{ $a->deadline?->format('M d, Y') ?? '—' }}
                        @if($a->deadline)
                            <br><small class="text-muted">{{ $a->deadline->diffForHumans() }}</small>
                        @endif
                    </td>
                    <td><small>{{ $a->assigned_at?->format('M d, Y') }}</small></td>
                    <td><small>{{ $a->assignedBy->name ?? '—' }}</small></td>
                    <td>
                        <a href="{{ route('admin.assignments.show', $a) }}" class="btn btn-sm btn-outline-primary" title="View Assignment">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.transfers.create', $a->task) }}" class="btn btn-sm btn-outline-warning" title="Transfer">
                            <i class="bi bi-arrow-left-right"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No assignments found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assignments->hasPages())
    <div class="card-footer bg-white">{{ $assignments->links() }}</div>
    @endif
</div>
@endsection
