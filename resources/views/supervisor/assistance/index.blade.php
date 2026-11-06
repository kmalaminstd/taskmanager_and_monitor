@extends('layouts.supervisor')

@section('title', 'Assistance')
@section('page-title', 'Assistance Requests')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Total</div><div class="fs-4 fw-bold">{{ $counts['total'] }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Pending</div><div class="fs-4 fw-bold">{{ $counts['pending'] }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Accepted</div><div class="fs-4 fw-bold text-success">{{ $counts['accepted'] }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card text-center"><div class="text-muted small">Completed</div><div class="fs-4 fw-bold">{{ $counts['completed'] }}</div></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search task..." value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','accepted','rejected','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-sm btn-primary">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Requester</th>
                    <th>Helpers</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>{{ Str::limit($req->task->title ?? '—', 35) }}</td>
                    <td>{{ $req->requester->name ?? '—' }}</td>
                    <td>
                        @foreach($req->users->take(3) as $h)
                            <span class="badge bg-light text-dark">{{ $h->name }}</span>
                        @endforeach
                        @if($req->users->count() > 3)
                            <span class="badge bg-secondary">+{{ $req->users->count()-3 }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $req->status === 'pending' ? 'warning' : ($req->status === 'accepted' ? 'success' : 'secondary') }}">{{ ucfirst($req->status) }}</span></td>
                    <td><small>{{ $req->created_at->format('M d, Y') }}</small></td>
                    <td><a href="{{ route('supervisor.assistance.show', $req) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No assistance requests</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="card-footer bg-white">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
