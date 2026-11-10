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
                            <select name="category_id" class="form-select select2-enable" data-placeholder="Select category">
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
                                <option value="one_time" @selected(old('task_type', 'one_time') === 'one_time')>One-time (single task)</option>
                                <option value="recurring" @selected(old('task_type') === 'recurring')>Recurring (daily / weekly / monthly)</option>
                            </select>
                            <div class="form-text">Choose <strong>One-time</strong> for a single assignment, or <strong>Recurring</strong> for repeating work.</div>
                        </div>
                        <div class="col-md-6 mb-3" id="frequency_wrapper" style="display:none;">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" id="frequency" class="form-select">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="weekday_wrap" style="display:none;">
                            <label class="form-label">Day of week <span class="text-muted small">(optional)</span></label>
                            <select name="recurrence_weekday" class="form-select">
                                <option value="">— Not set —</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                                <option value="0">Sunday</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="monthday_wrap" style="display:none;">
                            <label class="form-label">Day of month <span class="text-muted small">(optional)</span></label>
                            <select name="recurrence_monthday" class="form-select">
                                <option value="">— Not set —</option>
                                @for($d=1;$d<=31;$d++)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Remind days before deadline</label>
                            <input type="number" name="reminder_days_before" class="form-control" value="1" min="0" max="30">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select name="assignee_ids[]" class="form-select select2-enable @error('assignee_ids') is-invalid @enderror" multiple required data-placeholder="Search and select assignees...">
                            @foreach($assignees as $user)
                                <option value="{{ $user->id }}" @selected(collect(old('assignee_ids', []))->contains($user->id))>
                                    {{ $user->name }}@if(isset($workloads[$user->id])) — {{ $workloads[$user->id]['active_tasks'] }} active ({{ $workloads[$user->id]['label'] }})@endif ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Search and select one or more assignees. Each gets an independent assignment.</div>
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
(function () {
    function syncTaskTypeUI() {
        var typeSelect = document.getElementById('task_type');
        var freqWrapper = document.getElementById('frequency_wrapper');
        var weekdayWrap = document.getElementById('weekday_wrap');
        var monthdayWrap = document.getElementById('monthday_wrap');
        var freq = document.getElementById('frequency');
        if (!typeSelect || !freqWrapper) return;

        var isRecurring = typeSelect.value === 'recurring';
        freqWrapper.style.display = isRecurring ? '' : 'none';

        var f = freq ? freq.value : '';
        if (weekdayWrap) weekdayWrap.style.display = (isRecurring && f === 'weekly') ? '' : 'none';
        if (monthdayWrap) monthdayWrap.style.display = (isRecurring && f === 'monthly') ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var typeSelect = document.getElementById('task_type');
        var freq = document.getElementById('frequency');

        if (typeSelect) {
            typeSelect.addEventListener('change', syncTaskTypeUI);
            // Select2 support (if applied)
            if (window.jQuery) {
                jQuery(typeSelect).on('change select2:select', syncTaskTypeUI);
            }
        }
        if (freq) {
            freq.addEventListener('change', syncTaskTypeUI);
            if (window.jQuery) {
                jQuery(freq).on('change select2:select', syncTaskTypeUI);
            }
        }
        syncTaskTypeUI();
    });
})();
</script>
@endpush

