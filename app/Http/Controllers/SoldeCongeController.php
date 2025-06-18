<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\HistoriqueConge;
use App\Notifications\Conge\SoldeCongeModifiedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SoldeCongeController extends Controller
{
    /**
     * Affiche la liste des employés pour la modification des soldes de congés
     */
    public function index()
    {
        // Récupérer les employés de la société de l'utilisateur connecté
        $employes = Employe::where('societe_id', Auth::user()->societe->id)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('conges.solde.index', compact('employes'));
    }

    /**
     * Affiche le formulaire de modification des soldes de congés
     */
    public function edit(Employe $employe)
    {
        // Vérifier que l'utilisateur est autorisé (employeur de la même société)
        if ($employe->societe_id !== Auth::user()->societe->id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier les congés de cet employé.');
        }

        return view('conges.solde.edit', compact('employe'));
    }

    /**
     * Met à jour les soldes de congés d'un employé
     */
    public function update(Request $request, Employe $employe)
    {
        try {
            // Vérifier que l'utilisateur est autorisé (employeur de la même société)
            if ($employe->societe_id !== Auth::user()->societe->id) {
                return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier les congés de cet employé.');
            }
            
            // Valider les données du formulaire
            $validated = $request->validate([
                'solde_conges' => 'required|numeric|min:0',
                'solde_rtt' => 'required|numeric|min:0',
                'solde_conges_exceptionnels' => 'required|numeric|min:0',
                'commentaire' => 'nullable|string|max:255',
            ]);
            
            // Convertir explicitement en float pour s'assurer du bon format
            $soldeConges = (float)$validated['solde_conges'];
            $soldeRtt = (float)$validated['solde_rtt'];
            $soldeExceptionnels = (float)$validated['solde_conges_exceptionnels'];
            $commentaire = $validated['commentaire'] ?? 'Ajustement manuel par l\'employeur';
            
            // Log des valeurs avant mise à jour
            Log::info('SoldeCongeController: Valeurs avant mise à jour', [
                'employe_id' => $employe->id,
                'ancien_solde_conges' => $employe->solde_conges,
                'nouveau_solde_conges' => $soldeConges,
                'ancien_solde_rtt' => $employe->solde_rtt,
                'nouveau_solde_rtt' => $soldeRtt
            ]);
            
            // Créer un enregistrement dans l'historique des soldes de congés
            $historique = HistoriqueConge::create([
                'employe_id' => $employe->id,
                'user_id' => Auth::id(),
                'type_modification' => 'ajustement_manuel',
                'ancien_solde_conges' => $employe->solde_conges,
                'ancien_solde_rtt' => $employe->solde_rtt,
                'ancien_solde_conges_exceptionnels' => $employe->solde_conges_exceptionnels,
                'nouveau_solde_conges' => $soldeConges,
                'nouveau_solde_rtt' => $soldeRtt,
                'nouveau_solde_conges_exceptionnels' => $soldeExceptionnels,
                'commentaire' => $commentaire
            ]);
            
            // Utiliser uniquement Eloquent pour la mise à jour
            // Récupérer une instance fraîche de l'employé pour éviter les problèmes de cache
            $employe = Employe::find($employe->id);
            
            // Définir les valeurs avec le bon type
            $employe->solde_conges = (float)$soldeConges;
            $employe->solde_rtt = (float)$soldeRtt;
            $employe->solde_conges_exceptionnels = (float)$soldeExceptionnels;
            
            // Sauvegarder les modifications
            $employe->save();
            
            // Rafraîchir l'instance pour s'assurer que les données sont à jour
            $employe->refresh();
            
            // Log des valeurs après mise à jour
            Log::info('SoldeCongeController: Valeurs après mise à jour', [
                'employe_id' => $employe->id,
                'solde_conges' => $employe->solde_conges,
                'solde_rtt' => $employe->solde_rtt,
                'solde_conges_exceptionnels' => $employe->solde_conges_exceptionnels
            ]);
            
            // Notifier l'employé si les soldes ont été modifiés
            if ($employe->user) {
                try {
                    $employe->user->notify(new SoldeCongeModifiedNotification($historique));
                    Log::info('SoldeCongeController: Notification envoyée à l\'employé', [
                        'employe_id' => $employe->id,
                        'user_id' => $employe->user->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('SoldeCongeController: Erreur lors de l\'envoi de la notification', [
                        'message' => $e->getMessage(),
                        'employe_id' => $employe->id
                    ]);
                }
            }
            
            // Supprimer tous les messages d'erreur potentiels
            session()->forget(['error', 'errors']);
            
            // Utiliser une redirection avec flash uniquement pour le message de succès
            // Utiliser la méthode flash() pour s'assurer que le message est disponible uniquement pour la prochaine requête
            session()->flash('success', 'Les soldes de congés de ' . $employe->prenom . ' ' . $employe->nom . ' ont été mis à jour avec succès.');
            
            // Rediriger vers la page d'index
            return redirect()->route('conges.solde.index');
            
                
        } catch (\Exception $e) {
            Log::error('SoldeCongeController: Erreur lors de la mise à jour des soldes', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour des soldes de congés.')
                ->withInput();
        }
    }
    
    /**
     * Affiche l'historique des modifications de solde de congés
     */
    public function historique(Employe $employe)
    {
        // Vérifier que l'utilisateur est autorisé (employeur de la même société)
        if ($employe->societe_id !== Auth::user()->societe->id) {
            abort(403, 'Vous n\'\u00eates pas autorisé à consulter l\'historique des congés de cet employé.');
        }
        
        // Récupérer l'historique des modifications de solde de congés
        $historique = HistoriqueConge::where('employe_id', $employe->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('conges.solde.historique', compact('employe', 'historique'));
    }
}
