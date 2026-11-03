@extends(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isSupervisor() ? 'layouts.supervisor' : 'layouts.assignee'))

@section('title', 'Calendar')
@section('page-title', 'Task Calendar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group">
        <a href="{{ route('calendar.index', ['month' => $prevMonth]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-left"></i>
        </a>
        <a href="{{ route('calendar.index', ['month' => now()->format('Y-m')]) }}" class="btn btn-outline-secondary btn-sm">Today</a>
        <a href="{{ route('calendar.index', ['month' => $nextMonth]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>
    <h5 class="mb-0">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</h5>
    <div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered mb-0 calendar-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Sun</th>
                    <th class="text-center">Mon</th>
                    <th class="text-center">Tue</th>
                    <th class="text-center">Wed</th>
                    <th class="text-center">Thu</th>
                    <th class="text-center">Fri</th>
                    <th class="text-center">Sat</th>
                </tr>
            </thead>
            <tbody>
                @php $chunks = collect($days)->chunk(7); @endphp
                @foreach($chunks as $week)
                <tr>
                    @foreach($week as $dateStr => $day)
                    <td class="calendar-day {{ !$day['isCurrentMonth'] ? 'bg-light text-muted' : '' }} {{ $day['isToday'] ? 'today' : '' }}" style="height:110px;vertical-align:top;width:14.28%;">
                        <div class="fw-medium small mb-1">{{ $day['date']->day }}</div>
                        @foreach($day['assignments']->take(3) as $a)
                            <div class="badge bg-primary text-truncate d-block mb-1" style="max-width:100%;font-size:0.65rem;" title="{{ $a->task->title ?? '' }}">
                                {{ Str::limit($a->task->title ?? '', 18) }}
                            </div>
                        @endforeach
                        @foreach($day['personal']->take(2) as $p)
                            <div class="badge bg-secondary text-truncate d-block mb-1" style="max-width:100%;font-size:0.65rem;" title="{{ $p->title }}">
                                {{ Str::limit($p->title, 18) }}
                            </div>
                        @endforeach
                        @if($day['assignments']->count() + $day['personal']->count() > 5)
                            <small class="text-muted">+ more</small>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .calendar-day.today { background: #EFF6FF !important; }
    .calendar-table td { padding: 0.4rem; }
</style>
@endsection
