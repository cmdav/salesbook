<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadImage($file, $path): string
    {
        // Store the file inside storage
        // $filename = time() . '.' . $file->getClientOriginalExtension();
        // $path = $file->storeAs("public/$path", $filename);
        // return Storage::url($path);

        // // local environment
        // $fileName = uniqid() . '_' . $file->getClientOriginalName();
        //  $filePath = $file->storeAs($path, $fileName, 'public');
        //  return Storage::disk('public')->url($filePath);

        //server en
        // $fileName = uniqid() . '_' . $file->getClientOriginalName();
        // $filePath = $file->storeAs($path, $fileName, 'root');
        // return Storage::disk('root')->url($filePath);
        //reduce length
        // Generate a unique prefix
        $uniquePrefix = uniqid() . '_';
        // Original file name
        $originalName = $file->getClientOriginalName();

        // Calculate the maximum length for the original name to keep the total length within 255 characters
        $maxLength = 255 - strlen($uniquePrefix);

        // Truncate the original name if it's too long
        if (strlen($originalName) > $maxLength) {
            $originalName = substr($originalName, 0, $maxLength);
        }

        // Combine the unique prefix and the truncated original name
        $fileName = $uniquePrefix . $originalName;

        // Store the file
        $filePath = $file->storeAs($path, $fileName, 'root');

        // Return the file URL
        return Storage::disk('root')->url($filePath);


    }

}
