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

    public $otp;
    public $expiryMinutes;
    public $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $expiryMinute = 3, $subject = 'Kode OTP Anda')
    {
        //
        $this->otp = $otp;
        $this->expiryMinutes = $expiryMinute;
        $this->subjectLine = $subject;
    }

    public function build()
    {
        Log::info("Membangun email dengan OTP: {$this->otp}");

        return $this->subject($this->subjectLine)
            ->view('email.otp')
            ->with([
                'otp' => $this->otp,
                'expiryMinutes' => $this->expiryMinutes,
                'subjectLine' => $this->subjectLine,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'O T P Mail',
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
