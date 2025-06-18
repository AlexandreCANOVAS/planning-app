<?php

namespace App\Notifications\Conge;

use App\Models\HistoriqueConge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SoldeCongeModifiedNotification extends Notification
{
    use Queueable;

    protected $historiqueConge;

    /**
     * Create a new notification instance.
     */
    public function __construct(HistoriqueConge $historiqueConge)
    {
        $this->historiqueConge = $historiqueConge;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Modification de votre solde de congés')
                    ->line('Votre solde de congés a été modifié.')
                    ->line('Ancien solde: ' . $this->historiqueConge->ancien_solde_conges . ' jours de congés payés, ' . 
                           $this->historiqueConge->ancien_solde_rtt . ' jours de RTT, ' . 
                           $this->historiqueConge->ancien_solde_conges_exceptionnels . ' jours de congés exceptionnels.')
                    ->line('Nouveau solde: ' . $this->historiqueConge->nouveau_solde_conges . ' jours de congés payés, ' . 
                           $this->historiqueConge->nouveau_solde_rtt . ' jours de RTT, ' . 
                           $this->historiqueConge->nouveau_solde_conges_exceptionnels . ' jours de congés exceptionnels.')
                    ->line('Commentaire: ' . ($this->historiqueConge->commentaire ?? 'Aucun commentaire'))
                    ->action('Voir les détails', url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $changes = [];
        
        if ($this->historiqueConge->ancien_solde_conges != $this->historiqueConge->nouveau_solde_conges) {
            $changes[] = 'Congés payés: ' . $this->historiqueConge->ancien_solde_conges . ' → ' . $this->historiqueConge->nouveau_solde_conges;
        }
        
        if ($this->historiqueConge->ancien_solde_rtt != $this->historiqueConge->nouveau_solde_rtt) {
            $changes[] = 'RTT: ' . $this->historiqueConge->ancien_solde_rtt . ' → ' . $this->historiqueConge->nouveau_solde_rtt;
        }
        
        if ($this->historiqueConge->ancien_solde_conges_exceptionnels != $this->historiqueConge->nouveau_solde_conges_exceptionnels) {
            $changes[] = 'Congés exceptionnels: ' . $this->historiqueConge->ancien_solde_conges_exceptionnels . ' → ' . $this->historiqueConge->nouveau_solde_conges_exceptionnels;
        }
        
        return [
            'title' => 'Modification de votre solde de congés',
            'message' => implode(', ', $changes),
            'icon' => 'calendar',
            'color' => 'purple',
            'url' => '/dashboard',
            'commentaire' => $this->historiqueConge->commentaire,
            'user_id' => $this->historiqueConge->user_id,
            'user_name' => $this->historiqueConge->user->name ?? 'Système',
            'created_at' => $this->historiqueConge->created_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
