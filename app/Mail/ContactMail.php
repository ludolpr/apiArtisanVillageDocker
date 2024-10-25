<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject('Un mail envoyé depuis Artisan Village')
        ->view('contact');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Un mail envoyé depuis Artisan Village',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'contact',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}