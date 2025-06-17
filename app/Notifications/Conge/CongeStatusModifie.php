<?php

namespace App\Notifications\Conge;

use App\Models\Conge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CongeStatusModifie extends Notification implements ShouldQueue
{
    use Queueable;

    public $conge;

    public function __construct(Conge $conge)
    {
        $this->conge = $conge;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $status = [
            'accepte' => 'acceptée',
            'refuse' => 'refusée',
            'en_attente' => 'en attente'
        ][$this->conge->statut];

        return (new MailMessage)
            ->subject('Mise à jour de votre demande de congé')
            ->line('Votre demande de congé a été ' . $status . '.')
            ->line('Période : du ' . $this->conge->date_debut->format('d/m/Y') . ' au ' . $this->conge->date_fin->format('d/m/Y'))
            ->line('Durée : ' . number_format($this->conge->duree, 1) . ' jours')
            ->action('Voir les détails', route('employe.mes-conges'));
    }

    public function toArray($notifiable)
    {
        $status = [
            'accepte' => 'acceptée',
            'refuse' => 'refusée',
            'en_attente' => 'en attente'
        ][$this->conge->statut];

        return [
            'conge_id' => $this->conge->id,
            'message' => 'Votre demande de congé a été ' . $status
        ];
    }
}
