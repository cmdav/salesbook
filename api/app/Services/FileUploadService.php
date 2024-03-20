<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadImage($file,$path): string
    {
        // Store the file inside storage
        // $filename = time() . '.' . $file->getClientOriginalExtension(); 
        // $path = $file->storeAs("public/$path", $filename); 
        // return Storage::url($path);
        
        // $fileName = uniqid() . '_' . $file->getClientOriginalName();
        //  $filePath = $file->storeAs($path, $fileName, 'public');
        //  return Storage::disk('public')->url($filePath);

        $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($path, $fileName, 'root'); 
            return Storage::disk('root')->url($filePath); 

    }
    
}
?>