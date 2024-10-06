<?php

namespace App\Services\Inventory\SaleService;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\Email\EmailService;
use App\Models\Price;
use App\Models\Store;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use App\Models\ProductType;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Exception;

class SaleRepository
{
    private function query($branchId = '')
    {

        $query = Sale::with(['product:id,product_type_name,product_type_image,product_type_description',
                            'payment_details:id,payment_identifier',
                         //'store:id,product_type_id,quantity_available',
                         'branches:id,name,state_id,country_id,city,phone_number,email,address',
                         'customers:id,first_name,last_name,contact_person,phone_number',
                         'Price:id,selling_price,cost_price'
                         //'organization:id,organization_name,organization_logo'
                         // 'Price' => function ($query) {
                         //     $query->select('id', 'product_type_id', 'cost_price', 'selling_price', 'discount');
                         // }
                     ]);
        if ($branchId !== 'all') {
            // Apply the where clause if branch_id is not 'all' and the user is not admin
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

        $sale = $this->query($branchId)->paginate(20);


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

        // $cost_price = optional($sale->price)->is_new == 1
        // ? (optional($sale->price)->new_cost_price ?? optional($sale->price->referencePrice)->new_cost_price)
        // : (optional($sale->price)->cost_price ?? optional($sale->price->referencePrice)->cost_price);

        return [
            'id' => $sale->id,
            'product_type_name' => optional($sale->product)->product_type_name,
            'product_type_description' => optional($sale->product)->product_type_description,
            'branch_name' => optional($sale->branches)->name,
            'branch_id' => optional($sale->branches)->id,
            'cost_price' => "No set",
            'price_sold_at' => $formatted_price_sold_at,
            'quantity' => $sale->quantity,
            'batch_no' => $sale->batch_no,
            'total_price' => $formatted_total_price,
            'payment_method' => $sale->payment_details->payment_identifier ?? null,
            'created_at' => $sale->created_at,

            'customer_detail' => optional($sale->customers)->first_name . ' ' . optional($sale->customers)->last_name . ' ' . optional($sale->customers)->contact_person,
            'transaction_id' => $sale->transaction_id,
            'customer_phone_number' => optional($sale->customers)->phone_number,
            // 'created_by' => optional($sale->creator)->fullname,
            // 'updated_by' => optional($sale->updater)->fullname,
            'created_by' => optional($sale->creator)->first_name . "  " .  optional($sale->creator)->last_name,
            'updated_by' => optional($sale->updater)->first_name . "  " .  optional($sale->updater)->last_name,

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
            'payment_method' => $sales->first()->payment_method,
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
                'product_type_name' => optional($sale->product)->product_type_name,
                'product_type_description' => optional($sale->product)->product_type_description,
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

    private function transformDailySales($sale)
    {
        $total_price = $sale->price_sold_at * $sale->quantity;
        $formatted_total_price = number_format($total_price, 2, '.', ',');
        return [
            'id' => $sale->id,
            'product_type_id' => optional($sale->product)->product_type_name,
            'price_sold_at' => $sale->price_sold_at,
            'quantity' => $sale->quantity,
            'total_price' => $formatted_total_price,
        ];
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
        if ($sale) {
            return $sale->delete();
        }
        return null;
    }
    public function create(array $data)
    {
        $emailService = new EmailService();
        $transactionId = time() . rand(1000, 9999);

        try {
            $response = DB::transaction(function () use ($data, $emailService, $transactionId) {
                $productDetails = [];
                $totalPrice = 0; // Initialize total price
                $branch = null; // Initialize branch

                foreach ($data['products'] as $product) {
                    // Get latest price id
                    $latestPrice = Price::where([
                            ['product_type_id', $product['product_type_id']],
                            ['status', 1]
                        ])->orderBy('created_at', 'desc')->firstOrFail();

                    // Get all batches of the product in the specific branch, ordered by oldest first
                    $stores = Store::where('product_type_id', $product['product_type_id'])
                                   ->where('branch_id', auth()->user()->branch_id) // Filter by authenticated user's branch
                                   ->where('status', 1)
                                   ->orderBy('created_at', 'asc')
                                   ->select("id", "capacity_qty_available", "branch_id")
                                   ->get();

                    $remainingQuantity = $product['quantity'];
                    $totalAvailableQuantity = $stores->sum('capacity_qty_available');

                    // Check if there is enough stock across all batches in the branch
                    if ($totalAvailableQuantity < $remainingQuantity) {
                        throw new \Exception("Insufficient stock for the requested quantity.", 400);
                    }

                    foreach ($stores as $store) {
                        if ($remainingQuantity <= 0) {
                            break;
                        }

                        if ($store->capacity_qty_available >= $remainingQuantity) {
                            $store->capacity_qty_available -= $remainingQuantity;
                            $remainingQuantity = 0;
                        } else {
                            $remainingQuantity -= $store->capacity_qty_available;
                            $store->capacity_qty_available = 0;
                        }

                        if ($store->capacity_qty_available == 0) {
                            $store->status = 0;
                        }

                        $store->save();
                        $branch = $store->branches; // Fetch the branch details
                    }

                    // Save the sale record
                    $sale = new Sale();
                    $sale->fill([
                        'product_type_id' => $product['product_type_id'],
                        'customer_id' => $data['customer_id'],
                        'price_sold_at' => $product['price_sold_at'],
                        'quantity' => $product['quantity'],
                        'vat' => $product['vat'],
                        'payment_method' => $data['payment_method'],
                        'transaction_id' => $transactionId,
                    ]);
                    $sale->price_id = $latestPrice->id;
                    $sale->save();

                    // Calculate the amount and VAT
                    $amount = $product['price_sold_at'] * $product['quantity'];
                    $vatValue = $product['vat'] == "yes" ? ($amount * 0.075) : 0; // 7.5% VAT
                    $amount += $vatValue;
                    $totalPrice += $amount;

                    $productDetails[] = [
                        "productTypeName" => $latestPrice->productType->product_type_name,
                        'price' => $product['price_sold_at'],
                        "quantity" => $product['quantity'],
                        "vat" => $product['vat'] == 'yes' ? 'yes' : 'no',
                        "amount" => $amount
                    ];
                }

                // Customer details for the email
                $user = Customer::select('id', 'first_name', 'last_name', 'email', 'contact_person', 'phone_number')
                            ->where('id', $data['customer_id'])
                            ->first();
                if ($user) {
                    $customerDetail = trim($user->first_name . ' ' . $user->last_name . ' ' . $user->contact_person);

                    // Generate email content
                    $tableDetail = $this->generateProductDetailsTable($productDetails, $totalPrice, $transactionId, $branch);
                    $emailService->sendEmail(['email' => $user->email, 'first_name' => $customerDetail], "sales-receipt", $tableDetail);
                }

                // Return the same response as downSalesReceipt after sale creation
                $receiptData = $this->downSalesReceipt($transactionId, ['branch_id' => auth()->user()->branch_id]);

                return $receiptData;
            });

            return response()->json(['success' => true, 'data' => $response, 'message' => 'Sales record was added successfully'], 201);
        } catch (\Exception $e) {
            // Check if the exception is due to insufficient stock
            if ($e->getCode() === 400) {
                return response()->json(['message' => $e->getMessage()], 400);
            }

            // Handle other general exceptions
            Log::channel('insertion_errors')->error('Error creating or updating sale: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Failed to create sales', 'error' => $e->getMessage()], 500);
        }
    }




    private function generateProductDetailsTable($productDetails, $totalPrice, $transactionId, $branch)
    {

        $transactionTime = Carbon::now()->format('Y-m-d H:i:s');

        // Include branch details
        $branchDetails = $branch ? [
            'name' => $branch->name,
            'state' => $branch->state_name,
            'city' => $branch->city,
            'email' => $branch->email,
            'phone_number' => $branch->phone_number,
            'address' => $branch->address,
        ] : [
            'name' => 'N/A',
            'state' => 'N/A',
            'city' => 'N/A',
            'email' => 'N/A',
            'phone_number' => 'N/A',
            'address' => 'N/A',
        ];

        $tableHtml = "<table style='width: 100%; border-collapse: collapse; max-width: 100%;'>
                        <tr>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Transaction Time</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;' colspan='4'><strong>{$transactionTime}</strong></td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Branch Name</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>{$branchDetails['name']}</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>City</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;' colspan='2'><strong>{$branchDetails['city']}</strong></td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>State</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>{$branchDetails['state']}</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Email</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'  colspan='2'><strong>{$branchDetails['email']}</strong></td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Phone Number</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>{$branchDetails['phone_number']}</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Address</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;' colspan='2'><strong>{$branchDetails['address']}</strong></td>
                        </tr>
                        <tr>
                            <th style='border: 1px solid black; padding: 8px;'>Product Name</th>
                            <th style='border: 1px solid black; padding: 8px;'>Price</th>
                            <th style='border: 1px solid black; padding: 8px;'>Quantity</th>
                            <th style='border: 1px solid black; padding: 8px;'>VAT</th>
                            <th style='border: 1px solid black; padding: 8px;'>Total</th>
                        </tr>";

        foreach ($productDetails as $detail) {
            // Format the price and total for each product
            $formattedPrice = number_format($detail['price'], 2, '.', ',');
            $formattedTotal = number_format($detail['amount'], 2, '.', ',');
            $vatValue = number_format($detail['amount'] - ($detail['price'] * $detail['quantity']), 2, '.', ','); // Calculate the VAT value

            $tableHtml .= "<tr>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['productTypeName']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>₦{$formattedPrice}</td>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['quantity']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>₦{$vatValue}</td>
                                <td style='border: 1px solid black; padding: 8px;'>₦{$formattedTotal}</td>
                           </tr>";
        }

        // Format the grand total price
        $formattedGrandTotal = number_format($totalPrice, 2, '.', ',');

        $tableHtml .= "<tr>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Transaction Id</strong></td>
                            <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>$transactionId</strong></td>
                            <td colspan='2' style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Total:</strong></td>
                            <td style='border: 1px solid black; padding: 8px;'><strong>₦{$formattedGrandTotal}</strong></td>
                       </tr>";

        $tableHtml .= "</table>";

        // Wrap the table in a responsive container
        $responsiveTableHtml = "<div style='width: 100%; overflow-x: auto;'>$tableHtml</div>";

        return $responsiveTableHtml;
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
        // $branchId = 'all';
        // if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
        //     $branchId = $request['branch_id'];
        // } elseif (auth()->user()->role->role_name != 'Admin') {
        //     $branchId = auth()->user()->branch_id;
        // }

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
                return $this->transformDailySales($sale);
            });

            return $response;

        }

        // Otherwise, paginate the results
        $sales = $salesQuery->paginate(20);

        // Transform the paginated collection
        $sales->getCollection()->transform(function ($sale) {
            return $this->transformDailySales($sale);
        });

        return $sales;
    }


    public function getmonthlySaleReport($request)
    {
        // $branchId = 'all';
        // if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
        //     $branchId = $request['branch_id'];
        // } elseif (auth()->user()->role->role_name != 'Admin') {
        //     $branchId = auth()->user()->branch_id;
        // }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Query for sales data for the current month
        $salesQuery = $this->querySales($startOfMonth, $endOfMonth);

        // Check if 'all' == true in the request to return all data without pagination
        if (isset($request['all']) && $request['all'] == true) {
            $sales = $salesQuery->get(); // Get all sales data without pagination

            // Transform the collection
            $response = $sales->map(function ($sale) {
                return $this->transformDailySales($sale);
            });

            return $response;
        }

        // Otherwise, paginate the results
        $sales = $salesQuery->paginate(20);

        // Transform the paginated collection
        $sales->getCollection()->transform(function ($sale) {
            return $this->transformDailySales($sale);
        });

        return $sales;
    }




    private function querySales($startDate, $endDate)
    {
        $branchId = 'all';
        $branchId = auth()->user()->branch_id;
        // if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
        //     $branchId = $request['branch_id'];
        // } elseif (auth()->user()->role->role_name != 'Admin') {
        //     $branchId = auth()->user()->branch_id;
        // }

        $query = Sale::select("id", "quantity", "product_type_id", "price_id", "price_sold_at", "batch_no")
                     ->with(['product:id,product_type_name', 'Price:id,selling_price'])
                     ->whereBetween('created_at', [$startDate, $endDate]);

        // Add branch filtering condition
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        return $query->latest();
    }


}
