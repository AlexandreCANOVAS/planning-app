<?php

namespace App\Mail;

use App\Models\Conge;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CongeStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Conge $conge,
        public User $employee,
        public string $status
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = match($this->status) {
            'approved' => 'approuvée',
            'rejected' => 'refusée',
            default => 'mise à jour'
        };
        
        return new Envelope(
            subject: "Demande de congé {$statusText}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.conges.status-updated',
            with: [
                'conge' => $this->conge,
                'employee' => $this->employee,
                'status' => $this->status,
                'url' => route('employee.conges.show', $this->conge->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
