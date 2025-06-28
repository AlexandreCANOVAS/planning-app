<?php

namespace App\Http\Helpers;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorQrCodeGenerator
{
    /**
     * Générer un code QR pour l'authentification à deux facteurs.
     */
    public static function generateQrCode($user)
    {
        if (!$user->two_factor_secret) {
            return null;
        }
        
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
}
