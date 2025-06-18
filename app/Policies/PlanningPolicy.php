<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Planning;

class PlanningPolicy
{
    public function update(User $user, Planning $planning): bool
    {
        return $user->societe->id === $planning->employe->societe_id;
    }

    public function delete(User $user, Planning $planning): bool
    {
        return $user->societe->id === $planning->employe->societe_id;
    }

    public function view(User $user, Planning $planning): bool
    {
        // Employeur can view any planning in their company
        if ($user->isEmployeur()) {
            return $user->societe->id === $planning->employe->societe_id;
        }
        
        // Employee can only view their own plannings
        if ($user->employe) {
            return $user->employe->id === $planning->employe_id;
        }
        
        return false;
    }
} 