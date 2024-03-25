<?php

namespace App\Services\Inventory\SaleService;

use App\Models\Sale;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Price;
use App\Models\Store;


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
       
         $sale =$this->query()->paginate(2);
                   

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
        })->paginate(2);
                   

        $sale->getCollection()->transform(function($sale){

                           return $this->transformProduct($sale);
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
            // 'created_at' => $sale->created_at,
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
    public function create(array $data)
{
    return DB::transaction(function () use ($data) {
        // Initialize the errors array
        $errors = [];

        // Find the latest price for the given product type
        $latestPrice = Price::where('product_type_id', $data['product_type_id'])
                            ->latest()
                            ->first();

        if (!$latestPrice) {
            $errors['price_id'] = ['Price not found for the product type.'];
        }

        // Retrieve the store item based on the product type id
        $store = Store::where('product_type_id', $data['product_type_id'])->first();

        if (!$store) {
            $errors['store_id'] = ['Store item not found for the product type.'];
        }

        // Check if the store has enough quantity
        if (empty($errors)) {  // Proceed only if no previous errors
            $newQuantity = $store->quantity_available - $data['quantity'];
            if ($newQuantity < 0) {
                $errors['quantity'] = ['Insufficient store items.'];
            } else {
                // Update the store with the new quantity
                $store->quantity_available = $newQuantity;
                $store->save();

                // Insert the sale with the latest price id
                $sale = new Sale();
                $sale->fill($data);
                $sale->price_id = $latestPrice->id; // Set the latest price id
                $sale->save();
                
                return $sale; // Return the sale if everything is successful
            }
        }

        // Check if there were any errors
        if (!empty($errors)) {
            // If there were errors, return them in the Laravel validation error format
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $errors
            ], 422); // HTTP status code 422 stands for Unprocessable Entity
        }
    });
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
   
}
