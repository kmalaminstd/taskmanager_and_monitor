@extends('layouts.assignee')

@section('title', 'Edit Personal Task')
@section('page-title', 'Edit Personal Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
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
                            <select name="frequency" class="form-select">
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
