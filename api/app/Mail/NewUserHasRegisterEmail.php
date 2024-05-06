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
use Illuminate\Support\Facades\Request;


class NewUserHasRegisterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $frontendUrl;
    public $first_paragraph;
    public $second_paragraph;
    public $btn_label;
    public $title;
    public $url;


    public function __construct($user, $type, $otherDetail)
    {
      
        $this->user = $user;
        if(isset($user->token)){

            $data=['token'=>$user->token,'type'=>$type,'otherDetail'=>$otherDetail];
            $this->url = Request::root();
            $jsonData = json_encode($data);
            $encryptedToken = EncryptDecryptService::encryptvalue($jsonData);
        }
      
         
        
        if ($type === 'resend' || $type === 'register') {
           
            
            $this->frontendUrl = env('FRONTEND_URL')."/email-verification/".$encryptedToken;
            $this->first_paragraph ="This is to inform you that your account has been successfully created and your organization code is <b>$user->organization_code</b>";
            $this->second_paragraph="Click the button to verify your email";
            $this->btn_label = "Verify Email";
            $this->title = "Registration";
           
         }
         else if($type == "reset-password"){
           
            $this->frontendUrl = env('FRONTEND_URL')."/password-reset/".$encryptedToken;
            $this->first_paragraph ="We received a password reset from you. However no further action is required if it is not from you.";
            $this->second_paragraph="Click the button to reset-your email";
            $this->btn_label = "Reset Password";
            $this->title = "Reset Password";
          
          }
          else if ($type == "new-supplier" || $type == "old-supplier") {
            $this->frontendUrl = env('FRONTEND_URL') . "/{$type}/" . $encryptedToken;
            $this->first_paragraph = "This is to inform you that {$otherDetail['organization_name']} has invited you to be part of their supplier.";
            $this->second_paragraph = "Click the button to accept the invitation";
            $this->btn_label = "Join Company";
            $this->title = "Invitation from {$otherDetail['organization_name']}";
        }
        else if ($type == "sales-receipt") {
           
               
            //$this->frontendUrl = env('FRONTEND_URL');
            $this->first_paragraph = "Thank you for your purchase. Below are the details of the item bought. 
                However we will like to inform you that the value of VAT is calculated as 7.5% on each item if it is included in your purchase";
            $this->second_paragraph = $otherDetail;
            //$this->btn_label = "Join Company";
            $this->title = "Customer Receipt";
        }

       
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.onboardingEmail',
           
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
