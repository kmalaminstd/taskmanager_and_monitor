@extends('layouts.admin')

@section('title', 'Workload')
@section('page-title', 'Employee Workload')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">High Workload</div>
            <div class="fs-3 fw-bold text-danger">{{ $summary['high'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Normal</div>
            <div class="fs-3 fw-bold text-warning">{{ $summary['normal'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Low / Available</div>
            <div class="fs-3 fw-bold text-success">{{ $summary['low'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Active Tasks</div>
            <div class="fs-3 fw-bold">{{ $summary['total_active'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card text-center">
            <div class="text-muted small">Total Overdue</div>
            <div class="fs-3 fw-bold text-danger">{{ $summary['total_overdue'] }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Active Tasks</th>
                    <th>Overdue</th>
                    <th>Workload</th>
                    <th width="120"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($workloads as $row)
                <tr>
                    <td class="fw-medium">{{ $row['user']->name }}</td>
                    <td>{{ $row['active_tasks'] }}</td>
                    <td>
                        @if($row['overdue_tasks'] > 0)
                            <span class="text-danger fw-semibold">{{ $row['overdue_tasks'] }}</span>
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $row['color'] }}">{{ $row['label'] }}</span>
                        <div class="progress mt-1" style="height:6px;width:100px;">
                            @php
                                $pct = min(100, ($row['active_tasks'] / 12) * 100);
                            @endphp
                            <div class="progress-bar bg-{{ $row['color'] }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.workload.show', $row['user']) }}" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
