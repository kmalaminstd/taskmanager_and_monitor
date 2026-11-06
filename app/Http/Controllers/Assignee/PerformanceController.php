<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Services\PerformanceService;
use App\Services\WorkloadService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function __construct(
        protected PerformanceService $performanceService,
        protected WorkloadService $workloadService
    ) {}

    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        [$from, $to] = $this->performanceService->resolvePeriod(
            $period,
            $request->get('from'),
            $request->get('to')
        );

        $stats = $this->performanceService->getUserPerformance(auth()->id(), $from, $to);
        $chartData = $this->performanceService->getChartData(auth()->id());
        $workload = $this->workloadService->forUser(auth()->user());

        return view('assignee.performance.index', compact('stats', 'chartData', 'from', 'to', 'period', 'workload'));
    }
}
