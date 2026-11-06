@extends('layouts.admin')

@section('title', $user->name . ' Workload')
@section('page-title', $user->name . ' — Workload')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.workload.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Active Tasks</div>
            <div class="fs-2 fw-bold">{{ $workload['active_tasks'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-2 fw-bold text-danger">{{ $workload['overdue_tasks'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="text-muted small">Workload Level</div>
            <div class="fs-2"><span class="badge bg-{{ $workload['color'] }} fs-5">{{ $workload['label'] }}</span></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Active Assignments</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $a)
                <tr class="{{ $a->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <a href="{{ route('admin.tasks.show', $a->task_id) }}">{{ Str::limit($a->task->title ?? '—', 40) }}</a>
                    </td>
                    <td><span class="badge badge-status-{{ $a->status }}">{{ str_replace('_',' ', $a->status) }}</span></td>
                    <td><span class="badge badge-priority-{{ $a->effective_priority }}">{{ $a->effective_priority }}</span></td>
                    <td>{{ $a->deadline?->format('M d, Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No active assignments</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
