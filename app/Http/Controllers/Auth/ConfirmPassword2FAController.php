<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ConfirmPassword2FAController extends Controller
{
    /**
     * Afficher le formulaire de confirmation de mot de passe.
     */
    public function show(Request $request)
    {
        $action = $request->query('action');
        
        if (!in_array($action, ['enable', 'disable', 'recovery-codes'])) {
            return redirect()->route('profile.edit');
        }
        
        return view('auth.confirm-password-2fa', [
            'action' => $action
        ]);
    }

    /**
     * Confirmer le mot de passe et rediriger vers l'action appropriée.
     */
    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
            'action' => ['required', 'string', 'in:enable,disable,recovery-codes'],
        ]);

        if (! Hash::check($validated['password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('Le mot de passe fourni est incorrect.'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        // Stocker la confirmation du mot de passe dans la session
        $request->session()->put('auth.password_confirmed_at', time());
        
        // Retourner une vue avec un formulaire qui sera automatiquement soumis
        return view('auth.auto-submit-form', [
            'action' => $validated['action'],
            'route' => match($validated['action']) {
                'enable' => route('two-factor.custom.enable'),
                'disable' => route('two-factor.custom.disable'),
                'recovery-codes' => route('two-factor.custom.recovery-codes'),
                default => route('profile.edit'),
            },
        ]);
    }
}
