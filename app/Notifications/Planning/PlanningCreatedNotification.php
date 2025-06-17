<?php

namespace App\Notifications\Planning;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanningCreatedNotification extends Notification
{
    use Queueable;

    protected $planning;

    /**
     * Create a new notification instance.
     */
    public function __construct(Planning $planning)
    {
        $this->planning = $planning;
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
                    ->subject('Nouveau planning créé')
                    ->greeting('Bonjour ' . $this->planning->employe->prenom . ',')
                    ->line('Un nouveau planning a été créé pour vous.')
                    ->line('Date: ' . $this->planning->date->format('d/m/Y'))
                    ->line('Horaires: ' . substr($this->planning->heure_debut, 0, 5) . ' - ' . substr($this->planning->heure_fin, 0, 5))
                    ->line('Lieu: ' . $this->planning->lieu->nom)
                    ->action('Voir mon planning', url('/employe/plannings'))
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
            'id' => $this->planning->id,
            'type' => 'planning_created',
            'title' => 'Nouveau planning créé',
            'message' => 'Votre nouveau planning pour le mois de ' . $this->planning->date->locale('fr')->isoFormat('MMMM YYYY') . ' a été créé',
            'icon' => 'fa-calendar-plus',
            'color' => 'indigo',
            'mois' => $this->planning->date->month,
            'annee' => $this->planning->date->year
        ];
    }
}
