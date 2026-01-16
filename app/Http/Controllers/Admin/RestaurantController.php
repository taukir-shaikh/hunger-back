<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\RestaurantService;
use DB;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function store(Request $request, RestaurantService $restaurantService)
    {
        try {
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
        $restaurant = DB::table('tb_restaurants')->where('id', $id)->first();
        if (!$restaurant) {
            return response()->json([
                'msg' => 'Restaurant Not Found'
            ], 404);
        }
        $data = $request->all();
        DB::table('tb_restaurants')->where('id', $id)->update($data);
        return response()->json(['message' => 'Restaurant updated successfully'], 200);
    }

    public function approve($id)
    {
        $restaurant = DB::table('tb_restaurants')->where('id', $id)->first();
        if (!$restaurant) {
            return response()->json([
                'msg' => 'Restaurant Not Found'
            ], 404);
        }
        if ($restaurant->is_approved) {
            return response()->json([
                'message' => 'Restaurant already approved'
            ], 200);
        }
        DB::table('tb_restaurants')->where('id', $id)->update(['is_approved' => true]);
        return response()->json(['message' => 'Restaurant approved successfully'], 200);
    }

    public function reject($id, OrderService $service)
    {
        $restaurant = DB::table('tb_restaurants')->where('id', $id)->first();
        if (!$restaurant) {
            return response()->json([
                'msg' => 'Restaurant not found',
            ], 404);
        }
        if (!$restaurant->is_approved) {
            return response()->json([
                'message' => 'Restaurant already rejected'
            ], 200);
        }
        DB::table('tb_restaurants')->where('id', $id)->update(['is_approved' => false]);
        return response()->json(['message' => 'Restaurant rejected successfully'], status: 200);

    }
}