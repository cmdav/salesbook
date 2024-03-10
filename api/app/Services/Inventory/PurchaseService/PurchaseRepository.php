<?php

namespace App\Services\Inventory\PurchaseService;

use App\Models\Purchase;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PurchaseRepository 
{
    public function index()
    {
       return 'purchase';
        $Purchase = Purchase::with('supplier_product:id,product_name,product_image,product_description')
        ->select('Purchases.supplier_product_id')
        ->addSelect([
            'quantity_remain' => Inventory::selectRaw('SUM(quantity_available)')
                ->whereColumn('supplier_product_id', 'Purchases.supplier_product_id')
                ->limit(1), // Subquery for remaining quantity in inventories
            'pending_request' => SupplierRequest::selectRaw('SUM(quantity)')
                ->where('status', 0)
                ->whereColumn('supplier_product_id', 'Purchases.supplier_product_id')
                ->limit(1), // Subquery for pending requests
            'completed_request' => SupplierRequest::selectRaw('SUM(quantity)')
                ->where('status', 1)
                ->whereColumn('supplier_product_id', 'Purchases.supplier_product_id')
                ->limit(1), // Subquery for completed requests
            'total_sales' => Sale::selectRaw('SUM(sales.quantity * sales.price)')
                ->join('Purchases as s', 's.id', '=', 'sales.Purchase_id')
                ->whereColumn('s.supplier_product_id', 'Purchases.supplier_product_id')
                ->limit(1) // Subquery for total sales
        ])
        ->where('Purchase_owner', auth()->user()->id)
        ->groupBy('Purchases.supplier_product_id')
        ->paginate(3);

        $Purchase->getCollection()->transform(function($Purchase){

            return $this->transformProduct($Purchase);
        });


    return $Purchase;

        //return Purchase::latest()->paginate(3);

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
       
        return Purchase::create($data);
    }

    public function findById($id)
    {
        return Purchase::find($id);
    }

    public function update($id, array $data)
    {
        $Purchase = $this->findById($id);
      
        if ($Purchase) {

            $Purchase->update($data);
        }
        return $Purchase;
    }

    public function delete($id)
    {
        $Purchase = $this->findById($id);
        if ($Purchase) {
            return $Purchase->delete();
        }
        return null;
    }
}
