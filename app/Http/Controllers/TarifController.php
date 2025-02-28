<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = auth()->user()->societe->tarifs()->latest()->get();
        return view('tarifs.index', compact('tarifs'));
    }

    public function create()
    {
        return view('tarifs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'taux_horaire' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        auth()->user()->societe->tarifs()->create($validated);

        return redirect()->route('tarifs.index')
            ->with('success', 'Tarif créé avec succès');
    }

    public function edit(Tarif $tarif)
    {
        $this->authorize('update', $tarif);
        return view('tarifs.edit', compact('tarif'));
    }

    public function update(Request $request, Tarif $tarif)
    {
        $this->authorize('update', $tarif);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'taux_horaire' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $tarif->update($validated);

        return redirect()->route('tarifs.index')
            ->with('success', 'Tarif mis à jour avec succès');
    }

    public function destroy(Tarif $tarif)
    {
        $this->authorize('delete', $tarif);
        
        $tarif->delete();

        return redirect()->route('tarifs.index')
            ->with('success', 'Tarif supprimé avec succès');
    }
}
