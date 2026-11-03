@extends('layouts.supervisor')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-list-task"></i></div>
                <div>
                    <div class="text-muted small">Total Tasks</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_tasks'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-people"></i></div>
                <div>
                    <div class="text-muted small">Assigned</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_assigned'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Completed</div>
                    <div class="fs-4 fw-bold">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="text-muted small">Pending</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-play-circle"></i></div>
                <div>
                    <div class="text-muted small">In Progress</div>
                    <div class="fs-4 fw-bold">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">Overdue</div>
                    <div class="fs-4 fw-bold">{{ $stats['overdue'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-day"></i></div>
                <div>
                    <div class="text-muted small">Due Today</div>
                    <div class="fs-4 fw-bold">{{ $stats['due_today']  }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-inbox"></i></div>
                <div>
                    <div class="text-muted small">Pending Approval</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_approvals'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Recent Tasks</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Assignees</th>
                                <th>Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTasks as $task)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ Str::limit($task->title, 40) }}</div>
                                    <small class="text-muted">by {{ $task->creator->name ?? '—' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                                </td>
                                <td>
                                    @foreach($task->activeAssignments->take(3) as $a)
                                        <span class="badge bg-light text-dark">{{ $a->assignee->name ?? '—' }}</span>
                                    @endforeach
                                    @if($task->activeAssignments->count() > 3)
                                        <span class="badge bg-secondary">+{{ $task->activeAssignments->count() - 3 }}</span>
                                    @endif
                                </td>
                                <td>{{ $task->deadline ? $task->deadline->format('M d, Y') : '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No tasks yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold text-danger">
                <i class="bi bi-exclamation-triangle me-1"></i> Overdue Assignments
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($overdueAssignments as $assignment)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-medium">{{ Str::limit($assignment->task->title ?? '—', 35) }}</div>
                                <small class="text-muted">{{ $assignment->assignee->name ?? '—' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger">{{ $assignment->deadline?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4">No overdue tasks 🎉</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
