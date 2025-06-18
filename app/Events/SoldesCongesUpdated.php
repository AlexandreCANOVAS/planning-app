<?php

namespace App\Events;

use App\Models\Employe;
use App\Models\HistoriqueConge;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SoldesCongesUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $employe;
    public $historique;

    /**
     * Create a new event instance.
     */
    public function __construct(Employe $employe, HistoriqueConge $historique)
    {
        $this->employe = $employe;
        $this->historique = $historique;
        
        Log::info('Événement SoldesCongesUpdated créé', [
            'employe_id' => $employe->id,
            'societe_id' => $employe->societe_id,
            'solde_conges' => (float)$employe->solde_conges,
            'solde_rtt' => (float)$employe->solde_rtt,
            'solde_conges_exceptionnels' => (float)$employe->solde_conges_exceptionnels
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Diffuser sur le canal de la société pour que l'employeur reçoive la mise à jour
        if ($this->employe->societe_id) {
            Log::info('Diffusion sur le canal societe.' . $this->employe->societe_id);
            return [
                new PrivateChannel('societe.' . $this->employe->societe_id),
            ];
        }
        
        return [];
    }
    
    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'SoldesCongesUpdated';
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        // Rafraîchir le modèle pour s'assurer d'avoir les données les plus récentes
        $employe = Employe::find($this->employe->id);
        
        // Log des données diffusées
        Log::info('Données diffusées par SoldesCongesUpdated', [
            'employe_id' => $employe->id,
            'nom_complet' => $employe->prenom . ' ' . $employe->nom,
            'solde_conges' => (float)$employe->solde_conges,
            'solde_rtt' => (float)$employe->solde_rtt,
            'solde_conges_exceptionnels' => (float)$employe->solde_conges_exceptionnels
        ]);
        
        return [
            'employe_id' => $employe->id,
            'nom_complet' => $employe->prenom . ' ' . $employe->nom,
            'solde_conges' => (float)$employe->solde_conges,
            'solde_rtt' => (float)$employe->solde_rtt,
            'solde_conges_exceptionnels' => (float)$employe->solde_conges_exceptionnels,
            'historique_id' => $this->historique->id,
            'commentaire' => $this->historique->commentaire,
            'timestamp' => now()->timestamp
        ];
    }
}
