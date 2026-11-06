@php $period = $period ?? request('period', 'this_month'); @endphp
<form method="GET" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <label class="form-label small mb-0">Period</label>
        <select name="period" class="form-select form-select-sm" onchange="this.form.querySelectorAll('.custom-date').forEach(e => e.classList.toggle('d-none', this.value !== 'custom')); if(this.value !== 'custom') this.form.submit();">
            <option value="all" @selected($period==='all')>All time</option>
            <option value="today" @selected($period==='today')>Today</option>
            <option value="this_week" @selected($period==='this_week')>This week</option>
            <option value="this_month" @selected($period==='this_month')>This month</option>
            <option value="last_month" @selected($period==='last_month')>Last month</option>
            <option value="custom" @selected($period==='custom')>Custom range</option>
        </select>
    </div>
    <div class="col-auto custom-date {{ $period === 'custom' ? '' : 'd-none' }}">
        <label class="form-label small mb-0">From</label>
        <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
    </div>
    <div class="col-auto custom-date {{ $period === 'custom' ? '' : 'd-none' }}">
        <label class="form-label small mb-0">To</label>
        <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Apply</button>
    </div>
</form>
