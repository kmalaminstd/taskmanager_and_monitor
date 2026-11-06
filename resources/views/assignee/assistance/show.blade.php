@extends('layouts.assignee')

@section('title', 'Assistance Details')
@section('page-title', 'Assistance Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('assignee.assistance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="mb-0">{{ $assistance->task->title ?? '—' }}</h5>
                    <span class="badge bg-{{ $assistance->status === 'pending' ? 'warning' : ($assistance->status === 'accepted' ? 'success' : ($assistance->status === 'completed' ? 'primary' : 'secondary')) }}">
                        {{ ucfirst($assistance->status) }}
                    </span>
                </div>
                <p class="text-muted mb-3">{{ $assistance->description }}</p>
                <div class="small text-muted">
                    <div><strong>Requested by:</strong> {{ $assistance->requester->name ?? '—' }}</div>
                    <div><strong>Date:</strong> {{ $assistance->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>

        @if($isHelper && $myPivotStatus === 'pending')
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
        @elseif($isHelper)
        <div class="alert alert-info">
            You have <strong>{{ $myPivotStatus }}</strong> this request.
        </div>
        @endif

        @if($isRequester && in_array($assistance->status, ['accepted', 'pending']))
        <form method="POST" action="{{ route('assignee.assistance.complete', $assistance) }}" class="mb-4">
            @csrf
            <button class="btn btn-primary" onclick="return confirm('Mark assistance as completed?')">
                <i class="bi bi-flag"></i> Mark Assistance Completed
            </button>
        </form>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Helpers</div>
            <ul class="list-group list-group-flush">
                @foreach($assistance->users as $helper)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $helper->name }}</span>
                    <span class="badge bg-{{ $helper->pivot->status === 'accepted' ? 'success' : ($helper->pivot->status === 'declined' ? 'danger' : 'warning') }}">
                        {{ ucfirst($helper->pivot->status) }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
