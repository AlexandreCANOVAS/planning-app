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

    /**
     * Affiche la page du centre d'aide.
     *
     * @return \Illuminate\View\View
     */
    public function help(): View
    {
        return view('pages.help');
    }

    /**
     * Affiche la page de documentation.
     *
     * @return \Illuminate\View\View
     */
    public function documentation(): View
    {
        return view('pages.documentation');
    }

    /**
     * Affiche la page de statut du système.
     *
     * @return \Illuminate\View\View
     */
    public function systemStatus(): View
    {
        // Ici vous pourriez ajouter une logique pour vérifier l'état des services
        $services = [
            [
                'name' => 'Application Web',
                'status' => 'Opérationnel',
                'uptime' => '99.9%'
            ],
            [
                'name' => 'Base de données',
                'status' => 'Opérationnel',
                'uptime' => '99.8%'
            ],
            [
                'name' => 'Système de notifications',
                'status' => 'Opérationnel',
                'uptime' => '99.7%'
            ],
            [
                'name' => 'API',
                'status' => 'Opérationnel',
                'uptime' => '99.9%'
            ]
        ];
        
        return view('pages.system-status', compact('services'));
    }
}
