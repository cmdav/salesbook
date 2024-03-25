<?php

namespace App\Services\Inventory\SearchService;
use App\Services\Inventory\SearchService\SearchRepository;


class SearchService 
{
    protected $SearchRepository;

    public function __construct(SearchRepository $SearchRepository)
    {
        $this->SearchRepository = $SearchRepository;
    }

    public function currency()
    {
       return 'currency';
        return $this->SearchRepository->currency();
    }

   
}
