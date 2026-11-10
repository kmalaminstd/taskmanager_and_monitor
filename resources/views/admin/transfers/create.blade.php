@extends('layouts.admin')

@section('title', 'Transfer Task')
@section('page-title', 'Direct Task Transfer')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-1">{{ $task->title }}</h5>
                <p class="text-muted mb-0">Admin Direct Transfer — no approval required</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Transfer Details</div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label text-muted">Current Active Assignees</label>
                    <div>
                        @forelse($task->activeAssignments as $a)
                            <span class="badge bg-primary me-1 mb-1">{{ $a->assignee->name }} ({{ str_replace('_',' ', $a->status) }})</span>
                        @empty
                            <span class="text-muted">None</span>
                        @endforelse
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.transfers.store', $task) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">New Assignee(s) <span class="text-danger">*</span></label>
                        <select name="new_assignee_ids[]" class="form-select select2-enable @error('new_assignee_ids') is-invalid @enderror" multiple size="7" required>
                            @foreach($assignees as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Search and select one or more. These will become the new active assignees.</div>
                        @error('new_assignee_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="keep_existing" value="1" id="keep_existing" class="form-check-input"
                               {{ old('keep_existing') ? 'checked' : '' }}>
                        <label for="keep_existing" class="form-check-label">
                            Keep existing assignees (only add the selected ones)
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transfer Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Additional Notes (optional)</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Important:</strong> Previous assignees will be deactivated (history preserved). 
                        New assignees start with status <strong>Pending</strong>.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Confirm this transfer? This action cannot be undone.')">
                            <i class="bi bi-arrow-left-right"></i> Confirm Transfer
                        </button>
                        <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
