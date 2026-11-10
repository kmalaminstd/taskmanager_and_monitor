<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function complete(Request $request, ReminderService $reminders)
    {
        $data = $request->validate([
            'type' => ['required', 'in:personal,assigned'],
            'id' => ['required', 'integer'],
            'frequency' => ['required', 'in:one_time,daily,weekly,monthly'],
        ]);

        $reminders->markComplete(auth()->id(), $data['type'], $data['id'], $data['frequency']);

        return back()->with('success', 'Marked as done for this period.');
    }
}
