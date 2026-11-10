@php
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('task_subtasks');
    $subtasks = $tableExists ? $task->subtasks()->with('assignee')->get() : collect();
    $progress = $tableExists ? $task->subtaskProgress() : ['total' => 0, 'completed' => 0, 'percent' => 0];
@endphp

<div class="card border-0 shadow-sm mb-4" id="task-subtasks">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-1"></i> Checklist / Subtasks</span>
        @if($progress['total'] > 0)
            <span class="small text-muted">{{ $progress['completed'] }} / {{ $progress['total'] }} — {{ $progress['percent'] }}%</span>
        @endif
    </div>
    <div class="card-body">
        @if(!$tableExists)
            <div class="alert alert-warning mb-0">
                Subtasks table is missing. Run: <code>php artisan migrate</code>
            </div>
        @else
            @if($progress['total'] > 0)
            <div class="progress mb-3" style="height:10px;">
                <div class="progress-bar bg-success" style="width:{{ $progress['percent'] }}%"></div>
            </div>
            @endif

            <ul class="list-group list-group-flush mb-3">
                @forelse($subtasks as $st)
                <li class="list-group-item px-0 d-flex align-items-start gap-2">
                    <form method="POST" action="{{ route('tasks.subtasks.toggle', $st) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $st->status === 'completed' ? 'btn-success' : 'btn-outline-secondary' }}">
                            <i class="bi bi-{{ $st->status === 'completed' ? 'check-square' : 'square' }}"></i>
                        </button>
                    </form>
                    <div class="flex-grow-1">
                        <div class="{{ $st->status === 'completed' ? 'text-decoration-line-through text-muted' : 'fw-medium' }}">
                            {{ $st->title }}
                        </div>
                        @if($st->description)
                            <div class="small text-muted">{{ $st->description }}</div>
                        @endif
                        <div class="small text-muted">
                            @if($st->assignee) {{ $st->assignee->name }} · @endif
                            @if($st->deadline) Due {{ $st->deadline->format('M d') }} · @endif
                            {{ ucfirst(str_replace('_',' ', $st->status)) }}
                        </div>
                    </div>
                    @if(auth()->user()->canMonitor())
                    <form method="POST" action="{{ route('tasks.subtasks.destroy', $st) }}" onsubmit="return confirm('Delete subtask?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </li>
                @empty
                <li class="list-group-item px-0 text-muted small">No subtasks yet.</li>
                @endforelse
            </ul>

            @if(auth()->user()->canMonitor())
            <form method="POST" action="{{ route('tasks.subtasks.store', $task) }}" class="row g-2">
                @csrf
                <div class="col-md-5">
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="New subtask title" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="deadline" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <select name="assignee_id" class="form-select select2-enable form-select-sm">
                        <option value="">Assignee</option>
                        @foreach(\App\Models\User::where('role','assignee')->where('status','active')->orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-primary w-100">Add</button>
                </div>
            </form>
            @endif
        @endif
    </div>
</div>
