<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\CountryService\CountryService;
use App\Http\Requests\Security\CountryFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    private $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        return $this->countryService->index();
    }

    public function show($id)
    {
        return $this->countryService->show($id);
    }

    public function store(CountryFormRequest $request)
    {
        return $this->countryService->store($request->all());
    }

    public function update(CountryFormRequest $request, $id)
    {
        return $this->countryService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->countryService->destroy($id);
    }
}