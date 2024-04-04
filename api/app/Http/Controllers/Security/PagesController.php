<?php
namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Security\PageServices\PageService;

class PagesController extends Controller
{

    protected $PageService;

    public function __construct(PageService $PageService)
    {
       $this->PageService = $PageService;
    }
    public function index()
    {
       
        $Page =$this->PageService->index();
        return response()->json($Page);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_name' =>'string|required|max:30|unique:pages
             |in:currencies,measurements,product-categories,product-sub-categories,products,product-types,sales,purchases,stores,prices,job-roles,pages,permissions'
        ]);
        $Page =$this->PageService->create($request->all());
        return response()->json($Page, 201);
    }

    public function show($id)
    {
        $Page =$this->PageService->show($id);
        return response()->json($Page);
    }
  
    public function update($id, Request $request)
    {
       
        $Page =$this->PageService->update($id, $request->all());
        return response()->json($Page);
    }

    public function destroy($id)
    {
       $this->PageService->delete($id);
        return response()->json(null, 204);
    }
}
