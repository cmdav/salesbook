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
    public function index($type)
    {
    
        return $this->customerRepository->index($type);
       
    }
   
    public function create(array $data)
    {
    
        return $this->customerRepository->create($data);
       
    }
    public function customerName($request)
    {
    
        return $this->customerRepository->customerName($request);
       
    }
    public function searchCustomer($criteria, $request)
    {
    
        return $this->customerRepository->searchCustomer($criteria, $request);
       
    }

   

    
   
   
}