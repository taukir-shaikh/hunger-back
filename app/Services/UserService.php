<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function updateUser($userId, array $data)
    {
        return $this->userRepository->updateUser($userId, $data);
    }
}
