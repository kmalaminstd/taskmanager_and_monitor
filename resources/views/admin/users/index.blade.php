@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
        <select name="role" class="form-select form-select-sm">
            <option value="">All Roles</option>
            <option value="admin" @selected(request('role')==='admin')>Admin</option>
            <option value="assignee" @selected(request('role')==='assignee')>Assignee</option>
        </select>
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="active" @selected(request('status')==='active')>Active</option>
            <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
        </select>
        <button class="btn btn-sm btn-primary">Filter</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add User
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="fw-medium">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-{{ $user->role === 'admin' ? 'dark' : 'primary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View Profile">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }}" title="Toggle status">
                                <i class="bi bi-{{ $user->status === 'active' ? 'pause' : 'play' }}"></i>
                            </button>
                        </form>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white">{{ $users->links() }}</div>
    @endif
</div>
@endsection
