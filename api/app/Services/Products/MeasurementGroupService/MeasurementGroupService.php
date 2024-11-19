<?php

namespace App\Services\Products\MeasurementGroupService;

use App\Services\Products\MeasurementGroupService\MeasurementGroupRepository;

class MeasurementGroupService
{
    protected $measurementGroupRepository;

    public function __construct(MeasurementGroupRepository $measurementGroupRepository)
    {
        $this->measurementGroupRepository = $measurementGroupRepository;
    }

    public function index()
    {
        return $this->measurementGroupRepository->index();
    }

    public function show($id)
    {
        return $this->measurementGroupRepository->show($id);
    }

    public function store($data)
    {
        return $this->measurementGroupRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->measurementGroupRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->measurementGroupRepository->destroy($id);
    }
}
