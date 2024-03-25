<?php

namespace App\Services\UserService;
use App\Services\UserService\CustomerRepository;


class CustomerService
{
    protected CustomerRepository $customerRepository;
    

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;

    }
    public function index()
    {
    
        return $this->customerRepository->index();
       
    }
   
    public function create(array $data)
    {
    
        return $this->customerRepository->create($data);
       
    }

   

    
   
   
}