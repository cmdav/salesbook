<?php

namespace App\Services\Inventory\MeasurementService;
use App\Services\Inventory\MeasurementService\MeasurementRepository;


class MeasurementService 
{
    protected $measurementRepository;

    public function __construct(MeasurementRepository $measurementRepository)
    {
        $this->measurementRepository = $measurementRepository;
    }

    public function createMeasurement(array $data)
    {
       
        return $this->measurementRepository->create($data);
    }

    public function getAllMeasurement()
    {
     
        return $this->measurementRepository->index();
    }

    public function getMeasurementById($id)
    {
        return $this->measurementRepository->findById($id);
    }

    public function updateMeasurement($id, array $data)
    {
        return $this->measurementRepository->update($id, $data);
    }

    public function deleteMeasurement($id)
    {
        return $this->measurementRepository->delete($id);
    }
    public function searchMeasurement($id)
    {
        return $this->measurementRepository->searchMeasurement($id);
    }
}
