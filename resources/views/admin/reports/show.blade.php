@extends('layouts.admin')

@section('title', 'Report')
@section('page-title', ucfirst(str_replace('_', ' ', $type)) . ' Report')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> All Reports
    </a>
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}">
        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}">
        @if(in_array($type, ['employee','completed','overdue','late']))
        <select name="user_id" class="form-select select2-enable form-select-sm">
            <option value="">All Employees</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" @selected($userId == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        @endif
        <button class="btn btn-sm btn-primary">Apply</button>
        <a href="{{ route('admin.reports.generate', request()->all() + ['export' => 'csv']) }}" class="btn btn-sm btn-success">
            <i class="bi bi-download"></i> CSV
        </a>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($type === 'overall')
            <div class="row g-3">
                @foreach($data as $key => $value)
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="text-muted small">{{ str_replace('_', ' ', ucfirst($key)) }}</div>
                        <div class="fs-3 fw-bold">{{ $value }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @elseif(in_array($type, ['employee', 'completed', 'overdue', 'late']))
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr>
                            <td>{{ Str::limit($row->task->title ?? '—', 40) }}</td>
                            <td>{{ $row->assignee->name ?? '—' }}</td>
                            <td><span class="badge badge-status-{{ $row->status }}">{{ $row->status }}</span></td>
                            <td>{{ $row->deadline?->format('M d, Y') }}</td>
                            <td>{{ $row->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($type === 'transfers')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Task</th><th>From</th><th>To</th><th>Type</th><th>By</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr>
                            <td>{{ Str::limit($row->task->title ?? '—', 30) }}</td>
                            <td>{{ $row->previousAssignee->name ?? '—' }}</td>
                            <td>{{ $row->newAssignee->name ?? '—' }}</td>
                            <td>{{ $row->transfer_type }}</td>
                            <td>{{ $row->transferredBy->name ?? '—' }}</td>
                            <td>{{ $row->transferred_at?->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No transfers</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($type === 'extensions')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Task</th><th>Requester</th><th>Current</th><th>Requested</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr>
                            <td>{{ Str::limit($row->assignment->task->title ?? '—', 30) }}</td>
                            <td>{{ $row->requester->name ?? '—' }}</td>
                            <td>{{ $row->current_deadline?->format('M d, Y') }}</td>
                            <td>{{ $row->requested_deadline?->format('M d, Y') }}</td>
                            <td><span class="badge bg-{{ $row->status === 'approved' ? 'success' : ($row->status === 'rejected' ? 'danger' : 'warning') }}">{{ $row->status }}</span></td>
                            <td>{{ $row->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No extension requests</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
