<?php

namespace App\Services\Security\StateService;

use App\Services\Security\StateService\StateRepository;

class StateService
{
    protected $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function index()
    {
        return $this->stateRepository->index();
    }

    public function show($id)
    {
        return $this->stateRepository->show($id);
    }

    public function store($data)
    {
        return $this->stateRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->stateRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->stateRepository->destroy($id);
    }
}
