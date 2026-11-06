{{-- Task discussion / comments --}}
@php
    $hasParentCol = \Illuminate\Support\Facades\Schema::hasColumn('task_notes', 'parent_id');
    $notesQuery = $task->notes()->with(['user']);
    if ($hasParentCol) {
        $notesQuery->whereNull('parent_id')->with(['replies.user', 'attachments']);
        if (\Illuminate\Support\Facades\Schema::hasColumn('task_notes', 'is_pinned')) {
            $notesQuery->orderByDesc('is_pinned');
        }
    }
    $notes = $notesQuery->orderBy('created_at')->get();
@endphp

<div class="card border-0 shadow-sm mb-4" id="task-discussion">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-chat-dots me-1"></i> Discussion</span>
        <span class="badge bg-secondary">{{ $notes->count() }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('tasks.comments.store', $task) }}" enctype="multipart/form-data" class="mb-4">
            @csrf
            <textarea name="note" class="form-control mb-2" rows="3" required placeholder="Write a comment..."></textarea>
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <input type="file" name="attachments[]" class="form-control form-control-sm" style="max-width:260px" multiple>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-send"></i> Post Comment
                </button>
            </div>
        </form>

        @forelse($notes as $note)
        <div class="border rounded p-3 mb-3 {{ ($note->is_pinned ?? false) ? 'border-warning bg-light' : '' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $note->user->name ?? 'User' }}</strong>
                    @if($note->is_pinned ?? false)
                        <span class="badge bg-warning text-dark">Pinned</span>
                    @endif
                    <span class="text-muted small ms-1">{{ $note->created_at->diffForHumans() }}
                        @if($note->edited_at ?? false) (edited) @endif
                    </span>
                </div>
                <div class="d-flex gap-1">
                    @if(auth()->user()->canMonitor())
                    <form method="POST" action="{{ route('tasks.comments.pin', $note) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-warning" title="Pin/Unpin"><i class="bi bi-pin-angle"></i></button>
                    </form>
                    @endif
                    @if($note->user_id === auth()->id() || auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('tasks.comments.destroy', $note) }}" onsubmit="return confirm('Delete this comment?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="mt-2">{!! nl2br(e($note->note)) !!}</div>

            @if(isset($note->attachments) && $note->attachments->count())
            <div class="mt-2">
                @foreach($note->attachments as $att)
                    <a href="{{ asset('storage/'.$att->file_path) }}" target="_blank" class="badge bg-light text-dark text-decoration-none me-1">
                        <i class="bi bi-paperclip"></i> {{ $att->original_name }}
                    </a>
                @endforeach
            </div>
            @endif

            @if($hasParentCol && $note->relationLoaded('replies'))
                @foreach($note->replies as $reply)
                <div class="ms-3 mt-2 border-start ps-3">
                    <strong class="small">{{ $reply->user->name ?? '' }}</strong>
                    <span class="text-muted small">{{ $reply->created_at->diffForHumans() }}</span>
                    <div class="small">{{ $reply->note }}</div>
                </div>
                @endforeach

                <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="mt-2 ms-3">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $note->id }}">
                    <div class="input-group input-group-sm">
                        <input type="text" name="note" class="form-control" placeholder="Reply..." required>
                        <button class="btn btn-outline-primary">Reply</button>
                    </div>
                </form>
            @endif
        </div>
        @empty
        <p class="text-muted text-center mb-0">No comments yet. Start the discussion.</p>
        @endforelse
    </div>
</div>
