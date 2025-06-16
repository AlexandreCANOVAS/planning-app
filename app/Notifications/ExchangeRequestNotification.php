<?php

namespace App\Notifications;

use App\Models\Employe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExchangeRequestNotification extends Notification
{
    use Queueable;

    protected $requestingEmploye;
    protected $targetEmploye;
    protected $date;
    protected $targetDate;
    protected $echangeId;

    /**
     * Create a new notification instance.
     */
    public function __construct(Employe $requestingEmploye, Employe $targetEmploye, $date, $targetDate, $echangeId = null)
    {
        $this->requestingEmploye = $requestingEmploye;
        $this->targetEmploye = $targetEmploye;
        $this->date = $date;
        $this->targetDate = $targetDate;
        $this->echangeId = $echangeId;
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
                    ->subject('Demande d\'échange de planning')
                    ->greeting('Bonjour ' . $this->targetEmploye->prenom . ',')
                    ->line($this->requestingEmploye->prenom . ' ' . $this->requestingEmploye->nom . ' vous a envoyé une demande d\'échange de planning.')
                    ->line('Date proposée: ' . $this->date->format('d/m/Y'))
                    ->line('En échange de votre date: ' . $this->targetDate->format('d/m/Y'))
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
            'id' => $this->echangeId,
            'type' => 'exchange_request',
            'title' => 'Demande d\'échange de planning',
            'message' => $this->requestingEmploye->prenom . ' ' . $this->requestingEmploye->nom . ' vous propose d\'échanger votre planning du ' . $this->targetDate->format('d/m/Y') . ' avec le sien du ' . $this->date->format('d/m/Y'),
            'icon' => 'fa-exchange-alt',
            'color' => 'blue',
            'requesting_employe_id' => $this->requestingEmploye->id,
            'target_employe_id' => $this->targetEmploye->id,
            'date' => $this->date->format('Y-m-d'),
            'target_date' => $this->targetDate->format('Y-m-d')
        ];
    }
}
