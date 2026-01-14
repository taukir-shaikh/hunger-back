<?php

use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicRestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard']);
});

Route::prefix('v1')->group(function () {
    
    Route::prefix('admin')->middleware(['auth:sanctum', 'rolecheck:ADMIN'])->group(function () {
        Route::post('restaurants', [RestaurantController::class, 'store']);
        Route::put('restaurants/{id}', [RestaurantController::class, 'update']);
        Route::put('restaurants/{id}/approve', [RestaurantController::class, 'approve']);
        Route::put('restaurants/{id}/reject', [RestaurantController::class, 'reject']);
    });

    Route::get('restaurants', [PublicRestaurantController::class, 'index']);
    Route::get('restaurants/{id}', [PublicRestaurantController::class, 'show']);
});
