<?php
namespace App\Services;

class EmailDataService
{
    protected static  $companyName ="Milan Detail Medic";
    protected static $salutation ="Hello";
    protected static $sender="Milan Detail Medic";
    public static function getEmailData($type, $parameters = [])
    {
        if (method_exists(self::class, $type)) {
            return self::$type($parameters);
        } else {
            return 0;
        }
    }

    private static function retail_customer($parameters = [])
    {
        $url=config('app.retail_customer_verification_url');
        $url= $url."auth/register/verify";
        $defaults = [
            'company_name'=>self::$companyName,
            'salutation'=>self::$salutation,//appear at the top of the email e.g Dear John
            'content' => "This is to inform you that your account has been successfully created.",
            'sender'=>self::$sender,
            'frontend_url'=>$url
        ];

        $data = array_merge($defaults, $parameters);

        return $data;
    }

    private static function franchisee($parameters = [])
    {
        $url=config('app.franchisee_registration_url');
        $url= $url."auth/register";
        $defaults = [
            'company_name'=>self::$companyName,
            'salutation'=>self::$salutation,
            'content' => "We're thrilled to extend an exclusive invitation to join our franchise. 
			                    Your talents are a perfect fit for our team. Let's discuss this exciting opportunity further.
                               ",
            'sender'=>self::$sender,
            'frontend_url' => $url
        ];

        $data = array_merge($defaults, $parameters);

        return $data;
    }
   

   
}
?>