<?php

namespace App\Services\Products\ProductSubCategoryService;

use App\Models\ProductSubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductSubCategoryRepository
{
    private function query()
    {

        return ProductSubCategory::select('id', 'category_id', 'sub_category_name', 'sub_category_description', 'created_by', 'updated_by')
                ->latest()
                 ->with('category:id,category_name')
                 ->with('creator', 'updater');
    }
    public function index()
    {

        $productSubCategory = $this->query()->latest()->paginate(20);
        $productSubCategory->getCollection()->transform(function ($productSubCategory) {
            return $this->transformProductService($productSubCategory);
        });
        return $productSubCategory;

    }
    public function onlySubProductCategory($category_id)
    {

        return ProductSubCategory::select('id', 'sub_category_name')->where('category_id', $category_id)->get();

    }
    public function searchProductSubCategory($searchCriteria)
    {

        $productSubCategory = $this->query()->where('sub_category_name', 'like', '%' . $searchCriteria . '%')->get();
        $productSubCategory->transform(function ($productSubCategory) {
            return $this->transformProductService($productSubCategory);
        });
        return $productSubCategory;

    }


    public function create(array $data)
    {
        try {

            $data = ProductSubCategory::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Product Subcategory created successfully',
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product Subcategory could not be created',
            ], 500);
        }
    }

    public function findById($id)
    {
        return ProductSubCategory::find($id);
    }

    public function update($id, array $data)
    {

        try {
            $productSubCategory = $this->findById($id);

            if ($productSubCategory) {

                $data = $productSubCategory->update($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Update successful',
                    'data' => $data,
                ], 200);
            }
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product Subcategory could not be updated',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $productSubCategory = $this->findById($id);
            if ($productSubCategory) {
                $productSubCategory->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Deletion successful',
                ], 200);
            }
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Product sub category is already in use',
            ], 500);
        }
    }
    private function transformProductService($productSubCategory)
    {

        return [
            'id' => $productSubCategory->id,
             'sub_category_id' => $productSubCategory->category_id,
            'sub_category_name' => $productSubCategory->sub_category_name,
            'sub_category_description' => $productSubCategory->sub_category_description,
            'category_id' => optional($productSubCategory->category)->category_name,
            // 'created_by' => optional($productSubCategory->creator)->fullname,
            // 'updated_by' => optional($productSubCategory->updater)->fullname,
            'created_by' => optional($productSubCategory->creator)->first_name ? optional($productSubCategory->creator)->first_name . " " . optional($productSubCategory->creator)->last_name : optional($productSubCategory->creator->organization)->organization_name,

            'updated_by' => optional($productSubCategory->updater)->first_name ? optional($productSubCategory->updater)->first_name . " " . optional($productSubCategory->updater)->last_name : optional($productSubCategory->updater->organization)->organization_name,

        ];
    }
}
