<?php

namespace App\Policies;

use App\Models\FichePaie;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FichePaiePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // Les employeurs peuvent voir toutes les fiches de paie
        return $user->role === 'employeur';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FichePaie  $fichePaie
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, FichePaie $fichePaie)
    {
        // Les employeurs peuvent voir toutes les fiches de paie de leur société
        if ($user->role === 'employeur') {
            return $fichePaie->employe->societe_id === $user->societe_id;
        }

        // Les employés ne peuvent voir que leurs propres fiches de paie publiées
        if ($user->role === 'employe') {
            return $fichePaie->employe->user_id === $user->id && $fichePaie->statut === 'publié';
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Seuls les employeurs peuvent créer des fiches de paie
        return $user->role === 'employeur';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FichePaie  $fichePaie
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FichePaie $fichePaie)
    {
        // Seuls les employeurs peuvent mettre à jour les fiches de paie de leur société
        // et seulement si elles ne sont pas publiées
        return $user->role === 'employeur' && 
               $fichePaie->employe->societe_id === $user->societe_id &&
               $fichePaie->statut !== 'publié';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FichePaie  $fichePaie
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FichePaie $fichePaie)
    {
        // Seuls les employeurs peuvent supprimer les fiches de paie de leur société
        // et seulement si elles ne sont pas publiées
        return $user->role === 'employeur' && 
               $fichePaie->employe->societe_id === $user->societe_id &&
               $fichePaie->statut !== 'publié';
    }
}
