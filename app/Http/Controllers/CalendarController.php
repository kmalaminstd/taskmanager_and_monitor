<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\PersonalTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $end = Carbon::parse($month)->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        if ($user->isAdmin()) {
            $assignments = TaskAssignment::with(['task', 'assignee'])
                ->where('is_active', true)
                ->whereBetween('deadline', [$start, $end])
                ->get();

            $personalTasks = collect(); // Admin doesn't see personal by default
        } else {
            $assignments = TaskAssignment::with(['task'])
                ->where('assignee_id', $user->id)
                ->where('is_active', true)
                ->whereBetween('deadline', [$start, $end])
                ->get();

            $personalTasks = PersonalTask::where('user_id', $user->id)
                ->whereBetween('deadline', [$start, $end])
                ->get();
        }

        // Build calendar grid
        $days = [];
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $days[$dateStr] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === Carbon::parse($month)->month,
                'isToday' => $current->isToday(),
                'assignments' => $assignments->filter(fn($a) => $a->deadline && $a->deadline->format('Y-m-d') === $dateStr),
                'personal' => $personalTasks->filter(fn($t) => $t->deadline && $t->deadline->format('Y-m-d') === $dateStr),
            ];
            $current->addDay();
        }

        $prevMonth = Carbon::parse($month)->subMonth()->format('Y-m');
        $nextMonth = Carbon::parse($month)->addMonth()->format('Y-m');

        return view('calendar.index', compact('days', 'month', 'prevMonth', 'nextMonth'));
    }
}
