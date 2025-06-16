<?php

namespace App\Notifications;

use App\Models\Conge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CongeStatusChangedNotification extends Notification
{
    use Queueable;

    protected $conge;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conge $conge, string $oldStatus)
    {
        $this->conge = $conge;
        $this->oldStatus = $oldStatus;
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
        $statusText = $this->conge->statut === 'accepte' ? 'acceptée' : 'refusée';
        $color = $this->conge->statut === 'accepte' ? 'success' : 'danger';
        
        return (new MailMessage)
                    ->subject('Statut de votre demande de congé mis à jour')
                    ->greeting('Bonjour ' . $this->conge->employe->prenom . ',')
                    ->line('Le statut de votre demande de congé a été mis à jour.')
                    ->line('Votre demande pour la période du ' . $this->conge->date_debut->format('d/m/Y') . ' au ' . $this->conge->date_fin->format('d/m/Y') . ' a été ' . $statusText . '.')
                    ->action('Voir les détails', url('/employe/conges/' . $this->conge->id))
                    ->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusText = $this->conge->statut === 'accepte' ? 'acceptée' : 'refusée';
        $color = $this->conge->statut === 'accepte' ? 'green' : 'red';
        $icon = $this->conge->statut === 'accepte' ? 'fa-check-circle' : 'fa-times-circle';
        
        return [
            'id' => $this->conge->id,
            'type' => 'conge_status_changed',
            'title' => 'Statut de congé mis à jour',
            'message' => 'Votre demande de congé pour la période du ' . $this->conge->date_debut->format('d/m/Y') . ' au ' . $this->conge->date_fin->format('d/m/Y') . ' a été ' . $statusText . '.',
            'icon' => $icon,
            'color' => $color
        ];
    }
}
