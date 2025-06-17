<?php

namespace App\Notifications\Echange;

use App\Models\Employe;
use App\Models\Echange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeAcceptedNotification extends Notification
{
    use Queueable;

    protected $echange;
    protected $requestingEmploye;
    protected $targetEmploye;

    /**
     * Create a new notification instance.
     */
    public function __construct(Echange $echange, Employe $requestingEmploye, Employe $targetEmploye)
    {
        $this->echange = $echange;
        $this->requestingEmploye = $requestingEmploye;
        $this->targetEmploye = $targetEmploye;
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
                    ->subject('Échange de planning accepté')
                    ->greeting('Bonjour,')
                    ->line('Un échange de planning a été accepté entre ' . $this->requestingEmploye->prenom . ' ' . $this->requestingEmploye->nom . ' et ' . $this->targetEmploye->prenom . ' ' . $this->targetEmploye->nom . '.')
                    ->line('Date 1: ' . \Carbon\Carbon::parse($this->echange->date)->format('d/m/Y'))
                    ->line('Date 2: ' . \Carbon\Carbon::parse($this->echange->target_date)->format('d/m/Y'))
                    ->action('Voir les plannings', url('/plannings/calendar'))
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
            'type' => 'exchange_accepted',
            'title' => 'Échange de planning accepté',
            'message' => 'Un échange de planning a été accepté entre ' . $this->requestingEmploye->prenom . ' ' . $this->requestingEmploye->nom . ' et ' . $this->targetEmploye->prenom . ' ' . $this->targetEmploye->nom . ' pour les dates du ' . \Carbon\Carbon::parse($this->echange->date)->format('d/m/Y') . ' et du ' . \Carbon\Carbon::parse($this->echange->target_date)->format('d/m/Y'),
            'icon' => 'fa-check-circle',
            'color' => 'green',
            'requesting_employe_id' => $this->requestingEmploye->id,
            'target_employe_id' => $this->targetEmploye->id
        ];
    }
}
