@extends('layouts.assignee')

@section('title', 'Assistance')
@section('page-title', 'Assistance Requests')

@section('content')
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#incoming">Incoming</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#outgoing">My Requests</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="incoming">
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                @forelse($incoming as $req)
                <a href="{{ route('assignee.assistance.show', $req) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-medium">{{ $req->task->title ?? '—' }}</div>
                            <small class="text-muted">From {{ $req->requester->name ?? '—' }} · {{ $req->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-{{ $req->status === 'pending' ? 'warning' : ($req->status === 'accepted' ? 'success' : 'secondary') }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="list-group-item text-center text-muted py-4">No incoming assistance requests</div>
                @endforelse
            </div>
            @if($incoming->hasPages())
            <div class="card-footer bg-white">{{ $incoming->links() }}</div>
            @endif
        </div>
    </div>

    <div class="tab-pane fade" id="outgoing">
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                @forelse($outgoing as $req)
                <a href="{{ route('assignee.assistance.show', $req) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-medium">{{ $req->task->title ?? '—' }}</div>
                            <small class="text-muted">
                                Helpers: {{ $req->users->pluck('name')->join(', ') ?: '—' }}
                                · {{ $req->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $req->status === 'pending' ? 'warning' : ($req->status === 'accepted' ? 'success' : 'secondary') }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="list-group-item text-center text-muted py-4">You have not requested assistance yet</div>
                @endforelse
            </div>
            @if($outgoing->hasPages())
            <div class="card-footer bg-white">{{ $outgoing->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
