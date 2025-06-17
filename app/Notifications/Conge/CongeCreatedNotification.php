<?php

namespace App\Notifications\Conge;

use App\Models\Conge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CongeCreatedNotification extends Notification
{
    use Queueable;

    protected $conge;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conge $conge)
    {
        $this->conge = $conge;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Nouvelle demande de congé')
                    ->greeting('Bonjour,')
                    ->line('Une nouvelle demande de congé a été créée.')
                    ->line('Employé: ' . $this->conge->employe->prenom . ' ' . $this->conge->employe->nom)
                    ->line('Période: du ' . $this->conge->date_debut->format('d/m/Y') . ' au ' . $this->conge->date_fin->format('d/m/Y'))
                    ->action('Voir la demande', url('/employeur/conges/' . $this->conge->id))
                    ->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->conge->id,
            'type' => 'conge_created',
            'title' => 'Nouvelle demande de congé',
            'message' => 'Une nouvelle demande de congé a été créée par ' . $this->conge->employe->prenom . ' ' . $this->conge->employe->nom,
            'icon' => 'fa-umbrella-beach',
            'color' => 'blue'
        ];
    }
}
