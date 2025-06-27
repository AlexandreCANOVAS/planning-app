<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieConsentController extends Controller
{
    /**
     * Accept the cookie consent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request)
    {
        // Set the cookie for 5 years (in minutes)
        $cookie = Cookie::make('laravel_cookie_consent', 'true', 60 * 24 * 365 * 5);

        return back()->withCookie($cookie);
    }
}
