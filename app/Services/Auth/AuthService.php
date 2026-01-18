<?php

namespace App\Services\Auth;

use App\Models\TbUsers;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
        $data['user_level_id'] = $role->user_level_id ?? $role->id ?? $role->user_id ?? null;
        if (!$data['user_level_id']) {
            throw new Exception("User level ID not found");
        }
        $data['status'] = $data['status'] ?? ($role->level_code === 'DELIVERY' ? 'PENDING' : 'ACTIVE');
        $userRepository = new UserRepository();
        $userData = $userRepository->createUser($data);
        $user = TbUsers::where('email', $userData->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data)
    {
        $user = TbUsers::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid Credentials']
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'msg' => 'Login Sucess',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_level_id' => $user->user_level_id,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}