<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\StoreNotificationRequest;
use App\Http\Requests\Admin\System\UpdateNotificationRequest;
use App\Models\System\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::with('user')
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when(request('is_read') !== null, function ($query) {
                $query->where('is_read', request('is_read'));
            })
            ->latest()
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('admin.system.notifications.index', compact('notifications', 'users'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.system.notifications.create', compact('users'));
    }

    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        Notification::create($request->validated());

        return redirect()
            ->route('admin.system.notifications.index')
            ->with('success', 'Notification created successfully');
    }

    public function show(Notification $notification): View
    {
        return view('admin.system.notifications.show', [
            'notification' => $notification->load('user'),
        ]);
    }

    public function edit(Notification $notification): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.system.notifications.edit', compact('notification', 'users'));
    }

    public function update(UpdateNotificationRequest $request, Notification $notification): RedirectResponse
    {
        $notification->update($request->validated());

        return redirect()
            ->route('admin.system.notifications.index')
            ->with('success', 'Notification updated successfully');
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()
            ->route('admin.system.notifications.index')
            ->with('success', 'Notification deleted successfully');
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $notification->update(['is_read' => true]);

        return redirect()
            ->back()
            ->with('success', 'Notification marked as read');
    }
}
