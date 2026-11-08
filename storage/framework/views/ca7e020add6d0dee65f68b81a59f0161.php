<?php $period = $period ?? request('period', 'this_month'); ?>
<form method="GET" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <label class="form-label small mb-0">Period</label>
        <select name="period" class="form-select form-select-sm" onchange="this.form.querySelectorAll('.custom-date').forEach(e => e.classList.toggle('d-none', this.value !== 'custom')); if(this.value !== 'custom') this.form.submit();">
            <option value="all" <?php if($period==='all'): echo 'selected'; endif; ?>>All time</option>
            <option value="today" <?php if($period==='today'): echo 'selected'; endif; ?>>Today</option>
            <option value="this_week" <?php if($period==='this_week'): echo 'selected'; endif; ?>>This week</option>
            <option value="this_month" <?php if($period==='this_month'): echo 'selected'; endif; ?>>This month</option>
            <option value="last_month" <?php if($period==='last_month'): echo 'selected'; endif; ?>>Last month</option>
            <option value="custom" <?php if($period==='custom'): echo 'selected'; endif; ?>>Custom range</option>
        </select>
    </div>
    <div class="col-auto custom-date <?php echo e($period === 'custom' ? '' : 'd-none'); ?>">
        <label class="form-label small mb-0">From</label>
        <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e(request('from')); ?>">
    </div>
    <div class="col-auto custom-date <?php echo e($period === 'custom' ? '' : 'd-none'); ?>">
        <label class="form-label small mb-0">To</label>
        <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e(request('to')); ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-primary">Apply</button>
    </div>
</form>
<?php /**PATH /mnt/newvolume5/TaskMasterTest/TaskManager Main/taskmaster main/resources/views/components/period-filter.blade.php ENDPATH**/ ?>