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

class SoldeCongeModified implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /**
     * Le nom de l'événement à diffuser.
     *
     * @var string
     */
    public $broadcastAs = 'solde.updated';

    public $employe;
    public $historique;

    /**
     * Create a new event instance.
     */
    public function __construct(Employe $employe, HistoriqueConge $historique)
    {
        $this->employe = $employe;
        $this->historique = $historique;
        
        // Log pour débogage
        \Illuminate\Support\Facades\Log::info('SoldeCongeModified construit', [
            'employe_id' => $employe->id,
            'historique_id' => $historique->id,
            'canal' => 'employe.' . $employe->id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employe.' . $this->employe->id),
        ];
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
        \Illuminate\Support\Facades\Log::info('Données diffusées par SoldeCongeModified', [
            'employe_id' => $employe->id,
            'solde_conges' => (float)$employe->solde_conges,
            'solde_rtt' => (float)$employe->solde_rtt,
            'solde_conges_exceptionnels' => (float)$employe->solde_conges_exceptionnels
        ]);
        
        return [
            'employe_id' => $employe->id,
            'solde_conges' => (float)$employe->solde_conges,
            'solde_rtt' => (float)$employe->solde_rtt,
            'solde_conges_exceptionnels' => (float)$employe->solde_conges_exceptionnels,
            'historique_id' => $this->historique->id,
            'commentaire' => $this->historique->commentaire,
            'timestamp' => now()->timestamp
        ];
    }
}
