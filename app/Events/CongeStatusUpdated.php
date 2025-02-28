<?php

namespace App\Events;

use App\Models\Conge;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CongeStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $congesEnAttente;
    private $societeId;

    public function __construct(Conge $conge)
    {
        $this->societeId = $conge->employe->user->societe_id;
        $this->congesEnAttente = Conge::where('statut', 'en_attente')
            ->whereHas('employe.user', function ($query) use ($conge) {
                $query->where('societe_id', $conge->employe->user->societe_id);
            })
            ->count();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('societe.' . $this->societeId);
    }

    public function broadcastAs()
    {
        return 'CongeStatusUpdated';
    }
}
