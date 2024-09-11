<?php

namespace App\Services\Products\ProductTypeService;

use App\Models\ProductType;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\Email\EmailService;
use App\Services\UserService\UserRepository;
use Carbon\Carbon;
use Exception;

class ProductTypeRepository
{
    protected UserRepository $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }
    private function query()
    {
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
        return ProductType::with([
            'sellingUnitCapacity:id,selling_unit_id,selling_unit_capacity',
            'unitPurchase:id,purchase_unit_name',
            'sellingUnit' => function ($query) {
                $query->select('selling_units.id', 'selling_units.purchase_unit_id', 'selling_units.selling_unit_name');
            },
        'subCategory:id,sub_category_name',

            'suppliers:id,first_name,last_name,phone_number',
            'activePrice' => function ($query) {
                $query->select('id', 'cost_price', 'selling_price', 'product_type_id');
            },

        ])->latest();
    }

    public function index()
    {
        return $this->getProductTypes();
    }
    public function searchProductType($searchCriteria)
    {

        $query = $this->query()->where('product_type_name', 'like', '%' . $searchCriteria . '%');


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
        $response = ProductType::select('id', 'product_type_name', 'purchase_unit_id')
            ->with('unitPurchase:id,purchase_unit_name')
            ->get()
            ->transform(function ($productType) {
                return [
                    'id' => $productType->id,
                    'product_type_name' => $productType->product_type_name,
                    'purchase_unit_id' => $productType->purchase_unit_id,
                    'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name, // Use optional to handle null cases
                ];
            });

        if($response) {
            return response()->json(['data' => $response], 200);
        }

        return [];
    }

    public function saleProductDetail()
    {

        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
        $response = ProductType::select(
            'id',
            'product_type_name',
            'barcode',
            'vat',
            'selling_unit_id'
        )
        ->with('unitselling:id,selling_unit_name')
        ->with(['store' => function ($query) use ($branchId) {
            $query->selectRaw('product_type_id, SUM(capacity_qty_available) as total_quantity')
                ->where('status', 1);

            if ($branchId !== 'all' && auth()->user()->role->role_name != 'Admin') {
                // Apply the where clause if branch_id is not 'all' and the user is not admin
                $query->where('branch_id', $branchId);
            }
            $query->groupBy('product_type_id');
        }])
        ->get();

        if ($response) {
            $response = $response->map(function ($item) {

                // Add latest price information to the response
                $latestPrice = $item->latest_price;
                $item->price_id = $latestPrice ? $latestPrice['price_id'] : null;
                $item->cost_price = $latestPrice ? $latestPrice['cost_price'] : null;
                $item->selling_price = $latestPrice ? $latestPrice['selling_price'] : null;
                $item->quantity_available = optional($item->store)->total_quantity;
                $item->selling_unit_name = optional($item->unitselling)->selling_unit_name;
                unset($item->store);
                unset($item->unitselling);

                return $item;
            });

            return response()->json(['data' => $response], 200);
        }



    }//use in sale page drop down
    public function getProductTypeByName($product_id)
    {

        if(!$product_id) {

            return $this->saleProductDetail();
        }

        $branchId = auth()->user()->branch_id;

        // Base query for 'product_types'
        $query = DB::table('product_types')
            ->select('product_types.id', 'product_types.product_type_name', 'product_types.vat')
            ->addSelect(DB::raw("
            (
               SELECT JSON_OBJECT(
                    'product_type_id', stores.product_type_id,
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
            $query->where('id', $productId);
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

            'product_sub_category' => optional($productType->subCategory)->sub_category_name,
            'product_sub_category_id' => optional($productType->subCategory)->id,
            'product_type_name' => $productType->product_type_name,
            'product_type_image' => $productType->product_type_image,
            'product_type_description' => $productType->product_type_description,
            'vat' => $productType->vat,
           'product_category' => optional($productType->product_category)->category_name,
           'product_category_id' => optional($productType->product_category)->id,
            'quantity_available' => optional($productType->store)->capacity_qty_available ?? 0,
            'purchasing_price' => optional($productType->activePrice)->cost_price ?? 'Not set',
            'selling_price' => optional($productType->activePrice)->selling_price ?? 'Not set',
            'selling_unit_capacity' => optional($productType->sellingUnitCapacity)->selling_unit_capacity,
            'selling_unit_capacity_id' => optional($productType->sellingUnitCapacity)->id,
            'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
            'purchase_unit_id' => optional($productType->unitPurchase)->id,
            'selling_unit_name' => optional($productType->sellingUnit)->selling_unit_name,
            'selling_unit_id' => optional($productType->sellingUnit)->id,
            'supplier_name' => trim((optional($productType->suppliers)->first_name ?? '') . ' ' . (optional($productType->suppliers)->last_name ?? '')) ?: 'None',
            'supplier_phone_number' => optional($productType->suppliers)->phone_number ?? 'None',
            'date_created' => $productType->created_at,
            // 'created_by' => optional($productType->creator)->fullname,
            // 'updated_by' => optional($productType->updater)->fullname,
            'created_by' => optional($productType->creator)->first_name . "  " .  optional($productType->creator)->last_name,
            'updated_by' => optional($productType->updater)->first_name . "  " .  optional($productType->updater)->last_name,
        ];


    }

    public function create(array $data)
    {

        try {
            $data = ProductType::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Product has been created successfully',
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
            $ProductType->delete();
            return response()->json([
                'success' => true,
                'message' => "Deletion successful"
            ], 200);


        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'This record is already in use'
            ], 500);
        }
    }
    public function getlistExpiredProduct($request)
    {
        $today = now();
        $nextWeek = now()->addDays(7);

        //$branchId = 'all';
        $branchId = auth()->user()->branch_id;
        //dd($branchId);
        // Admin can specify the branch; others use their own branch
        // if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
        //     $branchId = $request['branch_id'];
        // } elseif (auth()->user()->role->role_name != 'Admin') {
        //     $branchId = auth()->user()->branch_id;
        // }

        // Query for expired products with branch filtering
        $expiredProducts = \App\Models\Purchase::whereBetween('expiry_date', [$today, $nextWeek])
            ->when($branchId !== 'all', function ($query) use ($branchId) {
                // Filter by branch ID if it's not 'all'
                $query->where('branch_id', $branchId);
            })
            ->with([
                'productType' => function ($query) {
                    $query->select('id', 'product_type_name', 'sub_category_id', 'purchase_unit_id', 'selling_unit_id', 'created_at');
                },
                'productType.subCategory:id,sub_category_name',
                'productType.unitPurchase:id,purchase_unit_name',
                'productType.unitSelling:id,selling_unit_name',
                'productType.store' => function ($query) {
                    $query->select('product_type_id', 'batch_no', 'capacity_qty_available');
                }
            ])
            ->select('product_type_id', 'expiry_date', 'batch_no', 'branch_id') // Include branch_id in the selection
            ->groupBy('product_type_id', 'expiry_date', 'batch_no', 'branch_id') // Group by branch_id as well
            ->get();

        // Transform the result to return the specific fields required
        $response = $expiredProducts->map(function ($purchase) {
            $productType = $purchase->productType;
            $store = $productType->store->where('batch_no', $purchase->batch_no)->first();

            return [
                'product_sub_category' => optional($productType->subCategory)->sub_category_name,
                'product_type_name' => $productType->product_type_name,
                'quantity_available' => $store->capacity_qty_available ?? 0,
                'batch_no' => $purchase->batch_no ?? '',
                'expiry_date' => $purchase->expiry_date,
                'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
                'selling_unit_name' => optional($productType->unitSelling)->selling_unit_name,
                'branch_id' => $purchase->branch_id // Include branch_id in the response
            ];
        });

        $responseMsg = "Record retrieved successfully";

        // Check if the request has download set to true
        if (!isset($request['download'])) {
            // Send email only if download is not set to true
            $emailService = new EmailService();
            $responseData = $this->userRepository->getuserOrgAndBranchDetail();

            // Generate the email table content
            $tableDetail = $this->generateExpiredProductTable($responseData, $response);

            // Send the email
            $responseMsg = "The generated record has been sent via email";
            $emailService->sendEmail(
                ['email' => $responseData['branch_email'], 'first_name' => $responseData['branch_name']],
                "getExpiredProduct",
                $tableDetail
            );
        }

        return ["response" => $response, "responseMsg" => $responseMsg];
    }



    private function generateExpiredProductTable($responseData, $productDetails)
    {
        // Organization and branch details table (header)
        $headerTable = "
        <table style='width: 100%; border-collapse: collapse;'>
            <tr>
                <td style='padding: 8px;'>
                    <strong>Branch Name:</strong> {$responseData['branch_name']}<br>
                    <strong>Branch Email:</strong> {$responseData['branch_email']}<br>
                    <strong>Branch Phone:</strong> {$responseData['branch_phone_number']}<br>
                    <strong>State:</strong> {$responseData['state_name']}<br>
                    <strong>Country:</strong> {$responseData['country_name']}
                </td>
                <td style='padding: 8px; text-align: right;'>
                  
                    <strong>Organization Name:</strong> {$responseData['organization_name']}<br>
                    <strong>Email:</strong> {$responseData['company_email']}<br>
                    <strong>Phone:</strong> {$responseData['company_phone_number']}<br>
                    <strong>Address:</strong> {$responseData['company_address']}
                </td>
            </tr>
        </table>
    ";

        // Product details table (listing expired products)
        $productTable = "
        <table style='width: 100%; border-collapse: collapse; border: 1px solid black;'>
            <thead>
                <tr>
                    <th style='border: 1px solid black; padding: 8px;'>Product Sub Category</th>
                    <th style='border: 1px solid black; padding: 8px;'>Product Name</th>
                    <th style='border: 1px solid black; padding: 8px;'>Quantity Available</th>
                    <th style='border: 1px solid black; padding: 8px;'>Batch No</th>
                    <th style='border: 1px solid black; padding: 8px;'>Expiry Date</th>
                    <th style='border: 1px solid black; padding: 8px;'>Purchase Unit</th>
                    <th style='border: 1px solid black; padding: 8px;'>Selling Unit</th>
                </tr>
            </thead>
            <tbody>
    ";

        // Add product rows
        foreach ($productDetails as $product) {
            $productTable .= "
            <tr>
                <td style='border: 1px solid black; padding: 8px;'>{$product['product_sub_category']}</td>
                <td style='border: 1px solid black; padding: 8px;'>{$product['product_type_name']}</td>
                <td style='border: 1px solid black; padding: 8px;'>{$product['quantity_available']}</td>
                <td style='border: 1px solid black; padding: 8px;'>{$product['batch_no']}</td>
                <td style='border: 1px solid black; padding: 8px;'>{$product['expiry_date']}</td>
                <td style='border: 1px solid black; padding: 8px;'>{$product['purchase_unit_name']}</td>
                <td style='border: 1px solid black; padding: 8px;'>{$product['selling_unit_name']}</td>
            </tr>
        ";
        }

        $productTable .= "</tbody></table>";

        // Combine the header and product tables
        $htmlTable = $headerTable . "<br>" . $productTable;

        return $htmlTable;
    }


    public function getexpiredProductByDate($request)
    {
        // Parse the start and end date from the request
        $startDate = Carbon::parse($request['start_date'])->startOfDay();
        $endDate = Carbon::parse($request['end_date'])->endOfDay();

        // Branch filter logic
        //$branchId = 'all';
        $branchId = auth()->user()->branch_id;


        // Create the query for expired products within the date range
        $expiredProductsQuery = \App\Models\Purchase::whereBetween('expiry_date', [$startDate, $endDate])
            ->with([
                'productType' => function ($query) {
                    $query->select('id', 'product_type_name', 'sub_category_id', 'purchase_unit_id', 'selling_unit_id', 'created_at');
                },
                'productType.subCategory:id,sub_category_name',
                'productType.unitPurchase:id,purchase_unit_name',
                'productType.unitSelling:id,selling_unit_name',
                'productType.store' => function ($query) use ($branchId) {
                    $query->select('product_type_id', 'batch_no', 'capacity_qty_available');
                    // Filter by branch ID if it's not 'all'
                    if ($branchId !== 'all') {
                        $query->where('branch_id', $branchId);
                    }
                }
            ])
            ->select('product_type_id', 'expiry_date', 'batch_no')
            ->groupBy('product_type_id', 'expiry_date', 'batch_no');

        // Check if 'all' parameter is passed and return all data without pagination
        if (isset($request['all']) && $request['all'] == true) {
            // Get all results without pagination
            $expiredProducts = $expiredProductsQuery->get();

            // Transform the result
            $response = $expiredProducts->map(function ($purchase) {
                $productType = $purchase->productType;
                $store = $productType->store->where('batch_no', $purchase->batch_no)->first();

                return [
                    'product_sub_category' => optional($productType->subCategory)->sub_category_name,
                    'product_type_name' => $productType->product_type_name,
                    'quantity_available' => $store->capacity_qty_available ?? 0,
                    'batch_no' => $purchase->batch_no ?? '',
                    'expiry_date' => $purchase->expiry_date,
                    'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
                    'selling_unit_name' => optional($productType->unitSelling)->selling_unit_name,
                ];
            });

            return $response;

        }

        // Otherwise, paginate the results
        $expiredProducts = $expiredProductsQuery->paginate(20);

        // Transform the paginated result
        $response = $expiredProducts->getCollection()->map(function ($purchase) {
            $productType = $purchase->productType;
            $store = $productType->store->where('batch_no', $purchase->batch_no)->first();

            return [
                'product_sub_category' => optional($productType->subCategory)->sub_category_name,
                'product_type_name' => $productType->product_type_name,
                'quantity_available' => $store->capacity_qty_available ?? 0,
                'batch_no' => $purchase->batch_no ?? '',
                'expiry_date' => $purchase->expiry_date,
                'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
                'selling_unit_name' => optional($productType->unitSelling)->selling_unit_name,
            ];
        });

        // Replace the original collection with the transformed collection
        $expiredProducts->setCollection($response);

        return $expiredProducts;
    }
    public function getproductPriceList($request)
    {
        // Query for unique products with their latest active price
        // $branchId = 'all'; // Default branch
        $branchId = auth()->user()->branch_id; // Get the authenticated user's branch

        // If the user is an admin and has provided a branch_id, use that branch
        // if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
        //     $branchId = $request['branch_id'];
        // } elseif (auth()->user()->role->role_name != 'Admin') {
        //     $branchId = auth()->user()->branch_id;
        // }

        // Filter the products by the active price in the selected branch
        $productsQuery = ProductType::with(['activePrice' => function ($query) use ($branchId) {
            // Filter the activePrice based on the branch_id
            $query->select('product_type_id', 'cost_price', 'selling_price', 'new_cost_price', 'new_selling_price', 'is_new', 'status', 'created_at')
                  ->where('branch_id', $branchId); // Filter by branch_id
        }]);

        // Check if the request has 'all' == true, and return all results without pagination
        if (isset($request['all']) && $request['all'] == true) {
            // Get all results without pagination
            $products = $productsQuery->get();

            // Transform each product to include the required data
            $response = $products->map(function ($product) {
                $activePrice = $product->activePrice;

                if ($activePrice) {
                    $costPrice = $activePrice->is_new ? $activePrice->new_cost_price : $activePrice->cost_price;
                    $sellingPrice = $activePrice->is_new ? $activePrice->new_selling_price : $activePrice->selling_price;
                } else {
                    $costPrice = 'No price available';
                    $sellingPrice = 'No price available';
                }

                return [
                    'product_type_name' => $product->product_type_name,
                    'product_description' => $product->product_type_description,
                    'cost_price' => $costPrice,
                    'selling_price' => $sellingPrice,
                ];
            });

            return $response;
        }

        // Otherwise, paginate the results
        $products = $productsQuery->paginate(20); // Paginate 20 per page

        // Transform each product to include the required data
        $products->getCollection()->transform(function ($product) {
            $activePrice = $product->activePrice;

            if ($activePrice) {
                $costPrice = $activePrice->is_new ? $activePrice->new_cost_price : $activePrice->cost_price;
                $sellingPrice = $activePrice->is_new ? $activePrice->new_selling_price : $activePrice->selling_price;
            } else {
                $costPrice = 'No price available';
                $sellingPrice = 'No price available';
            }

            return [
                'product_type_name' => $product->product_type_name,
                'product_description' => $product->product_type_description,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
            ];
        });

        return $products;
    }


    // public function getproductPriceList($request)
    // {
    //     // Query for unique products with their latest active price
    //     $branchId = 'all';
    //     $branchId = auth()->user()->branch_id;

    //     if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
    //         $branchId = $request['branch_id'];
    //     } elseif (auth()->user()->role->role_name != 'Admin') {
    //         $branchId = auth()->user()->branch_id;
    //     }


    //     $productsQuery = ProductType::with(['activePrice' => function ($query) {
    //         $query->select('product_type_id', 'cost_price', 'selling_price', 'new_cost_price', 'new_selling_price', 'is_new', 'status', 'created_at');
    //     }]);

    //     // Check if the request has 'all' == true, and return all results without pagination
    //     if (isset($request['all']) && $request['all'] == true) {
    //         // Get all results without pagination
    //         $products = $productsQuery->get();

    //         // Transform each product to include the required data
    //         $response = $products->map(function ($product) {
    //             $activePrice = $product->activePrice;

    //             if ($activePrice) {
    //                 $costPrice = $activePrice->is_new ? $activePrice->new_cost_price : $activePrice->cost_price;
    //                 $sellingPrice = $activePrice->is_new ? $activePrice->new_selling_price : $activePrice->selling_price;
    //             } else {
    //                 $costPrice = 'No price available';
    //                 $sellingPrice = 'No price available';
    //             }

    //             return [
    //                 'product_type_name' => $product->product_type_name,
    //                 'product_description' => $product->product_type_description,
    //                 'cost_price' => $costPrice,
    //                 'selling_price' => $sellingPrice,
    //             ];
    //         });

    //         return $response;
    //     }

    //     // Otherwise, paginate the results
    //     $products = $productsQuery->paginate(20); // Paginate 20 per page

    //     // Transform each product to include the required data
    //     $products->getCollection()->transform(function ($product) {
    //         $activePrice = $product->activePrice;

    //         if ($activePrice) {
    //             $costPrice = $activePrice->is_new ? $activePrice->new_cost_price : $activePrice->cost_price;
    //             $sellingPrice = $activePrice->is_new ? $activePrice->new_selling_price : $activePrice->selling_price;
    //         } else {
    //             $costPrice = 'No price available';
    //             $sellingPrice = 'No price available';
    //         }

    //         return [
    //             'product_type_name' => $product->product_type_name,
    //             'product_description' => $product->product_type_description,
    //             'cost_price' => $costPrice,
    //             'selling_price' => $sellingPrice,
    //         ];
    //     });

    //     return $products;
    // }




}
