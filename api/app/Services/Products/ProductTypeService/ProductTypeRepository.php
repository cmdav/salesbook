<?php

namespace App\Services\Products\ProductTypeService;

use App\Models\ProductType;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductTypeRepository
{
    private function query()
    {
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
        return ProductType::with([
            'product:id,category_id,product_name,vat,sub_category_id',
            // 'product.measurement:id,measurement_name',
            'product.subCategory:id,sub_category_name',
            'suppliers:id,first_name,last_name,phone_number',
            'activePrice' => function ($query) {
                $query->select('id', 'cost_price', 'selling_price', 'product_type_id');
            },
            // 'store' => function ($query) use ($branchId) {
            //     $query->selectRaw('product_type_id, SUM(quantity_available) as total_quantity')
            //         ->where('status', 1);

            //     if ($branchId !== 'all' && auth()->user()->role->role_name != 'Admin') {
            //         // Apply the where clause if branch_id is not 'all' and the user is not admin
            //         $query->where('branch_id', $branchId);
            //     }

            //     $query->groupBy('product_type_id');
            // }
        ])->latest();
    }

    public function index()
    {
        return $this->getProductTypes();
    }
    public function searchProductType($searchCriteria)
    {

        $query = $this->query()
            ->where('product_type_name', 'like', '%' . $searchCriteria . '%')
            ->Orwhere(function ($query) use ($searchCriteria) {
                $query->whereHas('product', function ($q) use ($searchCriteria) {
                    $q->where('product_name', 'like', '%' . $searchCriteria . '%');
                });
                // ->orWhereHas('product.product_category', function($q) use ($searchCriteria) {
                //     $q->where('category_name', 'like', '%' . $searchCriteria . '%');
                // });
            });

        $productTypes = $query->get();

        $productTypes->transform(function ($productType) {
            return $this->transformProductType($productType);
        });

        return $productTypes;
    }

    public function getProductTypeByProductId($id)
    {
        return $this->getProductTypes($id);
    }
    public function onlyProductTypeName()
    {
        $response = ProductType::select('id', 'product_type_name')->get();
        if($response) {
            return response()->json(['data' => $response], 200);
        }
        return [];
    }
    public function getProductTypeByName($product_id)
    {

        $branchId = auth()->user()->branch_id;

        // Base query for 'product_types'
        $query = DB::table('product_types')
            ->select('product_types.id', 'product_types.product_type_name', 'product_types.vat')
            ->addSelect(DB::raw("
            (
               SELECT JSON_OBJECT(
                    'product_type_id', stores.product_type_id,
                    'container_qty_available', SUM(stores.container_qty_available),
                    'capacity_qty_available', SUM(stores.capacity_qty_available)
                )
                FROM stores
                WHERE stores.product_type_id = product_types.id
                AND stores.status = 1
                " . ($branchId ? "AND stores.branch_id = $branchId " : "") . "
                GROUP BY stores.product_type_id
                        ) as store
            "))
            ->addSelect(DB::raw("
            (
                SELECT JSON_OBJECT(
                    'cost_price', prices.cost_price,
                    'selling_price', prices.selling_price
                )
                FROM prices
                WHERE prices.product_type_id = product_types.id
                AND
                prices.status = 1
                ORDER BY prices.created_at DESC
                LIMIT 1
            ) as latest_price
        "));

        // Execute the query and get the results
        $productTypes = $query->get();

        // Transform the results
        // $productTypes->transform(function ($item) {
        //     // Decode the JSON fields
        //     $store = json_decode($item->store);
        //     $latestPrice = json_decode($item->latest_price);

        //     // Return a formatted array with the desired structure
        //     return [
        //         'id' => $item->id,
        //         'product_type_name' => $item->product_type_name,
        //         'vat_percentage' => 7.5, // Static VAT percentage
        //         'cost_price' => $latestPrice->cost_price ?? 0, // Cost price from the latest price or 0 if not available
        //         'selling_price' => $latestPrice->selling_price ?? 0, // Selling price from the latest price or 0 if not available
        //         'container_qty_available' => $store->container_qty_available ?? 0, // Container quantity available or 0 if not available
        //         'capacity_qty_available' => $store->capacity_qty_available ?? 0, // Capacity quantity available or 0 if not available
        //         'vat' => $item->vat ? 'Yes' : 'No', // VAT value from the product or 'No' if not available
        //     ];
        // });

        // Return the transformed product types
        return $productTypes;
    }


    // public function getProductTypeByName()
    // {
    //     $branchId = auth()->user()->branch_id;

    //     // Base query for 'product_types'
    //     $query = DB::table('product_types')
    //         ->select('product_types.id', 'product_types.product_type_name', 'product_types.product_id')

    //         // Select a JSON object containing 'id' and 'vat' from the 'products' table where the 'product_id' matches
    //         ->addSelect(DB::raw("
    //             (SELECT JSON_OBJECT(
    //                 'id', products.id,
    //                 'vat', products.vat
    //             )
    //             FROM products
    //             WHERE products.id = product_types.product_id
    //             ) as product"));

    //     // Conditional part for 'store'
    //     $storeQuery = "
    //         (SELECT JSON_OBJECT(
    //             'product_type_id', stores.product_type_id,
    //             'total_quantity', SUM(stores.quantity_available)
    //         )
    //         FROM stores
    //         WHERE stores.product_type_id = product_types.id
    //         AND stores.status = 1 ";
    //     if ($branchId) {
    //         $storeQuery .= "AND stores.branch_id = $branchId ";
    //     }
    //     $storeQuery .= "GROUP BY stores.product_type_id
    //         ) as store";

    //     $query->addSelect(DB::raw($storeQuery));

    //     // Conditional part for 'batches'
    //     $batchesQuery = "
    //         (SELECT JSON_ARRAYAGG(JSON_OBJECT(
    //             'id', stores.id,
    //             'product_type_id', stores.product_type_id,
    //             'batch_no', stores.batch_no,
    //             'quantity_available', stores.quantity_available,
    //             'selling_price',
    //                 COALESCE(
    //                     (CASE WHEN prices.is_new = 1 AND prices.status = 1 THEN prices.new_selling_price ELSE prices.selling_price END),
    //                     (SELECT CASE WHEN p.is_new = 1 THEN p.new_selling_price ELSE p.selling_price END FROM prices p WHERE p.id = prices.price_id)
    //                 ),
    //             'cost_price',
    //                 COALESCE(
    //                     (CASE WHEN prices.is_new = 1 AND prices.status = 1 THEN prices.new_cost_price ELSE prices.cost_price END),
    //                     (SELECT CASE WHEN p.is_new = 1 THEN p.new_cost_price ELSE p.cost_price END FROM prices p WHERE p.id = prices.price_id)
    //                 )
    //         ))
    //         FROM stores
    //         JOIN prices ON prices.batch_no = stores.batch_no AND prices.product_type_id = stores.product_type_id
    //         WHERE stores.product_type_id = product_types.id
    //         AND stores.status = 1
    //         AND prices.status = 1
    //         AND stores.quantity_available > 0 ";
    //     if ($branchId) {
    //         $batchesQuery .= "AND stores.branch_id = $branchId ";
    //     }
    //     $batchesQuery .= ") as batches";

    //     $query->addSelect(DB::raw($batchesQuery));

    //     // Execute the query and get the results
    //     $productTypes = $query->get();

    //     // Transform the results
    //     $productTypes->transform(function ($item) {
    //         // Decode the JSON fields
    //         $item->product = json_decode($item->product);
    //         $item->store = json_decode($item->store);
    //         $item->batches = json_decode($item->batches);

    //         // Return a formatted array with the desired structure
    //         return [
    //             'id' => $item->id,
    //             'product_type_name' => $item->product_type_name,
    //             'vat_percentage' => 7.5, // Static VAT percentage
    //             'cost_price' => $item->batches[0]->cost_price ?? 0, // Cost price from the first batch or 0 if not available
    //             'selling_price' => $item->batches[0]->selling_price ?? 0, // Selling price from the first batch or 0 if not available
    //             'quantity_available' => $item->store->total_quantity ?? 0, // Total quantity available from the store or 0 if not available
    //             'vat' => $item->product->vat ?? 'No', // VAT value from the product or 'No' if not available
    //             'batches' => collect($item->batches)->map(function ($batch) {
    //                 // Create a label combining batch number and selling price
    //                 $batchLabel = $batch->batch_no."->".$batch->selling_price;
    //                 // Return a formatted array for each batch
    //                 return [
    //                     'id' => $batch->id,
    //                     'batch_no' =>  $batchLabel,
    //                     'batch_quantity_left' => $batch->quantity_available,
    //                     'batch_selling_price' => $batch->selling_price
    //                 ];
    //             })->toArray()
    //         ];
    //     });

    //     // Return the transformed product types
    //     return $productTypes;
    // }


    private function getProductTypes($productId = null)
    {
        $query = $this->query();
        if ($productId) {
            $query->where('product_id', $productId);
        };

        $productTypes = $query->paginate(20);



        $productTypes->getCollection()->transform(function ($productType) {
            return $this->transformProductType($productType);
        });

        return $productTypes;
    }

    private function transformProductType($productType)
    {

        return [
            'id' => $productType->id,
            'product_name' => optional($productType->product)->product_name,
            'product_type_name' => $productType->product_type_name,
            'product_type_image' => $productType->product_type_image,
            'product_type_description' => $productType->product_type_description,
            'vat' => optional($productType->product)->vat,
            'product_name' => optional($productType->product)->product_name,
            'product_description' => $productType->product_type_description,
            'product_image' => $productType->product_type_image,

            'product_category' => optional(optional($productType->product)->product_category)->category_name,

            'category_id' => optional(optional($productType->product)->product_category)->id,
            'category_name' => optional(optional($productType->product)->product_category)->category_name,

            'product_sub_category' => optional(optional($productType->product)->subCategory)->sub_category_name,
            'sub_category_id' => optional(optional($productType->product)->subCategory)->id,
            'quantity_available' => optional($productType->store)->total_quantity ?? 0,
            "measurement" => "Litre",
            "container_type" => "Tank",
            "container_type_capacity" => 50,
            'purchasing_price' => optional($productType->activePrice)->cost_price ?? 'Not set',
            'selling_price' => optional($productType->activePrice)->selling_price ?? 'Not set',
            'supplier_name' => trim((optional($productType->suppliers)->first_name ?? '') . ' ' . (optional($productType->suppliers)->last_name ?? '')) ?: 'None',
            'supplier_phone_number' => optional($productType->suppliers)->phone_number ?? 'None',
            'date_created' => $productType->created_at,
            'created_by' => optional($productType->creator)->fullname,
            'updated_by' => optional($productType->updater)->fullname,
        ];


    }

    public function create(array $data)
    {
        try {
            $data = ProductType::create($data);
            return response()->json([
                'success' => true,
                'message' => 'This Product type created successfully',
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Product type could not be created',
            ], 500);
        }
    }

    public function findById($id)
    {
        return ProductType::find($id);
    }

    public function update($id, array $data)
    {
        try {
            $ProductType = $this->findById($id);

            if ($ProductType) {

                $data = $ProductType->update($data);
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
                'message' => 'This Product type could not be updated',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $ProductType = $this->findById($id);
            if (!$ProductType) {
                return response()->json([
                    'success' => false,
                    'message' => "No product found"
                ], 404);
            }

            // Check if type is a product
            if ($ProductType->type == 1) {
                $product = \App\Models\Product::find($ProductType->product_id);
                if ($product) {
                    // Check if the product has more than one product type
                    $productTypes = ProductType::where('product_id', $product->id);
                    if ($productTypes->count() > 1) {
                        return response()->json([
                            'success' => false,
                            'message' => "This Record is already in use"
                        ], 500);
                    } else {
                        // Delete the only product and product type
                        $ProductType->delete();
                        $product->delete();
                        return response()->json([
                            'success' => true,
                            'message' => 'Deletion successful',
                        ], 200);
                    }
                }
                $ProductType->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Deletion successful"
                ], 200);
            } else {
                $ProductType->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Deletion successful',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'This record is already in use'
            ], 500);
        }
    }

}
