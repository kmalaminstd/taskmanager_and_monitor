<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $employees = $this->performanceService->getAllEmployeesPerformance($from, $to);
        $overall = $this->performanceService->getUserPerformance(null, $from, $to);
        $chartData = $this->performanceService->getChartData();

        return view('admin.performance.index', compact('employees', 'overall', 'chartData', 'from', 'to'));
    }

    public function show(User $user, Request $request)
    {
        if ($user->role !== 'assignee') {
            abort(404);
        }

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : null;

        $stats = $this->performanceService->getUserPerformance($user->id, $from, $to);
        $chartData = $this->performanceService->getChartData($user->id);

        return view('admin.performance.show', compact('user', 'stats', 'chartData', 'from', 'to'));
    }
}
