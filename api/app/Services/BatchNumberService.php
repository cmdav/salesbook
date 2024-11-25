<?php

namespace App\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class BatchNumberService
{
    public function generateBatchNumber()
    {
        $branchName = Auth::user()->branches->name;
        $branchId = Auth::user()->branch_id;
        $branchPrefix = strtoupper(substr($branchName, 0, 3));

        $lastBatchRecord = Purchase::select('batch_no')
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastBatchRecord) {
            // Extract the last part of the batch number and increment it
            $lastBatchNumber = $lastBatchRecord->batch_no;
            $lastNumber = (int) substr($lastBatchNumber, -2); // Assuming the last two digits are the number part
            $newBatchNumber = sprintf('%02d', $lastNumber + 1);
            $newBatchNumber = $branchPrefix . '/' . $newBatchNumber;
        } else {
            // If no record exists, start with 01
            $newBatchNumber = $branchPrefix . '/01';
        }

        return $newBatchNumber;
    }
}
