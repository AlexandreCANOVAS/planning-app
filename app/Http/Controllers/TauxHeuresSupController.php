<?php

namespace App\Http\Controllers;

use App\Models\TauxHeuresSup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TauxHeuresSupController extends Controller
{
    public function index()
    {
        $taux = TauxHeuresSup::where('societe_id', Auth::user()->societe_id)->get();
        return view('taux.index', compact('taux'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'seuil_debut' => 'required|integer|min:0',
            'seuil_fin' => 'nullable|integer|gt:seuil_debut',
            'taux' => 'required|numeric|min:0'
        ]);

        TauxHeuresSup::create([
            'societe_id' => Auth::user()->societe_id,
            'nom' => $request->nom,
            'seuil_debut' => $request->seuil_debut,
            'seuil_fin' => $request->seuil_fin,
            'taux' => $request->taux
        ]);

        return redirect()->route('taux.index')->with('success', 'Taux ajouté avec succès');
    }

    public function update(Request $request, TauxHeuresSup $taux)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'seuil_debut' => 'required|integer|min:0',
            'seuil_fin' => 'nullable|integer|gt:seuil_debut',
            'taux' => 'required|numeric|min:0'
        ]);

        $taux->update($request->all());
        return redirect()->route('taux.index')->with('success', 'Taux mis à jour avec succès');
    }

    public function destroy(TauxHeuresSup $taux)
    {
        $taux->delete();
        return redirect()->route('taux.index')->with('success', 'Taux supprimé avec succès');
    }
}
