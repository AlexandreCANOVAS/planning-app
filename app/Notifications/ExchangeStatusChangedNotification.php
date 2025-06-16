<?php

namespace App\Notifications;

use App\Models\Echange;
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
    public function __construct(Echange $echange)
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
        $statusText = $this->echange->status === 'accepted' ? 'acceptée' : 'refusée';
        $color = $this->echange->status === 'accepted' ? 'success' : 'danger';
        
        return (new MailMessage)
                    ->subject('Réponse à votre demande d\'échange')
                    ->greeting('Bonjour ' . $this->echange->employe->prenom . ',')
                    ->line('Votre demande d\'échange de planning a été ' . $statusText . '.')
                    ->line('Jour proposé: ' . \Carbon\Carbon::parse($this->echange->date)->format('d/m/Y'))
                    ->line('En échange du jour: ' . \Carbon\Carbon::parse($this->echange->target_date)->format('d/m/Y'))
                    ->action('Voir les détails', url('/employe/echanges'))
                    ->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusText = $this->echange->status === 'accepted' ? 'acceptée' : 'refusée';
        $color = $this->echange->status === 'accepted' ? 'green' : 'red';
        $icon = $this->echange->status === 'accepted' ? 'fa-check-circle' : 'fa-times-circle';
        
        return [
            'id' => $this->echange->id,
            'type' => 'exchange_status_changed',
            'title' => 'Réponse à votre demande d\'échange',
            'message' => 'Votre demande d\'échange de planning pour le ' . \Carbon\Carbon::parse($this->echange->date)->format('d/m/Y') . ' a été ' . $statusText . '.',
            'icon' => $icon,
            'color' => $color
        ];
    }
}
