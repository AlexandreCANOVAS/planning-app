<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $token;
    public string $societeName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $societeName)
    {
        $this->token = $token;
        $this->societeName = $societeName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invitationUrl = route('employee.invitation.accept', ['token' => $this->token]);

        return (new MailMessage)
                    ->subject('Invitation à rejoindre ' . $this->societeName)
                    ->greeting('Bonjour !')
                    ->line('Vous avez été invité(e) par ' . $this->societeName . ' à rejoindre leur espace sur notre plateforme de planning.')
                    ->line('Pour finaliser votre inscription, veuillez cliquer sur le bouton ci-dessous.')
                    ->action('Activer mon compte', $invitationUrl)
                    ->line('Ce lien d\'invitation expirera dans 7 jours.')
                    ->line('Si vous n\'êtes pas à l\'origine de cette invitation, vous pouvez ignorer cet email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
