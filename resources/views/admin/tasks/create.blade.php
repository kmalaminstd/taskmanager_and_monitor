@extends('layouts.admin')

@section('title', 'Create Task')
@section('page-title', 'Create New Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Task Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tasks.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Task Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="high" @selected(old('priority') === 'high')>High</option>
                                <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                                <option value="low" @selected(old('priority') === 'low')>Low</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                                   value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}">
                            @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Task Type <span class="text-danger">*</span></label>
                            <select name="task_type" id="task_type" class="form-select" required>
                                <option value="one_time" @selected(old('task_type', 'one_time') === 'one_time')>One-time</option>
                                <option value="recurring" @selected(old('task_type') === 'recurring')>Recurring</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="frequency_wrapper" style="display:none;">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" class="form-select">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select name="assignee_ids[]" class="form-select @error('assignee_ids') is-invalid @enderror" multiple size="6" required>
                            @foreach($assignees as $user)
                                <option value="{{ $user->id }}" @selected(collect(old('assignee_ids', []))->contains($user->id))>
                                    {{ $user->name }}@if(isset($workloads[$user->id])) — {{ $workloads[$user->id]['active_tasks'] }} active ({{ $workloads[$user->id]['label'] }})@endif ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl / Cmd to select multiple users. Each will get an independent assignment.</div>
                        @error('assignee_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Instructions</label>
                        <textarea name="instructions" rows="3" class="form-control">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Attachment (optional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                        <div class="form-text">Max 10MB. Allowed: pdf, doc, docx, xls, xlsx, png, jpg, zip</div>
                        @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Create & Assign Task
                        </button>
                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const typeSelect = document.getElementById('task_type');
    const freqWrapper = document.getElementById('frequency_wrapper');
    function toggleFreq() {
        freqWrapper.style.display = typeSelect.value === 'recurring' ? 'block' : 'none';
    }
    typeSelect.addEventListener('change', toggleFreq);
    toggleFreq();
</script>
@endpush
