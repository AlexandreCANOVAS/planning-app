<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
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
        // Récupérer les catégories prédéfinies et celles de la société de l'utilisateur
        $categories = DocumentCategory::where(function($query) {
            $query->where('is_default', true)
                  ->orWhere('societe_id', Auth::user()->societe_id)
                  ->orWhereNull('societe_id');
        })->orderBy('name')->get();
        
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
            'category_id' => 'nullable|exists:document_categories,id',
            'nouvelle_categorie' => 'required_if:category_id,nouvelle|string|max:100|nullable',
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

        // Gérer la catégorie (nouvelle ou existante)
        $categoryId = null;
        
        if ($request->category_id === 'nouvelle' && $request->filled('nouvelle_categorie')) {
            // Vérifier si la catégorie existe déjà
            $existingCategory = DocumentCategory::where('name', $request->nouvelle_categorie)
                ->where(function($query) {
                    $query->where('is_default', true)
                          ->orWhere('societe_id', Auth::user()->societe_id)
                          ->orWhereNull('societe_id');
                })
                ->first();
                
            if ($existingCategory) {
                // Utiliser la catégorie existante
                $categoryId = $existingCategory->id;
            } else {
                // Créer une nouvelle catégorie
                $newCategory = new DocumentCategory();
                $newCategory->name = $request->nouvelle_categorie;
                $newCategory->is_default = false;
                $newCategory->societe_id = Auth::user()->societe_id;
                $newCategory->created_by = Auth::id();
                $newCategory->save();
                
                $categoryId = $newCategory->id;
            }
        } elseif ($request->filled('category_id') && $request->category_id !== 'nouvelle') {
            // Utiliser une catégorie existante
            $categoryId = $request->category_id;
        }
        
        // Créer le document
        $document = Document::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'fichier_path' => $cheminFichier,
            'type_fichier' => $extension,
            'category_id' => $categoryId,
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
        $document = Document::with(['employes', 'category'])->findOrFail($id);
        $societes = Societe::all();
        
        // Récupérer les catégories prédéfinies et celles de la société de l'utilisateur
        $categories = DocumentCategory::where(function($query) {
            $query->where('is_default', true)
                  ->orWhere('societe_id', Auth::user()->societe_id)
                  ->orWhereNull('societe_id');
        })->orderBy('name')->get();
        
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
            'category_id' => 'nullable|exists:document_categories,id',
            'nouvelle_categorie' => 'required_if:category_id,nouvelle|string|max:100|nullable',
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

        // Gérer la catégorie (nouvelle ou existante)
        $categoryId = null;
        
        if ($request->category_id === 'nouvelle' && $request->filled('nouvelle_categorie')) {
            // Vérifier si la catégorie existe déjà
            $existingCategory = DocumentCategory::where('name', $request->nouvelle_categorie)
                ->where(function($query) {
                    $query->where('is_default', true)
                          ->orWhere('societe_id', Auth::user()->societe_id)
                          ->orWhereNull('societe_id');
                })
                ->first();
                
            if ($existingCategory) {
                // Utiliser la catégorie existante
                $categoryId = $existingCategory->id;
            } else {
                // Créer une nouvelle catégorie
                $newCategory = new DocumentCategory();
                $newCategory->name = $request->nouvelle_categorie;
                $newCategory->is_default = false;
                $newCategory->societe_id = Auth::user()->societe_id;
                $newCategory->created_by = Auth::id();
                $newCategory->save();
                
                $categoryId = $newCategory->id;
            }
        } elseif ($request->filled('category_id') && $request->category_id !== 'nouvelle') {
            // Utiliser une catégorie existante
            $categoryId = $request->category_id;
        }
        
        // Mettre à jour les informations du document
        $document->titre = $request->titre;
        $document->description = $request->description;
        $document->category_id = $categoryId;
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
