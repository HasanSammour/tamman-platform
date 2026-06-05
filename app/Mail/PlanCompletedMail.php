<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanCompletedMail extends Mailable
{
    use Queueable, SerializesModels;
public $user;
    public $plan;
    public $bonusPoints;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $plan, $bonusPoints)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->bonusPoints = $bonusPoints;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Treatment Plan Completed - ') . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.plan-completed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
