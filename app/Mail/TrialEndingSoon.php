<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrialEndingSoon extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $daysLeft
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Votre periode d\'essai se termine bientot')
            ->view('emails.trial-ending-soon');
    }
}
