<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public string $plainPassword,
        public string $role,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Admin Account Has Been Created – ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_created',
            with: [
                'admin'         => $this->admin,
                'plainPassword' => $this->plainPassword,
                'role'          => $this->role,
                'dashboardUrl'  => config('app.url') . '/admin',
            ],
        );
    }
}