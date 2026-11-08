@extends('layouts.assignee')

@section('title', 'My Tasks')
@section('page-title', 'My Assigned Tasks')

@section('content')
<div class="mb-4">
    <form method="GET" class="row g-2">
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending','in_progress','submitted','completed','returned','overdue'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_',' ', ucfirst($s)) }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

<div class="row g-3">
    @forelse($assignments as $assignment)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 {{ $assignment->isOverdue() ? 'border-danger' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge badge-priority-{{ $assignment->effective_priority }}">
                        {{ ucfirst($assignment->effective_priority) }}
                    </span>
                    <span class="badge badge-status-{{ $assignment->status }}">
                        {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                    </span>
                </div>
                <h6 class="card-title">
                    <a href="{{ route('assignee.tasks.show', $assignment) }}" class="text-decoration-none">
                        {{ Str::limit($assignment->task->title, 50) }}
                    </a>
                </h6>
                <p class="card-text small text-muted mb-2">
                    {{ Str::limit($assignment->task->description, 80) }}
                </p>
                <div class="small text-muted">
                    <div><i class="bi bi-calendar"></i> 
                        {{ $assignment->deadline ? $assignment->deadline->format('M d, Y') : 'No deadline' }}
                        @if($assignment->isOverdue())
                            <span class="text-danger fw-medium"> (Overdue)</span>
                        @endif
                    </div>
                    <div><i class="bi bi-person"></i> Assigned by {{ $assignment->assignedBy->name ?? '—' }}</div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="{{ route('assignee.tasks.show', $assignment) }}" class="btn btn-sm btn-outline-primary w-100">
                    View Details
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No tasks found.
        </div>
    </div>
    @endforelse
</div>

@if($assignments->hasPages())
<div class="mt-4">
    {{ $assignments->links('vendor.pagination.bootstrap-5') }}
</div>
@endif
@endsection
