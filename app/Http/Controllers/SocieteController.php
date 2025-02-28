<?php

namespace App\Http\Controllers;

use App\Models\Societe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocieteController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        
        // Si l'employeur a déjà une société
        if ($user->societe) {
            return redirect()->route('dashboard');
        }

        return view('societes.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Si l'employeur a déjà une société
        if ($user->societe) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'siret' => 'required|string|size:14',
            'adresse' => 'required|string|max:255',
            'forme_juridique' => 'required|string|max:50',
            'telephone' => 'nullable|string|max:20',
        ]);

        $societe = new Societe();
        $societe->nom = $validated['nom'];
        $societe->siret = $validated['siret'];
        $societe->adresse = $validated['adresse'];
        $societe->forme_juridique = $validated['forme_juridique'];
        $societe->telephone = $validated['telephone'];
        $societe->user_id = $user->id;
        $societe->save();

        $user->societe_id = $societe->id;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Société créée avec succès');
    }

    public function edit(Societe $societe)
    {
        $this->authorize('update', $societe);
        return view('societes.edit', compact('societe'));
    }

    public function update(Request $request, Societe $societe)
    {
        $this->authorize('update', $societe);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'siret' => 'required|string|size:14|unique:societes,siret,' . $societe->id,
            'forme_juridique' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'nullable|string|max:20',
        ]);

        $societe->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Société mise à jour avec succès');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = 'logo.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/company'), $imageName);

            return back()->with('success', 'Logo mis à jour avec succès.');
        }

        return back()->with('error', 'Veuillez sélectionner une image.');
    }
} 