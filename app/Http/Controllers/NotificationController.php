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
        try {
            // Vérifier si l'utilisateur est authentifié
            if (!auth()->check()) {
                return response()->json([
                    'count' => 0,
                    'notifications' => [],
                    'error' => 'Non authentifié',
                    'auth_status' => 'unauthenticated'
                ], 200);
            }
            
            // Si l'utilisateur n'a pas vérifié son email, retourner une réponse vide
            if (!auth()->user()->hasVerifiedEmail()) {
                return response()->json([
                    'count' => 0,
                    'notifications' => [],
                    'auth_status' => 'email_not_verified'
                ], 200);
            }
            
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
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des notifications: ' . $e->getMessage());
            
            return response()->json([
                'count' => 0,
                'notifications' => [],
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ], 500);
        }
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