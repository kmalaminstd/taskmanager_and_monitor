@extends('layouts.assignee')

@section('title', 'Edit Personal Task')
@section('page-title', 'Edit Personal Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('assignee.personal-tasks.update', $personalTask) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $personalTask->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $personalTask->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                @foreach(['high','medium','low'] as $p)
                                    <option value="{{ $p }}" @selected($personalTask->priority === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" id="frequency" class="form-select">
                                @foreach(['one_time','daily','weekly','monthly'] as $f)
                                    <option value="{{ $f }}" @selected($personalTask->frequency === $f)>{{ str_replace('_',' ', ucfirst($f)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','in_progress','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" @selected($personalTask->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" value="{{ $personalTask->deadline?->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3" id="weekday_wrap" style="display:none;">
                            <label class="form-label">Day of week (optional)</label>
                            <select name="recurrence_weekday" class="form-select">
                                <option value="">— Not set —</option>
                                @foreach([1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',0=>'Sunday'] as $v=>$label)
                                    <option value="{{ $v }}" @selected((string)$personalTask->recurrence_weekday === (string)$v)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="monthday_wrap" style="display:none;">
                            <label class="form-label">Day of month (optional)</label>
                            <select name="recurrence_monthday" class="form-select">
                                <option value="">— Not set —</option>
                                @for($d=1;$d<=31;$d++)
                                    <option value="{{ $d }}" @selected((int)$personalTask->recurrence_monthday === $d)>{{ $d }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Remind days before deadline</label>
                            <input type="number" name="reminder_days_before" class="form-control" value="{{ old('reminder_days_before', $personalTask->reminder_days_before ?? 1) }}" min="0" max="30">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
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
