<?php
namespace App\Services;

use Carbon\Carbon;

class ExpiryService
{
    public static function hasLinkExpiry($created_time)
    {
       
        $expiresAt = Carbon::parse($created_time)->addMinutes(20);
        
        $now = Carbon::now();

        if ($now->greaterThan($expiresAt)) {

           return true;
        }
        return false;
    }
	
}
?>