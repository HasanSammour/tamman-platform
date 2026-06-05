<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FreeSessionRedeemedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rewardName;
    public $sessionType;
    public $pointsSpent;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $rewardName, $sessionType, $pointsSpent)
    {
        $this->user = $user;
        $this->rewardName = $rewardName;
        $this->sessionType = $sessionType;
        $this->pointsSpent = $pointsSpent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Free Session Unlocked!') . ' - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.free-session-redeemed',
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
