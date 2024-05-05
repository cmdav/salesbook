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



class SaleRepository 
{
   
    private function query(){

       return Sale::with(['product:id,product_type_name,product_type_image,product_type_description',
                        //'store:id,product_type_id,quantity_available',
                        'customers:id,first_name,last_name,contact_person,phone_number',
                        'Price:id,selling_price,cost_price'
                        //'organization:id,organization_name,organization_logo'
                        // 'Price' => function ($query) {
                        //     $query->select('id', 'product_type_id', 'cost_price', 'selling_price', 'discount');
                        // }
                        ])->latest();
    }
    public function index()
    {
       
         $sale =$this->query()->paginate(20);
                   

         $sale->getCollection()->transform(function($sale){

                            return $this->transformProduct($sale);
                        });
                
                
                    return $sale;

    }
    public function searchSale($searchCriteria)
    {
        $sale =$this->query()->where(function($query) use ($searchCriteria) {
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

       
        $sale =  Sale::select("id","quantity","product_type_id","price_id","price_sold_at")->with(['product:id,product_type_name','Price:id,selling_price' ])
                     ->latest()->whereBetween('created_at', [$startOfDay, $endOfDay])->paginate(20);
                   
        
        // Transform the collection to apply any needed transformations
        $sale->getCollection()->transform(function($sale) {
            return $this->transformDailySales($sale);
        });

        return $sale;
    }



    private function transformProduct($sale){
       
       
        return [
            'id' => $sale->id,
            //'store_id' => $sale->store_id,
            //'customer_id' => $sale->customer_id,
            'product_type_id' => optional($sale->product)->product_type_name,
            'product_type_description' => optional($sale->product)->product_type_description,
            'cost_price' => optional($sale->Price)->cost_price,
            'price_sold_at' => $sale->price_sold_at,
            'quantity' => $sale->quantity,
            'total_price' => $sale->price_sold_at * $sale->quantity,
            'payment_method' => $sale->payment_method,
            
            //'sales_owner' => $sale->sales_owner,
            // 'created_by' => $sale->created_by,
            // 'updated_by' => $sale->updated_by,
            'created_at' => $sale->created_at,
            // 'updated_at' => $sale->updated_at,
            // Store details
            // 'store_product_type_id' => optional($sale->store)->product_type_id,
            // 'store_price_id' => optional($sale->store)->price_id,
            // 'store_quantity_available' => optional($sale->store)->quantity_available,
            // Customer details
            'customer_detail' => optional($sale->customers)->first_name . ' ' . optional($sale->customers)->last_name . ' ' . optional($sale->customers)->contact_person,

            'customer_phone_number' => optional($sale->customers)->phone_number,
            'created_by' => optional($sale->creator)->fullname,
            'updated_by' => optional($sale->updater)->fullname,
            // Organization details
            // 'organization_id' => optional($sale->organization)->id,
            // 'organization_name' => optional($sale->organization)->organization_name,
            // 'organization_logo' => optional($sale->organization)->organization_logo,
        ];
    }
    private function transformDailySales($sale){
       
       
        return [
            'id' => $sale->id,
            'product_type_id' => optional($sale->product)->product_type_name,
            'price_sold_at' => $sale->price_sold_at,
            'quantity' => $sale->quantity,
            'total_price' => $sale->price_sold_at * $sale->quantity
        ];
    }
    public function create(array $data)
{
   //dd($data);
   
    $emailService = new EmailService();
    try {
        $response = DB::transaction(function () use ($data, $emailService) {
            $productDetails = [];
           
            foreach ($data['products'] as $product) {
                // Check the latest price and store availability for each product
                $latestPrice = Price::where(
                    [
                        ['product_type_id', $product['product_type_id']],
                        ['status', 1]
                ]
                )->firstOrFail();

                $store = Store::where([['product_type_id', $product['product_type_id']],
                                        ['batch_no', $product['batch_no']]])->firstOrFail();
                if ($store->quantity_available < $product['quantity']) {
                    throw new Exception("Insufficient stock for {$latestPrice->productType->product_type_name} 
                            with {$product['batch_no']} number ");
                }
                $store->quantity_available -= $product['quantity'];
                $store->save();

                // Create the sale record
                $sale = new Sale();
                $sale->fill([
                    'product_type_id' => $product['product_type_id'],
                    'customer_id' => $data['customer_id'],
                    'price_sold_at' => $product['price_sold_at'],
                    'quantity' => $product['quantity'],
                    'batch_no' => $product['batch_no'],
                    'payment_method' => $data['payment_method']
                ]);
                $sale->price_id = $latestPrice->id;
                $sale->save();

                // Prepare product details for the email
                $productDetails[] = [
                    "productTypeName" => $latestPrice->productType->product_type_name,
                    "price" => $latestPrice->selling_price,
                    "quantity" => $product['quantity']
                ];
            }

            // Customer details for the email
            $user = Customer::select('id', 'first_name', 'last_name', 'email', 'contact_person', 'phone_number')
                        ->where('id', $data['customer_id'])
                        ->first();
            if($user){
                $customerDetail = trim($user->first_name . ' ' . $user->last_name . ' ' . $user->contact_person);

                // Generate email content

                $tableDetail = $this->generateProductDetailsTable($productDetails);
                $emailService->sendEmail(
                    ['email' => $user->email, 'first_name' => $customerDetail],
                    "sales-receipt",
                    $tableDetail
                );
            }

            return true;
        });
        return response()->json(['success' => true], 200);
    } catch (Exception $e) {
       // return response()->json(['success' => false, 'message' => 'Sale creation failed: ' . $e->getMessage()], 500);
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
    private function generateProductDetailsTable($productDetails) {
        $tableHtml = "<table style='width:100%; border-collapse: collapse;'>
                        <tr>
                            <th style='border: 1px solid black; padding: 8px;'>Product Name</th>
                            <th style='border: 1px solid black; padding: 8px;'>Price</th>
                            <th style='border: 1px solid black; padding: 8px;'>Quantity</th>
                            <th style='border: 1px solid black; padding: 8px;'>Total</th>
                        </tr>";
    
        foreach ($productDetails as $detail) {
            $totalPrice = $detail['price'] * $detail['quantity'];
            $tableHtml .= "<tr>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['productTypeName']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['price']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['quantity']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>$totalPrice</td>
                           </tr>";
        }
    
        $tableHtml .= "</table>";
    
        return $tableHtml;
    }
  
    
    
    
    
    
   
}
