<?php

namespace App\Mail;

use App\Models\Planning;
use App\Models\User;
use App\Services\PlanningPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanningCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $pdfPath;
    
    public function __construct(
        public Planning $planning,
        public User $employee
    ) {
        // Générer le PDF du planning
        $pdfService = new PlanningPdfService();
        $this->pdfPath = $pdfService->generatePlanningPdf($planning, $employee);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau planning disponible',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.planning.created-modern',
            with: [
                'planning' => $this->planning,
                'employee' => $this->employee,
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
        // Vérifier si le fichier PDF existe
        if (file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('planning_' . $this->planning->employe_id . '.html')
                    ->withMime('text/html'),
            ];
        }
        
        return [];
    }
}
