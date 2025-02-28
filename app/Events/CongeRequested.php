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

class CongeRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $congesEnAttente;
    private $societeId;

    /**
     * Create a new event instance.
     */
    public function __construct(Conge $conge)
    {
        $this->societeId = $conge->employe->user->societe_id;
        $this->congesEnAttente = Conge::where('statut', 'en_attente')
            ->whereHas('employe.user', function ($query) use ($conge) {
                $query->where('societe_id', $conge->employe->user->societe_id);
            })
            ->count();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('societe.' . $this->societeId);
    }

    /**
     * Get the broadcast event name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'CongeRequested';
    }
}
