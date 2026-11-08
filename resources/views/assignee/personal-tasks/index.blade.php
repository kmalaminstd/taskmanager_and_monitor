@extends('layouts.assignee')

@section('title', 'My Personal Tasks')
@section('page-title', 'My Personal Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach(['pending','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('assignee.personal-tasks.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> New Personal Task
    </a>
</div>

<div class="row g-3">
    @forelse($tasks as $task)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 {{ $task->status === 'completed' ? 'opacity-75' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge badge-priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'secondary' }}">
                        {{ ucfirst($task->status) }}
                    </span>
                </div>
                <h6 class="{{ $task->status === 'completed' ? 'text-decoration-line-through' : '' }}">
                    {{ $task->title }}
                </h6>
                @if($task->description)
                    <p class="small text-muted mb-2">{{ Str::limit($task->description, 70) }}</p>
                @endif
                <div class="small text-muted">
                    @if($task->deadline)
                        <i class="bi bi-calendar"></i> {{ $task->deadline->format('M d, Y') }}
                    @endif
                    <span class="ms-2"><i class="bi bi-arrow-repeat"></i> {{ str_replace('_',' ', ucfirst($task->frequency)) }}</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 d-flex gap-1">
                @if($task->status !== 'completed')
                    <form method="POST" action="{{ route('assignee.personal-tasks.complete', $task) }}">
                        @csrf
                        <button class="btn btn-sm btn-success" title="Mark Complete"><i class="bi bi-check-lg"></i></button>
                    </form>
                @endif
                <a href="{{ route('assignee.personal-tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('assignee.personal-tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-journal-plus fs-1 d-block mb-2"></i>
        No personal tasks yet. Create one!
    </div>
    @endforelse
</div>

@if($tasks->hasPages())
<div class="mt-4">{{ $tasks->links('vendor.pagination.bootstrap-5') }}</div>
@endif
@endsection
