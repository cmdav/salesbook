<?php

namespace App\Services\Security\PageServices;
use App\Services\Security\PageServices\PageRepository;


class PageService 
{
    protected $PageRepository;

    public function __construct(PageRepository $PageRepository)
    {
        $this->PageRepository = $PageRepository;
    }

    public function create(array $data)
    {
       
        return $this->PageRepository->create($data);
    }

    public function Index()
    {
       
        return $this->PageRepository->index();
    }

    public function show($id)
    {
        return $this->PageRepository->findById($id);
    }
   
    public function update($id, array $data)
    {
        return $this->PageRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->PageRepository->delete($id);
    }
    public function names()
    {
        return $this->PageRepository->names();
    }
}
