<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\Document;
use App\Models\Employe;
use Carbon\Carbon;

class EmployeDocumentsController extends Controller
{
    /**
     * Affiche la liste des documents disponibles pour l'employé
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Récupérer l'employé connecté
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Profil employé non trouvé.');
        }
        
        // Récupérer les documents accessibles à l'employé
        $query = Document::whereHas('employes', function ($query) use ($employe) {
            $query->where('employe_id', $employe->id);
        })->orWhere('visible_pour_tous', true);
        
        // Filtrer par catégorie si spécifié
        if ($request->has('categorie') && $request->categorie != 'all') {
            $query->where('categorie', $request->categorie);
        }
        
        // Filtrer par recherche si spécifié
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('categorie', 'like', "%{$search}%");
            });
        }
        
        // Récupérer les documents avec pagination
        $documents = $query->with(['employes' => function ($query) use ($employe) {
            $query->where('employe_id', $employe->id);
        }])->orderBy('created_at', 'desc')->paginate(10);
        
        // Récupérer les catégories distinctes pour le filtre
        $categories = Document::whereHas('employes', function ($query) use ($employe) {
            $query->where('employe_id', $employe->id);
        })->orWhere('visible_pour_tous', true)
          ->select('categorie')
          ->distinct()
          ->pluck('categorie');
        
        // Traiter chaque document pour s'assurer que les informations pivot sont correctement chargées
        foreach ($documents as $document) {
            // Extraire les informations de la relation pivot
            $document->pivot = $document->employes->first() ? $document->employes->first()->pivot : null;
        }
        
        return view('employes.documents.index', compact('employe', 'documents', 'categories'));
    }
    
    /**
     * Affiche un document spécifique
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Récupérer l'employé connecté
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Profil employé non trouvé.');
        }
        
        // Récupérer le document avec la relation pivot
        $document = Document::with(['employes' => function ($query) use ($employe) {
            $query->where('employe_id', $employe->id);
        }])->findOrFail($id);
        
        // Vérifier si l'employé a accès au document
        if (!$document->visible_pour_tous && !$document->employes->contains($employe->id)) {
            return redirect()->route('employe.documents.index')
                ->with('error', 'Vous n\'avez pas accès à ce document.');
        }
        
        // Extraire les informations de la relation pivot
        $document->pivot = $document->employes->first() ? $document->employes->first()->pivot : null;
        
        // Marquer le document comme consulté si ce n'est pas déjà fait
        if ($document->pivot && !$document->pivot->consulte_le) {
            $employe->documents()->updateExistingPivot($document->id, [
                'consulte_le' => Carbon::now()
            ]);
        }
        
        return view('employes.documents.show', compact('document'));
    }
    
    /**
     * Affiche l'aperçu d'un document spécifique
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $employe = Auth::user()->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil employé non trouvé.');
        }
        
        // Récupérer le document
        $document = Document::findOrFail($id);
        
        // Vérifier si l'employé a accès au document
        if (!$document->visible_pour_tous && !$document->isAccessibleBy($employe->id)) {
            return redirect()->route('employe.documents.index')
                ->with('error', 'Vous n\'avez pas accès à ce document.');
        }
        
        // Vérifier si le fichier existe
        if (!Storage::exists($document->fichier_path)) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }
        
        // Marquer le document comme consulté si ce n'est pas déjà fait
        $pivot = $document->employes()->where('employe_id', $employe->id)->first();
        if ($pivot && !$pivot->pivot->consulte_le) {
            $employe->documents()->updateExistingPivot($document->id, [
                'consulte_le' => Carbon::now()
            ]);
        }
        
        // Récupérer le contenu du fichier
        $fileContent = Storage::get($document->fichier_path);
        $mimeType = Storage::mimeType($document->fichier_path);
        
        // Renvoyer le contenu avec le bon type MIME pour l'affichage dans le navigateur
        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }
    
    /**
     * Télécharge un document spécifique
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        // Récupérer l'employé connecté
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Profil employé non trouvé.');
        }
        
        // Récupérer le document
        $document = Document::findOrFail($id);
        
        // Vérifier si l'employé a accès au document
        if (!$document->visible_pour_tous && !$document->employes()->where('employe_id', $employe->id)->exists()) {
            return redirect()->route('employe.documents.index')
                ->with('error', 'Vous n\'avez pas accès à ce document.');
        }
        
        // Vérifier si le fichier existe
        if (!Storage::exists($document->fichier_path)) {
            return redirect()->route('employe.documents.show', $document->id)
                ->with('error', 'Le fichier n\'est pas disponible.');
        }
        
        // Marquer le document comme consulté si ce n'est pas déjà fait
        $pivot = $document->employes()->where('employe_id', $employe->id)->first();
        if ($pivot && !$pivot->pivot->consulte_le) {
            $employe->documents()->updateExistingPivot($document->id, [
                'consulte_le' => Carbon::now()
            ]);
        }
        
        // Obtenir le nom du fichier original
        $filename = pathinfo($document->fichier_path, PATHINFO_BASENAME);
        
        // Télécharger le fichier
        return Storage::download($document->fichier_path, $filename);
    }
    
    /**
     * Confirme la lecture d'un document
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmLecture(Request $request, $id)
    {
        // Valider la requête
        $request->validate([
            'confirmation' => 'required|accepted',
        ]);
        
        // Récupérer l'employé connecté
        $user = Auth::user();
        $employe = $user->employe;
        
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Profil employé non trouvé.');
        }
        
        // Récupérer le document
        $document = Document::findOrFail($id);
        
        // Vérifier si l'employé a accès au document
        if (!$document->visible_pour_tous && !$document->employes()->where('employe_id', $employe->id)->exists()) {
            return redirect()->route('employe.documents.index')
                ->with('error', 'Vous n\'avez pas accès à ce document.');
        }
        
        // Vérifier si la relation pivot existe
        if (!$document->employes()->where('employe_id', $employe->id)->exists()) {
            // Créer la relation avec l'employé si elle n'existe pas (cas des documents visibles pour tous)
            $document->employes()->attach($employe->id, [
                'consulte_le' => Carbon::now(),
                'confirme_lecture' => true,
                'confirme_le' => Carbon::now()
            ]);
        } else {
            // Mettre à jour la relation existante
            $employe->documents()->updateExistingPivot($document->id, [
                'confirme_lecture' => true,
                'confirme_le' => Carbon::now()
            ]);
        }
        
        return redirect()->route('employe.documents.show', $document->id)
            ->with('success', 'Lecture du document confirmée avec succès.');
    }
}
