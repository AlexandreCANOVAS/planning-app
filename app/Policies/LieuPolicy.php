<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lieu;

class LieuPolicy
{
    /**
     * Vérifie si l'utilisateur peut voir un lieu
     */
    public function view(User $user, Lieu $lieu): bool
    {
        // Vérifie si l'utilisateur est un employeur et a une société
        if (!$user->isEmployeur() || !$user->societe) {
            return false;
        }
        
        // Vérifie si le lieu appartient à la société de l'utilisateur ou est un lieu spécial
        return $user->societe->id === $lieu->societe_id || $lieu->is_special;
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour un lieu
     */
    public function update(User $user, Lieu $lieu): bool
    {
        // Vérifie si l'utilisateur est un employeur et a une société
        if (!$user->isEmployeur() || !$user->societe) {
            return false;
        }
        
        // Vérifie si le lieu appartient à la société de l'utilisateur
        return $user->societe->id === $lieu->societe_id;
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un lieu
     */
    public function delete(User $user, Lieu $lieu): bool
    {
        // Vérifie si l'utilisateur est un employeur et a une société
        if (!$user->isEmployeur() || !$user->societe) {
            return false;
        }
        
        // Vérifie si le lieu appartient à la société de l'utilisateur
        return $user->societe->id === $lieu->societe_id;
    }
} 