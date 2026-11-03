@extends(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isSupervisor() ? 'layouts.supervisor' : 'layouts.assignee'))

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    @if(auth()->user()->unreadNotifications->count())
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button class="btn btn-sm btn-outline-primary">Mark all as read</button>
    </form>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <ul class="list-group list-group-flush">
        @forelse($notifications as $notification)
        <li class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-medium">
                        @if(!$notification->read_at)
                            <span class="badge bg-primary me-1">New</span>
                        @endif
                        {{ $notification->data['title'] ?? 'Notification' }}
                    </div>
                    <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                    <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <div>
                    @if(!empty($notification->data['url']))
                        <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-outline-primary">
                            View
                        </a>
                    @else
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">Mark read</button>
                        </form>
                    @endif
                </div>
            </div>
        </li>
        @empty
        <li class="list-group-item text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            No notifications yet
        </li>
        @endforelse
    </ul>
    @if($notifications->hasPages())
    <div class="card-footer bg-white">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
