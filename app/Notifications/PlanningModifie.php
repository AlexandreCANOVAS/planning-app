<?php

namespace App\Notifications;

use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable as QueueableTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\HandlesNotificationFailures;

class PlanningModifie extends Notification implements ShouldQueue
{
    use QueueableTrait, HandlesNotificationFailures;

    public $planning;

    public function __construct(Planning $planning)
    {
        $this->planning = $planning;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Modification de votre planning')
            ->view('emails.planning-modifie', [
                'planning' => $this->planning,
                'notifiable' => $notifiable
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'planning_id' => $this->planning->id,
            'message' => 'Votre planning du ' . \Carbon\Carbon::parse($this->planning->date)->format('d/m/Y') . ' a été modifié'
        ];
    }
} 