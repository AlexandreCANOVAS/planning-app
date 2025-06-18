<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Récupère les notifications de l'utilisateur connecté
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Journaliser la requête pour débogage
        Log::debug('Requête de notifications reçue', [
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'auth_check' => auth()->check(),
            'headers' => $request->headers->all()
        ]);
        
        // Vérifier l'authentification
        if (!auth()->check()) {
            Log::warning('Tentative d\'accès aux notifications sans authentification');
            return response()->json([
                'notifications' => [],
                'unread_count' => 0,
                'auth_status' => 'unauthenticated'
            ], 200); // Renvoyer 200 même si non authentifié pour éviter les erreurs JS
        }
        
        try {
            $user = auth()->user();
            $unreadNotifications = $user->unreadNotifications;
            $allNotifications = $user->notifications()->latest()->take(10)->get();
            
            Log::debug('Notifications récupérées avec succès', [
                'user_id' => $user->id,
                'total_count' => $allNotifications->count(),
                'unread_count' => $unreadNotifications->count()
            ]);
            
            return response()->json([
                'notifications' => $allNotifications->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'data' => $notification->data,
                        'created_at' => $notification->created_at->diffForHumans(),
                        'read_at' => $notification->read_at,
                        'type' => $notification->type
                    ];
                }),
                'unread_count' => $unreadNotifications->count(),
                'auth_status' => 'authenticated',
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, journaliser et renvoyer un tableau vide
            Log::error('Erreur lors de la récupération des notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ]);
        }
    }
}
