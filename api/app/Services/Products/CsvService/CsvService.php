<?php

namespace App\Services\Products\CsvService;
use App\Services\Products\CsvService\CsvRepository;


class CsvService 
{
    protected $csvRepository;

    public function __construct(CsvRepository $csvRepository)
    {
        $this->csvRepository = $csvRepository;
    }

    public function index()
    {
       
        return $this->csvRepository->index();
    }

   
}
