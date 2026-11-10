@extends('layouts.assignee')

@section('title', 'New Personal Task')
@section('page-title', 'Create Personal Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('assignee.personal-tasks.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" id="frequency" class="form-select">
                                <option value="one_time">One Time</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                        </div>
                    </div>

                    <div class="row" id="recurrence_options">
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
                            <div class="form-text">Default is 1 day before deadline.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Task</button>
                        <a href="{{ route('assignee.personal-tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleRecurrence() {
    const f = document.getElementById('frequency').value;
    document.getElementById('weekday_wrap').style.display = f === 'weekly' ? 'block' : 'none';
    document.getElementById('monthday_wrap').style.display = f === 'monthly' ? 'block' : 'none';
}
document.getElementById('frequency').addEventListener('change', toggleRecurrence);
toggleRecurrence();
</script>
@endpush
