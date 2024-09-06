<?php

namespace App\Services\UserService\UserOrgAndBranchDetailService;

use App\Services\UserService\UserRepository;

class UserOrgAndBranchDetailService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->userRepository->getuserOrgAndBranchDetail($data, $id);
    }
}
