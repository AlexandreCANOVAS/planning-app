<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index(Request $request)
    {
        $query = Formation::where('societe_id', auth()->user()->societe_id);
        
        // Recherche par nom ou description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('objectifs_pedagogiques', 'like', "%{$search}%")
                  ->orWhere('prerequis', 'like', "%{$search}%")
                  ->orWhere('organisme_formateur', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par type de formateur
        if ($request->filled('formateur_type')) {
            $formateurType = $request->input('formateur_type');
            if ($formateurType === 'interne') {
                $query->where('formateur_interne', true);
            } elseif ($formateurType === 'externe') {
                $query->where('formateur_interne', false);
            }
        }
        
        $formations = $query->orderBy('nom')->get();

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
            'objectifs_pedagogiques' => 'nullable|string',
            'prerequis' => 'nullable|string',
            'duree_validite_mois' => 'nullable|integer|min:1',
            'duree_recommandee_heures' => 'nullable|integer|min:1',
            'organisme_formateur' => 'nullable|string|max:255',
            'formateur_interne' => 'boolean',
            'cout' => 'nullable|numeric|min:0',
        ]);

        // Gestion du checkbox formateur_interne
        $validated['formateur_interne'] = $request->has('formateur_interne');
        
        $validated['societe_id'] = auth()->user()->societe_id;

        Formation::create($validated);

        return redirect()->route('formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    public function show(Formation $formation)
    {
        return view('formations.show', compact('formation'));
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
            'objectifs_pedagogiques' => 'nullable|string',
            'prerequis' => 'nullable|string',
            'duree_validite_mois' => 'nullable|integer|min:1',
            'duree_recommandee_heures' => 'nullable|integer|min:1',
            'organisme_formateur' => 'nullable|string|max:255',
            'formateur_interne' => 'boolean',
            'cout' => 'nullable|numeric|min:0',
        ]);
        
        // Gestion du checkbox formateur_interne
        $validated['formateur_interne'] = $request->has('formateur_interne');

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
