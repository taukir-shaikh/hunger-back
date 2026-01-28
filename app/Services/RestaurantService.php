<?php

namespace App\Services;

use App\Models\TbRestaurants;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RestaurantService
    /**
     * Get all restaurants with caching.
     */
{
    public function getAllRestaurants()
    {
        return Cache::remember('restaurants.all', 60, function () {
            return TbRestaurants::get();
        });
    }
    /**
     * Store a new restaurant with business logic.
     */
    public function store(array $data)
    {

        if (TbRestaurants::where('name', $data['name'])->exists()) {
            return response()->json(['message' => 'Restaurant name already exists.'], 400);
        }
        DB::beginTransaction();
        $data['is_approved'] = false;
        $data['is_active'] = $data['is_active'] ?? true;
        $restaurantId = DB::table('tb_restaurants')->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'address' => $data['address'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'open_time' => $data['open_time'],
            'close_time' => $data['close_time'],
            'is_active' => $data['is_active'] ?? true,
            'is_approved' => false,
        ]);

        if (!empty($data['category_ids'])) {
            foreach ($data['category_ids'] as $categoryId) {
                DB::table('tb_restaurant_category_map')->insert([
                    'restaurant_id' => $restaurantId,
                    'category_id' => $categoryId,
                ]);
            }
        }

        DB::commit();
        return DB::table('tb_restaurants')->where('id', $restaurantId)->first();
    }
}
