<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PriceFormRequest;
use App\Services\Inventory\PriceService\PriceService;
use Illuminate\Http\Request;

class SupplierByProductController extends Controller
{
     protected $priceService;

    public function __invoke(PriceService $priceService, $product_type_id)
    {
       $this->priceService = $priceService;
        
       $suppliers = \App\Models\SupplierProduct::select('id', 'supplier_id')
       ->with(['users:id,first_name,last_name,contact_person,phone_number']) 
       ->where('product_type_id', $product_type_id) 
       ->get()
       ->map(function ($supplier) {
            $user = $supplier->users ? $supplier->users->first() : null;
           if ($user) {
              
               $supplier_detail = "{$user->first_name} {$user->last_name} - {$user->phone_number}";
           } else {
               
               $supplier_detail = 'No contact details available';
           }
           // Return the formatted array for each supplier
           return [
               'id' => $supplier->id,
               'supplier_detail' => $supplier_detail
           ];
       });
   
        return $suppliers;
   








       return $this->priceService->getLatestSupplierPrice($product_type_id);
    }
    
   
}
