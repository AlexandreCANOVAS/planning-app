<?php

namespace App\Notifications;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanningUpdatedNotification extends Notification
{
    use Queueable;

    protected $planning;
    protected $changes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Planning $planning, array $changes = [])
    {
        $this->planning = $planning;
        $this->changes = $changes;
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
        $mail = (new MailMessage)
            ->subject('Modification de votre planning')
            ->greeting('Bonjour ' . $this->planning->employe->prenom . ',')
            ->line('Votre planning du ' . $this->planning->date->format('d/m/Y') . ' a été modifié.');

        if (!empty($this->changes)) {
            $mail->line('Modifications apportées :');
            foreach ($this->changes as $field => $values) {
                $label = $this->getFieldLabel($field);
                $mail->line("- $label : {$values['old']} → {$values['new']}");
            }
        }

        return $mail
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
            'type' => 'planning_updated',
            'title' => 'Planning modifié',
            'message' => 'Votre planning du mois de ' . $this->planning->date->locale('fr')->isoFormat('MMMM YYYY') . ' a été modifié.',
            'icon' => 'fa-calendar-edit',
            'color' => 'yellow',
            'changes' => $this->changes,
            'mois' => $this->planning->date->month,
            'annee' => $this->planning->date->year
        ];
    }

    /**
     * Get human-readable field label.
     */
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'heure_debut' => 'Heure de début',
            'heure_fin' => 'Heure de fin',
            'lieu_id' => 'Lieu de travail',
            'date' => 'Date',
        ];

        return $labels[$field] ?? $field;
    }
}
