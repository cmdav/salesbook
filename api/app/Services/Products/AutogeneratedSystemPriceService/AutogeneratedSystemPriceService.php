<?php

namespace App\Services\Products\AutogeneratedSystemPriceService;

use App\Services\Products\AutogeneratedSystemPriceService\AutogeneratedSystemPriceRepository;

class AutogeneratedSystemPriceService
{
    protected $autogeneratedSystemPriceRepository;

    public function __construct(AutogeneratedSystemPriceRepository $autogeneratedSystemPriceRepository)
    {
        $this->autogeneratedSystemPriceRepository = $autogeneratedSystemPriceRepository;
    }

    public function index()
    {
        return $this->autogeneratedSystemPriceRepository->index();
    }

    public function show($id)
    {
        return $this->autogeneratedSystemPriceRepository->show($id);
    }

    public function store($data)
    {
        return $this->autogeneratedSystemPriceRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->autogeneratedSystemPriceRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->autogeneratedSystemPriceRepository->destroy($id);
    }
}
