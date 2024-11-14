<?php

namespace App\Services\Products\ProductMeasurementService;

use App\Services\Products\ProductMeasurementService\ProductMeasurementRepository;

class ProductMeasurementService
{
    protected $productMeasurementRepository;

    public function __construct(ProductMeasurementRepository $productMeasurementRepository)
    {
        $this->productMeasurementRepository = $productMeasurementRepository;
    }

    public function index()
    {
        return $this->productMeasurementRepository->index();
    }

    public function show($id)
    {
        return $this->productMeasurementRepository->show($id);
    }

    public function store($data)
    {
        return $this->productMeasurementRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->productMeasurementRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->productMeasurementRepository->destroy($id);
    }
}
