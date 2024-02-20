<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EncryptDecryptService;
use App\Services\EmailDataService;


class NewUserHasRegisterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $frontendUrl;

    public function __construct($user, $type)
    {
        $this->user = $user;
        $encryptedToken = EncryptDecryptService::encryptvalue($user->token);
        $this->frontendUrl = env('FRONTEND_URL')."/email-verification/".$encryptedToken;

        // $data = EmailDataService::getEmailData($userType);
        // $url=$data['frontend_url']."/".$hash;
        //  $this->emailData = array_merge($data, array('frontend_url'=>$url));

       
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
