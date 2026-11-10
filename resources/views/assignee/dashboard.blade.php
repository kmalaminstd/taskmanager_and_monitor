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
</div>

{{-- Today's Reminders --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-semibold"><i class="bi bi-bell me-1 text-warning"></i> Today's Reminders</h6>
    <span class="badge bg-primary">{{ $recurringDone }}/{{ $recurringTotal }} completed this period</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-briefcase me-1"></i> Assigned Tasks</span>
                <span class="badge bg-primary">{{ $reminderData['assigned']->count() }}</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($reminderData['assigned'] as $item)
                <div class="list-group-item {{ !empty($item['done_this_period']) ? 'opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <a href="{{ $item['url'] }}" class="fw-medium text-decoration-none">{{ $item['title'] }}</a>
                            <div class="small text-muted">
                                @switch($item['reason'])
                                    @case('daily') Daily work @break
                                    @case('weekly_day')
                                    @case('weekly_default') Weekly work @break
                                    @case('monthly_day')
                                    @case('monthly_default') Monthly work @break
                                    @case('deadline_today') <span class="text-danger">Deadline today</span> @break
                                    @case('deadline_soon') Deadline approaching @break
                                    @case('overdue') <span class="text-danger">Overdue</span> @break
                                    @default Reminder
                                @endswitch
                                · {{ ucfirst(str_replace('_',' ', $item['frequency'])) }}
                                @if($item['deadline']) · Due {{ \Carbon\Carbon::parse($item['deadline'])->format('M d') }} @endif
                            </div>
                        </div>
                        <div>
                            @if(!empty($item['done_this_period']))
                                <span class="badge bg-success">Done</span>
                            @elseif(in_array($item['frequency'], ['daily','weekly','monthly']))
                                <form method="POST" action="{{ route('assignee.reminders.complete') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="assigned">
                                    <input type="hidden" name="id" value="{{ $item['source']->id }}">
                                    <input type="hidden" name="frequency" value="{{ $item['frequency'] }}">
                                    <button class="btn btn-sm btn-outline-success">Mark done</button>
                                </form>
                            @else
                                <a href="{{ $item['url'] }}" class="btn btn-sm btn-outline-primary">Open</a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">No assigned reminders today</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-plus me-1"></i> Personal Tasks</span>
                <span class="badge bg-secondary">{{ $reminderData['personal']->count() }}</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($reminderData['personal'] as $item)
                <div class="list-group-item {{ !empty($item['done_this_period']) ? 'opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <a href="{{ $item['url'] }}" class="fw-medium text-decoration-none">{{ $item['title'] }}</a>
                            <div class="small text-muted">
                                @switch($item['reason'])
                                    @case('daily') Daily work @break
                                    @case('weekly_day')
                                    @case('weekly_default') Weekly work @break
                                    @case('monthly_day')
                                    @case('monthly_default') Monthly work @break
                                    @case('deadline_today') <span class="text-danger">Deadline today</span> @break
                                    @case('deadline_soon') Deadline approaching (1 day before) @break
                                    @case('overdue') <span class="text-danger">Overdue</span> @break
                                    @default Reminder
                                @endswitch
                                · {{ ucfirst(str_replace('_',' ', $item['frequency'])) }}
                                @if($item['deadline']) · Due {{ \Carbon\Carbon::parse($item['deadline'])->format('M d') }} @endif
                            </div>
                        </div>
                        <div>
                            @if(!empty($item['done_this_period']))
                                <span class="badge bg-success">Done</span>
                            @else
                                <form method="POST" action="{{ route('assignee.reminders.complete') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="personal">
                                    <input type="hidden" name="id" value="{{ $item['source']->id }}">
                                    <input type="hidden" name="frequency" value="{{ $item['frequency'] }}">
                                    <button class="btn btn-sm btn-outline-success">Mark done</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">No personal reminders today</div>
                @endforelse
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
