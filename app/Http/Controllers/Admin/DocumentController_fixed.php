<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Afficher la liste des documents.
     */
    public function index()
    {
        $documents = Document::where('societe_id', Auth::user()->societe_id)
                           ->orderBy('created_at', 'desc')
                           ->get();
        
        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Afficher le formulaire de création d'un nouveau document.
     */
    public function create()
    {
        // Récupérer les catégories (prédéfinies ou de la société)
        $categories = DocumentCategory::where(function($query) {
            $query->where('is_default', true)
                  ->orWhere('societe_id', Auth::user()->societe_id)
                  ->orWhereNull('societe_id');
        })->orderBy('name')->get();
        
        // Récupérer les employés de la société
        $employes = Employe::where('societe_id', Auth::user()->societe_id)
                          ->orderBy('nom')
                          ->get();
        
        return view('admin.documents.create', compact('categories', 'employes'));
    }

    /**
     * Enregistrer un nouveau document.
     */
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'required|file|max:10240', // 10MB max
            'category_id' => 'required|string',
            'nouvelle_categorie' => 'required_if:category_id,nouvelle|string|max:255',
            'date_expiration' => 'nullable|date',
            'employes' => 'required_if:visible_pour_tous,null|array',
            'societe_id' => 'required|exists:societes,id',
        ]);
        
        // Gérer le fichier
        $fichier = $request->file('fichier');
        $extension = $fichier->getClientOriginalExtension();
        $nomFichier = Str::slug($request->titre) . '_' . time() . '.' . $extension;
        $cheminFichier = $fichier->storeAs('documents', $nomFichier);

        // Gérer la catégorie (nouvelle ou existante)
        $categoryId = null;
        $categorieTexte = null; // Pour rétrocompatibilité
        
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
                $categorieTexte = $existingCategory->name; // Pour rétrocompatibilité
            } else {
                // Créer une nouvelle catégorie
                $newCategory = new DocumentCategory();
                $newCategory->name = $request->nouvelle_categorie;
                $newCategory->is_default = false;
                $newCategory->societe_id = Auth::user()->societe_id;
                $newCategory->created_by = Auth::id();
                $newCategory->save();
                
                $categoryId = $newCategory->id;
                $categorieTexte = $newCategory->name; // Pour rétrocompatibilité
            }
        } elseif ($request->filled('category_id') && $request->category_id !== 'nouvelle') {
            // Utiliser une catégorie existante
            $categoryId = $request->category_id;
            $category = DocumentCategory::find($categoryId);
            $categorieTexte = $category ? $category->name : 'Non catégorisé'; // Pour rétrocompatibilité
        }
        
        // Créer le document
        $document = Document::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'fichier_path' => $cheminFichier,
            'type_fichier' => $extension,
            'category_id' => $categoryId,
            'categorie' => $categorieTexte, // Champ requis pour rétrocompatibilité
            'visible_pour_tous' => $request->has('visible_pour_tous'),
            'societe_id' => $request->societe_id,
            'uploaded_by' => Auth::id(),
            'date_expiration' => $request->date_expiration,
        ]);

        // Associer les employés sélectionnés si le document n'est pas visible pour tous
        if (!$request->has('visible_pour_tous') && $request->has('employes')) {
            $document->employes()->attach($request->employes);
        }

        return redirect()->route('documents.show', $document->id)
                         ->with('success', 'Document créé avec succès.');
    }

    /**
     * Afficher les détails d'un document.
     */
    public function show(Document $document)
    {
        // Vérifier que l'utilisateur a accès à ce document
        if ($document->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à ce document.');
        }
        
        return view('admin.documents.show', compact('document'));
    }

    /**
     * Afficher le formulaire de modification d'un document.
     */
    public function edit(Document $document)
    {
        // Vérifier que l'utilisateur a accès à ce document
        if ($document->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à ce document.');
        }
        
        // Récupérer les catégories (prédéfinies ou de la société)
        $categories = DocumentCategory::where(function($query) {
            $query->where('is_default', true)
                  ->orWhere('societe_id', Auth::user()->societe_id)
                  ->orWhereNull('societe_id');
        })->orderBy('name')->get();
        
        // Récupérer les employés de la société
        $employes = Employe::where('societe_id', Auth::user()->societe_id)
                          ->orderBy('nom')
                          ->get();
        
        // Récupérer les IDs des employés associés à ce document
        $employesIds = $document->employes->pluck('id')->toArray();
        
        return view('admin.documents.edit', compact('document', 'categories', 'employes', 'employesIds'));
    }

    /**
     * Mettre à jour un document existant.
     */
    public function update(Request $request, Document $document)
    {
        // Vérifier que l'utilisateur a accès à ce document
        if ($document->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à ce document.');
        }
        
        // Valider les données
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'nullable|file|max:10240', // 10MB max
            'category_id' => 'required|string',
            'nouvelle_categorie' => 'required_if:category_id,nouvelle|string|max:255',
            'date_expiration' => 'nullable|date',
            'employes' => 'required_if:visible_pour_tous,null|array',
        ]);
        
        // Gérer le fichier si un nouveau fichier est fourni
        $cheminFichier = $document->fichier_path;
        $extension = $document->type_fichier;
        
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier
            Storage::delete($document->fichier_path);
            
            // Stocker le nouveau fichier
            $fichier = $request->file('fichier');
            $extension = $fichier->getClientOriginalExtension();
            $nomFichier = Str::slug($request->titre) . '_' . time() . '.' . $extension;
            $cheminFichier = $fichier->storeAs('documents', $nomFichier);
        }
        
        // Gérer la catégorie (nouvelle ou existante)
        $categoryId = $document->category_id;
        $categorieTexte = $document->categorie; // Pour rétrocompatibilité
        
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
                $categorieTexte = $existingCategory->name; // Pour rétrocompatibilité
            } else {
                // Créer une nouvelle catégorie
                $newCategory = new DocumentCategory();
                $newCategory->name = $request->nouvelle_categorie;
                $newCategory->is_default = false;
                $newCategory->societe_id = Auth::user()->societe_id;
                $newCategory->created_by = Auth::id();
                $newCategory->save();
                
                $categoryId = $newCategory->id;
                $categorieTexte = $newCategory->name; // Pour rétrocompatibilité
            }
        } elseif ($request->filled('category_id') && $request->category_id !== 'nouvelle') {
            // Utiliser une catégorie existante
            $categoryId = $request->category_id;
            $category = DocumentCategory::find($categoryId);
            $categorieTexte = $category ? $category->name : 'Non catégorisé'; // Pour rétrocompatibilité
        }
        
        // Mettre à jour le document
        $document->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'fichier_path' => $cheminFichier,
            'type_fichier' => $extension,
            'category_id' => $categoryId,
            'categorie' => $categorieTexte, // Champ requis pour rétrocompatibilité
            'visible_pour_tous' => $request->has('visible_pour_tous'),
            'date_expiration' => $request->date_expiration,
        ]);
        
        // Mettre à jour les employés associés
        if ($request->has('visible_pour_tous')) {
            // Si visible pour tous, supprimer toutes les associations
            $document->employes()->detach();
        } else {
            // Sinon, synchroniser avec les employés sélectionnés
            $document->employes()->sync($request->employes ?? []);
        }
        
        return redirect()->route('documents.show', $document->id)
                         ->with('success', 'Document mis à jour avec succès.');
    }

    /**
     * Supprimer un document.
     */
    public function destroy(Document $document)
    {
        // Vérifier que l'utilisateur a accès à ce document
        if ($document->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à ce document.');
        }
        
        // Supprimer le fichier
        Storage::delete($document->fichier_path);
        
        // Supprimer le document
        $document->delete();
        
        return redirect()->route('documents.index')
                         ->with('success', 'Document supprimé avec succès.');
    }
    
    /**
     * Télécharger un document.
     */
    public function download(Document $document)
    {
        // Vérifier que l'utilisateur a accès à ce document
        if ($document->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à ce document.');
        }
        
        // Vérifier que le fichier existe
        if (!Storage::exists($document->fichier_path)) {
            return back()->with('error', 'Le fichier n\'existe pas.');
        }
        
        // Télécharger le fichier
        return Storage::download(
            $document->fichier_path, 
            $document->titre . '.' . $document->type_fichier
        );
    }
    
    /**
     * Afficher les statistiques des documents.
     */
    public function stats()
    {
        $societeId = Auth::user()->societe_id;
        
        // Nombre total de documents
        $totalDocuments = Document::where('societe_id', $societeId)->count();
        
        // Documents par catégorie
        $documentsByCategory = Document::where('societe_id', $societeId)
            ->select('categorie', \DB::raw('count(*) as total'))
            ->groupBy('categorie')
            ->orderBy('total', 'desc')
            ->get();
        
        // Documents par type de fichier
        $documentsByType = Document::where('societe_id', $societeId)
            ->select('type_fichier', \DB::raw('count(*) as total'))
            ->groupBy('type_fichier')
            ->orderBy('total', 'desc')
            ->get();
        
        // Documents par mois
        $documentsByMonth = Document::where('societe_id', $societeId)
            ->select(
                \DB::raw('YEAR(created_at) as year'),
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('count(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        return view('admin.documents.stats', compact(
            'totalDocuments',
            'documentsByCategory',
            'documentsByType',
            'documentsByMonth'
        ));
    }
    
    /**
     * Gérer les documents d'un employé.
     */
    public function manageEmployes(Employe $employe)
    {
        // Vérifier que l'utilisateur a accès à cet employé
        if ($employe->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cet employé.');
        }
        
        // Documents associés à cet employé
        $associatedDocuments = $employe->documents;
        
        // Documents non associés à cet employé
        $availableDocuments = Document::where('societe_id', Auth::user()->societe_id)
            ->where('visible_pour_tous', false)
            ->whereNotIn('id', $associatedDocuments->pluck('id'))
            ->get();
        
        return view('admin.documents.manage-employes', compact(
            'employe',
            'associatedDocuments',
            'availableDocuments'
        ));
    }
    
    /**
     * Associer des documents à un employé.
     */
    public function attachDocuments(Request $request, Employe $employe)
    {
        // Vérifier que l'utilisateur a accès à cet employé
        if ($employe->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cet employé.');
        }
        
        // Valider les données
        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'exists:documents,id',
        ]);
        
        // Associer les documents
        $employe->documents()->attach($request->documents);
        
        return back()->with('success', 'Documents associés avec succès.');
    }
    
    /**
     * Dissocier un document d'un employé.
     */
    public function detachDocument(Employe $employe, Document $document)
    {
        // Vérifier que l'utilisateur a accès à cet employé et à ce document
        if ($employe->societe_id !== Auth::user()->societe_id || 
            $document->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à ces ressources.');
        }
        
        // Dissocier le document
        $employe->documents()->detach($document->id);
        
        return back()->with('success', 'Document dissocié avec succès.');
    }
}
