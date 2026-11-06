<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PerformanceService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function __construct(protected PerformanceService $performanceService) {}

    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        [$from, $to] = $this->performanceService->resolvePeriod(
            $period,
            $request->get('from'),
            $request->get('to')
        );

        $employees = $this->performanceService->getAllEmployeesPerformance($from, $to);
        $overall = $this->performanceService->getUserPerformance(null, $from, $to);
        $chartData = $this->performanceService->getChartData();

        return view('admin.performance.index', compact('employees', 'overall', 'chartData', 'from', 'to', 'period'));
    }

    public function show(User $user, Request $request)
    {
        if ($user->role !== 'assignee') {
            abort(404);
        }

        $period = $request->get('period', 'this_month');
        [$from, $to] = $this->performanceService->resolvePeriod(
            $period,
            $request->get('from'),
            $request->get('to')
        );

        $stats = $this->performanceService->getUserPerformance($user->id, $from, $to);
        $chartData = $this->performanceService->getChartData($user->id);

        return view('admin.performance.show', compact('user', 'stats', 'chartData', 'from', 'to', 'period'));
    }
}
