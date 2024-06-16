<?php

namespace App\Services\Security\PageServices;
use Illuminate\Support\Facades\Log;
use App\Models\Pages;


class PageRepository 
{
    public function query(){

        return Pages::select('id', 'page_name')->where('page_name', '!=','subscriptions');
    }
    public function index()
    {
        return $this->query()->paginate(20);
      
    }
    
    public function names()
    {
        return $this->query()->get();
      
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
