<?php

namespace App\Http\Controllers;

use App\Models\LieuTravail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LieuController extends Controller
{
    private $lieuxSpeciaux = ['RH', 'CP'];

    public function index()
    {
        // Récupérer uniquement les lieux de travail de la société de l'employeur connecté
        $lieux = LieuTravail::where('societe_id', Auth::user()->societe->id)
            ->orderBy('nom')
            ->paginate(10);
        return view('lieux.index', compact('lieux'));
    }

    public function create()
    {
        return view('lieux.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'couleur' => 'nullable|string|max:7'
        ]);

        $lieu = Auth::user()->societe->lieuxTravail()->create($validated);

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail créé avec succès');
    }

    public function edit(LieuTravail $lieu)
    {
        $this->authorize('view', $lieu);
        $this->authorize('update', $lieu);
        // Ne pas permettre la modification des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux)) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de modifier ce lieu de travail');
        }

        return view('lieux.edit', compact('lieu'));
    }

    public function update(Request $request, LieuTravail $lieu)
    {
        $this->authorize('update', $lieu);
        // Ne pas permettre la modification des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux)) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de modifier ce lieu de travail');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'couleur' => 'nullable|string|max:7'
        ]);

        $lieu->update($validated);

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail mis à jour avec succès');
    }

    public function destroy(LieuTravail $lieu)
    {
        $this->authorize('delete', $lieu);
        // Ne pas permettre la suppression des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux)) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de supprimer ce lieu de travail');
        }
        
        $lieu->delete();

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail supprimé avec succès');
    }
}