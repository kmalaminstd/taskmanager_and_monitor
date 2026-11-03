@extends('layouts.admin')

@section('title', 'Approvals')
@section('page-title', 'Approval Management')

@section('content')
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#submissions" type="button">
            Submissions <span class="badge bg-primary">{{ $pendingSubmissions->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#extensions" type="button">
            Extensions <span class="badge bg-warning text-dark">{{ $pendingExtensions->count() }}</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transfers" type="button">
            Transfers <span class="badge bg-info">{{ $pendingTransfers->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Submissions -->
    <div class="tab-pane fade show active" id="submissions">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pending Task Submissions</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Submitted</th>
                            <th>Details</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSubmissions as $submission)
                        <tr>
                            <td>
                                <a href="{{ route('admin.tasks.show', $submission->assignment->task) }}">
                                    {{ Str::limit($submission->assignment->task->title ?? '—', 40) }}
                                </a>
                            </td>
                            <td>{{ $submission->assignment->assignee->name ?? '—' }}</td>
                            <td>{{ $submission->created_at->format('M d, H:i') }}</td>
                            <td>
                                <small>{{ Str::limit($submission->completion_details, 60) }}</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.approvals.submissions.approve', $submission) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this submission?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#returnModal{{ $submission->id }}">
                                    <i class="bi bi-arrow-return-left"></i> Return
                                </button>

                                <!-- Return Modal -->
                                <div class="modal fade" id="returnModal{{ $submission->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('admin.approvals.submissions.return', $submission) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Return Submission</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label">Return Note <span class="text-danger">*</span></label>
                                                    <textarea name="return_note" class="form-control" rows="3" required placeholder="Explain what needs to be fixed..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Return to Assignee</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No pending submissions</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Extensions -->
    <div class="tab-pane fade" id="extensions">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pending Extension Requests</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Requester</th>
                            <th>Current</th>
                            <th>Requested</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingExtensions as $ext)
                        <tr>
                            <td>{{ Str::limit($ext->assignment->task->title ?? '—', 30) }}</td>
                            <td>{{ $ext->requester->name ?? '—' }}</td>
                            <td>{{ $ext->current_deadline?->format('M d, Y') }}</td>
                            <td class="fw-medium">{{ $ext->requested_deadline?->format('M d, Y') }}</td>
                            <td><small>{{ Str::limit($ext->reason, 50) }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.approvals.extensions.approve', $ext) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success" onclick="return confirm('Approve extension?')">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.approvals.extensions.reject', $ext) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject extension?')">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No pending extension requests</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfers -->
    <div class="tab-pane fade" id="transfers">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pending Transfer Requests</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingTransfers as $tr)
                        <tr>
                            <td>{{ Str::limit($tr->assignment->task->title ?? '—', 30) }}</td>
                            <td>{{ $tr->requester->name ?? '—' }}</td>
                            <td>{{ $tr->newAssignee->name ?? '—' }}</td>
                            <td><small>{{ Str::limit($tr->reason, 50) }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.approvals.transfers.approve', $tr) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success" onclick="return confirm('Approve transfer?')">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.approvals.transfers.reject', $tr) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject transfer?')">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No pending transfer requests</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
