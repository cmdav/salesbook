<?php

namespace App\Services\Inventory\SaleService;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\Email\EmailService;
use App\Services\UserService\UserRepository;
use App\Models\Price;
use App\Models\Store;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use App\Models\ProductType;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Services\GeneratePdf;
use Exception;
use App\Services\Security\LogService\LogRepository;

class SaleRepository
{
    protected UserRepository $userRepository;
    protected GeneratePdf $generatePdf;
    protected $logRepository;
    protected $username;


    public function __construct(UserRepository $userRepository, GeneratePdf $generatePdf, LogRepository $logRepository)
    {
        $this->userRepository = $userRepository;
        $this->generatePdf = $generatePdf;
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();


    }
    //index to select all sales
    private function query($branchId = '')
    {
        $query = Sale::with([
            'product:id,product_type_name,product_type_image,product_type_description',
            'payment_details:id,payment_identifier',
            'unitPurchase',
            'branches:id,name,state_id,country_id,city,phone_number,email,address',
            'customers:id,first_name,last_name,contact_person,phone_number',
            'Price:id,selling_price,cost_price'
        ])
        ->selectRaw('id,
                    transaction_id,
                     SUM(quantity) as total_quantity,
                     price_id,
                     purchase_unit_id,
                     branch_id,
                     payment_method,
                     product_type_id,
                     customer_id,
                     created_at,
                     SUM(quantity) as quantity,
                     MAX(batch_no) as batch_no,
                     MAX(price_sold_at) as price_sold_at,
                     MAX(vat) as vat,
                     MAX(created_by) as created_by,
                     MAX(updated_by) as updated_by,
                    
                     MAX(updated_at) as updated_at,
                     MAX(old_price_id) as old_price_id')
        ->groupBy('transaction_id', 'price_id', 'branch_id', 'payment_method', 'product_type_id', 'customer_id', 'created_at', 'id', 'purchase_unit_id');

        // Apply the where clause if branch_id is not 'all'
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        return $query->latest();
    }

    public function index($request)
    {
        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }

        $this->logRepository->logEvent(
            'sales',
            'view',
            null,
            'Sale',
            "$this->username viewed all sales"
        );

        $sale = $this->query($branchId)->paginate(20);
        //return $sale;

        $sale->getCollection()->transform(function ($sale) {

            return $this->transformProduct($sale);
        });


        return $sale;

    }

    public function searchSale($searchCriteria, $request)
    {
        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }
        $this->logRepository->logEvent(
            'sales',
            'search',
            null,
            'Sale',
            "$this->username searched for sales with criteria: $searchCriteria"
        );

        $sale = $this->query($branchId)->where(function ($query) use ($searchCriteria) {
            $query->whereHas('product', function ($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            })
            ->orWhereHas('customers', function ($q) use ($searchCriteria) {
                $q->where('first_name', 'like', '%' . $searchCriteria . '%')
                  ->orWhere('last_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();


        $sale->transform(function ($sale) {

            return $this->transformProduct($sale);
        });


        return $sale;
    }




    private function transformProduct($sale)
    {
        $total_price = $sale->price_sold_at * $sale->quantity;
        $formatted_total_price = number_format($total_price, 2, '.', ',');
        $formatted_price_sold_at = number_format($sale->price_sold_at, 2, '.', ',');

        return [
            'id' => $sale->id,
            'product_type_name' => optional($sale->product)->product_type_name,
            'product_type_description' => optional($sale->product)->product_type_description,
            'branch_name' => optional($sale->branches)->name,
            'branch_id' => optional($sale->branches)->id,
            'cost_price' => "No set",
            'price_sold_at' => $formatted_price_sold_at,
            'quantity' =>  $sale->quantity." ".optional($sale->unitPurchase)->purchase_unit_name,
            'batch_no' => $sale->batch_no,
            'total_price' => $formatted_total_price,
            'payment_method' => $sale->payment_details->payment_identifier ?? null,
            'created_at' => $sale->created_at,

            'customer_detail' => optional($sale->customers)->first_name . ' ' . optional($sale->customers)->last_name . ' ' . optional($sale->customers)->contact_person,
            'transaction_id' => $sale->transaction_id,
            'customer_phone_number' => optional($sale->customers)->phone_number,
            // 'created_by' => optional($sale->creator)->fullname,
            // 'updated_by' => optional($sale->updater)->fullname,
             'created_by' => optional($sale->creator)->first_name ? optional($sale->creator)->first_name . " " . optional($sale->creator)->last_name : optional($sale->creator->organization)->organization_name,

            'updated_by' => optional($sale->updater)->first_name ? optional($sale->updater)->first_name . " " . optional($sale->updater)->last_name : optional($sale->updater->organization)->organization_name,

            'organization_name' => optional(auth()->user()->organization)->organization_name,
            'organization_phone_number' => auth()->user()->phone_number,
            'organization_email' => auth()->user()->email,
            'organization_address' => optional(auth()->user()->organization)->company_address,

        ];
    }

    public function downSalesReceipt($transactionId, $request)
    {

        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }
        //
        $sales = $this->query($branchId)->where('transaction_id', $transactionId)->get();


        if ($sales->isEmpty()) {
            return response()->json(['message' => 'No sales found for this transaction.'], 404);
        }
        //return $sales;
        $transformedData = $this->transformSalesReceipt($sales);

        return $transformedData;
    }


    private function transformSalesReceipt($sales)
    {



        $paymentDetail = \App\Models\PaymentDetail::where('id', $sales->first()->payment_method)->first();
        $paymentIdentifier = optional($paymentDetail)->payment_identifier;
        // Define admin details
        $adminDetails = [
            'organization_name' => 'iSalesbook',
            'organization_phone_number' => '+2348161749665',
            'organization_email' => 'salesbook@rdas.com.ng',
            'organization_address' => 'Lagos',
        ];

        // Check if the user is authenticated
        if (Auth::check()) {
            // dd(auth()->user()->organization);
            $organizationDetails = [
               'organization_name' => optional(auth()->user()->organization)->organization_name,
                'organization_phone_number' => optional(auth()->user()->organization)->company_phone_number,
                'organization_email' => optional(auth()->user()->organization)->company_email,
                'company_address' => optional(auth()->user()->organization)->company_address,
            ];
        } else {
            $organizationDetails = $adminDetails;
        }

        // Collect transaction details from the first sale
        $transactionDetails = [
            'created_at' => $sales->first()->created_at,
            'customer_detail' => optional($sales->first()->customers)->first_name . ' ' . optional($sales->first()->customers)->last_name . ' ' . optional($sales->first()->customers)->contact_person,
            'customer_phone_number' => optional($sales->first()->customers)->phone_number,
            //'branch_name' => optional($sales->branches)->name,
            'branch_name' => optional($sales->first()->branches)->name,
            'branch_country' => optional($sales->first()->branches)->country_name,
            'branch_state' => optional($sales->first()->branches)->state_name,
            'branch_city' => optional($sales->first()->branches)->city,
            'branch_address' => optional($sales->first()->branches)->address,
            'branch_email' => optional($sales->first()->branches)->email,
            'branch_phone_number' => optional($sales->first()->branches)->phone_number,

             //'branch_name' => '',
            'transaction_id' => $sales->first()->transaction_id,
            'transaction_amount' => 0, // Will be calculated below
            'organization_name' => $organizationDetails['organization_name'],
            'organization_phone_number' => $organizationDetails['organization_phone_number'],
            'organization_email' => $organizationDetails['organization_email'],
            'organization_address' => $organizationDetails['company_address'],
            'payment_method' => $paymentIdentifier,
        ];

        $items = $sales->map(function ($sale) use (&$transactionDetails) {

            $total_price = $sale->price_sold_at * $sale->quantity;
            $vatAmount = $sale->vat == "yes" ? $total_price * 0.075 : 0; // Assuming VAT is 7.5%
            $total_price_with_vat = $total_price + $vatAmount;
            $formatted_total_price = number_format($total_price_with_vat, 2, '.', ',');

            // Accumulate total transaction amount
            $transactionDetails['transaction_amount'] += $total_price_with_vat;

            return [
                'id' => $sale->id,
                'product_name' => optional($sale->product)->product_type_name,
                'product_description' => optional($sale->product)->product_type_description,
                'price_sold_at' => $sale->price_sold_at,
                'vat_state' => $sale->vat,//yes or no
                'quantity' => $sale->quantity,
                'amount' => $total_price,
                'vat' => $vatAmount,
                'total_price' => $formatted_total_price,
               // 'payment_method' => $sale->payment_method,

            ];
        });

        // Package everything into the expected structure
        return [
            'transaction_details' => $transactionDetails,
            'items' => $items,
        ];
    }

    private function transformDailySales($sale, $isPdf = false)
    {
        $total_price = $sale->price_sold_at * $sale->quantity;
        $formatted_total_price = number_format($total_price, 2, '.', ',');

        $data = [
            'product_name' => optional($sale->product)->product_type_name,
            'price_sold_at' => $sale->price_sold_at,
            'quantity' => $sale->quantity,
            'total_price' => $formatted_total_price,
        ];

        // Add the 'id' only if $isPdf is false
        if (!$isPdf) {
            $data['id'] = $sale->id;
        }

        return $data;
    }

    public function findById($id)
    {
        return Sale::find($id);
    }

    public function update($id, array $data)
    {
        $sale = $this->findById($id);

        if ($sale) {

            $sale->update($data);
        }
        return $sale;
    }

    public function delete($id)
    {
        $sale = $this->findById($id);

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
        }

        try {
            $sale->delete();

            $this->logRepository->logEvent(
                'sales',
                'delete',
                $id,
                'Sale',
                "$this->username deleted a sale with ID $id"
            );

            return response()->json(['success' => true, 'message' => 'Deletion successful'], 200);
        } catch (Exception $e) {
            Log::error('Error deleting sale: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting sale'], 500);
        }
    }
    private function getSmallestUnitCapacity($purchaseUnit, $capacityQty)
    {
        // If the purchase unit has no sub-units, return its capacity
        if ($purchaseUnit->subPurchaseUnits->isEmpty()) {
            return $capacityQty;
        }

        // Traverse through sub-units and calculate total smallest units
        $totalSmallestUnits = 0;
        foreach ($purchaseUnit->subPurchaseUnits as $subUnit) {
            $totalSmallestUnits += $this->getSmallestUnitCapacity($subUnit, $capacityQty * $subUnit->unit);
        }

        return $totalSmallestUnits;
    }

    public function create(array $data)
    {
        $emailService = new EmailService();
        $transactionId = time() . rand(1000, 9999);

        try {
            $response = DB::transaction(function () use ($data, $emailService, $transactionId) {
                $productDetails = [];
                $totalPrice = 0;

                foreach ($data['products'] as $product) {
                    // Get the latest price for the product with purchase_unit_id
                    $latestPrice = Price::where([
                            ['product_type_id', $product['product_type_id']],
                            ['purchase_unit_id', $product['purchase_unit_id']],
                            ['status', 1]
                        ])->orderBy('created_at', 'desc')->firstOrFail();

                    // Get all batches of the product in the branch
                    $stores = Store::where('product_type_id', $product['product_type_id'])
                                  // ->where('purchase_unit_id', $product['purchase_unit_id'])
                                   ->where('branch_id', auth()->user()->branch_id)
                                   ->where('status', 1)
                                   ->orderBy('created_at', 'asc')
                                   ->select("id", "capacity_qty_available", "branch_id", "batch_no")
                                   ->get();

                    // Get the purchase unit with its sub-units
                    $purchaseUnit = \App\Models\PurchaseUnit::with(['subPurchaseUnits:id,purchase_unit_name,unit,parent_purchase_unit_id'])
                        ->where('id', $product['purchase_unit_id'])
                        ->first();

                    // Calculate the smallest unit capacity
                    $smallestUnitCapacity = $this->getSmallestUnitCapacity($purchaseUnit, $product['quantity']);

                    // Get the total available quantity across all stores
                    $totalAvailableQuantity = $stores->sum('capacity_qty_available');

                    if ($totalAvailableQuantity < $smallestUnitCapacity) {
                        throw new \Exception("Insufficient stock for the requested quantity.", 400);
                    }

                    foreach ($stores as $store) {
                        if ($smallestUnitCapacity <= 0) {
                            break;
                        }

                        $oldPrice = Price::where('batch_no', $store->batch_no)
                                         ->where('product_type_id', $product['product_type_id'])
                                         ->where('purchase_unit_id', $product['purchase_unit_id'])
                                         ->first();

                        $oldPriceId = $oldPrice ? $oldPrice->id : null;

                        $soldQuantityFromBatch = min($smallestUnitCapacity, $store->capacity_qty_available);
                        $store->capacity_qty_available -= $soldQuantityFromBatch;
                        $smallestUnitCapacity -= $soldQuantityFromBatch;

                        if ($store->capacity_qty_available == 0) {
                            $store->status = 0;
                        }

                        $store->save();

                        $sale = new Sale();
                        $sale->fill([
                            'product_type_id' => $product['product_type_id'],
                            'customer_id' => $data['customer_id'],
                            'price_sold_at' => $product['price_sold_at'],
                            'quantity' => $product['quantity'],
                            'vat' => $product['vat'],
                            'payment_method' => $data['payment_method'],
                            'transaction_id' => $transactionId,
                            'is_offline' => $data['is_offline'] ?? 0,
                            'old_price_id' => $oldPriceId,
                            'batch_no' => $store->batch_no,
                            'purchase_unit_id' => $product['purchase_unit_id'], // Added purchase_unit_id
                        ]);
                        $sale->price_id = $latestPrice->id;
                        $sale->save();

                        $amount = $product['price_sold_at'] * $soldQuantityFromBatch;
                        $vatValue = $product['vat'] == "yes" ? ($amount * 0.075) : 0;
                        $amount += $vatValue;
                        $totalPrice += $amount;

                        $productDetails[] = [
                            "productTypeName" => $latestPrice->productType->product_type_name,
                            'price' => $product['price_sold_at'],
                            "quantity" => $product['quantity'],
                            "vat" => $product['vat'] == 'yes' ? 'yes' : 'no',
                            "amount" => $amount,
                        ];
                    }
                }

                $user = Customer::select('id', 'first_name', 'last_name', 'email', 'contact_person', 'phone_number')
                                ->where('id', $data['customer_id'])
                                ->first();
                if ($user) {
                    $customerDetail = trim($user->first_name . ' ' . $user->last_name . ' ' . $user->contact_person);

                    $tableDetail = $this->generateProductDetailsTable($productDetails, $totalPrice, $transactionId);
                    // if (!isset($data['is_offline'])) {
                    //     $emailService->sendEmail(['email' => $user->email, 'first_name' => $customerDetail], "sales-receipt", $tableDetail);
                    // }
                }
                $this->logRepository->logEvent(
                    'sales',
                    'create',
                    null,
                    'Sale',
                    "$this->username created a new sale with transaction ID $transactionId",
                    $data
                );

                $receiptData = $this->downSalesReceipt($transactionId, ['branch_id' => auth()->user()->branch_id]);

                return $receiptData;
            });

            return response()->json(['success' => true, 'data' => $response, 'message' => 'Sales record was added successfully'], 201);
        } catch (\Exception $e) {
            if ($e->getCode() === 400) {
                return response()->json(['message' => $e->getMessage()], 400);
            }

            Log::channel('insertion_errors')->error('Error creating or updating sale: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create sales', 'error' => $e->getMessage()], 500);
        }
    }







    private function generateProductDetailsTable($productDetails, $totalPrice, $transactionId)
    {

        $transactionTime = Carbon::now()->format('Y-m-d H:i:s');
        $branch = $this->userRepository->getuserOrgAndBranchDetail();

        $tableHtml = "
<table style='width: 100%; max-width: 100%; border-collapse: collapse; border: 1px solid black;'>
    <tr>
        <th style='border: 1px solid black; padding: 8px; font-size: 14px'>Product Name</th>
        <th style='border: 1px solid black; padding: 8px; font-size: 14px'>Price</th>
        <th style='border: 1px solid black; padding: 8px; font-size: 14px'>Quantity</th>
        <th style='border: 1px solid black; padding: 8px; font-size: 14px'>VAT</th>
        <th style='border: 1px solid black; padding: 8px; font-size: 14px'>Total</th>
    </tr>";

        foreach ($productDetails as $detail) {
            // Format the price and total for each product
            $formattedPrice = number_format($detail['price'], 2, '.', ',');
            $formattedTotal = number_format($detail['amount'], 2, '.', ',');
            $vatValue = number_format($detail['amount'] - ($detail['price'] * $detail['quantity']), 2, '.', ',');

            $tableHtml .= "<tr>
        <td style='border: 1px solid black; padding: 8px; font-size: 14px;'>{$detail['productTypeName']}</td>
        <td style='border: 1px solid black; padding: 8px; font-size: 14px;'>₦{$formattedPrice}</td>
        <td style='border: 1px solid black; padding: 8px; font-size: 14px;'>{$detail['quantity']}</td>
        <td style='border: 1px solid black; padding: 8px; font-size: 14px;'>₦{$vatValue}</td>
        <td style='border: 1px solid black; padding: 8px; font-size: 14px;'>₦{$formattedTotal}</td>
    </tr>";
        }

        // Format the grand total price
        $formattedGrandTotal = number_format($totalPrice, 2, '.', ',');

        $tableHtml .= "<tr>
    <td style='border: 1px solid black; padding: 8px; text-align: right; font-size: 14px;'><strong>Transaction Id</strong></td>
    <td style='border: 1px solid black; padding: 8px; text-align: right; font-size: 14px;'><strong>$transactionId</strong></td>
    <td colspan='2' style='border: 1px solid black; padding: 8px; text-align: right; font-size: 14px;'><strong>Total:</strong></td>
    <td style='border: 1px solid black; padding: 8px; font-size: 14px;'><strong>₦{$formattedGrandTotal}</strong></td>
</tr>";

        $tableHtml .= "</table>";

        // Wrap the table in a responsive container


        return $tableHtml;

    }
    public function dailySale()
    {
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay = Carbon::now()->endOfDay();

        $sales = $this->querySales($startOfDay, $endOfDay)->paginate(20);

        // Transform the collection to apply any needed transformations
        $sales->getCollection()->transform(function ($sale) {
            return $this->transformDailySales($sale);
        });

        return $sales;
    }

    public function gettotalSaleReport($request)
    {


        // Retrieve start and end date from the request
        $startDate = isset($request['start_date']) ? Carbon::parse($request['start_date'])->startOfDay() : null;
        $endDate = isset($request['end_date']) ? Carbon::parse($request['end_date'])->endOfDay() : null;

        if (!$startDate || !$endDate) {
            return response()->json(['success' => false, 'message' => 'Start date and end date are required'], 400);
        }

        // Query for sales data within the time frame
        $salesQuery = $this->querySales($startDate, $endDate);

        // Check if 'all' == true in the request to return all data without pagination
        if (isset($request['all']) && $request['all'] == true) {
            $sales = $salesQuery->get(); // Get all sales data without pagination

            // Transform the collection to apply any needed transformations
            $response = $sales->map(function ($sale) {
                return $this->transformDailySales($sale, true);
            });

            $pdf = $this->generatePdf->generatePdf($response, "Total Sales ");

            return ["data" => $pdf, "isPdf" => true];

        }

        // Otherwise, paginate the results
        $sales = $salesQuery->paginate(20);

        // Transform the paginated collection
        $sales->getCollection()->transform(function ($sale) {
            return $this->transformDailySales($sale);
        });
        return ["data" => $sales, "isPdf" => false];
        //return $sales;
    }


    public function getmonthlySaleReport($request)
    {

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Query for sales data for the current month
        $salesQuery = $this->querySales($startOfMonth, $endOfMonth);

        // Check if 'all' == true in the request to return all data without pagination
        if (isset($request['all']) && $request['all'] == true) {
            $sales = $salesQuery->get(); // Get all sales data without pagination

            // Transform the collection
            $response = $sales->map(function ($sale) {
                return $this->transformDailySales($sale, true);
            });

            $startOfMonthFormatted = date('F Y', strtotime($startOfMonth));
            $endOfMonthFormatted = date('F Y', strtotime($endOfMonth));

            $pdf = $this->generatePdf->generatePdf($response, "Monthly Sales ($startOfMonthFormatted - $endOfMonthFormatted)");


            return ["data" => $pdf, "isPdf" => true];
        }

        // Otherwise, paginate the results
        $sales = $salesQuery->paginate(20);

        // Transform the paginated collection
        $sales->getCollection()->transform(function ($sale) {
            return $this->transformDailySales($sale);
        });
        return ["data" => $sales, "isPdf" => false];
        //return $sales;
    }




    private function querySales($startDate, $endDate)
    {
        // $branchId = auth()->user()->branch_id;
        $branchId = auth()->user()->branch_id;
        $query = Sale::with([
            'product:id,product_type_name',
            'Price:id,selling_price'
        ])
        ->selectRaw('transaction_id,
                     SUM(quantity) as quantity,
                     MAX(id) as id,
                     product_type_id,
                     price_id,
                     MAX(price_sold_at) as price_sold_at,
                     MAX(batch_no) as batch_no,
                     MAX(created_at) as created_at')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('transaction_id', 'product_type_id', 'price_id');

        // Apply branch filtering if necessary
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        return $query->latest();
    }



}
