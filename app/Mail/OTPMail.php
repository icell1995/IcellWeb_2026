<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected string $otp;

    /**
     * @var int
     */
    public int $expiryMinutes;

    /**
     * @var string
     */
    public string $subjectLine;

    /**
     * Create a new message instance.
     *
     * @param string|int $otp
     * @param int $expiryMinute
     * @param string $subject
     */
    public function __construct(string|int $otp, int $expiryMinute = 3, string $subject = 'Kode OTP Anda')
    {
        $this->otp = (string) $otp;
        $this->expiryMinutes = $expiryMinute;
        $this->subjectLine = $subject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.otp',
            text: 'email.otp_plain',
            with: [
                'otp' => $this->otp,
                'expiryMinutes' => $this->expiryMinutes,
                'subjectLine' => $this->subjectLine,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
