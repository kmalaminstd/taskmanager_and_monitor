@extends('layouts.assignee')

@section('title', 'Assistance Details')
@section('page-title', 'Assistance Details')

@section('content')
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route('assignee.assistance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    @if($isRequester && !in_array($assistance->status, ['completed','cancelled']))
        <form method="POST" action="{{ route('assignee.assistance.cancel', $assistance) }}" onsubmit="return confirm('Cancel this entire assistance request?')">
            @csrf
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Cancel Request</button>
        </form>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="mb-0">{{ $assistance->task->title ?? '—' }}</h5>
                    <span class="badge bg-{{ $assistance->status === 'pending' ? 'warning' : ($assistance->status === 'accepted' ? 'info' : ($assistance->status === 'completed' ? 'success' : 'secondary')) }}">
                        {{ ucfirst($assistance->status) }}
                    </span>
                </div>
                <p class="mb-3">{{ $assistance->description }}</p>
                <div class="small text-muted">
                    <div><strong>Requested by:</strong> {{ $assistance->requester->name ?? '—' }}</div>
                    <div><strong>Date:</strong> {{ $assistance->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Helper: accept / decline --}}
        @if($isHelper && $myPivot && $myPivot->status === 'pending')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Your Response</div>
            <div class="card-body d-flex gap-2">
                <form method="POST" action="{{ route('assignee.assistance.respond', $assistance) }}">
                    @csrf
                    <input type="hidden" name="action" value="accepted">
                    <button class="btn btn-success"><i class="bi bi-check-lg"></i> Accept</button>
                </form>
                <form method="POST" action="{{ route('assignee.assistance.respond', $assistance) }}">
                    @csrf
                    <input type="hidden" name="action" value="declined">
                    <button class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> Decline</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Helper: submit work after accept --}}
        @if($isHelper && $myPivot && $myPivot->status === 'accepted')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Submit Your Assistance Work</div>
            <div class="card-body">
                @if($myPivot->requester_note)
                    <div class="alert alert-warning small">
                        <strong>Feedback from assignee:</strong> {{ $myPivot->requester_note }}
                    </div>
                @endif
                <form method="POST" action="{{ route('assignee.assistance.submit-work', $assistance) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Work note / what you did <span class="text-danger">*</span></label>
                        <textarea name="helper_note" class="form-control" rows="4" required placeholder="Describe the help you provided..."></textarea>
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-send"></i> Submit for Review</button>
                </form>
            </div>
        </div>
        @endif

        @if($isHelper && $myPivot && $myPivot->status === 'submitted')
        <div class="alert alert-info">
            You submitted your work. Waiting for the task assignee to review.
            @if($myPivot->helper_note)
                <div class="mt-2 small"><strong>Your note:</strong> {{ $myPivot->helper_note }}</div>
            @endif
        </div>
        @endif

        @if($isHelper && $myPivot && $myPivot->status === 'completed')
        <div class="alert alert-success">Your assistance was approved and marked complete.</div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Helpers</div>
            <ul class="list-group list-group-flush">
                @foreach($assistance->users as $helper)
                @php $st = $helper->pivot->status; @endphp
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-medium">{{ $helper->name }}</span>
                        <span class="badge bg-{{ $st === 'completed' ? 'success' : ($st === 'submitted' ? 'primary' : ($st === 'accepted' ? 'info' : ($st === 'declined' || $st === 'rejected' ? 'danger' : 'warning'))) }}">
                            {{ ucfirst($st) }}
                        </span>
                    </div>

                    @if($helper->pivot->helper_note)
                        <div class="small text-muted mb-1"><strong>Helper note:</strong> {{ $helper->pivot->helper_note }}</div>
                    @endif
                    @if($helper->pivot->requester_note)
                        <div class="small text-muted mb-1"><strong>Your note:</strong> {{ $helper->pivot->requester_note }}</div>
                    @endif

                    {{-- Assignee actions on submitted work --}}
                    @if($isRequester && $st === 'submitted')
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        <form method="POST" action="{{ route('assignee.assistance.approve-helper', [$assistance, $helper->id]) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Mark Complete</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#reject-{{ $helper->id }}">
                            Reject with Note
                        </button>
                    </div>
                    <div class="collapse mt-2" id="reject-{{ $helper->id }}">
                        <form method="POST" action="{{ route('assignee.assistance.reject-helper', [$assistance, $helper->id]) }}">
                            @csrf
                            <textarea name="requester_note" class="form-control form-control-sm mb-1" rows="2" required placeholder="What needs to be fixed?"></textarea>
                            <button class="btn btn-sm btn-danger">Send Rejection</button>
                        </form>
                    </div>
                    @endif

                    {{-- Assignee can remove helper --}}
                    @if($isRequester && !in_array($assistance->status, ['completed','cancelled']) && !in_array($st, ['completed']))
                    <form method="POST" action="{{ route('assignee.assistance.remove-helper', [$assistance, $helper->id]) }}" class="mt-2"
                          onsubmit="return confirm('Remove this helper?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-person-dash"></i> Remove</button>
                    </form>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
