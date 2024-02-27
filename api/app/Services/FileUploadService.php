<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadImage($file,$path): string
    {
        $filename = time() . '.' . $file->getClientOriginalExtension(); 
        $path = $file->storeAs("public/$path", $filename); 
        return Storage::url($path); 
    }
	
}
?>