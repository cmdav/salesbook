<?php

namespace App\Services\Inventory\PriceService;

use App\Models\Price;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PriceRepository 
{
    public function index()
    {
       return 'price';
        $Price = Price::with('supplier_product:id,product_name,product_image,product_description')
        ->select('Prices.supplier_product_id')
        ->addSelect([
            'quantity_remain' => Inventory::selectRaw('SUM(quantity_available)')
                ->whereColumn('supplier_product_id', 'Prices.supplier_product_id')
                ->limit(1), // Subquery for remaining quantity in inventories
            'pending_request' => SupplierRequest::selectRaw('SUM(quantity)')
                ->where('status', 0)
                ->whereColumn('supplier_product_id', 'Prices.supplier_product_id')
                ->limit(1), // Subquery for pending requests
            'completed_request' => SupplierRequest::selectRaw('SUM(quantity)')
                ->where('status', 1)
                ->whereColumn('supplier_product_id', 'Prices.supplier_product_id')
                ->limit(1), // Subquery for completed requests
            'total_sales' => Sale::selectRaw('SUM(sales.quantity * sales.price)')
                ->join('Prices as s', 's.id', '=', 'sales.Price_id')
                ->whereColumn('s.supplier_product_id', 'Prices.supplier_product_id')
                ->limit(1) // Subquery for total sales
        ])
        ->where('Price_owner', auth()->user()->id)
        ->groupBy('Prices.supplier_product_id')
        ->paginate(3);

        $Price->getCollection()->transform(function($Price){

            return $this->transformProduct($Price);
        });


    return $Price;

        //return Price::latest()->paginate(3);

    }
    private function transformProduct($supplyToCompany){

        return [
            'product_name'=>optional($supplyToCompany->supplier_product)->product_name,
            'product_image'=>optional($supplyToCompany->supplier_product)->product_image,
            'product_description'=>optional($supplyToCompany->supplier_product)->product_description,
            'quantity_remaining'=>$supplyToCompany->quantity_remain,
            'pending_request'=>$supplyToCompany->pending_request,
            'completed_request'=>$supplyToCompany->completed_request,
            'total_sales'=>$supplyToCompany->total_sales,
        ];

    }







    
    public function create(array $data)
    {
       
        return Price::create($data);
    }

    public function findById($id)
    {
        return Price::find($id);
    }

    public function update($id, array $data)
    {
        $Price = $this->findById($id);
      
        if ($Price) {

            $Price->update($data);
        }
        return $Price;
    }

    public function delete($id)
    {
        $Price = $this->findById($id);
        if ($Price) {
            return $Price->delete();
        }
        return null;
    }
}
