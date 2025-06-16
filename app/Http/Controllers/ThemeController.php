<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ThemeController extends Controller
{
    public function toggleTheme(Request $request)
    {
        $currentTheme = $request->cookie('theme', 'light');
        $newTheme = $currentTheme === 'light' ? 'dark' : 'light';
        
        // Définir le cookie pour 1 an (en minutes)
        $minutes = 60 * 24 * 365;
        
        return redirect()->back()->withCookie(cookie('theme', $newTheme, $minutes));
    }
}
