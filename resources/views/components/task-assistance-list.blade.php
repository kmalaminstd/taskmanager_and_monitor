{{--
  Expects:
  - $assistances : collection of TaskAssistanceRequest
  - $canRemove (bool) optional - admin can remove
--}}
@php
    $canRemove = $canRemove ?? false;
    $assistances = $assistances ?? collect();
@endphp

<div class="card border-0 shadow-sm mb-4" id="task-assistance-list">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-1"></i> Assistance</span>
        <span class="badge bg-secondary">{{ $assistances->count() }}</span>
    </div>
    <div class="list-group list-group-flush">
        @forelse($assistances as $req)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                        <span class="badge bg-{{ $req->status === 'completed' ? 'success' : ($req->status === 'accepted' ? 'info' : ($req->status === 'pending' ? 'warning' : 'secondary')) }}">
                            {{ ucfirst($req->status) }}
                        </span>
                        <small class="text-muted">{{ $req->created_at?->format('M d, Y H:i') }}</small>
                        @if($req->requester)
                            <small class="text-muted">· For: {{ $req->requester->name }}</small>
                        @endif
                    </div>
                    <div class="small mb-2">{{ $req->description }}</div>
                    <div class="small">
                        <strong>Helpers:</strong>
                        @forelse($req->users as $helper)
                            <span class="badge bg-light text-dark border">
                                {{ $helper->name }}
                                <span class="text-muted">({{ $helper->pivot->status ?? 'pending' }})</span>
                            </span>
                        @empty
                            <span class="text-muted">None</span>
                        @endforelse
                    </div>
                </div>
                <div class="d-flex flex-column gap-1">
                    @if(auth()->user()->isAdmin() || auth()->user()->isAssignee())
                        @php
                            $detailRoute = auth()->user()->isAdmin()
                                ? route('admin.assistance.show', $req)
                                : route('assignee.assistance.show', $req);
                        @endphp
                        <a href="{{ $detailRoute }}" class="btn btn-sm btn-outline-primary">View</a>
                    @endif
                    @if($canRemove && auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.assistance.destroy', $req) }}"
                              onsubmit="return confirm('Remove this assistance request?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="list-group-item text-center text-muted py-4">No assistance on this task yet</div>
        @endforelse
    </div>
</div>
