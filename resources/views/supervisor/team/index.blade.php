@extends('layouts.supervisor')

@section('title', 'Team')
@section('page-title', 'Team Members')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('supervisor.team.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td class="fw-medium">{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>
                        <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </td>
                    <td>{{ $member->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('supervisor.team.show', $member) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Profile
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No team members found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
    <div class="card-footer bg-white">{{ $members->links() }}</div>
    @endif
</div>
@endsection
