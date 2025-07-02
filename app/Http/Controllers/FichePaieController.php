<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\FichePaie;
use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PDF;

class FichePaieController extends Controller
{
    /**
     * Affiche la liste des fiches de paie
     */
    public function index()
    {
        // Récupérer les employés de la société de l'utilisateur connecté
        $employes = Employe::where('societe_id', Auth::user()->societe_id)
            ->with('user')
            ->get();
            
        $moisActuel = Carbon::now()->format('Y-m');
        
        // Récupérer les fiches de paie
        $fichesPaie = FichePaie::whereIn('employe_id', $employes->pluck('id'))
            ->orderBy('mois', 'desc')
            ->orderBy('employe_id')
            ->paginate(20);
        
        return view('fiches-paie.index', compact('employes', 'moisActuel', 'fichesPaie'));
    }
    
    /**
     * Affiche le formulaire de création d'une fiche de paie
     */
    public function create()
    {
        $employes = Employe::where('societe_id', Auth::user()->societe_id)
            ->with('user')
            ->get();
            
        $moisActuel = Carbon::now()->format('Y-m');
        
        return view('fiches-paie.create', compact('employes', 'moisActuel'));
    }
    
    /**
     * Enregistre une nouvelle fiche de paie
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employe_id' => 'required|exists:employes,id',
            'mois' => [
                'required',
                'date_format:Y-m',
                Rule::unique('fiches_paie')->where(function ($query) use ($request) {
                    return $query->where('employe_id', $request->employe_id);
                }),
            ],
            'salaire_base' => 'required|numeric|min:0',
            'prime_transport' => 'nullable|numeric|min:0',
            'prime_anciennete' => 'nullable|numeric|min:0',
            'prime_performance' => 'nullable|numeric|min:0',
            'autres_primes' => 'nullable|numeric|min:0',
            'indemnites_repas' => 'nullable|numeric|min:0',
            'cotisations_salariales' => 'required|numeric|min:0',
            'cotisations_patronales' => 'required|numeric|min:0',
            'impot_revenu' => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Récupérer les données des heures travaillées à partir du contrôleur de comptabilité
        $comptabiliteController = new ComptabiliteController();
        
        // Extraire le mois et l'année du format Y-m
        $dateParts = explode('-', $request->mois);
        $annee = $dateParts[0];
        $mois = $dateParts[1];
        
        // Appeler la méthode calculerHeures avec les paramètres d'URL
        $response = $comptabiliteController->calculerHeures($request->employe_id, $mois, $annee);
        
        // Convertir la réponse JSON en tableau
        $heuresData = json_decode($response->getContent(), true);
        
        // Convertir les heures du format HH:MM en décimal pour les calculs
        function convertirHHMMEnDecimal($hhmmStr) {
            if (!$hhmmStr || $hhmmStr === '00:00') return 0;
            
            $parts = explode(':', $hhmmStr);
            $heures = intval($parts[0]);
            $minutes = isset($parts[1]) ? intval($parts[1]) : 0;
            
            return $heures + ($minutes / 60);
        }
        
        // Calculer les montants des heures
        $tauxHoraire = $request->salaire_base / 151.67; // 35h * 4.33 semaines
        
        // Vérifier si nous avons les données nécessaires
        if (isset($heuresData['total_mois'])) {
            // Convertir les heures du format HH:MM en décimal
            $totalHeures = convertirHHMMEnDecimal($heuresData['total_mois']['heures']);
            $heuresSup25 = convertirHHMMEnDecimal($heuresData['total_mois']['heures_sup_25']);
            $heuresSup50 = convertirHHMMEnDecimal($heuresData['total_mois']['heures_sup_50']);
            $heuresNuit = convertirHHMMEnDecimal($heuresData['total_mois']['heures_nuit']);
            $heuresDimanche = convertirHHMMEnDecimal($heuresData['total_mois']['heures_dimanche']);
            $heuresJoursFeries = convertirHHMMEnDecimal($heuresData['total_mois']['heures_jours_feries']);
            
            // Calculer les heures normales (total - toutes les heures spéciales)
            $heuresNormales = $totalHeures - $heuresSup25 - $heuresSup50 - $heuresNuit - $heuresDimanche - $heuresJoursFeries;
            
            // Calculer les montants
            $montantHeuresNormales = $heuresNormales * $tauxHoraire;
            $montantHeuresSup25 = $heuresSup25 * $tauxHoraire * 1.25;
            $montantHeuresSup50 = $heuresSup50 * $tauxHoraire * 1.5;
            $montantHeuresNuit = $heuresNuit * $tauxHoraire * 1.1;
            $montantHeuresDimanche = $heuresDimanche * $tauxHoraire * 1.5;
            $montantHeuresJoursFeries = $heuresJoursFeries * $tauxHoraire * 2;
        } else {
            // Utiliser les valeurs du formulaire si les données ne sont pas disponibles
            $heuresNormales = $request->heures_normales;
            $heuresSup25 = $request->heures_sup_25;
            $heuresSup50 = $request->heures_sup_50;
            $heuresNuit = $request->heures_nuit;
            $heuresDimanche = $request->heures_dimanche_ferie;
            $heuresJoursFeries = 0; // Pas de champ séparé pour les jours fériés dans le formulaire
            
            $montantHeuresNormales = $heuresNormales * $tauxHoraire;
            $montantHeuresSup25 = $heuresSup25 * $tauxHoraire * 1.25;
            $montantHeuresSup50 = $heuresSup50 * $tauxHoraire * 1.5;
            $montantHeuresNuit = $heuresNuit * $tauxHoraire * 1.1;
            $montantHeuresDimanche = $heuresDimanche * $tauxHoraire * 1.5;
            $montantHeuresJoursFeries = 0;
        }
        
        // Calculer le salaire brut
        $salaireBrut = $montantHeuresNormales + 
                       $montantHeuresSup25 + 
                       $montantHeuresSup50 + 
                       $montantHeuresNuit + 
                       $montantHeuresDimanche + 
                       $montantHeuresJoursFeries + 
                       $request->prime_transport + 
                       $request->prime_anciennete + 
                       $request->prime_performance + 
                       $request->autres_primes + 
                       $request->indemnites_repas;
        
        // Calculer le salaire net
        $salaireNet = $salaireBrut - $request->cotisations_salariales - $request->impot_revenu;
        
        // Créer la fiche de paie
        $fichePaie = new FichePaie();
        $fichePaie->employe_id = $request->employe_id;
        $fichePaie->mois = $request->mois;
        $fichePaie->salaire_base = $request->salaire_base;
        $fichePaie->heures_normales = $heuresNormales;
        $fichePaie->heures_sup_25 = $heuresSup25;
        $fichePaie->heures_sup_50 = $heuresSup50;
        $fichePaie->heures_nuit = $heuresNuit;
        $fichePaie->heures_dimanche = $heuresDimanche;
        $fichePaie->heures_jours_feries = $heuresJoursFeries;
        $fichePaie->montant_heures_normales = $montantHeuresNormales;
        $fichePaie->montant_heures_sup_25 = $montantHeuresSup25;
        $fichePaie->montant_heures_sup_50 = $montantHeuresSup50;
        $fichePaie->montant_heures_nuit = $montantHeuresNuit;
        $fichePaie->montant_heures_dimanche = $montantHeuresDimanche;
        $fichePaie->montant_heures_jours_feries = $montantHeuresJoursFeries;
        $fichePaie->prime_transport = $request->prime_transport ?? 0;
        $fichePaie->prime_anciennete = $request->prime_anciennete ?? 0;
        $fichePaie->prime_performance = $request->prime_performance ?? 0;
        $fichePaie->autres_primes = $request->autres_primes ?? 0;
        $fichePaie->indemnites_repas = $request->indemnites_repas ?? 0;
        $fichePaie->salaire_brut = $salaireBrut;
        $fichePaie->cotisations_salariales = $request->cotisations_salariales;
        $fichePaie->cotisations_patronales = $request->cotisations_patronales;
        $fichePaie->impot_revenu = $request->impot_revenu ?? 0;
        $fichePaie->salaire_net = $salaireNet;
        $fichePaie->salaire_net_a_payer = $salaireNet;
        $fichePaie->commentaires = $request->commentaires;
        $fichePaie->statut = 'brouillon';
        $fichePaie->save();
        
        return redirect()->route('fiches-paie.show', $fichePaie->id)
            ->with('success', 'La fiche de paie a été créée avec succès.');
    }
    
    /**
     * Affiche une fiche de paie
     */
    public function show($id)
    {
        $fichePaie = FichePaie::with('employe.user')->findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('view', $fichePaie);
        
        return view('fiches-paie.show', compact('fichePaie'));
    }
    
    /**
     * Affiche le formulaire d'édition d'une fiche de paie
     */
    public function edit($id)
    {
        $fichePaie = FichePaie::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('update', $fichePaie);
        
        // Récupérer la liste des employés pour le formulaire
        $societe = auth()->user()->societe;
        $employes = Employe::where('societe_id', $societe->id)->get();
        
        return view('fiches-paie.edit', compact('fichePaie', 'employes'));
    }
    
    /**
     * Met à jour une fiche de paie
     */
    public function update(Request $request, $id)
    {
        $fichePaie = FichePaie::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('update', $fichePaie);
        
        $validator = Validator::make($request->all(), [
            'salaire_base' => 'required|numeric|min:0',
            'heures_normales' => 'required|numeric|min:0',
            'heures_sup_25' => 'nullable|numeric|min:0',
            'heures_sup_50' => 'nullable|numeric|min:0',
            'heures_nuit' => 'nullable|numeric|min:0',
            'heures_dimanche' => 'nullable|numeric|min:0',
            'heures_jours_feries' => 'nullable|numeric|min:0',
            'montant_heures_normales' => 'required|numeric|min:0',
            'montant_heures_sup_25' => 'nullable|numeric|min:0',
            'montant_heures_sup_50' => 'nullable|numeric|min:0',
            'montant_heures_nuit' => 'nullable|numeric|min:0',
            'montant_heures_dimanche' => 'nullable|numeric|min:0',
            'montant_heures_jours_feries' => 'nullable|numeric|min:0',
            'prime_transport' => 'nullable|numeric|min:0',
            'prime_anciennete' => 'nullable|numeric|min:0',
            'prime_performance' => 'nullable|numeric|min:0',
            'autres_primes' => 'nullable|numeric|min:0',
            'indemnites_repas' => 'nullable|numeric|min:0',
            'cotisations_salariales' => 'required|numeric|min:0',
            'cotisations_patronales' => 'required|numeric|min:0',
            'impot_revenu' => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Calculer le salaire brut
        $salaireBrut = $request->montant_heures_normales + 
                       $request->montant_heures_sup_25 + 
                       $request->montant_heures_sup_50 + 
                       $request->montant_heures_nuit + 
                       $request->montant_heures_dimanche + 
                       $request->montant_heures_jours_feries + 
                       $request->prime_transport + 
                       $request->prime_anciennete + 
                       $request->prime_performance + 
                       $request->autres_primes + 
                       $request->indemnites_repas;
        
        // Calculer le salaire net
        $salaireNet = $salaireBrut - $request->cotisations_salariales - $request->impot_revenu;
        
        // Mettre à jour la fiche de paie
        $fichePaie->salaire_base = $request->salaire_base;
        $fichePaie->heures_normales = $request->heures_normales;
        $fichePaie->heures_sup_25 = $request->heures_sup_25 ?? 0;
        $fichePaie->heures_sup_50 = $request->heures_sup_50 ?? 0;
        $fichePaie->heures_nuit = $request->heures_nuit ?? 0;
        $fichePaie->heures_dimanche = $request->heures_dimanche ?? 0;
        $fichePaie->heures_jours_feries = $request->heures_jours_feries ?? 0;
        $fichePaie->montant_heures_normales = $request->montant_heures_normales;
        $fichePaie->montant_heures_sup_25 = $request->montant_heures_sup_25 ?? 0;
        $fichePaie->montant_heures_sup_50 = $request->montant_heures_sup_50 ?? 0;
        $fichePaie->montant_heures_nuit = $request->montant_heures_nuit ?? 0;
        $fichePaie->montant_heures_dimanche = $request->montant_heures_dimanche ?? 0;
        $fichePaie->montant_heures_jours_feries = $request->montant_heures_jours_feries ?? 0;
        $fichePaie->prime_transport = $request->prime_transport ?? 0;
        $fichePaie->prime_anciennete = $request->prime_anciennete ?? 0;
        $fichePaie->prime_performance = $request->prime_performance ?? 0;
        $fichePaie->autres_primes = $request->autres_primes ?? 0;
        $fichePaie->indemnites_repas = $request->indemnites_repas ?? 0;
        $fichePaie->salaire_brut = $salaireBrut;
        $fichePaie->cotisations_salariales = $request->cotisations_salariales;
        $fichePaie->cotisations_patronales = $request->cotisations_patronales;
        $fichePaie->impot_revenu = $request->impot_revenu ?? 0;
        $fichePaie->salaire_net = $salaireNet;
        $fichePaie->salaire_net_a_payer = $salaireNet;
        $fichePaie->commentaires = $request->commentaires;
        $fichePaie->save();
        
        return redirect()->route('fiches-paie.show', $fichePaie->id)
            ->with('success', 'La fiche de paie a été mise à jour avec succès.');
    }
    
    /**
     * Valide une fiche de paie
     */
    public function valider($id)
    {
        $fichePaie = FichePaie::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('update', $fichePaie);
        
        $fichePaie->statut = 'validé';
        $fichePaie->date_validation = now();
        $fichePaie->save();
        
        return redirect()->route('fiches-paie.show', $fichePaie->id)
            ->with('success', 'La fiche de paie a été validée avec succès.');
    }
    
    /**
     * Publie une fiche de paie
     */
    public function publier($id)
    {
        $fichePaie = FichePaie::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('update', $fichePaie);
        
        if ($fichePaie->statut !== 'validé') {
            return redirect()->back()
                ->with('error', 'La fiche de paie doit être validée avant d\'être publiée.');
        }
        
        $fichePaie->statut = 'publié';
        $fichePaie->date_publication = now();
        $fichePaie->save();
        
        // Envoyer une notification à l'employé (à implémenter plus tard)
        
        return redirect()->route('fiches-paie.show', $fichePaie->id)
            ->with('success', 'La fiche de paie a été publiée avec succès.');
    }
    
    /**
     * Supprime une fiche de paie
     */
    public function destroy($id)
    {
        $fichePaie = FichePaie::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('delete', $fichePaie);
        
        if ($fichePaie->statut === 'publié') {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer une fiche de paie publiée.');
        }
        
        $fichePaie->delete();
        
        return redirect()->route('fiches-paie.index')
            ->with('success', 'La fiche de paie a été supprimée avec succès.');
    }
    
    /**
     * Génère un PDF de la fiche de paie
     */
    public function exportPDF($id)
    {
        $fichePaie = FichePaie::with('employe.user', 'employe.societe')->findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette fiche de paie
        $this->authorize('view', $fichePaie);
        
        $pdf = PDF::loadView('fiches-paie.pdf', compact('fichePaie'));
        
        return $pdf->download('fiche-paie-' . $fichePaie->employe->nom . '-' . $fichePaie->mois . '.pdf');
    }
}
