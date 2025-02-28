<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lieu;

class LieuPolicy
{
    public function update(User $user, Lieu $lieu): bool
    {
        return $user->societe->id === $lieu->societe_id;
    }

    public function delete(User $user, Lieu $lieu): bool
    {
        return $user->societe->id === $lieu->societe_id;
    }
} 