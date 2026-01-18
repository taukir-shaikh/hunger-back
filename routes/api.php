<?php

use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicRestaurantController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
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
    Route::post('verify-email', [AuthController::class, 'verifyOtp']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);

    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
});

Route::prefix('v1')->group(function () {
    Route::get('orders/{id}/timeline', [OrderController::class, 'getOrderTimeline'])
        ->middleware('auth:sanctum');
    // Admin Routes
    Route::prefix('admin')->middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
        Route::post('restaurants', [RestaurantController::class, 'store']);
        Route::put('restaurants/{id}', [RestaurantController::class, 'update']);
        Route::put('restaurants/{id}/approve', [RestaurantController::class, 'approve']);
        Route::put('restaurants/{id}/reject', [RestaurantController::class, 'reject']);
        //admin routes for orders
        Route::put('orders', [OrderController::class, 'index']);
        Route::put('orders/{id}', [OrderController::class, 'show']);
        Route::put('orders/{id}/cancel', [OrderController::class, 'cancel']);
        Route::put('orders/{id}/force-status', [OrderController::class, 'forceStatus']);
        Route::put('orders/{id}/assign-delivery', [OrderController::class, 'assignDelivery']);
    });

    // User Routes
    Route::prefix('user')->middleware(['auth:sanctum', RoleMiddleware::class . ':USER'])->group(function () {
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::post('orders', [OrderController::class, 'orderStore']);
        Route::get('orders', [OrderController::class, 'orderIndex']);
        Route::get('orders/{id}', [OrderController::class, 'orderShow']);
        Route::get('orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
        Route::put('orders/{id}', [OrderController::class, 'orderUpdate']);
    });
    // Restaurant Order Management Routes
    Route::prefix('restaurant')->middleware(['auth:sanctum', RoleMiddleware::class . ':RESTAURANT'])->group(function () {
        Route::get('orders', [RestaurantOrderController::class, 'index']);
        Route::get('orders/{id}', [RestaurantOrderController::class, 'show']);
        Route::put('orders/{id}/accept', [RestaurantOrderController::class, 'acceptOrder']);
        Route::put('orders/{id}/reject', [RestaurantOrderController::class, 'rejectOrder']);
        Route::put('orders/{id}/preparing', [RestaurantOrderController::class, 'preparingOrder']);
        Route::put('orders/{id}/ready', [RestaurantOrderController::class, 'readyOrder']);
    });

    // Delivery Order Management Routes
    Route::prefix('delivery')->middleware(['auth:sanctum', RoleMiddleware::class . ':DELIVERY'])->group(function () {
        // Route::get('orders', [OrderController::class, 'index']);
        // Route::get('orders/{id}', [OrderController::class, 'show']);
        Route::put('orders/{id}/pickup', [OrderController::class, 'pickup']);
        Route::put('orders/{id}/deliver', [OrderController::class, 'deliver']);
    });

    Route::get('restaurants', [PublicRestaurantController::class, 'index']);
    Route::get('restaurants/{id}', [PublicRestaurantController::class, 'show']);
});
