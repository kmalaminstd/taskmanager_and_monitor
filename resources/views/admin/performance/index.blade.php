@extends('layouts.admin')

@section('title', 'Performance')
@section('page-title', 'Performance Monitoring')

@section('content')
@include('components.period-filter', ['period' => $period ?? request('period')])
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
        <a href="{{ route('admin.performance.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Overall Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Total Assignments</div>
            <div class="fs-3 fw-bold">{{ $overall['assigned'] ?? $overall['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Completed</div>
            <div class="fs-3 fw-bold text-success">{{ $overall['completed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Completion Rate</div>
            <div class="fs-3 fw-bold">{{ $overall['completion_rate'] }}%</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">On-Time Rate</div>
            <div class="fs-3 fw-bold">{{ $overall['on_time_rate'] }}%</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
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

<!-- Employee Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Employee Performance</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Total</th>
                    <th>Completed</th>
                    <th>On-Time</th>
                    <th>Late</th>
                    <th>Overdue</th>
                    <th>Completion %</th>
                    <th>On-Time %</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $row)
                <tr>
                    <td class="fw-medium">{{ $row['user']->name }}</td>
                    <td>{{ $row['stats']['total'] }}</td>
                    <td>{{ $row['stats']['completed'] }}</td>
                    <td>{{ $row['stats']['on_time'] }}</td>
                    <td>{{ $row['stats']['late'] }}</td>
                    <td>
                        @if($row['stats']['overdue'] > 0)
                            <span class="text-danger fw-medium">{{ $row['stats']['overdue'] }}</span>
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        <div class="progress" style="height:8px;width:80px;display:inline-block;vertical-align:middle;">
                            <div class="progress-bar bg-success" style="width:{{ $row['stats']['completion_rate'] }}%"></div>
                        </div>
                        {{ $row['stats']['completion_rate'] }}%
                    </td>
                    <td>{{ $row['stats']['on_time_rate'] }}%</td>
                    <td>
                        <a href="{{ route('admin.performance.show', $row['user']) }}" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
        labels: Object.keys(statusData).map(s => s.replace('_',' ')),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#6366F1','#3B82F6','#F59E0B','#10B981','#EF4444','#94A3B8']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: [{
            label: 'Completed',
            data: trendData.data,
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
