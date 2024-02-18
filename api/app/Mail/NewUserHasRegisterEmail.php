<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EncryptDecryptService;


class NewUserHasRegisterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $frontendUrl;

    public function __construct($user)
    {
        $this->user = $user;
        $value = $user->email."..".$user->updated_at;
        $encryptedEmail = EncryptDecryptService::encryptvalue($value);
        $this->frontendUrl = env('FRONTEND_URL')."/email-verification/".$encryptedEmail;


    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new_user_has_register',
           
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
