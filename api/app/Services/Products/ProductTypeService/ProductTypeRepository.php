<?php

namespace App\Services\Products\ProductTypeService;

use App\Models\ProductType;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\ProductMeasurement;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\Email\EmailService;
use App\Services\UserService\UserRepository;
use App\Services\Security\LogService\LogRepository;
use App\Services\GeneratePdf;
use App\Services\CalculatePurchaseUnit;
use Carbon\Carbon;
use Exception;

class ProductTypeRepository
{
    protected UserRepository $userRepository;
    protected GeneratePdf $generatePdf;
    protected $logRepository;
    protected $username;
    protected $processPurchaseUnit;

    public function __construct(UserRepository $userRepository, GeneratePdf $generatePdf, LogRepository $logRepository, CalculatePurchaseUnit $calculatePurchaseUnit)
    {
        $this->userRepository = $userRepository;
        $this->generatePdf = $generatePdf;
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
        $this->processPurchaseUnit = $calculatePurchaseUnit;
    }
    private function query()
    {

        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;

        return ProductType::with([
            'productMeasurement',
            'productMeasurement.PurchaseUnit',
            'subCategory:id,sub_category_name',
            'suppliers:id,first_name,last_name,phone_number',
            'activePrices' => function ($query) {
                $query->select('id', 'cost_price', 'selling_price', 'product_type_id', 'purchase_unit_id');
            },

        ])->latest();
    }

    public function index()
    {
        $this->logRepository->logEvent(
            'product_types',
            'view',
            null,
            'ProductType',
            "$this->username viewed all product types"
        );

        return $this->getProductTypes();
    }
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
        // Get the authenticated user's branch ID
        $branchId = auth()->user()->branch_id;

        // Sum up all quantities available for the product type in the specified branch
        $quantityAvailable = \App\Models\Store::where('product_type_id', $productType->id)
            ->where('branch_id', $branchId)
            ->sum('capacity_qty_available');

        $activePrices = $productType->activePrices()->get();

        // Map purchase units with purchasing and selling prices
        $purchasingPrices = [];
        $sellingPrices = [];

        $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($productType->productMeasurement);
        //dd($no_of_smallestUnit_in_each_unit);
        $quantityBreakdown = $this->processPurchaseUnit->calculateQuantityBreakdown($quantityAvailable, $no_of_smallestUnit_in_each_unit);
        // dd($quantityBreakdown);
        $formattedBreakdown = $this->processPurchaseUnit->formatQuantityBreakdown($quantityBreakdown);
        //dd($formattedBreakdown);

        foreach ($productType->productMeasurement as $measurement) {
            $purchaseUnitName = optional($measurement->purchaseUnit)->purchase_unit_name;

            if ($purchaseUnitName) {
                $matchingPrice = $activePrices->firstWhere('purchase_unit_id', $measurement->purchasing_unit_id);

                $purchasingPrices[] = $purchaseUnitName . ': ' .
                    ($matchingPrice ? $matchingPrice->cost_price . ' Ngn' : 'Not set');

                $sellingPrices[] = $purchaseUnitName . ': ' .
                    ($matchingPrice ? $matchingPrice->selling_price . ' Ngn' : 'Not set');
            }
        }

        // Function to calculate the quantity breakdown


        return [
            'id' => $productType->id,
            'product_sub_category' => optional($productType->subCategory)->sub_category_name,
            'product_sub_category_id' => optional($productType->subCategory)->id,
            'product_name' => $productType->product_type_name,
            'product_image' => $productType->product_type_image,
            'product_description' => $productType->product_type_description,
            'vat' => $productType->vat,
            'product_category' => optional($productType->product_category)->category_name,
            'product_category_id' => optional($productType->product_category)->id,

            // Using the quantity breakdown function
            'quantity_available' => $formattedBreakdown,


            'purchasing_price' => $purchasingPrices,
            'selling_price' => $sellingPrices,
            'purchase_unit_name' => $productType->productMeasurement->map(function ($measurement) {
                return optional($measurement->purchaseUnit)->purchase_unit_name ?? null;
            })->toArray(),

            'unit' => $productType->productMeasurement->map(function ($measurement) {
                $unitValue = optional($measurement->purchaseUnit)->unit;
                $purchaseUnitName = optional($measurement->purchaseUnit)->purchase_unit_name ?? 'unit';

                $parentPurchaseUnit = optional($measurement->purchaseUnit)->parentPurchaseUnit;

                if ($parentPurchaseUnit) {
                    $parentUnitName = $parentPurchaseUnit->purchase_unit_name ?? 'unit';
                    $unitValue = "$unitValue $purchaseUnitName in a $parentUnitName";
                } else {
                    $unitValue = "$unitValue unit of $purchaseUnitName";
                }

                return $unitValue;
            })->toArray(),

            'supplier_name' => trim((optional($productType->suppliers)->first_name ?? '') . ' ' . (optional($productType->suppliers)->last_name ?? '')) ?: 'None',
            'supplier_phone_number' => optional($productType->suppliers)->phone_number ?? 'None',
            'date_created' => $productType->created_at,
            'created_by' => optional($productType->creator)->first_name . " " . optional($productType->creator)->last_name,
            'updated_by' => optional($productType->updater)->first_name . " " . optional($productType->updater)->last_name,
        ];
    }





    public function searchProductType($searchCriteria)
    {

        $this->logRepository->logEvent(
            'product_types',
            'search',
            null,
            'ProductType',
            "$this->username searched for product types with criteria: $searchCriteria"
        );
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
    //use in purchase to load all product
    public function onlyProductTypeName($mode)
    {
        // Retrieve the product types with their related measurements and purchase units
        $response = ProductType::select("id", "product_type_name")
    ->when($mode === "actual", function ($query) {
        $query->where('is_estimated', '=', 0);
    })
    ->with([
        'productMeasurement' => function ($query) {
            $query->select('id', 'product_type_id', 'purchasing_unit_id');
        },
        'productMeasurement.purchaseUnit' => function ($query) {
            $query->select('id', 'purchase_unit_name', 'unit', 'parent_purchase_unit_id');
        },
    ])
    ->get()
    ->transform(function ($productType) use ($mode) {
        $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($productType->productMeasurement);

        return [
            'id' => $productType->id,
            'product_type_name' => $productType->product_type_name,
            'product_measurement' => $productType->productMeasurement->map(function ($measurement) {
                $purchaseUnit = optional($measurement->purchaseUnit);

                return [
                    'purchase_unit_id' => optional($purchaseUnit)->id,
                    'purchase_unit_name' => optional($purchaseUnit)->purchase_unit_name,
                    'parent_purchase_unit_id' => optional($purchaseUnit)->parent_purchase_unit_id,
                    // Using the unit directly from purchase unit instead of selling units
                    'unit' => optional($purchaseUnit)->unit,
                ];
            })->values(), // Reset array keys after transformation
            // Conditionally include `no_of_smallestUnit_in_each_unit`
        ] + ($mode === "actual" ? ['no_of_smallestUnit_in_each_unit' => $no_of_smallestUnit_in_each_unit] : []);
    });


        // Return the response as JSON
        return response()->json(['data' => $response]);
    }


    //*************************** */


    public function getProductTypeByName($product_id)
    {
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;

        $response = ProductType::select(
            'id',
            'product_type_name',
            'barcode',
            'vat',
            'is_capacity_quantity_est'
        )
        ->with([
            'productMeasurement',
            'productMeasurement.PurchaseUnit:id,purchase_unit_name,unit,parent_purchase_unit_id', // Load PurchaseUnit relationship
            'price' => function ($query) {
                $query->select('id', 'cost_price', 'selling_price', 'product_type_id', 'is_cost_price_est', 'is_selling_price_est');
            },
            'stores' => function ($query) use ($branchId) {
                $query->selectRaw('product_type_id, purchase_unit_id, SUM(capacity_qty_available) as total_quantity')
                    ->where('status', 1);

                if ($branchId !== 'all' && auth()->user()->role->role_name != 'Admin') {
                    $query->where('branch_id', $branchId);
                }
                $query->groupBy('product_type_id', 'purchase_unit_id');
            },
        ])
        ->get();

        if ($response) {
            $response = $response->map(function ($item) {
                // Calculate the total capacity_quantity_available across all stores
                $totalCapacityQuantity = collect($item->stores)->sum('total_quantity');

                $stores = collect($item->stores)->groupBy('purchase_unit_id'); // Group stores by purchase_unit_id
                $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($item->productMeasurement);

                $measurements = $item->productMeasurement->map(function ($measurement) use ($stores, $item) {
                    // Match the purchase_unit_id with the store data
                    $store = $stores->get($measurement->purchasing_unit_id)?->first();

                    // Find the corresponding price for this product type
                    $price = $item->price->firstWhere('product_type_id', $item->id);

                    return [
                        'purchase_unit_id' => optional($measurement->PurchaseUnit)->id,
                        'purchase_unit_name' => optional($measurement->PurchaseUnit)->purchase_unit_name,
                        'unit' => optional($measurement->PurchaseUnit)->unit,
                        'price_id' => optional($price)->id,
                        'cost_price' => optional($price)->cost_price,
                        'is_cost_price_est' => optional($price)->is_cost_price_est,
                        'selling_price' => optional($price)->selling_price,
                        'is_selling_price_est' => optional($price)->is_selling_price_est,
                        'capacity_quantity_available' => optional($store)->total_quantity, // Specific capacity quantity for the unit
                    ];
                });

                $item->purchase_units = $measurements;

                // Add total capacity quantity after is_capacity_quantity_est
                $item->no_of_smallestUnit_in_each_unit =  $no_of_smallestUnit_in_each_unit;

                // Remove unnecessary relationships
                unset($item->stores, $item->productMeasurement, $item->price);

                return $item;
            });

            return response()->json(['data' => $response], 200);
        }

        return response()->json(['data' => []], 404); // Return empty response if no data found
    }











    public function create(array $data)
    {



        try {
            DB::beginTransaction();

            // Remove array values from `$data` for `ProductType` insertion
            $productData = $data;
            unset($productData['purchase_unit_id']);

            // Insert into `product_types` table without the array fields
            $productType = ProductType::create($productData);

            // Use the original arrays for inserting into `ProductMeasurement`
            $purchaseUnitIds = $data['purchase_unit_id'];


            foreach ($purchaseUnitIds as $index => $purchaseUnitId) {


                // Insert each combination into `ProductMeasurement`
                if ($purchaseUnitId) {
                    \App\Models\ProductMeasurement::create([
                        'product_type_id' => $productType->id,
                        'purchasing_unit_id' => $purchaseUnitId,

                    ]);
                }
            }

            DB::commit(); // Commit the transaction if all operations succeed

            $this->logRepository->logEvent(
                'product_types',
                'create',
                $productType->id,
                'ProductType',
                "$this->username created a new product type: {$productType->product_type_name}",
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Product has been created successfully',
                'data' => $productType,
            ], 200);

        } catch (Exception $e) {
            DB::rollBack(); // Rollback the transaction if any exception occurs

            return response()->json([
                'success' => false,
                'message' => 'Product could not be created',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function findById($id)
    {
        // Retrieve the product type with its related measurements and other details
        $productType = ProductType::with([
            'productMeasurement.PurchaseUnit',
        ])->find($id);

        if (!$productType) {
            return null; // Handle the case when the product is not found
        }

        // Initialize the result array with basic product details
        $result = [
            'product_type_name' => $productType->product_type_name,
            'product_type_description' => $productType->product_type_description,
            'barcode' => $productType->barcode ?? '', // Include barcode if available
            'vat' => $productType->vat ?? '',
            'sub_category_id' => optional($productType->subCategory)->id,
            'category_id' => optional($productType->product_category)->id,
        ];

        // Transform related product measurements
        $measurements = $productType->productMeasurement->map(function ($measurement, $index) use ($productType) {
            // Get current purchase unit details
            $purchaseUnit = optional($measurement->purchaseUnit);

            return [
                // Use the index in the keys to show them sequentially
                "purchase_unit_id[$index]" => $purchaseUnit->id ?? null,
                "purchase_unit_name[$index]" => $purchaseUnit->purchase_unit_name ?? 'unit',
                "unit[$index]" =>  optional($measurement->purchaseUnit)->unit,
            ];
        })->collapse()->toArray(); // Collapse the collection into a single array

        // Merge measurements data into the result
        $result = array_merge($result, $measurements);

        return $result;
    }



    public function update($id, array $data)
    {
        // Begin a transaction

        DB::beginTransaction();

        try {
            // Retrieve the existing product type
            $productType = ProductType::find($id);

            if (!$productType) {
                return response()->json(['success' => false, 'message' => 'Product type not found'], 404);
            }

            // Update the product type attributes
            $productType->update([
                'product_type_name' => $data['product_type_name'] ?? $productType->product_type_name,
                'product_type_description' => $data['product_type_description'] ?? $productType->product_type_description,
                'barcode' => $data['barcode'] ?? $productType->barcode,
                'vat' => $data['vat'],
                'sub_category_id' => $data['sub_category_id'] ?? $productType->sub_category_id,
                'category_id' => $data['category_id'] ?? $productType->category_id,
            ]);

            // Process measurement units
            // Process measurement units
            $purchaseUnitIds = $data['purchase_unit_id'] ?? [];


            foreach ($purchaseUnitIds as $index => $purchaseUnitId) {


                if ($purchaseUnitId) {
                    // Check if an existing measurement exists
                    $existingMeasurement = ProductMeasurement::where('product_type_id', $productType->id)->where('purchasing_unit_id', $purchaseUnitId)->first();


                    if ($existingMeasurement) {
                        // Update the existing measurement
                        $existingMeasurement->update([
                            'product_type_id' => $productType->id,
                            'purchasing_unit_id' => $purchaseUnitId,

                        ]);
                    } else {
                        // Insert a new measurement
                        ProductMeasurement::create([
                            'product_type_id' => $productType->id,
                            'purchasing_unit_id' => $purchaseUnitId,

                        ]);
                    }
                }
            }


            // Commit the transaction
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Product type updated successfully'], 200);
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            Log::channel('insertion_errors')->error('Error updating product type: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'This product type could not be updated'], 500);
        }
    }


    public function delete($id)
    {
        try {
            $ProductType = ProductType::find($id);


            if (!$ProductType) {
                return response()->json([
                    'success' => false,
                    'message' => "No product found"
                ], 404);
            }
            $ProductType->delete();
            $this->logRepository->logEvent(
                'product_types',
                'delete',
                $id,
                'ProductType',
                "$this->username deleted product type with ID $id"
            );
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
                'product_name' => $productType->product_type_name,
                'quantity_available' => $store->capacity_qty_available ?? 0,
                'batch_no' => $purchase->batch_no ?? '',
                'expiry_date' => $purchase->expiry_date,
                'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
                'selling_unit_name' => optional($productType->unitSelling)->selling_unit_name,
                //'branch_id' => $purchase->branch_id // Include branch_id in the response
            ];
        });

        $responseMsg = "Record retrieved successfully";


        // Check if the request has download set to true
        if (!isset($request['download'])) {
            // Send email only if download is not set to true
            $isPdf = false;
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
        } else {

            $pdf = $this->generatePdf->generatePdf($response, "Products that about to expire in 7 days");
            $isPdf = true;
            return ["data" => $pdf, "responseMsg" => $responseMsg, "isPdf" => $isPdf];
        }


        return ["data" => $response, "responseMsg" => $responseMsg, "isPdf" => $isPdf];
    }



    private function generateExpiredProductTable($responseData, $productDetails)
    {
        // Organization and branch details table (header)
        // <strong>State:</strong> {$responseData['state_name']}<br>
        //<strong>Country:</strong> {$responseData['country_name']}
        $headerTable = "
        <table style='width: 100%; border-collapse: collapse;'>
            <tr>
                <td style='padding: 8px;'>
                    <strong>Branch Name:</strong> {$responseData['branch_name']}<br>
                    <strong>Branch Email:</strong> {$responseData['branch_email']}<br>
                    <strong>Branch Phone:</strong> {$responseData['branch_phone_number']}<br>
                   
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
                <td style='border: 1px solid black; padding: 8px;'>{$product['product_name']}</td>
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



        $startDate = Carbon::parse($request['start_date'])->startOfDay();
        $endDate = Carbon::parse($request['end_date'])->endOfDay();

        //Log::info('Start Date:', ['startDate' => $startDate->toDateTimeString()]);
        //Log::info('End Date:', ['endDate' => $endDate->toDateTimeString()]);



        $branchId = auth()->user()->branch_id;



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


        if (isset($request['all']) && $request['all'] == true) {

            $expiredProducts = $expiredProductsQuery->get();
            $response = $expiredProducts->map(function ($purchase) {
                $productType = $purchase->productType;
                $store = null;

                if ($productType->store) {
                    $store = $productType->store->where('batch_no', $purchase->batch_no)->first();
                }


                return [
                    'product_sub_category' => optional($productType->subCategory)->sub_category_name,
                    'product_name' => $productType->product_type_name,
                    'quantity_available' => $store->capacity_qty_available ?? 0,
                    'batch_no' => $purchase->batch_no ?? '',
                    'expiry_date' => $purchase->expiry_date,
                    'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
                    'selling_unit_name' => optional($productType->unitSelling)->selling_unit_name,
                ];
            });

            $startDateFormatted = date('d-m-y', strtotime($startDate));
            $endDateFormatted = date('d-m-y', strtotime($endDate));

            $pdf = $this->generatePdf->generatePdf($response, " Expired Products ($startDateFormatted - $endDateFormatted)");



            return ["data" => $pdf, "isPdf" => true];

        }

        $expiredProducts = $expiredProductsQuery->paginate(20);
        //log::info($expiredProducts);
        $response = $expiredProducts->getCollection()->map(function ($purchase) {
            $productType = $purchase->productType;
            $store = null;

            if ($productType->store) {
                $store = $productType->store->where('batch_no', $purchase->batch_no)->first();
            }


            return [
                'product_sub_category' => optional($productType->subCategory)->sub_category_name,
                'product_name' => $productType->product_type_name,
                'quantity_available' => $store->capacity_qty_available ?? 0,
                'batch_no' => $purchase->batch_no ?? '',
                'expiry_date' => $purchase->expiry_date,
                'purchase_unit_name' => optional($productType->unitPurchase)->purchase_unit_name,
                'selling_unit_name' => optional($productType->unitSelling)->selling_unit_name,
            ];
        });
        $expiredProducts->setCollection($response);
        return ["data" => $expiredProducts, "isPdf" => false];
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

                $latestPrice = $product->latest_price;

                return [
                    'product_name' => $product->product_type_name,
                    'cost_price' => $latestPrice ? $latestPrice['cost_price'] : '',
                    'selling_price' => $latestPrice ? $latestPrice['selling_price'] : '',
                ];
            });
            $pdf = $this->generatePdf->generatePdf($response, " Price Lists");
            return ["data" => $pdf, "isPdf" => true];
            //return $response;
        }

        // Otherwise, paginate the results
        $products = $productsQuery->paginate(20); // Paginate 20 per page

        // Transform each product to include the required data
        $products->getCollection()->transform(function ($product) {
            $latestPrice = $product->latest_price;

            return [
                'product_name' => $product->product_type_name,
                'product_description' => $product->product_type_description,
                'cost_price' => $latestPrice ? $latestPrice['cost_price'] : 'No price available',
                'selling_price' => $latestPrice ? $latestPrice['selling_price'] : 'No price available',
            ];
        });
        return ["data" => $products, "isPdf" => false];
        // return $products;
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
