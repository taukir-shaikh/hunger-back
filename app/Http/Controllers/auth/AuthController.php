<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\TbUsers;
use App\Services\Auth\AuthService;
use App\Services\EmailOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService)
    {
        try {
            $validated = $request->validated();
            if ($validated) {
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
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'Validation failed'], 422);
        }

    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);

    }
    public function verifyOtp(Request $request, EmailOtpService $emailService)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
        $user = TbUsers::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $isValid = $emailService->verify($user, $request->otp);

        if (!$isValid) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 422);
        }

        // 4️⃣ Success response
        return response()->json([
            'message' => 'Email verified successfully'
        ]);
    }
    public function sendOtp(Request $request, TbUsers $userModel, EmailOtpService $emailService)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = $userModel->where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $otp = $emailService->generate($user);
        if (!$otp) {
            return response()->json([
                'message' => 'Failed to generate OTP'
            ], 500);
        }
        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp
        ], 200);
    }
}
