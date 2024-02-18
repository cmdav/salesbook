<?php
namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class EncryptDecryptService
{
    public static function encryptValue($value)
    {
        try {

            return Crypt::encryptString($value);

        } catch (EncryptException $e) {
            
            return null; 
        }
    }
	public static function decryptValue($value)
    {
      
        try {

            return Crypt::decryptString($value);
            
        } catch (DecryptException $e) {
            
            return null; 
        }
    }
}
?>