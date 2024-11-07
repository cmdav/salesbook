<?php

namespace App\Services\Security\LogService;

use App\Services\Security\LogService\LogRepository;

class LogService
{
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function index($request)
    {
        return $this->logRepository->index($request);
    }

    public function show($id)
    {
        return $this->logRepository->show($id);
    }

    public function store($data)
    {
        return $this->logRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->logRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->logRepository->destroy($id);
    }
}
