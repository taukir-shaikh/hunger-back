<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function store(Request $request, RestaurantService $restaurantService)
    {
        try{
            $data = $request->all();
            $restaurant = $restaurantService->store($data);
            return $restaurant;
        } catch (\Exception $e) {
            \Log::error('Restaurant creation failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lines' => $e->getLine(),
                'file' => $e->getFile(),
                'payload' => $request->all(),
            ]);
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Logic to update a restaurant
    }

    public function approve($id)
    {
        // Logic to approve a restaurant
        

    }

    public function reject($id)
    {
        // Logic to reject a restaurant
        
    }
}