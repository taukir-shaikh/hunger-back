<?php

namespace App\Repositories;

use App\Models\TbUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function createUser(array $data)
    {
        $isActive = $data['status'] === 'ACTIVE' ? 1 : 0;
        $user = TbUsers::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'user_level_id' => $data['user_level_id'],
            'is_active' => $isActive,
        ]);
        return $user;
    }

    public function updateUser($userId, array $data)
    {
        $user = TbUsers::find($userId);
        if (!$user) {
            return null;
        }
        $user->fill($data);
        $user->save();
        return $user;
    }
}
