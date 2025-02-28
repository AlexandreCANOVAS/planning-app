<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employe;

class EmployePolicy
{
    /**
     * Determine if the user can view the employee's stats
     */
    public function view(User $user, Employe $employe): bool
    {
        // Allow access if:
        // 1. The user is from the same company
        // 2. The user is an admin (has a null societe_id)
        return $user->societe_id === $employe->societe_id || $user->societe_id === null;
    }

    /**
     * Determine if the user can update the employee
     */
    public function update(User $user, Employe $employe): bool
    {
        return $user->societe_id === $employe->societe_id || $user->societe_id === null;
    }

    /**
     * Determine if the user can delete the employee
     */
    public function delete(User $user, Employe $employe): bool
    {
        return $user->societe_id === $employe->societe_id || $user->societe_id === null;
    }
}