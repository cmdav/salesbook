<?php

namespace App\Services\Products\ProductCategoryService;

use App\Models\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductCategoryRepository
{
    private function query()
    {
        {

            return ProductCategory::select('id', 'category_name', 'category_description')->latest();

        }

    }
    public function index()
    {

        $productCategories = ProductCategory::select('id', 'category_name', 'category_description', 'created_by', 'updated_by')->latest()->with('creator', 'updater')->get();

        $transformed = $productCategories->map(function ($productCategory) {
            return [
                'id' => $productCategory->id,
                'category_name' => $productCategory->category_name,
                'category_description' => $productCategory->category_description,
                // 'created_by' => $productCategory->creator->fullname ?? '',
                // 'updated_by' => $productCategory->updater->fullname ?? ''
                   'created_by' => isset($productCategory->creator) ? $productCategory->creator->first_name . ' ' . $productCategory->creator->last_name : '',
                // Concatenating first_name and last_name for updated_by
                'updated_by' => isset($productCategory->updater) ? $productCategory->updater->first_name . ' ' . $productCategory->updater->last_name : ''
            ];
        });

        return $transformed;


    }
    public function searchProductCategory($searchCriteria)
    {

        return $this->query()->where('category_name', 'like', '%' . $searchCriteria . '%')->latest()->get();
        ;

    }

    public function create(array $data)
    {
        try {
            $productCategory = ProductCategory::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Product category created successfully',
                'data' => $productCategory
            ], 201);

        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product category could not be created',
            ], 500);
        }
    }

    public function findById($id)
    {
        return ProductCategory::find($id);
    }

    public function update($id, array $data)
    {
        try {
            $productCategory = $this->findById($id);

            if ($productCategory) {

                $productCategory = $productCategory->update($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Update successful',
                    'data' => $productCategory
                ], 200);
            }
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This category could not be updated',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $productCategory = $this->findById($id);
            if ($productCategory) {
                return $productCategory->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Deletion Successful..',
            ], 204);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Product category is already in use',
            ], 500);
        }
    }
}
