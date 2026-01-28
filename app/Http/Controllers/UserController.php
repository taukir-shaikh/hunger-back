<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateProfile(Request $request, UserService $userService)
    {
        $user = Auth::user();
        $data = $request->only(['name', 'email', 'phone']);
        $updatedUser = $userService->updateUser($user->id, $data);
        if (!$updatedUser) {
            return response()->json(['message' => 'Update failed'], 400);
        }
        return response()->json(['message' => 'Profile updated successfully', 'user' => $updatedUser], 200);
    }

    public function getProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        return response()->json(['user' => $user], 200);
    }
}
