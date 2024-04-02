<?php

namespace App\Services\Inventory\SearchService;

use App\Models\Search;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SearchRepository 
{
    public function index()
    {
        $Search = $this->queryCommon()->paginate(20);

        return $this->transformAndReturn($Search);
    }
   
}
