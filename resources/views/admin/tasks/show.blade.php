@extends('layouts.admin')

@section('title', $task->title)
@section('page-title', 'Task Details')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.transfers.create', $task) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-arrow-left-right"></i> Transfer
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-1">{{ $task->title }}</h4>
                <div class="mb-3">
                    <span class="badge badge-priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                    @if($task->category)
                        <span class="badge bg-light text-dark">{{ $task->category->name }}</span>
                    @endif
                    <span class="badge bg-secondary">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span>
                </div>

                @if($task->description)
                    <p class="text-muted">{{ $task->description }}</p>
                @endif

                @if($task->instructions)
                    <div class="alert alert-light border">
                        <strong>Instructions:</strong><br>
                        {{ $task->instructions }}
                    </div>
                @endif

                <div class="row text-muted small">
                    <div class="col-sm-4"><strong>Created by:</strong> {{ $task->creator->name ?? '—' }}</div>
                    <div class="col-sm-4"><strong>Deadline:</strong> {{ $task->deadline?->format('M d, Y H:i') ?? '—' }}</div>
                    <div class="col-sm-4"><strong>Created:</strong> {{ $task->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Assignments -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Assignments ({{ $task->assignments->where('is_active', true)->count() }} active)</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Submitted</th>
                            <th>Deadline</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->assignments->sortByDesc('is_active') as $assignment)
                        <tr class="{{ !$assignment->is_active ? 'table-secondary' : '' }}">
                            <td>
                                {{ $assignment->assignee->name ?? '—' }}
                                @if(!$assignment->is_active)
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-status-{{ $assignment->status }}">
                                    {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                                </span>
                                @if($assignment->isOverdue())
                                    <span class="badge bg-danger">Overdue</span>
                                @endif
                            </td>
                            <td>{{ $assignment->started_at?->format('M d, H:i') ?? '—' }}</td>
                            <td>{{ $assignment->submitted_at?->format('M d, H:i') ?? '—' }}</td>
                            <td>{{ $assignment->deadline?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                @if($assignment->is_active && !in_array($assignment->status, ['completed', 'cancelled']))
                                <button type="button" class="btn btn-sm btn-outline-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#adminAssistModal{{ $assignment->id }}">
                                    <i class="bi bi-people"></i> Add Assistance
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History -->
        @if($task->histories->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Activity History</div>
            <ul class="list-group list-group-flush">
                @foreach($task->histories->sortByDesc('created_at')->take(15) as $history)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $history->action)) }}</strong>
                            @if($history->description)
                                — {{ $history->description }}
                            @endif
                            <br>
                            <small class="text-muted">by {{ $history->user->name ?? 'System' }}</small>
                        </div>
                        <small class="text-muted">{{ $history->created_at->diffForHumans() }}</small>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Assistance list --}}
@php
    $assistances = $task->assistanceRequests ?? $task->assistanceRequests()->with(['users','requester','assignment.assignee'])->latest()->get();
@endphp
@include('components.task-assistance-list', ['assistances' => $assistances, 'canRemove' => true])
    </div>

    <div class="col-lg-4">
        @if($task->attachments->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Attachments</div>
            <ul class="list-group list-group-flush">
                @foreach($task->attachments as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-paperclip"></i> {{ $file->original_name }}</span>
                    <small class="text-muted">{{ number_format($file->file_size / 1024, 1) }} KB</small>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($task->transferHistories->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Transfer History</div>
            <ul class="list-group list-group-flush">
                @foreach($task->transferHistories->sortByDesc('transferred_at') as $transfer)
                <li class="list-group-item small">
                    <div>
                        {{ $transfer->previousAssignee->name ?? '—' }}
                        <i class="bi bi-arrow-right"></i>
                        {{ $transfer->newAssignee->name ?? '—' }}
                    </div>
                    <div class="text-muted">
                        {{ $transfer->transfer_type === 'admin_direct' ? 'Admin Direct' : 'Request Approved' }}
                        · {{ $transfer->transferred_at->format('M d, Y') }}
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>



{{-- Checklist & Discussion --}}
<div class="row mt-4">
    <div class="col-12">
        @include('components.task-subtasks', ['task' => $task])
        @include('components.task-discussion', ['task' => $task])
    </div>
</div>





{{-- Admin assign assistance modals --}}
@foreach($task->assignments->where('is_active', true) as $assignment)
@if(!in_array($assignment->status, ['completed', 'cancelled']))
<div class="modal fade" id="adminAssistModal{{ $assignment->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.assistance.store-for-assignment', [$task, $assignment]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Assistance for {{ $assignment->assignee->name ?? 'Assignee' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">Task: <strong>{{ $task->title }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Select Helpers <span class="text-danger">*</span></label>
                        <select name="helper_ids[]" class="form-select select2-enable" multiple required data-placeholder="Search team members...">
                            @foreach(\App\Models\User::whereIn('role', ['assignee', 'supervisor'])->where('status', 'active')->where('id', '!=', $assignment->assignee_id)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Search and select one or more people to help this assignee.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions / why help is needed <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Describe what the helpers should do..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white">Assign Assistance</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach

@endsection
