<?php

namespace App\Http\Controllers;

use App\Models\Lieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LieuController extends Controller
{
    private $lieuxSpeciaux = ['RH', 'CP'];

    public function index()
    {
        // Récupérer les lieux de travail de la société de l'employeur connecté, en excluant les lieux spéciaux
        $lieux = Lieu::where('societe_id', Auth::user()->societe->id)
            ->whereNotIn('nom', $this->lieuxSpeciaux)
            ->orderBy('nom')
            ->paginate(10);
            
        // Calculer le premier et dernier jour du mois en cours
        $debutMois = now()->startOfMonth()->format('Y-m-d');
        $finMois = now()->endOfMonth()->format('Y-m-d');
        
        // Pour chaque lieu, calculer les statistiques d'utilisation
        foreach ($lieux as $lieu) {
            // Nombre d'employés assignés aujourd'hui
            $lieu->employes_aujourdhui = $lieu->plannings()
                ->where('date', now()->format('Y-m-d'))
                ->distinct('employe_id')
                ->count('employe_id');
                
            // Nombre d'heures planifiées ce mois-ci
            $lieu->heures_mois = $lieu->plannings()
                ->whereBetween('date', [$debutMois, $finMois])
                ->sum('heures_travaillees');
        }
        
        // Récupérer tous les lieux avec adresses complètes pour la carte
        $lieuxAvecCoordonnees = Lieu::where('societe_id', Auth::user()->societe->id)
            ->whereNotNull('adresse')
            ->whereNotNull('ville')
            ->get(['id', 'nom', 'adresse', 'ville', 'code_postal', 'couleur', 'telephone', 'horaires', 'contact_principal']);
        
        return view('lieux.index', compact('lieux', 'lieuxAvecCoordonnees'));
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
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7'
        ]);

        $lieu = Auth::user()->societe->lieux()->create($validated);

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail créé avec succès');
    }

    public function edit(Lieu $lieu)
    {
        // Ne pas permettre la modification des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux)) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de modifier ce lieu de travail');
        }

        // Vérifier simplement que l'utilisateur est un employeur
        if (!Auth::user()->isEmployeur()) {
            return redirect()->route('lieux.index')
                ->with('error', 'Seuls les employeurs peuvent modifier les lieux de travail');
        }

        return view('lieux.edit', compact('lieu'));
    }

    public function update(Request $request, Lieu $lieu)
    {
        // Ne pas permettre la modification des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux)) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de modifier ce lieu de travail');
        }

        // Vérifier simplement que l'utilisateur est un employeur
        if (!Auth::user()->isEmployeur()) {
            return redirect()->route('lieux.index')
                ->with('error', 'Seuls les employeurs peuvent modifier les lieux de travail');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7'
        ]);

        $lieu->update($validated);

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail mis à jour avec succès');
    }

    public function destroy(Lieu $lieu)
    {
        // Ne pas permettre la suppression des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux) || $lieu->is_special) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de supprimer ce lieu de travail');
        }

        // Vérifier simplement que l'utilisateur est un employeur
        if (!Auth::user()->isEmployeur()) {
            return redirect()->route('lieux.index')
                ->with('error', 'Seuls les employeurs peuvent supprimer les lieux de travail');
        }
        
        // Vérifier si le lieu est utilisé dans des plannings
        if ($lieu->plannings()->count() > 0) {
            // Retourner avec un message d'erreur et une session flash pour indiquer que le lieu est utilisé
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de supprimer ce lieu car il est utilisé dans des plannings')
                ->with('lieu_id', $lieu->id);
        }
        
        $lieu->delete();

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail supprimé avec succès');
    }
    
    /**
     * Supprime un lieu de travail même s'il est utilisé dans des plannings.
     *
     * @param  \App\Models\Lieu  $lieu
     * @return \Illuminate\Http\Response
     */
    public function forceDestroy(Lieu $lieu)
    {
        // Ne pas permettre la suppression des lieux spéciaux
        if (in_array($lieu->nom, $this->lieuxSpeciaux) || $lieu->is_special) {
            return redirect()->route('lieux.index')
                ->with('error', 'Impossible de supprimer ce lieu de travail');
        }

        // Vérifier simplement que l'utilisateur est un employeur
        if (!Auth::user()->isEmployeur()) {
            return redirect()->route('lieux.index')
                ->with('error', 'Seuls les employeurs peuvent supprimer les lieux de travail');
        }
        
        // Supprimer le lieu même s'il est utilisé dans des plannings
        $lieu->delete();

        return redirect()->route('lieux.index')
            ->with('success', 'Lieu de travail supprimé avec succès');
    }
    
    /**
     * Affiche les plannings associés à un lieu spécifique
     *
     * @param Lieu $lieu
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function plannings(Lieu $lieu, Request $request)
    {
        // Vérifier que le lieu appartient à la société de l'utilisateur connecté
        if ($lieu->societe_id !== Auth::user()->societe->id) {
            return redirect()->route('lieux.index')
                ->with('error', 'Vous n\'avez pas accès à ce lieu.');
        }
        
        // Récupérer les employés qui ont des plannings dans ce lieu
        $employesIds = $lieu->plannings()
            ->distinct('employe_id')
            ->pluck('employe_id');
            
        $employes = \App\Models\Employe::whereIn('id', $employesIds)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        
        // Récupérer l'employé sélectionné (si présent)
        $selectedEmployeId = $request->query('employe_id');
        
        // Récupérer le tri demandé
        $sortBy = $request->query('sort_by', 'date'); // Par défaut, tri par date
        $sortOrder = $request->query('sort_order', 'desc'); // Par défaut, ordre décroissant
        
        // Valider les options de tri
        $validSortFields = ['date', 'heures_travaillees'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'date';
        }
        
        $validSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $validSortOrders)) {
            $sortOrder = 'desc';
        }
        
        // Récupérer les plannings associés à ce lieu
        $query = $lieu->plannings()->with('employe');
        
        // Filtrer par employé si sélectionné
        if ($selectedEmployeId) {
            $query->where('employe_id', $selectedEmployeId);
        }
        
        // Appliquer le tri
        $query->orderBy($sortBy, $sortOrder);
        
        $plannings = $query->paginate(15)->withQueryString();
        
        return view('lieux.plannings', compact('lieu', 'plannings', 'sortBy', 'sortOrder', 'employes', 'selectedEmployeId'));
    }
    
    /**
     * Duplique un lieu existant
     *
     * @param Lieu $lieu
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Lieu $lieu)
    {
        // Vérifier que le lieu appartient à la société de l'utilisateur connecté
        if ($lieu->societe_id !== Auth::user()->societe->id) {
            return redirect()->route('lieux.index')
                ->with('error', 'Vous n\'avez pas accès à ce lieu.');
        }
        
        // Créer une copie du lieu
        $newLieu = $lieu->replicate();
        $newLieu->nom = $lieu->nom . ' (copie)';
        $newLieu->save();
        
        return redirect()->route('lieux.edit', $newLieu->id)
            ->with('success', 'Le lieu a été dupliqué avec succès. Vous pouvez maintenant le modifier.');
    }
    
    /**
     * Exporte la liste des lieux au format PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        // Récupérer les lieux de la société de l'utilisateur connecté
        $lieux = Lieu::where('societe_id', Auth::user()->societe->id)
            ->orderBy('nom')
            ->get();
            
        // Générer le PDF avec la vue
        $pdf = \PDF::loadView('lieux.export.pdf', compact('lieux'));
        
        // Télécharger le PDF
        return $pdf->download('lieux-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Exporte la liste des lieux au format Excel
     *
     * @return \Illuminate\Http\Response
     */
    public function exportExcel()
    {
        return \Excel::download(new \App\Exports\LieuxExport(Auth::user()->societe->id), 'lieux-' . now()->format('Y-m-d') . '.xlsx');
    }
}