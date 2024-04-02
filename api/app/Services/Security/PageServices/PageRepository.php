<?php

namespace App\Services\Security\PageServices;

use App\Models\Pages;


class PageRepository 
{
    public function index()
    {
        return Pages::paginate(20);
      
    }
    
    public function names()
    {
        return Pages::select('id', 'page_name')->get();
      
    }
    public function create(array $data)
    {
       
            
        return Pages::create($data); 

           
    }
    

    public function findById($id)
    {
        return Pages::find($id);
    }

    public function update($id, array $data)
    {
        $Page = $this->findById($id);
      
        if ($Page) {

            $Page->update($data);
        }
        return $Page;
    }

    public function delete($id)
    {
        $Page = $this->findById($id);
        if ($Page) {
            return $Page->delete();
        }
        return null;
    }
}
