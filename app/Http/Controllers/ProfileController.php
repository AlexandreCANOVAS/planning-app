<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Employe;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $employe = null;

        if ($user->isEmploye()) {
            $employe = $user->employe;
            
            if (!$employe) {
                $employe = Employe::create([
                    'nom' => explode(' ', $user->name)[0] ?? $user->name,
                    'prenom' => explode(' ', $user->name)[1] ?? '',
                    'email' => $user->email,
                    'telephone' => $user->phone,
                    'societe_id' => $user->societe_id,
                    'user_id' => $user->id
                ]);
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'employe' => $employe,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Mettre à jour le profil employé si nécessaire
        if ($user->isEmploye() && $user->employe) {
            $user->employe->update([
                'nom' => explode(' ', $user->name)[0] ?? $user->name,
                'prenom' => explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'telephone' => $user->phone
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Supprimer l'employé associé si nécessaire
        if ($user->isEmploye() && $user->employe) {
            $user->employe->delete();
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
