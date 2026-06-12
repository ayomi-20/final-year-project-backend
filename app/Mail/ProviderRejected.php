<?php

namespace App\Mail;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProviderRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Provider $provider,
        public string $reason
    ) {
        $this->provider->loadMissing('user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Provider Application',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.provider-rejected',
            with: [
                'provider' => $this->provider,
                'reason'   => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}