<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, subject: string, message: string}  $payload
     */
    public function __construct(
        public array $payload
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Nouveau message de contact : '.$this->payload['subject'])
            ->replyTo($this->payload['email'], $this->payload['name'])
            ->view('emails.contact-message');
    }
}
