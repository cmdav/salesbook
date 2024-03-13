<?php

namespace App\Services\Inventory\SaleService;

use App\Models\Sale;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SaleRepository 
{
    public function index()
    {
       
         $sale =Sale::with('store:id,product_type_id,price_id,quantity_available',
                        'customers:id,first_name,last_name,phone_number',
                        'organization:id,organization_name,organization_logo')
                        ->latest()
                        ->paginate(20);

         $sale->getCollection()->transform(function($sale){

                            return $this->transformProduct($sale);
                        });
                
                
                    return $sale;

    }


    private function transformProduct($sale){
       
        return [
            'id' => $sale->id,
            'store_id' => $sale->store_id,
            'customer_id' => $sale->customer_id,
            'price_sold_at' => $sale->price_sold_at,
            'quantity' => $sale->quantity,
            'sales_owner' => $sale->sales_owner,
            'created_by' => $sale->created_by,
            'updated_by' => $sale->updated_by,
            'created_at' => $sale->created_at,
            'updated_at' => $sale->updated_at,
            // Store details
            'store_product_type_id' => optional($sale->store)->product_type_id,
            'store_price_id' => optional($sale->store)->price_id,
            'store_quantity_available' => optional($sale->store)->quantity_available,
            // Customer details
            'customer_first_name' => optional($sale->customers)->first_name,
            'customer_last_name' => optional($sale->customers)->last_name,
            'customer_phone_number' => optional($sale->customers)->phone_number,
            // Organization details
            'organization_id' => optional($sale->organization)->id,
            'organization_name' => optional($sale->organization)->organization_name,
            'organization_logo' => optional($sale->organization)->organization_logo,
        ];
    }
    public function create(array $data)
    {
       
        return Sale::create($data);
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
