<?php

namespace App\Services\Products\SearchMeasurementGroupService;

use App\Services\Products\MeasurementGroupService\MeasurementGroupRepository;

class SearchMeasurementGroupService
{
    protected $measurementGroupRepository;

    public function __construct(MeasurementGroupRepository $measurementGroupRepository)
    {
        $this->measurementGroupRepository = $measurementGroupRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->measurementGroupRepository->getsearchMeasurementGroup($data, $id);
    }
}
