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
use App\Models\ProductType;
use Carbon\Carbon;



class SaleRepository 
{
   
    private function query(){

       return Sale::with(['product:id,product_type_name,product_type_image,product_type_description',
                        //'store:id,product_type_id,quantity_available',
                        'customers:id,first_name,last_name,phone_number',
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
            'customer_first_name' => optional($sale->customers)->first_name,
            'customer_last_name' => optional($sale->customers)->last_name,
            'customer_phone_number' => optional($sale->customers)->phone_number,
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

        $emailService = new EmailService(); 
            try{
                $response =DB::transaction(function () use ($data, $emailService) { 
                    // Retrieve the latest price for the given product type
                    $latestPrice = Price::where([['product_type_id', $data['product_type_id']],['status',1]])->firstOrFail(); 
                    $store = Store::where('product_type_id', $data['product_type_id'])->firstOrFail(); 
                    $store->quantity_available -= $data['quantity'];
                    $store->save();

                    // Create and return the sale record
                    $sale = new Sale();
                    $sale->fill($data);
                    $sale->price_id = $latestPrice->id;  
                    $sale->save();

                    $user = User::select('id','first_name','last_name','email','contact_person','phone_number')->where('id', $data['customer_id'])->first();
                    $productType = ProductType::select("id","product_type_name")->where('id', $data['product_type_id'])->first();
                    $customerDetail = (isset($user->first_name) ? $user->first_name : '') .(isset($user->last_name) ? $user->last_name : '').
                                    (isset($user->contact_person) ? $user->contact_person : '');

                
                    $productTypeName = $productType->product_type_name;
                    $qty=$data['quantity'];
                    $price =$latestPrice->selling_price;
                    $email = $user->email;
                    $productDetail = [ "customerDetail"=>$customerDetail,"productTypeName"=>$productTypeName,"quantity"=> $qty,"price"=> $price];
                    $tableDetail = $this->generateProductDetailsTable([$productDetail]);
                    $user = ['email'=>$email,'first_name' => $customerDetail];
                    $emailService->sendEmail($user, "sales-receipt", $tableDetail);
                    return true;
                

                
                });
                return true;
            } catch (Exception $e) {
                
                //Log::error('Sale creation failed: ' . $e->getMessage());
                return response()->json([ 'success' => false,'message' => 'Sale creation failed due to an internal error.'
                ],500); 
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
            $tableHtml .= "<tr>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['productTypeName']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['price']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>{$detail['quantity']}</td>
                                <td style='border: 1px solid black; padding: 8px;'>".$detail['price'] * $detail['quantity']."</td>
                           </tr>";
        }
    
        $tableHtml .= "</table>";
    
        return $tableHtml;
    }
    
   
}
