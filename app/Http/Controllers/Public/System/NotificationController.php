<?php

namespace App\Http\Controllers\Public\System;

use App\Http\Controllers\Controller;
use App\Models\System\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->when(request('is_read') !== null, function ($query) {
                $query->where('is_read', request('is_read'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('public.notifications.index', compact('notifications'));
    }

    public function show(int $id): View
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
            ]);
        }

        return view('public.notifications.show', compact('notification'));
    }

    public function markAllRead(): RedirectResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('notifications.index')->with('success', 'All notifications marked as read.');
    }
}
