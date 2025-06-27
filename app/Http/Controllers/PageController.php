<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Affiche la page de la politique de confidentialité.
     *
     * @return \Illuminate\View\View
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Affiche la page de la politique de cookies.
     *
     * @return \Illuminate\View\View
     */
    public function cookies(): View
    {
        return view('pages.cookies');
    }

}
