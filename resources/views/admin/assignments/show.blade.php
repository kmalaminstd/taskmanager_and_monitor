@extends('layouts.admin')

@section('title', 'Assignment Details')
@section('page-title', 'Assignment Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Assignments
    </a>
    <a href="{{ route('admin.tasks.show', $assignment->task) }}" class="btn btn-outline-primary btn-sm">
        View Parent Task
    </a>
    <a href="{{ route('admin.transfers.create', $assignment->task) }}" class="btn btn-warning btn-sm">
        <i class="bi bi-arrow-left-right"></i> Transfer
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h4>{{ $assignment->task->title }}</h4>
                <div class="mb-3">
                    <span class="badge badge-status-{{ $assignment->status }}">
                        {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                    </span>
                    <span class="badge badge-priority-{{ $assignment->effective_priority }}">
                        {{ ucfirst($assignment->effective_priority) }}
                    </span>
                    @if($assignment->isOverdue())
                        <span class="badge bg-danger">Overdue</span>
                    @endif
                    @if(!$assignment->is_active)
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>

                @if($assignment->task->description)
                    <p class="text-muted">{{ $assignment->task->description }}</p>
                @endif

                <div class="row small">
                    <div class="col-sm-6">
                        <strong>Assignee:</strong> {{ $assignment->assignee->name ?? '—' }}<br>
                        <strong>Assigned By:</strong> {{ $assignment->assignedBy->name ?? '—' }}<br>
                        <strong>Assigned At:</strong> {{ $assignment->assigned_at?->format('M d, Y H:i') }}
                    </div>
                    <div class="col-sm-6">
                        <strong>Deadline:</strong> {{ $assignment->deadline?->format('M d, Y') ?? '—' }}<br>
                        <strong>Started:</strong> {{ $assignment->started_at?->format('M d, Y H:i') ?? '—' }}<br>
                        <strong>Submitted:</strong> {{ $assignment->submitted_at?->format('M d, Y H:i') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

        @if($assignment->submissions->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Submissions</div>
            <ul class="list-group list-group-flush">
                @foreach($assignment->submissions as $sub)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-{{ $sub->status === 'approved' ? 'success' : ($sub->status === 'returned' ? 'danger' : 'warning') }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                            <div class="mt-1">{{ $sub->completion_details }}</div>
                            @if($sub->return_note)
                                <div class="text-danger small mt-1">Return note: {{ $sub->return_note }}</div>
                            @endif
                        </div>
                        <small class="text-muted">{{ $sub->created_at->format('M d, H:i') }}</small>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($assignment->histories->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Activity History</div>
            <ul class="list-group list-group-flush">
                @foreach($assignment->histories->sortByDesc('created_at')->take(20) as $h)
                <li class="list-group-item small">
                    <strong>{{ ucfirst(str_replace('_',' ',$h->action)) }}</strong>
                    @if($h->description) — {{ $h->description }} @endif
                    <br>
                    <span class="text-muted">{{ $h->user->name ?? 'System' }} · {{ $h->created_at->diffForHumans() }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($assignment->notes->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Notes</div>
            <ul class="list-group list-group-flush">
                @foreach($assignment->notes as $note)
                <li class="list-group-item small">
                    <div>{{ $note->note }}</div>
                    <div class="text-muted">{{ $note->user->name ?? '—' }} · {{ $note->created_at->diffForHumans() }}</div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection
