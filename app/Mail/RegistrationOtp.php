<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationOtp extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public readonly string $code)
    {
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject(__('Your K-Wave verification code'))
            ->markdown('emails.registration-otp', [
                'code' => $this->code,
            ]);
    }
}
