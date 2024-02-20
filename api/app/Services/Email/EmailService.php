<?php
namespace App\Services\Email;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserHasRegisterEmail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class EmailService
{
    public static function sendEmail($user, $type)
    {
       try{
            
           
            Mail::to($user['email'])->send(new NewUserHasRegisterEmail($user, $type));

            return true;
       }
       catch (Exception $e) {

            Log::channel('email_errors')->error('Error sending email: ' . $exception->getMessage());
            return false;
        }
       
    }
	
}
?>