@extends('layouts.admin')

@section('title', 'Task Monitoring')
@section('page-title', 'Task Monitoring')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Task
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.tasks.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search task name..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','in_progress','submitted','completed','returned','cancelled','overdue'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ str_replace('_', ' ', ucfirst($status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    @foreach(['high','medium','low'] as $p)
                        <option value="{{ $p }}" @selected(($filters['priority'] ?? '') === $p)>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="assignee_id" class="form-select select2-enable">
                    <option value="">All Assignees</option>
                    @foreach($assignees as $user)
                        <option value="{{ $user->id }}" @selected(($filters['assignee_id'] ?? '') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                <tr class="{{ $assignment->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <a href="{{ route('admin.tasks.show', $assignment->task) }}" class="text-decoration-none fw-medium">
                            {{ Str::limit($assignment->task->title ?? '—', 45) }}
                        </a>
                        @if($assignment->task->category)
                            <br><small class="text-muted">{{ $assignment->task->category->name }}</small>
                        @endif
                    </td>
                    <td>{{ $assignment->assignee->name ?? '—' }}</td>
                    <td>
                        @php $prio = $assignment->effective_priority; @endphp
                        <span class="badge badge-priority-{{ $prio }}">{{ ucfirst($prio) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-status-{{ $assignment->status }}">
                            {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                        </span>
                        @if($assignment->isOverdue())
                            <span class="badge bg-danger ms-1">Overdue</span>
                        @endif
                    </td>
                    <td>
                        {{ $assignment->deadline ? $assignment->deadline->format('M d, Y') : '—' }}
                        @if($assignment->deadline)
                            <br><small class="text-muted">{{ $assignment->deadline->diffForHumans() }}</small>
                        @endif
                    </td>
                    <td>
                        <small>{{ $assignment->assigned_at?->format('M d, Y') }}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.tasks.show', $assignment->task) }}" class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.tasks.edit', $assignment->task) }}" class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No assignments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assignments->hasPages())
    <div class="card-footer bg-white">
        {{ $assignments->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
