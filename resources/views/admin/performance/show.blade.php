@extends('layouts.admin')

@section('title', $user->name . ' - Performance')
@section('page-title', $user->name . ' - Performance')

@section('content')
@include('components.period-filter', ['period' => $period ?? request('period')])
<div class="mb-3">
    <a href="{{ route('admin.performance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Performance
    </a>
</div>

<!-- Filters -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from?->format('Y-m-d') }}">
    </div>
    <div class="col-auto">
        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to?->format('Y-m-d') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('admin.performance.show', $user) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Assignments</div>
            <div class="fs-3 fw-bold">{{ $stats['assigned'] ?? $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completed</div>
            <div class="fs-3 fw-bold text-success">{{ $stats['completed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">Completion Rate</div>
            <div class="fs-3 fw-bold">{{ $stats['completion_rate'] }}%</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="text-muted small">On-Time Rate</div>
            <div class="fs-3 fw-bold">{{ $stats['on_time_rate'] }}%</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold">{{ $stats['pending'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold">{{ $stats['in_progress'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Submitted</div>
            <div class="fs-4 fw-bold">{{ $stats['submitted'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Returned</div>
            <div class="fs-4 fw-bold">{{ $stats['returned'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Overdue</div>
            <div class="fs-4 fw-bold text-danger">{{ $stats['overdue'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center">
            <div class="text-muted small">Late Completed</div>
            <div class="fs-4 fw-bold">{{ $stats['late'] }}</div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Status Distribution</div>
            <div class="card-body">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Completion Trend (6 months)</div>
            <div class="card-body">
                <canvas id="trendChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const statusData = @json($chartData['status']);
const trendData = @json($chartData['trend']);

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.replace('_', ' ')),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#6366F1', '#3B82F6', '#F59E0B', '#10B981', '#EF4444', '#94A3B8']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.labels || [],
        datasets: [{
            label: 'Completed',
            data: trendData.data || [],
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endpush
