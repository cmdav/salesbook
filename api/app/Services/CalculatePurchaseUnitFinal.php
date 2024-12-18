<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CalculatePurchaseUnit
{
    private function calculateSmallestUnit($unitId, $unitsMap, &$cache)
    {
        // If the unit is not found, default to 1
        if (!isset($unitsMap[$unitId])) {
            return ['smallest_unit' => 1, 'purchase_unit_id' => $unitId];
        }

        // Prevent recalculating already computed units
        if (isset($cache[$unitId])) {
            return $cache[$unitId];
        }

        $currentUnit = $unitsMap[$unitId];

        // If no parent exists, return this unit's value
        if (!isset($currentUnit['parent_purchase_unit_id']) || $currentUnit['parent_purchase_unit_id'] === null) {
            $cache[$unitId] = [
                'smallest_unit' => floatval($currentUnit['unit'] ?? 1),
                'purchase_unit_id' => $unitId
            ];
            return $cache[$unitId];
        }

        // Recursively calculate the parent's unit value
        $parentResult = $this->calculateSmallestUnit($currentUnit['parent_purchase_unit_id'], $unitsMap, $cache);

        // Ensure we're using numeric values for division
        $currentUnitValue = floatval($currentUnit['unit'] ?? 1);

        // Multiply the current unit's value by its parent's value
        $cache[$unitId] = [
            'smallest_unit' => $parentResult['smallest_unit'] / $currentUnitValue,
            'purchase_unit_id' => $unitId  // Note the change here: return the current unit's ID
        ];

        return $cache[$unitId];
    }

    public function calculatePurchaseUnits($measurements)
    {
        $result = [];

        // Create a map of purchase units by ID for easy lookup
        $unitsMap = [];
        foreach ($measurements as $measurement) {
            if (isset($measurement['purchaseUnit'])) {
                $unitsMap[$measurement['purchasing_unit_id']] = $measurement['purchaseUnit'];
            }
        }

        $cache = [];

        // Iterate through the measurements to calculate each purchase unit's value
        foreach ($measurements as $measurement) {
            if (!isset($measurement['purchaseUnit'])) {
                continue;
            }

            $unitId = $measurement['purchasing_unit_id'];
            $unitName = strtolower($measurement['purchaseUnit']['purchase_unit_name'] ?? 'unknown');

            // Calculate the total number of smallest units for this purchase unit
            $calculatedUnit = $this->calculateSmallestUnit($unitId, $unitsMap, $cache);

            // Correctly assign the `purchase_unit_id` of the current unit
            $result[$unitName] = [
                'value' => $calculatedUnit['smallest_unit'],
                'purchase_unit_id' => $unitId // Use the current unit's ID, not the parent ID
            ];
        }

        // Reverse the result to start with the smallest unit
        $result = array_reverse($result, true);

        // Find the smallest non-zero value to use as a scaling factor
        $minValue = min(array_filter(array_column($result, 'value'), function ($value) {
            return $value > 0;
        }));

        // Scale up the values to whole numbers
        $scaledResult = [];
        foreach ($result as $unitName => $unitData) {
            $scaledResult[$unitName] = [
                'value' => round($unitData['value'] / $minValue),
                'purchase_unit_id' => $unitData['purchase_unit_id']
            ];
        }

        return $scaledResult;
    }


    public function calculateQuantityBreakdown($quantityAvailable, $quantityBreakdown)
    {
        $result = [];

        // Sort the breakdown values in descending order
        uasort($quantityBreakdown, function ($a, $b) {
            return $b['value'] <=> $a['value'];
        });

        foreach ($quantityBreakdown as $unit => $unitData) {
            $value = $unitData['value'];

            if ($quantityAvailable >= $value) {
                $count = intdiv($quantityAvailable, $value); // Calculate how many units can be used
                $quantityAvailable %= $value; // Update the remaining quantity

                $result[] = [
                    'unit' => $unit,
                    'count' => $count,
                    'purchase_unit_id' => $unitData['purchase_unit_id']
                ];
            }
        }

        return $result;
    }

    public function formatQuantityBreakdown($quantityBreakdown)
    {
        $result = [];

        foreach ($quantityBreakdown as $data) {
            // Ensure the count is greater than 0
            if (!empty($data['count']) && $data['count'] > 0) {
                // Handle singular/plural unit naming
                $unitName = $data['count'] > 1 ? $data['unit'] : rtrim($data['unit'], 's');
                $result[] = "{$data['count']} {$unitName}";
            }
        }

        // Combine the results into a comma-separated string
        return implode(', ', $result);
    }
    public function calculateQuantityInAPurchaseUnit($capacityQtyAvailable, $purchase_unit_id, $no_of_smallestUnit_in_each_unit)
    {
        // Extract the value that matches the purchase_unit_id
        $value = collect($no_of_smallestUnit_in_each_unit)->firstWhere('purchase_unit_id', $purchase_unit_id)['value'] ?? null;

        // If no matching value is found, return 0
        if (!$value) {
            return 0;
        }

        // Calculate the result
        $result = $capacityQtyAvailable / $value;

        // If the result is less than 1, return 0; otherwise, return the result
        return $result < 1 ? 0 : $result;
    }





}
