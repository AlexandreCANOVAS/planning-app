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
        return $user->societe->id === $planning->employe->societe_id;
    }
} 