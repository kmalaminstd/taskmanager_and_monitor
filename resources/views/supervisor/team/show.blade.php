@extends('layouts.supervisor')

@section('title', $user->name . ' - Profile')
@section('page-title', 'User Profile')

@section('content')
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route('supervisor.team.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
    <a href="{{ route('supervisor.team.show', $user) }}" class="btn btn-primary btn-sm">
    </a>
    <a href="{{ route('supervisor.performance.show', $user) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-bar-chart"></i> Performance
    </a>
</div>

<!-- Profile Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:64px;height:64px;font-size:1.5rem;background:linear-gradient(135deg,#2563EB,#1D4ED8);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1">{{ $user->name }}</h4>
                <div class="text-muted small">
                    <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                    <span class="mx-2">·</span>
                    <span class="badge bg-{{ $user->role === 'admin' ? 'dark' : 'primary' }}">{{ ucfirst($user->role) }}</span>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span>
                </div>
                <div class="text-muted small mt-1">
                    Joined {{ $user->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Tasks Overview -->
<div class="mb-2">
    <h6 class="fw-semibold text-muted text-uppercase" style="font-size:0.75rem;letter-spacing:0.05em;">
        Admin Assigned Tasks
    </h6>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Total</div>
            <div class="fs-4 fw-bold">{{ $assignedStats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold">{{ $assignedStats['pending'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold">{{ $assignedStats['in_progress'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Submitted</div>
            <div class="fs-4 fw-bold">{{ $assignedStats['submitted'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-4 fw-bold text-success">{{ $assignedStats['completed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-4 fw-bold text-danger">{{ $assignedStats['overdue'] }}</div>
        </div>
    </div>
</div>

<!-- Personal Tasks Overview -->
<div class="mb-2">
    <h6 class="fw-semibold text-muted text-uppercase" style="font-size:0.75rem;letter-spacing:0.05em;">
        Personal Tasks
    </h6>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Total</div>
            <div class="fs-4 fw-bold">{{ $personalStats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold">{{ $personalStats['pending'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold">{{ $personalStats['in_progress'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-4 fw-bold text-success">{{ $personalStats['completed'] }}</div>
        </div>
    </div>
</div>

<!-- Tabs: Assigned Tasks + Personal Tasks -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#assignedTab" type="button">
            Assigned Tasks <span class="badge bg-primary ms-1">{{ $assignedStats['total'] }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#personalTab" type="button">
            Personal Tasks <span class="badge bg-secondary ms-1">{{ $personalStats['total'] }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Assigned Tasks Tab -->
    <div class="tab-pane fade show active" id="assignedTab">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Assigned</th>
                            <th>Assigned By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignedTasks as $a)
                        <tr class="{{ $a->isOverdue() ? 'table-danger' : '' }}">
                            <td>
                                <a href="{{ route('supervisor.tasks.show', $a->task_id) }}" class="fw-medium">
                                    {{ Str::limit($a->task->title ?? '—', 40) }}
                                </a>
                                @if($a->task->category ?? false)
                                    <br><small class="text-muted">{{ $a->task->category->name }}</small>
                                @endif
                            </td>
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
                                @if(!$a->is_active)
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{ $a->deadline?->format('M d, Y') ?? '—' }}
                            </td>
                            <td><small>{{ $a->assigned_at?->format('M d, Y') }}</small></td>
                            <td><small>{{ $a->assignedBy->name ?? '—' }}</small></td>
                            <td>
                                <a href="{{ route('supervisor.tasks.show', $a) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No assigned tasks</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assignedTasks->hasPages())
            <div class="card-footer bg-white">
                {{ $assignedTasks->appends(request()->except('assigned_page'))->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Personal Tasks Tab -->
    <div class="tab-pane fade" id="personalTab">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Frequency</th>
                            <th>Deadline</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personalTasks as $pt)
                        <tr class="{{ $pt->status === 'completed' ? 'opacity-75' : '' }}">
                            <td>
                                <span class="fw-medium {{ $pt->status === 'completed' ? 'text-decoration-line-through' : '' }}">
                                    {{ $pt->title }}
                                </span>
                                @if($pt->description)
                                    <br><small class="text-muted">{{ Str::limit($pt->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-priority-{{ $pt->priority }}">
                                    {{ ucfirst($pt->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $pt->status === 'completed' ? 'success' : ($pt->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($pt->status) }}
                                </span>
                            </td>
                            <td><small>{{ str_replace('_', ' ', ucfirst($pt->frequency)) }}</small></td>
                            <td>{{ $pt->deadline?->format('M d, Y') ?? '—' }}</td>
                            <td>{{ $pt->completed_at?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No personal tasks</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($personalTasks->hasPages())
            <div class="card-footer bg-white">
                {{ $personalTasks->appends(request()->except('personal_page'))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
