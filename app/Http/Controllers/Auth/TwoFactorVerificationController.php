<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorVerificationController extends Controller
{
    /**
     * Afficher le formulaire de vérification 2FA.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.two-factor-challenge');
    }

    /**
     * Vérifier le code 2FA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();
        
        // Vérifier si le code est valide
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if ($valid) {
            // Marquer la session comme vérifiée pour la 2FA
            Session::put('two_factor_verified', true);
            
            // Rediriger vers l'URL prévue ou le tableau de bord
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'code' => 'Le code d\'authentification est invalide.',
        ]);
    }

    /**
     * Vérifier un code de récupération.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = $request->user();
        
        if (!$user->two_factor_recovery_codes) {
            return back()->withErrors([
                'recovery_code' => 'Aucun code de récupération disponible.',
            ]);
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        
        // Vérifier si le code de récupération est valide
        if (in_array($request->recovery_code, $recoveryCodes)) {
            // Supprimer le code utilisé
            $recoveryCodes = array_diff($recoveryCodes, [$request->recovery_code]);
            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();
            
            // Marquer la session comme vérifiée pour la 2FA
            Session::put('two_factor_verified', true);
            
            // Rediriger vers l'URL prévue ou le tableau de bord
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'recovery_code' => 'Le code de récupération est invalide.',
        ]);
    }
}
