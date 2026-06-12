<?php

namespace App\Mail;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProviderApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Provider $provider)
    {
        $this->provider->loadMissing('user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Provider Application Has Been Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.provider-approved',
            with: ['provider' => $this->provider],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}