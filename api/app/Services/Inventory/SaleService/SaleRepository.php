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
   
    private function query($branchId=''){

       $query=Sale::with(['product:id,product_type_name,product_type_image,product_type_description',
                        //'store:id,product_type_id,quantity_available',
                        'branches:id,name',
                        'customers:id,first_name,last_name,contact_person,phone_number',
                        'Price:id,selling_price,cost_price'
                        //'organization:id,organization_name,organization_logo'
                        // 'Price' => function ($query) {
                        //     $query->select('id', 'product_type_id', 'cost_price', 'selling_price', 'discount');
                        // }
                    ]);
                    if ($branchId !== 'all' && auth()->user()->role->role_name != 'Admin') {
                        // Apply the where clause if branch_id is not 'all' and the user is not admin
                        $query->where('branch_id', $branchId);
                    }
                    return $query->latest();
    }
    public function index($request)
    {
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
       
         $sale =$this->query($branchId)->paginate(20);
                   

         $sale->getCollection()->transform(function($sale){

                            return $this->transformProduct($sale);
                        });
                
                
                    return $sale;

    }
    
    public function searchSale($searchCriteria, $request)
    {
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
        $sale =$this->query($branchId)->where(function($query) use ($searchCriteria) {
            $query->whereHas('product', function($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            })
            ->orWhereHas('customers', function($q) use ($searchCriteria) { 
                $q->where('first_name', 'like', '%' . $searchCriteria . '%')
                  ->orWhere('last_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();
                   

        $sale->transform(function($sale){

                           return $this->transformProduct($sale);
                       });
               
               
                   return $sale;
    }
    public function dailySale()
    {
       
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay = Carbon::now()->endOfDay();

       
        $sale =  Sale::select("id","quantity","product_type_id","price_id","price_sold_at","batch_no")
                     ->with(['product:id,product_type_name','Price:id,selling_price' ])
                     ->latest()->whereBetween('created_at', [$startOfDay, $endOfDay])->paginate(20);
                   
        
        // Transform the collection to apply any needed transformations
        $sale->getCollection()->transform(function($sale) {
            return $this->transformDailySales($sale);
        });

        return $sale;
    }



    private function transformProduct($sale){
        $total_price = $sale->price_sold_at * $sale->quantity;
        $formatted_total_price = number_format($total_price, 2, '.', ',');
        $formatted_price_sold_at = number_format($sale->price_sold_at, 2, '.', ',');
        
        $cost_price = optional($sale->price)->is_new == 1 
        ? (optional($sale->price)->new_cost_price ?? optional($sale->price->referencePrice)->new_cost_price) 
        : (optional($sale->price)->cost_price ?? optional($sale->price->referencePrice)->cost_price);
        
        return [
            'id' => $sale->id,
            'product_type_name' => optional($sale->product)->product_type_name,
            'product_type_description' => optional($sale->product)->product_type_description,
            'branch_name' => optional($sale->branches)->name,
            'cost_price' =>  $cost_price,
            'price_sold_at' => $formatted_price_sold_at,
            'quantity' => $sale->quantity,
            'batch_no' => $sale->batch_no,
            'total_price' => $formatted_total_price,
            'payment_method' => $sale->payment_method,
            'created_at' => $sale->created_at,
            
            'customer_detail' => optional($sale->customers)->first_name . ' ' . optional($sale->customers)->last_name . ' ' . optional($sale->customers)->contact_person,
            'transaction_id' => $sale->transaction_id,
            'customer_phone_number' => optional($sale->customers)->phone_number,
            'created_by' => optional($sale->creator)->fullname,
            'updated_by' => optional($sale->updater)->fullname,

            'organization_name' => auth()->user()->company_name,
            'organization_phone_number' => auth()->user()->phone_number,
            'organization_email' => auth()->user()->email,
            'organization_address' => auth()->user()->company_address,
                    
        ];
    }

    public function downSalesReceipt($transactionId)
    {
       
        $sales = $this->query()->where('transaction_id', $transactionId)->get();

        if ($sales->isEmpty()) {
            return response()->json(['message' => 'No sales found for this transaction.'], 404);
        }

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
        $organizationDetails = [
            'organization_name' => Auth::user()->company_name,
            'organization_phone_number' => Auth::user()->phone_number,
            'organization_email' => Auth::user()->email,
            'organization_address' => Auth::user()->company_address,
        ];
    } else {
        $organizationDetails = $adminDetails;
    }

    // Collect transaction details from the first sale
    $transactionDetails = [
        'created_at' => $sales->first()->created_at,
        'customer_detail' => optional($sales->first()->customers)->first_name . ' ' . optional($sales->first()->customers)->last_name . ' ' . optional($sales->first()->customers)->contact_person,
        'customer_phone_number' => optional($sales->first()->customers)->phone_number,
        'branch_name' => optional($sales->branches)->name,
        'transaction_id' => $sales->first()->transaction_id,
        'transaction_amount' => 0, // Will be calculated below
        'organization_name' => $organizationDetails['organization_name'],
        'organization_phone_number' => $organizationDetails['organization_phone_number'],
        'organization_email' => $organizationDetails['organization_email'],
        'organization_address' => $organizationDetails['organization_address'],
        'payment_method' => $sales->first()->payment_method,
    ];

    $items = $sales->map(function ($sale) use (&$transactionDetails) {
        $total_price = $sale->price_sold_at * $sale->quantity;
        $vatAmount = $sale->vat == 1 ? $total_price * 0.075 : 0; // Assuming VAT is 7.5%
        $total_price_with_vat = $total_price + $vatAmount;
        $formatted_total_price = number_format($total_price_with_vat, 2, '.', ',');

        // Accumulate total transaction amount
        $transactionDetails['transaction_amount'] += $total_price_with_vat;

        return [
            'id' => $sale->id,
            'product_type_name' => optional($sale->product)->product_type_name,
            'product_type_description' => optional($sale->product)->product_type_description,
            'price_sold_at' => $sale->price_sold_at,
            //'vat' => $sale->vat,
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

    private function transformDailySales($sale){
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
    public function create(array $data)
{
   
    $emailService = new EmailService();
    $transactionId =  time() . rand(1000, 9999);
    try {
        $response = DB::transaction(function () use ($data, $emailService, $transactionId) {
            $productDetails = [];
            $totalPrice = 0; // Initialize total price

            foreach ($data['products'] as $product) {

                $batchNoParts = explode('->', $product['batch_no']);
                $batchNo = $batchNoParts[0];
              
                $latestPrice = Price::where([
                    ['product_type_id', $product['product_type_id']],
                    ['batch_no', $batchNo],
                    ['status', 1]
                ])->firstOrFail();
              
                $store = Store::where([
                    ['product_type_id', $product['product_type_id']],
                    ['batch_no', $batchNo]
                ])->firstOrFail();

                if ($store->quantity_available < $product['quantity']) {
                    throw new Exception("Insufficient stock for batch {$batchNo}");
                }
                $store->quantity_available -= $product['quantity'];
                $store->save();

                $sale = new Sale();
                $sale->fill([
                    'product_type_id' => $product['product_type_id'],
                    'customer_id' => $data['customer_id'],
                    'price_sold_at' => $product['price_sold_at'],
                    'quantity' => $product['quantity'],
                    'batch_no' => $batchNo,
                    'vat' => $product['vat'],
                    'payment_method' => $data['payment_method'],
                    'transaction_id' =>$transactionId
                ]);
                $sale->price_id =  $latestPrice->id;
               
                $sale->save();

                $amount = $product['price_sold_at'] * $product['quantity'];
                $vatValue = $product['vat'] == 1 ? ($amount * 0.075) : 0; // 7.5% VAT
                $amount += $vatValue;
                $totalPrice += $amount; 

                
                $productDetails[] = [
                    "productTypeName" => $latestPrice->productType->product_type_name,
                    'price' => $product['price_sold_at'],
                    "quantity" => $product['quantity'],
                    "vat" => $product['vat'] == 1 ? 'Yes' : 'No', 
                    "amount" => $amount
                ];
            }

            // Customer details for the email
            $user = Customer::select('id', 'first_name', 'last_name', 'email', 'contact_person', 'phone_number')
                        ->where('id', $data['customer_id'])
                        ->first();
            if ($user) {
                $customerDetail = trim($user->first_name . ' ' . $user->last_name . ' ' . $user->contact_person);

            //    $p=json_encode($productDetails);
            //     throw new Exception($p);
                // Generate email content
                $tableDetail = $this->generateProductDetailsTable($productDetails, $totalPrice, $transactionId);
                $emailService->sendEmail(
                    ['email' => $user->email, 'first_name' => $customerDetail],
                    "sales-receipt",
                    $tableDetail
                );
            }

            return true;
        });
        return response()->json(['success' => true,'message' => 'Sale successfully recorded: '], 200);
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Sale creation failed: ' . $e->getMessage()], 500);
    }
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
    private function generateProductDetailsTable($productDetails, $totalPrice, $transactionId) {

        $transactionTime = Carbon::now()->format('Y-m-d H:i:s');

        $tableHtml = "<table style='width: 100%; border-collapse: collapse; max-width: 100%;'>
                        <tr>
                             <td style='border: 1px solid black; padding: 8px; text-align: right;'><strong>Transaction Time</strong></td>
                             <td style='border: 1px solid black; padding: 8px; text-align: right;' colspan='4'><strong>{$transactionTime}</strong></td>
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
    

    
    
  
    
    
    
    
    
   
}
