<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact)
    {
    }

    public function envelope(): Envelope
    {
        $replyTo = filter_var($this->contact->email, FILTER_VALIDATE_EMAIL)
            ? [new Address($this->contact->email, $this->contact->name)]
            : [];

        return new Envelope(
            subject: '[Norm] Liên hệ mới từ ' . $this->contact->name,
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-lead',
        );
    }
}

