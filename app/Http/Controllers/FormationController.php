<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::where('societe_id', auth()->user()->societe_id)
            ->orderBy('nom')
            ->get();

        return view('formations.index', compact('formations'));
    }

    public function create()
    {
        return view('formations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_validite_mois' => 'nullable|integer|min:1',
        ]);

        $validated['societe_id'] = auth()->user()->societe_id;

        Formation::create($validated);

        return redirect()->route('formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    public function edit(Formation $formation)
    {
        return view('formations.edit', compact('formation'));
    }

    public function update(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_validite_mois' => 'nullable|integer|min:1',
        ]);

        $formation->update($validated);

        return redirect()->route('formations.index')
            ->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();

        return redirect()->route('formations.index')
            ->with('success', 'Formation supprimée avec succès.');
    }
}
