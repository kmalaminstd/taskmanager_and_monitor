<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function __construct(protected PerformanceService $performanceService) {}

    public function index(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : null;

        $stats = $this->performanceService->getUserPerformance(auth()->id(), $from, $to);
        $chartData = $this->performanceService->getChartData(auth()->id());

        return view('assignee.performance.index', compact('stats', 'chartData', 'from', 'to'));
    }
}
