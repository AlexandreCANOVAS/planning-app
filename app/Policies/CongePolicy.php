<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conge;

class CongePolicy
{
    public function update(User $user, Conge $conge): bool
    {
        return $user->societe->id === $conge->employe->societe_id;
    }

    public function delete(User $user, Conge $conge): bool
    {
        return $user->societe->id === $conge->employe->societe_id;
    }
} 