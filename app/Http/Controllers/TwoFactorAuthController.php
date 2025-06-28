<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    /**
     * Afficher le formulaire d'authentification à deux facteurs.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Générer le QR code si l'authentification à deux facteurs est activée
        $qrCodeSvg = null;
        if ($user->two_factor_secret) {
            $qrCodeSvg = $this->generateQrCode($user);
        }
        
        return view('profile.two-factor-authentication-form-new', [
            'enabled' => $user->two_factor_secret ? true : false,
            'showingQrCode' => $user->two_factor_secret ? true : false,
            'showingRecoveryCodes' => $user->two_factor_recovery_codes ? true : false,
            'user' => $user,
            'qrCodeSvg' => $qrCodeSvg,
            'recoveryCodes' => $user->two_factor_recovery_codes ? json_decode(decrypt($user->two_factor_recovery_codes), true) : [],
            'twoFactorSecret' => $user->two_factor_secret ? decrypt($user->two_factor_secret) : null,
        ]);
    }
    
    /**
     * Générer un code QR pour l'authentification à deux facteurs.
     */
    protected function generateQrCode($user)
    {
        $secret = decrypt($user->two_factor_secret);
        
        $renderer = new ImageRenderer(
            new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        
        // Générer l'URL pour l'application d'authentification
        $company = config('app.name');
        $email = $user->email;
        $twoFactorUrl = "otpauth://totp/{$company}:{$email}?secret={$secret}&issuer={$company}";
        
        return $writer->writeString($twoFactorUrl);
    }

    /**
     * Activer l'authentification à deux facteurs.
     */
    public function enable(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();
        
        // Générer un secret pour l'utilisateur
        $secret = $google2fa->generateSecretKey();
        
        // Stocker le secret et générer des codes de récupération
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_recovery_codes = encrypt(json_encode(
            Collection::times(8, function () {
                return Str::random(10).'-'.Str::random(10);
            })->all()
        ));
        $user->save();

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * Désactiver l'authentification à deux facteurs.
     */
    public function disable(Request $request)
    {
        $user = $request->user();
        
        // Supprimer les données 2FA de l'utilisateur
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();
        
        // Supprimer la session 2FA vérifiée si elle existe
        if ($request->session()->has('two_factor_verified')) {
            $request->session()->forget('two_factor_verified');
        }

        return back()->with('status', 'two-factor-authentication-disabled');
    }

    /**
     * Générer de nouveaux codes de récupération.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();
        
        // Générer de nouveaux codes de récupération
        $user->two_factor_recovery_codes = encrypt(json_encode(
            Collection::times(8, function () {
                return Str::random(10).'-'.Str::random(10);
            })->all()
        ));
        $user->save();

        return back()->with('status', 'recovery-codes-generated');
    }
}
