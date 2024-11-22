<?php

namespace App\Services\Products\ProductSubCategoryService;

use App\Models\ProductSubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\Security\LogService\LogRepository;

class ProductSubCategoryRepository
{
    protected $logRepository;
    protected $username;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
    }

    private function query()
    {
        return ProductSubCategory::select('id', 'category_id', 'sub_category_name', 'sub_category_description', 'created_by', 'updated_by')
            ->latest()
            ->with('category:id,category_name')
            ->with('creator', 'updater');
    }

    public function index()
    {
        $this->logRepository->logEvent(
            'product_sub_categories',
            'view',
            null,
            'ProductSubCategory',
            "$this->username viewed all product subcategories"
        );

        $productSubCategory = $this->query()->latest()->paginate(20);
        $productSubCategory->getCollection()->transform(function ($productSubCategory) {
            return $this->transformProductService($productSubCategory);
        });

        return $productSubCategory;
    }

    public function onlySubProductCategory($category_id)
    {
        $this->logRepository->logEvent(
            'product_sub_categories',
            'view',
            $category_id,
            'ProductSubCategory',
            "$this->username viewed subcategories under category ID $category_id"
        );

        return ProductSubCategory::select('id', 'sub_category_name')->where('category_id', $category_id)->get();
    }

    public function searchProductSubCategory($searchCriteria)
    {
        $this->logRepository->logEvent(
            'product_sub_categories',
            'search',
            null,
            'ProductSubCategory',
            "$this->username searched for product subcategories with criteria: $searchCriteria"
        );

        $productSubCategory = $this->query()->where('sub_category_name', 'like', '%' . $searchCriteria . '%')->get();
        $productSubCategory->transform(function ($productSubCategory) {
            return $this->transformProductService($productSubCategory);
        });

        return $productSubCategory;
    }

    public function create(array $data)
    {
        try {
            $productSubCategory = ProductSubCategory::create($data);

            $this->logRepository->logEvent(
                'product_sub_categories',
                'create',
                $productSubCategory->id,
                'ProductSubCategory',
                "$this->username created a new product subcategory: {$productSubCategory->sub_category_name}",
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Product subcategory created successfully',
                'data' => $productSubCategory,
            ], 200);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating product subcategory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product subcategory could not be created',
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
                $productSubCategory->update($data);

                $this->logRepository->logEvent(
                    'product_sub_categories',
                    'update',
                    $id,
                    'ProductSubCategory',
                    "$this->username updated product subcategory with ID $id",
                    $data
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Update successful',
                    'data' => $productSubCategory,
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error updating product subcategory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product subcategory could not be updated',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $productSubCategory = $this->findById($id);

            if (!$productSubCategory) {
                return response()->json(['success' => false, 'message' => 'Product subcategory not found'], 404);
            }

            $productSubCategory->delete();

            $this->logRepository->logEvent(
                'product_sub_categories',
                'delete',
                $id,
                'ProductSubCategory',
                "$this->username deleted product subcategory with ID $id"
            );

            return response()->json([
                'success' => true,
                'message' => 'Deletion successful',
            ], 200);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error deleting product subcategory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This product subcategory is already in use',
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
            'created_by' => optional($productSubCategory->creator)->first_name
                ? optional($productSubCategory->creator)->first_name . " " . optional($productSubCategory->creator)->last_name
                : optional($productSubCategory->creator->organization)->organization_name,
            'updated_by' => optional($productSubCategory->updater)->first_name
                ? optional($productSubCategory->updater)->first_name . " " . optional($productSubCategory->updater)->last_name
                : optional($productSubCategory->updater->organization)->organization_name,
        ];
    }
}
