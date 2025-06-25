<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GDPRController extends Controller
{
    public function exportData()
    {
        $user = auth()->user();

        $data = [
            'user' => $user->toArray(),
            'societe' => $user->societe ? $user->societe->toArray() : null,
            'employes' => $user->employes ? $user->employes->toArray() : [],
            'plannings' => $user->plannings ? $user->plannings->toArray() : [],
            'conges' => $user->conges ? $user->conges->toArray() : [],
        ];

        $fileName = 'mes_donnees_' . $user->id . '_' . date('Y-m-d') . '.json';
        $headers = [
            'Content-type'        => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->json($data, 200, $headers);
    }
}
