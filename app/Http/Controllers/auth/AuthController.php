<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService)
    {
        try {
            $validated = $request->validated();
            if ($validated) {
                // Proceed with registration logic using $authService
                $result = $authService->register($validated);
                return response()->json($result, 201);
            }
        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lines' => $e->getLine(),
                'file' => $e->getFile(),
                'payload' => $request->except(['password']),
            ]);
            return response()->json(['message' => 'Server Error'], 500);
        }
        return response()->json(['message' => 'Validation failed'], 422);
    }

    public function login(LoginRequest $request, AuthService $authService)
    {
        $validated = $request->validated();
        if ($validated) {
            $result = $authService->login($validated);
            return response()->json(['msg'=> 'Login Success','data'=> $result]);
        } else {
            return response()->json(['message' => 'Validation failed'], 422);
        }

    }

    public function logout(Request $request)
    {
    }
}
