@extends('layouts.admin')

@section('title', 'Edit Task')
@section('page-title', 'Edit Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Edit: {{ $task->title }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tasks.update', $task) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Task Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $task->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                @foreach(['high','medium','low'] as $p)
                                    <option value="{{ $p }}" @selected(old('priority', $task->priority) === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select select2-enable">
                                <option value="">— None —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $task->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control"
                                   value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assignees (active)</label>
                        <select name="assignee_ids[]" class="form-select select2-enable" multiple size="6">
                            @foreach($assignees as $user)
                                <option value="{{ $user->id }}"
                                    @selected(collect(old('assignee_ids', $currentAssigneeIds))->contains($user->id))>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Changing this will add/remove active assignments. History is preserved.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" rows="3" class="form-control">{{ old('instructions', $task->instructions) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Add Attachment</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Task</button>
                        <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
