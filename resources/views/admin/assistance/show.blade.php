@extends('layouts.admin')

@section('title', 'Assistance Details')
@section('page-title', 'Assistance Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.assistance.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5>{{ $assistance->task->title ?? '—' }}</h5>
                <span class="badge bg-secondary mb-3">{{ ucfirst($assistance->status) }}</span>
                <p>{{ $assistance->description }}</p>
                <div class="small text-muted">
                    Requester: {{ $assistance->requester->name ?? '—' }} ·
                    {{ $assistance->created_at->format('M d, Y H:i') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Helpers</div>
            <ul class="list-group list-group-flush">
                @foreach($assistance->users as $helper)
                <li class="list-group-item d-flex justify-content-between">
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
