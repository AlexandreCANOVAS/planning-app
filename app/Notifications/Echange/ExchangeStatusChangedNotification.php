<?php

namespace App\Notifications\Echange;

use App\Models\EchangeJour;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeStatusChangedNotification extends Notification
{
    use Queueable;

    protected $echange;

    /**
     * Create a new notification instance.
     */
    public function __construct(EchangeJour $echange)
    {
        $this->echange = $echange;
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
        $statusText = $this->echange->statut === 'accepte' ? 'acceptée' : 'refusée';
        $color = $this->echange->statut === 'accepte' ? 'success' : 'danger';
        
        return (new MailMessage)
                    ->subject('Réponse à votre demande d\'échange')
                    ->greeting('Bonjour ' . $this->echange->demandeur->prenom . ',')
                    ->line('Votre demande d\'échange de planning a été ' . $statusText . '.')
                    ->line('Jour proposé: ' . $this->echange->jour_demandeur->format('d/m/Y'))
                    ->line('En échange du jour: ' . $this->echange->jour_receveur->format('d/m/Y'))
                    ->action('Voir les détails', url('/employe/plannings/liste-echanges'))
                    ->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusText = $this->echange->statut === 'accepte' ? 'acceptée' : 'refusée';
        $color = $this->echange->statut === 'accepte' ? 'green' : 'red';
        $icon = $this->echange->statut === 'accepte' ? 'fa-check-circle' : 'fa-times-circle';
        
        return [
            'id' => $this->echange->id,
            'type' => 'exchange_status_changed',
            'title' => 'Réponse à votre demande d\'échange',
            'message' => 'Votre demande d\'échange de planning pour le ' . $this->echange->jour_demandeur->format('d/m/Y') . ' a été ' . $statusText . '.',
            'icon' => $icon,
            'color' => $color
        ];
    }
}
