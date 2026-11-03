@extends('layouts.assignee')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="text-muted small">Assigned</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_assigned'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass"></i></div>
                <div>
                    <div class="text-muted small">Pending</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-play-circle"></i></div>
                <div>
                    <div class="text-muted small">In Progress</div>
                    <div class="fs-4 fw-bold">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Completed</div>
                    <div class="fs-4 fw-bold">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">Overdue</div>
                    <div class="fs-4 fw-bold">{{ $stats['overdue'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-day"></i></div>
                <div>
                    <div class="text-muted small">Due Today</div>
                    <div class="fs-4 fw-bold">{{ $stats['due_today'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-send"></i></div>
                <div>
                    <div class="text-muted small">Submitted</div>
                    <div class="fs-4 fw-bold">{{ $stats['submitted'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal"></i></div>
                <div>
                    <div class="text-muted small">Personal</div>
                    <div class="fs-4 fw-bold">{{ $stats['personal_tasks'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Recent Assigned Tasks</div>
            <div class="list-group list-group-flush">
                @forelse($recentAssignments as $assignment)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-medium">{{ $assignment->task->title ?? '—' }}</div>
                            <small class="text-muted">
                                Assigned by {{ $assignment->assignedBy->name ?? '—' }} · 
                                {{ $assignment->assigned_at?->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : ($assignment->status === 'pending' ? 'secondary' : 'primary') }}">
                            {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">No assigned tasks yet</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-calendar-event me-1"></i> Upcoming Deadlines
            </div>
            <div class="list-group list-group-flush">
                @forelse($upcomingDeadlines as $assignment)
                <div class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-medium">{{ Str::limit($assignment->task->title ?? '—', 30) }}</div>
                        <small class="text-muted">{{ ucfirst($assignment->status) }}</small>
                    </div>
                    <div class="text-end">
                        <div class="small fw-medium">{{ $assignment->deadline?->format('M d') }}</div>
                        <small class="text-muted">{{ $assignment->deadline?->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">No upcoming deadlines</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
