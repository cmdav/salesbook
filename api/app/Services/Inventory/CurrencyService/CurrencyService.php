<?php

namespace App\Services\Inventory\CurrencyService;
use App\Services\Inventory\CurrencyService\CurrencyRepository;


class CurrencyService 
{
    protected $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function createCurrency(array $data)
    {
       
        return $this->currencyRepository->create($data);
    }

    public function getAllCurrency()
    {
       
        return $this->currencyRepository->index();
    }

    public function getCurrencyById($id)
    {
        return $this->currencyRepository->findById($id);
    }

    public function updateCurrency($id, array $data)
    {
        return $this->currencyRepository->update($id, $data);
    }

    public function deleteCurrency($id)
    {
        return $this->currencyRepository->delete($id);
    }
}
