<?php

namespace App\Services\Security\CountryService;

use App\Services\Security\CountryService\CountryRepository;

class CountryService
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index()
    {
        return $this->countryRepository->index();
    }

    public function show($id)
    {
        return $this->countryRepository->show($id);
    }

    public function store($data)
    {
        return $this->countryRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->countryRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->countryRepository->destroy($id);
    }
}
