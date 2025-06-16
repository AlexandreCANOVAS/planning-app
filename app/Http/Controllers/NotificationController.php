<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }
    
    public function getUnreadNotifications()
    {
        $unreadNotifications = auth()->user()->unreadNotifications;
        return response()->json([
            'count' => $unreadNotifications->count(),
            'notifications' => $unreadNotifications->take(5)->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at
                ];
            })
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marquée comme lue');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }
} 