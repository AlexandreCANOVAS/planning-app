<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Employe;
use App\Models\Societe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Affiche la liste des documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::with(['societe', 'uploadedBy', 'employes'])->latest()->paginate(10);
        $categories = Document::select('categorie')->distinct()->pluck('categorie');
        
        return view('admin.documents.index', compact('documents', 'categories'));
    }

    /**
     * Affiche le formulaire de création d'un document.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $societes = Societe::all();
        $categories = Document::select('categorie')->distinct()->pluck('categorie');
        $employes = Employe::with('user')->get();
        
        return view('admin.documents.create', compact('societes', 'categories', 'employes'));
    }

    /**
     * Enregistre un nouveau document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'required|file|max:10240', // 10MB max
            'categorie' => 'required|string|max:100',
            'societe_id' => 'required|exists:societes,id',
            'visible_pour_tous' => 'boolean',
            'employes' => 'required_if:visible_pour_tous,0|array',
            'date_expiration' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Gérer le téléversement du fichier
        $fichier = $request->file('fichier');
        $extension = $fichier->getClientOriginalExtension();
        $nomFichier = Str::slug($request->titre) . '_' . time() . '.' . $extension;
        $cheminFichier = $fichier->storeAs('documents', $nomFichier);

        // Créer le document
        $document = Document::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'fichier_path' => $cheminFichier,
            'type_fichier' => $extension,
            'categorie' => $request->categorie,
            'visible_pour_tous' => $request->has('visible_pour_tous'),
            'societe_id' => $request->societe_id,
            'uploaded_by' => Auth::id(),
            'date_expiration' => $request->date_expiration,
        ]);

        // Associer les employés sélectionnés si le document n'est pas visible pour tous
        if (!$request->has('visible_pour_tous') && $request->has('employes')) {
            $document->employes()->attach($request->employes);
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = Document::with(['societe', 'uploadedBy', 'employes.user'])->findOrFail($id);
        
        return view('admin.documents.show', compact('document'));
    }

    /**
     * Affiche le formulaire d'édition d'un document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $document = Document::with('employes')->findOrFail($id);
        $societes = Societe::all();
        $categories = Document::select('categorie')->distinct()->pluck('categorie');
        $employes = Employe::with('user')->get();
        $employesSelectionnes = $document->employes->pluck('id')->toArray();
        
        return view('admin.documents.edit', compact('document', 'societes', 'categories', 'employes', 'employesSelectionnes'));
    }

    /**
     * Met à jour un document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'nullable|file|max:10240', // 10MB max
            'categorie' => 'required|string|max:100',
            'societe_id' => 'required|exists:societes,id',
            'visible_pour_tous' => 'boolean',
            'employes' => 'required_if:visible_pour_tous,0|array',
            'date_expiration' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mettre à jour les informations du document
        $document->titre = $request->titre;
        $document->description = $request->description;
        $document->categorie = $request->categorie;
        $document->visible_pour_tous = $request->has('visible_pour_tous');
        $document->societe_id = $request->societe_id;
        $document->date_expiration = $request->date_expiration;

        // Gérer le remplacement du fichier si un nouveau fichier est fourni
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier
            Storage::delete($document->fichier_path);
            
            // Téléverser le nouveau fichier
            $fichier = $request->file('fichier');
            $extension = $fichier->getClientOriginalExtension();
            $nomFichier = Str::slug($request->titre) . '_' . time() . '.' . $extension;
            $cheminFichier = $fichier->storeAs('documents', $nomFichier);
            
            $document->fichier_path = $cheminFichier;
            $document->type_fichier = $extension;
        }

        $document->save();

        // Gérer les associations avec les employés
        if ($document->visible_pour_tous) {
            // Si le document est visible pour tous, supprimer toutes les associations spécifiques
            $document->employes()->detach();
        } else {
            // Mettre à jour les associations avec les employés sélectionnés
            $document->employes()->sync($request->employes ?? []);
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document mis à jour avec succès');
    }

    /**
     * Supprime un document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Supprimer le fichier physique
        Storage::delete($document->fichier_path);
        
        // Supprimer le document de la base de données (les relations seront automatiquement supprimées grâce aux contraintes de clé étrangère)
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé avec succès.');
    }
    
    /**
     * Affiche les statistiques de consultation des documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
        $documents = Document::withCount([
            'employes', 
            'employes as vus_count' => function ($query) {
                $query->whereNotNull('document_employe.consulte_le');
            },
            'employes as confirmes_count' => function ($query) {
                $query->where('document_employe.confirme_lecture', true);
            }
        ])->get();
        
        return view('admin.documents.stats', compact('documents'));
    }
    
    /**
     * Permet d'ajouter ou de retirer des employés spécifiques pour un document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageEmployes(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        if ($document->visible_pour_tous) {
            return redirect()->back()->with('error', 'Ce document est visible pour tous les employés. Désactivez cette option pour gérer les accès spécifiques.');
        }
        
        $validator = Validator::make($request->all(), [
            'employes' => 'required|array',
            'employes.*' => 'exists:employes,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Mettre à jour les associations
        $document->employes()->sync($request->employes);
        
        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Accès des employés mis à jour avec succès.');
    }
}
