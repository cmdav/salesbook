<?php

namespace App\Services\Products\ProductCategoryService;

use App\Models\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\Security\LogService\LogRepository;

class ProductCategoryRepository
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
        return ProductCategory::select('id', 'category_name', 'category_description')->latest();
    }

    public function index()
    {
        $this->logRepository->logEvent(
            'product_categories',
            'view',
            null,
            'ProductCategory',
            "$this->username viewed all product categories"
        );

        $productCategories = ProductCategory::select('id', 'category_name', 'category_description', 'created_by', 'updated_by')
            ->latest()
            ->with('creator', 'updater')
            ->get();

        $transformed = $productCategories->map(function ($productCategory) {
            return [
                'id' => $productCategory->id,
                'category_name' => $productCategory->category_name,
                'category_description' => $productCategory->category_description,
                'created_by' => optional($productCategory->creator)->first_name
                    ? optional($productCategory->creator)->first_name . " " . optional($productCategory->creator)->last_name
                    : optional($productCategory->creator->organization)->organization_name,
                'updated_by' => optional($productCategory->updater)->first_name
                    ? optional($productCategory->updater)->first_name . " " . optional($productCategory->updater)->last_name
                    : optional($productCategory->updater->organization)->organization_name,
            ];
        });

        return $transformed;
    }

    public function searchProductCategory($searchCriteria)
    {
        $this->logRepository->logEvent(
            'product_categories',
            'search',
            null,
            'ProductCategory',
            "$this->username searched for product categories with criteria: $searchCriteria"
        );

        return $this->query()->where('category_name', 'like', '%' . $searchCriteria . '%')->latest()->get();
    }

    public function create(array $data)
    {
        try {
            $productCategory = ProductCategory::create($data);

            $this->logRepository->logEvent(
                'product_categories',
                'create',
                $productCategory->id,
                'ProductCategory',
                "$this->username created a new product category: {$productCategory->category_name}",
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Product category created successfully',
                'data' => $productCategory
            ], 201);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating product category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Product category could not be created',
            ], 500);
        }
    }

    public function update($id, array $data)
    {
        try {
            $productCategory = $this->findById($id);

            if ($productCategory) {
                $productCategory->update($data);

                $this->logRepository->logEvent(
                    'product_categories',
                    'update',
                    $id,
                    'ProductCategory',
                    "$this->username updated product category with ID $id",
                    $data
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Update successful',
                    'data' => $productCategory
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error updating product category: ' . $e->getMessage());
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
            if (!$productCategory) {
                return response()->json(['success' => false, 'message' => 'Product category not found'], 404);
            }

            $productCategory->delete();

            $this->logRepository->logEvent(
                'product_categories',
                'delete',
                $id,
                'ProductCategory',
                "$this->username deleted product category with ID $id"
            );

            return response()->json([
                'success' => true,
                'message' => 'Product category deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error deleting product category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This product category is already in use',
            ], 500);
        }
    }

    public function findById($id)
    {
        return ProductCategory::find($id);
    }
}
