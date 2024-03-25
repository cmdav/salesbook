<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Services\Inventory\SearchService\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
       $this->searchService = $searchService;
    }
    public function currency()
    {
      
        $searchResult =$this->searchService->currency();
        return response()->json($searchResult);
    }

   
}
