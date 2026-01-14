<?php

namespace App\Services\Auth;

use App\Models\TbUsers;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use App\Models\User;

class AuthService
{
    public function register(array $data)
    {
        $role = DB::table('tb_user_levels')->where('level_code', $data['user_level_code'])->first();
        if (!$role) {
            throw new Exception("Invalid User Role");
        }
        if ($role->level_code === "ADMIN") {
            throw new Exception("Cannot register as Admin");
        }
        // Map user_level_id and set default status
        $data['user_level_id'] = $role->user_level_id ?? $role->id ?? $role->user_id ?? null;
        if (!$data['user_level_id']) {
            throw new Exception("User level ID not found");
        }
        $data['status'] = $data['status'] ?? ($role->level_code === 'DELIVERY' ? 'PENDING' : 'ACTIVE');
        $userRepository = new UserRepository();
        $userData = $userRepository->createUser($data);
        // Retrieve the Eloquent TbUsers model instance
        $user = TbUsers::where('email', $userData->email)->first();
        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data){
        
    }
}