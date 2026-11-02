<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

        public function markAsRead($id)
        {
            $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
            $notification->markAsRead();

            $url = $notification->data['url'] ?? null;
            return $url ? redirect($url) : back();
        }

    // public function markAsRead($id)
    // {
    //     $notification = auth()->user()
    //         ->notifications()
    //         ->where('id', $id)
    //         ->firstOrFail();

    //     $notification->markAsRead();

    //     $url = $notification->data['url'] ?? null;

    //     if ($url && str_starts_with($url, '/') && !str_starts_with($url, '//')) {
    //         return redirect($url);
    //     }

    //     return redirect()->route('notifications.index');
    // }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }
}
