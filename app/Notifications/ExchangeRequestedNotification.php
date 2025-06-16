<?php

namespace App\Notifications;

use App\Models\EchangeJour;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeRequestedNotification extends Notification
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
        return (new MailMessage)
                    ->subject('Nouvelle demande d\'échange de planning')
                    ->greeting('Bonjour,')
                    ->line($this->echange->demandeur->prenom . ' ' . $this->echange->demandeur->nom . ' vous a envoyé une demande d\'échange de planning.')
                    ->line('Jour proposé: ' . $this->echange->jour_demandeur->format('d/m/Y'))
                    ->line('En échange de votre jour: ' . $this->echange->jour_receveur->format('d/m/Y'))
                    ->action('Voir la demande', url('/employe/echanges'))
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
            'id' => $this->echange->id,
            'type' => 'exchange_requested',
            'title' => 'Nouvelle demande d\'échange',
            'message' => $this->echange->demandeur->prenom . ' ' . $this->echange->demandeur->nom . ' vous propose d\'échanger son jour du ' . $this->echange->jour_demandeur->format('d/m/Y') . ' contre votre jour du ' . $this->echange->jour_receveur->format('d/m/Y') . '.',
            'icon' => 'fa-exchange-alt',
            'color' => 'purple'
        ];
    }
}
